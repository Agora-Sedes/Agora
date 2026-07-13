<?php

namespace App\Http\Controllers;

use App\Mail\VerifyAssistanceMail;
use App\Models\Attendant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class ExternalMercadoPagoController extends Controller
{
    public function callback(Request $request)
    {
        Log::debug('Callback de Mercado Pago', $request->query());

        return view('external.mercado-pago.callback');
    }
    /**
     * Notificación server-to-server de Mercado Pago cuando cambia el estado de un pago.
     * El webhook solo trae el ID; consultamos el detalle en la API y, si el pago fue
     * aprobado, confirmamos y mandamos el mail de asistencia a todos los inscriptos
     * agrupados por el external_reference de la compra.
     */
    public function successfulPaymentWebhook(Request $request)
    {
        Log::debug('[MP webhook] >>> ENTRÓ al controller', [
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
            'query'   => $request->query(),
            'body'    => $request->all(),
            'raw'     => $request->getContent(),
        ]);

        // Solo nos interesan las notificaciones de pago.
        $type      = $request->input('type', $request->query('topic'));
        $paymentId = $request->input('data.id', $request->query('id'));

        if ($type !== 'payment' || ! $paymentId) {
            return response()->json(['ignored' => true], 200);
        }

        try {
            // Consultar el pago real en la API de MP para conocer su estado y el comprador
            $token = config('services.mercadopago.access_token');
            Log::debug('[MP webhook] consultando pago en la API de MP', [
                'paymentId'   => $paymentId,
                'token_seteado' => ! empty($token),
            ]);

            MercadoPagoConfig::setAccessToken($token);
            $payment = (new PaymentClient())->get($paymentId);

            Log::debug('[MP webhook] pago obtenido', [
                'id'     => $payment->id,
                'status' => $payment->status,
                'payer'  => $payment->payer->email ?? null,
                'external_reference' => $payment->external_reference ?? null,
            ]);

            // Procesar el pago SOLO cuando fue aprobado.
            $aprobado = $payment->status === 'approved';

            // external_reference agrupa a TODOS los inscriptos de esta compra.
            // Un pagador puede comprar varias entradas (para sí y para otros),
            // así que confirmamos y mandamos el mail a cada uno del grupo.
            $orderReference = $payment->external_reference ?? null;

            $attendants = $orderReference
                ? Attendant::where('order_reference', $orderReference)->get()
                : collect();

            Log::debug('[MP webhook] evaluando envío de mails', [
                'aprobado'       => $aprobado,
                'orderReference' => $orderReference,
                'inscriptos'     => $attendants->count(),
            ]);

            if ($aprobado && $attendants->isNotEmpty()) {
                foreach ($attendants as $attendant) {
                    $attendant->update([
                        'is_draft' => false,
                        'payment_id' => (string) $payment->id,
                    ]);

                    Log::debug('[MP webhook] >>> ENVIANDO mail', ['to' => $attendant->email]);
                    Mail::to($attendant->email)
                        ->send(new VerifyAssistanceMail($attendant));
                    Log::debug('[MP webhook] <<< mail ENVIADO OK', ['to' => $attendant->email]);
                }
            } else {
                Log::debug('[MP webhook] NO se envían mails (condición no cumplida)');
            }
        } catch (\Throwable $e) {
            Log::error('[MP webhook] ERROR procesando el webhook', [
                'error' => $e->getMessage(),
                'class' => $e::class,
                'file'  => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // MP espera un 200 para no reintentar la notificación
        return response()->json(['received' => true], 200);
    }
}
