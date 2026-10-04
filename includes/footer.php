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
          <li><a href="#fees">Costs &amp; Appointments</a></li>
          <li><a href="#therapies">Therapy Types</a></li>
          <li><a href="#cancellation">Cancellation Policy</a></li>
        </ul>
      </div>
      <div>
        <h5>About</h5>
        <ul>
          <li><a href="#about">Qualifications</a></li>
          <li><a href="#about">Availability</a></li>
        </ul>
      </div>
      <div>
        <h5>Sessions</h5>
        <ul>
          <li><?= e(setting('footer_sessions_days')) ?></li>
          <li><?= e(setting('footer_sessions_hours')) ?></li>
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

<script src="assets/js/main.js"></script>
</body>
</html>
