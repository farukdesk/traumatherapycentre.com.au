-- Upgrade: per-slot booking capacity + break message (run once on existing databases)
-- Import: mysql -u root -p traumatherapy < database/upgrade_slot_capacity.sql
--
-- slot_capacity = how many appointments can be accepted for each time slot
-- on that weekday. Once a slot has that many non-cancelled bookings it is
-- shown as unavailable on the public booking calendar.
-- (Admin → Availability also applies this automatically on first visit.)

USE traumatherapy;

ALTER TABLE booking_schedule
  ADD COLUMN slot_capacity SMALLINT UNSIGNED NOT NULL DEFAULT 1;

-- Break message shown on book.php when enabled in Admin → Availability.
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('booking_break_enabled', '0'),
('booking_break_message', '');
