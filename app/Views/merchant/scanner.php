<?php /** @var array $merchant */ /** @var bool $can_redeem */ /** @var string $reason */ ?>
<h1 class="mt-0"><?= e(__('merchant.scan_title')) ?></h1>
<p class="text-muted"><?= e(__('merchant.scan_hint')) ?></p>

<?php if (!$can_redeem): ?>
  <div class="alert alert-error">⚠️ <?= e($reason) ?></div>
<?php endif; ?>

<div class="card">
  <div id="qr-result" class="alert alert-info hidden"></div>
  <div class="scanner-frame">
    <div id="qr-reader"></div>
  </div>
  <p class="text-muted text-center mt-1" style="font-size:.85rem;">Camera permission required.</p>

  <hr>
  <h3 class="mt-0"><?= e(__('merchant.manual_entry')) ?></h3>
  <form method="post" action="<?= e(url('/merchant/scanner/redeem')) ?>" id="manual-form">
    <?= csrf_field() ?>
    <div class="field">
      <label><?= e(__('merchant.voucher_code')) ?></label>
      <input class="input" name="code" placeholder="SLV-XXX-2026-XXXXXX" required <?= $can_redeem ? '' : 'disabled' ?>>
    </div>
    <button class="btn btn-primary btn-lg btn-block" <?= $can_redeem ? '' : 'disabled' ?>><?= e(__('merchant.redeem')) ?></button>
  </form>
</div>

<?php
$endpoint = url('/merchant/scanner/redeem');
$canRedeemJs = $can_redeem ? 'true' : 'false';
\App\Core\View::pushScript('<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>');
\App\Core\View::pushScript('<script>
(function(){
  if (!' . $canRedeemJs . ') return;
  const csrf = document.querySelector(\'#manual-form [name=_csrf]\').value;
  const result = document.getElementById("qr-result");
  let scanning = false;
  function show(html, cls){
    result.className = "alert alert-" + cls;
    result.innerHTML = html;
    result.classList.remove("hidden");
  }
  async function redeem(payload){
    try {
      const fd = new FormData();
      fd.append("_csrf", csrf);
      Object.keys(payload).forEach(k => fd.append(k, payload[k]));
      const res = await fetch("' . $endpoint . '", {
        method: "POST", body: fd, headers: { "X-Requested-With": "XMLHttpRequest" }
      });
      const data = await res.json();
      if (data.ok) {
        show("✅ " + data.message + "<br><strong>" + data.voucher_code + "</strong>", "success");
        setTimeout(() => location.href = "' . url('/merchant/redemptions') . '", 1500);
      } else {
        show("❌ " + (data.message || "Error"), "error");
      }
    } catch (e) {
      show("Network error.", "error");
    }
  }
  const reader = new Html5Qrcode("qr-reader");
  Html5Qrcode.getCameras().then(cams => {
    if (!cams || !cams.length) {
      show("No camera found. Use manual entry.", "warning");
      return;
    }
    const camId = cams[cams.length - 1].id;
    reader.start(
      camId,
      { fps: 10, qrbox: { width: 240, height: 240 } },
      (decoded) => {
        if (scanning) return;
        scanning = true;
        reader.stop().catch(() => {});
        redeem({ token: decoded });
      },
      () => {}
    ).catch(err => {
      show("Camera error: " + err, "error");
    });
  });

  const manual = document.getElementById("manual-form");
  manual.addEventListener("submit", function(e){
    e.preventDefault();
    const code = manual.querySelector("[name=code]").value.trim();
    if (code) redeem({ code });
  });
})();
</script>');
?>
