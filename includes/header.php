<?php
require_once __DIR__ . '/functions.php';
$bookingUrl = setting('booking_url');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e(setting('site_title')) ?></title>
<meta name="description" content="<?= e(setting('meta_description')) ?>">
<link rel="icon" href="<?= e(setting('favicon_url')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..600;1,9..144,300..600&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="topbar">
  <div class="wrap">
    <span><i></i><?= e(setting('topbar_left')) ?></span>
    <span><?= e(setting('topbar_right')) ?></span>
  </div>
</div>

<header id="hdr">
  <div class="wrap nav">
    <a href="index.php" class="logo" aria-label="<?= e(setting('site_title')) ?> home">
      <img src="<?= e(setting('logo_url')) ?>" alt="<?= e(setting('site_title')) ?>">
    </a>
    <ul class="menu" id="menu">
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="#fees">Costs &amp; Appointments</a></li>
      <li><a href="#therapies">Therapy Types</a></li>
      <li><a href="#cancellation">Cancellation Policy</a></li>
      <li><a href="#about">About</a></li>
    </ul>
    <div class="nav-r">
      <a href="<?= e($bookingUrl) ?>" class="btn btn-dark">Book <span class="arr"></span></a>
      <button class="burger" id="burger" aria-label="Menu"><span></span><span></span></button>
    </div>
  </div>
</header>
