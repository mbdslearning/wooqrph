<?php

namespace WooQRPh\UI\Assets;

use WooQRPh\UI\QRModal\Renderer;

defined( 'ABSPATH' ) || exit;

final class Enqueue {

    public static function register(): void {
        add_action( 'wp_enqueue_scripts', [ self::class, 'enqueue' ] );
        add_action( 'woocommerce_before_thankyou', [ Renderer::class, 'render' ] );
    }

    public static function enqueue(): void {
        if ( ! is_checkout() && ! is_order_received_page() ) {
            return;
        }

        wp_enqueue_style(
            'wooqrph-modal',
            plugins_url( '/assets/css/wooqrph-modal.css', dirname( __FILE__, 3 ) ),
            [],
            '0.1.0'
        );

        wp_enqueue_script(
            'wooqrph-modal',
            plugins_url( '/assets/js/wooqrph-modal.js', dirname( __FILE__, 3 ) ),
            [],
            '0.1.0',
            true
        );

        wp_enqueue_script(
            'wooqrph-app',
            plugins_url( '/assets/js/wooqrph-app.js', dirname( __FILE__, 3 ) ),
            [],
            '0.1.0',
            true
        );

        wp_localize_script(
            'wooqrph-app',
            'WooQRPhConfig',
            [
                'restUrl' => rest_url( 'wooqrph/v1' ),
            ]
        );
    }
}
