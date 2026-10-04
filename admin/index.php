<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/tables.php';
require_admin();

$pageTitle = 'Dashboard';
$counts = [];
foreach (admin_tables() as $key => $def) {
    $counts[$key] = (int) db()->query("SELECT COUNT(*) FROM `$key`")->fetchColumn();
}

require __DIR__ . '/includes/admin_header.php';
?>
<h1>Dashboard</h1>
<p class="muted">Welcome back, <?= e(current_admin()['username'] ?? '') ?>. Manage every section of the website from here.</p>

<div class="cards">
  <a class="card" href="settings.php">
    <h3>Site Settings</h3>
    <p>Headings, hero, fees, footer &amp; all page text</p>
  </a>
  <?php foreach (admin_tables() as $key => $def): ?>
  <a class="card" href="manage.php?table=<?= e($key) ?>">
    <h3><?= e($def['label']) ?></h3>
    <p><?= $counts[$key] ?> item<?= $counts[$key] === 1 ? '' : 's' ?></p>
  </a>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
