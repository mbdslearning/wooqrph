<?php

namespace WooQRPh\Gateways\WooCommerce\Blocks;

defined( 'ABSPATH' ) || exit;

final class Registrar {

    public static function register(): void {
        add_action(
            'woocommerce_blocks_loaded',
            static function () {
                if ( ! class_exists( '\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
                    return;
                }

                add_action(
                    'woocommerce_blocks_payment_method_type_registration',
                    static function ( $registry ) {
                        $registry->register( new QRPhBlock() );
                    }
                );
            }
        );
    }
}
