<?php

namespace WooQRPh\Domain\PaymentIntent;

use WooQRPh\Domain\PaymentState\PaymentState;

defined( 'ABSPATH' ) || exit;

final class PaymentIntent {

    public function __construct(
        public readonly string $id,
        public readonly string $clientKey,
        public PaymentState $state,
        public readonly array $raw
    ) {}
}
