-- Upgrade: daily booking limit (run once on existing databases)
-- Import: mysql -u root -p traumatherapy < database/upgrade_booking_limit.sql

USE traumatherapy;

-- Maximum bookings accepted per day; the booking calendar blocks a day
-- once it is reached. 0 = unlimited. Edit in Admin → Site Settings →
-- Booking Page & Notifications.
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('booking_daily_limit', '0');
