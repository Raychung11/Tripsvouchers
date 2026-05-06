<?php
/** @var ?array $campaign */ /** @var array $locations */
$action = $campaign ? url('/admin/campaigns/' . $campaign['id']) : url('/admin/campaigns');
$g = fn (string $k, $d = '') => e((string) old($k, $campaign[$k] ?? $d));
?>
<a href="<?= e(url('/admin/campaigns')) ?>" class="text-muted">← <?= e(__('common.back')) ?></a>
<h1 class="mt-1"><?= e($campaign ? __('admin.edit') : __('admin.create')) ?> · <?= e(__('admin.campaigns')) ?></h1>

<form class="card" method="post" action="<?= e($action) ?>" style="max-width:760px;">
  <?= csrf_field() ?>
  <div class="field"><label>Campaign name *</label><input class="input" name="campaign_name" required value="<?= $g('campaign_name') ?>"></div>
  <div class="field"><label>Description</label><textarea class="input" name="description"><?= $g('description') ?></textarea></div>
  <div class="grid grid-2">
    <div class="field">
      <label>Location</label>
      <select class="input" name="location_id">
        <option value="">—</option>
        <?php foreach ($locations as $loc): ?>
          <option value="<?= e($loc['id']) ?>" <?= (int) ($campaign['location_id'] ?? 0) === (int) $loc['id'] ? 'selected' : '' ?>>
            <?= e($loc['area_name']) ?> · <?= e($loc['state']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label>Voucher type</label>
      <select class="input" name="voucher_type">
        <?php foreach (['cash','discount','free_gift','b1f1','experience','tourism','festival'] as $vt): ?>
          <option value="<?= e($vt) ?>" <?= ($campaign['voucher_type'] ?? 'cash') === $vt ? 'selected' : '' ?>>
            <?= e(__('campaign.types.' . $vt)) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="grid grid-3">
    <div class="field"><label>Voucher value (RM) *</label><input class="input" type="number" step="0.01" name="voucher_value" required value="<?= $g('voucher_value', '10') ?>"></div>
    <div class="field"><label>Claim limit (0 = unlimited)</label><input class="input" type="number" name="claim_limit" value="<?= $g('claim_limit', '0') ?>"></div>
    <div class="field">
      <label>Status</label>
      <select class="input" name="status">
        <?php foreach (['draft','active','paused','ended'] as $s): ?>
          <option value="<?= e($s) ?>" <?= ($campaign['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div class="grid grid-2">
    <div class="field"><label>Start date</label><input class="input" type="date" name="start_date" value="<?= $g('start_date') ?>"></div>
    <div class="field"><label>End date</label><input class="input" type="date" name="end_date" value="<?= $g('end_date') ?>"></div>
  </div>
  <div class="field"><label>Banner image URL</label><input class="input" name="banner_image" value="<?= $g('banner_image') ?>"></div>
  <div class="field"><label>Terms &amp; conditions</label><textarea class="input" name="terms"><?= $g('terms') ?></textarea></div>
  <button class="btn btn-primary btn-lg"><?= e(__('admin.save')) ?></button>
</form>
