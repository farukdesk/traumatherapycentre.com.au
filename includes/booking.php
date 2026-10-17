<?php
/**
 * Booking helpers — validation, storage and owner/client notifications.
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/sms.php';

/** Human-readable session type label. */
function booking_session_label(string $type): string
{
    return $type === 'telehealth' ? 'Telehealth' : 'In person';
}

/** Admin-set maximum bookings accepted per day (0 = unlimited). */
function booking_daily_limit(): int
{
    return max(0, (int) setting('booking_daily_limit', '0'));
}

/**
 * Map admin availability rows to calendar data:
 * weekday (0=Sun..6=Sat) => hourly time slots parsed from the hours label.
 */
function booking_calendar_days(): array
{
    $dayMap = [
        'sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
        'thursday' => 4, 'friday' => 5, 'saturday' => 6,
    ];
    $calendarDays = [];
    foreach (get_rows('availability') as $slot) {
        $day = strtolower(trim($slot['day_label']));
        if (!isset($dayMap[$day])) {
            continue;
        }
        $times = [];
        if (preg_match('/(\d{1,2}):(\d{2})\s*(am|pm)?\s*[\x{2013}\x{2014}-]\s*(\d{1,2}):(\d{2})\s*(am|pm)?/iu', $slot['hours_label'], $m)) {
            $startH = (int) $m[1];
            if (strtolower($m[3] ?? '') === 'pm' && $startH < 12) {
                $startH += 12;
            }
            $endH = (int) $m[4];
            if (strtolower($m[6] ?? '') === 'pm' && $endH < 12) {
                $endH += 12;
            }
            $start = $startH * 60 + (int) $m[2];
            $end = $endH * 60 + (int) $m[5];
            for ($t = $start; $t + 60 <= $end; $t += 60) {
                $h = intdiv($t, 60);
                $times[] = sprintf('%d:%02d %s', $h % 12 ?: 12, $t % 60, $h < 12 ? 'am' : 'pm');
            }
        }
        $calendarDays[$dayMap[$day]] = $times;
    }
    return $calendarDays;
}

/** Upcoming dates that have reached the daily booking limit ('Y-m-d' strings). */
function booking_full_dates(): array
{
    $limit = booking_daily_limit();
    if ($limit <= 0) {
        return [];
    }
    $stmt = db()->prepare(
        "SELECT preferred_date FROM bookings
         WHERE status <> 'cancelled' AND preferred_date >= CURDATE()
         GROUP BY preferred_date
         HAVING COUNT(*) >= ?"
    );
    $stmt->execute([$limit]);
    return array_column($stmt->fetchAll(), 'preferred_date');
}

/**
 * Server-side availability checks for an already-validated booking:
 * the chosen weekday must be an available day and the date must not be full.
 */
function booking_validate_availability(array $data): array
{
    $errors = [];
    $date = DateTime::createFromFormat('Y-m-d', $data['preferred_date']);
    if (!$date || $date->format('Y-m-d') !== $data['preferred_date']) {
        return $errors; // format problems are reported by booking_validate()
    }
    $calendarDays = booking_calendar_days();
    $hasSlots = (bool) array_filter($calendarDays);
    $dow = (int) $date->format('w');
    if ($hasSlots && empty($calendarDays[$dow])) {
        $errors['preferred_date'] = 'Bookings are not available on that day of the week — please pick an available day.';
    } elseif (in_array($data['preferred_date'], booking_full_dates(), true)) {
        $errors['preferred_date'] = 'That day is fully booked — please choose another date.';
    }
    return $errors;
}

/**
 * Validate submitted booking data.
 * Returns [data, errors]; data is normalised and safe to insert when errors is empty.
 */
function booking_validate(array $input): array
{
    $errors = [];
    $data = [
        'name'           => trim((string) ($input['name'] ?? '')),
        'email'          => trim((string) ($input['email'] ?? '')),
        'phone'          => trim((string) ($input['phone'] ?? '')),
        'session_type'   => ($input['session_type'] ?? '') === 'telehealth' ? 'telehealth' : 'in_person',
        'preferred_date' => trim((string) ($input['preferred_date'] ?? '')),
        'preferred_time' => trim((string) ($input['preferred_time'] ?? '')),
        'message'        => trim((string) ($input['message'] ?? '')),
    ];

    if ($data['name'] === '' || mb_strlen($data['name']) > 120) {
        $errors['name'] = 'Please enter your full name.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($data['email']) > 190) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^[0-9+\s().-]{6,40}$/', $data['phone'])) {
        $errors['phone'] = 'Please enter a valid phone number.';
    }

    $date = DateTime::createFromFormat('Y-m-d', $data['preferred_date']);
    if (!$date || $date->format('Y-m-d') !== $data['preferred_date']) {
        $errors['preferred_date'] = 'Please choose a preferred date.';
    } elseif ($date->format('Y-m-d') < date('Y-m-d')) {
        $errors['preferred_date'] = 'The preferred date must be in the future.';
    }

    if ($data['preferred_time'] === '' || mb_strlen($data['preferred_time']) > 40) {
        $errors['preferred_time'] = 'Please choose a preferred time.';
    }
    if (mb_strlen($data['message']) > 2000) {
        $errors['message'] = 'Message is too long (max 2000 characters).';
    }

    return [$data, $errors];
}

/** Insert a booking and return its id. */
function booking_create(array $data): int
{
    $stmt = db()->prepare(
        'INSERT INTO bookings (name, email, phone, session_type, preferred_date, preferred_time, message)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $data['name'], $data['email'], $data['phone'], $data['session_type'],
        $data['preferred_date'], $data['preferred_time'], $data['message'],
    ]);
    return (int) db()->lastInsertId();
}

/** Simple branded wrapper used by both booking emails. */
function booking_email_wrap(string $heading, string $bodyHtml): string
{
    $siteTitle = e(setting('site_title', 'Trauma Therapy Centre'));
    $year = date('Y');
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>{$heading} — {$siteTitle}</title></head>
<body style="margin:0;padding:0;background-color:#f4f1ec;font-family:Georgia,'Times New Roman',serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f1ec;padding:32px 16px;">
    <tr><td align="center">
      <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background-color:#fffdf8;border:1px solid #e3ddd2;border-radius:16px;overflow:hidden;">
        <tr><td style="background-color:#3f4a3c;padding:24px 36px;text-align:center;">
          <h1 style="margin:0;font-size:20px;font-weight:normal;color:#fffdf8;letter-spacing:.5px;">{$siteTitle}</h1>
        </td></tr>
        <tr><td style="padding:32px 36px;">
          <h2 style="margin:0 0 16px;font-size:18px;color:#3f4a3c;">{$heading}</h2>
          {$bodyHtml}
        </td></tr>
        <tr><td style="padding:18px 36px;background-color:#f4f1ec;border-top:1px solid #e3ddd2;text-align:center;">
          <p style="margin:0;font-size:12px;color:#8a8278;">&copy; {$year} {$siteTitle}. This is an automated message.</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

/** HTML details table of a booking (used in both emails). */
function booking_details_html(array $data, int $bookingId): string
{
    $rows = [
        'Booking #'      => (string) $bookingId,
        'Name'           => $data['name'],
        'Email'          => $data['email'],
        'Phone'          => $data['phone'],
        'Session type'   => booking_session_label($data['session_type']),
        'Preferred date' => date('l, j F Y', strtotime($data['preferred_date'])),
        'Preferred time' => $data['preferred_time'],
    ];
    $html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;border:1px solid #e3ddd2;border-radius:10px;">';
    foreach ($rows as $label => $value) {
        $html .= '<tr><td style="padding:9px 14px;font-size:13px;color:#8a8278;border-bottom:1px solid #efe9df;white-space:nowrap;">' . e($label) . '</td>'
               . '<td style="padding:9px 14px;font-size:14px;color:#4a4540;border-bottom:1px solid #efe9df;">' . e($value) . '</td></tr>';
    }
    $html .= '</table>';
    if ($data['message'] !== '') {
        $html .= '<p style="margin:0 0 6px;font-size:13px;color:#8a8278;">Message:</p>'
               . '<p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#4a4540;">' . nl2br(e($data['message'])) . '</p>';
    }
    return $html;
}

/**
 * Send all notifications for a new booking (best-effort — a failed
 * notification never blocks the booking itself).
 */
function booking_notify(array $data, int $bookingId): void
{
    $siteTitle = setting('site_title', 'Trauma Therapy Centre');

    // 1. Email the owner.
    $ownerEmail = trim(setting('booking_notify_email'));
    if ($ownerEmail !== '') {
        $body = '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#4a4540;">'
              . 'You have a new booking request. Review and confirm it in your admin dashboard.</p>'
              . booking_details_html($data, $bookingId);
        send_html_email(
            $ownerEmail,
            'New booking #' . $bookingId . ' — ' . $data['name'],
            booking_email_wrap('New booking request', $body)
        );
    }

    // 2. SMS the owner.
    $ownerSms = trim(setting('booking_notify_sms'));
    if ($ownerSms !== '') {
        send_sms($ownerSms, sprintf(
            "%s: new booking #%d from %s (%s) — %s %s, %s. Details in your dashboard.",
            $siteTitle,
            $bookingId,
            $data['name'],
            $data['phone'],
            date('D j M', strtotime($data['preferred_date'])),
            $data['preferred_time'],
            booking_session_label($data['session_type'])
        ));
    }

    // 3. Confirmation email to the client.
    $body = '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#4a4540;">Hi <strong>' . e($data['name']) . '</strong>,</p>'
          . '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#4a4540;">'
          . 'Thank you for your booking request. It has been received and you will be contacted shortly to confirm your appointment. Here is a copy of what you submitted:</p>'
          . booking_details_html($data, $bookingId)
          . '<p style="margin:0;font-size:13px;line-height:1.6;color:#8a8278;">Please note this is a request — your appointment is confirmed once you hear back from us.</p>';
    send_html_email(
        $data['email'],
        'Booking request received — ' . $siteTitle,
        booking_email_wrap('Your booking request', $body)
    );
}
