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
  'SSM-202301-0088', '11:00 - 22:00 daily', 250.00, 'active', 'active'
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
