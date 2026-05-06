<?php /** @var array $rows */ ?>
<div class="flex-between">
  <h1 class="mt-0"><?= e(__('admin.locations')) ?></h1>
  <a href="<?= e(url('/admin/locations/create')) ?>" class="btn btn-primary">+ <?= e(__('admin.create')) ?></a>
</div>
<div class="card">
  <div class="table-wrap"><table class="table">
    <thead><tr><th>State</th><th>City</th><th>Area</th><th>Slug</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $l): ?>
        <tr>
          <td><?= e($l['state']) ?></td>
          <td><?= e($l['city'] ?? '—') ?></td>
          <td><?= e($l['area_name']) ?></td>
          <td class="font-mono"><?= e($l['slug']) ?></td>
          <td><span class="pill <?= $l['status'] === 'active' ? 'pill-success' : 'pill-warning' ?>"><?= e($l['status']) ?></span></td>
          <td><a class="btn btn-outline btn-sm" href="<?= e(url('/admin/locations/' . $l['id'] . '/edit')) ?>"><?= e(__('admin.edit')) ?></a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
