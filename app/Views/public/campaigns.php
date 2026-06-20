<?php /** @var array $campaigns */ ?>
<style>
  /* Campaigns grid — 2-up on mobile, 2-up on tablet, 3-up on large desktop */
  .camps-grid {
    display: grid; gap: 12px;
    grid-template-columns: 1fr 1fr;
  }
  @media (min-width: 720px) {
    .camps-grid { gap: 18px; }
  }
  @media (min-width: 1100px) {
    .camps-grid { grid-template-columns: repeat(3, 1fr); gap: 20px; }
  }

  .camp-card {
    display: flex; flex-direction: column;
    background: #fff; border: 1px solid var(--c-border, #E2E8F0);
    border-radius: 16px; overflow: hidden;
    text-decoration: none; color: inherit;
    box-shadow: 0 2px 8px rgba(15,23,42,.04);
    transition: transform .15s ease, box-shadow .25s ease, border-color .15s ease;
  }
  .camp-card:hover {
    text-decoration: none; transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(15,23,42,.10);
    border-color: var(--c-primary, #0D47A1);
  }

  .camp-thumb {
    width: 100%; aspect-ratio: 16/9;
    background-size: cover; background-position: center;
    background-color: #e2e8f0; position: relative;
  }
  .camp-thumb.fallback {
    background-image:
      radial-gradient(circle at 30% 30%, rgba(102,187,106,.7), transparent 60%),
      linear-gradient(135deg, var(--c-primary, #0D47A1), var(--c-primary-dk, #093479));
  }

  .camp-body { padding: 12px 14px; display: flex; flex-direction: column; flex: 1; }
  .camp-row1 { display: flex; justify-content: space-between; align-items: center; gap: 6px; margin-bottom: 6px; }
  .camp-row1 .pill { padding: 2px 8px; font-size: .65rem; }
  .camp-value {
    color: var(--c-primary-dk, #093479); font-weight: 800; font-size: .95rem;
    white-space: nowrap;
  }
  .camp-card h3 {
    margin: 0 0 6px; font-size: .92rem; line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .camp-meta {
    color: var(--c-muted, #64748B); font-size: .76rem;
    margin: 0 0 8px; line-height: 1.3;
  }
  .camp-desc {
    font-size: .82rem; color: var(--c-text, #0F172A);
    line-height: 1.4; margin: 0 0 10px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .camp-dates {
    color: var(--c-muted, #64748B); font-size: .72rem;
    margin: 0 0 10px;
  }
  .camp-cta {
    margin-top: auto;
    background: var(--c-primary, #0D47A1); color: #fff;
    padding: 9px 12px; border-radius: 10px; text-align: center;
    font-size: .82rem; font-weight: 600;
    box-shadow: 0 3px 8px rgba(13,71,161,.20);
  }

  @media (max-width: 480px) {
    .camp-body { padding: 10px 11px; }
    .camp-card h3 { font-size: .85rem; }
    .camp-desc { display: none; }  /* hide description on very narrow screens */
    .camp-dates { display: none; }
    .camp-cta { padding: 8px 10px; font-size: .78rem; }
  }
  @media (min-width: 720px) {
    .camp-body { padding: 16px 18px; }
    .camp-card h3 { font-size: 1.05rem; }
    .camp-desc { font-size: .9rem; }
  }
</style>

<h1 class="mt-0 mb-2"><?= e(__('campaign.list_title')) ?></h1>

<?php if (empty($campaigns)): ?>
  <div class="card text-center text-muted"><?= e(__('campaign.no_campaigns')) ?></div>
<?php else: ?>
  <div class="camps-grid">
    <?php foreach ($campaigns as $c): ?>
      <a class="camp-card" href="<?= e(url('/campaigns/' . $c['id'])) ?>">
        <?php if (!empty($c['banner_image'])): ?>
          <div class="camp-thumb" style="background-image: url('<?= e(asset_or_upload($c['banner_image'])) ?>');"></div>
        <?php else: ?>
          <div class="camp-thumb fallback"></div>
        <?php endif; ?>
        <div class="camp-body">
          <div class="camp-row1">
            <span class="pill pill-success"><?= e(__('campaign.types.' . $c['voucher_type'])) ?></span>
            <span class="camp-value"><?= e(rm($c['voucher_value'])) ?></span>
          </div>
          <h3><?= e($c['campaign_name']) ?></h3>
          <p class="camp-meta">
            📍 <?= e($c['location_name'] ?? '') ?><?php if (!empty($c['location_state'])): ?> · <?= e($c['location_state']) ?><?php endif; ?>
          </p>
          <p class="camp-desc"><?= e($c['description'] ?? '') ?></p>
          <p class="camp-dates">
            <?= e(__('campaign.ends')) ?>: <?= e($c['end_date'] ?: '—') ?>
          </p>
          <span class="camp-cta"><?= e(__('campaign.claim_now')) ?> →</span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
