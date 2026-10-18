<?php
require_once __DIR__ . '/includes/functions.php';

$therapies   = get_rows('therapies');
$goldRows    = get_rows('gold_standards');
$therapyMeta = require __DIR__ . '/includes/therapy_meta.php';
$bookingUrl  = setting('booking_url');
$pageTitle   = 'Therapy Types';

/** Find the meta slug for a therapy row by title match. */
function therapy_slug(array $therapy, array $meta): string
{
    foreach ($meta as $slug => $m) {
        if (stripos($therapy['title'], $m['match']) !== false) {
            return $slug;
        }
    }
    return 'therapy-' . (int) $therapy['id'];
}

/** Escape a title, rendering ® as a small superscript. */
function therapy_title(string $title): string
{
    return str_replace('®', '<sup class="reg">®</sup>', e($title));
}

require __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="th-hero">
  <div class="wrap">
    <div class="th-head">
      <div class="rv">
        <div class="eyebrow"><b>i.</b> Therapy types</div>
        <h2><?= rich_inline(setting('therapies_heading')) ?></h2>
      </div>
      <p class="rv"><?= e(setting('therapies_sub')) ?></p>
    </div>
  </div>
</section>

<!-- GOLD STANDARDS — same data source as the homepage cards -->
<section class="gold-sec th-gold">
  <div class="wrap">
    <div class="eyebrow rv"><b>ii.</b> The foundation</div>
    <div class="gold-pair">
      <?php foreach ($goldRows as $i => $row):
        $bodyHtml  = render_body($row['body']);
        $firstPara = $bodyHtml;
        $restHtml  = '';
        $cut = strpos($bodyHtml, '</p>');
        if ($cut !== false && strlen($bodyHtml) > $cut + 4) {
            $firstPara = substr($bodyHtml, 0, $cut + 4);
            $restHtml  = substr($bodyHtml, $cut + 4);
        }
        $isEmdr  = stripos($row['title'] . ' ' . $row['full_name'], 'EMDR') !== false;
        $goldId  = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $row['title']), '-'));
        $moreId  = 'th-gold-more-' . ((int) $i + 1);
      ?>
      <article class="gold-row gold-card" id="<?= e($goldId) ?>" data-reveal data-reveal-delay="<?= (int) $i * 150 ?>">
        <div class="num numeral-display"><?= e($row['num_label']) ?></div>
        <h3><?= e($row['title']) ?></h3>
        <div class="full"><?= rich_inline($row['full_name']) ?></div>
        <?php if ($isEmdr): ?>
        <!-- Signature detail: bilateral-stimulation dot gliding along a hairline -->
        <div class="bls" aria-hidden="true"><span class="bls-dot"></span></div>
        <?php endif; ?>
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
    <p class="gold-standards-note rv"><em><?= e(setting('gold_note_em')) ?></em></p>
  </div>
</section>

<!-- SUPPORTING THERAPIES -->
<section id="supporting">
  <div class="wrap">
    <div class="th-head th-head-sub">
      <div class="rv">
        <div class="eyebrow"><b>iii.</b> Woven in</div>
        <h2>Supporting <em>therapies</em></h2>
      </div>
      <p class="rv">Brought in gently, depending on how each session is going.</p>
    </div>

    <div class="tacc">
      <?php foreach ($therapies as $i => $therapy):
        $slug    = therapy_slug($therapy, $therapyMeta);
        $meta    = $therapyMeta[$slug] ?? null;
        $panelId = 'panel-' . $slug;
        $btnId   = 'tab-' . $slug;
        $title   = therapy_title($therapy['title']);
      ?>
      <div class="tacc-item" id="<?= e($slug) ?>" data-reveal data-reveal-delay="<?= (int) $i * 60 ?>">
        <h3 class="tacc-h">
          <button type="button" class="tacc-btn" id="<?= e($btnId) ?>" aria-expanded="false" aria-controls="<?= e($panelId) ?>">
            <span class="tacc-num">
              <span class="i numeral-display"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
              <?php if ($meta): ?><?= $meta['icon'] ?><?php endif; ?>
            </span>
            <span class="tacc-t">
              <span class="tacc-title"><?= $title ?></span>
              <span class="tacc-sub-m"><?= e($therapy['subtitle']) ?></span>
            </span>
            <span class="tacc-s"><?= e($therapy['subtitle']) ?></span>
            <span class="tacc-pm" aria-hidden="true"></span>
          </button>
        </h3>
        <div class="tacc-panel" id="<?= e($panelId) ?>" role="region" aria-labelledby="<?= e($btnId) ?>">
          <div class="tacc-panel-inner">
            <div class="tacc-grid">
              <div class="tacc-text"><?= render_body($therapy['body']) ?></div>
              <?php if ($meta): ?>
              <aside class="tacc-side">
                <div class="tacc-side-row">
                  <h4>Helps with</h4>
                  <ul class="tacc-pills">
                    <?php foreach ($meta['helps'] as $tag): ?><li><?= e($tag) ?></li><?php endforeach; ?>
                  </ul>
                </div>
                <div class="tacc-side-row">
                  <h4>In a session</h4>
                  <p><?= e($meta['session']) ?></p>
                </div>
                <div class="tacc-side-row">
                  <h4>Works well with</h4>
                  <ul class="tacc-links">
                    <?php foreach ($meta['works_with'] as $other): if (!isset($therapyMeta[$other])) continue; ?>
                    <li><a class="link" href="#<?= e($other) ?>"><?= therapy_title($therapyMeta[$other]['label']) ?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </aside>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<div class="cta cta-flat">
  <div class="wrap">
    <div class="cta-box">
      <div class="eyebrow"><b>Begin</b></div>
      <h2>Not sure which is <em>right for you?</em></h2>
      <p>You don't need to know. We'll work it out together.</p>
      <a href="<?= e($bookingUrl) ?>" class="btn btn-ivory">Book an appointment <span class="arr"></span></a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
