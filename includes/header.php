<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/booking.php';
require_once __DIR__ . '/site.php';
$bookingUrl = setting('booking_url');
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
$topbarDays = site_session_days();
$topbarLeft = $topbarDays !== '' ? 'Sessions ' . $topbarDays : setting('topbar_left');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e(isset($pageTitle) ? $pageTitle . ' — ' . setting('site_title') : setting('site_title')) ?></title>
<meta name="description" content="<?= e(setting('meta_description')) ?>">
<link rel="icon" href="<?= e(setting('favicon_url')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..600;1,9..144,300..600&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body<?= isset($bodyClass) && $bodyClass !== '' ? ' class="' . e($bodyClass) . '"' : '' ?>>

<div class="topbar">
  <div class="wrap">
    <span><i></i><?= e($topbarLeft) ?></span>
    <span><?= e(setting('topbar_right')) ?></span>
  </div>
</div>

<header id="hdr">
  <div class="wrap nav">
    <a href="index.php" class="logo" aria-label="<?= e(setting('site_title')) ?> home">
      <img src="<?= e(setting('logo_url')) ?>" alt="<?= e(setting('site_title')) ?>">
    </a>
    <ul class="menu" id="menu">
      <li><a href="index.php"<?= $currentPage === 'index.php' ? ' class="active"' : '' ?>>Home</a></li>
      <li><a href="fees.php"<?= $currentPage === 'fees.php' ? ' class="active"' : '' ?>>Costs &amp; Appointments</a></li>
      <li><a href="therapies.php"<?= $currentPage === 'therapies.php' ? ' class="active"' : '' ?>>Therapy Types</a></li>
      <li><a href="cancellation.php"<?= $currentPage === 'cancellation.php' ? ' class="active"' : '' ?>>Cancellation Policy</a></li>
      <li><a href="about.php"<?= $currentPage === 'about.php' ? ' class="active"' : '' ?>>About</a></li>
    </ul>
    <div class="nav-r">
      <a href="<?= e($bookingUrl) ?>" class="btn btn-dark">Book <span class="arr"></span></a>
      <button class="burger" id="burger" aria-label="Menu"><span></span><span></span></button>
    </div>
  </div>
</header>
