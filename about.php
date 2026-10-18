<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/booking.php';

$availability = booking_session_times();
$bookingUrl   = setting('booking_url');
$pageTitle    = 'About';

require __DIR__ . '/includes/header.php';
?>

<!-- MEET PATRICIA — about hero -->
<section class="hero about-hero">
  <div class="wrap">
    <div class="about-hero-grid">
      <div class="figure" data-reveal>
        <div class="disc"></div>
        <div class="photo">
          <!-- TODO: Use a different photo from the homepage — a candid shot of Patricia. -->
          <img src="<?= e(setting('meet_photo_url') ?: setting('hero_photo_url')) ?>" alt="<?= e(setting('hero_photo_alt')) ?>">
        </div>
      </div>
      <div data-reveal data-reveal-delay="150">
        <div class="eyebrow"><b>i.</b> About</div>
        <h1>Hello, I'm <em>Patricia</em>.</h1>
        <!-- TODO: Patricia to write: who I am, why trauma work, what sessions feel like. -->
        <p class="about-bio">I'm a trauma-focused psychologist, and this practice is built around one belief: healing happens at the pace that feels safe to you. <!-- TODO: placeholder bio --></p>
        <p class="about-bio">I came to trauma work because I saw how often people carry what happened to them alone. Evidence-based therapies like EMDR, offered with warmth and without hurry, can change that. <!-- TODO: placeholder bio --></p>
        <p class="about-bio">Sessions with me are gentle and unhurried — we go only as far as feels right, and you always set the pace. <!-- TODO: placeholder bio --></p>
        <div class="about-facts">
          <div><b class="numeral-display">11</b><span>years of Enneagram study</span></div>
          <!-- TODO: Confirm this fact with Patricia. -->
          <div><b class="numeral-display">00+</b><span>years in practice</span></div>
          <!-- TODO: Confirm this fact with Patricia. -->
          <div><b class="numeral-display">0,000</b><span>sessions held</span></div>
        </div>
        <p class="signature" aria-hidden="true">Patricia</p>
      </div>
    </div>
  </div>
</section>

<!-- AVAILABILITY + QUALIFICATIONS -->
<section id="about">
  <div class="wrap about-grid">
    <aside class="avail rv" id="availability">
      <div class="kicker"><b>ii.</b> Sessions</div>
      <h3>My <em>availability</em></h3>
      <?php foreach ($availability as $slot): ?>
      <div class="slot"><span><?= e($slot['day_label']) ?></span><span><?= e($slot['hours_label']) ?></span></div>
      <?php endforeach; ?>
      <div class="avail-tags">
        <span class="avail-tag">Adults 18+</span>
        <span class="avail-tag">In person &amp; telehealth</span>
      </div>
      <a href="<?= e($bookingUrl) ?>" class="btn btn-ivory">Book appointment <span class="arr"></span></a>
    </aside>
    <div class="creds rv" id="qualifications">
      <div class="kicker"><b>iii.</b> Qualifications</div>
      <h2><?= rich_inline(setting('about_heading')) ?></h2>

      <div class="qual-lead">
        <div class="qual-eyebrow">Education</div>
        <p class="qual-cred">Masters in Professional Psychology</p>
      </div>

      <div class="qual-group">
        <h4>Registered with</h4>
        <ul class="qual-list">
          <li>Psychology Board of Australia</li>
          <!-- TODO: Add real AHPRA number; confirm whether NZ registration applies; the official NZ body is the 'New Zealand Psychologists Board'. -->
          <li>AHPRA registration no. PSY0000000000</li>
          <li>Member, Australian Association of Psychologists Inc (AAPi)</li>
        </ul>
      </div>

      <div class="qual-group">
        <h4>Funding &amp; referral pathways</h4>
        <ul class="badge-list">
          <li class="badge">Medicare Allied Health Provider</li>
          <li class="badge">Department of Veterans' Affairs</li>
          <li class="badge">Australian Defence Force</li>
          <li class="badge">WorkCover Queensland</li>
          <li class="badge">Victim Support ACT</li>
          <li class="badge">NDIS Registered</li>
        </ul>
      </div>

      <div class="qual-group">
        <h4>Training &amp; accreditation</h4>
        <dl class="qual-training">
          <div>
            <dt>Trauma therapies</dt>
            <!-- TODO: EMDR has no entry on the Therapy Types page yet — add one or point this link elsewhere. -->
            <dd><a class="link" href="therapies.php#emdr">Accredited EMDR Therapist</a> (EMDR Association of Australia), <a class="link" href="therapies.php#ifs">IFS Therapist</a>, <a class="link" href="therapies.php#brainspotting">Brainspotting Therapist</a></dd>
          </div>
          <div>
            <dt>Body &amp; rest</dt>
            <dd><a class="link" href="therapies.php#irest">iRest<sup>&reg;</sup> Provider</a></dd>
          </div>
          <div>
            <dt>Creative</dt>
            <dd><a class="link" href="therapies.php#expressive-arts">Expressive Arts Therapist</a></dd>
          </div>
          <div>
            <dt>Growth</dt>
            <dd><a class="link" href="therapies.php#enneagram">Enneagram Teacher</a></dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</section>

<!-- WHERE WE MEET -->
<section class="where">
  <div class="wrap">
    <div class="eyebrow rv"><b>iv.</b> Where we meet</div>
    <!-- TODO: Confirm heading copy with the client. -->
    <h2 class="rv">In the room, or <em>from home</em></h2>
    <div class="wm-grid">
      <article class="wm-card" data-reveal>
        <span class="wm-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/></svg>
        </span>
        <h3>In person</h3>
        <!-- TODO: Confirm suburb and parking notes. -->
        <p>Sessions are held in [Suburb].</p>
        <ul class="wm-list">
          <li>Easy parking close by <!-- TODO: Confirm parking notes. --></li>
          <li>The gate code is sent in your reminder email &amp; text</li>
        </ul>
      </article>
      <article class="wm-card" data-reveal data-reveal-delay="150">
        <span class="wm-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="12" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
        </span>
        <h3>Telehealth</h3>
        <p>Sessions via Zoom, from wherever you feel most comfortable.</p>
        <ul class="wm-list">
          <li>Zoom is free to download</li>
          <li>Ear buds are recommended for better sound</li>
          <li><a class="link" href="fees.php">Telehealth tips on the costs page</a></li>
        </ul>
      </article>
    </div>
  </div>
</section>

<!-- CTA — reused closing card -->
<div class="cta cta-flat">
  <div class="wrap">
    <div class="cta-box" data-reveal>
      <div class="kicker"><b>Begin</b></div>
      <h2>When you're ready, we'll begin <em>gently, together</em>.</h2>
      <p><?= e(setting('cta_text')) ?></p>
      <a href="<?= e($bookingUrl) ?>" class="btn btn-ivory">Book an appointment <span class="arr"></span></a>
      <!-- TODO: Confirm these facts with the client. -->
      <p class="cta-micro">Medicare rebates available &middot; In person or telehealth &middot; Reply within 1 business day</p>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
