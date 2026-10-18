<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/site.php';

$bookingUrl = setting('booking_url');
$pageTitle  = 'Cancellation Policy';
// Shell page background so the header band matches the page (no seam).
$bodyClass  = 'page-shell';

$fees    = site_fees();
$contact = site_contact();

// Fee scale stops — amounts come from the shared fees config
// (same sessionFee as the costs page).
$scaleStops = [
    ['notice' => '3+ business days',          'fee' => 'No fee',                                              'note' => 'Nothing charged'],
    ['notice' => '2 business days',           'fee' => '$' . number_format($fees['lateCancel']),              'note' => "If the appointment isn't filled"],
    ['notice' => 'Less than 2 business days', 'fee' => 'Full fee ($' . number_format($fees['session']) . ')', 'note' => "If the appointment isn't filled"],
];

require __DIR__ . '/includes/header.php';
?>

<!-- CANCELLATION -->
<section class="cancel" id="cancellation">
  <div class="wrap">
    <div class="kicker rv"><b>i.</b> Cancellation policy</div>
    <h2 class="rv"><?= rich_inline(setting('cancel_heading')) ?></h2>
    <p class="sub rv"><?= e(setting('cancel_sub')) ?></p>

    <!-- Fee scale — one connected timeline card -->
    <div class="fee-scale rv" data-fee-scale>
      <ol class="fee-track">
        <?php foreach ($scaleStops as $stop): ?>
        <li class="fee-stop">
          <span class="fee-stop-dot" aria-hidden="true"></span>
          <small>Notice given</small>
          <h3><?= e($stop['notice']) ?></h3>
          <b class="fee numeral-display"><?= e($stop['fee']) ?></b>
          <span class="fee-note"><?= e($stop['note']) ?></span>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>

    <div class="c-grid">
      <div class="rv">
        <h4 class="eyebrow">Why it matters</h4>
        <!-- TODO: Consider a warmer framing for a trauma audience, e.g. "Your appointment time is held just for you." -->
        <p class="c-intro"><?= e(setting('cancel_why_intro')) ?></p>
        <ul class="three">
          <?php $romans = ['i.', 'ii.', 'iii.', 'iv.', 'v.'];
          foreach (preg_split('/\R/', trim(setting('cancel_why_items'))) as $i => $item): if (trim($item) === '') continue; ?>
          <li><b><?= e($romans[$i] ?? ($i + 1) . '.') ?></b><?= rich_inline(trim($item)) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="rv">
        <h4 class="eyebrow">Fees &amp; payment</h4>
        <ul class="list-dots">
          <?= render_list_items(setting('cancel_settle_items')) ?>
          <li>If an initial appointment is missed, <strong>further appointments won't be available</strong> — <a class="link" href="fees.php">see costs &amp; appointments</a></li>
        </ul>
      </div>
      <div class="rv">
        <h4 class="eyebrow">When fees are waived</h4>
        <div class="waived-panel">
          <ul class="list"><?= render_list_items(setting('cancel_waived_items')) ?></ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- NEED TO CANCEL OR RESCHEDULE? — shared CTA card -->
<div class="cta cta-flat">
  <div class="wrap">
    <div class="cta-box" data-reveal>
      <div class="kicker"><b>Contact</b></div>
      <h2>Need to <em>cancel or reschedule?</em></h2>
      <p>Let me know as early as you can. A quick email or text is all it takes.</p>
      <!-- TODO: Add real email + mobile. -->
      <div class="cta-actions">
        <a href="mailto:<?= e($contact['email']) ?>" class="btn btn-ivory">Email Patricia <span class="arr"></span></a>
        <a href="sms:<?= e($contact['mobile']) ?>" class="btn btn-outline">Send a text</a>
      </div>
      <p class="cta-micro">Same-day notice? Please still get in touch. Extenuating circumstances are always considered.</p>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
