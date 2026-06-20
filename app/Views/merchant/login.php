<style>
  .login-shell { max-width: 420px; margin: 24px auto; }
  .login-card {
    background: #fff; border: 1px solid var(--c-border);
    border-radius: var(--r-lg); box-shadow: var(--shadow-md);
    padding: 32px 28px;
  }
  .login-eyebrow {
    display: inline-block; padding: 4px 12px; border-radius: 999px;
    background: var(--c-primary-lt); color: var(--c-primary-dk);
    font-size: .72rem; font-weight: 700; letter-spacing: .04em;
    text-transform: uppercase; margin-bottom: 12px;
  }
  .login-title { font-size: 1.6rem; margin: 0 0 6px; }
  .login-subtitle { color: var(--c-muted); margin: 0 0 20px; font-size: .92rem; }
  .login-divider {
    display: flex; align-items: center; gap: 10px;
    margin: 18px 0; color: var(--c-muted); font-size: .8rem;
  }
  .login-divider::before, .login-divider::after {
    content: ''; flex: 1; height: 1px; background: var(--c-border);
  }
  .login-cta {
    text-align: center; margin-top: 20px; padding-top: 18px;
    border-top: 1px solid var(--c-border);
    font-size: .9rem; color: var(--c-muted);
  }
  .login-cta strong { display: block; color: var(--c-text); margin-bottom: 4px; }
  .pwd-wrap { position: relative; }
  .pwd-toggle {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    background: transparent; border: 0; cursor: pointer;
    color: var(--c-muted); padding: 6px 8px; font-size: .85rem;
  }
</style>

<div class="login-shell">
  <div class="login-card">
    <span class="login-eyebrow"><?= e(__('nav.merchant_portal')) ?></span>
    <h1 class="login-title"><?= e(__('merchant.login_title')) ?></h1>
    <p class="login-subtitle"><?= e(__('merchant.register_subtitle', ['fee' => number_format((float) config('config.pricing.redemption_fee', 1.5), 2)])) ?></p>

    <form method="post" action="<?= e(url('/merchant/login')) ?>">
      <?= csrf_field() ?>
      <div class="field">
        <label for="email"><?= e(__('auth.email')) ?></label>
        <input class="input" id="email" type="email" name="email" required
               value="<?= e((string) old('email')) ?>" autocomplete="email"
               placeholder="you@business.com">
      </div>
      <div class="field">
        <label for="password"><?= e(__('auth.password')) ?></label>
        <div class="pwd-wrap">
          <input class="input" id="password" type="password" name="password" required
                 autocomplete="current-password">
          <button type="button" class="pwd-toggle" data-toggle="password" aria-label="Show password">👁</button>
        </div>
      </div>
      <button class="btn btn-primary btn-block btn-lg" type="submit"><?= e(__('auth.submit')) ?></button>
    </form>

    <div class="login-divider"><?= e(__('auth.no_account')) ?></div>

    <a href="<?= e(url('/merchant/register')) ?>" class="btn btn-outline btn-block">
      <?= e(__('auth.register')) ?> →
    </a>

    <div class="login-cta">
      <strong>👤 <?= e(__('nav.admin')) ?>?</strong>
      <a href="<?= e(url('/admin/login')) ?>"><?= e(__('admin.login_title')) ?> →</a>
    </div>
  </div>
</div>

<?php
\App\Core\View::pushScript('<script>
document.querySelectorAll(".pwd-toggle").forEach(function(btn){
  btn.addEventListener("click", function(){
    var input = document.getElementById(btn.dataset.toggle);
    if (!input) return;
    input.type = input.type === "password" ? "text" : "password";
    btn.textContent = input.type === "password" ? "👁" : "🙈";
  });
});
</script>');
?>
