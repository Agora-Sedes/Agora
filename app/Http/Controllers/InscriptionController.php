<?php

namespace App\Http\Controllers;

use App\Mail\VerifyPaymentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class InscriptionController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $quantity = (int)$request->query('quantity', 1);
        $method = $request->query('method', 'card');
        return view('forminscription', ['quantity' => $quantity, 'method' => $method]);
    }
     public function store(Request $request)
    {
        $data = $request->validate([
            'participants' => 'required|array',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.lastname' => 'required|string|max:255',
            'participants.*.dni' => 'required|string|max:255',
            'participants.*.email' => 'required|email|max:255',
            'participants.*.phone' => 'required|string|max:50',
            'participants.*.mode' => 'required|in:presencial,virtual',
            'payment_method' => 'required|in:cash,mp',
        ]);

        if ($data['payment_method'] === 'mp') {
            $initPoint = $this->createMercadoPagoPreference($data['participants']);
            return redirect()->away($initPoint);
        }

        foreach ($data['participants'] as $participant) {
            $inscriptionId = md5($participant['dni']);
            Mail::to($participant['email'])->send(new VerifyPaymentMail($inscriptionId));
        }

        return view('inscription-confirmation', ['participants' => $data['participants']]);
    }

    private function createMercadoPagoPreference(array $participants): string
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $buyer = $participants[0];

        $client = new PreferenceClient();
        //@TODO: update hardcoded price and item name once DB pr is merged
        $preference = $client->create([
            'items' => [
                [
                    'title' => 'Entrada a la jornada',
                    'quantity' => count($participants), 
                    'unit_price' => 12000,
                    'currency_id' => 'ARS',
                ],
            ],
            'payer' => [
                'name' => $buyer['name'],
                'surname' => $buyer['lastname'],
                'email' => $buyer['email'],
                'phone' => [
                    'number' => $buyer['phone'],
                ],
            ],
            'back_urls' => [
                'success' => env('APP_URL') . '/mercado-pago/callback',
                'failure' => env('APP_URL') . '/mercado-pago/callback',
                'pending' => env('APP_URL') . '/mercado-pago/callback',
            ],
            'notification_url' => config('services.mercadopago.notification_url'),
            // Texto que ve el comprador en el resumen de su tarjeta (usa APP_NAME=Agora)
            'statement_descriptor' => config('app.name'),
        ]);
        return $preference->init_point;
    }
}
