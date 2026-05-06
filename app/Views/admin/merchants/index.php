<?php /** @var array $rows */ ?>
<h1 class="mt-0"><?= e(__('admin.merchants')) ?></h1>
<div class="card">
  <div class="table-wrap">
    <table class="table">
      <thead><tr>
        <th>Business</th><th>Owner</th><th>Email</th><th>Category</th><th>Wallet</th>
        <th>Subscription</th><th>Status</th><th></th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $m): ?>
        <tr>
          <td><strong><?= e($m['business_name']) ?></strong>
            <div class="text-muted" style="font-size:.8rem;"><?= e($m['location_name'] ?? '') ?></div>
          </td>
          <td><?= e($m['owner_name']) ?></td>
          <td><?= e($m['email']) ?></td>
          <td><?= e(__('merchant.category_options.' . $m['category'])) ?></td>
          <td><?= e(rm($m['wallet_balance'])) ?></td>
          <td>
            <span class="pill <?= $m['subscription_status'] === 'active' ? 'pill-success' : 'pill-warning' ?>">
              <?= e($m['subscription_status']) ?>
            </span>
          </td>
          <td>
            <span class="pill <?= $m['status'] === 'active' ? 'pill-success' : ($m['status'] === 'suspended' ? 'pill-danger' : 'pill-warning') ?>">
              <?= e($m['status']) ?>
            </span>
          </td>
          <td><a class="btn btn-sm btn-outline" href="<?= e(url('/admin/merchants/' . $m['id'])) ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
