<?php
/**
 * Site-wide shared configuration — single source of truth for session
 * days/hours, fee figures and contact details. No page or partial should
 * hard-code these values.
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/booking.php';

/**
 * Open session days as a label, e.g. 'Friday · Saturday'
 * (or 'Fri · Sat' when $short). Derived from the admin booking schedule,
 * falling back to the legacy text setting when the table doesn't exist yet.
 *
 * TODO: Confirm session days with Patricia.
 */
function site_session_days(bool $short = false): string
{
    $label = booking_open_days_label($short);
    return $label !== '' ? $label : setting('footer_sessions_days');
}

/** Session hours label, e.g. '9:00 am – 1:00 pm'. */
function site_session_hours(): string
{
    $label = booking_session_hours_label();
    return $label !== '' ? $label : setting('footer_sessions_hours');
}

/**
 * Shared fee figures in dollars — used by the costs page card/toggle and
 * the cancellation fee scale.
 * TODO: Confirm current fee and Medicare rebate amounts with the client.
 */
function site_fees(): array
{
    return [
        'session'    => 255.00,
        'rebate'     => 101.55,
        'lateCancel' => 115.00,
    ];
}

/**
 * Contact details for cancel/reschedule contact points.
 * TODO: Add real email + mobile.
 */
function site_contact(): array
{
    return [
        'email'  => 'patricia@example.com',
        'mobile' => '0400000000',
    ];
}
