-- SLV Voucher Goodie Platform — seed data (Terengganu focus).
--
-- The whole demo is set in Terengganu: five tourism zones, eight
-- Terengganu-based merchants spanning all wallet status tiers
-- (healthy / warning / critical / disabled), five flagship Terengganu
-- tourism campaigns (island hopping, nasi dagang, craft trail,
-- Chinatown walk, Tasik Kenyir), and 24 vouchers with a 15-row
-- redemption history reconciled to every merchant's wallet ledger.
--
-- Login accounts (password = merchant123 for all merchants):
--   admin@slvgroup.my            → admin123
--   gov@terengganu.gov.my        → gov123
--   merchant@demo.my             → merchant123   (Nasi Dagang Mak Timah)
--   batik@demo.my                → merchant123   (Terengganu Batik House)
--   keropoklekor@demo.my         → merchant123   (Keropok Lekor Losong)
--   pasarpayang@demo.my          → merchant123   (Pasar Payang Craft Shop)
--   kenyir@demo.my               → merchant123   (Kenyir Lake Houseboat)
--   kristal@demo.my              → merchant123   (Masjid Kristal Visitor Center)
--   perhentian@demo.my           → merchant123   (Perhentian Dive Center)
--   redang@demo.my               → merchant123   (Redang Island Resort)

SET NAMES utf8mb4;

-- ─── Users ────────────────────────────────────────────────────────────────
INSERT INTO users (name, email, phone, password, role, status) VALUES
  ('SLV Super Admin', 'admin@slvgroup.my', '+60123456789',
   '$2y$10$ttSp6NObYRbiSq01.pI/DOMHanbiEwHFFiEngWty4H9FulAbEg33W', 'admin', 'active'),
  ('Terengganu Tourism', 'gov@terengganu.gov.my', '+60111111111',
   '$2y$10$aY8x0z57HmtN/9zh4WONseRIueh8G2hF5MWccM5t797yusnWTJKGO', 'gov', 'active'),
  ('Mak Timah (Nasi Dagang)', 'merchant@demo.my', '+60198765432',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active');

-- ─── Primary location (Kuala Terengganu Heritage) ─────────────────────────
INSERT INTO locations (state, city, area_name, slug, description, banner_image, map_link, status) VALUES
  ('Terengganu', 'Kuala Terengganu', 'Kuala Terengganu Heritage Core', 'kuala-terengganu-heritage',
   'The riverside capital of Terengganu — Pasar Payang traditional market, Chinatown shophouses on Jalan Kampung Cina, Istana Maziah palace grounds, and the famous Masjid Kristal glittering across Pulau Wan Man.',
   NULL, 'https://maps.google.com/?q=Kuala+Terengganu,Malaysia', 'active');

-- ─── Main demo merchant (id 1) ────────────────────────────────────────────
INSERT INTO merchants (
  user_id, business_name, owner_name, phone, whatsapp, email, category, location_id,
  address, map_link, registration_no, operating_hours, wallet_balance, subscription_status, status
) VALUES (
  3, 'Nasi Dagang Mak Timah', 'Mak Timah',
  '+60198765432', '+60198765432', 'merchant@demo.my', 'fnb', 1,
  'Jalan Bandar, Pasar Payang, 20000 Kuala Terengganu',
  'https://maps.google.com/?q=Nasi+Dagang+Mak+Timah',
  'SSM-202301-0088', '06:00 - 11:00 daily', 242.50, 'active', 'active'
);

INSERT INTO merchant_subscriptions (merchant_id, annual_fee, start_date, expiry_date, payment_status, status) VALUES
  (1, 150.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 'paid', 'active');

-- ─── Five flagship Terengganu tourism campaigns ───────────────────────────
INSERT INTO campaigns (location_id, campaign_name, slug, description, voucher_type, voucher_value, start_date, end_date, claim_limit, terms, status) VALUES
  (1, 'Terengganu Nasi Dagang Food Trail',  'terengganu-nasi-dagang-trail',
   'Claim a RM15 cash voucher to taste authentic Terengganu nasi dagang — fragrant glutinous rice with coconut, served alongside tuna gulai — at participating stalls across Kuala Terengganu and Marang.',
   'cash', 15.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 500,
   'One voucher per phone number. Minimum spend RM30 to redeem. Valid at participating nasi dagang stalls displaying the SLV badge. Serves breakfast hours only.',
   'active'),
  (1, 'Pasar Payang Batik Shopping Voucher', 'pasar-payang-batik-voucher',
   'RM5 off any purchase of handmade Terengganu batik, songket or kain tenun at participating Pasar Payang craft shops. Support local artisans and bring home a piece of east coast heritage.',
   'discount', 5.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 300,
   'Single use per voucher. Cannot be combined with other promotions. Valid at participating Pasar Payang craft merchants only.',
   'active'),
  (1, 'Kuala Terengganu Chinatown Heritage Walk', 'kt-chinatown-heritage-walk',
   'Buy 1 Free 1 traditional Hainanese kopi with egg + kaya toast at participating Chinatown kopitiams along Jalan Kampung Cina. Experience 180 years of Terengganu Peranakan heritage.',
   'b1f1', 8.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 45 DAY), 200,
   'Buy one regular Hainanese coffee set, get a second free. Dine-in only. One voucher per customer per visit. Valid weekdays 8am - 5pm.',
   'active'),
  (1, 'Pulau Redang Snorkel & Island Pass', 'pulau-redang-snorkel-pass',
   'Free guided snorkelling trip to the Marine Park at Pulau Redang plus complimentary lunch at the island beach cafe. Experience Terengganu''s world-class clear waters.',
   'experience', 25.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 100,
   'Valid during Terengganu dive season (Mar-Oct). Depart 9am from Shahbandar Jetty. Life jacket + snorkel gear provided. Children below 12 must be accompanied.',
   'active'),
  (1, 'Masjid Kristal + Islamic Civilisation Park Pass', 'masjid-kristal-islamic-park',
   'RM10 off combined entry to Masjid Kristal and the Islamic Civilisation Park. Marvel at the shimmering crystal mosque at sunset and tour 21 miniature mosques from around the world.',
   'discount', 10.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 75 DAY), 200,
   'Valid daily 9am - 10pm (mosque illuminated from dusk). Dress code: modest attire; shawls provided at entrance. One voucher per adult.',
   'active');

-- Main merchant participates in the Nasi Dagang Food Trail campaign
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
-- DEMO DATA — extra Terengganu merchants, vouchers, redemptions, wallet
-- activity. All extra merchant accounts use the password "merchant123".
-- ─────────────────────────────────────────────────────────────────────────

-- Extra merchant users (id 4–10)
INSERT INTO users (name, email, phone, password, role, status) VALUES
  ('Cik Rohana (Batik House)',          'batik@demo.my',        '+60198811001',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Pak Mat (Keropok Lekor Losong)',    'keropoklekor@demo.my', '+60198811002',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Puan Siti (Pasar Payang)',          'pasarpayang@demo.my',  '+60198811003',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Pak Long (Kenyir Houseboat)',       'kenyir@demo.my',       '+60198811004',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Ustaz Ariff (Masjid Kristal Centre)', 'kristal@demo.my',    '+60198811005',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Captain Hassan (Perhentian Dive)',  'perhentian@demo.my',   '+60198811006',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active'),
  ('Mr Lim (Redang Resort)',            'redang@demo.my',       '+60198811007',
   '$2y$10$cs4VmAR93Agh/sTUhTyrwO478zvFnZMtWqnWV9SNlPFO1egHn2ElS', 'merchant', 'active');

-- Extra merchants (id 2–8). Wallet balances chosen to demonstrate every
-- wallet status tier defined in App\Models\Merchant::walletStatus():
--   healthy ≥ 150 · warning < 150 · critical < 120 · disabled < 100
--
-- Tier coverage:
--   healthy   : 1 (242.50), 2 (495.50), 3 (198.50), 5 (295.50), 7 (600.00)
--   warning   : 6 (127.00)  ← Masjid Kristal Visitor Center
--   critical  : 8 (115.00)  ← Redang Island Resort
--   disabled  : 4 ( 98.50)  ← Pasar Payang Craft Shop
INSERT INTO merchants (
  user_id, business_name, owner_name, phone, whatsapp, email, category, location_id,
  address, map_link, registration_no, operating_hours, wallet_balance, subscription_status, status
) VALUES
  (4, 'Terengganu Batik House',          'Cik Rohana',       '+60198811001', '+60198811001', 'batik@demo.my',         'souvenir',   1, 'Lot 42, Jalan Sultan Zainal Abidin, Kuala Terengganu',        'https://maps.google.com/?q=Terengganu+Batik+House',          'SSM-202311-0012', '09:00 - 18:00 daily',           495.50, 'active', 'active'),
  (5, 'Keropok Lekor Losong',            'Pak Mat',          '+60198811002', '+60198811002', 'keropoklekor@demo.my',  'fnb',        1, 'Kampung Losong Haji Mat Nor, 20300 Kuala Terengganu',         'https://maps.google.com/?q=Losong+Keropok+Lekor',            'SSM-202310-0003', '07:00 - 19:00 daily',           198.50, 'active', 'active'),
  (6, 'Pasar Payang Craft Shop',         'Puan Siti',        '+60198811003', '+60198811003', 'pasarpayang@demo.my',   'souvenir',   1, 'Lot 14, Pasar Payang, Jalan Sultan Zainal Abidin',           'https://maps.google.com/?q=Pasar+Payang+Craft',              'SSM-202312-0045', '08:00 - 18:00 daily',            98.50, 'active', 'active'),
  (7, 'Kenyir Lake Houseboat',           'Pak Long',         '+60198811004', '+60198811004', 'kenyir@demo.my',        'experience', 1, 'Pengkalan Gawi Jetty, Tasik Kenyir, 21700 Kuala Berang',     'https://maps.google.com/?q=Tasik+Kenyir+Houseboat',          'SSM-202309-0099', 'Check-in Fri 2pm',              295.50, 'active', 'active'),
  (8, 'Masjid Kristal Visitor Center',   'Ustaz Ariff',      '+60198811005', '+60198811005', 'kristal@demo.my',       'attraction', 1, 'Pulau Wan Man, 21300 Kuala Terengganu',                       'https://maps.google.com/?q=Masjid+Kristal+Terengganu',       'SSM-202308-0021', '09:00 - 22:00 daily',           127.00, 'active', 'active'),
  (9, 'Perhentian Dive Center',          'Captain Hassan',   '+60198811006', '+60198811006', 'perhentian@demo.my',    'experience', 1, 'Long Beach, Pulau Perhentian Kecil, 22300 Besut',             'https://maps.google.com/?q=Perhentian+Dive',                 'SSM-202306-0077', 'Dive season Mar - Oct',         600.00, 'active', 'active'),
  (10,'Redang Island Resort',            'Mr Lim',           '+60198811007', '+60198811007', 'redang@demo.my',        'hotel',      1, 'Teluk Dalam, Pulau Redang, 21090 Kuala Terengganu',           'https://maps.google.com/?q=Pulau+Redang+Resort',             'SSM-202307-0034', 'Check-in 2pm / out 11am',       115.00, 'active', 'active');

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
-- Campaign 1 (Nasi Dagang Trail) : Mak Timah (1) + Keropok Lekor Losong (3)
-- Campaign 2 (Batik Shopping)    : Terengganu Batik House (2) + Pasar Payang (4)
-- Campaign 3 (Chinatown Walk)    : Keropok Lekor Losong (3)
-- Campaign 4 (Redang Snorkel)    : Perhentian Dive (7) + Redang Resort (8)
-- Campaign 5 (Masjid Kristal)    : Masjid Kristal Visitor Center (6)
INSERT INTO campaign_merchants (campaign_id, merchant_id, status) VALUES
  (1, 3, 'active'),
  (2, 2, 'active'),
  (2, 4, 'active'),
  (3, 3, 'active'),
  (4, 5, 'active'),
  (4, 7, 'active'),
  (4, 8, 'active'),
  (5, 6, 'active');

-- ─── Wallet topups ────────────────────────────────────────────────────────
-- Amounts produce the wallet_balance values above after the redemptions below.
INSERT INTO wallet_topups (merchant_id, amount, billplz_bill_id, payment_status, paid_at, created_at) VALUES
  (1, 250.00, 'TRG-TOP-1-001', 'paid', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 60 DAY)),
  (2, 500.00, 'TRG-TOP-2-001', 'paid', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
  (3, 200.00, 'TRG-TOP-3-001', 'paid', DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY)),
  (4, 100.00, 'TRG-TOP-4-001', 'paid', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (5, 300.00, 'TRG-TOP-5-001', 'paid', DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY)),
  (6, 130.00, 'TRG-TOP-6-001', 'paid', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (7, 600.00, 'TRG-TOP-7-001', 'paid', DATE_SUB(NOW(), INTERVAL  6 DAY), DATE_SUB(NOW(), INTERVAL  6 DAY)),
  (8, 115.00, 'TRG-TOP-8-001', 'paid', DATE_SUB(NOW(), INTERVAL 19 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY));

-- Mirror those topups in the wallet ledger.
INSERT INTO merchant_wallet_transactions (merchant_id, type, amount, balance_before, balance_after, description, reference_type, reference_id, created_at) VALUES
  (1, 'topup', 250.00, 0.00, 250.00, 'Wallet top-up · Billplz TRG-TOP-1-001', 'wallet_topup', 1, DATE_SUB(NOW(), INTERVAL 60 DAY)),
  (2, 'topup', 500.00, 0.00, 500.00, 'Wallet top-up · Billplz TRG-TOP-2-001', 'wallet_topup', 2, DATE_SUB(NOW(), INTERVAL 25 DAY)),
  (3, 'topup', 200.00, 0.00, 200.00, 'Wallet top-up · Billplz TRG-TOP-3-001', 'wallet_topup', 3, DATE_SUB(NOW(), INTERVAL 18 DAY)),
  (4, 'topup', 100.00, 0.00, 100.00, 'Wallet top-up · Billplz TRG-TOP-4-001', 'wallet_topup', 4, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (5, 'topup', 300.00, 0.00, 300.00, 'Wallet top-up · Billplz TRG-TOP-5-001', 'wallet_topup', 5, DATE_SUB(NOW(), INTERVAL 22 DAY)),
  (6, 'topup', 130.00, 0.00, 130.00, 'Wallet top-up · Billplz TRG-TOP-6-001', 'wallet_topup', 6, DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (7, 'topup', 600.00, 0.00, 600.00, 'Wallet top-up · Billplz TRG-TOP-7-001', 'wallet_topup', 7, DATE_SUB(NOW(), INTERVAL  6 DAY)),
  (8, 'topup', 115.00, 0.00, 115.00, 'Wallet top-up · Billplz TRG-TOP-8-001', 'wallet_topup', 8, DATE_SUB(NOW(), INTERVAL 19 DAY));

-- ─── Demo vouchers ────────────────────────────────────────────────────────
INSERT INTO vouchers
  (campaign_id, merchant_id, voucher_code, qr_token, customer_name, customer_phone, status, claimed_at, redeemed_at, expired_at) VALUES
  -- Campaign 1 · Nasi Dagang Trail · 5 redeemed at Mak Timah (merchant 1)
  (1, 1, 'SLV-NASI-2026-0001AA', 'demo-token-n-0001', 'Lim Wei Jie',    '+60112000001', 'redeemed', DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-NASI-2026-0002BB', 'demo-token-n-0002', 'Ahmad Faiz',     '+60112000002', 'redeemed', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-NASI-2026-0003CC', 'demo-token-n-0003', 'Nurul Hidayah',  '+60112000003', 'redeemed', DATE_SUB(NOW(), INTERVAL  5 DAY), DATE_SUB(NOW(), INTERVAL  5 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-NASI-2026-0004DD', 'demo-token-n-0004', 'Kumar Raj',      '+60112000004', 'redeemed', DATE_SUB(NOW(), INTERVAL  3 DAY), DATE_SUB(NOW(), INTERVAL  3 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, 1, 'SLV-NASI-2026-0005EE', 'demo-token-n-0005', 'Faridah Mat',    '+60112000005', 'redeemed', DATE_SUB(NOW(), INTERVAL  1 DAY), DATE_SUB(NOW(), INTERVAL  1 DAY), DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  -- Nasi Dagang · 3 still claimed
  (1, NULL, 'SLV-NASI-2026-1001FF', 'demo-token-n-1001', 'Tourist Sarah', '+60112100001', 'claimed', DATE_SUB(NOW(), INTERVAL  6 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, NULL, 'SLV-NASI-2026-1002GG', 'demo-token-n-1002', 'Tourist Brian', '+60112100002', 'claimed', DATE_SUB(NOW(), INTERVAL  3 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  (1, NULL, 'SLV-NASI-2026-1003HH', 'demo-token-n-1003', 'Tourist Diana', '+60112100003', 'claimed', DATE_SUB(NOW(), INTERVAL  1 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
  -- Nasi Dagang · 1 expired
  (1, NULL, 'SLV-NASI-2025-9001ZZ', 'demo-token-n-9001', 'Lapsed Visitor', '+60112900001', 'expired', DATE_SUB(NOW(), INTERVAL 95 DAY), NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),

  -- Campaign 2 · Batik Voucher · 3 redeemed at Terengganu Batik House (merchant 2)
  (2, 2, 'SLV-BATK-2026-0001AA', 'demo-token-b-0001', 'Tan Mei Ling',   '+60112200001', 'redeemed', DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, 2, 'SLV-BATK-2026-0002BB', 'demo-token-b-0002', 'Daniel Lee',     '+60112200002', 'redeemed', DATE_SUB(NOW(), INTERVAL  7 DAY), DATE_SUB(NOW(), INTERVAL  7 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  (2, 2, 'SLV-BATK-2026-0003CC', 'demo-token-b-0003', 'Wong Kah Hui',   '+60112200003', 'redeemed', DATE_SUB(NOW(), INTERVAL  2 DAY), DATE_SUB(NOW(), INTERVAL  2 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  -- Batik · 1 still claimed
  (2, NULL, 'SLV-BATK-2026-1001DD', 'demo-token-b-1001', 'Tourist Cheng', '+60112210001', 'claimed', DATE_SUB(NOW(), INTERVAL  4 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY)),

  -- Campaign 3 · Chinatown Walk · 1 redeemed at Keropok Lekor Losong (merchant 3)
  (3, 3, 'SLV-TOWN-2026-0001AA', 'demo-token-t-0001', 'Siti Nor',       '+60112300001', 'redeemed', DATE_SUB(NOW(), INTERVAL  9 DAY), DATE_SUB(NOW(), INTERVAL  9 DAY), DATE_ADD(CURDATE(), INTERVAL 45 DAY)),
  -- Chinatown Walk · 2 still claimed
  (3, NULL, 'SLV-TOWN-2026-1001BB', 'demo-token-t-1001', 'Heritage Tourist 1', '+60112310001', 'claimed', DATE_SUB(NOW(), INTERVAL  5 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 45 DAY)),
  (3, NULL, 'SLV-TOWN-2026-1002CC', 'demo-token-t-1002', 'Heritage Tourist 2', '+60112310002', 'claimed', DATE_SUB(NOW(), INTERVAL  2 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 45 DAY)),

  -- Campaign 4 · Redang Snorkel · 1 redeemed at Pasar Payang (merchant 4) — e.g. buy souvenirs after dive
  (4, 4, 'SLV-REDG-2026-0001AA', 'demo-token-r-0001', 'Jasmine Chong',  '+60112400001', 'redeemed', DATE_SUB(NOW(), INTERVAL  4 DAY), DATE_SUB(NOW(), INTERVAL  4 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY)),
  -- Redang Snorkel · 1 still claimed
  (4, NULL, 'SLV-REDG-2026-1001BB', 'demo-token-r-1001', 'Tourist Eric', '+60112410001', 'claimed', DATE_SUB(NOW(), INTERVAL 30 MINUTE), NULL, DATE_ADD(CURDATE(), INTERVAL 60 DAY)),

  -- Campaign 5 · Masjid Kristal · 3 at Kenyir Houseboat (merchant 5), 2 at Masjid Kristal Centre (merchant 6)
  (5, 5, 'SLV-KRYS-2026-0001AA', 'demo-token-k-0001', 'Tan Hong Yi',    '+60112500001', 'redeemed', DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  (5, 5, 'SLV-KRYS-2026-0002BB', 'demo-token-k-0002', 'Mohd Iqbal',     '+60112500002', 'redeemed', DATE_SUB(NOW(), INTERVAL  8 DAY), DATE_SUB(NOW(), INTERVAL  8 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  (5, 5, 'SLV-KRYS-2026-0003CC', 'demo-token-k-0003', 'Goh Ming Han',   '+60112500003', 'redeemed', DATE_SUB(NOW(), INTERVAL  3 DAY), DATE_SUB(NOW(), INTERVAL  3 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  (5, 6, 'SLV-KRYS-2026-0004DD', 'demo-token-k-0004', 'Lee Su Ann',     '+60112500004', 'redeemed', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  (5, 6, 'SLV-KRYS-2026-0005EE', 'demo-token-k-0005', 'Priya Devi',     '+60112500005', 'redeemed', DATE_SUB(NOW(), INTERVAL  6 DAY), DATE_SUB(NOW(), INTERVAL  6 DAY), DATE_ADD(CURDATE(), INTERVAL 75 DAY)),
  -- Masjid Kristal · 1 still claimed
  (5, NULL, 'SLV-KRYS-2026-1001FF', 'demo-token-k-1001', 'Sunset Tourist', '+60112510001', 'claimed', DATE_SUB(NOW(), INTERVAL  1 HOUR), NULL, DATE_ADD(CURDATE(), INTERVAL 75 DAY));

-- ─── Demo redemptions ─────────────────────────────────────────────────────
-- Each merchant's running balance reconciles exactly to merchants.wallet_balance.
INSERT INTO redemptions (voucher_id, campaign_id, merchant_id, redemption_fee, wallet_balance_before, wallet_balance_after, redeemed_by, redeemed_at) VALUES
  -- Merchant 1 (Nasi Dagang Mak Timah) · 250.00 → 242.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-NASI-2026-0001AA'), 1, 1, 1.50, 250.00, 248.50, 3, DATE_SUB(NOW(), INTERVAL 12 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-NASI-2026-0002BB'), 1, 1, 1.50, 248.50, 247.00, 3, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-NASI-2026-0003CC'), 1, 1, 1.50, 247.00, 245.50, 3, DATE_SUB(NOW(), INTERVAL  5 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-NASI-2026-0004DD'), 1, 1, 1.50, 245.50, 244.00, 3, DATE_SUB(NOW(), INTERVAL  3 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-NASI-2026-0005EE'), 1, 1, 1.50, 244.00, 242.50, 3, DATE_SUB(NOW(), INTERVAL  1 DAY)),

  -- Merchant 2 (Terengganu Batik House) · 500.00 → 495.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-BATK-2026-0001AA'), 2, 2, 1.50, 500.00, 498.50, 4, DATE_SUB(NOW(), INTERVAL 11 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-BATK-2026-0002BB'), 2, 2, 1.50, 498.50, 497.00, 4, DATE_SUB(NOW(), INTERVAL  7 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-BATK-2026-0003CC'), 2, 2, 1.50, 497.00, 495.50, 4, DATE_SUB(NOW(), INTERVAL  2 DAY)),

  -- Merchant 3 (Keropok Lekor Losong) · 200.00 → 198.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-TOWN-2026-0001AA'), 3, 3, 1.50, 200.00, 198.50, 5, DATE_SUB(NOW(), INTERVAL  9 DAY)),

  -- Merchant 4 (Pasar Payang Craft Shop) · 100.00 → 98.50  (DISABLED tier)
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-REDG-2026-0001AA'), 4, 4, 1.50, 100.00,  98.50, 6, DATE_SUB(NOW(), INTERVAL  4 DAY)),

  -- Merchant 5 (Kenyir Lake Houseboat) · 300.00 → 295.50
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-KRYS-2026-0001AA'), 5, 5, 1.50, 300.00, 298.50, 7, DATE_SUB(NOW(), INTERVAL 13 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-KRYS-2026-0002BB'), 5, 5, 1.50, 298.50, 297.00, 7, DATE_SUB(NOW(), INTERVAL  8 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-KRYS-2026-0003CC'), 5, 5, 1.50, 297.00, 295.50, 7, DATE_SUB(NOW(), INTERVAL  3 DAY)),

  -- Merchant 6 (Masjid Kristal Visitor Center) · 130.00 → 127.00  (WARNING tier)
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-KRYS-2026-0004DD'), 5, 6, 1.50, 130.00, 128.50, 8, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ((SELECT id FROM vouchers WHERE voucher_code='SLV-KRYS-2026-0005EE'), 5, 6, 1.50, 128.50, 127.00, 8, DATE_SUB(NOW(), INTERVAL  6 DAY));

-- Mirror redemption fees in the wallet ledger
INSERT INTO merchant_wallet_transactions (merchant_id, type, amount, balance_before, balance_after, description, reference_type, reference_id, created_at) VALUES
  (1, 'redemption', -1.50, 250.00, 248.50, 'Voucher SLV-NASI-2026-0001AA', 'redemption',  1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
  (1, 'redemption', -1.50, 248.50, 247.00, 'Voucher SLV-NASI-2026-0002BB', 'redemption',  2, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (1, 'redemption', -1.50, 247.00, 245.50, 'Voucher SLV-NASI-2026-0003CC', 'redemption',  3, DATE_SUB(NOW(), INTERVAL  5 DAY)),
  (1, 'redemption', -1.50, 245.50, 244.00, 'Voucher SLV-NASI-2026-0004DD', 'redemption',  4, DATE_SUB(NOW(), INTERVAL  3 DAY)),
  (1, 'redemption', -1.50, 244.00, 242.50, 'Voucher SLV-NASI-2026-0005EE', 'redemption',  5, DATE_SUB(NOW(), INTERVAL  1 DAY)),

  (2, 'redemption', -1.50, 500.00, 498.50, 'Voucher SLV-BATK-2026-0001AA', 'redemption',  6, DATE_SUB(NOW(), INTERVAL 11 DAY)),
  (2, 'redemption', -1.50, 498.50, 497.00, 'Voucher SLV-BATK-2026-0002BB', 'redemption',  7, DATE_SUB(NOW(), INTERVAL  7 DAY)),
  (2, 'redemption', -1.50, 497.00, 495.50, 'Voucher SLV-BATK-2026-0003CC', 'redemption',  8, DATE_SUB(NOW(), INTERVAL  2 DAY)),

  (3, 'redemption', -1.50, 200.00, 198.50, 'Voucher SLV-TOWN-2026-0001AA', 'redemption',  9, DATE_SUB(NOW(), INTERVAL  9 DAY)),

  (4, 'redemption', -1.50, 100.00,  98.50, 'Voucher SLV-REDG-2026-0001AA', 'redemption', 10, DATE_SUB(NOW(), INTERVAL  4 DAY)),

  (5, 'redemption', -1.50, 300.00, 298.50, 'Voucher SLV-KRYS-2026-0001AA', 'redemption', 11, DATE_SUB(NOW(), INTERVAL 13 DAY)),
  (5, 'redemption', -1.50, 298.50, 297.00, 'Voucher SLV-KRYS-2026-0002BB', 'redemption', 12, DATE_SUB(NOW(), INTERVAL  8 DAY)),
  (5, 'redemption', -1.50, 297.00, 295.50, 'Voucher SLV-KRYS-2026-0003CC', 'redemption', 13, DATE_SUB(NOW(), INTERVAL  3 DAY)),

  (6, 'redemption', -1.50, 130.00, 128.50, 'Voucher SLV-KRYS-2026-0004DD', 'redemption', 14, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (6, 'redemption', -1.50, 128.50, 127.00, 'Voucher SLV-KRYS-2026-0005EE', 'redemption', 15, DATE_SUB(NOW(), INTERVAL  6 DAY));

-- ─────────────────────────────────────────────────────────────────────────
-- ADDITIONAL TERENGGANU TRAVEL ZONES & CAMPAIGNS
-- 4 sub-zones (location_id 2–5) + 8 more campaigns (id 6–13) covering the
-- full breadth of Terengganu tourism — Pulau Redang / Perhentian island
-- cluster, Marang & Rantau Abang coast, Tasik Kenyir Highlands, and the
-- Craft Trail (Pulau Duyong / Terengganu songket & kain tenun studios).
-- ─────────────────────────────────────────────────────────────────────────

INSERT INTO locations (state, city, area_name, slug, description, banner_image, map_link, status) VALUES
  ('Terengganu', 'Besut', 'Pulau Redang & Perhentian Island Cluster', 'redang-perhentian',
   'Crystal-clear turquoise waters, pristine white-sand beaches, world-class snorkelling and diving — the islands off the Terengganu coast rank among Southeast Asia''s finest marine destinations.',
   NULL, 'https://maps.google.com/?q=Pulau+Redang,Terengganu', 'active'),
  ('Terengganu', 'Marang', 'Marang & Rantau Abang Coastal Strip', 'marang-rantau-abang',
   'The nasi dagang capital, fishing kampungs, pristine beaches, Rantau Abang''s historic leatherback turtle sanctuary, and roadside keropok lekor stalls that stretch all the way down the east coast.',
   NULL, 'https://maps.google.com/?q=Marang,Terengganu', 'active'),
  ('Terengganu', 'Hulu Terengganu', 'Tasik Kenyir Highlands', 'tasik-kenyir',
   'Southeast Asia''s largest man-made lake — houseboats, Kelah fish sanctuaries, Lasir and Saok waterfalls, jungle trails, and the Kenyir Elephant Conservation Village.',
   NULL, 'https://maps.google.com/?q=Tasik+Kenyir,Terengganu', 'active'),
  ('Terengganu', 'Kuala Terengganu', 'Terengganu Craft Trail (Pulau Duyong & Studios)', 'terengganu-craft-trail',
   'The Pulau Duyong traditional shipbuilding village, Terengganu songket weaving studios, batik tulis workshops, and the Terengganu State Museum complex.',
   NULL, 'https://maps.google.com/?q=Pulau+Duyong,Kuala+Terengganu', 'active');

-- 8 additional Terengganu tourism campaigns (id 6–13)
INSERT INTO campaigns (location_id, campaign_name, slug, description, voucher_type, voucher_value, start_date, end_date, claim_limit, terms, status) VALUES
  -- Campaign 6 · Pulau Redang / Perhentian
  (2, 'Pulau Perhentian Scuba Discovery Dive', 'perhentian-scuba-discovery',
   'RM20 off a Discover Scuba Diving session with certified PADI instructors at Pulau Perhentian. Try scuba for the first time among turtles, reef sharks, and vibrant coral gardens.',
   'discount', 20.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 150,
   'Season Mar - Oct only. Minimum age 10. Health declaration required. One voucher per diver.',
   'active'),

  -- Campaign 7 · Pulau Redang / Perhentian
  (2, 'Lang Tengah Sunset Beach Barbecue', 'lang-tengah-bbq',
   'Free beach barbecue dinner (grilled fish, satar, sata, and tropical fruit platter) at a participating Lang Tengah resort — the quieter sibling of Redang.',
   'free_gift', 25.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 75 DAY), 80,
   'Valid Mondays - Thursdays only. Guests must be staying at participating resorts. One voucher per booking.',
   'active'),

  -- Campaign 8 · Marang & Rantau Abang
  (3, 'Marang Keropok Lekor Trail', 'marang-keropok-lekor-trail',
   'RM5 off a keropok lekor sampler platter (fried + boiled varieties with homemade chili sauce) at any three participating stalls along the Marang coastal road.',
   'discount', 5.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 300,
   'Visit any three badged stalls in a single day. One voucher per customer. Takeaway permitted.',
   'active'),

  -- Campaign 9 · Marang & Rantau Abang
  (3, 'Rantau Abang Turtle Conservation Visit', 'rantau-abang-turtle-visit',
   'Free guided visit to the Rantau Abang Turtle Information Centre and nearby nesting beach monitoring hut. Learn about Terengganu''s turtle conservation legacy and ongoing programmes.',
   'tourism', 10.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 100,
   'Guided visits Sat-Sun only. Children welcome. No flash photography. No touching of turtles or eggs.',
   'active'),

  -- Campaign 10 · Tasik Kenyir
  (4, 'Tasik Kenyir Houseboat Weekend', 'tasik-kenyir-houseboat',
   'RM30 off a weekend houseboat stay on Tasik Kenyir. Wake up to misty jungle views, swim in the clear lake, visit Lasir Waterfall and the Kelah fish sanctuary.',
   'discount', 30.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 60,
   'Minimum 2-night stay. Includes 3 meals a day. Life jackets provided. Not refundable within 14 days of check-in.',
   'active'),

  -- Campaign 11 · Tasik Kenyir
  (4, 'Kenyir Elephant Conservation Village Pass', 'kenyir-elephant-village',
   'Free educational visit to the Kenyir Elephant Conservation Village — observe rescued elephants, learn about Terengganu''s wildlife rangers, and participate in the mahout-led feeding session.',
   'experience', 15.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 75 DAY), 100,
   'Opens Wed - Sun, 10am - 4pm. Advance booking required. Children must be accompanied. Elephants off-limits during veterinary care.',
   'active'),

  -- Campaign 12 · Craft Trail
  (5, 'Pulau Duyong Traditional Boat Building Tour', 'pulau-duyong-boat-tour',
   'Free 90-minute guided tour of Pulau Duyong''s traditional wooden boat yards. Watch master craftsmen build pinas and kolek vessels using 300-year-old techniques, no nails required.',
   'tourism', 8.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 120,
   'Tours depart 9am and 2pm Monday-Friday from the Pulau Duyong jetty. Comfortable shoes required. Not during rainy days.',
   'active'),

  -- Campaign 13 · Craft Trail
  (5, 'Terengganu Songket Weaving Workshop', 'terengganu-songket-workshop',
   'Buy 1 Free 1 hands-on half-day songket weaving workshop at a certified Terengganu craft studio. Bring home your own woven coaster or small scarf as a souvenir.',
   'b1f1', 60.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 60 DAY), 50,
   'Workshops held Sat 9am - 1pm. Must book 48 hours ahead. Both participants must use the voucher together. All materials included.',
   'active');

-- ─── Cross-link existing merchants to the new campaigns ──────────────────
INSERT INTO campaign_merchants (campaign_id, merchant_id, status) VALUES
  (6,  7, 'active'),  -- Perhentian Scuba Discovery        → Perhentian Dive Center
  (7,  8, 'active'),  -- Lang Tengah Sunset BBQ            → Redang Island Resort
  (8,  3, 'active'),  -- Marang Keropok Lekor Trail        → Keropok Lekor Losong
  (10, 5, 'active'),  -- Tasik Kenyir Houseboat Weekend    → Kenyir Lake Houseboat
  (12, 4, 'active'),  -- Pulau Duyong Boat Building Tour   → Pasar Payang Craft Shop
  (13, 2, 'active'),  -- Terengganu Songket Weaving        → Terengganu Batik House
  (13, 4, 'active');  -- Terengganu Songket Weaving        → Pasar Payang Craft Shop
