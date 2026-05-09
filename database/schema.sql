-- SLV Voucher Goodie Platform — schema
-- MySQL 5.7+ / MariaDB 10+ (utf8mb4)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS meta_ad_metrics;
DROP TABLE IF EXISTS site_settings;
DROP TABLE IF EXISTS redemptions;
DROP TABLE IF EXISTS vouchers;
DROP TABLE IF EXISTS campaign_merchants;
DROP TABLE IF EXISTS campaigns;
DROP TABLE IF EXISTS merchant_wallet_transactions;
DROP TABLE IF EXISTS wallet_topups;
DROP TABLE IF EXISTS merchant_subscriptions;
DROP TABLE IF EXISTS merchants;
DROP TABLE IF EXISTS locations;
DROP TABLE IF EXISTS users;

-- ─── users ────────────────────────────────────────────────────────────────
CREATE TABLE users (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(120) NOT NULL,
  email       VARCHAR(180) NOT NULL,
  phone       VARCHAR(40)  DEFAULT NULL,
  password    VARCHAR(255) NOT NULL,
  role        ENUM('admin','gov','merchant') NOT NULL DEFAULT 'merchant',
  status      ENUM('active','suspended','pending') NOT NULL DEFAULT 'active',
  lang        VARCHAR(5)   NOT NULL DEFAULT 'en',
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── locations ────────────────────────────────────────────────────────────
CREATE TABLE locations (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  state        VARCHAR(80)  NOT NULL,
  city         VARCHAR(80)  DEFAULT NULL,
  area_name    VARCHAR(120) NOT NULL,
  slug         VARCHAR(80)  NOT NULL,
  description  TEXT,
  banner_image VARCHAR(255) DEFAULT NULL,
  map_link     VARCHAR(500) DEFAULT NULL,
  status       ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_locations_slug (slug),
  KEY idx_locations_state (state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── merchants ────────────────────────────────────────────────────────────
CREATE TABLE merchants (
  id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id             BIGINT UNSIGNED NOT NULL,
  business_name       VARCHAR(180) NOT NULL,
  owner_name          VARCHAR(120) NOT NULL,
  phone               VARCHAR(40)  NOT NULL,
  whatsapp            VARCHAR(40)  DEFAULT NULL,
  email               VARCHAR(180) NOT NULL,
  category            ENUM('fnb','hotel','retail','souvenir','attraction','transport','experience','others') NOT NULL DEFAULT 'fnb',
  location_id         BIGINT UNSIGNED DEFAULT NULL,
  address             VARCHAR(500) DEFAULT NULL,
  map_link            VARCHAR(500) DEFAULT NULL,
  registration_no     VARCHAR(80)  DEFAULT NULL,
  logo                VARCHAR(255) DEFAULT NULL,
  shop_photos         TEXT DEFAULT NULL,
  operating_hours     VARCHAR(255) DEFAULT NULL,
  wallet_balance      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  subscription_status ENUM('pending','active','expired','suspended') NOT NULL DEFAULT 'pending',
  status              ENUM('pending','active','suspended') NOT NULL DEFAULT 'pending',
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_merchants_user (user_id),
  KEY idx_merchants_location (location_id),
  KEY idx_merchants_status (status),
  CONSTRAINT fk_merchants_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_merchants_location FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── merchant_subscriptions ───────────────────────────────────────────────
CREATE TABLE merchant_subscriptions (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  merchant_id     BIGINT UNSIGNED NOT NULL,
  annual_fee      DECIMAL(10,2) NOT NULL DEFAULT 150.00,
  start_date      DATE DEFAULT NULL,
  expiry_date     DATE DEFAULT NULL,
  payment_status  ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
  billplz_bill_id VARCHAR(80) DEFAULT NULL,
  status          ENUM('pending','active','expired','suspended') NOT NULL DEFAULT 'pending',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_subs_merchant (merchant_id),
  KEY idx_subs_bill (billplz_bill_id),
  CONSTRAINT fk_subs_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── wallet_topups ────────────────────────────────────────────────────────
CREATE TABLE wallet_topups (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  merchant_id     BIGINT UNSIGNED NOT NULL,
  amount          DECIMAL(10,2) NOT NULL,
  billplz_bill_id VARCHAR(80) DEFAULT NULL,
  payment_status  ENUM('pending','paid','failed','expired') NOT NULL DEFAULT 'pending',
  paid_at         DATETIME DEFAULT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_topups_bill (billplz_bill_id),
  KEY idx_topups_merchant (merchant_id),
  CONSTRAINT fk_topups_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── merchant_wallet_transactions ─────────────────────────────────────────
CREATE TABLE merchant_wallet_transactions (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  merchant_id     BIGINT UNSIGNED NOT NULL,
  type            ENUM('topup','redemption','adjustment','refund') NOT NULL,
  amount          DECIMAL(10,2) NOT NULL, -- positive for credits, negative for debits
  balance_before  DECIMAL(10,2) NOT NULL,
  balance_after   DECIMAL(10,2) NOT NULL,
  description     VARCHAR(255) DEFAULT NULL,
  reference_type  VARCHAR(40)  DEFAULT NULL,
  reference_id    BIGINT UNSIGNED DEFAULT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_walletx_merchant (merchant_id),
  KEY idx_walletx_ref (reference_type, reference_id),
  CONSTRAINT fk_walletx_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── campaigns ────────────────────────────────────────────────────────────
CREATE TABLE campaigns (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  location_id    BIGINT UNSIGNED DEFAULT NULL,
  campaign_name  VARCHAR(180) NOT NULL,
  slug           VARCHAR(180) NOT NULL,
  description    TEXT,
  voucher_type   ENUM('cash','discount','free_gift','b1f1','experience','tourism','festival') NOT NULL DEFAULT 'cash',
  voucher_value  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  start_date     DATE DEFAULT NULL,
  end_date       DATE DEFAULT NULL,
  banner_image   VARCHAR(255) DEFAULT NULL,
  claim_limit    INT UNSIGNED NOT NULL DEFAULT 0, -- 0 = unlimited
  terms          TEXT,
  status         ENUM('draft','active','ended','paused') NOT NULL DEFAULT 'draft',
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_campaigns_slug (slug),
  KEY idx_campaigns_location (location_id),
  CONSTRAINT fk_campaigns_location FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── campaign_merchants ───────────────────────────────────────────────────
CREATE TABLE campaign_merchants (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  campaign_id  BIGINT UNSIGNED NOT NULL,
  merchant_id  BIGINT UNSIGNED NOT NULL,
  status       ENUM('pending','active','removed') NOT NULL DEFAULT 'active',
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cm_pair (campaign_id, merchant_id),
  KEY idx_cm_campaign (campaign_id),
  KEY idx_cm_merchant (merchant_id),
  CONSTRAINT fk_cm_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
  CONSTRAINT fk_cm_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── vouchers ─────────────────────────────────────────────────────────────
CREATE TABLE vouchers (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  campaign_id     BIGINT UNSIGNED NOT NULL,
  merchant_id     BIGINT UNSIGNED DEFAULT NULL, -- nullable until redeemed at a specific merchant
  voucher_code    VARCHAR(60) NOT NULL,
  qr_token        TEXT NOT NULL, -- encrypted token for QR URL
  customer_name   VARCHAR(120) NOT NULL,
  customer_phone  VARCHAR(40) NOT NULL,
  status          ENUM('claimed','redeemed','expired','void') NOT NULL DEFAULT 'claimed',
  claimed_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  redeemed_at     DATETIME DEFAULT NULL,
  expired_at      DATETIME DEFAULT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_vouchers_code (voucher_code),
  KEY idx_vouchers_campaign (campaign_id),
  KEY idx_vouchers_phone (customer_phone),
  KEY idx_vouchers_status (status),
  CONSTRAINT fk_vouchers_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
  CONSTRAINT fk_vouchers_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── redemptions ──────────────────────────────────────────────────────────
CREATE TABLE redemptions (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  voucher_id            BIGINT UNSIGNED NOT NULL,
  campaign_id           BIGINT UNSIGNED NOT NULL,
  merchant_id           BIGINT UNSIGNED NOT NULL,
  redemption_fee        DECIMAL(10,2) NOT NULL DEFAULT 1.50,
  wallet_balance_before DECIMAL(10,2) NOT NULL,
  wallet_balance_after  DECIMAL(10,2) NOT NULL,
  redeemed_by           BIGINT UNSIGNED DEFAULT NULL, -- user_id of merchant operator
  redeemed_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_redemptions_voucher (voucher_id),
  KEY idx_red_campaign (campaign_id),
  KEY idx_red_merchant (merchant_id),
  CONSTRAINT fk_red_voucher FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE CASCADE,
  CONSTRAINT fk_red_campaign FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
  CONSTRAINT fk_red_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON DELETE CASCADE,
  CONSTRAINT fk_red_user FOREIGN KEY (redeemed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── site_settings ────────────────────────────────────────────────────────
-- Simple key/value store for admin-managed marketing assets:
-- hero images, intro paragraphs, etc. Edited via /admin/site.
CREATE TABLE site_settings (
  `key`        VARCHAR(80)  NOT NULL,
  `value`      TEXT,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── meta_ad_metrics ──────────────────────────────────────────────────────
-- Daily Meta (Facebook) ad insights cached locally so the admin dashboard
-- doesn't hit the Marketing API on every page load. One row per (date, ad_id).
-- The raw_actions JSON keeps the full Meta `actions` array so we can extract
-- additional metrics later without re-syncing.
CREATE TABLE meta_ad_metrics (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  date            DATE NOT NULL,
  campaign_id     VARCHAR(64)  DEFAULT NULL,
  campaign_name   VARCHAR(255) DEFAULT NULL,
  adset_id        VARCHAR(64)  DEFAULT NULL,
  adset_name      VARCHAR(255) DEFAULT NULL,
  ad_id           VARCHAR(64)  NOT NULL,
  ad_name         VARCHAR(255) DEFAULT NULL,
  objective       VARCHAR(64)  DEFAULT NULL,
  -- Headline metrics
  impressions     INT UNSIGNED NOT NULL DEFAULT 0,
  reach           INT UNSIGNED NOT NULL DEFAULT 0,
  clicks          INT UNSIGNED NOT NULL DEFAULT 0,
  link_clicks     INT UNSIGNED NOT NULL DEFAULT 0,
  spend           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  ctr             DECIMAL(8,4)  NOT NULL DEFAULT 0.0000,
  -- WhatsApp / Messenger conversation metrics
  whatsapp_clicks               INT UNSIGNED NOT NULL DEFAULT 0,
  messenger_clicks              INT UNSIGNED NOT NULL DEFAULT 0,
  conversations_started         INT UNSIGNED NOT NULL DEFAULT 0,
  -- Full actions array for forensics / future metrics
  raw_actions     TEXT,
  synced_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_meta_metric (date, ad_id),
  KEY idx_meta_date (date),
  KEY idx_meta_campaign (campaign_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── audit_logs ───────────────────────────────────────────────────────────
CREATE TABLE audit_logs (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     BIGINT UNSIGNED DEFAULT NULL,
  action      VARCHAR(80) NOT NULL,
  entity      VARCHAR(40) DEFAULT NULL,
  entity_id   BIGINT UNSIGNED DEFAULT NULL,
  meta        TEXT,
  ip          VARCHAR(64) DEFAULT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_audit_user (user_id),
  KEY idx_audit_action (action),
  KEY idx_audit_entity (entity, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
