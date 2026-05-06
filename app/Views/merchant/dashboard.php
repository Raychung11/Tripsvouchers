<?php
/** @var array $merchant */ /** @var array $stats */ /** @var array $recent */ /** @var string $wallet_status */
$walletPill = match ($wallet_status) {
    'healthy' => 'pill-success',
    'warning' => 'pill-warning',
    'critical', 'disabled' => 'pill-danger',
    default => 'pill',
};
$min = (float) config('config.pricing.wallet_min', 100);
?>
<h1 class="mt-0"><?= e($merchant['business_name']) ?></h1>
<p class="text-muted"><?= e(__('merchant.category_options.' . $merchant['category'])) ?></p>

<?php if ($merchant['subscription_status'] !== 'active'): ?>
  <div class="alert alert-warning">
    ⚠️ <?= e(__('merchant.sub_expired_msg')) ?>
    <a class="btn btn-warning btn-sm mt-1" href="<?= e(url('/merchant/subscription')) ?>"><?= e(__('merchant.sub_pay')) ?></a>
  </div>
<?php endif; ?>

<?php if ($wallet_status === 'disabled'): ?>
  <div class="alert alert-error">
    🚫 <?= e(__('merchant.redemption_disabled_msg', ['min' => $min])) ?>
    <a class="btn btn-danger btn-sm mt-1" href="<?= e(url('/merchant/wallet/topup')) ?>"><?= e(__('merchant.topup')) ?></a>
  </div>
<?php endif; ?>

<div class="grid grid-4">
  <div class="stat info">
    <div class="stat-label"><?= e(__('merchant.wallet_balance')) ?></div>
    <div class="stat-value"><?= e(rm($merchant['wallet_balance'])) ?></div>
    <div class="pill <?= e($walletPill) ?> mt-1"><?= e(__('merchant.wallet_status.' . $wallet_status)) ?></div>
  </div>
  <div class="stat success">
    <div class="stat-label"><?= e(__('admin.totals.redeemed')) ?></div>
    <div class="stat-value"><?= e((string) $stats['redeemed_total']) ?></div>
    <div class="text-muted" style="font-size:.85rem;">today: <?= e((string) $stats['redeemed_today']) ?></div>
  </div>
  <div class="stat">
    <div class="stat-label">Total fees</div>
    <div class="stat-value"><?= e(rm($stats['fees_total'])) ?></div>
  </div>
  <div class="stat">
    <div class="stat-label"><?= e(__('merchant.campaigns')) ?></div>
    <div class="stat-value"><?= e((string) $stats['campaigns']) ?></div>
  </div>
</div>

<div class="grid grid-2 mt-3">
  <a class="btn btn-primary btn-lg" href="<?= e(url('/merchant/scanner')) ?>">
    📷 <?= e(__('merchant.scan_title')) ?>
  </a>
  <a class="btn btn-outline btn-lg" href="<?= e(url('/merchant/wallet/topup')) ?>">
    💳 <?= e(__('merchant.topup')) ?>
  </a>
</div>

<div class="card mt-3">
  <h2 class="mt-0"><?= e(__('merchant.redemptions')) ?></h2>
  <?php if (empty($recent)): ?>
    <p class="text-muted"><?= e(__('merchant.no_redemptions')) ?></p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead><tr>
          <th><?= e(__('common.date')) ?></th>
          <th><?= e(__('voucher.code')) ?></th>
          <th><?= e(__('common.name')) ?></th>
          <th>Fee</th>
        </tr></thead>
        <tbody>
        <?php foreach ($recent as $r): ?>
          <tr>
            <td><?= e($r['redeemed_at']) ?></td>
            <td class="font-mono"><?= e($r['voucher_code']) ?></td>
            <td><?= e($r['customer_name']) ?></td>
            <td><?= e(rm($r['redemption_fee'])) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
