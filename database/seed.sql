-- SLV Voucher Goodie Platform — seed data (Bentong, Pahang focus).
--
-- The whole demo is set in Bentong: one location, eight Bentong-based
-- merchants spanning all wallet status tiers (healthy / warning /
-- critical / disabled), five Bentong tourism campaigns (durian, ginger,
-- heritage coffee, Chamang waterfall, hot spring), and 24 vouchers with
-- a full 15-row redemption history reconciled to every merchant's
-- wallet ledger.
--
-- Login accounts (password = same as account name, e.g. merchant123):
--   admin@slvgroup.my            → admin123
--   gov@pahang.gov.my            → gov123
--   merchant@demo.my             → merchant123   (Restoran Lou Wong)
--   gingercafe@demo.my           → merchant123   (Bentong Ginger Cafe)
--   kopitiam@demo.my             → merchant123   (Hai Lam Heritage Kopitiam)
--   chamang@demo.my              → merchant123   (Chamang Waterfall Adventures)
--   hotspring@demo.my            → merchant123   (Suria Bentong Hot Spring)
--   homestay@demo.my             → merchant123   (Mountain View Homestay)
--   souvenir@demo.my             → merchant123   (Pekan Bentong Souvenir House)
--   durianstall@demo.my          → merchant123   (Bentong Durian Trail Stall)

SET NAMES utf8mb4;

-- ─── Users ────────────────────────────────────────────────────────────────
INSERT INTO users (name, email, phone, password, role, status) VALUES
  ('SLV Super Admin', 'admin@slvgroup.my', '+60123456789',
   '$2y$10$ttSp6NObYRbiSq01.pI/DOMHanbiEwHFFiEngWty4H9FulAbEg33W', 'admin', 'active'),
  ('Pahang Tourism', 'gov@pahang.gov.my', '+60111111111',
   '$2y$10$aY8x0z57HmtN/9zh4WONseRIueh8G2hF5MWccM5t797yusnWTJKGO', 'gov', 'active'),
  ('Mr Tan (Lou Wong)', 'merchant@demo.my', '+60198765432',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active');

-- ─── Location (Bentong only) ──────────────────────────────────────────────
INSERT INTO locations (state, city, area_name, slug, description, banner_image, map_link, status) VALUES
  ('Pahang', 'Bentong', 'Bentong Town & Highlands', 'bentong',
   'Cool highland gateway to Pahang famed for premium Musang King durians, Bentong ginger, heritage shophouses on Pekan Bentong, Chamang waterfall, and natural hot springs.',
   NULL, 'https://maps.google.com/?q=Bentong,Pahang', 'active');

-- ─── Main demo merchant (id 1) ────────────────────────────────────────────
INSERT INTO merchants (
  user_id, business_name, owner_name, phone, whatsapp, email, category, location_id,
  address, map_link, registration_no, operating_hours, wallet_balance, subscription_status, status
) VALUES (
  3, 'Restoran Lou Wong Bentong', 'Mr Tan',
  '+60198765432', '+60198765432', 'merchant@demo.my', 'fnb', 1,
  'No 88, Jalan Loke Yew, Pekan Bentong, 28700 Bentong, Pahang',
  'https://maps.google.com/?q=Pekan+Bentong+Restoran',
  'SSM-202301-0088', '11:00 - 22:00 daily', 242.50, 'active', 'active'
);

INSERT INTO merchant_subscriptions (merchant_id, annual_fee, start_date, expiry_date, payment_status, status) VALUES
  (1, 150.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 'paid', 'active');

-- ─── Five Bentong tourism campaigns ───────────────────────────────────────
INSERT INTO campaigns (location_id, campaign_name, slug, description, voucher_type, voucher_value, start_date, end_date, claim_limit, terms, status) VALUES
  (1, 'Bentong Durian Trail 2026', 'bentong-durian-trail-2026',
   'Claim a RM15 cash voucher to taste authentic Musang King and other Bentong-grown durians along the Bentong Durian Trail. Visit certified farms and participating stalls during peak season.',
   'cash', 15.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 500,
   'One voucher per phone number. Minimum spend RM30 to redeem. Valid only at participating durian stalls displaying the SLV badge. Voucher expires on campaign end date.',
   'active'),
  (1, 'Bentong Ginger Discount Trail', 'bentong-ginger-trail',
   'RM5 off any purchase of authentic Bentong ginger products — fresh ginger root, ginger candy, ginger tea, ginger soap and herbal blends — at participating Pekan Bentong shops.',
   'discount', 5.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 300,
   'Single use per voucher. Cannot be combined with other promotions. Valid at participating Bentong ginger merchants only.',
   'active'),
  (1, 'Pekan Bentong Heritage Coffee Walk', 'pekan-bentong-coffee-walk',
   'Buy 1 Free 1 traditional white coffee with kaya toast at heritage kopitiams along the old shophouse street of Pekan Bentong. Walk the trail, taste the history.',
   'b1f1', 8.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 45 DAY), 200,
   'Buy one regular coffee + kaya toast set, get a second free. Dine-in only. One voucher per customer per visit. Valid weekdays 8am-5pm.',
   'active'),
  (1, 'Chamang Waterfall Adventure Pass', 'chamang-waterfall-pass',
   'Free guided morning trail to Chamang Waterfall plus a complimentary Bentong herbal drink at the rest stop. Discover one of Pahang''s most photogenic jungle waterfalls.',
   'experience', 25.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 100,
   'Weekends only (Sat-Sun). Trail starts 8:00am at Chamang carpark. Bring water and proper footwear. Children below 12 must be accompanied.',
   'active'),
  (1, 'Bentong Hot Spring Sunset Voucher', 'bentong-hot-spring-sunset',
   'RM10 off entry to participating Bentong natural hot springs after 5pm. Soak away your travels in geothermal mineral water under the Pahang highland sunset.',
   'discount', 10.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 75 DAY), 200,
   'Valid daily after 5:00pm. One voucher per customer per visit. Bring your own towel. Children below 6 enter free.',
   'active');

-- Main merchant participates in the Durian Trail campaign
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
-- DEMO DATA — extra Bentong merchants, vouchers, redemptions, wallet
-- activity. Skip / delete this block in production-style seeds.
-- All extra merchant accounts use the password "merchant123".
-- ─────────────────────────────────────────────────────────────────────────

-- Extra merchant users (id 4–10)
INSERT INTO users (name, email, phone, password, role, status) VALUES
  ('Aunty Lim (Ginger Cafe)', 'gingercafe@demo.my', '+60198811001',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Uncle Wong (Hai Lam Kopitiam)', 'kopitiam@demo.my', '+60198811002',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Sis Aisyah (Chamang Tours)', 'chamang@demo.my', '+60198811003',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Mr Lee (Suria Hot Spring)', 'hotspring@demo.my', '+60198811004',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Ms Chong (Mountain View)', 'homestay@demo.my', '+60198811005',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Mr Daniel (Pekan Souvenir)', 'souvenir@demo.my', '+60198811006',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Mr Roslan (Durian Trail Stall)', 'durianstall@demo.my', '+60198811007',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active');

-- Extra merchants (id 2–8). Wallet balances chosen to demonstrate every
-- wallet status tier defined in App\Models\Merchant::walletStatus():
--   healthy ≥ 150 · warning < 150 · critical < 120 · disabled < 100
--
-- Tier coverage:
--   healthy   : 1 (242.50), 2 (495.50), 3 (198.50), 5 (295.50), 7 (600.00)
--   warning   : 6 (127.00)  ← Mountain View Homestay
--   critical  : 8 (115.00)  ← Bentong Durian Trail Stall
--   disabled  : 4 ( 98.50)  ← Chamang Waterfall Adventures
INSERT INTO merchants (
  user_id, business_name, owner_name, phone, whatsapp, email, category, location_id,
  address, map_link, registration_no, operating_hours, wallet_balance, subscription_status, status
) VALUES
  (4, 'Bentong Ginger Cafe & Shop',     'Aunty Lim',    '+60198811001', '+60198811001', 'gingercafe@demo.my',  'fnb',        1, 'Lot 12, Jalan Loke Yew, Pekan Bentong',           'https://maps.google.com/?q=Pekan+Bentong+Ginger',         'SSM-202311-0012', '08:00 - 18:00 daily',           495.50, 'active', 'active'),
  (5, 'Hai Lam Heritage Kopitiam',      'Uncle Wong',   '+60198811002', '+60198811002', 'kopitiam@demo.my',    'fnb',        1, 'No 3, Jalan Ah Peng, Pekan Bentong',              'https://maps.google.com/?q=Pekan+Bentong+Kopitiam',       'SSM-202310-0003', '07:00 - 17:00 daily',           198.50, 'active', 'active'),
  (6, 'Chamang Waterfall Adventures',   'Sis Aisyah',   '+60198811003', '+60198811003', 'chamang@demo.my',     'experience', 1, 'Kampung Chamang, Jalan Chamang, 28700 Bentong',   'https://maps.google.com/?q=Chamang+Waterfall+Bentong',    'SSM-202312-0045', '07:00 - 18:00 (Sat-Sun)',         98.50, 'active', 'active'),
  (7, 'Suria Bentong Hot Spring',       'Mr Lee',       '+60198811004', '+60198811004', 'hotspring@demo.my',   'experience', 1, 'Jalan Suria, 28700 Bentong, Pahang',              'https://maps.google.com/?q=Suria+Hot+Spring+Bentong',     'SSM-202309-0099', '10:00 - 23:00 daily',           295.50, 'active', 'active'),
  (8, 'Mountain View Homestay Bentong', 'Ms Chong',     '+60198811005', '+60198811005', 'homestay@demo.my',    'hotel',      1, 'Bukit Tinggi Road, 28700 Bentong',                'https://maps.google.com/?q=Bentong+Homestay',             'SSM-202308-0021', 'Check-in 14:00 / Check-out 11:00',127.00, 'active', 'active'),
  (9, 'Pekan Bentong Souvenir House',   'Mr Daniel',    '+60198811006', '+60198811006', 'souvenir@demo.my',    'souvenir',   1, 'No 25, Jalan Loke Yew, Pekan Bentong',            'https://maps.google.com/?q=Pekan+Bentong+Souvenir',       'SSM-202306-0077', '09:00 - 21:00 daily',           600.00, 'active', 'active'),
  (10,'Bentong Durian Trail Stall',     'Mr Roslan',    '+60198811007', '+60198811007', 'durianstall@demo.my', 'fnb',        1, 'Bentong Durian Trail, KM12 Jalan Genting',        'https://maps.google.com/?q=Bentong+Durian+Stall',         'SSM-202307-0034', '11:00 - 22:00 (durian season)', 115.00, 'active', 'active');

-- All extra merchants have an active 1-year subscription
INSERT INTO merchant_subscriptions (merchant_id, annual_fee, start_date, expiry_date, payment_status, status) VALUES
  (2, 150.00, DATE_SUB(CURDATE(), INTERVAL 30 DAY), DATE_ADD(CURDATE(), INTERVAL 335 DAY), 'paid', 'active'),
  (3, 150.00, DATE_SUB(CURDATE(), INTERVAL 60 DAY), DATE_ADD(CURDATE(), INTERVAL 305 DAY), 'paid', 'active'),
  (4, 150.00, DATE_SUB(CURDATE(), INTERVAL 14 DAY), DATE_ADD(CURDATE(), INTERVAL 351 DAY), 'paid', 'active'),
  (5, 150.00, DATE_SUB(CURDATE(), INTERVAL 90 DAY), DATE_ADD(CURDATE(), INTERVAL 275 DAY), 'paid', 'active'),
  (6, 150.00, DATE_SUB(CURDATE(), INTERVAL 45 DAY), DATE_ADD(CURDATE(), INTERVAL 320 DAY), 'paid', 'active'),
  (7, 150.00, DATE_SUB(CURDATE(), INTERVAL 7 DAY),  DATE_ADD(CURDATE(), INTERVAL 358 DAY), 'paid', 'active'),
  (8, 150.00, DATE_SUB(CURDATE(), INTERVAL 21 DAY), DATE_ADD(CURDATE(), INTERVAL 344 DAY), 'paid', 'active');

-- ─── Campaign ↔ Merchant participation ────────────────────────────────────
-- Campaign 1 (Durian Trail)  : Restoran Lou Wong (1) + Durian Trail Stall (8)
-- Campaign 2 (Ginger)        : Bentong Ginger Cafe (2) + Pekan Souvenir House (7)
-- Campaign 3 (Coffee Walk)   : Hai Lam Heritage Kopitiam (3)
-- Campaign 4 (Chamang)       : Chamang Waterfall Adventures (4)
-- Campaign 5 (Hot Spring)    : Suria Hot Spring (5) + Mountain View Homestay (6)
INSERT INTO campaign_merchants (campaign_id, merchant_id, status) VALUES
  (1, 8, 'active'),
  (2, 2, 'active'),
  (2, 7, 'active'),
  (3, 3, 'active'),
  (4, 4, 'active'),
  (5, 5, 'active'),
  (5, 6, 'active');

-- ─── Wallet topups ────────────────────────────────────────────────────────
-- Amounts produce the wallet_balance values above after the redemptions below.
INSERT INTO wallet_topups (merchant_id, amount, billplz_bill_id, payment_status, paid_at, created_at) VALUES
  (1, 250.00, 'BENT-TOP-1-001', 'paid', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 60 DAY)),
  (2, 500.00, 'BENT-TOP-2-001', 'paid', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
  (3, 200.00, 'BENT-TOP-3-001', 'paid', DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY)),
  (4, 100.00, 'BENT-TOP-4-001', 'paid', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (5, 300.00, 'BENT-TOP-5-001', 'paid', DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY)),
  (6, 130.00, 'BENT-TOP-6-001', 'paid', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (7, 600.00, 'BENT-TOP-7-001', 'paid', DATE_SUB(NOW(), INTERVAL  6 DAY), DATE_SUB(NOW(), INTERVAL  6 DAY)),
  (8, 115.00, 'BENT-TOP-8-001', 'paid', DATE_SUB(NOW(), INTERVAL 19 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY));

-- Mirror those topups in the wallet ledger.
INSERT INTO merchant_wallet_transactions (merchant_id, type, amount, balance_before, balance_after, description, reference_type, reference_id, created_at) VALUES
  (1, 'topup', 250.00, 0.00, 250.00, 'Wallet top-up · Billplz BENT-TOP-1-001', 'wallet_topup', 1, DATE_SUB(NOW(), INTERVAL 60 DAY)),
  (2, 'topup', 500.00, 0.00, 500.00, 'Wallet top-up · Billplz BENT-TOP-2-001', 'wallet_topup', 2, DATE_SUB(NOW(), INTERVAL 25 DAY)),
  (3, 'topup', 200.00, 0.00, 200.00, 'Wallet top-up · Billplz BENT-TOP-3-001', 'wallet_topup', 3, DATE_SUB(NOW(), INTERVAL 18 DAY)),
  (4, 'topup', 100.00, 0.00, 100.00, 'Wallet top-up · Billplz BENT-TOP-4-001', 'wallet_topup', 4, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (5, 'topup', 300.00, 0.00, 300.00, 'Wallet top-up · Billplz BENT-TOP-5-001', 'wallet_topup', 5, DATE_SUB(NOW(), INTERVAL 22 DAY)),
  (6, 'topup', 130.00, 0.00, 130.00, 'Wallet top-up · Billplz BENT-TOP-6-001', 'wallet_topup', 6, DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (7, 'topup', 600.00, 0.00, 600.00, 'Wallet top-up · Billplz BENT-TOP-7-001', 'wallet_topup', 7, DATE_SUB(NOW(), INTERVAL  6 DAY)),
  (8, 'topup', 115.00, 0.00, 115.00, 'Wallet top-up · Billplz BENT-TOP-8-001', 'wallet_topup', 8, DATE_SUB(NOW(), INTERVAL 19 DAY));

-- ─── Demo vouchers ────────────────────────────────────────────────────────
-- qr_token is a placeholder; the QR scanner won't decode these (HMAC fails),
-- but manual code entry uses voucher_code directly and works fine.

INSERT INTO vouchers
  (campaign_id, merchant_id, voucher_code, qr_token, customer_name, customer_phone, status, claimed_at, redeemed_at, expired_at) VALUES
  -- ── Campaign 1: Durian Trail · 5 redeemed at Restoran Lou Wong (merchant 1)
  (1, 1, 'SLV-DURI-2026-0001AA', 'demo-token-d-0001', 'Lim Wei Jie',    '+60112000001', 'redeemed', DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-DURI-2026-0002BB', 'demo-token-d-0002', 'Ahmad Faiz',     '+60112000002', 'redeemed', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-DURI-2026-0003CC', 'demo-token-d-0003', 'Nurul Hidayah',  '+60112000003', 'redeemed', DATE_SUB(NOW(), INTERVAL  5 DAY), DATE_SUB(NOW(), INTERVAL  5 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-DURI-2026-0004DD', 'demo-token-d-0004', 'Kumar Raj',      '+60112000004', 'redeemed', DATE_SUB(NOW(), INTERVAL  3 DAY), DATE_SUB(NOW(), INTERVAL  3 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-DURI-2026-0005EE', 'demo-token-d-0005', 'Faridah Mat',    '+60112000005', 'redeemed', DATE_SUB(NOW(), INTERVAL  1 DAY), DATE_SUB(NOW(), INTERVAL  1 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  -- Durian Trail · 3 still claimed
  (1, NULL, 'SLV-DURI-2026-1001FF', 'demo-token-d-1001', 'Tourist Sarah', '+60112100001', 'claimed', DATE_SUB(NOW(), INTERVAL  6 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, NULL, 'SLV-DURI-2026-1002GG', 'demo-token-d-1002', 'Tourist Brian', '+60112100002', 'claimed', DATE_SUB(NOW(), INTERVAL  3 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, NULL, 'SLV-DURI-2026-1003HH', 'demo-token-d-1003', 'Tourist Diana', '+60112100003', 'claimed', DATE_SUB(NOW(), INTERVAL  1 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  -- Durian Trail · 1 expired
  (1, NULL, 'SLV-DURI-2025-9001ZZ', 'demo-token-d-9001', 'Lapsed Visitor', '+60112900001', 'expired', DATE_SUB(NOW(), INTERVAL 95 DAY), NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),

  -- ── Campaign 2: Ginger Trail · 3 redeemed at Bentong Ginger Cafe (merchant 2)
  (2, 2, 'SLV-GING-2026-0001AA', 'demo-token-g-0001', 'Tan Mei Ling',   '+60112200001', 'redeemed', DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, 2, 'SLV-GING-2026-0002BB', 'demo-token-g-0002', 'Daniel Lee',     '+60112200002', 'redeemed', DATE_SUB(NOW(), INTERVAL  7 DAY), DATE_SUB(NOW(), INTERVAL  7 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, 2, 'SLV-GING-2026-0003CC', 'demo-token-g-0003', 'Wong Kah Hui',   '+60112200003', 'redeemed', DATE_SUB(NOW(), INTERVAL  2 DAY), DATE_SUB(NOW(), INTERVAL  2 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  -- Ginger · 1 still claimed
  (2, NULL, 'SLV-GING-2026-1001DD', 'demo-token-g-1001', 'Tourist Cheng', '+60112210001', 'claimed', DATE_SUB(NOW(), INTERVAL  4 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY)),

  -- ── Campaign 3: Coffee Walk · 1 redeemed at Hai Lam Kopitiam (merchant 3)
  (3, 3, 'SLV-COFF-2026-0001AA', 'demo-token-c-0001', 'Siti Nor',       '+60112300001', 'redeemed', DATE_SUB(NOW(), INTERVAL  9 DAY), DATE_SUB(NOW(), INTERVAL  9 DAY), DATE_ADD(CURDATE(), INTERVAL 45 DAY)),
  -- Coffee Walk · 2 still claimed
  (3, NULL, 'SLV-COFF-2026-1001BB', 'demo-token-c-1001', 'Heritage Tourist 1', '+60112310001', 'claimed', DATE_SUB(NOW(), INTERVAL  5 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 45 DAY)),
  (3, NULL, 'SLV-COFF-2026-1002CC', 'demo-token-c-1002', 'Heritage Tourist 2', '+60112310002', 'claimed', DATE_SUB(NOW(), INTERVAL  2 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 45 DAY)),

  -- ── Campaign 4: Chamang Waterfall · 1 redeemed at Chamang Adventures (merchant 4)
  (4, 4, 'SLV-CHAM-2026-0001AA', 'demo-token-w-0001', 'Jasmine Chong',  '+60112400001', 'redeemed', DATE_SUB(NOW(), INTERVAL  4 DAY), DATE_SUB(NOW(), INTERVAL  4 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  -- Chamang · 1 still claimed
  (4, NULL, 'SLV-CHAM-2026-1001BB', 'demo-token-w-1001', 'Tourist Eric', '+60112410001', 'claimed', DATE_SUB(NOW(), INTERVAL 30 MINUTE), NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY)),

  -- ── Campaign 5: Hot Spring · 3 at Suria (merchant 5), 2 at Mountain View (merchant 6)
  (5, 5, 'SLV-SPRG-2026-0001AA', 'demo-token-s-0001', 'Tan Hong Yi',    '+60112500001', 'redeemed', DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  (5, 5, 'SLV-SPRG-2026-0002BB', 'demo-token-s-0002', 'Mohd Iqbal',     '+60112500002', 'redeemed', DATE_SUB(NOW(), INTERVAL  8 DAY), DATE_SUB(NOW(), INTERVAL  8 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  (5, 5, 'SLV-SPRG-2026-0003CC', 'demo-token-s-0003', 'Goh Ming Han',   '+60112500003', 'redeemed', DATE_SUB(NOW(), INTERVAL  3 DAY), DATE_SUB(NOW(), INTERVAL  3 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  (5, 6, 'SLV-SPRG-2026-0004DD', 'demo-token-s-0004', 'Lee Su Ann',     '+60112500004', 'redeemed', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  (5, 6, 'SLV-SPRG-2026-0005EE', 'demo-token-s-0005', 'Priya Devi',     '+60112500005', 'redeemed', DATE_SUB(NOW(), INTERVAL  6 DAY), DATE_SUB(NOW(), INTERVAL  6 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  -- Hot Spring · 1 still claimed
  (5, NULL, 'SLV-SPRG-2026-1001FF', 'demo-token-s-1001', 'Sunset Tourist', '+60112510001', 'claimed', DATE_SUB(NOW(), INTERVAL  1 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 75 DAY));

-- ─── Demo redemptions ─────────────────────────────────────────────────────
-- Each merchant's running balance reconciles exactly to merchants.wallet_balance.
INSERT INTO redemptions (voucher_id, campaign_id, merchant_id, redemption_fee, wallet_balance_before, wallet_balance_after, redeemed_by, redeemed_at) VALUES
  -- Merchant 1 (Restoran Lou Wong) · 250.00 → 242.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-DURI-2026-0001AA'), 1, 1, 1.50, 250.00, 248.50, 3, DATE_SUB(NOW(), INTERVAL 12 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-DURI-2026-0002BB'), 1, 1, 1.50, 248.50, 247.00, 3, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-DURI-2026-0003CC'), 1, 1, 1.50, 247.00, 245.50, 3, DATE_SUB(NOW(), INTERVAL  5 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-DURI-2026-0004DD'), 1, 1, 1.50, 245.50, 244.00, 3, DATE_SUB(NOW(), INTERVAL  3 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-DURI-2026-0005EE'), 1, 1, 1.50, 244.00, 242.50, 3, DATE_SUB(NOW(), INTERVAL  1 DAY)),

  -- Merchant 2 (Bentong Ginger Cafe) · 500.00 → 495.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-GING-2026-0001AA'), 2, 2, 1.50, 500.00, 498.50, 4, DATE_SUB(NOW(), INTERVAL 11 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-GING-2026-0002BB'), 2, 2, 1.50, 498.50, 497.00, 4, DATE_SUB(NOW(), INTERVAL  7 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-GING-2026-0003CC'), 2, 2, 1.50, 497.00, 495.50, 4, DATE_SUB(NOW(), INTERVAL  2 DAY)),

  -- Merchant 3 (Hai Lam Kopitiam) · 200.00 → 198.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-COFF-2026-0001AA'), 3, 3, 1.50, 200.00, 198.50, 5, DATE_SUB(NOW(), INTERVAL  9 DAY)),

  -- Merchant 4 (Chamang Waterfall Adventures) · 100.00 → 98.50  (DISABLED tier)
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-CHAM-2026-0001AA'), 4, 4, 1.50, 100.00,  98.50, 6, DATE_SUB(NOW(), INTERVAL  4 DAY)),

  -- Merchant 5 (Suria Hot Spring) · 300.00 → 295.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SPRG-2026-0001AA'), 5, 5, 1.50, 300.00, 298.50, 7, DATE_SUB(NOW(), INTERVAL 13 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SPRG-2026-0002BB'), 5, 5, 1.50, 298.50, 297.00, 7, DATE_SUB(NOW(), INTERVAL  8 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SPRG-2026-0003CC'), 5, 5, 1.50, 297.00, 295.50, 7, DATE_SUB(NOW(), INTERVAL  3 DAY)),

  -- Merchant 6 (Mountain View Homestay) · 130.00 → 127.00  (WARNING tier)
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SPRG-2026-0004DD'), 5, 6, 1.50, 130.00, 128.50, 8, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-SPRG-2026-0005EE'), 5, 6, 1.50, 128.50, 127.00, 8, DATE_SUB(NOW(), INTERVAL  6 DAY));

-- Mirror redemption fees in the wallet ledger (each merchant's chain matches above).
INSERT INTO merchant_wallet_transactions (merchant_id, type, amount, balance_before, balance_after, description, reference_type, reference_id, created_at) VALUES
  (1, 'redemption', -1.50, 250.00, 248.50, 'Voucher SLV-DURI-2026-0001AA', 'redemption',  1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
  (1, 'redemption', -1.50, 248.50, 247.00, 'Voucher SLV-DURI-2026-0002BB', 'redemption',  2, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (1, 'redemption', -1.50, 247.00, 245.50, 'Voucher SLV-DURI-2026-0003CC', 'redemption',  3, DATE_SUB(NOW(), INTERVAL  5 DAY)),
  (1, 'redemption', -1.50, 245.50, 244.00, 'Voucher SLV-DURI-2026-0004DD', 'redemption',  4, DATE_SUB(NOW(), INTERVAL  3 DAY)),
  (1, 'redemption', -1.50, 244.00, 242.50, 'Voucher SLV-DURI-2026-0005EE', 'redemption',  5, DATE_SUB(NOW(), INTERVAL  1 DAY)),

  (2, 'redemption', -1.50, 500.00, 498.50, 'Voucher SLV-GING-2026-0001AA', 'redemption',  6, DATE_SUB(NOW(), INTERVAL 11 DAY)),
  (2, 'redemption', -1.50, 498.50, 497.00, 'Voucher SLV-GING-2026-0002BB', 'redemption',  7, DATE_SUB(NOW(), INTERVAL  7 DAY)),
  (2, 'redemption', -1.50, 497.00, 495.50, 'Voucher SLV-GING-2026-0003CC', 'redemption',  8, DATE_SUB(NOW(), INTERVAL  2 DAY)),

  (3, 'redemption', -1.50, 200.00, 198.50, 'Voucher SLV-COFF-2026-0001AA', 'redemption',  9, DATE_SUB(NOW(), INTERVAL  9 DAY)),

  (4, 'redemption', -1.50, 100.00,  98.50, 'Voucher SLV-CHAM-2026-0001AA', 'redemption', 10, DATE_SUB(NOW(), INTERVAL  4 DAY)),

  (5, 'redemption', -1.50, 300.00, 298.50, 'Voucher SLV-SPRG-2026-0001AA', 'redemption', 11, DATE_SUB(NOW(), INTERVAL 13 DAY)),
  (5, 'redemption', -1.50, 298.50, 297.00, 'Voucher SLV-SPRG-2026-0002BB', 'redemption', 12, DATE_SUB(NOW(), INTERVAL  8 DAY)),
  (5, 'redemption', -1.50, 297.00, 295.50, 'Voucher SLV-SPRG-2026-0003CC', 'redemption', 13, DATE_SUB(NOW(), INTERVAL  3 DAY)),

  (6, 'redemption', -1.50, 130.00, 128.50, 'Voucher SLV-SPRG-2026-0004DD', 'redemption', 14, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (6, 'redemption', -1.50, 128.50, 127.00, 'Voucher SLV-SPRG-2026-0005EE', 'redemption', 15, DATE_SUB(NOW(), INTERVAL  6 DAY));
