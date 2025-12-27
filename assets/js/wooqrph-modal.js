(function () {
  if (window.WooQRPhModal) return;

  function qs(sel, ctx) { return (ctx || document).querySelector(sel); }

  const Modal = {
    open() {
      qs('.wooqrph-overlay').style.display = 'block';
      qs('.wooqrph-modal').style.display = 'flex';
      qs('.wooqrph-close').focus();
    },
    close() {
      qs('.wooqrph-overlay').style.display = 'none';
      qs('.wooqrph-modal').style.display = 'none';
    }
  };

  window.WooQRPhModal = Modal;

  document.addEventListener('click', function (e) {
    if (e.target.matches('.wooqrph-close') || e.target.matches('.wooqrph-overlay')) {
      Modal.close();
    }
  });
})();
