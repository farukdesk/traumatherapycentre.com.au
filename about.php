<?php
require_once __DIR__ . '/includes/functions.php';

$availability  = get_rows('availability');
$registrations = array_filter(get_rows('qualifications'), fn ($q) => $q['category'] === 'registrations');
$training      = array_filter(get_rows('qualifications'), fn ($q) => $q['category'] === 'training');
$bookingUrl    = setting('booking_url');
$pageTitle     = 'About';

require __DIR__ . '/includes/header.php';
?>

<!-- ABOUT -->
<section id="about">
  <div class="wrap about-grid">
    <aside class="avail rv">
      <div class="kicker"><b>i.</b> Sessions</div>
      <h3>My <em>availability</em></h3>
      <?php foreach ($availability as $slot): ?>
      <div class="slot"><span><?= e($slot['day_label']) ?></span><span><?= e($slot['hours_label']) ?></span></div>
      <?php endforeach; ?>
      <p class="age"><?= e(setting('availability_note')) ?></p>
      <a href="<?= e($bookingUrl) ?>" class="btn btn-ivory">Book appointment <span class="arr"></span></a>
    </aside>
    <div class="creds rv">
      <div class="kicker"><b>ii.</b> Qualifications</div>
      <h2><?= rich_inline(setting('about_heading')) ?></h2>
      <div class="cols">
        <div>
          <h4>Registrations</h4>
          <ul>
            <?php foreach ($registrations as $q): ?>
            <li><?= e($q['item']) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div>
          <h4>Training &amp; memberships</h4>
          <ul>
            <?php foreach ($training as $q): ?>
            <li><?= e($q['item']) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
