<?php /** @var array $rows */ ?>
<style>
  .loc-thumb {
    width: 64px; height: 40px; border-radius: 8px;
    background-size: cover; background-position: center;
    background-color: #e2e8f0; display: inline-block;
  }
  .loc-thumb.fallback {
    background-image:
      radial-gradient(circle at 30% 30%, rgba(102,187,106,.6), transparent 60%),
      linear-gradient(135deg, var(--c-primary, #0D47A1), var(--c-primary-dk, #093479));
  }
</style>
<div class="flex-between">
  <h1 class="mt-0"><?= e(__('admin.locations')) ?></h1>
  <a href="<?= e(url('/admin/locations/create')) ?>" class="btn btn-primary">+ <?= e(__('admin.create')) ?></a>
</div>
<div class="card">
  <div class="table-wrap"><table class="table">
    <thead><tr><th></th><th>State</th><th>City</th><th>Area</th><th>Slug</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $l): ?>
        <tr>
          <td>
            <?php if (!empty($l['banner_image'])): ?>
              <span class="loc-thumb" style="background-image: url('<?= e(asset_or_upload($l['banner_image'])) ?>');"></span>
            <?php else: ?>
              <span class="loc-thumb fallback"></span>
            <?php endif; ?>
          </td>
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
