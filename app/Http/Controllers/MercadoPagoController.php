<?php

namespace App\Http\Controllers;

use App\Services\MercadoPagoService;
use App\Models\Raffle;
use App\Models\TicketPurchase;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MercadoPagoController extends Controller
{
    protected $mercadoPagoService;

    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
        Log::info('MercadoPagoController iniciado.');
    }

    /**
     * Crear preferencia de pago y devolver URL de redirección.
     */
    public function createPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'raffle_id' => 'required|exists:raffles,id',
                'ticket_numbers' => 'required|array'
            ]);

            $raffle = Raffle::findOrFail($validated['raffle_id']);
            $user = Auth::user();

            $items = [[
                "title" => "Boletos para {$raffle->title}",
                "quantity" => count($validated['ticket_numbers']),
                "unit_price" => floatval($raffle->price_tickets),
                "currency_id" => "COP"
            ]];

            $payer = [
                "name" => $user->name,
                "email" => $user->email
            ];

            $preference = $this->mercadoPagoService->createPaymentPreference($items, $payer);

            return response()->json([
                'redirect_url' => $preference->init_point
            ]);

        } catch (\Exception $e) {
            Log::error('Error en createPayment:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Acción cuando el pago es exitoso.
     */
    public function success(Request $request)
    {
        Log::info('Pago exitoso recibido:', ['payment_id' => $request->get('payment_id')]);

        $paymentId = $request->get('payment_id');

        $payment = $this->mercadoPagoService->getPaymentStatus($paymentId);

        if (isset($payment['error'])) {
            Log::error('Error al obtener el estado del pago:', ['payment' => $payment]);
            return response()->json([
                'error' => $payment['error']
            ], 400);
        }

        if ($payment && $payment->status == 'approved') {
            Log::info('Pago aprobado, creando boletos...', ['payment' => $payment]);

            $raffle = Raffle::find($payment->external_reference);

            if ($raffle) {
                TicketPurchase::create([
                    'user_id' => Auth::id(),
                    'raffle_id' => $raffle->id,
                    'quantity' => count($request->get('ticket_numbers')),
                ]);

                Log::info('Boletos creados correctamente para el usuario.', ['user_id' => Auth::id()]);
                return response()->json([
                    'message' => 'Pago realizado con éxito y boletos creados.'
                ], 200);
            }

            Log::error('Rifa no encontrada para el pago.', ['raffle_id' => $payment->external_reference]);
            return response()->json([
                'error' => 'Rifa no encontrada.'
            ], 400);
        }

        return response()->json([
            'error' => 'El pago ha fallado.'
        ], 400);
    }

    /**
     * Acción cuando el pago falla.
     */
    public function failure()
    {
        Log::error('El pago ha fallado.');
        return response()->json([
            'error' => 'El pago ha fallado.'
        ], 400);
    }
}
