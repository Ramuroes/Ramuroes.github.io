/**
 * ESTAVILLO — Hero scroll transition (Home only).
 *
 * Sets --es-hero-progress (0→1) on the hero as the reader scrolls through
 * it, driving the restrained content-fade / background-linger choreography
 * defined in hero.css. Deliberately light:
 *
 *  - honours prefers-reduced-motion: reduce (does nothing → hero stays
 *    fully visible and static, normal document flow);
 *  - only listens to scroll WHILE the hero intersects the viewport
 *    (IntersectionObserver starts/stops the listener — no permanent heavy
 *    scroll handler running for the whole page);
 *  - reads/writes inside requestAnimationFrame (one write per frame);
 *  - no animation library, no scroll hijacking, no snap.
 *
 * The scroll indicator (rendered by the hero) also gets a smooth-scroll to
 * the existing #process anchor on click (native anchor jump is the
 * no-JS/failure fallback).
 */
(function () {
	'use strict';

	var hero = document.querySelector('.es-hero');
	if (!hero) {
		return;
	}

	var indicator = hero.querySelector('.es-hero__scroll');
	if (indicator) {
		indicator.addEventListener('click', function (ev) {
			var target = document.getElementById('process');
			if (!target) {
				return; // let the native anchor handle it
			}
			ev.preventDefault();
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});
	}

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (reduce) {
		return; // no choreography under reduced motion
	}

	var ticking = false;
	var listening = false;

	function update() {
		ticking = false;
		var rect = hero.getBoundingClientRect();
		var h = rect.height || 1;
		// 0 at the top of the hero, 1 once it has scrolled ~one hero-height up.
		var scrolled = Math.min(Math.max(-rect.top, 0), h);
		hero.style.setProperty('--es-hero-progress', (scrolled / h).toFixed(3));
		// Raw scrolled distance in px. Parallax mode (hero.css) counter-
		// translates the background layer by this amount so it stays visually
		// ANCHORED from the first scroll movement (no sticky, no
		// background-attachment:fixed). Standard mode ignores it.
		hero.style.setProperty('--es-hero-scroll-px', scrolled.toFixed(1) + 'px');
	}

	function onScroll() {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(update);
		}
	}

	function startListening() {
		if (!listening) {
			listening = true;
			window.addEventListener('scroll', onScroll, { passive: true });
			update();
		}
	}

	function stopListening(settleValue) {
		if (listening) {
			listening = false;
			window.removeEventListener('scroll', onScroll);
		}
		// Settle so nothing snaps back when the hero leaves the viewport.
		hero.style.setProperty('--es-hero-progress', settleValue);
		// Scrolled-past (settle '1') → the parallax bg is already faded to ~0,
		// so its transform is moot; back at the top (settle '0') → reset the
		// anchor offset so the bg sits in its natural place again.
		if (settleValue === '0') {
			hero.style.setProperty('--es-hero-scroll-px', '0px');
		}
	}

	if ('IntersectionObserver' in window) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					startListening();
				} else {
					// Above the fold when scrolled past → hold at 1; below → 0.
					stopListening(entry.boundingClientRect.top < 0 ? '1' : '0');
				}
			});
		}, { threshold: 0 });
		io.observe(hero);
	} else {
		// No IO: a single passive listener is an acceptable fallback.
		startListening();
	}
})();
