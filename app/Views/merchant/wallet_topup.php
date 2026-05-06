<?php /** @var array $merchant */ ?>
<h1 class="mt-0"><?= e(__('merchant.topup')) ?></h1>
<div class="card" style="max-width:520px;">
  <p class="text-muted">
    <?= e(__('merchant.topup_min', ['amount' => rm(config('config.pricing.wallet_min_reload', 100))])) ?>
  </p>
  <form method="post" action="<?= e(url('/merchant/wallet/topup')) ?>">
    <?= csrf_field() ?>
    <div class="field">
      <label><?= e(__('merchant.amount')) ?> (RM)</label>
      <input class="input" type="number" name="amount" min="100" step="10" value="<?= e((string) old('amount', '100')) ?>" required>
    </div>
    <div class="grid grid-4 mb-2">
      <?php foreach ([100, 200, 500, 1000] as $q): ?>
        <button type="button" class="btn btn-outline" onclick="document.querySelector('[name=amount]').value=<?= $q ?>"><?= e(rm($q)) ?></button>
      <?php endforeach; ?>
    </div>
    <button class="btn btn-primary btn-lg btn-block">
      <?= e(__('merchant.pay_now')) ?> · Billplz
    </button>
  </form>
</div>
