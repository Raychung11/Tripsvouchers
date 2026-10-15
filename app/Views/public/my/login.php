<style>
  .my-login-shell { max-width: 440px; margin: 24px auto; }
  .my-login-card {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-lg); box-shadow: var(--shadow-md);
    padding: 32px 28px;
  }
  .my-login-eyebrow {
    display: inline-block; padding: 4px 12px; border-radius: 999px;
    background: var(--c-primary-lt); color: var(--c-primary-dk);
    font-size: .72rem; font-weight: 700; letter-spacing: .04em;
    text-transform: uppercase; margin-bottom: 12px;
  }
  .my-login-card h1 { font-size: 1.6rem; margin: 0 0 6px; }
  .my-login-card .help { font-size: .82rem; color: var(--c-muted); margin: 4px 0 0; }
  .my-login-cta {
    text-align: center; margin-top: 18px; padding-top: 16px;
    border-top: 1px solid var(--c-border);
    font-size: .9rem; color: var(--c-muted);
  }
  .my-login-cta strong { display: block; color: var(--c-text); margin-bottom: 4px; }
</style>

<div class="my-login-shell">
  <div class="my-login-card">
    <span class="my-login-eyebrow">🎟 <?= e(__('visitor.eyebrow')) ?></span>
    <h1><?= e(__('visitor.login_title')) ?></h1>
    <p class="text-muted"><?= e(__('visitor.login_subtitle')) ?></p>

    <form method="post" action="<?= e(url('/my')) ?>" novalidate>
      <?= csrf_field() ?>
      <div class="field">
        <label for="phone"><?= e(__('claim.phone')) ?></label>
        <input class="input" id="phone" type="tel" name="phone" required
               value="<?= e((string) old('phone')) ?>"
               autocomplete="tel" inputmode="tel"
               placeholder="+60123456789"
               pattern="^[+0-9 \-]{7,20}$">
        <p class="help"><?= e(__('visitor.phone_hint')) ?></p>
      </div>
      <button class="btn btn-primary btn-block btn-lg" type="submit">
        <?= e(__('visitor.find_button')) ?> →
      </button>
    </form>

    <div class="my-login-cta">
      <strong><?= e(__('visitor.no_voucher_yet')) ?></strong>
      <a href="<?= e(url('/campaigns')) ?>"><?= e(__('home.cta_browse')) ?> →</a>
    </div>
  </div>
</div>
