<?php /** @var array $rows */ /** @var array $totals */ ?>
<h1 class="mt-0"><?= e(__('admin.wallet')) ?></h1>

<div class="grid grid-3">
  <div class="stat info">
    <div class="stat-label"><?= e(__('admin.totals.wallet_total')) ?></div>
    <div class="stat-value"><?= e(rm($totals['wallet'])) ?></div>
  </div>
  <div class="stat success">
    <div class="stat-label">Total topups (paid)</div>
    <div class="stat-value"><?= e(rm($totals['topups'])) ?></div>
  </div>
  <div class="stat success">
    <div class="stat-label"><?= e(__('admin.totals.revenue')) ?></div>
    <div class="stat-value"><?= e(rm($totals['revenue'])) ?></div>
  </div>
</div>

<div class="card">
  <div class="table-wrap"><table class="table">
    <thead><tr>
      <th>Merchant</th><th>Wallet</th><th>Subscription</th><th>Status</th>
      <th>Total topup</th><th>Total fees</th>
    </tr></thead>
    <tbody>
      <?php foreach ($rows as $m): ?>
        <tr>
          <td><a href="<?= e(url('/admin/merchants/' . $m['id'])) ?>"><?= e($m['business_name']) ?></a></td>
          <td><?= e(rm($m['wallet_balance'])) ?></td>
          <td><span class="pill <?= $m['subscription_status'] === 'active' ? 'pill-success' : 'pill-warning' ?>"><?= e($m['subscription_status']) ?></span></td>
          <td><span class="pill <?= $m['status'] === 'active' ? 'pill-success' : 'pill-warning' ?>"><?= e($m['status']) ?></span></td>
          <td><?= e(rm($m['total_topup'])) ?></td>
          <td><?= e(rm($m['total_fees'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
