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

  // Cancellation fee scale — track draws, then dots appear in sequence.
  // Static (full track, dots visible) under reduced motion via CSS.
  const feeScale = document.querySelector('[data-fee-scale]');
  if (feeScale) {
    if (reduceMotion || !('IntersectionObserver' in window)) {
      feeScale.classList.add('fs-in');
    } else {
      const fsIo = new IntersectionObserver(
        (es, obs) =>
          es.forEach((e) => {
            if (e.isIntersecting) {
              feeScale.classList.add('fs-in');
              obs.disconnect();
            }
          }),
        { threshold: 0.3 }
      );
      fsIo.observe(feeScale);
    }
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

  // Accessible disclosures (therapy card "Read more" + FAQ accordion)
  document.querySelectorAll('button[aria-controls]').forEach((btn) => {
    const panel = document.getElementById(btn.getAttribute('aria-controls'));
    if (!panel || !panel.classList.contains('disclose')) return;
    btn.addEventListener('click', () => {
      const open = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!open));
      panel.classList.toggle('open', !open);
      const label = btn.getAttribute(open ? 'data-more' : 'data-less');
      if (label) btn.textContent = label;
    });
  });

  // Gentle parallax for decorative circles (max px in data-parallax)
  const parallaxEls = document.querySelectorAll('[data-parallax]');
  if (parallaxEls.length && !reduceMotion && !coarse) {
    let pRaf = 0;
    const applyParallax = () => {
      pRaf = 0;
      parallaxEls.forEach((el) => {
        const max = parseInt(el.getAttribute('data-parallax'), 10) || 30;
        const rect = el.parentElement.getBoundingClientRect();
        const p = (rect.top + rect.height / 2 - window.innerHeight / 2) / window.innerHeight;
        const y = Math.max(-max, Math.min(max, p * -max));
        el.style.transform = 'translateY(' + y + 'px)';
      });
    };
    window.addEventListener(
      'scroll',
      () => {
        if (!pRaf) pRaf = requestAnimationFrame(applyParallax);
      },
      { passive: true }
    );
    applyParallax();
  }

  // Sticky therapy cards: dim + scale card 01 as card 02 slides over it
  const goldCards = document.querySelectorAll('.gold-stack .gold-row');
  if (goldCards.length > 1 && !reduceMotion) {
    let gRaf = 0;
    const updateCovered = () => {
      gRaf = 0;
      goldCards.forEach((card, i) => {
        const next = goldCards[i + 1];
        if (!next) return;
        const covered = next.getBoundingClientRect().top < card.getBoundingClientRect().bottom - 40;
        card.classList.toggle('covered', covered);
      });
    };
    window.addEventListener(
      'scroll',
      () => {
        if (!gRaf) gRaf = requestAnimationFrame(updateCovered);
      },
      { passive: true }
    );
  }

  // Quote: word-by-word reveal (40ms stagger); full opacity under reduced motion
  const quoteEl = document.querySelector('[data-words]');
  if (quoteEl && !reduceMotion && 'IntersectionObserver' in window) {
    const wrapWords = (node) => {
      Array.from(node.childNodes).forEach((child) => {
        if (child.nodeType === Node.TEXT_NODE) {
          const frag = document.createDocumentFragment();
          child.textContent.split(/(\s+)/).forEach((part) => {
            if (/^\s*$/.test(part)) {
              frag.appendChild(document.createTextNode(part));
            } else {
              const span = document.createElement('span');
              span.className = 'w';
              span.textContent = part;
              frag.appendChild(span);
            }
          });
          node.replaceChild(frag, child);
        } else if (child.nodeType === Node.ELEMENT_NODE) {
          wrapWords(child);
        }
      });
    };
    wrapWords(quoteEl);
    quoteEl.querySelectorAll('.w').forEach((w, i) => {
      w.style.setProperty('--w-delay', i * 40 + 'ms');
    });
    const qIo = new IntersectionObserver(
      (es, obs) =>
        es.forEach((e) => {
          if (e.isIntersecting) {
            quoteEl.classList.add('words-in');
            obs.disconnect();
          }
        }),
      { threshold: 0.4 }
    );
    qIo.observe(quoteEl);
  }
})();
