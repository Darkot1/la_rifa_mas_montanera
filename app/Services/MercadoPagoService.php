<?php

namespace App\Services;

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    public function __construct()
    {
        $this->authenticate();
    }

    protected function authenticate()
    {
        $mpAccessToken = env('MERCADO_PAGO_ACCESS_TOKEN');
        MercadoPagoConfig::setAccessToken($mpAccessToken);
    }


    public function createPaymentPreference($items, $payer)
    {
        
        $client = new PreferenceClient();
        
        $paymentMethods = [
            "excluded_payment_methods" => [],
            "installments" => 12,
            "default_installments" => 1
        ];

        $backUrls = [
            'success' => route('mercadopago.success'),
            'failure' => route('mercadopago.failed')
        ];

        $request = [
            "items" => $items,
            "payer" => $payer,
            "payment_methods" => $paymentMethods,
            "back_urls" => $backUrls,
            "statement_descriptor" => "NAME_DISPLAYED_IN_USER_BILLING",
            "external_reference" => "1234567890",
            "expires" => false,
            "auto_return" => 'approved',
        ];

        try {
            $preference = $client->create($request);
            return $preference;
        } catch (MPApiException $error) {
            Log::error('MercadoPago Error: ' . $error->getMessage());
            return response()->json([
                'error' => 'Error al crear la preferencia de pago',
                'details' => $error->getMessage()
            ]);
        }
        
    }
}