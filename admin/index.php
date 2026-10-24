<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/tables.php';
require_once __DIR__ . '/../includes/booking.php';
require_admin();

$pageTitle = 'Dashboard';
$counts = [];
foreach (admin_tables() as $key => $def) {
    $counts[$key] = (int) db()->query("SELECT COUNT(*) FROM `$key`")->fetchColumn();
}
$bookingTotal = (int) db()->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$bookingNew = (int) db()->query("SELECT COUNT(*) FROM bookings WHERE status = 'new'")->fetchColumn();
$bookingUpcoming = (int) db()->query("SELECT COUNT(*) FROM bookings WHERE status IN ('new','confirmed') AND preferred_date >= CURDATE()")->fetchColumn();
$bookingWeek = (int) db()->query('SELECT COUNT(*) FROM bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();
$recent = db()->query('SELECT * FROM bookings ORDER BY created_at DESC LIMIT 6')->fetchAll();
$statusLabels = ['new' => 'New', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];

require __DIR__ . '/includes/admin_header.php';
?>
<div class="page-head">
  <div>
    <h1>Welcome back, <?= e(current_admin()['username'] ?? '') ?></h1>
    <p class="muted">Here's what's happening with your practice website.</p>
  </div>
  <a href="../book.php" target="_blank" class="btn-ghost">Open booking page ↗</a>
</div>

<div class="stat-grid">
  <a class="stat-card <?= $bookingNew > 0 ? 'highlight' : '' ?>" href="bookings.php?status=new">
    <span class="stat-value"><?= $bookingNew ?></span>
    <span class="stat-label">New bookings</span>
    <span class="stat-hint"><?= $bookingNew > 0 ? 'Awaiting your review' : 'All caught up' ?></span>
  </a>
  <a class="stat-card" href="bookings.php?status=confirmed">
    <span class="stat-value"><?= $bookingUpcoming ?></span>
    <span class="stat-label">Upcoming appointments</span>
    <span class="stat-hint">New &amp; confirmed, today onwards</span>
  </a>
  <a class="stat-card" href="bookings.php">
    <span class="stat-value"><?= $bookingWeek ?></span>
    <span class="stat-label">Received this week</span>
    <span class="stat-hint">Last 7 days</span>
  </a>
  <a class="stat-card" href="bookings.php">
    <span class="stat-value"><?= $bookingTotal ?></span>
    <span class="stat-label">Total bookings</span>
    <span class="stat-hint">All time</span>
  </a>
</div>

<div class="dash-columns">
  <section class="panel">
    <div class="panel-head">
      <h2>Recent bookings</h2>
      <a href="bookings.php" class="panel-link">View all →</a>
    </div>
    <?php if (!$recent): ?>
    <p class="muted empty-note">No bookings yet. They'll appear here as soon as a client books online.</p>
    <?php else: ?>
    <ul class="recent-list">
      <?php foreach ($recent as $b): ?>
      <li>
        <div class="recent-main">
          <strong><?= e($b['name']) ?></strong>
          <span class="muted"><?= e(date('D j M', strtotime($b['preferred_date']))) ?> · <?= e($b['preferred_time']) ?> · <?= e(booking_session_label($b['session_type'])) ?></span>
        </div>
        <span class="badge badge-<?= e($b['status']) ?>"><?= e($statusLabels[$b['status']] ?? $b['status']) ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>

  <section class="panel">
    <div class="panel-head">
      <h2>Quick actions</h2>
    </div>
    <div class="quick-actions">
      <a href="availability.php" class="quick-action">
        <strong>Edit availability</strong>
        <span>Session days, hours &amp; capacity</span>
      </a>
      <a href="settings.php" class="quick-action">
        <strong>Site settings</strong>
        <span>Headings, hero, fees &amp; page text</span>
      </a>
      <?php foreach (admin_tables() as $key => $def): ?>
      <a href="manage.php?table=<?= e($key) ?>" class="quick-action">
        <strong><?= e($def['label']) ?></strong>
        <span><?= $counts[$key] ?> item<?= $counts[$key] === 1 ? '' : 's' ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </section>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
