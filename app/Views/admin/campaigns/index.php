<?php /** @var array $rows */ ?>
<div class="flex-between">
  <h1 class="mt-0"><?= e(__('admin.campaigns')) ?></h1>
  <a href="<?= e(url('/admin/campaigns/create')) ?>" class="btn btn-primary">+ <?= e(__('admin.create')) ?></a>
</div>
<div class="card">
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Name</th><th>Location</th><th>Type</th><th>Value</th><th>Period</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $c): ?>
        <tr>
          <td><strong><?= e($c['campaign_name']) ?></strong>
            <div class="text-muted font-mono" style="font-size:.75rem;"><?= e($c['slug']) ?></div></td>
          <td><?= e($c['location_name'] ?? '—') ?></td>
          <td><?= e(__('campaign.types.' . $c['voucher_type'])) ?></td>
          <td><?= e(rm($c['voucher_value'])) ?></td>
          <td><?= e($c['start_date'] ?: '—') ?> → <?= e($c['end_date'] ?: '—') ?></td>
          <td>
            <span class="pill <?= $c['status'] === 'active' ? 'pill-success' : ($c['status'] === 'ended' ? 'pill-danger' : 'pill-warning') ?>">
              <?= e($c['status']) ?>
            </span>
          </td>
          <td>
            <a class="btn btn-outline btn-sm" href="<?= e(url('/admin/campaigns/' . $c['id'])) ?>">View</a>
            <a class="btn btn-sm" href="<?= e(url('/admin/campaigns/' . $c['id'] . '/edit')) ?>"><?= e(__('admin.edit')) ?></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
