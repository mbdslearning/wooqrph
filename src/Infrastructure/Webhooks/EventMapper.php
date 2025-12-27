<?php

namespace WooQRPh\Infrastructure\Webhooks;

use WooQRPh\Domain\PaymentState\PaymentState;

defined( 'ABSPATH' ) || exit;

final class EventMapper {

    public static function mapToState( string $eventType ): ?PaymentState {
        return match ( $eventType ) {
            'payment_intent.succeeded' => PaymentState::PAID,
            'payment_intent.failed'    => PaymentState::FAILED,
            'payment_intent.expired'   => PaymentState::EXPIRED,
            default                    => null,
        };
    }
}
