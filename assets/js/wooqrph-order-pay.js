( function () {
    if ( typeof WooQRPhConfig === 'undefined' ) {
        return;
    }

    const params = new URLSearchParams( window.location.search );
    const orderId = params.get( 'order_id' );

    if ( ! orderId ) {
        return;
    }

    const poll = async () => {
        const res = await fetch(
            `${WooQRPhConfig.restUrl}/payment/${orderId}`,
            { credentials: 'same-origin' }
        );
        const data = await res.json();

        if ( data.state === 'paid' ) {
            window.location.reload();
        }
    };

    setInterval( poll, 3000 );
} )();
