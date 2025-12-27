<?php

namespace WooQRPh\Rest\Controllers;

use WP_REST_Request;
use WP_REST_Response;
use WC_Order;
use WooQRPh\Domain\PaymentState\PaymentState;

defined( 'ABSPATH' ) || exit;

final class PaymentController {

    public function register(): void {
        register_rest_route(
            'wooqrph/v1',
            '/payment/(?P<order_id>\d+)',
            [
                'methods'  => 'GET',
                'callback' => [ $this, 'get_status' ],
                'permission_callback' => [ $this, 'can_access' ],
            ]
        );
    }

    public function can_access(): bool {
        return is_user_logged_in() || wc()->session !== null;
    }

    public function get_status( WP_REST_Request $request ): WP_REST_Response {
        $order = wc_get_order( (int) $request['order_id'] );

        if ( ! $order instanceof WC_Order ) {
            return new WP_REST_Response( [ 'error' => 'Order not found' ], 404 );
        }

        $state = $order->get_meta( '_wooqrph_payment_state' );
        $clientKey = $order->get_meta( '_wooqrph_payment_client_key' );

        return new WP_REST_Response(
            [
                'order_id'  => $order->get_id(),
                'state'     => $state ?: PaymentState::CREATED->value,
                'clientKey' => $clientKey,
            ],
            200
        );
    }
}
