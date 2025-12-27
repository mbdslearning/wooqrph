<?php

namespace WooQRPh\Infrastructure\Webhooks;

use WP_REST_Request;
use WP_REST_Response;
use WooQRPh\Domain\PaymentIntent\StateMachine;
use WooQRPh\Domain\PaymentState\PaymentState;
use WooQRPh\Infrastructure\Persistence\OrderPaymentRepository;
use WooQRPh\Infrastructure\Persistence\WebhookEventRepository;

defined( 'ABSPATH' ) || exit;

final class Controller {

    public function register(): void {
        register_rest_route(
            'wooqrph/v1',
            '/webhook',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'handle' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function handle( WP_REST_Request $request ): WP_REST_Response {

        $rawPayload = $request->get_body();
        $signature  = $request->get_header( 'paymongo-signature' );

        if ( ! is_string( $signature ) ) {
            return new WP_REST_Response( [ 'error' => 'Missing signature' ], 400 );
        }

        $isLiveMode = (bool) apply_filters( 'wooqrph_is_live_mode', false );
        $secret     = apply_filters( 'wooqrph_webhook_secret', '' );

        $verifier = new Verifier();

        if ( ! $verifier->verify( $rawPayload, $signature, $secret, $isLiveMode ) ) {
            return new WP_REST_Response( [ 'error' => 'Invalid signature' ], 400 );
        }

        $event = json_decode( $rawPayload, true );

        if ( ! is_array( $event ) || empty( $event['id'] ) ) {
            return new WP_REST_Response( [ 'error' => 'Invalid event' ], 400 );
        }

        // 🔒 Idempotency gate
        $events = new WebhookEventRepository();

        if ( ! $events->acquire( $event['id'] ) ) {
            // Event already processed — MUST return 2xx to stop retries
            return new WP_REST_Response( [ 'duplicate' => true ], 200 );
        }

        $orderId = $event['data']['attributes']['metadata']['order_id'] ?? null;
        if ( ! $orderId ) {
            return new WP_REST_Response( [ 'ignored' => true ], 200 );
        }

        $order = wc_get_order( (int) $orderId );
        if ( ! $order ) {
            return new WP_REST_Response( [ 'not_found' => true ], 200 );
        }

        $newState = EventMapper::mapToState( $event['type'] ?? '' );
        if ( ! $newState ) {
            return new WP_REST_Response( [ 'ignored' => true ], 200 );
        }

        $current = PaymentState::from(
            $order->get_meta( '_wooqrph_payment_state' ) ?: PaymentState::CREATED->value
        );

        $final = StateMachine::transition( $current, $newState );

        ( new OrderPaymentRepository() )->updateState( $order, $final );

        if ( $final === PaymentState::PAID ) {
            $order->payment_complete();
        }

        return new WP_REST_Response( [ 'ok' => true ], 200 );
    }
}
