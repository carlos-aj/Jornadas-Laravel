<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Eventos;
use App\Models\Inscripcion;
use App\Notifications\InscripcionNotification;
use Illuminate\Support\Facades\Auth;

class PayPalController extends Controller
{
    public function handlePayment(Request $request, $id, $tipo_inscripcion)
    {
        $evento = Eventos::find($id);
        if ($evento == null) {
            return response()->json('Evento no encontrado', 404);
        }

        $price = $request->input('price');

        if (is_null($tipo_inscripcion) || is_null($price)) {
            return response()->json('Tipo de inscripción o precio no proporcionado', 400);
        }

        if ($price == 0) {
            // Save the inscription with tipo_inscripcion
            $inscripcion = new Inscripcion();
            $inscripcion->user_id = Auth::id();
            $inscripcion->evento_id = $id;
            $inscripcion->tipo_inscripcion = $tipo_inscripcion;
            $inscripcion->save();

            // Enviar notificación por correo
            Auth::user()->notify(new InscripcionNotification($inscripcion));

            return response()->json('Inscripción realizada con éxito', 201);
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => $price
                    ]
                ]
            ],
            "application_context" => [
                "cancel_url" => route('cancel.payment'),
                "return_url" => route('success.payment', ['id' => $id, 'tipo_inscripcion' => $tipo_inscripcion])
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        } else {
            return response()->json('Error al crear el pedido de PayPal', 500);
        }
    }

    public function paymentCancel()
    {
        return response()->json('Pago cancelado', 200);
    }

    public function paymentSuccess(Request $request, $id, $tipo_inscripcion)
    {
        $evento = Eventos::find($id);
        if ($evento == null) {
            return response()->json('Evento no encontrado', 404);
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $inscripcion = new Inscripcion();
            $inscripcion->user_id = Auth::id();
            $inscripcion->evento_id = $id;
            $inscripcion->tipo_inscripcion = $tipo_inscripcion;
            $inscripcion->save();

            // Enviar notificación por correo
            Auth::user()->notify(new InscripcionNotification($inscripcion));

            return response()->json('Inscripción realizada con éxito', 201);
        } else {
            return response()->json('Error al capturar el pago de PayPal', 500);
        }
    }
}
