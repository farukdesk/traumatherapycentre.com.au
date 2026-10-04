-- Trauma Therapy Centre — database schema & seed data
-- Import: mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS traumatherapy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE traumatherapy;

-- ---------------------------------------------------------------- admins
CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

-- Default login: admin / admin123  (CHANGE THIS after first login!)
INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$0DTLXbIkWu/beoX487TEkOHG2ksRsdKPiAqxJZuOBNGfIK146hu0y');

-- ---------------------------------------------------------------- settings
CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(80) NOT NULL,
  setting_value TEXT NOT NULL,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Trauma Therapy Centre'),
('meta_description', 'Trauma therapy with Patricia — trained in EMDR and TF-CBT, the two gold standards of trauma treatment. Medicare, DVA, NDIS and telehealth available.'),
('logo_url', 'https://traumatherapycentre.com.au/wp-content/uploads/2020/04/cropped-preview6.png'),
('favicon_url', 'https://traumatherapycentre.com.au/wp-content/uploads/2020/04/cropped-preview6.png'),
('booking_url', 'https://calendly.com/pmre-1'),
('topbar_left', 'Sessions Tuesday · Thursday · Saturday'),
('topbar_right', 'In person & telehealth · Adults 18+'),
('hero_kicker_b', 'Welcome'),
('hero_kicker_text', 'Trauma-focused psychology'),
('hero_line1', 'A quiet place'),
('hero_line2', 'to <em>heal</em> & find'),
('hero_line3', 'your way <em>home.</em>'),
('hero_lead', 'Trained in the two gold standards of trauma treatment — EMDR and Trauma-Focused CBT — with gentle, body-aware therapies woven in as each session unfolds.'),
('hero_photo_url', 'https://traumatherapycentre.com.au/wp-content/uploads/2023/01/headshot-round-1200px-500x500.png'),
('hero_photo_alt', 'Patricia, psychologist at Trauma Therapy Centre'),
('hero_orbit_text', 'EMDR · Internal Family Systems · Brainspotting · iRest® · Expressive Arts · Schema ·'),
('hero_seal_small', 'Accredited'),
('hero_seal_bold', 'EMDR Therapist'),
('intro_heading', 'Therapy that changes the <em>way the brain</em> holds the past.'),
('intro_big', 'Every therapy offered here works by changing the brain''s neural networks — helping memories that once felt overwhelming settle into their rightful place.'),
('intro_small', 'Sessions are grounded in the two gold standards of trauma care, with other approaches brought in gently depending on how each session is going. You never have to go faster than feels safe.'),
('gold_heading', 'Two proven paths through <em>trauma.</em>'),
('gold_note_em', 'Recognised in the Phoenix Australia PTSD Guidelines.'),
('gold_note_text', 'Other therapies may be woven in depending on how each session is going.'),
('therapies_heading', 'Many paths, <em>one intention.</em>'),
('therapies_sub', 'Each approach is chosen to meet you where you are — and many work beautifully alongside one another.'),
('quote_text', 'Personal growth is an <em>inevitable</em> part of trauma work — and a journey worth taking in its <em>own right.</em>'),
('quote_cite', 'Patricia · Trauma Therapy Centre'),
('fees_heading', 'Clear fees, <em>no surprises.</em>'),
('price_label', 'Standard session with MHCP'),
('price_amount', '255'),
('price_unit', '/ 50 min'),
('price_oop_label', 'Out of pocket after Medicare'),
('price_oop_value', '$153.45'),
('price_fine', 'The AAPi recommended fee for a psychologist is $340 for a standard 40–60 minute consultation (1 July 2026 – 30 June 2027). Sessions running over time are $60 per 15 minutes or part thereof. <strong>No bulk billing available.</strong>'),
('reminder_timeline', '2w:Email|1w:Email|3d:Email|24h:Text'),
('reminder_note', 'Four reminders, so you never miss a session. The gate code is in your email and text.'),
('cancel_heading', 'Kindly give <em>three business days''</em> notice.'),
('cancel_sub', 'If you''re unable to attend, letting me know early avoids a cancellation fee — and frees the time for someone waiting.'),
('cancel_why_intro', 'A missed appointment is a loss for three people:'),
('cancel_why_items', 'You, delaying your own therapy progress
A client on the waitlist who urgently needed that time
Me, having prepared for the session'),
('cancel_settle_items', 'Cancellation fees are due within <strong>48 hours</strong>
Medicare doesn''t cover cancellation fees
Without contact by email or text within 48 hours, further bookings pause until payment is made
After two missed appointments, no further appointments are available'),
('cancel_waived_items', 'You let me know by email or text within 48 hours that you were unwell, and supply a medical certificate within two days
Extenuating circumstances or an emergency'),
('about_heading', 'Experienced, accredited & <em>here for you.</em>'),
('availability_note', '"You must be 18 years or older to receive assistance from me."'),
('cta_heading', 'When you''re ready, <em>we''ll begin gently</em> — together.'),
('cta_text', 'Book online in a few moments, in person or via telehealth.'),
('footer_about', 'Trauma-focused psychological care in a calm, confidential and respectful space.'),
('footer_sessions_days', 'Tue · Thu · Sat'),
('footer_sessions_hours', '9:00 am – 1:00 pm'),
('crisis_line1', 'If you are in immediate danger, call <b>000</b>.'),
('crisis_line2', '24/7 crisis support: <b>Lifeline 13 11 14</b>'),
('footer_big_word', 'begin again'),
('footer_copyright', '© 2026 Trauma Therapy Centre. All rights reserved.'),
('footer_credit', 'Enneagram material with thanks to the Enneagram Institute.');

-- ---------------------------------------------------------------- hero stats
CREATE TABLE IF NOT EXISTS hero_stats (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  stat_value VARCHAR(20) NOT NULL,
  stat_label VARCHAR(120) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO hero_stats (stat_value, stat_label, sort_order) VALUES
('2', 'Gold-standard trauma therapies', 1),
('10', 'Medicare-rebated sessions per year', 2),
('11', 'Years of Enneagram study', 3);

-- ---------------------------------------------------------------- marquee
CREATE TABLE IF NOT EXISTS marquee_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  label VARCHAR(120) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO marquee_items (label, sort_order) VALUES
('Medicare Allied Health', 1),
('Veterans'' Affairs', 2),
('Australian Defence Force', 3),
('WorkCover Queensland', 4),
('Victim Support ACT', 5),
('NDIS Registered', 6),
('EMDR Association of Australia', 7);

-- ---------------------------------------------------------------- gold standards
CREATE TABLE IF NOT EXISTS gold_standards (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  num_label VARCHAR(10) NOT NULL,
  title VARCHAR(80) NOT NULL,
  full_name VARCHAR(160) NOT NULL,
  body TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO gold_standards (num_label, title, full_name, body, sort_order) VALUES
('01', 'EMDR', 'Eye Movement Desensitisation & Reprocessing',
'Overwhelming emotions during trauma can disconnect a memory from the rest of your memory network. While focusing on images, thoughts, emotions and sensations alongside bilateral sounds or sensations, the emotional charge fades — and a more helpful belief replaces the negative one.

You don''t need to discuss the event in detail, though we do need to activate it. Usually one event is worked on per session, and EMDR''s effects compound — so it''s often briefer than other therapies.', 1),
('02', 'TF-CBT', 'Trauma-Focused Cognitive Behavioural Therapy',
'TF-CBT addresses the emotional, cognitive and behavioural symptoms of PTSD.

It brings together psycho-education and symptom management with exposure therapy and cognitive therapy.', 2);

-- ---------------------------------------------------------------- therapies
CREATE TABLE IF NOT EXISTS therapies (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(120) NOT NULL,
  subtitle VARCHAR(200) NOT NULL DEFAULT '',
  body TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO therapies (title, subtitle, body, sort_order) VALUES
('Internal Family Systems', 'Befriending the protective parts within', 'IFS offers a new way to understand what''s happening in the body. Processes often seen as pathological — dissociation, flashbacks, panic attacks — are understood as Protective Parts. With support, you can give these Parts the emotional and psychological care they missed out on as a child. IFS combines easily with other therapies, is particularly good with trauma, and is a truly healing process.', 1),
('Brainspotting', 'Where you look affects how you feel', 'The subcortical, unconscious part of the brain is accessed through eye positions and how they''re felt in the body. There''s a unique eye position for the event being worked on. Developed from a natural-flow form of EMDR, Brainspotting takes your lead — it''s beautifully healing and very empowering.', 2),
('Schema Therapy', 'Understanding patterns formed early in life', 'Schema Therapy explores patterns of thinking and behaving that developed when childhood needs weren''t met. Carried into adult life they can do harm — being drawn to the same partner who doesn''t meet your needs, overreacting to the same kind of situation, or feeling stuck. It''s also useful in EMDR to identify themes in memories.', 3),
('HRV Biofeedback', 'Training heart–brain communication', 'Heart Rate Variability biofeedback assesses and trains heart rhythms. You learn to vary the interval between one heartbeat and the next — the interbeat interval — which supports physical, emotional and mental functioning, including:
- Anger & anxiety disorders
- Athletic performance
- Performance for actors & speakers
- Asthma & cardiovascular conditions
- COPD & irritable bowel syndrome
- Chronic fatigue & chronic pain', 4),
('iRest®', 'Deep rest for a nervous system on alert', 'A well-researched practice of deep relaxation shown to effectively reduce PTSD symptoms. Trauma can leave the nervous system stuck in fight/flight; iRest calms it and builds a sense of control, wellbeing and resilience. It''s used with active-duty military personnel, veterans and their families in over 50 US veterans'' hospitals and military bases.', 5),
('Expressive Arts', 'When drawing says what words can''t', 'Traumatic or disturbing experiences can be accessed and processed through drawing, reaching emotions, body sensations and general state. Drawing can be free-flowing or structured, such as Zentangles, and may be paired with bilateral music. It changes brain patterns while helping you stay within your Window of Tolerance.', 6),
('Personal Growth', 'The Enneagram, meditation & mindfulness', 'Drawing on 11 years of Enneagram study, numerous meditation retreats and mindfulness practice. Personal growth is an inevitable part of trauma therapy — and it can also be explored on its own, separate from any trauma work.', 7);

-- ---------------------------------------------------------------- fee steps
CREATE TABLE IF NOT EXISTS fee_steps (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(120) NOT NULL,
  items TEXT NOT NULL,
  flag VARCHAR(255) NOT NULL DEFAULT '',
  show_timeline TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO fee_steps (title, items, flag, show_timeline, sort_order) VALUES
('Mental Health Care Plan', 'I recommend getting an MHCP from your GP — ask for a <strong>long appointment</strong> to set it up
It gives you a <strong>Medicare rebate</strong>
<strong>10 sessions per calendar year</strong> — 6, then 4 more after a GP review
You don''t need an MHCP to book — only to claim the rebate', '', 0, 1),
('Payment & reminders', 'Payment at the end of each session by <strong>cash, EFTPOS or direct debit</strong>
Claim your rebate through myGov using the receipt emailed the same day', '', 1, 2),
('Your first appointment', 'We''ll talk through the therapy and set up processes
It''s a time to get to know each other — one of us will usually do a lot of talking
It may take 2–3 sessions, sometimes more, before active therapy begins — it depends where you''re at', 'If an initial appointment is missed, further appointments won''t be available', 0, 3),
('Telehealth sessions', 'Download <strong>Zoom</strong> on your device — it''s free
Ear buds give better sound quality
Keep a glass of water and tissues close by', '', 0, 4);

-- ---------------------------------------------------------------- cancellation tiers
CREATE TABLE IF NOT EXISTS cancellation_tiers (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  notice_label VARCHAR(80) NOT NULL,
  fee_label VARCHAR(40) NOT NULL,
  fee_note VARCHAR(120) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO cancellation_tiers (notice_label, fee_label, fee_note, sort_order) VALUES
('3+ business days', 'No fee', 'Nothing charged', 1),
('2 business days', '$115', 'If the appointment isn''t filled', 2),
('~1 day or same day', 'Full fee', 'If the appointment isn''t filled', 3);

-- ---------------------------------------------------------------- qualifications
CREATE TABLE IF NOT EXISTS qualifications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category ENUM('registrations','training') NOT NULL DEFAULT 'registrations',
  item VARCHAR(160) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO qualifications (category, item, sort_order) VALUES
('registrations', 'Psychology Boards of Australia & New Zealand', 1),
('registrations', 'Medicare Allied Health Provider', 2),
('registrations', 'Department of Veterans'' Affairs', 3),
('registrations', 'Australian Defence Force', 4),
('registrations', 'WorkCover Queensland', 5),
('registrations', 'Victim Support (ACT)', 6),
('registrations', 'NDIS Registered', 7),
('training', 'Masters in Professional Psychology', 1),
('training', 'Australian Association of Psychologists Inc', 2),
('training', 'EMDR Association of Australia', 3),
('training', 'Accredited EMDR Therapist', 4),
('training', 'IFS Therapist', 5),
('training', 'Brainspotting Therapist', 6),
('training', 'iRest® Provider', 7),
('training', 'Enneagram Teacher', 8),
('training', 'Expressive Arts Therapist', 9);

-- ---------------------------------------------------------------- availability
CREATE TABLE IF NOT EXISTS availability (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  day_label VARCHAR(40) NOT NULL,
  hours_label VARCHAR(60) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB;

INSERT INTO availability (day_label, hours_label, sort_order) VALUES
('Tuesday', '9:00 – 13:00', 1),
('Thursday', '9:00 – 13:00', 2),
('Saturday', '9:00 – 13:00', 3);
