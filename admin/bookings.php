<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/booking.php';
require_admin();

$pageTitle = 'Bookings';
$statuses = ['new' => 'New', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];

// Handle status change / delete.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);

    if (($_POST['action'] ?? '') === 'status' && isset($statuses[$_POST['status'] ?? ''])) {
        $newStatus = $_POST['status'];
        $stmt = db()->prepare('SELECT * FROM bookings WHERE id = ?');
        $stmt->execute([$id]);
        $booking = $stmt->fetch();
        if ($booking && $booking['status'] !== $newStatus) {
            $stmt = db()->prepare('UPDATE bookings SET status = ? WHERE id = ?');
            $stmt->execute([$newStatus, $id]);
            $note = '';
            if (in_array($newStatus, ['confirmed', 'completed', 'cancelled'], true)) {
                $note = booking_notify_status($booking, $newStatus)
                    ? ' The client has been notified by email.'
                    : ' Could not send the notification email to the client.';
            }
            flash_set('success', 'Booking #' . $id . ' marked as ' . $statuses[$newStatus] . '.' . $note);
        }
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $stmt = db()->prepare('DELETE FROM bookings WHERE id = ?');
        $stmt->execute([$id]);
        flash_set('success', 'Booking #' . $id . ' deleted.');
    }
    redirect('bookings.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
}

// Filter + list.
$filter = $_GET['status'] ?? '';
if (!isset($statuses[$filter])) {
    $filter = '';
}
if ($filter !== '') {
    $stmt = db()->prepare('SELECT * FROM bookings WHERE status = ? ORDER BY created_at DESC');
    $stmt->execute([$filter]);
} else {
    $stmt = db()->query('SELECT * FROM bookings ORDER BY created_at DESC');
}
$bookings = $stmt->fetchAll();

$counts = ['' => 0];
foreach (db()->query('SELECT status, COUNT(*) AS c FROM bookings GROUP BY status') as $row) {
    $counts[$row['status']] = (int) $row['c'];
    $counts[''] += (int) $row['c'];
}

require __DIR__ . '/includes/admin_header.php';
?>
<div class="page-head">
  <div>
    <h1>Bookings</h1>
    <p class="muted">Review requests, confirm appointments and keep clients updated — status changes email the client automatically.</p>
  </div>
</div>

<div class="booking-filters">
  <a href="bookings.php" class="pill <?= $filter === '' ? 'on' : 'off' ?>">All (<?= $counts[''] ?>)</a>
  <?php foreach ($statuses as $key => $label): ?>
  <a href="bookings.php?status=<?= e($key) ?>" class="pill <?= $filter === $key ? 'on' : 'off' ?>"><?= e($label) ?> (<?= $counts[$key] ?? 0 ?>)</a>
  <?php endforeach; ?>
</div>

<div class="table-wrap">
<table class="data-table">
  <thead>
    <tr>
      <th>#</th><th>Client</th><th>Contact</th><th>Appointment</th><th>Message</th><th>Received</th><th>Status</th><th></th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$bookings): ?>
    <tr><td colspan="8" class="muted">No bookings<?= $filter !== '' ? ' with this status' : ' yet' ?>.</td></tr>
    <?php endif; ?>
    <?php foreach ($bookings as $b): ?>
    <tr class="<?= $b['status'] === 'cancelled' ? 'inactive' : '' ?>">
      <td><?= (int) $b['id'] ?></td>
      <td><strong><?= e($b['name']) ?></strong></td>
      <td>
        <a href="mailto:<?= e($b['email']) ?>"><?= e($b['email']) ?></a><br>
        <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $b['phone'])) ?>"><?= e($b['phone']) ?></a>
      </td>
      <td>
        <?= e(date('D j M Y', strtotime($b['preferred_date']))) ?><br>
        <?= e($b['preferred_time']) ?> · <?= e(booking_session_label($b['session_type'])) ?>
      </td>
      <td class="booking-msg"><?= $b['message'] !== '' ? nl2br(e($b['message'])) : '<span class="muted">—</span>' ?></td>
      <td><?= e(date('j M Y, g:i a', strtotime($b['created_at']))) ?></td>
      <td>
        <span class="badge badge-<?= e($b['status']) ?>"><?= e($statuses[$b['status']] ?? $b['status']) ?></span>
        <form method="post" action="bookings.php<?= $filter !== '' ? '?status=' . e($filter) : '' ?>" class="inline status-form">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="status">
          <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
          <select name="status" onchange="this.form.submit()" aria-label="Change status">
            <?php foreach ($statuses as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $b['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </td>
      <td class="actions">
        <form method="post" action="bookings.php<?= $filter !== '' ? '?status=' . e($filter) : '' ?>" class="inline" onsubmit="return confirm('Delete this booking?');">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
          <button type="submit" class="btn-danger sm">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
