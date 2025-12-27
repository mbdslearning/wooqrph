<?php

namespace WooQRPh\Infrastructure\Persistence;

use WooQRPh\Domain\PaymentState\PaymentState;
use WC_Order;

defined( 'ABSPATH' ) || exit;

final class OrderPaymentRepository {

    private const META_INTENT_ID    = '_wooqrph_payment_intent_id';
    private const META_CLIENT_KEY   = '_wooqrph_payment_client_key';
    private const META_STATE        = '_wooqrph_payment_state';
    private const META_RAW_RESPONSE = '_wooqrph_payment_raw';

    public function persist(
        WC_Order $order,
        string $intentId,
        string $clientKey,
        PaymentState $state,
        array $raw
    ): void {
        $order->update_meta_data( self::META_INTENT_ID, $intentId );
        $order->update_meta_data( self::META_CLIENT_KEY, $clientKey );
        $order->update_meta_data( self::META_STATE, $state->value );
        $order->update_meta_data( self::META_RAW_RESPONSE, $raw );
        $order->save();
    }

    public function updateState(
        WC_Order $order,
        PaymentState $state
    ): void {
        $order->update_meta_data( self::META_STATE, $state->value );
        $order->save();
    }
}
