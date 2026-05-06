<?php /** @var array $voucher */ /** @var array|null $campaign */ ?>
<div class="container-sm" style="max-width:520px;margin:0 auto;">
  <div class="card text-center">
    <h1 class="mt-0">🎁 <?= e($campaign['campaign_name'] ?? __('voucher.title')) ?></h1>
    <p class="font-mono"><?= e($voucher['voucher_code']) ?></p>
    <p>
      <span class="pill <?= $voucher['status'] === 'claimed' ? 'pill-success' : 'pill-info' ?>">
        <?= e(__('voucher.status_' . $voucher['status'])) ?>
      </span>
    </p>
    <p class="text-muted"><?= e(__('voucher.show_at')) ?></p>
    <a class="btn btn-primary" href="<?= e(url('/merchant/scanner')) ?>">
      <?= e(__('merchant.scan_title')) ?>
    </a>
    <p class="text-muted mt-2" style="font-size:.85rem;">
      Merchant login required to redeem.
    </p>
  </div>
</div>
