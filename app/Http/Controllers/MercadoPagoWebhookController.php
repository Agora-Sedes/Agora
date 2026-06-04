<?php

namespace App\Http\Controllers;

use App\Mail\VerifyAssistanceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoWebhookController extends Controller
{
    /**
     * Notificación server-to-server de Mercado Pago cuando cambia el estado de un pago.
     * El webhook solo trae el ID; consultamos el detalle en la API y, si el pago fue
     * aprobado, mandamos el mail de asistencia al comprador.
     */
    public function __invoke(Request $request)
    {
        Log::info('[MP webhook] >>> ENTRÓ al controller', [
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
            Log::info('[MP webhook] consultando pago en la API de MP', [
                'paymentId'   => $paymentId,
                'token_seteado' => ! empty($token),
            ]);

            MercadoPagoConfig::setAccessToken($token);
            $payment = (new PaymentClient())->get($paymentId);

            Log::info('[MP webhook] pago obtenido', [
                'id'     => $payment->id,
                'status' => $payment->status,
                'payer'  => $payment->payer->email ?? null,
            ]);

            // Mandar el mail de asistencia SOLO cuando el pago fue aprobado
            $aprobado    = $payment->status === 'approved';
            $tieneEmail  = ! empty($payment->payer->email);
            Log::info('[MP webhook] evaluando envío de mail', [
                'aprobado'   => $aprobado,
                'tieneEmail' => $tieneEmail,
                'email'      => $payment->payer->email ?? null,
            ]);

            if ($aprobado && $tieneEmail) {
                Log::info('[MP webhook] >>> ENVIANDO mail', ['to' => $payment->payer->email]);
                Mail::to($payment->payer->email)
                    ->send(new VerifyAssistanceMail((string) $payment->id));
                Log::info('[MP webhook] <<< mail ENVIADO OK', ['to' => $payment->payer->email]);
            } else {
                Log::info('[MP webhook] NO se envía mail (condición no cumplida)');
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
