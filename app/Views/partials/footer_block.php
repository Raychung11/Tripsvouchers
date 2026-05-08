<?php
/**
 * Visible footer block — multi-column layout with brand, navigation,
 * contact, social and a bottom bar with copyright + legal links.
 *
 * Company info, social URLs and contact details come from
 * config('config.company') so they're editable in one place.
 */
$company = config('config.company', []);
$waNumber = preg_replace('/\D+/', '', (string) ($company['whatsapp'] ?? ''));
$waLink = $waNumber !== '' ? 'https://wa.me/' . $waNumber : '#';
$mailto = 'mailto:' . ($company['email'] ?? 'info@example.com');
$social = $company['social'] ?? [];
?>
<style>
/* Footer styles co-located with the markup so a single file upload
   keeps both in sync. Uses hardcoded colors (not CSS vars) so the
   footer still renders correctly even if app.css is stale. */
.footer { background: linear-gradient(180deg,#0f172a 0%,#020617 100%);
  color: #cbd5e1; padding: 56px 16px 0; margin-top: 48px; font-size: .92rem; }
.footer-inner { max-width: 1180px; margin: 0 auto; }
.footer a { color: #cbd5e1; text-decoration: none; transition: color .15s ease; }
.footer a:hover { color: #fff; text-decoration: none; }

.footer-grid { display: grid; gap: 36px; padding-bottom: 40px;
  grid-template-columns: 1fr; }
@media (min-width: 600px) {
  .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px 24px; }
}
@media (min-width: 960px) {
  .footer-grid { grid-template-columns: 1.6fr 1fr 1fr 1.2fr; }
}
.footer-grid > * { min-width: 0; }

.footer-brand { display: flex; flex-direction: column; gap: 14px; }
.footer-brand-row { display: inline-flex; align-items: center; gap: 10px;
  color: #fff; align-self: flex-start; text-decoration: none; }
.footer-brand-row .brand-logo { width: 36px; height: 36px; border-radius: 8px;
  background: linear-gradient(135deg, #0d9488, #f59e0b);
  color: #fff; display: grid; place-items: center; font-weight: 800; font-size: .85rem; }
.footer-brand-row strong { font-size: 1.05rem; color: #fff; }
.footer-tagline { color: #94a3b8; line-height: 1.55; margin: 0;
  max-width: 360px; font-size: .9rem; }

.footer-social { display: flex; align-items: center; gap: 10px;
  margin-top: 4px; flex-wrap: wrap; }
.footer-social-label { color: #64748b; font-size: .72rem;
  text-transform: uppercase; letter-spacing: .08em;
  margin-right: 4px; width: 100%; }
.footer-social a { width: 36px; height: 36px; border-radius: 50%;
  background: rgba(255,255,255,.06); display: inline-grid; place-items: center;
  color: #cbd5e1; transition: background .15s ease, color .15s ease, transform .15s ease; }
.footer-social a:hover { background: #0d9488; color: #fff; transform: translateY(-2px); }

.footer-col h4 { color: #fff; font-size: .78rem; margin: 0 0 14px;
  text-transform: uppercase; letter-spacing: .1em; font-weight: 700; }
.footer-col ul { list-style: none; padding: 0; margin: 0;
  display: flex; flex-direction: column; gap: 9px; }
.footer-col li { line-height: 1.4; }
.footer-col a { font-size: .9rem; }

.footer-contact ul { gap: 14px; }
.footer .contact-item { display: flex; align-items: flex-start; gap: 10px; }
.footer .contact-link { display: flex; align-items: flex-start; gap: 10px;
  flex: 1; min-width: 0; color: #cbd5e1; text-decoration: none; }
.footer .contact-icon { font-size: 1.05rem; line-height: 1.4;
  flex-shrink: 0; width: 20px; text-align: center; }
.footer .contact-text { display: flex; flex-direction: column;
  min-width: 0; flex: 1; }
.footer .contact-label { color: #64748b; font-size: .72rem;
  text-transform: uppercase; letter-spacing: .08em;
  line-height: 1.2; margin-bottom: 2px; }
.footer .contact-value { color: #fff; font-weight: 500; font-size: .92rem;
  text-transform: none; letter-spacing: normal;
  word-break: break-word; line-height: 1.35; }

.footer-bottom { border-top: 1px solid rgba(255,255,255,.08);
  padding: 18px 0 24px;
  display: flex; justify-content: space-between; align-items: center;
  gap: 12px 18px; flex-wrap: wrap;
  font-size: .8rem; color: #64748b; }
.footer-bottom-left { color: #94a3b8; }
.footer-bottom-right { display: flex; gap: 16px; flex-wrap: wrap; }
.footer-bottom-right a { color: #94a3b8; font-size: .8rem; }
.footer-bottom-right a:hover { color: #fff; }
</style>

<footer class="footer" role="contentinfo">
  <div class="footer-inner">

    <!-- ─── Top: brand + columns ───────────────────────────────────────── -->
    <div class="footer-grid">

      <!-- Brand column -->
      <div class="footer-brand">
        <a href="<?= e(url('/')) ?>" class="footer-brand-row">
          <span class="brand-logo">SLV</span>
          <strong><?= e(__('app.name')) ?></strong>
        </a>
        <p class="footer-tagline"><?= e(__('footer.tagline')) ?></p>

        <?php if (!empty($social)): ?>
          <div class="footer-social" aria-label="<?= e(__('footer.follow')) ?>">
            <span class="footer-social-label"><?= e(__('footer.follow')) ?>:</span>
            <?php if (!empty($social['facebook'])): ?>
              <a href="<?= e($social['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M13 22v-8h2.7l.4-3.1H13V8.9c0-.9.3-1.5 1.6-1.5h1.7V4.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.4H7v3.1h2.6V22z"/></svg>
              </a>
            <?php endif; ?>
            <?php if (!empty($social['instagram'])): ?>
              <a href="<?= e($social['instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.8.1 1.2.1 2 .2 2.4.4.6.2 1.1.5 1.6 1s.8 1 1 1.6c.2.5.3 1.2.4 2.4.1 1.2.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 1.2-.2 2-.4 2.4-.2.6-.5 1.1-1 1.6s-1 .8-1.6 1c-.5.2-1.2.3-2.4.4-1.2.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-1.2-.1-2-.2-2.4-.4-.6-.2-1.1-.5-1.6-1s-.8-1-1-1.6c-.2-.5-.3-1.2-.4-2.4C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.8c.1-1.2.2-2 .4-2.4.2-.6.5-1.1 1-1.6s1-.8 1.6-1c.5-.2 1.2-.3 2.4-.4C8.4 2.2 8.8 2.2 12 2.2zM12 0C8.7 0 8.3 0 7.1.1 5.8.1 5 .3 4.2.6c-.8.3-1.5.7-2.2 1.4S.9 3.4.6 4.2C.3 5 .1 5.8.1 7.1 0 8.3 0 8.7 0 12s0 3.7.1 4.9c.1 1.3.2 2.1.5 2.9.3.8.7 1.5 1.4 2.2.7.7 1.4 1.1 2.2 1.4.8.3 1.6.5 2.9.5 1.2.1 1.6.1 4.9.1s3.7 0 4.9-.1c1.3-.1 2.1-.2 2.9-.5.8-.3 1.5-.7 2.2-1.4.7-.7 1.1-1.4 1.4-2.2.3-.8.5-1.6.5-2.9.1-1.2.1-1.6.1-4.9s0-3.7-.1-4.9c-.1-1.3-.2-2.1-.5-2.9-.3-.8-.7-1.5-1.4-2.2C20.6 1.3 19.9.9 19.1.6 18.3.3 17.5.1 16.2.1 15 0 14.6 0 12 0zm0 5.8c-3.4 0-6.2 2.8-6.2 6.2s2.8 6.2 6.2 6.2 6.2-2.8 6.2-6.2S15.4 5.8 12 5.8zm0 10.2c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4zm6.4-11.8c-.8 0-1.4.6-1.4 1.4s.6 1.4 1.4 1.4 1.4-.6 1.4-1.4-.6-1.4-1.4-1.4z"/></svg>
              </a>
            <?php endif; ?>
            <?php if (!empty($social['tiktok'])): ?>
              <a href="<?= e($social['tiktok']) ?>" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M19.6 6.7a5.4 5.4 0 0 1-3.2-1V15a5.6 5.6 0 1 1-5.6-5.6c.3 0 .6 0 .9.1v2.7c-.3-.1-.6-.1-.9-.1a2.9 2.9 0 1 0 2.9 2.9V2h2.7a5.4 5.4 0 0 0 3.2 4.7z"/></svg>
              </a>
            <?php endif; ?>
            <a href="<?= e($waLink) ?>" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M20.5 3.5A11.9 11.9 0 0 0 12 0a12 12 0 0 0-10.4 18L0 24l6.2-1.6A12 12 0 1 0 20.5 3.5zm-8.5 18a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A10 10 0 1 1 12 21.5zm5.6-7.5c-.3-.2-1.8-.9-2-1s-.5-.2-.7.2-.8 1-1 1.2-.4.2-.7 0a8.2 8.2 0 0 1-2.4-1.5 9 9 0 0 1-1.6-2c-.2-.3 0-.5.1-.6l.5-.6.3-.4c.1-.2 0-.4 0-.5l-.7-1.7c-.2-.4-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2c0 1.3.9 2.5 1 2.7s1.9 2.9 4.5 4a15.5 15.5 0 0 0 1.5.5 3.6 3.6 0 0 0 1.7.1c.5-.1 1.6-.7 1.8-1.3s.2-1.2.2-1.3-.3-.2-.6-.3z"/></svg>
            </a>
          </div>
        <?php endif; ?>
      </div>

      <!-- Platform column -->
      <div class="footer-col">
        <h4><?= e(__('footer.col_platform')) ?></h4>
        <ul>
          <li><a href="<?= e(url('/campaigns')) ?>"><?= e(__('footer.link_campaigns')) ?></a></li>
          <li><a href="<?= e(url('/merchants')) ?>"><?= e(__('footer.link_listing')) ?></a></li>
          <li><a href="<?= e(url('/chat')) ?>"><?= e(__('footer.link_chat')) ?></a></li>
          <li><a href="<?= e(url('/about')) ?>"><?= e(__('footer.link_about')) ?></a></li>
        </ul>
      </div>

      <!-- For Merchants + Support column -->
      <div class="footer-col">
        <h4><?= e(__('footer.col_merchants')) ?></h4>
        <ul>
          <li><a href="<?= e(url('/for-merchants')) ?>"><?= e(__('footer.link_join')) ?></a></li>
          <li><a href="<?= e(url('/for-merchants#pricing')) ?>"><?= e(__('footer.link_pricing')) ?></a></li>
          <li><a href="<?= e(url('/merchant/register')) ?>"><?= e(__('footer.link_register')) ?></a></li>
          <li><a href="<?= e(url('/merchant/login')) ?>"><?= e(__('footer.link_login')) ?></a></li>
          <li><a href="<?= e(url('/for-merchants#faq')) ?>"><?= e(__('footer.link_faq')) ?></a></li>
          <li><a href="<?= e(url('/contact')) ?>"><?= e(__('footer.link_help')) ?></a></li>
        </ul>
      </div>

      <!-- Contact column -->
      <div class="footer-col footer-contact">
        <h4><?= e(__('footer.col_contact')) ?></h4>
        <ul>
          <?php if (!empty($company['whatsapp'])): ?>
            <li class="contact-item">
              <a href="<?= e($waLink) ?>" target="_blank" rel="noopener" class="contact-link">
                <span class="contact-icon">📱</span>
                <span class="contact-text">
                  <span class="contact-label"><?= e(__('footer.whatsapp_label')) ?></span>
                  <span class="contact-value"><?= e($company['phone'] ?? '+60 ' . $waNumber) ?></span>
                </span>
              </a>
            </li>
          <?php endif; ?>
          <?php if (!empty($company['email'])): ?>
            <li class="contact-item">
              <a href="<?= e($mailto) ?>" class="contact-link">
                <span class="contact-icon">✉️</span>
                <span class="contact-text">
                  <span class="contact-label"><?= e(__('footer.email_label')) ?></span>
                  <span class="contact-value"><?= e($company['email']) ?></span>
                </span>
              </a>
            </li>
          <?php endif; ?>
          <?php if (!empty($company['address'])): ?>
            <li class="contact-item">
              <span class="contact-icon">📍</span>
              <span class="contact-text">
                <span class="contact-label"><?= e(__('footer.address_label')) ?></span>
                <span class="contact-value"><?= e($company['address']) ?></span>
              </span>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <!-- ─── Bottom bar ─────────────────────────────────────────────────── -->
    <div class="footer-bottom">
      <div class="footer-bottom-left">
        &copy; <?= date('Y') ?>
        <?= e(__('footer.company', [
            'name' => $company['name'] ?? 'SLV',
            'reg'  => $company['reg_no'] ?? '—',
        ])) ?>
        · <?= e(__('footer.rights')) ?>
      </div>
      <div class="footer-bottom-right">
        <a href="<?= e(url('/privacy')) ?>"><?= e(__('footer.link_privacy')) ?></a>
        <a href="<?= e(url('/terms')) ?>"><?= e(__('footer.link_terms')) ?></a>
        <a href="<?= e(url('/admin/login')) ?>"><?= e(__('footer.link_admin')) ?></a>
      </div>
    </div>
  </div>
</footer>
