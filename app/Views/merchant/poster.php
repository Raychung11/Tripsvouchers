<?php /** @var array $merchant */ /** @var array $campaign */ /** @var string $claim_url */ ?>
<style>
  @media print { .topbar, .footer, .app-side, .no-print { display: none !important; } .app-main { padding: 0 !important; } }
  .poster {
    max-width: 720px; margin: 0 auto; background: linear-gradient(160deg, #0d9488 0%, #f59e0b 100%);
    color: #fff; border-radius: 18px; padding: 40px; text-align: center;
  }
  .poster .qr-card { background: #fff; padding: 16px; border-radius: 16px; display: inline-block; margin: 24px auto; }
  .poster h1 { font-size: 2rem; margin: 0; }
  .poster .value { font-size: 4rem; font-weight: 800; margin: 16px 0; }
</style>
<div class="no-print mb-2 flex gap-1">
  <a class="btn btn-outline" href="<?= e(url('/merchant/marketing')) ?>">← <?= e(__('common.back')) ?></a>
  <button class="btn btn-primary" onclick="window.print()">🖨 Print</button>
</div>
<div class="poster">
  <h1><?= e($campaign['campaign_name']) ?></h1>
  <p style="opacity:.9;">at <?= e($merchant['business_name']) ?></p>
  <div class="value"><?= e(rm($campaign['voucher_value'])) ?></div>
  <div class="qr-card"><div data-qr="<?= e($claim_url) ?>" data-qr-size="240"></div></div>
  <p style="font-size:1.2rem; margin: 16px 0 0;">📱 Scan to claim your voucher</p>
  <p style="font-size:.85rem; opacity:.85; margin-top:8px;"><?= e($claim_url) ?></p>
  <p style="margin-top:24px; opacity:.85;">Powered by SLV Voucher Goodie</p>
</div>
<?php
\App\Core\View::pushScript('<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>');
\App\Core\View::pushScript('<script>document.addEventListener("DOMContentLoaded",function(){window.SLV&&SLV.renderQRCodes&&SLV.renderQRCodes();});</script>');
?>
