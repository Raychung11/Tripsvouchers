<?php
/** @var ?array $location */
$action = $location ? url('/admin/locations/' . $location['id']) : url('/admin/locations');
$g = fn (string $k, $d = '') => e((string) old($k, $location[$k] ?? $d));
?>
<a href="<?= e(url('/admin/locations')) ?>" class="text-muted">← <?= e(__('common.back')) ?></a>
<h1 class="mt-1"><?= e($location ? __('admin.edit') : __('admin.create')) ?> · <?= e(__('admin.locations')) ?></h1>

<form class="card" method="post" action="<?= e($action) ?>" style="max-width:680px;">
  <?= csrf_field() ?>
  <div class="grid grid-2">
    <div class="field"><label>State *</label><input class="input" name="state" required value="<?= $g('state') ?>"></div>
    <div class="field"><label>City</label><input class="input" name="city" value="<?= $g('city') ?>"></div>
  </div>
  <div class="field"><label>Area / Tourism Zone *</label><input class="input" name="area_name" required value="<?= $g('area_name') ?>"></div>
  <div class="field"><label>Description</label><textarea class="input" name="description"><?= $g('description') ?></textarea></div>
  <div class="grid grid-2">
    <div class="field"><label>Banner URL</label><input class="input" name="banner_image" value="<?= $g('banner_image') ?>"></div>
    <div class="field"><label>Google Map link</label><input class="input" name="map_link" value="<?= $g('map_link') ?>"></div>
  </div>
  <div class="field">
    <label>Status</label>
    <select class="input" name="status">
      <option value="active"   <?= ($location['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
      <option value="inactive" <?= ($location['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
    </select>
  </div>
  <button class="btn btn-primary btn-lg"><?= e(__('admin.save')) ?></button>
</form>
