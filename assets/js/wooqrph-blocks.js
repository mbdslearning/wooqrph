import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { createElement, useEffect } from '@wordpress/element';

const QRPhContent = ( { eventRegistration, emitResponse } ) => {
    useEffect( () => {
        const unsubscribe = eventRegistration.onPaymentSetup( async () => {
            emitResponse( {
                type: 'success',
            } );
        } );

        return () => unsubscribe();
    }, [] );

    return createElement(
        'div',
        { className: 'wooqrph-blocks-placeholder' },
        'You will be prompted to scan a Dynamic QR Ph after placing the order.'
    );
};

registerPaymentMethod( {
    name: 'wooqrph',
    label: 'Dynamic QR Ph',
    content: createElement( QRPhContent ),
    edit: createElement( QRPhContent ),
    canMakePayment: () => true,
    ariaLabel: 'Dynamic QR Ph payment method',
    supports: {
        features: [ 'products' ],
    },
} );
