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
        <?php
        // Each word in the rotating ring links to its therapy anchor.
        $orbitSlugs = [
            'emdr' => 'emdr', 'internal family systems' => 'ifs', 'brainspotting' => 'brainspotting',
            'irest' => 'irest', 'expressive arts' => 'expressive-arts', 'schema' => 'schema',
            'hrv' => 'hrv', 'personal growth' => 'personal-growth', 'tf-cbt' => 'tf-cbt',
        ];
        $orbitLabels = array_values(array_filter(array_map('trim', explode('·', setting('hero_orbit_text')))));
        $orbitHtml = '';
        foreach ($orbitLabels as $label) {
            $key = strtolower(str_replace('®', '', $label));
            $orbitHtml .= isset($orbitSlugs[$key])
                ? '<a href="therapies.php#' . e($orbitSlugs[$key]) . '">' . e($label) . '</a> · '
                : e($label) . ' · ';
        }
        ?>
        <div class="orbit-wrap" role="navigation" aria-label="Therapies: <?= e(implode(', ', $orbitLabels)) ?>">
          <svg class="orbit" viewBox="0 0 400 400">
            <defs><path id="circ" d="M200,200 m-188,0 a188,188 0 1,1 376,0 a188,188 0 1,1 -376,0"/></defs>
            <text><textPath href="#circ"><?= $orbitHtml ?></textPath></text>
          </svg>
        </div>
        <div class="seal"><small><?= e(setting('hero_seal_small')) ?></small><b><?= e(setting('hero_seal_bold')) ?></b></div>
      </div>
    </div>

    <?php if ($heroStats): ?>
    <div class="hero-foot rv" data-stats>
      <?php foreach ($heroStats as $stat): ?>
      <div><b class="numeral-display" data-count="<?= e($stat['stat_value']) ?>"><?= e($stat['stat_value']) ?></b><span><?= e($stat['stat_label']) ?></span></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- MARQUEE -->
<?php if ($marquee): ?>
<div class="marquee" aria-label="Registrations">
  <div class="track">
    <div class="track-group">
      <?php foreach ($marquee as $item): ?><span><?= e($item['label']) ?></span><?php endforeach; ?>
    </div>
    <div class="track-group" aria-hidden="true">
      <?php foreach ($marquee as $item): ?><span><?= e($item['label']) ?></span><?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- INTRO -->
<section class="intro">
  <div class="wrap intro-grid">
    <div data-reveal>
      <div class="kicker"><b>i.</b> The approach</div>
      <h2><?= rich_inline(setting('intro_heading')) ?></h2>
    </div>
    <div data-reveal data-reveal-delay="150">
      <p class="big"><?= e(setting('intro_big')) ?></p>
      <p class="sm"><?= e(setting('intro_small')) ?></p>
    </div>
  </div>
</section>

<!-- GOLD STANDARDS -->
<section class="gold-sec">
  <div class="gold-circle" aria-hidden="true" data-parallax="30"></div>
  <div class="gold-circle gold-circle-sm" aria-hidden="true" data-parallax="18"></div>
  <div class="wrap">
    <div class="kicker rv"><b>ii.</b> The gold standards</div>
    <h2 class="rv"><?= rich_inline(setting('gold_heading')) ?></h2>

    <div class="gold-stack">
    <?php foreach ($goldRows as $i => $row):
      $bodyHtml  = render_body($row['body']);
      $firstPara = $bodyHtml;
      $restHtml  = '';
      $cut = strpos($bodyHtml, '</p>');
      if ($cut !== false && strlen($bodyHtml) > $cut + 4) {
          $firstPara = substr($bodyHtml, 0, $cut + 4);
          $restHtml  = substr($bodyHtml, $cut + 4);
      }
      $isEmdr = stripos($row['title'] . ' ' . $row['full_name'], 'EMDR') !== false;
      $moreId = 'gold-more-' . ((int) $i + 1);
    ?>
    <article class="gold-row rv" style="--stick-top:<?= 120 + (int) $i * 20 ?>px">
      <div class="num numeral-display"><?= e($row['num_label']) ?></div>
      <div>
        <h3><?= e($row['title']) ?></h3>
        <div class="full"><?= rich_inline($row['full_name']) ?></div>
        <?php if ($isEmdr): ?>
        <!-- Signature detail: bilateral-stimulation dot gliding along a hairline -->
        <div class="bls" aria-hidden="true"><span class="bls-dot"></span></div>
        <?php endif; ?>
      </div>
      <div class="gold-body">
        <?= $firstPara ?>
        <?php if ($restHtml !== ''): ?>
        <div class="disclose" id="<?= e($moreId) ?>">
          <div class="disclose-inner"><?= $restHtml ?></div>
        </div>
        <button type="button" class="read-more" aria-expanded="false" aria-controls="<?= e($moreId) ?>" data-more="Read more" data-less="Read less">Read more</button>
        <?php endif; ?>
      </div>
    </article>
    <?php endforeach; ?>
    </div>

    <div class="gold-note rv">
      <em><?= e(setting('gold_note_em')) ?></em>
      <span><?= e(setting('gold_note_text')) ?></span>
    </div>
  </div>
</section>

<!-- WHAT TO EXPECT -->
<section class="expect">
  <div class="wrap">
    <div class="kicker rv"><b>iii.</b> What to expect</div>
    <!-- TODO: Confirm heading and step copy with the client. -->
    <h2 class="rv">Your first steps, <em>gently paced</em></h2>
    <ol class="expect-steps">
      <li data-reveal>
        <span class="expect-num numeral-display" aria-hidden="true">01</span>
        <h3>Book online</h3>
        <p>Choose a time that suits you — no referral needed to get started. <!-- TODO: placeholder copy --></p>
      </li>
      <li data-reveal data-reveal-delay="150">
        <span class="expect-num numeral-display" aria-hidden="true">02</span>
        <h3>Your first session</h3>
        <p>A gentle, unhurried conversation about what brings you here and what you'd like to change. <!-- TODO: placeholder copy --></p>
      </li>
      <li data-reveal data-reveal-delay="300">
        <span class="expect-num numeral-display" aria-hidden="true">03</span>
        <h3>Ongoing care</h3>
        <p>A plan shaped around you, at a pace you set — in person or via telehealth. <!-- TODO: placeholder copy --></p>
      </li>
    </ol>
  </div>
</section>

<!-- MEET PATRICIA -->
<section class="meet">
  <div class="wrap meet-grid">
    <div class="meet-figure" data-reveal>
      <div class="meet-photo">
        <!-- TODO: Use a second photo, different from the hero — a candid shot of Patricia. -->
        <img src="<?= e(setting('meet_photo_url') ?: setting('hero_photo_url')) ?>" alt="<?= e(setting('hero_photo_alt')) ?>">
      </div>
    </div>
    <div data-reveal data-reveal-delay="150">
      <div class="kicker"><b>iv.</b> Meet Patricia</div>
      <!-- TODO: Confirm bio copy and credentials with the client. -->
      <h2>Care shaped by <em>experience</em></h2>
      <p class="meet-bio">Patricia is a trauma-focused psychologist dedicated to evidence-based care delivered with warmth and patience. <!-- TODO: placeholder bio --></p>
      <p class="meet-bio">She works alongside you at a pace that feels safe, drawing primarily on EMDR to help you move from surviving to living well. <!-- TODO: placeholder bio --></p>
      <ul class="meet-creds">
        <li>Accredited EMDR Therapist</li>
        <li>Registered Psychologist</li>
        <li>15 years in practice — psychology is her fourth career</li>
      </ul>
      <a href="about.php" class="link">More about Patricia</a>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq">
  <div class="wrap faq-grid">
    <div class="faq-intro rv">
      <div class="kicker"><b>v.</b> Questions</div>
      <h2>Frequently asked <em>questions</em></h2>
      <!-- TODO: Confirm supporting copy with the client. -->
      <p class="faq-sub">Short answers to the things people most often ask before their first session.</p>
      <p class="faq-contact">Still have questions? <a class="link" href="<?= e($bookingUrl) ?>">Get in touch</a></p>
    </div>
    <div class="faq-list">
      <?php
      // TODO: Confirm FAQ answers with the client.
      $faqs = [
          ['Do you offer Medicare rebates?', 'Yes — with a Mental Health Treatment Plan from your GP, Medicare rebates apply to eligible sessions. <!-- TODO: placeholder copy -->'],
          ['Do I need a referral?', 'No referral is needed to book, though a GP referral is required to claim Medicare rebates. <!-- TODO: placeholder copy -->'],
          ['Can I attend by telehealth?', 'Yes — sessions are available in person or via secure telehealth, whichever feels most comfortable. <!-- TODO: placeholder copy -->'],
          ["What if I'm not ready to talk about it?", "That's okay. You set the pace — therapy can begin with stabilisation and grounding long before any trauma is discussed directly. <!-- TODO: placeholder copy -->"],
      ];
      foreach ($faqs as $fi => $faq): $fid = 'faq-panel-' . ((int) $fi + 1); ?>
      <div class="faq-item" data-reveal data-reveal-delay="<?= (int) $fi * 100 ?>">
        <h3>
          <button type="button" class="faq-q" aria-expanded="false" aria-controls="<?= e($fid) ?>">
            <?= e($faq[0]) ?>
            <span class="faq-pm" aria-hidden="true"></span>
          </button>
        </h3>
        <div class="disclose" id="<?= e($fid) ?>">
          <div class="disclose-inner"><p><?= $faq[1] ?></p></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- QUOTE -->
<section class="quote">
  <div class="wrap rv">
    <span class="mark" aria-hidden="true">&ldquo;</span>
    <blockquote data-words><?= rich_inline(setting('quote_text')) ?></blockquote>
    <?php
    $citeParts = preg_split('/\s*(?:—|–|,|·)\s*/u', setting('quote_cite'), 2);
    $quoteName = trim($citeParts[0] ?? '') !== '' ? trim($citeParts[0]) : 'Patricia';
    $quoteRole = trim($citeParts[1] ?? '');
    ?>
    <div class="quote-attrib">
      <!-- TODO: Replace with a dedicated 48px portrait of Patricia. -->
      <img class="quote-avatar" src="<?= e(setting('hero_photo_url')) ?>" alt="">
      <div>
        <cite class="quote-name"><?= e($quoteName) ?></cite>
        <?php if ($quoteRole !== ''): ?><span class="quote-role"><?= e($quoteRole) ?></span><?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<div class="cta">
  <div class="wrap">
    <div class="cta-box">
      <div class="kicker"><b>Begin</b></div>
      <h2><?= rich_inline(setting('cta_heading')) ?></h2>
      <p><?= e(setting('cta_text')) ?></p>
      <a href="<?= e($bookingUrl) ?>" class="btn btn-ivory">Book an appointment <span class="arr"></span></a>
      <!-- TODO: Confirm these facts with the client. -->
      <p class="cta-micro">Medicare rebates available &middot; In person or telehealth &middot; Reply within 1 business day</p>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
