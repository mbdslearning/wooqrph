<?php

namespace WooQRPh\Core;

defined( 'ABSPATH' ) || exit;

final class Plugin {

    private static ?self $instance = null;
    private Container $container;

    private function __construct() {}

    public static function instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function boot(): void {
        $this->container = new Container();

        $this->register_core_services();
        $this->register_hooks();
    }

    private function register_core_services(): void {
        $this->container->singleton( Container::class, fn () => $this->container );
    }

    private function register_hooks(): void {
        add_filter(
            'woocommerce_payment_gateways',
            [ $this, 'register_gateway' ]
        );

        Hooks::register();
    }

    public function register_gateway( array $gateways ): array {
        $gateways[] = \WooQRPh\Gateways\WooCommerce\QRPhGateway::class;
        return $gateways;
    }
}
