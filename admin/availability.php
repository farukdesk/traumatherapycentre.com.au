<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/booking.php';
require_admin();

$pageTitle = 'Availability';
$weekdayNames = booking_weekday_names();
// Edit order: Monday-first for a natural weekly reading order.
$editOrder = [1, 2, 3, 4, 5, 6, 0];
$slotOptions = [30, 45, 50, 60, 75, 90, 120];

// Make sure the schedule table exists (first visit after deploying the upgrade).
db()->exec(
    "CREATE TABLE IF NOT EXISTS booking_schedule (
      weekday TINYINT UNSIGNED NOT NULL,
      is_open TINYINT(1) NOT NULL DEFAULT 0,
      start_time TIME NOT NULL DEFAULT '09:00:00',
      end_time TIME NOT NULL DEFAULT '13:00:00',
      slot_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 60,
      PRIMARY KEY (weekday)
    ) ENGINE=InnoDB"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $errors = [];
    $rows = [];
    foreach ($editOrder as $weekday) {
        $isOpen = !empty($_POST['open'][$weekday]) ? 1 : 0;
        $start = trim((string) ($_POST['start'][$weekday] ?? ''));
        $end = trim((string) ($_POST['end'][$weekday] ?? ''));
        $slot = (int) ($_POST['slot'][$weekday] ?? 60);
        if (!preg_match('/^([01]?\d|2[0-3]):[0-5]\d$/', $start)) {
            $start = '09:00';
        }
        if (!preg_match('/^([01]?\d|2[0-3]):[0-5]\d$/', $end)) {
            $end = '13:00';
        }
        if (!in_array($slot, $slotOptions, true)) {
            $slot = 60;
        }
        if ($isOpen) {
            $startMin = booking_time_to_minutes($start);
            $endMin = booking_time_to_minutes($end);
            if ($endMin <= $startMin) {
                $errors[] = $weekdayNames[$weekday] . ': the end time must be after the start time.';
            } elseif ($startMin + $slot > $endMin) {
                $errors[] = $weekdayNames[$weekday] . ': the hours are shorter than one session slot.';
            }
        }
        $rows[] = [$weekday, $isOpen, $start . ':00', $end . ':00', $slot];
    }

    if ($errors) {
        flash_set('error', implode(' ', $errors));
    } else {
        $stmt = db()->prepare(
            'REPLACE INTO booking_schedule (weekday, is_open, start_time, end_time, slot_minutes)
             VALUES (?, ?, ?, ?, ?)'
        );
        foreach ($rows as $row) {
            $stmt->execute($row);
        }
        flash_set('success', 'Weekly availability saved — the booking calendar is updated immediately.');
    }
    redirect('availability.php');
}

// Current schedule (defaults for weekdays not yet saved).
$schedule = booking_schedule();
foreach ($editOrder as $weekday) {
    if (!isset($schedule[$weekday])) {
        $schedule[$weekday] = ['is_open' => false, 'start_time' => '09:00', 'end_time' => '13:00', 'slot_minutes' => 60];
    }
}
$calendarDays = booking_calendar_days();

require __DIR__ . '/includes/admin_header.php';
?>
<div class="page-head">
  <h1>Availability</h1>
</div>

<p class="muted" style="margin:0 0 18px;max-width:720px;">
  Set your weekly schedule below. It repeats every week automatically until you change it —
  open days show on the booking calendar with time slots generated from your start/end times
  and session length. Closed days cannot be booked.
</p>

<form method="post" action="availability.php">
  <?= csrf_field() ?>
  <div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr><th>Day</th><th>Open for bookings</th><th>From</th><th>Until</th><th>Session length</th><th>Bookable slots</th></tr>
    </thead>
    <tbody>
      <?php foreach ($editOrder as $weekday): $row = $schedule[$weekday]; ?>
      <tr class="<?= $row['is_open'] ? '' : 'inactive' ?>">
        <td><strong><?= e($weekdayNames[$weekday]) ?></strong></td>
        <td>
          <label>
            <input type="checkbox" name="open[<?= (int) $weekday ?>]" value="1" <?= $row['is_open'] ? 'checked' : '' ?>>
            Open
          </label>
        </td>
        <td><input type="time" name="start[<?= (int) $weekday ?>]" value="<?= e($row['start_time']) ?>" required></td>
        <td><input type="time" name="end[<?= (int) $weekday ?>]" value="<?= e($row['end_time']) ?>" required></td>
        <td>
          <select name="slot[<?= (int) $weekday ?>]">
            <?php foreach ($slotOptions as $opt): ?>
            <option value="<?= $opt ?>" <?= (int) $row['slot_minutes'] === $opt ? 'selected' : '' ?>><?= $opt ?> min</option>
            <?php endforeach; ?>
          </select>
        </td>
        <td class="muted">
          <?= !empty($calendarDays[$weekday]) ? e(implode(', ', $calendarDays[$weekday])) : '—' ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <div class="form-actions">
    <button type="submit" class="btn-primary">Save weekly availability</button>
  </div>
</form>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
