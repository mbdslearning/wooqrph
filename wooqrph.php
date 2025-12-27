<?php
/**
 * Plugin Name: WooQRPh – PayMongo Dynamic QR Ph
 * Description: Modern, extensible WooCommerce payment gateway for PayMongo Dynamic QR Ph.
 * Version: 0.1.0
 * Author: You
 */

defined( 'ABSPATH' ) || exit;

// -----------------------------------------------------------------------------
// Autoloader
// -----------------------------------------------------------------------------

require_once __DIR__ . '/vendor/autoload.php';

// -----------------------------------------------------------------------------
// Core bootstrap
// -----------------------------------------------------------------------------

use WooQRPh\Core\Hooks;
use WooQRPh\Core\Activator;

// -----------------------------------------------------------------------------
// Activation
// -----------------------------------------------------------------------------

register_activation_hook(
    __FILE__,
    [ Activator::class, 'activate' ]
);

// -----------------------------------------------------------------------------
// Runtime configuration (AUTHORITATIVE LOCATION)
// -----------------------------------------------------------------------------

/**
 * Determine PayMongo mode.
 *
 * true  = live mode (uses `li` signature)
 * false = test mode (uses `te` signature)
 */
add_filter(
    'wooqrph_is_live_mode',
    static function (): bool {
        return false; // CHANGE TO true IN PRODUCTION
    }
);

/**
 * PayMongo webhook secret key.
 *
 * IMPORTANT:
 * - This is the webhook secret_key
 * - NOT the API key
 */
add_filter(
    'wooqrph_webhook_secret',
    static function (): string {
        return 'whsec_xxxxxxxxxxxxxxxxxxxxx';
    }
);

/**
 * Webhook timestamp tolerance (replay protection).
 *
 * Default: 300 seconds (5 minutes)
 * Set to 0 to disable.
 */
add_filter(
    'wooqrph_webhook_timestamp_tolerance',
    static function (): int {
        return 300;
    }
);

// -----------------------------------------------------------------------------
// Plugin initialization
// -----------------------------------------------------------------------------

add_action(
    'plugins_loaded',
    static function (): void {
        Hooks::register();
    }
);
