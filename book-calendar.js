/**
 * book-calendar.js — Book-a-call CALENDAR modal (header CTA).
 * Front-end stage: visual + interaction only. No submit / real availability yet.
 *
 * Studio working hours live in STUDIO_TZ; slots are converted to the
 * visitor's timezone for display (auto-detected, switchable).
 */
(function () {
    'use strict';

    var STUDIO_TZ = 'Europe/London';            // studio reference timezone
    var WORK_START = 9 * 60;                     // 09:00 (studio local, minutes)
    var WORK_END = 18 * 60;                      // 18:00
    var SLOT_STEP = 30;                          // minutes
    var CALL_MIN = 30;                           // call length shown to user
    var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July',
        'August', 'September', 'October', 'November', 'December'];
    var WD = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    var TZ_CHOICES = [
        'Europe/London', 'Europe/Kyiv', 'Europe/Berlin', 'Europe/Moscow',
        'America/New_York', 'America/Chicago', 'America/Los_Angeles',
        'Asia/Dubai', 'Asia/Singapore', 'Asia/Tokyo', 'Australia/Sydney'
    ];

    function init() {
        var root = document.querySelector('[data-bookcal]');
        if (!root) return;

        var state = { view: new Date(), tz: detectTz(), day: null, instant: null };
        state.view = new Date(state.view.getFullYear(), state.view.getMonth(), 1);

        var grid = root.querySelector('[data-cal-grid]');
        var monthEl = root.querySelector('[data-cal-month]');
        var slotsWrap = root.querySelector('[data-slots-wrap]');
        var slotsEl = root.querySelector('[data-slots]');
        var slotsLabel = root.querySelector('[data-slots-label]');
        var tzSel = root.querySelector('[data-tz]');
        var toStep2 = root.querySelector('[data-to-step2]');

        buildTzOptions(tzSel, state.tz);

        tzSel.addEventListener('change', function () {
            state.tz = tzSel.value;
            if (state.day) renderSlots(state, slotsWrap, slotsEl, slotsLabel, toStep2);
        });

        root.querySelector('[data-cal-prev]').addEventListener('click', function () {
            state.view = new Date(state.view.getFullYear(), state.view.getMonth() - 1, 1);
            clampAndRender();
        });
        root.querySelector('[data-cal-next]').addEventListener('click', function () {
            state.view = new Date(state.view.getFullYear(), state.view.getMonth() + 1, 1);
            clampAndRender();
        });

        var today = new Date();
        var minMonth = today.getFullYear() * 12 + today.getMonth();
        var maxMonth = minMonth + 2;

        function clampAndRender() {
            var vm = state.view.getFullYear() * 12 + state.view.getMonth();
            if (vm < minMonth) state.view = new Date(today.getFullYear(), today.getMonth(), 1);
            if (vm > maxMonth) state.view = new Date(today.getFullYear(), today.getMonth() + 2, 1);
            renderGrid();
        }

        function renderGrid() {
            monthEl.textContent = MONTHS[state.view.getMonth()] + ' ' + state.view.getFullYear();
            var vm = state.view.getFullYear() * 12 + state.view.getMonth();
            root.querySelector('[data-cal-prev]').style.opacity = vm <= minMonth ? '.35' : '1';
            root.querySelector('[data-cal-next]').style.opacity = vm >= maxMonth ? '.35' : '1';
            grid.innerHTML = '';
            var y = state.view.getFullYear(), m = state.view.getMonth();
            var lead = (new Date(y, m, 1).getDay() + 6) % 7;
            for (var i = 0; i < lead; i++) grid.appendChild(document.createElement('span'));
            var days = new Date(y, m + 1, 0).getDate();
            var t0 = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            for (var d = 1; d <= days; d++) {
                (function (d) {
                    var dt = new Date(y, m, d);
                    var weekend = dt.getDay() === 0 || dt.getDay() === 6;
                    var avail = dt >= t0 && !weekend;
                    var cell = document.createElement('button');
                    cell.type = 'button';
                    cell.className = 'bookcal__day' + (avail ? '' : ' is-disabled');
                    cell.textContent = d;
                    if (avail) {
                        cell.addEventListener('click', function () {
                            state.day = dt;
                            Array.prototype.forEach.call(grid.querySelectorAll('.bookcal__day'),
                                function (c) { c.classList.remove('is-selected'); });
                            cell.classList.add('is-selected');
                            renderSlots(state, slotsWrap, slotsEl, slotsLabel, toStep2);
                        });
                    }
                    grid.appendChild(cell);
                })(d);
            }
        }

        renderGrid();
        wireSteps(root, state);
    }

    function renderSlots(state, slotsWrap, slotsEl, slotsLabel, toStep2) {
        slotsWrap.hidden = false;
        slotsEl.innerHTML = '';
        state.instant = null;
        toStep2.disabled = true;
        var d = state.day;
        slotsLabel.textContent = fmtDayLabel(d) + ' — pick a ' + CALL_MIN + '-min slot';
        for (var mins = WORK_START; mins < WORK_END; mins += SLOT_STEP) {
            var instant = studioWallToInstant(d.getFullYear(), d.getMonth(), d.getDate(),
                Math.floor(mins / 60), mins % 60);
            var label = fmtTime(instant, state.tz);
            (function (instant, label) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'bookcal__slot';
                b.textContent = label;
                b.addEventListener('click', function () {
                    state.instant = instant;
                    Array.prototype.forEach.call(slotsEl.querySelectorAll('.bookcal__slot'),
                        function (c) { c.classList.remove('is-selected'); });
                    b.classList.add('is-selected');
                    toStep2.disabled = false;
                });
                slotsEl.appendChild(b);
            })(instant, label);
        }
    }

    function wireSteps(root, state) {
        var steps = root.querySelectorAll('.bookcal__step');
        var modal = root.closest('.modal-book-calendar') || document;
        var introSell = modal.querySelector('[data-intro-sell]');
        var introThanks = modal.querySelector('[data-intro-thanks]');
        function go(n) {
            Array.prototype.forEach.call(steps, function (s) {
                s.hidden = String(s.getAttribute('data-step')) !== String(n);
            });
            var done = (String(n) === '3');
            if (introSell) introSell.hidden = done;
            if (introThanks) introThanks.hidden = !done;
        }
        root.querySelector('[data-to-step2]').addEventListener('click', function () {
            if (this.disabled) return;
            var txt = fmtDayLabel(state.day) + ' · ' + fmtTime(state.instant, state.tz)
                + ' (' + CALL_MIN + ' min, ' + tzShort(state.tz) + ')';
            root.querySelector('[data-summary]').textContent = txt;
            var sf = root.querySelector('[data-slot-field]');
            if (sf) sf.value = state.instant.toISOString();
            go(2);
        });
        root.querySelector('[data-back-step1]').addEventListener('click', function (e) {
            e.preventDefault(); go(1);
        });
        var form = root.querySelector('[data-bookcal-form]');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!state.instant) { go(1); return; }

            var btn = form.querySelector('[type="submit"]');
            var errEl = form.querySelector('[data-bookcal-error]');
            if (errEl) errEl.textContent = '';
            btn.setAttribute('disabled', 'disabled');

            var fd = new FormData(form);
            fd.append('action', 'mfs_book_call');
            fd.append('nonce', (window.mfsBookCfg && mfsBookCfg.nonce) || '');
            fd.append('tz', state.tz);
            fd.append('duration', String(CALL_MIN));
            fd.append('slot_iso', state.instant.toISOString());
            fd.append('slot_studio', fmtDayLabel(state.day) + ' ' + fmtTime(state.instant, STUDIO_TZ) + ' (' + tzShort(STUDIO_TZ) + ')');
            fd.append('slot_client', fmtDayLabel(state.day) + ' ' + fmtTime(state.instant, state.tz) + ' (' + tzShort(state.tz) + ')');
            fd.append('page_url', window.location.href);

            var url = (window.mfsBookCfg && mfsBookCfg.ajaxurl) || '/wp-admin/admin-ajax.php';
            fetch(url, { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    btn.removeAttribute('disabled');
                    if (res && res.success) {
                        if (window.dataLayer) window.dataLayer.push({ event: 'generate_lead', form_name: 'book_call_calendar', form_type: 'consultation' });
                        root.querySelector('[data-done-text]').textContent =
                            fmtDayLabel(state.day) + ' at ' + fmtTime(state.instant, state.tz) + ' (' + tzShort(state.tz) + ')';
                        go(3);
                    } else {
                        if (errEl) errEl.textContent = (res && res.data && res.data.message) || 'Something went wrong. Please try again.';
                    }
                })
                .catch(function () {
                    btn.removeAttribute('disabled');
                    if (errEl) errEl.textContent = 'Network error. Please try again.';
                });
        });
    }

    /* ---- timezone helpers (native Intl, no library) ---- */
    function detectTz() {
        try { return Intl.DateTimeFormat().resolvedOptions().timeZone || STUDIO_TZ; }
        catch (e) { return STUDIO_TZ; }
    }
    function tzOffsetMs(tz, date) {
        var dtf = new Intl.DateTimeFormat('en-US', {
            timeZone: tz, hourCycle: 'h23', year: 'numeric', month: '2-digit',
            day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
        var p = {};
        dtf.formatToParts(date).forEach(function (x) { p[x.type] = x.value; });
        var asUTC = Date.UTC(p.year, p.month - 1, p.day, p.hour, p.minute, p.second);
        return asUTC - date.getTime();
    }
    function studioWallToInstant(y, m, d, hh, mm) {
        var guess = Date.UTC(y, m, d, hh, mm, 0);
        var off = tzOffsetMs(STUDIO_TZ, new Date(guess));
        return new Date(guess - off);
    }
    function fmtTime(instant, tz) {
        return new Intl.DateTimeFormat('en-GB', {
            timeZone: tz, hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
        }).format(instant);
    }
    function tzShort(tz) {
        try {
            var s = new Intl.DateTimeFormat('en-US', { timeZone: tz, timeZoneName: 'short' })
                .formatToParts(new Date()).find(function (p) { return p.type === 'timeZoneName'; });
            return (s && s.value) ? s.value : tz;
        } catch (e) { return tz; }
    }
    function tzLabel(tz) {
        var city = tz.split('/').pop().replace(/_/g, ' ');
        return city + ' (' + tzShort(tz) + ')';
    }
    function buildTzOptions(sel, current) {
        var list = TZ_CHOICES.slice();
        if (list.indexOf(current) === -1) list.unshift(current);
        sel.innerHTML = '';
        list.forEach(function (tz) {
            var o = document.createElement('option');
            o.value = tz; o.textContent = tzLabel(tz);
            if (tz === current) o.selected = true;
            sel.appendChild(o);
        });
    }
    function fmtDayLabel(dt) {
        return WD[(dt.getDay() + 6) % 7] + ', ' + MONTHS[dt.getMonth()] + ' ' + dt.getDate();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
