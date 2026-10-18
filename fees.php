<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/site.php';

$feeSteps   = get_rows('fee_steps');
$bookingUrl = setting('booking_url');
$pageTitle  = 'Costs & Appointments';

// Single source of truth for fee figures (shared site config) — also drives
// the JS toggle via data-fee-config.
$fees = site_fees();
$feeConfig = [
    'session' => $fees['session'],
    'rebate'  => $fees['rebate'],
];
$feeConfig['gap'] = round($feeConfig['session'] - $feeConfig['rebate'], 2);

// Fine print without the "No bulk billing" sentence — shown as its own pill below.
$priceFine = trim(preg_replace('/\s*<strong>\s*No bulk billing[^<]*<\/strong>\s*/i', ' ', setting('price_fine')));

require __DIR__ . '/includes/header.php';
?>

<!-- FEES -->
<section id="fees">
  <div class="wrap">
    <div class="fees-head rv">
      <div class="kicker"><b>i.</b> Costs &amp; appointments</div>
      <h2><?= rich_inline(setting('fees_heading')) ?></h2>
      <!-- TODO: placeholder copy -->
      <p class="fees-sub">Everything you need to know before your first session, in one place.</p>
    </div>

    <!-- At a glance — compact hairline strip, same style as the homepage stats -->
    <div class="glance rv">
      <div><b class="numeral-display">50 min</b><span>/ session</span></div>
      <div><b class="numeral-display">$<?= number_format($feeConfig['gap'], 2) ?></b><span>after Medicare</span></div>
      <div><b class="numeral-display">10</b><span>rebated sessions / yr</span></div>
      <div><b class="numeral-display"><?= e(site_session_days(true)) ?></b><span>sessions</span></div>
    </div>

    <div class="fees-grid">
      <div class="fees-l rv">
        <div class="price" data-fee-card data-fee-config='<?= e(json_encode($feeConfig)) ?>'>
          <div class="fee-toggle" role="group" aria-label="Show fees with or without a Mental Health Care Plan">
            <button type="button" aria-pressed="true" data-fee-mode="mhcp">With MHCP</button>
            <button type="button" aria-pressed="false" data-fee-mode="full">Without MHCP</button>
          </div>
          <div class="lbl" data-fee-label><?= e(setting('price_label')) ?></div>
          <div class="amt"><sup>$</sup><span data-fee-amount><?= number_format($feeConfig['gap'], 2) ?></span><small data-fee-caption>out of pocket after Medicare</small></div>
          <p class="sr-only" aria-live="polite" data-fee-live></p>
          <dl class="fee-breakdown">
            <div><dt>Session fee</dt><dd>$<?= number_format($feeConfig['session'], 2) ?></dd></div>
            <div><dt>Medicare rebate</dt><dd>&minus;&nbsp;$<?= number_format($feeConfig['rebate'], 2) ?></dd></div>
            <div class="fee-total"><dt>You pay</dt><dd>$<?= number_format($feeConfig['gap'], 2) ?></dd></div>
          </dl>
          <p class="fine"><?= rich_inline($priceFine) ?></p>
          <span class="fee-pill">No bulk billing available</span>
          <div class="fee-actions">
            <a href="<?= e($bookingUrl) ?>" class="btn btn-dark">Book a session <span class="arr"></span></a>
            <a href="cancellation.php" class="link fee-policy-link">Read the cancellation policy &rarr;</a>
          </div>
        </div>
      </div>

      <div class="steps journey" data-journey>
        <span class="journey-line" aria-hidden="true"><span class="journey-fill" data-journey-fill></span></span>
        <?php foreach ($feeSteps as $i => $step): ?>
        <div class="step" data-reveal data-reveal-delay="<?= (int) $i * 100 ?>">
          <h3><?= e($step['title']) ?></h3>
          <ul class="list"><?= render_list_items($step['items']) ?></ul>
          <?php if ($step['show_timeline']): ?>
          <div class="tl" data-timeline>
            <?php foreach (explode('|', setting('reminder_timeline')) as $slot):
                [$when, $how] = array_pad(explode(':', $slot, 2), 2, ''); ?>
            <div><b><?= e($when) ?></b><span><?= e($how) ?></span></div>
            <?php endforeach; ?>
          </div>
          <p class="aside"><?= e(setting('reminder_note')) ?></p>
          <?php endif; ?>
          <?php if ($step['flag'] !== ''): ?>
          <div class="fee-callout">
            <svg class="fee-callout-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5" stroke-linecap="round"/><path d="M12 8h.01" stroke-linecap="round"/></svg>
            <!-- TODO: placeholder wording -->
            <p><strong>Please note:</strong> <?= e(rtrim($step['flag'], '.')) ?>. <a class="link" href="cancellation.php">Cancellation policy &rarr;</a></p>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- CTA — reused homepage closing card -->
<div class="cta cta-flat">
  <div class="wrap">
    <div class="cta-box" data-reveal>
      <div class="kicker"><b>Begin</b></div>
      <!-- TODO: placeholder heading copy -->
      <h2>Ready <em>when you are.</em></h2>
      <p><?= e(setting('cta_text')) ?></p>
      <a href="<?= e($bookingUrl) ?>" class="btn btn-ivory">Book a session <span class="arr"></span></a>
      <!-- TODO: Confirm these facts with the client. -->
      <p class="cta-micro">Medicare rebates available &middot; In person or telehealth &middot; Reply within 1 business day</p>
    </div>
  </div>
</div>

<script src="assets/js/fees.js?v=<?= filemtime(__DIR__ . '/assets/js/fees.js') ?>" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
