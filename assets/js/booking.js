// Trauma Therapy Centre — interactive booking calendar
(function () {
  'use strict';

  const schedule = document.getElementById('bk-schedule');
  if (!schedule) return;

  let availability = {};
  try {
    availability = JSON.parse(schedule.dataset.availability || '{}');
  } catch (e) {
    return; // fall back to native inputs
  }
  let fullDates = [];
  try {
    fullDates = JSON.parse(schedule.dataset.fullDates || '[]') || [];
  } catch (e) {
    fullDates = [];
  }
  let fullSlots = {};
  try {
    fullSlots = JSON.parse(schedule.dataset.fullSlots || '{}') || {};
  } catch (e) {
    fullSlots = {};
  }
  const hasSlots = Object.keys(availability).some((k) => (availability[k] || []).length);
  if (!hasSlots) return; // nothing to build a calendar from

  const picker = document.getElementById('bk-picker');
  const native = document.getElementById('bk-native');
  const daysEl = document.getElementById('bk-days');
  const titleEl = document.getElementById('bk-title');
  const prevBtn = document.getElementById('bk-prev');
  const nextBtn = document.getElementById('bk-next');
  const slotsHead = document.getElementById('bk-slots-head');
  const slotList = document.getElementById('bk-slot-list');
  const summary = document.getElementById('bk-summary');
  const summaryText = document.getElementById('bk-summary-text');
  const dateInput = document.getElementById('bf-date');
  const timeInput = document.getElementById('bf-time');
  if (!picker || !native || !daysEl || !dateInput || !timeInput) return;

  const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'];
  const DOW = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

  // "Today" from the server (site timezone) so client/server validation agree.
  let today;
  if (/^\d{4}-\d{2}-\d{2}$/.test(schedule.dataset.today || '')) {
    const tp = schedule.dataset.today.split('-');
    today = new Date(+tp[0], +tp[1] - 1, +tp[2]);
  } else {
    today = new Date();
    today.setHours(0, 0, 0, 0);
  }

  let viewYear = today.getFullYear();
  let viewMonth = today.getMonth();
  let selectedDate = null; // 'YYYY-MM-DD'
  let selectedTime = '';

  const pad = (n) => (n < 10 ? '0' + n : '' + n);
  const iso = (d) => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

  const normTime = (t) => t.toLowerCase().replace(/\s+/g, '');

  function isSlotFull(isoStr, t) {
    return (fullSlots[isoStr] || []).indexOf(normTime(t)) !== -1;
  }

  function isAvailable(d) {
    if (d < today || fullDates.indexOf(iso(d)) !== -1) return false;
    const slots = availability[d.getDay()] || [];
    if (!slots.length) return false;
    return slots.some((t) => !isSlotFull(iso(d), t));
  }

  function prettyDate(isoStr) {
    const p = isoStr.split('-');
    const d = new Date(+p[0], +p[1] - 1, +p[2]);
    return DOW[d.getDay()] + ', ' + d.getDate() + ' ' + MONTHS[d.getMonth()] + ' ' + d.getFullYear();
  }

  function updateSummary() {
    if (!summary || !summaryText) return;
    if (selectedDate && selectedTime) {
      summaryText.textContent = prettyDate(selectedDate) + ' at ' + selectedTime;
      summary.hidden = false;
    } else {
      summary.hidden = true;
    }
  }

  function renderSlots() {
    slotList.innerHTML = '';
    if (!selectedDate) {
      slotsHead.textContent = 'Pick a day to see available times';
      updateSummary();
      return;
    }
    const p = selectedDate.split('-');
    const d = new Date(+p[0], +p[1] - 1, +p[2]);
    const slots = availability[d.getDay()] || [];
    slotsHead.textContent = prettyDate(selectedDate);
    slots.forEach((t) => {
      const full = isSlotFull(selectedDate, t);
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'bk-slot' + (t === selectedTime ? ' selected' : '') + (full ? ' bk-slot-full' : '');
      b.setAttribute('role', 'option');
      b.setAttribute('aria-selected', t === selectedTime ? 'true' : 'false');
      b.textContent = t;
      if (full) {
        b.disabled = true;
        b.setAttribute('aria-label', t + ', fully booked');
      } else {
        b.addEventListener('click', () => {
          selectedTime = t;
          timeInput.value = t;
          renderSlots();
        });
      }
      slotList.appendChild(b);
    });
    updateSummary();
  }

  function renderCalendar() {
    titleEl.textContent = MONTHS[viewMonth] + ' ' + viewYear;
    prevBtn.disabled = viewYear === today.getFullYear() && viewMonth === today.getMonth();
    daysEl.innerHTML = '';

    const first = new Date(viewYear, viewMonth, 1);
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
    for (let i = 0; i < first.getDay(); i++) {
      daysEl.appendChild(document.createElement('span'));
    }
    for (let day = 1; day <= daysInMonth; day++) {
      const d = new Date(viewYear, viewMonth, day);
      const avail = isAvailable(d);
      const cell = document.createElement('button');
      cell.type = 'button';
      cell.textContent = day;
      cell.className = 'bk-day' + (avail ? ' bk-avail' : '');
      if (iso(d) === iso(today)) cell.classList.add('today');
      if (selectedDate === iso(d)) {
        cell.classList.add('selected');
        cell.setAttribute('aria-pressed', 'true');
      }
      cell.disabled = !avail;
      cell.setAttribute('aria-label', DOW[d.getDay()] + ' ' + day + ' ' + MONTHS[viewMonth] + (avail ? ', available' : ', unavailable'));
      if (avail) {
        cell.addEventListener('click', () => {
          selectedDate = iso(d);
          selectedTime = '';
          dateInput.value = selectedDate;
          timeInput.value = '';
          renderCalendar();
          renderSlots();
        });
      }
      daysEl.appendChild(cell);
    }
  }

  prevBtn.addEventListener('click', () => {
    viewMonth--;
    if (viewMonth < 0) { viewMonth = 11; viewYear--; }
    renderCalendar();
  });
  nextBtn.addEventListener('click', () => {
    viewMonth++;
    if (viewMonth > 11) { viewMonth = 0; viewYear++; }
    renderCalendar();
  });

  // Restore state after a failed server-side validation round-trip.
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateInput.value)) {
    const p = dateInput.value.split('-');
    const d = new Date(+p[0], +p[1] - 1, +p[2]);
    if (isAvailable(d)) {
      selectedDate = dateInput.value;
      viewYear = d.getFullYear();
      viewMonth = d.getMonth();
      const slots = availability[d.getDay()] || [];
      if (slots.indexOf(timeInput.value) !== -1 && !isSlotFull(selectedDate, timeInput.value)) selectedTime = timeInput.value;
    }
  }

  // Swap the native fields for the interactive picker.
  native.hidden = true;
  dateInput.removeAttribute('required');
  timeInput.removeAttribute('required');
  picker.hidden = false;

  renderCalendar();
  renderSlots();

  // Make sure a date and time were chosen before submitting.
  const form = schedule.closest('form');
  if (form) {
    form.addEventListener('submit', (ev) => {
      if (!selectedDate || !selectedTime) {
        ev.preventDefault();
        slotsHead.textContent = selectedDate ? 'Please choose a time for your session' : 'Please pick a day for your session';
        picker.classList.add('bk-attn');
        picker.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => picker.classList.remove('bk-attn'), 900);
      }
    });
  }
})();
