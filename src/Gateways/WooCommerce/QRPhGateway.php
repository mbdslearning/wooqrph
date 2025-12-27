<?php

namespace WooQRPh\Gateways\WooCommerce;

use WC_Payment_Gateway;
use WooQRPh\Infrastructure\PayMongo\Client;
use WooQRPh\Infrastructure\Persistence\OrderPaymentRepository;
use WooQRPh\Payments\PaymentIntentService;

defined( 'ABSPATH' ) || exit;

final class QRPhGateway extends WC_Payment_Gateway {

    public function __construct() {
        $this->id                 = 'wooqrph';
        $this->method_title       = __( 'Dynamic QR Ph (PayMongo)', 'wooqrph' );
        $this->method_description = __( 'Pay via Dynamic QR Ph using PayMongo.', 'wooqrph' );
        $this->has_fields         = false;

        $this->supports = [ 'products' ];

        $this->init_form_fields();
        $this->init_settings();
    }

    public function init_form_fields(): void {
        $this->form_fields = [
            'enabled' => [
                'title'   => __( 'Enable/Disable', 'wooqrph' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable Dynamic QR Ph', 'wooqrph' ),
                'default' => 'no',
            ],
            'secret_key' => [
                'title' => __( 'PayMongo Secret Key', 'wooqrph' ),
                'type'  => 'password',
            ],
        ];
    }

    public function process_payment( $order_id ): array {
        $order = wc_get_order( $order_id );

        $client = new Client( $this->get_option( 'secret_key' ) );
        $repo   = new OrderPaymentRepository();

        $service = new PaymentIntentService( $client, $repo );
        $service->createDynamicQRPhIntent( $order );

        return [
            'result'   => 'success',
            'redirect' => $order->get_checkout_payment_url( true ),
        ];
    }
}
