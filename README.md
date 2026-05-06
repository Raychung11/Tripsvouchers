# SLV Voucher Goodie Platform

AI Tourism Engagement & Merchant Voucher Ecosystem.

> "SLV transforms tourism campaigns into measurable AI-powered visitor
> engagement ecosystems using smart voucher activation and merchant
> redemption technology."

## Stack

- **Backend:** Native PHP 8+ (custom MVC, no framework)
- **Database:** MySQL 5.7+ / MariaDB 10+
- **Frontend:** Mobile-first responsive HTML + vanilla JS (PWA-ready)
- **QR Scanner:** HTML5 camera (`html5-qrcode`)
- **QR Generator:** `qrcode.js` (client-side rendering of encrypted token URL)
- **Payment:** Billplz (sandbox + production)
- **AI:** OpenAI API (Tour Guide chatbot)
- **Languages:** English / 中文 / Bahasa Melayu

## Quick start

```bash
# 1. Copy env
cp .env.example .env

# 2. Generate APP_KEY (32 random bytes, base64)
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
# paste into .env APP_KEY=

# 3. Create database
mysql -u root -p -e "CREATE DATABASE slv_voucher CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p slv_voucher < database/schema.sql
mysql -u root -p slv_voucher < database/seed.sql

# 4. Serve
php -S localhost:8000 -t public
```

Default seeded accounts:

| Role     | Email                    | Password  |
|----------|--------------------------|-----------|
| Admin    | admin@slvgroup.my        | admin123  |
| Merchant | merchant@demo.my         | merchant123 |

## Project structure

```
.
├── app/
│   ├── Controllers/        # HTTP request handlers
│   ├── Models/             # PDO-backed entities
│   ├── Core/               # Router, DB, Auth, View, Lang, Token, Billplz
│   ├── Lang/               # en.php / zh.php / ms.php
│   └── Views/              # PHP templates (layouts + pages)
├── config/                 # config.php, database.php
├── database/               # schema.sql, seed.sql
├── public/                 # Web root (index.php, assets/)
└── storage/                # logs, uploads
```

## Phase 1 MVP scope

- Admin: dashboard · merchants · campaigns · locations · vouchers · analytics
- Merchant: subscription · wallet topup · scanner · redemption history · marketing assets
- Visitor: campaign page · AI Tour Guide chat · voucher claim · QR voucher
- Billplz topup + subscription with callback verification
- Encrypted voucher tokens (AES-256-CBC) + signed redemption URLs
- Multilingual UI (EN / ZH / MS)
- Mobile-first responsive

## Hosting

Designed to run on Hostinger VPS (or any LAMP host) with Apache `mod_rewrite`
or Nginx with the equivalent rewrite. See `public/.htaccess` for the front
controller rewrite rules.
