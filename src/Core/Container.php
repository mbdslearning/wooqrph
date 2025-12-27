<?php

namespace WooQRPh\Core;

use RuntimeException;

defined( 'ABSPATH' ) || exit;

final class Container {

    private array $bindings = [];
    private array $instances = [];

    public function singleton( string $id, callable $factory ): void {
        $this->bindings[ $id ] = $factory;
    }

    public function get( string $id ): mixed {
        if ( isset( $this->instances[ $id ] ) ) {
            return $this->instances[ $id ];
        }

        if ( ! isset( $this->bindings[ $id ] ) ) {
            throw new RuntimeException( "Service not registered: {$id}" );
        }

        $this->instances[ $id ] = ( $this->bindings[ $id ] )();

        return $this->instances[ $id ];
    }
}
