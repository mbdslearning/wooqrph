<?php

namespace WooQRPh\Infrastructure\PayMongo;

use RuntimeException;

defined( 'ABSPATH' ) || exit;

final class Client {

    private string $secretKey;
    private string $baseUrl = 'https://api.paymongo.com/v1';

    public function __construct( string $secretKey ) {
        $this->secretKey = $secretKey;
    }

    public function createPaymentIntent(
        int $amount,
        string $currency,
        array $allowedPaymentMethods,
        array $metadata = []
    ): array {
        return $this->request(
            'POST',
            '/payment_intents',
            [
                'data' => [
                    'attributes' => [
                        'amount' => $amount,
                        'currency' => $currency,
                        'payment_method_allowed' => $allowedPaymentMethods,
                        'metadata' => $metadata,
                    ],
                ],
            ]
        );
    }

    public function attachPaymentMethod(
        string $paymentIntentId,
        array $paymentMethodData
    ): array {
        return $this->request(
            'POST',
            "/payment_intents/{$paymentIntentId}/attach",
            [
                'data' => [
                    'attributes' => $paymentMethodData,
                ],
            ]
        );
    }

    private function request(
        string $method,
        string $path,
        array $body = []
    ): array {
        $response = wp_remote_request(
            $this->baseUrl . $path,
            [
                'method'  => $method,
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode( $this->secretKey . ':' ),
                    'Content-Type'  => 'application/json',
                ],
                'body'    => json_encode( $body ),
                'timeout' => 30,
            ]
        );

        if ( is_wp_error( $response ) ) {
            throw new RuntimeException( $response->get_error_message() );
        }

        $status = wp_remote_retrieve_response_code( $response );
        $data   = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $status < 200 || $status >= 300 ) {
            throw new RuntimeException(
                'PayMongo API error: ' . wp_json_encode( $data )
            );
        }

        return $data;
    }
}
