<?php

namespace WooQRPh\Core;

use WooQRPh\Rest\Controllers\PaymentController;
use WooQRPh\Infrastructure\Webhooks\Controller as WebhookController;
use WooQRPh\UI\Assets\Enqueue;
use WooQRPh\Gateways\WooCommerce\Blocks\Registrar;

defined( 'ABSPATH' ) || exit;

final class Hooks {

    public static function register(): void {
        add_action( 'rest_api_init', function () {
            ( new PaymentController() )->register();
            ( new WebhookController() )->register();
        } );

        Enqueue::register();
        Registrar::register();
    }
}
