<?php
/**
 * SMS helper — sends text messages through the Twilio REST API.
 * Configure in Admin → Site Settings → Booking Notifications:
 *   twilio_sid, twilio_token, twilio_from and booking_notify_sms.
 * If any credential is missing the send is silently skipped.
 */

require_once __DIR__ . '/functions.php';

/** Whether SMS sending is configured. */
function sms_configured(): bool
{
    return setting('twilio_sid') !== ''
        && setting('twilio_token') !== ''
        && setting('twilio_from') !== '';
}

/** Send an SMS. Returns true on success, false otherwise. */
function send_sms(string $to, string $body): bool
{
    $to = trim($to);
    if ($to === '' || !sms_configured() || !function_exists('curl_init')) {
        return false;
    }

    $sid   = setting('twilio_sid');
    $token = setting('twilio_token');
    $from  = setting('twilio_from');

    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode($sid) . '/Messages.json';

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'From' => $from,
            'To'   => $to,
            'Body' => mb_substr($body, 0, 1500),
        ]),
        CURLOPT_USERPWD        => $sid . ':' . $token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
    ]);
    curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $status >= 200 && $status < 300;
}
