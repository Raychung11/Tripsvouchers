// SLV Voucher Goodie — small client-side helpers.
(function () {
  // Confirm-on-click for forms with data-confirm
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form && form.dataset && form.dataset.confirm) {
      if (!window.confirm(form.dataset.confirm)) {
        e.preventDefault();
      }
    }
  });

  // Auto-dismiss flash alerts after 5 seconds.
  document.querySelectorAll('.alert.auto-dismiss').forEach(function (el) {
    setTimeout(function () { el.style.display = 'none'; }, 5000);
  });

  // Render QR codes for elements with data-qr
  function renderQRCodes() {
    if (typeof QRCode === 'undefined') return;
    document.querySelectorAll('[data-qr]').forEach(function (node) {
      if (node.dataset.qrRendered) return;
      const url = node.dataset.qr;
      const size = parseInt(node.dataset.qrSize || '240', 10);
      new QRCode(node, {
        text: url,
        width: size,
        height: size,
        colorDark: '#0f172a',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M,
      });
      node.dataset.qrRendered = '1';
    });
  }
  renderQRCodes();
  window.SLV = window.SLV || {};
  window.SLV.renderQRCodes = renderQRCodes;

  // Share button (Web Share API fallback to copy)
  document.querySelectorAll('[data-share]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const url = btn.dataset.share;
      const title = btn.dataset.shareTitle || document.title;
      if (navigator.share) {
        navigator.share({ url: url, title: title }).catch(function () {});
      } else if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function () {
          btn.textContent = 'Copied!';
        });
      }
    });
  });
})();
