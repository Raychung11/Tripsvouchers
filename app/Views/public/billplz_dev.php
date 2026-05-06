<?php /** @var string $bill */ /** @var string $ref */ ?>
<div class="container-sm" style="max-width:480px; margin:0 auto;">
  <div class="card">
    <h1 class="mt-0">💳 Billplz (sandbox dev)</h1>
    <p class="text-muted">No live Billplz credentials configured. Use this fake screen to test the payment flow locally.</p>
    <p>Bill ID: <code><?= e($bill) ?></code></p>
    <p>Reference: <code><?= e($ref) ?></code></p>
    <form method="post" action="<?= e(url('/billplz/dev-pay')) ?>" class="mt-2">
      <?= csrf_field() ?>
      <input type="hidden" name="bill" value="<?= e($bill) ?>">
      <div class="grid grid-2">
        <button class="btn btn-success btn-lg" name="action" value="pay">✅ Mark as paid</button>
        <button class="btn btn-outline" name="action" value="cancel">Cancel</button>
      </div>
    </form>
  </div>
</div>
