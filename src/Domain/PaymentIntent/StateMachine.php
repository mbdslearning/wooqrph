<?php

namespace WooQRPh\Domain\PaymentIntent;

use WooQRPh\Domain\PaymentState\PaymentState;
use RuntimeException;

defined( 'ABSPATH' ) || exit;

final class StateMachine {

    private const ALLOWED_TRANSITIONS = [
        PaymentState::CREATED->value => [
            PaymentState::QR_GENERATED,
            PaymentState::FAILED,
        ],
        PaymentState::QR_GENERATED->value => [
            PaymentState::PENDING_PAYMENT,
            PaymentState::EXPIRED,
        ],
        PaymentState::PENDING_PAYMENT->value => [
            PaymentState::PAID,
            PaymentState::FAILED,
            PaymentState::EXPIRED,
        ],
    ];

    public static function transition(
        PaymentState $from,
        PaymentState $to
    ): PaymentState {
        if ( $from === $to ) {
            return $from;
        }

        $allowed = self::ALLOWED_TRANSITIONS[ $from->value ] ?? [];

        if ( ! in_array( $to, $allowed, true ) ) {
            throw new RuntimeException(
                sprintf(
                    'Invalid payment state transition: %s → %s',
                    $from->value,
                    $to->value
                )
            );
        }

        return $to;
    }
}
