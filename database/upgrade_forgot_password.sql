-- Upgrade script for existing installs: adds "forgot password" support.
-- Import: mysql -u root -p traumatherapy < database/upgrade_forgot_password.sql
-- Afterwards, set your admin email so reset links can be delivered:
--   UPDATE admins SET email = 'you@example.com' WHERE username = 'admin';

ALTER TABLE admins
  ADD COLUMN email VARCHAR(190) NOT NULL DEFAULT '' AFTER username,
  ADD COLUMN reset_token_hash VARCHAR(64) DEFAULT NULL AFTER password_hash,
  ADD COLUMN reset_token_expires DATETIME DEFAULT NULL AFTER reset_token_hash;
