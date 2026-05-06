<?php
/** @var array $voucher */ /** @var array|null $campaign */ /** @var string $qr_url */
$statusKey = 'voucher.status_' . $voucher['status'];
$pillClass = match ($voucher['status']) {
    'claimed'  => 'pill-success',
    'redeemed' => 'pill-info',
    'expired'  => 'pill-warning',
    'void'     => 'pill-danger',
    default    => 'pill',
};
?>
<div class="container-sm" style="max-width:520px; margin:0 auto;">
  <article class="voucher-card">
    <div class="voucher-banner">
      🎁 <?= e($campaign['campaign_name'] ?? __('voucher.title')) ?>
    </div>
    <div class="text-center">
      <span class="pill <?= e($pillClass) ?>"><?= e(__($statusKey)) ?></span>
    </div>
    <p class="text-center font-bold" style="font-size:1.5rem; margin:8px 0 0;"><?= e(rm($campaign['voucher_value'] ?? 0)) ?></p>
    <p class="text-center text-muted" style="margin:0 0 16px;"><?= e($campaign['location_name'] ?? '') ?></p>

    <?php if ($voucher['status'] === 'claimed'): ?>
      <div class="voucher-qr"><div data-qr="<?= e($qr_url) ?>" data-qr-size="240"></div></div>
      <p class="text-center"><?= e(__('voucher.show_at')) ?></p>
    <?php elseif ($voucher['status'] === 'redeemed'): ?>
      <div class="alert alert-info text-center"><?= e(__('voucher.status_redeemed')) ?> · <?= e($voucher['redeemed_at']) ?></div>
    <?php else: ?>
      <div class="alert alert-warning text-center"><?= e(__($statusKey)) ?></div>
    <?php endif; ?>

    <div class="voucher-code"><?= e($voucher['voucher_code']) ?></div>

    <div class="grid grid-2 mt-2" style="font-size:.9rem;">
      <div><strong><?= e(__('voucher.code')) ?>:</strong><br><span class="font-mono"><?= e($voucher['voucher_code']) ?></span></div>
      <div><strong><?= e(__('voucher.expires')) ?>:</strong><br><?= e($voucher['expired_at'] ?? '—') ?></div>
    </div>

    <div class="flex gap-1 mt-2">
      <button class="btn btn-outline btn-block" data-share="<?= e(url('/voucher/' . $voucher['voucher_code'])) ?>" data-share-title="<?= e($campaign['campaign_name'] ?? '') ?>">
        <?= e(__('voucher.share')) ?>
      </button>
    </div>
  </article>

  <div class="card mt-2">
    <h3 class="mt-0"><?= e(__('voucher.how_redeem')) ?></h3>
    <ol style="padding-left:1.2em; line-height:1.8;">
      <li><?= e(__('voucher.redeem_step1')) ?></li>
      <li><?= e(__('voucher.redeem_step2')) ?></li>
      <li><?= e(__('voucher.redeem_step3')) ?></li>
    </ol>
  </div>
</div>

<?php \App\Core\View::pushScript('<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>'); ?>
<?php \App\Core\View::pushScript('<script>document.addEventListener("DOMContentLoaded",function(){window.SLV&&SLV.renderQRCodes&&SLV.renderQRCodes();});</script>'); ?>
