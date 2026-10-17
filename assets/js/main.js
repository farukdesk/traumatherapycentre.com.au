// Trauma Therapy Centre — front-end behaviour
(function () {
  'use strict';

  // Sticky header shadow on scroll
  const hdr = document.getElementById('hdr');
  if (hdr) {
    const onScroll = () => hdr.classList.toggle('scrolled', window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // Mobile menu toggle
  const burger = document.getElementById('burger');
  const menu = document.getElementById('menu');
  if (burger && menu) {
    burger.addEventListener('click', () => {
      menu.classList.toggle('open');
      burger.classList.toggle('x');
    });
    menu.querySelectorAll('a').forEach((a) =>
      a.addEventListener('click', () => {
        menu.classList.remove('open');
        burger.classList.remove('x');
      })
    );
  }

  // Reveal-on-scroll animations (.rv legacy + [data-reveal] utility)
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const revealEls = document.querySelectorAll('.rv, [data-reveal]');
  if (reduceMotion || !('IntersectionObserver' in window)) {
    revealEls.forEach((el) => el.classList.add('in'));
  } else {
    const io = new IntersectionObserver(
      (es) =>
        es.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add('in');
            io.unobserve(e.target);
          }
        }),
      { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
    );
    revealEls.forEach((el) => {
      const delay = el.getAttribute('data-reveal-delay');
      if (delay) el.style.setProperty('--reveal-delay', `${parseInt(delay, 10) || 0}ms`);
      io.observe(el);
    });
  }

  // Stats count-up — real final values stay in the DOM for screen readers
  const statsStrip = document.querySelector('[data-stats]');
  if (statsStrip && !reduceMotion && 'IntersectionObserver' in window) {
    const easeOut = (t) => 1 - Math.pow(1 - t, 3);
    const countUp = (el) => {
      const finalText = el.getAttribute('data-count') || el.textContent;
      const target = parseFloat(finalText.replace(/[^\d.]/g, ''));
      if (isNaN(target)) return;
      const start = performance.now();
      const dur = 1400;
      const tick = (now) => {
        const p = Math.min((now - start) / dur, 1);
        el.textContent = p < 1 ? String(Math.round(easeOut(p) * target)) : finalText;
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    };
    const statIo = new IntersectionObserver(
      (es, obs) =>
        es.forEach((e) => {
          if (e.isIntersecting) {
            e.target.querySelectorAll('[data-count]').forEach(countUp);
            obs.unobserve(e.target);
          }
        }),
      { threshold: 0.3 }
    );
    statIo.observe(statsStrip);
  }

  // Subtle hero portrait parallax (max 20px) — off on reduced motion / touch
  const photo = document.querySelector('.figure .photo');
  const coarse = window.matchMedia('(hover: none), (pointer: coarse)').matches;
  if (photo && !reduceMotion && !coarse) {
    let raf = 0;
    const parallax = () => {
      raf = 0;
      const y = Math.max(-20, Math.min(20, window.scrollY * -0.03));
      photo.style.transform = 'translateY(' + y + 'px)';
    };
    window.addEventListener(
      'scroll',
      () => {
        if (!raf) raf = requestAnimationFrame(parallax);
      },
      { passive: true }
    );
  }

  // One therapy accordion open at a time
  document.querySelectorAll('.acc details').forEach((d) =>
    d.addEventListener('toggle', () => {
      if (d.open)
        document.querySelectorAll('.acc details').forEach((o) => {
          if (o !== d) o.open = false;
        });
    })
  );
})();
