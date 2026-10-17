<?php
require_once __DIR__ . '/includes/functions.php';

$cancelTiers = get_rows('cancellation_tiers');
$bookingUrl  = setting('booking_url');
$pageTitle   = 'Cancellation Policy';

require __DIR__ . '/includes/header.php';
?>

<!-- CANCELLATION -->
<section class="cancel" id="cancellation">
  <div class="wrap">
    <div class="kicker rv"><b>i.</b> Cancellation policy</div>
    <h2 class="rv"><?= rich_inline(setting('cancel_heading')) ?></h2>
    <p class="sub rv"><?= e(setting('cancel_sub')) ?></p>

    <div class="scale rv">
      <?php foreach ($cancelTiers as $tier): ?>
      <div><small>Notice given</small><h3><?= e($tier['notice_label']) ?></h3><div class="fee"><?= e($tier['fee_label']) ?><span><?= e($tier['fee_note']) ?></span></div></div>
      <?php endforeach; ?>
    </div>

    <div class="c-grid">
      <div class="rv">
        <h4>Why it matters</h4>
        <p style="color:var(--muted);margin-bottom:10px"><?= e(setting('cancel_why_intro')) ?></p>
        <ul class="three">
          <?php $romans = ['i.', 'ii.', 'iii.', 'iv.', 'v.'];
          foreach (preg_split('/\R/', trim(setting('cancel_why_items'))) as $i => $item): if (trim($item) === '') continue; ?>
          <li><b><?= e($romans[$i] ?? ($i + 1) . '.') ?></b><?= rich_inline(trim($item)) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="rv">
        <h4>Settling fees</h4>
        <ul class="list"><?= render_list_items(setting('cancel_settle_items')) ?></ul>
      </div>
      <div class="rv">
        <h4>When fees are waived</h4>
        <ul class="list"><?= render_list_items(setting('cancel_waived_items')) ?></ul>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
