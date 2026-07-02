<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use App\Mail\VerifyPaymentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class AttendantRegistrationController extends Controller
{
    public function amountAndPaymentMethodForm(int $id)
    {
        $conference = Conference::findOrFail($id);

        return view('conferences.register.amount-and-payment-method', [
            'conference' => $conference,
        ]);
    }

    public function participantsForm(int $id, Request $request)
    {
        $amount = intval($request->post('amount', 1), 10);
        /** @var 'cash'|'mp' $method */
        $method = $request->post('payment_method', 'cash');

        $conference = Conference::findOrFail($id);

        return view('conferences.register.participants', [
            'amount' => $amount,
            'method' => $method,
            'conference' => $conference,
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

        $conference = Conference::findOrFail($id);
        $conferenceId = $conference->id;
        $isDraft = $data['payment_method'] === 'mp';
        // Referencia única de esta compra: agrupa a todos los inscriptos de este registro.
        $orderReference = (string) Str::uuid();

        DB::transaction(function () use ($conferenceId, $data, $isDraft, $orderReference) {
            foreach ($data['participants'] as $participant) {
                Attendant::create([
                    'conference_id' => $conferenceId,
                    'order_reference' => $orderReference,
                    'mode' => $participant['mode'],
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
            $initPoint = $this->createMercadoPagoPreference($orderReference, $data['participants'], $conference->price);
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

    private function createMercadoPagoPreference(string $orderReference, array $participants, float $unitPrice): string
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        $buyer = $participants[0];

        $client = new PreferenceClient();
        $preference = $client->create([
            // Referencia de la compra: el webhook la usa para ubicar a todo el grupo.
            'external_reference' => $orderReference,
            'items' => [
                [
                    'title' => 'Entrada a la jornada',
                    'quantity' => count($participants),
                    'unit_price' => $unitPrice,
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
