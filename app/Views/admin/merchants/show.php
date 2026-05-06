<?php /** @var array $merchant */ /** @var ?array $sub */ /** @var array $transactions */ /** @var array $redemptions */ ?>
<a href="<?= e(url('/admin/merchants')) ?>" class="text-muted">← <?= e(__('common.back')) ?></a>
<h1 class="mt-1"><?= e($merchant['business_name']) ?></h1>

<div class="grid grid-2">
  <div class="card">
    <h3 class="mt-0">Profile</h3>
    <p class="text-muted">Owner: <?= e($merchant['owner_name']) ?></p>
    <p class="text-muted"><?= e($merchant['email']) ?> · <?= e($merchant['phone']) ?></p>
    <p>Category: <?= e(__('merchant.category_options.' . $merchant['category'])) ?></p>
    <p>Address: <?= e($merchant['address'] ?? '—') ?></p>
    <p>Status: <span class="pill <?= $merchant['status'] === 'active' ? 'pill-success' : 'pill-warning' ?>"><?= e($merchant['status']) ?></span></p>

    <div class="flex gap-1 mt-2">
      <?php if ($merchant['status'] !== 'active'): ?>
        <form method="post" action="<?= e(url('/admin/merchants/' . $merchant['id'] . '/approve')) ?>">
          <?= csrf_field() ?>
          <button class="btn btn-success btn-sm"><?= e(__('admin.approve')) ?></button>
        </form>
      <?php endif; ?>
      <?php if ($merchant['status'] !== 'suspended'): ?>
        <form method="post" action="<?= e(url('/admin/merchants/' . $merchant['id'] . '/suspend')) ?>" data-confirm="Suspend this merchant?">
          <?= csrf_field() ?>
          <button class="btn btn-danger btn-sm"><?= e(__('admin.suspend')) ?></button>
        </form>
      <?php endif; ?>
    </div>
  </div>
  <div class="card">
    <h3 class="mt-0">Wallet & Subscription</h3>
    <div class="stat info">
      <div class="stat-label"><?= e(__('merchant.wallet_balance')) ?></div>
      <div class="stat-value"><?= e(rm($merchant['wallet_balance'])) ?></div>
    </div>
    <p class="mt-2">Subscription: <span class="pill <?= $merchant['subscription_status'] === 'active' ? 'pill-success' : 'pill-warning' ?>"><?= e($merchant['subscription_status']) ?></span></p>
    <?php if ($sub): ?>
      <p class="text-muted">Expires: <?= e($sub['expiry_date']) ?></p>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <h3 class="mt-0">Wallet transactions</h3>
  <?php if (empty($transactions)): ?>
    <p class="text-muted">—</p>
  <?php else: ?>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>After</th><th>Description</th></tr></thead>
      <tbody>
      <?php foreach ($transactions as $tx): ?>
        <tr>
          <td><?= e($tx['created_at']) ?></td>
          <td><span class="pill <?= $tx['amount'] >= 0 ? 'pill-success' : 'pill-danger' ?>"><?= e($tx['type']) ?></span></td>
          <td><?= e(rm($tx['amount'])) ?></td>
          <td><?= e(rm($tx['balance_after'])) ?></td>
          <td><?= e($tx['description'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div>

<div class="card">
  <h3 class="mt-0">Redemptions</h3>
  <?php if (empty($redemptions)): ?>
    <p class="text-muted">—</p>
  <?php else: ?>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Date</th><th>Voucher</th><th>Campaign</th><th>Fee</th></tr></thead>
      <tbody>
      <?php foreach ($redemptions as $r): ?>
        <tr>
          <td><?= e($r['redeemed_at']) ?></td>
          <td class="font-mono"><?= e($r['voucher_code']) ?></td>
          <td><?= e($r['campaign_name']) ?></td>
          <td><?= e(rm($r['redemption_fee'])) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div>
