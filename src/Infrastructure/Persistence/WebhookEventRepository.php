<?php

namespace WooQRPh\Infrastructure\Persistence;

use wpdb;

defined( 'ABSPATH' ) || exit;

final class WebhookEventRepository {

    private wpdb $db;
    private string $table;

    public function __construct() {
        global $wpdb;
        $this->db    = $wpdb;
        $this->table = $wpdb->prefix . 'wooqrph_webhook_events';
    }

    /**
     * Attempt to register an event ID.
     *
     * Returns true if this is the FIRST time we see this event.
     * Returns false if the event was already processed.
     */
    public function acquire( string $eventId ): bool {

        $result = $this->db->query(
            $this->db->prepare(
                "INSERT IGNORE INTO {$this->table} (event_id, processed_at)
                 VALUES (%s, UTC_TIMESTAMP())",
                $eventId
            )
        );

        // INSERT IGNORE returns 1 on insert, 0 on duplicate
        return $result === 1;
    }
}
