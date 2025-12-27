<?php

namespace WooQRPh\Payments;

use WooQRPh\Infrastructure\PayMongo\Client;
use WooQRPh\Infrastructure\Persistence\OrderPaymentRepository;
use WooQRPh\Domain\PaymentIntent\PaymentIntent;
use WooQRPh\Domain\PaymentState\PaymentState;
use WC_Order;

defined( 'ABSPATH' ) || exit;

final class PaymentIntentService {

    public function __construct(
        private Client $client,
        private OrderPaymentRepository $repository
    ) {}

    public function createDynamicQRPhIntent( WC_Order $order ): PaymentIntent {
        $amount   = (int) round( $order->get_total() * 100 );
        $currency = strtoupper( $order->get_currency() );

        $intentResponse = $this->client->createPaymentIntent(
            $amount,
            $currency,
            [ 'qrph' ],
            [
                'order_id' => (string) $order->get_id(),
            ]
        );

        $attributes = $intentResponse['data']['attributes'];

        $intent = new PaymentIntent(
            $intentResponse['data']['id'],
            $attributes['client_key'],
            PaymentState::CREATED,
            $intentResponse
        );

        $this->repository->persist(
            $order,
            $intent->id,
            $intent->clientKey,
            $intent->state,
            $intent->raw
        );

        return $intent;
    }
}
