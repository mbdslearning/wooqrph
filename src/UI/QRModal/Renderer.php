<?php

namespace WooQRPh\UI\QRModal;

defined( 'ABSPATH' ) || exit;

final class Renderer {

    public static function render(): void {
        ?>
        <div class="wooqrph-overlay" aria-hidden="true"></div>
        <div class="wooqrph-modal" role="dialog" aria-modal="true">
            <div class="wooqrph-card">
                <div class="wooqrph-header">
                    <div class="wooqrph-title"><?php esc_html_e( 'Scan to Pay', 'wooqrph' ); ?></div>
                    <button class="wooqrph-close" aria-label="<?php esc_attr_e( 'Close', 'wooqrph' ); ?>">×</button>
                </div>
                <div class="wooqrph-body">
                    <img id="wooqrph-qr" class="wooqrph-qr" alt="QR Code" />
                    <div id="wooqrph-status" class="wooqrph-status">
                        <?php esc_html_e( 'Preparing QR…', 'wooqrph' ); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
