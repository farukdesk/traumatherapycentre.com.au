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

  // Reveal-on-scroll animations
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
  document.querySelectorAll('.rv').forEach((el) => io.observe(el));

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
