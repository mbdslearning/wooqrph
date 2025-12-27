(function () {
  if (typeof WooQRPhConfig === 'undefined') return;

  const stateEl = document.getElementById('wooqrph-status');
  const qrEl = document.getElementById('wooqrph-qr');

  let stopped = false;

  async function fetchStatus(orderId) {
    const res = await fetch(`${WooQRPhConfig.restUrl}/payment/${orderId}`, {
      credentials: 'same-origin'
    });
    if (!res.ok) throw new Error('Failed to fetch status');
    return res.json();
  }

  function renderQR(clientKey) {
    // Minimal QR rendering using a public QR generator
    // Replaceable later with a dedicated renderer
    const url = `https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=${encodeURIComponent(clientKey)}`;
    qrEl.src = url;
  }

  async function loop(orderId) {
    try {
      const data = await fetchStatus(orderId);

      if (data.state === 'paid') {
        stateEl.textContent = 'Payment received. Finalizing...';
        stopped = true;
        window.location.reload();
        return;
      }

      if (data.state === 'failed' || data.state === 'expired') {
        stateEl.textContent = 'Payment failed or expired.';
        stopped = true;
        return;
      }

      if (data.clientKey && !qrEl.src) {
        renderQR(data.clientKey);
        stateEl.textContent = 'Scan the QR code with your banking app.';
      }
    } catch (e) {
      stateEl.textContent = 'Waiting for payment...';
    }

    if (!stopped) {
      setTimeout(() => loop(orderId), 3000);
    }
  }

  window.WooQRPhApp = {
    start(orderId) {
      loop(orderId);
    }
  };
})();
