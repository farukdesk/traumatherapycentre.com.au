-- Upgrade: client content review (run once on existing databases)
-- Import: mysql -u root -p traumatherapy < database/upgrade_content_review.sql
--
-- Applies Patricia's content feedback:
--   * Reduce TF-CBT prominence — EMDR is the approach used most often.
--   * Emphasise telehealth (no in-home visits are offered).
--   * 15 years in practice / fourth career instead of "sessions held".
--   * In-person sessions are held in rooms at Highgate Hill.
--   * Cancellation: as much notice as possible, at least 3 business days.

USE traumatherapy;

UPDATE settings SET setting_value = 'Trauma therapy with Patricia — an accredited EMDR therapist with 15 years in practice. In person at Highgate Hill or via telehealth. Medicare, DVA and NDIS available.'
WHERE setting_key = 'meta_description';

UPDATE settings SET setting_value = 'An accredited EMDR therapist with fifteen years in practice — and gentle, body-aware therapies woven in as each session unfolds.'
WHERE setting_key = 'hero_lead';

UPDATE settings SET setting_value = 'Sessions are grounded in EMDR — a gold standard of trauma care — with other approaches brought in gently depending on how each session is going. You never have to go faster than feels safe.'
WHERE setting_key = 'intro_small';

UPDATE settings SET setting_value = 'EMDR is the approach used most often here — other therapies may be woven in depending on how each session is going.'
WHERE setting_key = 'gold_note_text';

UPDATE settings SET setting_value = 'Kindly give as much notice as you can — <em>at least three business days.</em>'
WHERE setting_key = 'cancel_heading';

UPDATE settings SET setting_value = 'If you''re unable to attend, letting me know as early as possible avoids a cancellation fee — and frees the time for someone waiting.'
WHERE setting_key = 'cancel_sub';

UPDATE settings SET setting_value = 'Book online in a few moments — in person at Highgate Hill or via telehealth.'
WHERE setting_key = 'cta_text';

-- Hero stat: replace "2 gold-standard trauma therapies" with years in practice.
UPDATE hero_stats SET stat_value = '15', stat_label = 'Years in practice'
WHERE stat_label = 'Gold-standard trauma therapies';

-- Note on the TF-CBT gold-standard card that EMDR is the primary approach.
UPDATE gold_standards
SET body = CONCAT(body, '\n\nPatricia is trained in TF-CBT, though most sessions draw primarily on EMDR.')
WHERE title = 'TF-CBT'
  AND body NOT LIKE '%draw primarily on EMDR%';
