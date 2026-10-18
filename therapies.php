<?php
require_once __DIR__ . '/includes/functions.php';

$therapies  = get_rows('therapies');
$bookingUrl = setting('booking_url');
$pageTitle  = 'Therapy Types';

/** Deep-link anchors for each therapy (linked from the About page). */
function therapy_slug(string $title): string
{
    $map = [
        'internal family systems' => 'ifs',
        'brainspotting'           => 'brainspotting',
        'schema therapy'          => 'schema',
        'hrv biofeedback'         => 'hrv',
        'irest®'                  => 'irest',
        'expressive arts'         => 'expressive-arts',
        'personal growth'         => 'enneagram',
    ];
    $key = strtolower(trim($title));
    if (isset($map[$key])) {
        return $map[$key];
    }
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $title), '-'));
    return $slug !== '' ? $slug : 'therapy';
}

require __DIR__ . '/includes/header.php';
?>

<!-- THERAPIES -->
<section id="therapies">
  <div class="wrap">
    <div class="th-head">
      <div class="rv">
        <div class="kicker"><b>i.</b> Therapy types</div>
        <h2><?= rich_inline(setting('therapies_heading')) ?></h2>
      </div>
      <p class="rv"><?= e(setting('therapies_sub')) ?></p>
    </div>

    <div class="acc rv">
      <?php foreach ($therapies as $i => $therapy): ?>
      <details id="<?= e(therapy_slug($therapy['title'])) ?>">
        <summary><span class="i"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span><h3><?= e($therapy['title']) ?></h3><span class="s"><?= e($therapy['subtitle']) ?></span><span class="pm"></span></summary>
        <div class="body"><div><?= render_body($therapy['body']) ?></div></div>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
