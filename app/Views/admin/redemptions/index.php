<?php /** @var array $rows */ ?>
<div class="flex-between">
  <h1 class="mt-0"><?= e(__('admin.redemptions')) ?></h1>
  <a class="btn btn-outline" href="<?= e(url('/admin/export/redemptions')) ?>"><?= e(__('admin.export')) ?></a>
</div>
<div class="card">
  <div class="table-wrap"><table class="table">
    <thead><tr>
      <th>Date</th><th>Voucher</th><th>Campaign</th><th>Merchant</th>
      <th>Customer</th><th>Phone</th><th>Fee</th>
    </tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['redeemed_at']) ?></td>
          <td class="font-mono"><?= e($r['voucher_code']) ?></td>
          <td><?= e($r['campaign_name']) ?></td>
          <td><?= e($r['business_name']) ?></td>
          <td><?= e($r['customer_name']) ?></td>
          <td><?= e($r['customer_phone']) ?></td>
          <td><?= e(rm($r['redemption_fee'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
