<?php
/** @var array $merchant */ /** @var array $topups */ /** @var array $transactions */ /** @var string $wallet_status */
$walletPill = match ($wallet_status) {
    'healthy' => 'pill-success',
    'warning' => 'pill-warning',
    'critical', 'disabled' => 'pill-danger',
    default => 'pill',
};
?>
<h1 class="mt-0"><?= e(__('merchant.wallet')) ?></h1>

<div class="grid grid-2">
  <div class="stat info">
    <div class="stat-label"><?= e(__('merchant.wallet_balance')) ?></div>
    <div class="stat-value"><?= e(rm($merchant['wallet_balance'])) ?></div>
    <div class="pill <?= e($walletPill) ?> mt-1"><?= e(__('merchant.wallet_status.' . $wallet_status)) ?></div>
  </div>
  <div class="card flex flex-col" style="justify-content:center;">
    <a class="btn btn-primary btn-lg btn-block" href="<?= e(url('/merchant/wallet/topup')) ?>">+ <?= e(__('merchant.topup')) ?></a>
    <p class="text-muted mt-1" style="margin:8px 0 0; font-size:.85rem;">
      <?= e(__('merchant.topup_min', ['amount' => rm(config('config.pricing.wallet_min_reload', 100))])) ?>
    </p>
  </div>
</div>

<div class="card mt-3">
  <h3 class="mt-0">Top-up history</h3>
  <?php if (empty($topups)): ?>
    <p class="text-muted">—</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Date</th><th>Amount</th><th>Bill ID</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($topups as $t): ?>
            <tr>
              <td><?= e($t['created_at']) ?></td>
              <td><?= e(rm($t['amount'])) ?></td>
              <td class="font-mono"><?= e($t['billplz_bill_id'] ?: '—') ?></td>
              <td><span class="pill <?= $t['payment_status'] === 'paid' ? 'pill-success' : ($t['payment_status'] === 'failed' ? 'pill-danger' : 'pill-warning') ?>"><?= e($t['payment_status']) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h3 class="mt-0">Transactions</h3>
  <?php if (empty($transactions)): ?>
    <p class="text-muted">—</p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Balance after</th><th>Description</th></tr></thead>
        <tbody>
          <?php foreach ($transactions as $tx): ?>
            <tr>
              <td><?= e($tx['created_at']) ?></td>
              <td><span class="pill <?= $tx['amount'] >= 0 ? 'pill-success' : 'pill-danger' ?>"><?= e($tx['type']) ?></span></td>
              <td class="<?= $tx['amount'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= ($tx['amount'] >= 0 ? '+' : '') ?><?= e(rm($tx['amount'])) ?></td>
              <td><?= e(rm($tx['balance_after'])) ?></td>
              <td><?= e($tx['description'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
