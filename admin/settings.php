<?php
require_once __DIR__ . '/auth.php';
require_admin();

$pageTitle = 'Site Settings';

$groups = [
    'General' => [
        'site_title'       => ['Site title', 'text'],
        'meta_description' => ['Meta description', 'textarea'],
        'logo_url'         => ['Logo URL', 'text'],
        'favicon_url'      => ['Favicon URL', 'text'],
        'booking_url'      => ['Booking URL', 'text'],
        'topbar_left'      => ['Top bar — left text', 'text'],
        'topbar_right'     => ['Top bar — right text', 'text'],
    ],
    'Hero' => [
        'hero_kicker_b'    => ['Kicker word', 'text'],
        'hero_kicker_text' => ['Kicker text', 'text'],
        'hero_line1'       => ['Heading line 1', 'text'],
        'hero_line2'       => ['Heading line 2', 'text'],
        'hero_line3'       => ['Heading line 3', 'text'],
        'hero_lead'        => ['Lead paragraph', 'textarea'],
        'hero_photo_url'   => ['Photo URL', 'text'],
        'hero_photo_alt'   => ['Photo alt text', 'text'],
        'hero_orbit_text'  => ['Orbit text', 'text'],
        'hero_seal_small'  => ['Seal — small text', 'text'],
        'hero_seal_bold'   => ['Seal — bold text', 'text'],
    ],
    'Intro & Gold Standards' => [
        'intro_heading'  => ['Intro heading', 'text'],
        'intro_big'      => ['Intro — big paragraph', 'textarea'],
        'intro_small'    => ['Intro — small paragraph', 'textarea'],
        'gold_heading'   => ['Gold standards heading', 'text'],
        'gold_note_em'   => ['Gold note — emphasised', 'text'],
        'gold_note_text' => ['Gold note — text', 'text'],
    ],
    'Therapies & Quote' => [
        'therapies_heading' => ['Therapies heading', 'text'],
        'therapies_sub'     => ['Therapies subheading', 'textarea'],
        'quote_text'        => ['Quote text', 'textarea'],
        'quote_cite'        => ['Quote attribution', 'text'],
    ],
    'Fees' => [
        'fees_heading'      => ['Fees heading', 'text'],
        'price_label'       => ['Price label', 'text'],
        'price_amount'      => ['Price amount (number only)', 'text'],
        'price_unit'        => ['Price unit', 'text'],
        'price_oop_label'   => ['Out-of-pocket label', 'text'],
        'price_oop_value'   => ['Out-of-pocket value', 'text'],
        'price_fine'        => ['Fine print', 'textarea'],
        'reminder_timeline' => ['Reminder timeline (format: 2w:Email|1w:Email|…)', 'text'],
        'reminder_note'     => ['Reminder note', 'textarea'],
    ],
    'Cancellation' => [
        'cancel_heading'      => ['Heading', 'text'],
        'cancel_sub'          => ['Subheading', 'textarea'],
        'cancel_why_intro'    => ['"Why it matters" intro', 'text'],
        'cancel_why_items'    => ['"Why it matters" items (one per line)', 'textarea'],
        'cancel_settle_items' => ['"Settling fees" items (one per line)', 'textarea'],
        'cancel_waived_items' => ['"Fees waived" items (one per line)', 'textarea'],
    ],
    'About & CTA' => [
        'about_heading'     => ['About heading', 'text'],
        'availability_note' => ['Availability note', 'textarea'],
        'cta_heading'       => ['CTA heading', 'text'],
        'cta_text'          => ['CTA text', 'textarea'],
    ],
    'Footer' => [
        'footer_about'         => ['Footer about text', 'textarea'],
        'footer_sessions_days' => ['Session days', 'text'],
        'footer_sessions_hours'=> ['Session hours', 'text'],
        'crisis_line1'         => ['Crisis line 1', 'text'],
        'crisis_line2'         => ['Crisis line 2', 'text'],
        'footer_big_word'      => ['Big background word', 'text'],
        'footer_copyright'     => ['Copyright line', 'text'],
        'footer_credit'        => ['Credit line', 'text'],
    ],
];

$allKeys = [];
foreach ($groups as $fields) {
    $allKeys = array_merge($allKeys, array_keys($fields));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($allKeys as $key) {
        if (isset($_POST[$key])) {
            $stmt->execute([$key, trim($_POST[$key])]);
        }
    }
    flash_set('success', 'Settings saved.');
    redirect('settings.php');
}

require __DIR__ . '/includes/admin_header.php';
?>
<h1>Site Settings</h1>
<p class="muted">Allowed inline tags in text: <code>&lt;em&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;b&gt;</code>, <code>&lt;i&gt;</code>, <code>&lt;u&gt;</code>, <code>&lt;br&gt;</code>. Everything else is escaped.</p>

<form method="post" action="settings.php" class="settings-form">
  <?= csrf_field() ?>
  <?php foreach ($groups as $groupName => $fields): ?>
  <details class="settings-group" <?= $groupName === 'General' ? 'open' : '' ?>>
    <summary><?= e($groupName) ?></summary>
    <div class="group-body">
      <?php foreach ($fields as $key => [$label, $type]): ?>
      <div class="field">
        <label for="<?= e($key) ?>"><?= e($label) ?></label>
        <?php if ($type === 'textarea'): ?>
        <textarea id="<?= e($key) ?>" name="<?= e($key) ?>" rows="3"><?= e(setting($key)) ?></textarea>
        <?php else: ?>
        <input type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e(setting($key)) ?>">
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </details>
  <?php endforeach; ?>
  <button type="submit" class="btn-primary">Save all settings</button>
</form>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
