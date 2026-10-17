<?php
require_once __DIR__ . '/includes/functions.php';

$heroStats   = get_rows('hero_stats');
$marquee     = get_rows('marquee_items');
$goldRows    = get_rows('gold_standards');
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
          <a href="therapies.php" class="link">Explore therapies</a>
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
      <div><b class="numeral-display"><?= e($stat['stat_value']) ?></b><span><?= e($stat['stat_label']) ?></span></div>
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
      <div class="num numeral-display"><?= e($row['num_label']) ?></div>
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

<!-- QUOTE -->
<section class="quote">
  <div class="wrap rv">
    <span class="mark">&ldquo;</span>
    <blockquote><?= rich_inline(setting('quote_text')) ?></blockquote>
    <cite><?= e(setting('quote_cite')) ?></cite>
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
