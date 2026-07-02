<?php

namespace App\Http\Controllers;

use App\Mail\VerifyAssistanceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class ExternalMercadoPagoController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('Callback de Mercado Pago', $request->query());

        return view('external.mercado-pago.callback');
    }

    public function successfulPaymentWebhook(Request $request)
    {
        Log::info('[MP webhook] >>> ENTRÓ al controller', [
            'method'  => $request->method(),
            'url'     => $request->fullUrl(),
            'query'   => $request->query(),
            'body'    => $request->all(),
            'raw'     => $request->getContent(),
        ]);

        $type      = $request->input('type', $request->query('topic'));
        $paymentId = $request->input('data.id', $request->query('id'));

        if ($type !== 'payment' || ! $paymentId) {
            return response()->json(['ignored' => true], 200);
        }

        try {
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

            $aprobado    = $payment->status === 'approved';
            $tieneEmail  = ! empty($payment->payer->email);

            Log::info('[MP webhook] evaluando envío de mail', [
                'aprobado'   => $aprobado,
                'tieneEmail' => $tieneEmail,
                'email'      => $payment->payer->email ?? null,
            ]);

            if ($aprobado && $tieneEmail) {
                $unitPrice = 12000;
                $peopleCount = 1;

                if (! empty($payment->external_reference)) {
                    $ref = json_decode($payment->external_reference, true);
                    if (is_array($ref)) {
                        $unitPrice = $ref['unit_price'] ?? $unitPrice;
                        $peopleCount = $ref['people_count'] ?? $peopleCount;
                    }
                }

                Log::info('[MP webhook] >>> ENVIANDO mail', [
                    'to' => $payment->payer->email,
                    'unitPrice' => $unitPrice,
                    'peopleCount' => $peopleCount,
                ]);

                Mail::to($payment->payer->email)
                    ->send(new VerifyAssistanceMail(
                        token: (string) $payment->id,
                        unitPrice: $unitPrice,
                        peopleCount: $peopleCount,
                    ));

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

        return response()->json(['received' => true], 200);
    }
}
