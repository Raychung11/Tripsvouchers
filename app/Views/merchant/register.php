<?php
$pricing = config('config.pricing');
$sub    = number_format((float) $pricing['subscription_fee'], 0);
$fee    = number_format((float) $pricing['redemption_fee'], 2);
$wallet = number_format((float) $pricing['wallet_min'], 0);
?>
<style>
  .register-shell { max-width: 1080px; margin: 0 auto; }
  .register-grid {
    display: grid; gap: 24px;
    grid-template-columns: 1fr;
  }
  @media (min-width: 880px) {
    .register-grid { grid-template-columns: 1.4fr 1fr; gap: 32px; align-items: start; }
  }
  .register-eyebrow {
    display: inline-block; padding: 4px 12px; border-radius: 999px;
    background: var(--c-primary-lt); color: var(--c-primary-dk);
    font-size: .75rem; font-weight: 700; letter-spacing: .04em;
    text-transform: uppercase; margin-bottom: 12px;
  }
  .register-title { font-size: 1.8rem; margin: 0 0 8px; line-height: 1.15; }
  .register-subtitle { color: var(--c-muted); margin: 0 0 24px; line-height: 1.55; }

  .form-section { padding: 18px 0; border-top: 1px solid var(--c-border); }
  .form-section:first-of-type { border-top: 0; padding-top: 0; }
  .form-section h3 {
    display: flex; align-items: center; gap: 10px;
    margin: 0 0 16px; font-size: .85rem;
    text-transform: uppercase; letter-spacing: .08em; color: var(--c-muted);
    font-weight: 700;
  }
  .form-section h3 .step-num {
    width: 24px; height: 24px; border-radius: 50%;
    background: var(--c-primary); color: #fff;
    display: inline-grid; place-items: center; font-size: .75rem;
    font-weight: 800; letter-spacing: 0;
  }
  .field .req { color: var(--c-danger); margin-left: 2px; }
  .field .help { font-size: .82rem; color: var(--c-muted); margin: 4px 0 0; line-height: 1.4; }

  .pwd-wrap { position: relative; }
  .pwd-toggle {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: transparent; border: 0; cursor: pointer;
    color: var(--c-muted); padding: 6px 8px; font-size: .85rem;
  }
  .pwd-toggle:hover { color: var(--c-text); }

  .terms-row {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px; background: #f8fafc; border-radius: var(--r-md);
    border: 1px solid var(--c-border); font-size: .9rem; line-height: 1.5;
  }
  .terms-row input { margin-top: 4px; transform: scale(1.15); }

  .register-aside {
    display: flex; flex-direction: column; gap: 16px;
  }
  .aside-card {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-lg); padding: 20px;
  }
  .aside-card h4 {
    margin: 0 0 12px; font-size: .82rem;
    text-transform: uppercase; letter-spacing: .08em; color: var(--c-muted);
    font-weight: 700;
  }
  .perks { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
  .perks li { display: flex; align-items: flex-start; gap: 10px; line-height: 1.4; font-size: .95rem; }
  .perks .check {
    flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%;
    background: var(--c-primary-lt); color: var(--c-primary-dk);
    display: inline-grid; place-items: center; font-weight: 800; font-size: .8rem;
  }
  .next-steps { padding-left: 0; counter-reset: step; list-style: none; margin: 0; }
  .next-steps li {
    counter-increment: step; padding: 8px 0 8px 32px;
    position: relative; line-height: 1.4; font-size: .95rem;
  }
  .next-steps li::before {
    content: counter(step);
    position: absolute; left: 0; top: 8px;
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--c-primary); color: #fff;
    display: grid; place-items: center;
    font-weight: 800; font-size: .75rem;
  }
  .pricing-stub {
    background: linear-gradient(135deg, #ecfeff 0%, #fff 100%);
    border: 1px solid var(--c-primary-lt);
  }
  .pricing-stub strong { color: var(--c-primary-dk); font-size: 1rem; }

  @media (max-width: 879px) {
    .register-aside { order: -1; }
    .register-aside .aside-card.next-card { display: none; }
  }
</style>

<div class="register-shell">
  <div class="register-grid">

    <!-- ───── Form column ──────────────────────────────────────────── -->
    <div class="card" style="margin: 0;">
      <span class="register-eyebrow"><?= e(__('merchant.register_eyebrow')) ?></span>
      <h1 class="register-title"><?= e(__('merchant.register_title')) ?></h1>
      <p class="register-subtitle"><?= e(__('merchant.register_subtitle', ['fee' => $fee])) ?></p>

      <form method="post" action="<?= e(url('/merchant/register')) ?>" autocomplete="on" novalidate>
        <?= csrf_field() ?>

        <!-- Business details -->
        <div class="form-section">
          <h3><span class="step-num">1</span> <?= e(__('merchant.register_section_business')) ?></h3>

          <div class="field">
            <label for="biz_name"><?= e(__('merchant.business_name')) ?> <span class="req">*</span></label>
            <input class="input" id="biz_name" name="business_name" required
                   value="<?= e((string) old('business_name')) ?>"
                   autocomplete="organization">
            <p class="help"><?= e(__('merchant.register_business_hint')) ?></p>
          </div>

          <div class="grid grid-2">
            <div class="field">
              <label for="owner_name"><?= e(__('merchant.owner_name')) ?> <span class="req">*</span></label>
              <input class="input" id="owner_name" name="owner_name" required
                     value="<?= e((string) old('owner_name')) ?>"
                     autocomplete="name">
              <p class="help"><?= e(__('merchant.register_owner_hint')) ?></p>
            </div>
            <div class="field">
              <label for="category"><?= e(__('merchant.category')) ?> <span class="req">*</span></label>
              <select class="input" id="category" name="category">
                <?php foreach (['fnb','hotel','retail','souvenir','attraction','transport','experience','others'] as $c): ?>
                  <option value="<?= e($c) ?>" <?= old('category') === $c ? 'selected' : '' ?>>
                    <?= e(__('merchant.category_options.' . $c)) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="field">
            <label for="address"><?= e(__('merchant.address')) ?></label>
            <textarea class="input" id="address" name="address"
                      autocomplete="street-address"
                      placeholder="No. 88, Jalan Pantai, Sekinchan, 45400 Selangor"><?= e((string) old('address')) ?></textarea>
            <p class="help"><?= e(__('merchant.register_address_hint')) ?></p>
          </div>
        </div>

        <!-- Contact -->
        <div class="form-section">
          <h3><span class="step-num">2</span> <?= e(__('merchant.register_section_contact')) ?></h3>

          <div class="grid grid-2">
            <div class="field">
              <label for="email"><?= e(__('auth.email')) ?> <span class="req">*</span></label>
              <input class="input" id="email" type="email" name="email" required
                     value="<?= e((string) old('email')) ?>"
                     autocomplete="email"
                     placeholder="you@business.com">
              <p class="help"><?= e(__('merchant.register_email_hint')) ?></p>
            </div>
            <div class="field">
              <label for="phone"><?= e(__('common.phone')) ?> <span class="req">*</span></label>
              <input class="input" id="phone" type="tel" name="phone" required
                     value="<?= e((string) old('phone')) ?>"
                     autocomplete="tel" inputmode="tel"
                     placeholder="+60123456789"
                     pattern="^\+?[0-9 \-]{7,20}$">
              <p class="help"><?= e(__('merchant.register_phone_hint')) ?></p>
            </div>
          </div>

          <div class="field">
            <label for="whatsapp">WhatsApp</label>
            <input class="input" id="whatsapp" name="whatsapp"
                   value="<?= e((string) old('whatsapp')) ?>"
                   inputmode="tel"
                   placeholder="+60123456789">
            <p class="help"><?= e(__('merchant.register_whatsapp_hint')) ?></p>
          </div>
        </div>

        <!-- Account -->
        <div class="form-section">
          <h3><span class="step-num">3</span> <?= e(__('merchant.register_section_account')) ?></h3>

          <div class="grid grid-2">
            <div class="field">
              <label for="password"><?= e(__('auth.password')) ?> <span class="req">*</span></label>
              <div class="pwd-wrap">
                <input class="input" id="password" type="password" name="password" required
                       minlength="8" autocomplete="new-password">
                <button type="button" class="pwd-toggle" data-toggle="password" aria-label="Show password">👁</button>
              </div>
              <p class="help"><?= e(__('merchant.register_password_hint')) ?></p>
            </div>
            <div class="field">
              <label for="password_confirm"><?= e(__('merchant.register_password_confirm')) ?> <span class="req">*</span></label>
              <div class="pwd-wrap">
                <input class="input" id="password_confirm" type="password" name="password_confirmation" required
                       minlength="8" autocomplete="new-password">
                <button type="button" class="pwd-toggle" data-toggle="password_confirm" aria-label="Show password">👁</button>
              </div>
            </div>
          </div>

          <label class="terms-row">
            <input type="checkbox" name="terms" value="1" <?= old('terms') ? 'checked' : '' ?> required>
            <span>
              <?= e(__('merchant.register_terms_pre')) ?>
              <a href="<?= e(url('/terms')) ?>" target="_blank" rel="noopener"><?= e(__('merchant.register_terms_link')) ?></a>
              <?= e(__('merchant.register_terms_and')) ?>
              <a href="<?= e(url('/privacy')) ?>" target="_blank" rel="noopener"><?= e(__('merchant.register_privacy_link')) ?></a><?= e(__('merchant.register_terms_post')) ?>
            </span>
          </label>
        </div>

        <button class="btn btn-primary btn-lg btn-block mt-2" type="submit">
          <?= e(__('merchant.register_submit')) ?> →
        </button>

        <p class="text-center mt-2 text-muted" style="font-size:.9rem;">
          <?= e(__('auth.have_account')) ?>
          <a href="<?= e(url('/merchant/login')) ?>"><?= e(__('auth.submit')) ?></a>
        </p>
      </form>
    </div>

    <!-- ───── Aside (perks / next steps / pricing) ─────────────────── -->
    <aside class="register-aside">
      <div class="aside-card">
        <h4>✨ <?= e(__('merchant.register_perks_title')) ?></h4>
        <ul class="perks">
          <li><span class="check">✓</span><span><?= __('merchant.register_perks_1') ?></span></li>
          <li><span class="check">✓</span><span><?= __('merchant.register_perks_2') ?></span></li>
          <li><span class="check">✓</span><span><?= __('merchant.register_perks_3') ?></span></li>
          <li><span class="check">✓</span><span><?= __('merchant.register_perks_4') ?></span></li>
        </ul>
      </div>

      <div class="aside-card next-card">
        <h4>📋 <?= e(__('merchant.register_after_title')) ?></h4>
        <ol class="next-steps">
          <li><?= e(__('merchant.register_after_1')) ?></li>
          <li><?= e(__('merchant.register_after_2', ['sub' => $sub])) ?></li>
          <li><?= e(__('merchant.register_after_3', ['wallet' => $wallet])) ?></li>
          <li><?= __('merchant.register_after_4') ?></li>
        </ol>
      </div>

      <div class="aside-card pricing-stub">
        <h4>💰 <?= e(__('merchant.register_pricing_title')) ?></h4>
        <p style="margin:0;"><strong><?= e(__('merchant.register_pricing_sub', [
            'sub' => $sub, 'fee' => $fee, 'wallet' => $wallet,
        ])) ?></strong></p>
        <p style="margin:8px 0 0; color: var(--c-muted); font-size:.85rem;">
          <a href="<?= e(url('/for-merchants#pricing')) ?>"><?= e(__('footer.link_pricing')) ?> →</a>
        </p>
      </div>
    </aside>
  </div>
</div>

<?php
\App\Core\View::pushScript('<script>
(function(){
  // Show/hide password toggles
  document.querySelectorAll(".pwd-toggle").forEach(function(btn){
    btn.addEventListener("click", function(){
      var id = btn.dataset.toggle;
      var input = document.getElementById(id);
      if (!input) return;
      input.type = input.type === "password" ? "text" : "password";
      btn.textContent = input.type === "password" ? "👁" : "🙈";
    });
  });
  // Live password match feedback
  var pwd = document.getElementById("password");
  var conf = document.getElementById("password_confirm");
  function checkMatch(){
    if (!pwd.value || !conf.value) { conf.setCustomValidity(""); return; }
    if (pwd.value !== conf.value) {
      conf.setCustomValidity("' . addslashes(__('merchant.register_password_mismatch')) . '");
    } else {
      conf.setCustomValidity("");
    }
  }
  if (pwd && conf) {
    pwd.addEventListener("input", checkMatch);
    conf.addEventListener("input", checkMatch);
  }
})();
</script>');
?>
