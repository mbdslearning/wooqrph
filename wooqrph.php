<?php
/**
 * Plugin Name: WooQRPh – PayMongo Dynamic QR Ph
 * Description: Modern, extensible WooCommerce payment gateway with Dynamic QR Ph (PayMongo).
 * Version: 0.1.0
 * Author: AL
 * Text Domain: wooqrph
 * Requires PHP: 8.1
 * Requires at least: 6.3
 * WC requires at least: 8.5
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

use WooQRPh\Core\Plugin;
use WooQRPh\Core\Activator;
use WooQRPh\Core\Deactivator;

register_activation_hook( __FILE__, [ Activator::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ Deactivator::class, 'deactivate' ] );

add_action(
    'plugins_loaded',
    static function () {
        if ( ! class_exists( \WooCommerce::class ) ) {
            return;
        }

        Plugin::instance()->boot();
    },
    0
);

add_filter( 'wooqrph_is_live_mode', fn () => true );
add_filter( 'wooqrph_webhook_secret', fn () => 'whsec_xxx' );
add_filter( 'wooqrph_webhook_timestamp_tolerance', fn () => 300 );

