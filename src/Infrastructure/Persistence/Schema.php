<?php

namespace WooQRPh\Infrastructure\Persistence;

defined( 'ABSPATH' ) || exit;

final class Schema {

    public static function create(): void {
        global $wpdb;

        $table = $wpdb->prefix . 'wooqrph_webhook_events';
        $charset = $wpdb->get_charset_collate();

        $sql = "
        CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id VARCHAR(191) NOT NULL,
            processed_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY event_id (event_id)
        ) {$charset};
        ";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
