// Trauma Therapy Centre — fees page behaviour
(function () {
  'use strict';

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasIO = 'IntersectionObserver' in window;
  const easeOut = (t) => 1 - Math.pow(1 - t, 3);

  // ---- Fee toggle (With / Without MHCP) -----------------------------------
  // Figures come from the data-fee-config JSON printed by fees.php.
  // TODO: Confirm current fee and Medicare rebate amounts with the client.
  const card = document.querySelector('[data-fee-card]');
  if (card) {
    let cfg = {};
    try {
      cfg = JSON.parse(card.getAttribute('data-fee-config') || '{}');
    } catch (err) {
      cfg = {};
    }
    const amountEl = card.querySelector('[data-fee-amount]');
    const captionEl = card.querySelector('[data-fee-caption]');
    const labelEl = card.querySelector('[data-fee-label]');
    const liveEl = card.querySelector('[data-fee-live]');
    const buttons = Array.from(card.querySelectorAll('[data-fee-mode]'));

    const modes = {
      mhcp: {
        amount: Number(cfg.gap),
        caption: captionEl ? captionEl.textContent : 'out of pocket after Medicare',
        label: labelEl ? labelEl.textContent : '',
      },
      full: {
        amount: Number(cfg.session),
        caption: '/ 50 min',
        label: 'Standard session',
      },
    };

    const fmt = (n) => (Number.isInteger(n) ? String(n) : n.toFixed(2));
    let raf = 0;

    const setAmount = (to) => {
      if (!amountEl) return;
      const from = parseFloat(amountEl.textContent.replace(/[^\d.]/g, '')) || 0;
      if (raf) cancelAnimationFrame(raf);
      if (reduceMotion || from === to) {
        amountEl.textContent = fmt(to);
        return;
      }
      const decimals = Number.isInteger(from) && Number.isInteger(to) ? 0 : 2;
      const start = performance.now();
      const dur = 500; // short count/roll, ease-out
      const tick = (now) => {
        const p = Math.min((now - start) / dur, 1);
        const v = from + (to - from) * easeOut(p);
        amountEl.textContent = p < 1 ? v.toFixed(decimals) : fmt(to);
        if (p < 1) raf = requestAnimationFrame(tick);
      };
      raf = requestAnimationFrame(tick);
    };

    const apply = (mode) => {
      const m = modes[mode];
      if (!m || isNaN(m.amount)) return;
      buttons.forEach((b) =>
        b.setAttribute('aria-pressed', String(b.getAttribute('data-fee-mode') === mode))
      );
      setAmount(m.amount);
      if (captionEl) captionEl.textContent = m.caption;
      if (labelEl && m.label) labelEl.textContent = m.label;
      if (liveEl) liveEl.textContent = '$' + fmt(m.amount) + ' ' + m.caption;
    };

    buttons.forEach((b) =>
      b.addEventListener('click', () => apply(b.getAttribute('data-fee-mode')))
    );
  }

  // ---- Journey progress line + numbered dots ------------------------------
  const journey = document.querySelector('[data-journey]');
  if (journey) {
    const steps = journey.querySelectorAll('.step');
    const fill = journey.querySelector('[data-journey-fill]');
    if (reduceMotion || !hasIO) {
      steps.forEach((s) => s.classList.add('active'));
      if (fill) fill.style.transform = 'none';
    } else {
      // A dot fills once its card reaches the viewport centre
      const dotIo = new IntersectionObserver(
        (es) =>
          es.forEach((e) => {
            const active =
              e.isIntersecting || e.boundingClientRect.top < window.innerHeight / 2;
            e.target.classList.toggle('active', active);
          }),
        { rootMargin: '-50% 0px -50% 0px', threshold: 0 }
      );
      steps.forEach((s) => dotIo.observe(s));

      // Scroll fallback for the plum progress fill when
      // CSS animation-timeline: view() is unsupported
      const timelineSupported =
        window.CSS && CSS.supports && CSS.supports('animation-timeline: view()');
      if (fill && !timelineSupported) {
        let pRaf = 0;
        const progress = () => {
          pRaf = 0;
          const r = journey.getBoundingClientRect();
          const raw = (window.innerHeight - r.top) / (r.height + window.innerHeight);
          const p = Math.min(1, Math.max(0, (raw - 0.15) / 0.7));
          fill.style.transform = 'scaleY(' + p + ')';
        };
        window.addEventListener(
          'scroll',
          () => {
            if (!pRaf) pRaf = requestAnimationFrame(progress);
          },
          { passive: true }
        );
        progress();
      }
    }
  }

  // ---- Reminder timeline draw-in ------------------------------------------
  const tl = document.querySelector('[data-timeline]');
  if (tl) {
    if (reduceMotion || !hasIO) {
      tl.classList.add('tl-in');
    } else {
      const tIo = new IntersectionObserver(
        (es, obs) =>
          es.forEach((e) => {
            if (e.isIntersecting) {
              tl.classList.add('tl-in');
              obs.disconnect();
            }
          }),
        { threshold: 0.4 }
      );
      tIo.observe(tl);
    }
  }
})();
