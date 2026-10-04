<?php
require_once __DIR__ . '/includes/functions.php';

$heroStats   = get_rows('hero_stats');
$marquee     = get_rows('marquee_items');
$goldRows    = get_rows('gold_standards');
$therapies   = get_rows('therapies');
$feeSteps    = get_rows('fee_steps');
$cancelTiers = get_rows('cancellation_tiers');
$availability = get_rows('availability');
$registrations = array_filter(get_rows('qualifications'), fn ($q) => $q['category'] === 'registrations');
$training      = array_filter(get_rows('qualifications'), fn ($q) => $q['category'] === 'training');
$bookingUrl = setting('booking_url');

require __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
  <div class="wrap">
    <div class="hero-grid">
      <div>
        <div class="kicker rv"><b><?= e(setting('hero_kicker_b')) ?></b> <?= e(setting('hero_kicker_text')) ?></div>
        <h1>
          <span class="ln"><span><?= rich_inline(setting('hero_line1')) ?></span></span>
          <span class="ln"><span><?= rich_inline(setting('hero_line2')) ?></span></span>
          <span class="ln"><span><?= rich_inline(setting('hero_line3')) ?></span></span>
        </h1>
        <p class="lead rv"><?= e(setting('hero_lead')) ?></p>
        <div class="actions rv">
          <a href="<?= e($bookingUrl) ?>" class="btn btn-dark">Book an appointment <span class="arr"></span></a>
          <a href="#therapies" class="link">Explore therapies</a>
        </div>
      </div>

      <div class="figure rv">
        <div class="disc"></div>
        <div class="photo">
          <img src="<?= e(setting('hero_photo_url')) ?>" alt="<?= e(setting('hero_photo_alt')) ?>">
        </div>
        <svg class="orbit" viewBox="0 0 400 400" aria-hidden="true">
          <defs><path id="circ" d="M200,200 m-188,0 a188,188 0 1,1 376,0 a188,188 0 1,1 -376,0"/></defs>
          <text><textPath href="#circ"><?= e(setting('hero_orbit_text')) ?></textPath></text>
        </svg>
        <div class="seal"><small><?= e(setting('hero_seal_small')) ?></small><b><?= e(setting('hero_seal_bold')) ?></b></div>
      </div>
    </div>

    <?php if ($heroStats): ?>
    <div class="hero-foot rv">
      <?php foreach ($heroStats as $stat): ?>
      <div><b><?= e($stat['stat_value']) ?></b><span><?= e($stat['stat_label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- MARQUEE -->
<?php if ($marquee): ?>
<div class="marquee" aria-label="Registrations">
  <div class="track">
    <?php for ($i = 0; $i < 2; $i++): foreach ($marquee as $item): ?><span><?= e($item['label']) ?></span><?php endforeach; endfor; ?>
  </div>
</div>
<?php endif; ?>

<!-- INTRO -->
<section class="intro">
  <div class="wrap intro-grid">
    <div class="rv">
      <div class="kicker"><b>i.</b> The approach</div>
      <h2><?= rich_inline(setting('intro_heading')) ?></h2>
    </div>
    <div class="rv">
      <p class="big"><?= e(setting('intro_big')) ?></p>
      <p class="sm"><?= e(setting('intro_small')) ?></p>
    </div>
  </div>
</section>

<!-- GOLD STANDARDS -->
<section class="gold-sec">
  <div class="wrap">
    <div class="kicker rv"><b>ii.</b> The gold standards</div>
    <h2 class="rv"><?= rich_inline(setting('gold_heading')) ?></h2>

    <?php foreach ($goldRows as $row): ?>
    <div class="gold-row rv">
      <div class="num"><?= e($row['num_label']) ?></div>
      <div>
        <h3><?= e($row['title']) ?></h3>
        <div class="full"><?= rich_inline($row['full_name']) ?></div>
      </div>
      <div><?= render_body($row['body']) ?></div>
    </div>
    <?php endforeach; ?>

    <div class="gold-note rv">
      <em><?= e(setting('gold_note_em')) ?></em>
      <span><?= e(setting('gold_note_text')) ?></span>
    </div>
  </div>
</section>

<!-- THERAPIES -->
<section id="therapies">
  <div class="wrap">
    <div class="th-head">
      <div class="rv">
        <div class="kicker"><b>iii.</b> Therapy types</div>
        <h2><?= rich_inline(setting('therapies_heading')) ?></h2>
      </div>
      <p class="rv"><?= e(setting('therapies_sub')) ?></p>
    </div>

    <div class="acc rv">
      <?php foreach ($therapies as $i => $therapy): ?>
      <details>
        <summary><span class="i"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span><h3><?= e($therapy['title']) ?></h3><span class="s"><?= e($therapy['subtitle']) ?></span><span class="pm"></span></summary>
        <div class="body"><div><?= render_body($therapy['body']) ?></div></div>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- QUOTE -->
<section class="quote">
  <div class="wrap rv">
    <span class="mark">&ldquo;</span>
    <blockquote><?= rich_inline(setting('quote_text')) ?></blockquote>
    <cite><?= e(setting('quote_cite')) ?></cite>
  </div>
</section>

<!-- FEES -->
<section id="fees">
  <div class="wrap fees-grid">
    <div class="fees-l rv">
      <div class="kicker"><b>iv.</b> Costs &amp; appointments</div>
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

<!-- CANCELLATION -->
<section class="cancel" id="cancellation">
  <div class="wrap">
    <div class="kicker rv"><b>v.</b> Cancellation policy</div>
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

<!-- ABOUT -->
<section id="about">
  <div class="wrap about-grid">
    <aside class="avail rv">
      <div class="kicker"><b>vi.</b> Sessions</div>
      <h3>My <em>availability</em></h3>
      <?php foreach ($availability as $slot): ?>
      <div class="slot"><span><?= e($slot['day_label']) ?></span><span><?= e($slot['hours_label']) ?></span></div>
      <?php endforeach; ?>
      <p class="age"><?= e(setting('availability_note')) ?></p>
      <a href="<?= e($bookingUrl) ?>" class="btn btn-ivory">Book appointment <span class="arr"></span></a>
    </aside>
    <div class="creds rv">
      <div class="kicker"><b>vii.</b> Qualifications</div>
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

<!-- CTA -->
<div class="cta">
  <div class="wrap">
    <div class="cta-box rv">
      <div class="kicker"><b>Begin</b></div>
      <h2><?= rich_inline(setting('cta_heading')) ?></h2>
      <p><?= e(setting('cta_text')) ?></p>
      <a href="<?= e($bookingUrl) ?>" class="btn btn-ivory">Book an appointment <span class="arr"></span></a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
