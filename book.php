<?php
require_once __DIR__ . '/includes/booking.php';

$errors = [];
$old = [];
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    // Honeypot — bots fill this hidden field; pretend success and discard.
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        redirect('book.php?sent=1');
    }

    [$data, $errors] = booking_validate($_POST);
    $errors += booking_validate_availability($data);
    if (!$errors) {
        $bookingId = booking_create($data);
        booking_notify($data, $bookingId);
        redirect('book.php?sent=1');
    }
    $old = $data;
}

$submitted = isset($_GET['sent']);
$availability = get_rows('availability');
$calendarDays = booking_calendar_days();
$fullDates = booking_full_dates();

require __DIR__ . '/includes/header.php';
?>

<section class="booking-section" id="book">
  <div class="wrap">
    <div class="booking-head rv">
      <span class="kicker"><b>Booking</b> Request an appointment</span>
      <h2><?= rich_inline(setting('booking_heading', 'Book your <em>appointment.</em>')) ?></h2>
      <p class="booking-sub"><?= e(setting('booking_sub')) ?></p>
    </div>

    <?php if ($submitted): ?>
    <div class="booking-card booking-success rv">
      <h3>Thank you — your request has been received.</h3>
      <p>A confirmation email is on its way to you, and your appointment will be confirmed by email or phone shortly.</p>
      <a href="index.php" class="btn btn-dark">Back to home <span class="arr"></span></a>
    </div>
    <?php else: ?>
    <div class="booking-grid rv">
      <form method="post" action="book.php" class="booking-card booking-form" novalidate>
        <?= csrf_field() ?>
        <p class="hp-field" aria-hidden="true">
          <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </p>

        <?php if ($errors): ?>
        <div class="booking-errors">Please fix the highlighted fields below.</div>
        <?php endif; ?>

        <div class="bf-row">
          <div class="bf-field">
            <label for="bf-name">Full name *</label>
            <input type="text" id="bf-name" name="name" maxlength="120" required value="<?= e($old['name'] ?? '') ?>">
            <?php if (isset($errors['name'])): ?><span class="bf-error"><?= e($errors['name']) ?></span><?php endif; ?>
          </div>
          <div class="bf-field">
            <label for="bf-phone">Phone *</label>
            <input type="tel" id="bf-phone" name="phone" maxlength="40" required value="<?= e($old['phone'] ?? '') ?>">
            <?php if (isset($errors['phone'])): ?><span class="bf-error"><?= e($errors['phone']) ?></span><?php endif; ?>
          </div>
        </div>

        <div class="bf-field">
          <label for="bf-email">Email *</label>
          <input type="email" id="bf-email" name="email" maxlength="190" required value="<?= e($old['email'] ?? '') ?>">
          <?php if (isset($errors['email'])): ?><span class="bf-error"><?= e($errors['email']) ?></span><?php endif; ?>
        </div>

        <div class="bf-field bf-schedule" id="bk-schedule" data-availability="<?= e(json_encode($calendarDays)) ?>" data-full-dates="<?= e(json_encode($fullDates)) ?>" data-today="<?= e(date('Y-m-d')) ?>">
          <span class="bf-label">Preferred date &amp; time *</span>

          <div class="bk-picker" id="bk-picker" hidden>
            <div class="bk-cal">
              <div class="bk-cal-head">
                <button type="button" class="bk-nav" id="bk-prev" aria-label="Previous month"><span class="bk-chev bk-chev-l"></span></button>
                <div class="bk-cal-title" id="bk-title" aria-live="polite"></div>
                <button type="button" class="bk-nav" id="bk-next" aria-label="Next month"><span class="bk-chev bk-chev-r"></span></button>
              </div>
              <div class="bk-dow" aria-hidden="true">
                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
              </div>
              <div class="bk-days" id="bk-days"></div>
              <div class="bk-legend">
                <span><i class="bk-dot bk-dot-avail"></i> Available</span>
                <span><i class="bk-dot bk-dot-sel"></i> Selected</span>
              </div>
            </div>
            <div class="bk-slots">
              <div class="bk-slots-head" id="bk-slots-head">Pick a day to see available times</div>
              <div class="bk-slot-list" id="bk-slot-list" role="listbox" aria-label="Available times"></div>
              <div class="bk-summary" id="bk-summary" hidden>
                <span class="bk-summary-ico" aria-hidden="true">&#10003;</span>
                <span id="bk-summary-text"></span>
              </div>
            </div>
          </div>

          <div class="bf-row" id="bk-native">
            <div class="bf-field">
              <label for="bf-date">Preferred date *</label>
              <input type="date" id="bf-date" name="preferred_date" required min="<?= e(date('Y-m-d')) ?>" value="<?= e($old['preferred_date'] ?? '') ?>">
            </div>
            <div class="bf-field">
              <label for="bf-time">Preferred time *</label>
              <input type="text" id="bf-time" name="preferred_time" maxlength="40" placeholder="e.g. 9:00 am" required value="<?= e($old['preferred_time'] ?? '') ?>">
            </div>
          </div>
          <?php if (isset($errors['preferred_date'])): ?><span class="bf-error"><?= e($errors['preferred_date']) ?></span><?php endif; ?>
          <?php if (isset($errors['preferred_time'])): ?><span class="bf-error"><?= e($errors['preferred_time']) ?></span><?php endif; ?>
        </div>

        <div class="bf-field">
          <span class="bf-label">Session type *</span>
          <div class="bf-radios">
            <label><input type="radio" name="session_type" value="in_person" <?= ($old['session_type'] ?? 'in_person') === 'in_person' ? 'checked' : '' ?>> In person</label>
            <label><input type="radio" name="session_type" value="telehealth" <?= ($old['session_type'] ?? '') === 'telehealth' ? 'checked' : '' ?>> Telehealth</label>
          </div>
        </div>

        <div class="bf-field">
          <label for="bf-message">Anything you'd like to share (optional)</label>
          <textarea id="bf-message" name="message" rows="4" maxlength="2000"><?= e($old['message'] ?? '') ?></textarea>
          <?php if (isset($errors['message'])): ?><span class="bf-error"><?= e($errors['message']) ?></span><?php endif; ?>
        </div>

        <button type="submit" class="btn btn-dark">Request appointment <span class="arr"></span></button>
      </form>

      <aside class="booking-card booking-aside">
        <h3>Session times</h3>
        <ul class="booking-hours">
          <?php foreach ($availability as $slot): ?>
          <li><span><?= e($slot['day_label']) ?></span><span><?= e($slot['hours_label']) ?></span></li>
          <?php endforeach; ?>
        </ul>
        <p class="booking-note"><?= e(setting('availability_note')) ?></p>
        <p class="booking-note"><?= e(setting('topbar_right')) ?></p>
      </aside>
    </div>
    <?php endif; ?>
  </div>
</section>

<script src="assets/js/booking.js?v=<?= filemtime(__DIR__ . '/assets/js/booking.js') ?>" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
