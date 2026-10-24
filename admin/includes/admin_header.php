<?php
require_once __DIR__ . '/tables.php';
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$currentTable = $_GET['table'] ?? '';
$navNewBookings = (int) db()->query("SELECT COUNT(*) FROM bookings WHERE status = 'new'")->fetchColumn();

/** Inline SVG icons for the admin nav. */
function admin_icon(string $name): string
{
    $icons = [
        'dashboard' => '<path d="M3 3h7v9H3zM14 3h7v5h-7zM14 12h7v9h-7zM3 16h7v5H3z"/>',
        'bookings'  => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 9h18"/>',
        'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>',
        'settings'  => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
        'content'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/>',
        'lock'      => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'external'  => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
    ];
    return '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($icons[$name] ?? '') . '</svg>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Admin') ?> — <?= e(setting('site_title')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin.css') ?>">
</head>
<body>
<div class="admin-layout">
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <span class="brand-mark">TT</span>
      <span class="brand-text">
        <strong>Admin Panel</strong>
        <span><?= e(setting('site_title')) ?></span>
      </span>
    </div>
    <nav class="sidebar-nav">
      <span class="nav-label">Overview</span>
      <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>"><?= admin_icon('dashboard') ?>Dashboard</a>

      <span class="nav-label">Appointments</span>
      <a href="bookings.php" class="<?= $currentPage === 'bookings.php' ? 'active' : '' ?>">
        <?= admin_icon('bookings') ?>Bookings
        <?php if ($navNewBookings > 0): ?><span class="nav-badge"><?= $navNewBookings ?></span><?php endif; ?>
      </a>
      <a href="availability.php" class="<?= $currentPage === 'availability.php' ? 'active' : '' ?>"><?= admin_icon('clock') ?>Availability</a>

      <span class="nav-label">Website Content</span>
      <a href="settings.php" class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>"><?= admin_icon('settings') ?>Site Settings</a>
      <?php foreach (admin_tables() as $navKey => $navDef): ?>
      <a href="manage.php?table=<?= e($navKey) ?>" class="<?= $currentPage === 'manage.php' && $currentTable === $navKey ? 'active' : '' ?>"><?= admin_icon('content') ?><?= e($navDef['label']) ?></a>
      <?php endforeach; ?>

      <span class="nav-label">Account</span>
      <a href="password.php" class="<?= $currentPage === 'password.php' ? 'active' : '' ?>"><?= admin_icon('lock') ?>Change Password</a>
    </nav>
    <div class="sidebar-foot">
      <a href="../index.php" target="_blank"><?= admin_icon('external') ?>View Website</a>
      <a href="logout.php" class="logout"><?= admin_icon('logout') ?>Log out</a>
    </div>
  </aside>
  <div class="admin-body">
    <header class="topbar">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
      </button>
      <span class="topbar-title"><?= e($pageTitle ?? 'Admin') ?></span>
      <div class="topbar-right">
        <?php if ($navNewBookings > 0): ?>
        <a href="bookings.php?status=new" class="topbar-pill"><?= $navNewBookings ?> new booking<?= $navNewBookings === 1 ? '' : 's' ?></a>
        <?php endif; ?>
        <span class="topbar-user" title="Signed in as <?= e(current_admin()['username'] ?? '') ?>">
          <span class="user-avatar"><?= e(strtoupper(substr(current_admin()['username'] ?? 'A', 0, 1))) ?></span>
          <span class="user-name"><?= e(current_admin()['username'] ?? '') ?></span>
        </span>
      </div>
    </header>
    <main class="admin-main">
    <?php if ($flash = flash_get()): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
