-- Upgrade: custom booking system (run once on existing databases)
-- Import: mysql -u root -p traumatherapy < database/upgrade_bookings.sql

USE traumatherapy;

CREATE TABLE IF NOT EXISTS bookings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  session_type ENUM('in_person','telehealth') NOT NULL DEFAULT 'in_person',
  preferred_date DATE NOT NULL,
  preferred_time VARCHAR(40) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new','confirmed','completed','cancelled') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_status (status),
  KEY idx_created (created_at)
) ENGINE=InnoDB;

-- Point the Book buttons at the new in-house booking page.
UPDATE settings SET setting_value = 'book.php' WHERE setting_key = 'booking_url';

-- Notification settings (edit in Admin → Site Settings → Booking Notifications).
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('booking_heading', 'Book your <em>appointment.</em>'),
('booking_sub', 'Fill in the form below and I''ll confirm your session by email or phone as soon as possible.'),
('booking_notify_email', ''),
('booking_notify_sms', ''),
('twilio_sid', ''),
('twilio_token', ''),
('twilio_from', '');
