<?php

namespace WooQRPh\Gateways\WooCommerce\Blocks;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

defined( 'ABSPATH' ) || exit;

final class QRPhBlock extends AbstractPaymentMethodType {

    protected $name = 'wooqrph';

    public function initialize(): void {
        $this->settings = get_option( 'woocommerce_wooqrph_settings', [] );
    }

    public function is_active(): bool {
        return ( $this->settings['enabled'] ?? 'no' ) === 'yes';
    }

    public function get_payment_method_script_handles(): array {
        wp_register_script(
            'wooqrph-blocks',
            plugins_url( '/assets/js/wooqrph-blocks.js', dirname( __FILE__, 4 ) ),
            [ 'wc-blocks-registry', 'wp-element', 'wp-i18n' ],
            '0.1.0',
            true
        );

        wp_localize_script(
            'wooqrph-blocks',
            'WooQRPhConfig',
            [
                'restUrl' => rest_url( 'wooqrph/v1' ),
            ]
        );

        return [ 'wooqrph-blocks' ];
    }

    public function get_payment_method_data(): array {
        return [
            'title'       => __( 'Dynamic QR Ph', 'wooqrph' ),
            'description' => __( 'Pay using Dynamic QR Ph.', 'wooqrph' ),
        ];
    }
}
