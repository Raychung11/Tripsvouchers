<?php
/** @var array $merchant */ /** @var array|null $sub */ /** @var float $fee */
$active = $merchant['subscription_status'] === 'active' && $sub;
?>
<h1 class="mt-0"><?= e(__('merchant.subscription')) ?></h1>

<div class="card">
  <div class="flex-between">
    <div>
      <div class="text-muted" style="font-size:.85rem;"><?= e(__('merchant.subscription_status')) ?></div>
      <div style="font-size:1.2rem; font-weight:700;">
        <?= $active ? '✅ Active' : '⚠️ ' . e(__('merchant.sub_expired_msg')) ?>
      </div>
      <?php if ($active): ?>
        <p class="text-muted" style="margin:8px 0 0;">
          <?= e(__('merchant.sub_active_until', ['date' => $sub['expiry_date']])) ?>
        </p>
      <?php endif; ?>
    </div>
    <div>
      <span class="font-bold" style="font-size:1.4rem; color:var(--c-primary-dk);"><?= e(rm($fee)) ?></span>
      <span class="text-muted">/ year</span>
    </div>
  </div>

  <form method="post" action="<?= e(url('/merchant/subscription/pay')) ?>" class="mt-2">
    <?= csrf_field() ?>
    <button class="btn btn-primary btn-lg btn-block">
      <?= e($active ? __('merchant.sub_renew') : __('merchant.sub_pay')) ?> · Billplz
    </button>
  </form>
</div>

<div class="card">
  <h3 class="mt-0">Reminders</h3>
  <p class="text-muted">We will send reminders 30, 14, 7 days before expiry, and on expiry day via email and dashboard notification.</p>
</div>
