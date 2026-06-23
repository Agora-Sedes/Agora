<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use App\Mail\VerifyPaymentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class AttendantRegistrationController extends Controller
{
    public function amountAndPaymentMethodForm(int $id)
    {
        return view('conferences.register.amount-and-payment-method', [
            'conference_id' => $id,
        ]);
    }

    public function participantsForm(int $id, Request $request)
    {
        $amount = intval($request->post('amount', 1), 10);
        /** @var 'cash'|'mp' $method */
        $method = $request->post('payment_method', 'cash');

        return view('conferences.register.participants', [
            'amount' => $amount,
            'method' => $method,
            'conference_id' => $id,
        ]);
    }

    public function completeRegistration(int $id, Request $request)
    {
        $data = $request->validate([
            'participants' => 'required|array',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.lastname' => 'required|string|max:255',
            'participants.*.dni' => 'required|string|max:255',
            'participants.*.email' => 'required|email|max:255',
            'participants.*.phone' => 'required|string|max:50',
            'participants.*.mode' => 'required|in:irl,online',
            'payment_method' => 'required|in:cash,mp',
        ]);

        $conferenceId = $id;
        $isDraft = $data['payment_method'] === 'mp';

        DB::transaction(function () use ($conferenceId, $data, $isDraft) {
            foreach ($data['participants'] as $participant) {
                Attendant::create([
                    'conference_id' => $conferenceId,
                    'is_draft' => $isDraft,
                    'was_present' => false,
                    'government_id' => $participant['dni'],
                    'full_name' => trim($participant['name'] . ' ' . $participant['lastname']),
                    'email' => $participant['email'],
                    'phone_number' => $participant['phone'],
                ]);
            }
        });

        if ($data['payment_method'] === 'mp') {
            $initPoint = $this->createMercadoPagoPreference($data['participants']);
            return redirect()->away($initPoint);
        } // ^ early return

        foreach ($data['participants'] as $participant) {
            $inscriptionId = md5($participant['dni']);
            Mail::to($participant['email'])->send(new VerifyPaymentMail($inscriptionId));
        }

        return view('conferences.register.success', [
            'participants' => $data['participants'],
        ]);
    }

    private function createMercadoPagoPreference(array $participants): string
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $buyer = $participants[0];

        $client = new PreferenceClient();
        // TODO: update hardcoded price and item name once DB pr is merged
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
                'success' => route('external.mercado-pago.callback'),
                'failure' => route('external.mercado-pago.callback'),
                'pending' => route('external.mercado-pago.callback'),
            ],
            'notification_url' => config('services.mercadopago.notification_url'),
            // Texto que ve el comprador en el resumen de su tarjeta (usa APP_NAME=Agora)
            'statement_descriptor' => config('app.name'),
        ]);
        return $preference->init_point;
    }
}
