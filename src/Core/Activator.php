<?php

namespace WooQRPh\Core;

use WooQRPh\Infrastructure\Persistence\Schema;

defined( 'ABSPATH' ) || exit;

final class Activator {

    public static function activate(): void {
        self::add_version_option();
        flush_rewrite_rules();
        Schema::create();
    }

    private static function add_version_option(): void {
        if ( get_option( 'wooqrph_version' ) === false ) {
            add_option( 'wooqrph_version', '0.1.0', '', false );
        }
    }
}
