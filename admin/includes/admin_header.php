<?php
require_once __DIR__ . '/tables.php';
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$currentTable = $_GET['table'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Admin') ?> — <?= e(setting('site_title')) ?></title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
  <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">☰</button>
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <strong>Admin</strong>
      <span><?= e(setting('site_title')) ?></span>
    </div>
    <nav>
      <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Dashboard</a>
      <a href="settings.php" class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>">Site Settings</a>
      <?php foreach (admin_tables() as $key => $def): ?>
      <a href="manage.php?table=<?= e($key) ?>" class="<?= $currentPage === 'manage.php' && $currentTable === $key ? 'active' : '' ?>"><?= e($def['label']) ?></a>
      <?php endforeach; ?>
      <a href="password.php" class="<?= $currentPage === 'password.php' ? 'active' : '' ?>">Change Password</a>
      <a href="../index.php" target="_blank">View Website ↗</a>
      <a href="logout.php" class="logout">Log out</a>
    </nav>
  </aside>
  <main class="admin-main">
    <?php if ($flash = flash_get()): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
