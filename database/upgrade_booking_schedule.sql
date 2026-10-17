-- Upgrade: structured weekly booking schedule (run once on existing databases)
-- Import: mysql -u root -p traumatherapy < database/upgrade_booking_schedule.sql
--
-- One row per weekday (0 = Sunday … 6 = Saturday). The schedule repeats
-- every week until it is changed in Admin → Availability. Open days,
-- start/end times and slot length are all editable — no code changes needed.

USE traumatherapy;

CREATE TABLE IF NOT EXISTS booking_schedule (
  weekday TINYINT UNSIGNED NOT NULL,
  is_open TINYINT(1) NOT NULL DEFAULT 0,
  start_time TIME NOT NULL DEFAULT '09:00:00',
  end_time TIME NOT NULL DEFAULT '13:00:00',
  slot_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  PRIMARY KEY (weekday)
) ENGINE=InnoDB;

-- Seed with the current availability (Tue / Thu / Sat, 9:00 am – 1:00 pm).
INSERT IGNORE INTO booking_schedule (weekday, is_open, start_time, end_time, slot_minutes) VALUES
(0, 0, '09:00:00', '13:00:00', 60),
(1, 0, '09:00:00', '13:00:00', 60),
(2, 1, '09:00:00', '13:00:00', 60),
(3, 0, '09:00:00', '13:00:00', 60),
(4, 1, '09:00:00', '13:00:00', 60),
(5, 0, '09:00:00', '13:00:00', 60),
(6, 1, '09:00:00', '13:00:00', 60);
