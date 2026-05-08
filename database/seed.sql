-- SLV Voucher Goodie Platform — seed data for development.
--
-- Default password for ALL seeded accounts is `admin123` / `merchant123` /
-- `gov123`. The bcrypt hashes below were generated with PHP's
-- password_hash($p, PASSWORD_BCRYPT, ['cost' => 10]).
--
-- admin@slvgroup.my       → admin123
-- gov@selangor.gov.my     → gov123
-- merchant@demo.my        → merchant123

SET NAMES utf8mb4;

INSERT INTO users (name, email, phone, password, role, status) VALUES
  ('SLV Super Admin', 'admin@slvgroup.my',  '+60123456789',
   '$2y$10$ttSp6NObYRbiSq01.pI/DOMHanbiEwHFFiEngWty4H9FulAbEg33W', 'admin', 'active'),
  ('Selangor Tourism', 'gov@selangor.gov.my', '+60111111111',
   '$2y$10$aY8x0z57HmtN/9zh4WONseRIueh8G2hF5MWccM5t797yusnWTJKGO', 'gov', 'active'),
  ('Sekinchan Seafood Owner', 'merchant@demo.my', '+60198765432',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active');

INSERT INTO locations (state, city, area_name, slug, description, banner_image, map_link, status) VALUES
  ('Selangor', 'Sabak Bernam', 'Sekinchan Food Trail', 'sekinchan',
   'Famous paddy fields, fresh seafood, and coastal sunsets. Claim your local food voucher and enjoy Sekinchan!',
   NULL, 'https://maps.google.com/?q=Sekinchan,Selangor', 'active'),
  ('Penang', 'George Town', 'Heritage Food Trail', 'georgetown',
   'UNESCO heritage streets, hawker classics and street art.',
   NULL, 'https://maps.google.com/?q=George+Town,Penang', 'active'),
  ('Sabah', 'Kota Kinabalu', 'Borneo Coastal Experience', 'kk-coastal',
   'Island hopping, sunset markets and Borneo cuisine.',
   NULL, 'https://maps.google.com/?q=Kota+Kinabalu,Sabah', 'active');

INSERT INTO merchants (
  user_id, business_name, owner_name, phone, whatsapp, email, category, location_id,
  address, map_link, registration_no, operating_hours, wallet_balance, subscription_status, status
) VALUES (
  3, 'Sekinchan Seafood Restaurant', 'Mr Tan',
  '+60198765432', '+60198765432', 'merchant@demo.my', 'fnb', 1,
  'No 88, Jalan Pantai, Sekinchan, Selangor',
  'https://maps.google.com/?q=Sekinchan+Seafood',
  'SSM-202301-0088', '11:00 - 22:00 daily', 242.50, 'active', 'active'
);

INSERT INTO merchant_subscriptions (merchant_id, annual_fee, start_date, expiry_date, payment_status, status) VALUES
  (1, 150.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 'paid', 'active');

INSERT INTO campaigns (location_id, campaign_name, slug, description, voucher_type, voucher_value, start_date, end_date, claim_limit, terms, status) VALUES
  (1, 'Visit Sekinchan Food Campaign', 'visit-sekinchan-food',
   'Claim a free local food voucher worth RM10 at participating Sekinchan merchants. Limited to 1,000 vouchers.',
   'cash', 10.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 1000,
   'One voucher per phone number. Valid only at participating merchants. Cannot be exchanged for cash.',
   'active'),
  (2, 'Penang Heritage Food Walk', 'penang-heritage-food',
   'RM5 off your next hawker meal in George Town heritage zone.',
   'discount', 5.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 500,
   'Single use per voucher. Not stackable.',
   'active');

INSERT INTO campaign_merchants (campaign_id, merchant_id, status) VALUES
  (1, 1, 'active');

-- Default site settings (empty = falls back to gradient/translation)
INSERT INTO site_settings (`key`, `value`) VALUES
  ('hero_home_image',          NULL),
  ('hero_about_image',         NULL),
  ('hero_for_merchants_image', NULL),
  ('intro_home_html',          NULL),
  ('intro_about_html',         NULL);

-- ─────────────────────────────────────────────────────────────────────────
-- DEMO DATA — extra merchants, vouchers, redemptions, wallet activity.
-- Skip / delete this block in production-style seeds.
-- All merchant accounts use the password "merchant123".
-- ─────────────────────────────────────────────────────────────────────────

-- Extra merchant users (id 4–10)
INSERT INTO users (name, email, phone, password, role, status) VALUES
  ('Aunty Lim',  'paddycafe@demo.my',     '+60198811001',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Mr Wong',    'souvenirhut@demo.my',   '+60198811002',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Sis Aisyah', 'sweetbakery@demo.my',   '+60198811003',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Ah Beng',    'koayteow@demo.my',      '+60198811004',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Ms Chong',   'heritagecafe@demo.my',  '+60198811005',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Captain Joe','sunsetlounge@demo.my',  '+60198811006',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Mr Daniel',  'borneosouvenir@demo.my','+60198811007',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active');

-- Extra merchants (id 2–8). Wallet balances chosen to demonstrate every
-- wallet status tier defined in App\Models\Merchant::walletStatus():
--   healthy ≥ 150 · warning < 150 · critical < 120 · disabled < 100
--
-- Tier coverage:
--   healthy   : 1 (242.50), 2 (495.50), 3 (198.50), 5 (295.50), 7 (600.00)
--   warning   : 6 (127.00)
--   critical  : 8 (115.00)
--   disabled  : 4 ( 98.50)
INSERT INTO merchants (
  user_id, business_name, owner_name, phone, whatsapp, email, category, location_id,
  address, map_link, registration_no, operating_hours, wallet_balance, subscription_status, status
) VALUES
  (4, 'Sekinchan Paddy Cafe',     'Aunty Lim',   '+60198811001', '+60198811001', 'paddycafe@demo.my',     'fnb',        1, 'No 12, Jalan Sawah Padi, Sekinchan',         'https://maps.google.com/?q=Sekinchan+Paddy+Cafe',  'SSM-202311-0012', '08:00 - 18:00 daily',           495.50, 'active', 'active'),
  (5, 'Sekinchan Souvenir Hut',   'Mr Wong',     '+60198811002', '+60198811002', 'souvenirhut@demo.my',   'souvenir',   1, 'No 3, Pantai Redang, Sekinchan',             'https://maps.google.com/?q=Sekinchan+Souvenir',    'SSM-202310-0003', '10:00 - 21:00 daily',           198.50, 'active', 'active'),
  (6, 'Sekinchan Sweet Bakery',   'Sis Aisyah',  '+60198811003', '+60198811003', 'sweetbakery@demo.my',   'fnb',        1, 'No 45, Jalan Bagan, Sekinchan',              'https://maps.google.com/?q=Sekinchan+Bakery',      'SSM-202312-0045', '07:00 - 19:00 daily',            98.50, 'active', 'active'),
  (7, 'Char Koay Teow Penang',    'Ah Beng',     '+60198811004', '+60198811004', 'koayteow@demo.my',      'fnb',        2, 'Lorong Selamat, George Town',                'https://maps.google.com/?q=Lorong+Selamat',        'SSM-202309-0099', '17:00 - 23:00 (closed Mon)',    295.50, 'active', 'active'),
  (8, 'Penang Heritage Cafe',     'Ms Chong',    '+60198811005', '+60198811005', 'heritagecafe@demo.my',  'fnb',        2, 'Armenian Street, George Town',               'https://maps.google.com/?q=Armenian+Street',       'SSM-202308-0021', '09:00 - 22:00 daily',           127.00, 'active', 'active'),
  (9, 'KK Sunset Lounge',         'Captain Joe', '+60198811006', '+60198811006', 'sunsetlounge@demo.my',  'experience', 3, 'Tanjung Aru, Kota Kinabalu',                 'https://maps.google.com/?q=Tanjung+Aru',           'SSM-202306-0077', '15:00 - 24:00 daily',           600.00, 'active', 'active'),
  (10,'Borneo Souvenir Shop',     'Mr Daniel',   '+60198811007', '+60198811007', 'borneosouvenir@demo.my','souvenir',   3, 'Gaya Street, Kota Kinabalu',                 'https://maps.google.com/?q=Gaya+Street+KK',        'SSM-202307-0034', '09:00 - 18:00 daily',           115.00, 'active', 'active');

-- All extra merchants have an active 1-year subscription
INSERT INTO merchant_subscriptions (merchant_id, annual_fee, start_date, expiry_date, payment_status, status) VALUES
  (2, 150.00, DATE_SUB(CURDATE(), INTERVAL 30 DAY),  DATE_ADD(CURDATE(), INTERVAL 335 DAY), 'paid', 'active'),
  (3, 150.00, DATE_SUB(CURDATE(), INTERVAL 60 DAY),  DATE_ADD(CURDATE(), INTERVAL 305 DAY), 'paid', 'active'),
  (4, 150.00, DATE_SUB(CURDATE(), INTERVAL 14 DAY),  DATE_ADD(CURDATE(), INTERVAL 351 DAY), 'paid', 'active'),
  (5, 150.00, DATE_SUB(CURDATE(), INTERVAL 90 DAY),  DATE_ADD(CURDATE(), INTERVAL 275 DAY), 'paid', 'active'),
  (6, 150.00, DATE_SUB(CURDATE(), INTERVAL 45 DAY),  DATE_ADD(CURDATE(), INTERVAL 320 DAY), 'paid', 'active'),
  (7, 150.00, DATE_SUB(CURDATE(), INTERVAL 7 DAY),   DATE_ADD(CURDATE(), INTERVAL 358 DAY), 'paid', 'active'),
  (8, 150.00, DATE_SUB(CURDATE(), INTERVAL 21 DAY),  DATE_ADD(CURDATE(), INTERVAL 344 DAY), 'paid', 'active');

-- Attach merchants to campaigns
INSERT INTO campaign_merchants (campaign_id, merchant_id, status) VALUES
  (1, 2, 'active'),
  (1, 3, 'active'),
  (1, 4, 'active'),
  (2, 5, 'active'),
  (2, 6, 'active');

-- Wallet topups (paid). Amounts produce the wallet_balance values above
-- after the redemptions below are applied.
INSERT INTO wallet_topups (merchant_id, amount, billplz_bill_id, payment_status, paid_at, created_at) VALUES
  (1, 250.00, 'DEMO-TOP-1-001',  'paid', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 60 DAY)),
  (2, 500.00, 'DEMO-TOP-2-001',  'paid', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
  (3, 200.00, 'DEMO-TOP-3-001',  'paid', DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY)),
  (4, 100.00, 'DEMO-TOP-4-001',  'paid', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (5, 300.00, 'DEMO-TOP-5-001',  'paid', DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY)),
  (6, 130.00, 'DEMO-TOP-6-001',  'paid', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (7, 600.00, 'DEMO-TOP-7-001',  'paid', DATE_SUB(NOW(), INTERVAL  6 DAY), DATE_SUB(NOW(), INTERVAL  6 DAY)),
  (8, 115.00, 'DEMO-TOP-8-001',  'paid', DATE_SUB(NOW(), INTERVAL 19 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY));

-- Mirror those topups in the wallet ledger.
INSERT INTO merchant_wallet_transactions (merchant_id, type, amount, balance_before, balance_after, description, reference_type, reference_id, created_at) VALUES
  (1, 'topup',  250.00,   0.00, 250.00, 'Wallet top-up · Billplz DEMO-TOP-1-001', 'wallet_topup', 1, DATE_SUB(NOW(), INTERVAL 60 DAY)),
  (2, 'topup',  500.00,   0.00, 500.00, 'Wallet top-up · Billplz DEMO-TOP-2-001', 'wallet_topup', 2, DATE_SUB(NOW(), INTERVAL 25 DAY)),
  (3, 'topup',  200.00,   0.00, 200.00, 'Wallet top-up · Billplz DEMO-TOP-3-001', 'wallet_topup', 3, DATE_SUB(NOW(), INTERVAL 18 DAY)),
  (4, 'topup',  100.00,   0.00, 100.00, 'Wallet top-up · Billplz DEMO-TOP-4-001', 'wallet_topup', 4, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (5, 'topup',  300.00,   0.00, 300.00, 'Wallet top-up · Billplz DEMO-TOP-5-001', 'wallet_topup', 5, DATE_SUB(NOW(), INTERVAL 22 DAY)),
  (6, 'topup',  130.00,   0.00, 130.00, 'Wallet top-up · Billplz DEMO-TOP-6-001', 'wallet_topup', 6, DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (7, 'topup',  600.00,   0.00, 600.00, 'Wallet top-up · Billplz DEMO-TOP-7-001', 'wallet_topup', 7, DATE_SUB(NOW(), INTERVAL  6 DAY)),
  (8, 'topup',  115.00,   0.00, 115.00, 'Wallet top-up · Billplz DEMO-TOP-8-001', 'wallet_topup', 8, DATE_SUB(NOW(), INTERVAL 19 DAY));

-- ─── Demo vouchers ────────────────────────────────────────────────────────
-- qr_token is a placeholder; the QR scanner won't decode these (HMAC fails),
-- but manual code entry uses voucher_code directly and works fine.
-- Mix of statuses (claimed / redeemed / expired) populates dashboards.

INSERT INTO vouchers
  (campaign_id, merchant_id, voucher_code, qr_token, customer_name, customer_phone, status, claimed_at, redeemed_at, expired_at) VALUES
  -- Sekinchan campaign · redeemed at various merchants
  (1, 1, 'SLV-SEKI-2026-0001AA', 'demo-token-0001', 'Lim Wei Jie',     '+60112000001', 'redeemed', DATE_SUB(NOW(), INTERVAL 12 DAY),  DATE_SUB(NOW(), INTERVAL 12 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 2, 'SLV-SEKI-2026-0002BB', 'demo-token-0002', 'Tan Mei Ling',    '+60112000002', 'redeemed', DATE_SUB(NOW(), INTERVAL 11 DAY),  DATE_SUB(NOW(), INTERVAL 11 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-SEKI-2026-0003CC', 'demo-token-0003', 'Ahmad Faiz',      '+60112000003', 'redeemed', DATE_SUB(NOW(), INTERVAL 10 DAY),  DATE_SUB(NOW(), INTERVAL 10 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 3, 'SLV-SEKI-2026-0004DD', 'demo-token-0004', 'Siti Nor',        '+60112000004', 'redeemed', DATE_SUB(NOW(), INTERVAL  9 DAY),  DATE_SUB(NOW(), INTERVAL  9 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 2, 'SLV-SEKI-2026-0005EE', 'demo-token-0005', 'Daniel Lee',      '+60112000005', 'redeemed', DATE_SUB(NOW(), INTERVAL  7 DAY),  DATE_SUB(NOW(), INTERVAL  7 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-SEKI-2026-0006FF', 'demo-token-0006', 'Nurul Hidayah',   '+60112000006', 'redeemed', DATE_SUB(NOW(), INTERVAL  5 DAY),  DATE_SUB(NOW(), INTERVAL  5 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 4, 'SLV-SEKI-2026-0007GG', 'demo-token-0007', 'Jasmine Chong',   '+60112000007', 'redeemed', DATE_SUB(NOW(), INTERVAL  4 DAY),  DATE_SUB(NOW(), INTERVAL  4 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-SEKI-2026-0008HH', 'demo-token-0008', 'Kumar Raj',       '+60112000008', 'redeemed', DATE_SUB(NOW(), INTERVAL  3 DAY),  DATE_SUB(NOW(), INTERVAL  3 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 2, 'SLV-SEKI-2026-0009II', 'demo-token-0009', 'Wong Kah Hui',    '+60112000009', 'redeemed', DATE_SUB(NOW(), INTERVAL  2 DAY),  DATE_SUB(NOW(), INTERVAL  2 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-SEKI-2026-0010JJ', 'demo-token-0010', 'Faridah Mat',     '+60112000010', 'redeemed', DATE_SUB(NOW(), INTERVAL  1 DAY),  DATE_SUB(NOW(), INTERVAL  1 DAY),  DATE_ADD(CURDATE(), INTERVAL 90 DAY)),

  -- Sekinchan campaign · still claimed (ready to redeem)
  (1, NULL, 'SLV-SEKI-2026-1001AA', 'demo-token-1001', 'Tourist Sarah',  '+60112100001', 'claimed', DATE_SUB(NOW(), INTERVAL  6 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, NULL, 'SLV-SEKI-2026-1002BB', 'demo-token-1002', 'Tourist Brian',  '+60112100002', 'claimed', DATE_SUB(NOW(), INTERVAL  4 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, NULL, 'SLV-SEKI-2026-1003CC', 'demo-token-1003', 'Tourist Cheng',  '+60112100003', 'claimed', DATE_SUB(NOW(), INTERVAL  2 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, NULL, 'SLV-SEKI-2026-1004DD', 'demo-token-1004', 'Tourist Diana',  '+60112100004', 'claimed', DATE_SUB(NOW(), INTERVAL  1 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, NULL, 'SLV-SEKI-2026-1005EE', 'demo-token-1005', 'Tourist Eric',   '+60112100005', 'claimed', DATE_SUB(NOW(), INTERVAL 30 MINUTE), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),

  -- Sekinchan campaign · expired
  (1, NULL, 'SLV-SEKI-2025-9001ZZ', 'demo-token-9001', 'Lapsed Visitor', '+60112900001', 'expired', DATE_SUB(NOW(), INTERVAL 95 DAY),  NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),

  -- Penang campaign · redemptions
  (2, 5, 'SLV-HERI-2026-0001AA', 'demo-token-2001', 'Tan Hong Yi',    '+60112200001', 'redeemed', DATE_SUB(NOW(), INTERVAL 13 DAY),  DATE_SUB(NOW(), INTERVAL 13 DAY),  DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, 6, 'SLV-HERI-2026-0002BB', 'demo-token-2002', 'Lee Su Ann',     '+60112200002', 'redeemed', DATE_SUB(NOW(), INTERVAL 10 DAY),  DATE_SUB(NOW(), INTERVAL 10 DAY),  DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, 5, 'SLV-HERI-2026-0003CC', 'demo-token-2003', 'Mohd Iqbal',     '+60112200003', 'redeemed', DATE_SUB(NOW(), INTERVAL  8 DAY),  DATE_SUB(NOW(), INTERVAL  8 DAY),  DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, 6, 'SLV-HERI-2026-0004DD', 'demo-token-2004', 'Priya Devi',     '+60112200004', 'redeemed', DATE_SUB(NOW(), INTERVAL  6 DAY),  DATE_SUB(NOW(), INTERVAL  6 DAY),  DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, 5, 'SLV-HERI-2026-0005EE', 'demo-token-2005', 'Goh Ming Han',   '+60112200005', 'redeemed', DATE_SUB(NOW(), INTERVAL  3 DAY),  DATE_SUB(NOW(), INTERVAL  3 DAY),  DATE_ADD(CURDATE(), INTERVAL 60 DAY)),

  -- Penang campaign · still claimed
  (2, NULL, 'SLV-HERI-2026-1001FF', 'demo-token-2101', 'Heritage Tourist 1', '+60112210001', 'claimed', DATE_SUB(NOW(), INTERVAL 5 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, NULL, 'SLV-HERI-2026-1002GG', 'demo-token-2102', 'Heritage Tourist 2', '+60112210002', 'claimed', DATE_SUB(NOW(), INTERVAL 3 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, NULL, 'SLV-HERI-2026-1003HH', 'demo-token-2103', 'Heritage Tourist 3', '+60112210003', 'claimed', DATE_SUB(NOW(), INTERVAL 1 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY));

-- ─── Demo redemptions ─────────────────────────────────────────────────────
-- Each merchant's running balance is reconciled to merchants.wallet_balance.
INSERT INTO redemptions (voucher_id, campaign_id, merchant_id, redemption_fee, wallet_balance_before, wallet_balance_after, redeemed_by, redeemed_at) VALUES
  -- Merchant 1 (Sekinchan Seafood) · 250.00 → 242.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0001AA'), 1, 1, 1.50, 250.00, 248.50, 3, DATE_SUB(NOW(), INTERVAL 12 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0003CC'), 1, 1, 1.50, 248.50, 247.00, 3, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0006FF'), 1, 1, 1.50, 247.00, 245.50, 3, DATE_SUB(NOW(), INTERVAL  5 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0008HH'), 1, 1, 1.50, 245.50, 244.00, 3, DATE_SUB(NOW(), INTERVAL  3 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0010JJ'), 1, 1, 1.50, 244.00, 242.50, 3, DATE_SUB(NOW(), INTERVAL  1 DAY)),

  -- Merchant 2 (Paddy Cafe) · 500.00 → 495.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0002BB'), 1, 2, 1.50, 500.00, 498.50, 4, DATE_SUB(NOW(), INTERVAL 11 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0005EE'), 1, 2, 1.50, 498.50, 497.00, 4, DATE_SUB(NOW(), INTERVAL  7 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0009II'), 1, 2, 1.50, 497.00, 495.50, 4, DATE_SUB(NOW(), INTERVAL  2 DAY)),

  -- Merchant 3 (Souvenir Hut) · 200.00 → 198.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0004DD'), 1, 3, 1.50, 200.00, 198.50, 5, DATE_SUB(NOW(), INTERVAL  9 DAY)),

  -- Merchant 4 (Sweet Bakery) · 100.00 → 98.50  (DISABLED tier)
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SEKI-2026-0007GG'), 1, 4, 1.50, 100.00,  98.50, 6, DATE_SUB(NOW(), INTERVAL  4 DAY)),

  -- Merchant 5 (Char Koay Teow Penang) · 300.00 → 295.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-HERI-2026-0001AA'), 2, 5, 1.50, 300.00, 298.50, 7, DATE_SUB(NOW(), INTERVAL 13 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-HERI-2026-0003CC'), 2, 5, 1.50, 298.50, 297.00, 7, DATE_SUB(NOW(), INTERVAL  8 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-HERI-2026-0005EE'), 2, 5, 1.50, 297.00, 295.50, 7, DATE_SUB(NOW(), INTERVAL  3 DAY)),

  -- Merchant 6 (Heritage Cafe) · 130.00 → 127.00  (WARNING tier)
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-HERI-2026-0002BB'), 2, 6, 1.50, 130.00, 128.50, 8, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-HERI-2026-0004DD'), 2, 6, 1.50, 128.50, 127.00, 8, DATE_SUB(NOW(), INTERVAL  6 DAY));

-- Mirror redemption fees in the wallet ledger (each merchant's chain matches above).
INSERT INTO merchant_wallet_transactions (merchant_id, type, amount, balance_before, balance_after, description, reference_type, reference_id, created_at) VALUES
  (1, 'redemption', -1.50, 250.00, 248.50, 'Voucher SLV-SEKI-2026-0001AA', 'redemption',  1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
  (1, 'redemption', -1.50, 248.50, 247.00, 'Voucher SLV-SEKI-2026-0003CC', 'redemption',  2, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (1, 'redemption', -1.50, 247.00, 245.50, 'Voucher SLV-SEKI-2026-0006FF', 'redemption',  3, DATE_SUB(NOW(), INTERVAL  5 DAY)),
  (1, 'redemption', -1.50, 245.50, 244.00, 'Voucher SLV-SEKI-2026-0008HH', 'redemption',  4, DATE_SUB(NOW(), INTERVAL  3 DAY)),
  (1, 'redemption', -1.50, 244.00, 242.50, 'Voucher SLV-SEKI-2026-0010JJ', 'redemption',  5, DATE_SUB(NOW(), INTERVAL  1 DAY)),

  (2, 'redemption', -1.50, 500.00, 498.50, 'Voucher SLV-SEKI-2026-0002BB', 'redemption',  6, DATE_SUB(NOW(), INTERVAL 11 DAY)),
  (2, 'redemption', -1.50, 498.50, 497.00, 'Voucher SLV-SEKI-2026-0005EE', 'redemption',  7, DATE_SUB(NOW(), INTERVAL  7 DAY)),
  (2, 'redemption', -1.50, 497.00, 495.50, 'Voucher SLV-SEKI-2026-0009II', 'redemption',  8, DATE_SUB(NOW(), INTERVAL  2 DAY)),

  (3, 'redemption', -1.50, 200.00, 198.50, 'Voucher SLV-SEKI-2026-0004DD', 'redemption',  9, DATE_SUB(NOW(), INTERVAL  9 DAY)),

  (4, 'redemption', -1.50, 100.00,  98.50, 'Voucher SLV-SEKI-2026-0007GG', 'redemption', 10, DATE_SUB(NOW(), INTERVAL  4 DAY)),

  (5, 'redemption', -1.50, 300.00, 298.50, 'Voucher SLV-HERI-2026-0001AA', 'redemption', 11, DATE_SUB(NOW(), INTERVAL 13 DAY)),
  (5, 'redemption', -1.50, 298.50, 297.00, 'Voucher SLV-HERI-2026-0003CC', 'redemption', 12, DATE_SUB(NOW(), INTERVAL  8 DAY)),
  (5, 'redemption', -1.50, 297.00, 295.50, 'Voucher SLV-HERI-2026-0005EE', 'redemption', 13, DATE_SUB(NOW(), INTERVAL  3 DAY)),

  (6, 'redemption', -1.50, 130.00, 128.50, 'Voucher SLV-HERI-2026-0002BB', 'redemption', 14, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (6, 'redemption', -1.50, 128.50, 127.00, 'Voucher SLV-HERI-2026-0004DD', 'redemption', 15, DATE_SUB(NOW(), INTERVAL  6 DAY));
