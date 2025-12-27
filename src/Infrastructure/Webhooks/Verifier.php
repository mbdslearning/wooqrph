<?php

namespace WooQRPh\Infrastructure\Webhooks;

defined( 'ABSPATH' ) || exit;

/**
 * PayMongo Webhook Signature Verifier
 *
 * Implements the officially documented algorithm:
 * - Paymongo-Signature header parsing (t, te, li)
 * - HMAC-SHA256 over "timestamp.payload"
 * - Constant-time comparison
 * - Optional replay protection
 *
 * This implementation uses NO undocumented assumptions.
 */
final class Verifier {

    /**
     * Maximum allowed clock skew in seconds (replay protection).
     *
     * PayMongo does not specify a value.
     * 300s (5 minutes) is an industry-standard default.
     *
     * Filterable to avoid hard assumptions.
     */
    private int $tolerance;

    public function __construct() {
        $this->tolerance = (int) apply_filters(
            'wooqrph_webhook_timestamp_tolerance',
            300
        );
    }

    /**
     * Verify PayMongo webhook signature.
     *
     * @param string $rawPayload Raw request body (php://input)
     * @param string $signatureHeader Value of Paymongo-Signature header
     * @param string $secret Webhook secret_key from PayMongo
     * @param bool   $isLiveMode Whether the webhook is live or test
     *
     * @return bool
     */
    public function verify(
        string $rawPayload,
        string $signatureHeader,
        string $secret,
        bool $isLiveMode
    ): bool {

        if ( $signatureHeader === '' || $secret === '' ) {
            return false;
        }

        $parts = $this->parseSignatureHeader( $signatureHeader );

        if ( ! isset( $parts['t'] ) ) {
            return false;
        }

        $timestamp = (int) $parts['t'];

        // Replay protection (optional but recommended by PayMongo)
        if ( ! $this->isTimestampValid( $timestamp ) ) {
            return false;
        }

        $receivedSignature = $isLiveMode
            ? ( $parts['li'] ?? null )
            : ( $parts['te'] ?? null );

        if ( ! is_string( $receivedSignature ) ) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $rawPayload;

        $expectedSignature = hash_hmac(
            'sha256',
            $signedPayload,
            $secret
        );

        return hash_equals( $expectedSignature, $receivedSignature );
    }

    /**
     * Parse Paymongo-Signature header into key-value pairs.
     *
     * Example:
     * t=1496734173,te=abc...,li=xyz...
     */
    private function parseSignatureHeader( string $header ): array {
        $result = [];

        foreach ( explode( ',', $header ) as $part ) {
            $pair = explode( '=', trim( $part ), 2 );
            if ( count( $pair ) === 2 ) {
                $result[ $pair[0] ] = $pair[1];
            }
        }

        return $result;
    }

    /**
     * Validate timestamp against tolerance window.
     */
    private function isTimestampValid( int $timestamp ): bool {
        if ( $this->tolerance <= 0 ) {
            return true;
        }

        return abs( time() - $timestamp ) <= $this->tolerance;
    }
}
