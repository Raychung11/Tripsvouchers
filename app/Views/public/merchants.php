<?php /** @var array $merchants */ ?>
<h1 class="mt-0 mb-2"><?= e(__('nav.merchants')) ?></h1>

<?php if (empty($merchants)): ?>
  <div class="card text-center text-muted">—</div>
<?php else: ?>
  <div class="grid grid-2">
    <?php foreach ($merchants as $m): ?>
      <article class="card">
        <h3 class="mt-0"><?= e($m['business_name']) ?></h3>
        <div class="pill pill-info"><?= e(__('merchant.category_options.' . $m['category'])) ?></div>
        <p class="text-muted mt-1" style="margin:8px 0 0;">
          📍 <?= e($m['location_name'] ?? '') ?>, <?= e($m['location_state'] ?? '') ?>
        </p>
        <?php if (!empty($m['address'])): ?>
          <p class="text-muted" style="margin:4px 0 0; font-size:.9rem;"><?= e($m['address']) ?></p>
        <?php endif; ?>
        <?php if (!empty($m['operating_hours'])): ?>
          <p class="text-muted" style="margin:4px 0 0; font-size:.9rem;">⏰ <?= e($m['operating_hours']) ?></p>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
