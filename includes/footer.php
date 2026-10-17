<?php
$footerSessionDays  = booking_open_days_label(true) ?: setting('footer_sessions_days');
$footerSessionHours = booking_session_hours_label() ?: setting('footer_sessions_hours');
?>
<footer>
  <div class="wrap">
    <div class="foot">
      <div>
        <a href="index.php" class="logo"><img src="<?= e(setting('logo_url')) ?>" alt="<?= e(setting('site_title')) ?>"></a>
        <p><?= e(setting('footer_about')) ?></p>
      </div>
      <div>
        <h5>Explore</h5>
        <ul>
          <li><a href="fees.php">Costs &amp; Appointments</a></li>
          <li><a href="therapies.php">Therapy Types</a></li>
          <li><a href="cancellation.php">Cancellation Policy</a></li>
        </ul>
      </div>
      <div>
        <h5>About</h5>
        <ul>
          <li><a href="about.php">Qualifications</a></li>
          <li><a href="about.php">Availability</a></li>
        </ul>
      </div>
      <div>
        <h5>Sessions</h5>
        <ul>
          <li><?= e($footerSessionDays) ?></li>
          <li><?= e($footerSessionHours) ?></li>
          <li><a href="<?= e(setting('booking_url')) ?>">Book online →</a></li>
        </ul>
      </div>
    </div>
    <div class="crisis">
      <span><?= rich(setting('crisis_line1')) ?></span>
      <span><?= rich(setting('crisis_line2')) ?></span>
    </div>
    <div class="big-word"><?= e(setting('footer_big_word')) ?></div>
    <div class="fbot">
      <span><?= e(setting('footer_copyright')) ?></span>
      <span><?= e(setting('footer_credit')) ?></span>
    </div>
  </div>
</footer>

<script src="assets/js/main.js?v=<?= filemtime(__DIR__ . '/../assets/js/main.js') ?>"></script>
</body>
</html>
