# Hostinger deployment

These are the two files that go at the **root of `public_html/`** so that
Hostinger's default docroot serves the SLV front controller.

```
public_html/
├── app/
├── config/
├── database/
├── public/
├── storage/
├── .env                ← update with your domain + DB creds
├── .htaccess           ← copy from deploy/hostinger/.htaccess
├── index.php           ← copy from deploy/hostinger/index.php
└── README.md
```

## Deploy steps

1. **Upload the project** under `public_html/` (you've done this).
2. **Copy** `deploy/hostinger/index.php` → `public_html/index.php`
   and `deploy/hostinger/.htaccess` → `public_html/.htaccess`
   via Hostinger File Manager (or SFTP).
3. **Update `public_html/.env`:**
   ```
   APP_NAME="SLV Voucher Goodie"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://lavenderblush-eagle-951876.hostingersite.com
   APP_TIMEZONE=Asia/Kuala_Lumpur

   # Generate via: php -r "echo 'base64:'.base64_encode(random_bytes(32));"
   APP_KEY=base64:CHANGE_ME

   # MySQL credentials from hPanel → Databases
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u123456_slv
   DB_USERNAME=u123456_slv
   DB_PASSWORD=...

   # Optional: Billplz + OpenAI keys
   BILLPLZ_BASE_URL=https://www.billplz-sandbox.com/api/v3
   BILLPLZ_API_KEY=
   BILLPLZ_COLLECTION_ID=
   BILLPLZ_X_SIGNATURE=
   OPENAI_API_KEY=
   OPENAI_MODEL=gpt-4o-mini
   ```
4. **Create the MySQL database** in hPanel → Databases → Create database,
   then import via phpMyAdmin:
   - `database/schema.sql` first
   - `database/seed.sql` second
5. **Smoke-check:**
   - Browse to `https://your-domain/` → home page should load
   - Browse to `https://your-domain/healthz` → `{"ok":true,"db":true,...}`
   - Browse to `https://your-domain/.env` → 403 Forbidden (security check)
   - Browse to `https://your-domain/app/` → 403 Forbidden

## Seeded demo logins

| Role     | Email                | Password    |
|----------|----------------------|-------------|
| Admin    | admin@slvgroup.my    | admin123    |
| Gov      | gov@selangor.gov.my  | gov123      |
| Merchant | merchant@demo.my     | merchant123 |
| Merchant | paddycafe@demo.my    | merchant123 |
| Merchant | sweetbakery@demo.my  | merchant123 |
| Merchant | heritagecafe@demo.my | merchant123 |

Pre-loaded: 8 merchants spanning all wallet status tiers (healthy / warning /
critical / disabled), 24 vouchers, 15 redemptions.

## Meta (Facebook) ad insights

The admin dashboard pulls ad performance — including **how many people
clicked the WhatsApp button** on your Click-to-WhatsApp ads — from the
Meta Marketing API. Live behind `/admin/meta`.

### One-time setup

1. **Run the migration** in phpMyAdmin (the `meta_ad_metrics` table):

   ```sql
   CREATE TABLE meta_ad_metrics (
     id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
     date            DATE NOT NULL,
     campaign_id     VARCHAR(64), campaign_name VARCHAR(255),
     adset_id        VARCHAR(64), adset_name    VARCHAR(255),
     ad_id           VARCHAR(64) NOT NULL, ad_name VARCHAR(255),
     objective       VARCHAR(64),
     impressions     INT UNSIGNED NOT NULL DEFAULT 0,
     reach           INT UNSIGNED NOT NULL DEFAULT 0,
     clicks          INT UNSIGNED NOT NULL DEFAULT 0,
     link_clicks     INT UNSIGNED NOT NULL DEFAULT 0,
     spend           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
     ctr             DECIMAL(8,4) NOT NULL DEFAULT 0.0000,
     whatsapp_clicks INT UNSIGNED NOT NULL DEFAULT 0,
     messenger_clicks INT UNSIGNED NOT NULL DEFAULT 0,
     conversations_started INT UNSIGNED NOT NULL DEFAULT 0,
     raw_actions     TEXT,
     synced_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (id),
     UNIQUE KEY uniq_meta_metric (date, ad_id),
     KEY idx_meta_date (date),
     KEY idx_meta_campaign (campaign_id)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```

2. **Get a Meta Marketing API access token** (one-time, doesn't expire):
   - Go to <https://business.facebook.com/settings/system-users>
   - Create a System User with **Admin** role
   - Click **Generate New Token**, select your app, tick `ads_read`
   - Copy the token (you only see it once)

3. **Get your Ad Account ID** from <https://adsmanager.facebook.com> — top
   left dropdown shows the numeric ID. Strip any `act_` prefix.

4. **Add to `public_html/.env`:**

   ```
   META_ACCESS_TOKEN=EAAB...your-token...
   META_AD_ACCOUNT_ID=1234567890
   META_API_VERSION=v21.0
   META_CRON_TOKEN=any-long-random-string
   ```

5. **Open** `https://your-domain/admin/meta` and click **Sync now**.

### Daily auto-sync (optional)

In hPanel → Advanced → Cron Jobs, add a daily job:

```
0 2 * * * curl -s "https://your-domain/admin/meta/sync?token=YOUR_META_CRON_TOKEN&days=7" >/dev/null
```

The endpoint returns plain text and is safe to hit from cron — no
session needed, just the secret token from `.env`.

### Demo mode

If you don't add `META_ACCESS_TOKEN`, the **Sync now** button still
works — it loads 30 days of plausible synthetic data so the dashboard
demo is populated. Useful for the sales pitch before the real ad
account is wired up.

## Image uploads

The admin can upload campaign banners, location banners, and platform
hero images via the dashboard. Files land in `public/uploads/<bucket>/`
and are served at `/uploads/<bucket>/<filename>` thanks to the
`/uploads/*` rewrite in `.htaccess`.

Make sure the directory is writable by PHP (`chmod 775 public/uploads`
or `chmod 755` if PHP runs as the file owner — usually fine on Hostinger
shared hosting). Each bucket subfolder is auto-created on first upload.

A defence-in-depth `public/uploads/.htaccess` denies execution of any
PHP/CGI scripts uploaded into that folder.

If uploads silently fail, check Hostinger hPanel → PHP Configuration:
`upload_max_filesize` and `post_max_size` should be ≥ 4 MB.

## Notes

- The inner `public/index.php` and `public/.htaccess` are unused on this
  layout but harmless to leave in place. The root `index.php` simply
  `require`s them, so editing/removing them will affect the live site.
- Hostinger uses Apache with mod_rewrite enabled by default — the
  `.htaccess` rules apply automatically.
- HTTPS is forced via the redirect block at the bottom of `.htaccess`.
- File permissions should be `755` for directories and `644` for files
  (the default upload state). `storage/` must be writable by PHP — if logs
  fail, `chmod -R 775 storage/` via SSH or File Manager.
