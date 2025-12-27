<?php

namespace WooQRPh\Domain\PaymentState;

defined( 'ABSPATH' ) || exit;

enum PaymentState: string {
    case CREATED         = 'created';
    case QR_GENERATED    = 'qr_generated';
    case PENDING_PAYMENT = 'pending_payment';
    case PAID            = 'paid';
    case FAILED          = 'failed';
    case EXPIRED         = 'expired';
}
