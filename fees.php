<?php
require_once __DIR__ . '/includes/functions.php';

$feeSteps   = get_rows('fee_steps');
$bookingUrl = setting('booking_url');
$pageTitle  = 'Costs & Appointments';

require __DIR__ . '/includes/header.php';
?>

<!-- FEES -->
<section id="fees">
  <div class="wrap fees-grid">
    <div class="fees-l rv">
      <div class="kicker"><b>i.</b> Costs &amp; appointments</div>
      <h2><?= rich_inline(setting('fees_heading')) ?></h2>
      <div class="price">
        <div class="lbl"><?= e(setting('price_label')) ?></div>
        <div class="amt"><sup>$</sup><?= e(setting('price_amount')) ?><small><?= e(setting('price_unit')) ?></small></div>
        <div class="oop"><span><?= e(setting('price_oop_label')) ?></span><b><?= e(setting('price_oop_value')) ?></b></div>
        <p class="fine"><?= rich_inline(setting('price_fine')) ?></p>
        <a href="<?= e($bookingUrl) ?>" class="btn btn-dark">Book a session <span class="arr"></span></a>
      </div>
    </div>

    <div class="steps rv">
      <?php foreach ($feeSteps as $step): ?>
      <div class="step">
        <h3><?= e($step['title']) ?></h3>
        <ul class="list"><?= render_list_items($step['items']) ?></ul>
        <?php if ($step['show_timeline']): ?>
        <div class="tl">
          <?php foreach (explode('|', setting('reminder_timeline')) as $slot):
              [$when, $how] = array_pad(explode(':', $slot, 2), 2, ''); ?>
          <div><b><?= e($when) ?></b><span><?= e($how) ?></span></div>
          <?php endforeach; ?>
        </div>
        <p class="aside"><?= e(setting('reminder_note')) ?></p>
        <?php endif; ?>
        <?php if ($step['flag'] !== ''): ?>
        <span class="flag"><?= e($step['flag']) ?></span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
