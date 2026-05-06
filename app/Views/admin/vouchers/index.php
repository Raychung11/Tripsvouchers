<?php /** @var array $rows */ ?>
<div class="flex-between">
  <h1 class="mt-0"><?= e(__('admin.vouchers')) ?></h1>
  <a class="btn btn-outline" href="<?= e(url('/admin/export/vouchers')) ?>"><?= e(__('admin.export')) ?></a>
</div>
<div class="card">
  <div class="table-wrap"><table class="table">
    <thead><tr>
      <th>Code</th><th>Campaign</th><th>Customer</th><th>Phone</th>
      <th>Status</th><th>Claimed</th><th>Redeemed at</th><th>Merchant</th>
    </tr></thead>
    <tbody>
      <?php foreach ($rows as $v):
        $cls = match ($v['status']) {
            'claimed'  => 'pill-success',
            'redeemed' => 'pill-info',
            'expired'  => 'pill-warning',
            default    => 'pill-danger',
        };
      ?>
        <tr>
          <td class="font-mono"><?= e($v['voucher_code']) ?></td>
          <td><?= e($v['campaign_name']) ?></td>
          <td><?= e($v['customer_name']) ?></td>
          <td><?= e($v['customer_phone']) ?></td>
          <td><span class="pill <?= e($cls) ?>"><?= e($v['status']) ?></span></td>
          <td><?= e($v['claimed_at']) ?></td>
          <td><?= e($v['redeemed_at'] ?? '—') ?></td>
          <td><?= e($v['business_name'] ?? '—') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
