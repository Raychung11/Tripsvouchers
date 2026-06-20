<?php /** @var array $rows */ ?>
<style>
  .vch-thumb {
    width: 48px; height: 32px; border-radius: 6px;
    background-size: cover; background-position: center;
    background-color: #e2e8f0; display: inline-block;
  }
  .vch-thumb.fallback {
    background-image:
      radial-gradient(circle at 30% 30%, rgba(102,187,106,.6), transparent 60%),
      linear-gradient(135deg, var(--c-primary, #0D47A1), var(--c-primary-dk, #093479));
  }
</style>
<div class="flex-between">
  <h1 class="mt-0"><?= e(__('admin.vouchers')) ?></h1>
  <a class="btn btn-outline" href="<?= e(url('/admin/export/vouchers')) ?>"><?= e(__('admin.export')) ?></a>
</div>
<div class="card">
  <div class="table-wrap"><table class="table">
    <thead><tr>
      <th></th><th>Code</th><th>Campaign</th><th>Customer</th><th>Phone</th>
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
          <td>
            <?php if (!empty($v['campaign_banner'])): ?>
              <span class="vch-thumb" style="background-image: url('<?= e(asset_or_upload($v['campaign_banner'])) ?>');"></span>
            <?php else: ?>
              <span class="vch-thumb fallback"></span>
            <?php endif; ?>
          </td>
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
