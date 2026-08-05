/**
 * ESTAVILLO — índice sticky del Case Study (.es-case-index)
 * -----------------------------------------------------------------------
 * Tres cosas, todas mejora progresiva sobre un menú que ya funciona solo:
 *
 *  1. Scrollspy vía IntersectionObserver. En vez de observar "¿la sección
 *     está visible?" (ambiguo: con secciones largas hay varias visibles a
 *     la vez), se observa una LÍNEA de 1px justo debajo del nav sticky —
 *     un rootMargin que recorta el viewport a esa altura. La sección que
 *     cruza esa línea es la activa: exactamente una a la vez, sin empates
 *     ni heurísticas de "cuál ocupa más".
 *
 *  2. El riel: una línea gris fina de ancho completo con un segmento verde
 *     del ancho del link activo. El segmento vive DENTRO del scroller, así
 *     que se posiciona con el offsetLeft/offsetWidth crudos del link, sin
 *     compensar el scroll a mano.
 *
 *  3. Los botones ‹ › : sólo se muestran cuando de verdad hay contenido
 *     tapado de ese lado. Avanzan ~70% del ancho visible y después alinean
 *     el borde a un link entero, para no dejar nunca medio label cortado.
 *
 * Nada de esto es indispensable: sin JS el menú se sigue scrolleando con
 * gesto, trackpad, rueda horizontal y teclado (es un overflow-x nativo), y
 * los botones nunca aparecen porque el markup los emite [hidden]. La
 * sección activa además NO depende sólo del verde: el link lleva
 * aria-current="location" y sube de contraste (ver case-study.css).
 *
 * @package estavillo-child
 */
(function () {
	'use strict';

	var root = document.querySelector('[data-es-case-index]');
	if (!root) {
		return;
	}

	var viewport = root.querySelector('.es-case-index__viewport');
	var scroller = root.querySelector('[data-es-index-scroller]');
	var rail = root.querySelector('[data-es-index-rail]');
	var prevBtn = root.querySelector('[data-es-index-prev]');
	var nextBtn = root.querySelector('[data-es-index-next]');
	var links = Array.prototype.slice.call(root.querySelectorAll('.es-case-index__link'));

	if (!scroller || !links.length) {
		return;
	}

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var activeLink = null;

	/** Resuelve cada link a la sección real del documento (si existe). */
	var entries = links
		.map(function (link) {
			var href = link.getAttribute('href') || '';
			var id = href.charAt(0) === '#' ? href.slice(1) : '';
			var section = id ? document.getElementById(id) : null;
			return { link: link, section: section };
		})
		.filter(function (entry) {
			return !!entry.section;
		});

	// ---------------------------------------------------------------- riel

	function moveRail(link) {
		if (!rail || !link) {
			return;
		}
		rail.style.transform = 'translateX(' + link.offsetLeft + 'px)';
		rail.style.width = link.offsetWidth + 'px';
	}

	/**
	 * Deja el link activo a la vista dentro del menú, sin tocar el scroll
	 * de la PÁGINA (scrollIntoView movería las dos cosas). Sólo corrige si
	 * el link quedó realmente fuera del área visible del scroller.
	 */
	function revealLink(link) {
		var left = link.offsetLeft;
		var right = left + link.offsetWidth;
		var viewLeft = scroller.scrollLeft;
		var viewRight = viewLeft + scroller.clientWidth;
		var pad = 24;
		var target = null;

		if (left < viewLeft + pad) {
			target = left - pad;
		} else if (right > viewRight - pad) {
			target = right - scroller.clientWidth + pad;
		}

		if (null !== target) {
			scrollTo(target);
		}
	}

	function setActive(link) {
		if (link === activeLink) {
			return;
		}
		if (activeLink) {
			activeLink.removeAttribute('aria-current');
		}
		activeLink = link;
		if (!link) {
			return;
		}
		link.setAttribute('aria-current', 'location');
		moveRail(link);
		revealLink(link);
	}

	// ------------------------------------------------------------ scrollspy

	var observer = null;

	/**
	 * (Re)crea el observer. El rootMargin depende de la altura real del
	 * nav sticky y del viewport, así que hay que rehacerlo en resize: un
	 * IntersectionObserver no permite cambiarle el rootMargin en caliente.
	 */
	function observeSections() {
		if (observer) {
			observer.disconnect();
		}

		// Borde inferior del nav sticky = dónde ponemos la línea de scan.
		var navBottom = root.getBoundingClientRect().bottom;
		if (navBottom < 0) {
			navBottom = 0;
		}
		var bottomMargin = window.innerHeight - navBottom - 1;
		if (bottomMargin < 0) {
			bottomMargin = 0;
		}

		observer = new IntersectionObserver(
			function (records) {
				records.forEach(function (record) {
					if (!record.isIntersecting) {
						return;
					}
					var match = entries.filter(function (entry) {
						return entry.section === record.target;
					})[0];
					if (match) {
						setActive(match.link);
					}
				});
			},
			{
				rootMargin: '-' + Math.round(navBottom) + 'px 0px -' + Math.round(bottomMargin) + 'px 0px',
				threshold: 0,
			}
		);

		entries.forEach(function (entry) {
			observer.observe(entry.section);
		});
	}

	// -------------------------------------------------------------- botones

	function scrollTo(left) {
		var max = scroller.scrollWidth - scroller.clientWidth;
		left = Math.max(0, Math.min(max, left));
		if (reduceMotion) {
			scroller.scrollLeft = left;
		} else {
			scroller.scrollTo({ left: left, behavior: 'smooth' });
		}
	}

	/**
	 * Alinea un scrollLeft tentativo al comienzo de un link entero, para
	 * que el borde izquierdo del menú nunca corte un label por la mitad.
	 */
	function snapToLink(target) {
		var pad = 12;
		for (var i = 0; i < links.length; i++) {
			if (links[i].offsetLeft >= target - pad) {
				return links[i].offsetLeft - pad;
			}
		}
		return target;
	}

	function step(direction) {
		// Una distancia razonable (no "una sección"), y después el snap se
		// encarga de que el link del borde quede entero — que es lo que
		// evita los saltos raros cuando los labels tienen anchos dispares.
		var amount = Math.max(120, scroller.clientWidth * 0.7);
		scrollTo(snapToLink(scroller.scrollLeft + direction * amount));
	}

	function syncArrows() {
		if (!prevBtn || !nextBtn || !viewport) {
			return;
		}
		// 2px de tolerancia: el scrollLeft puede ser fraccionario y nunca
		// llegar exacto al máximo (zoom del navegador, DPR no entero).
		var maxScroll = scroller.scrollWidth - scroller.clientWidth;
		var hasPrev = scroller.scrollLeft > 2;
		var hasNext = scroller.scrollLeft < maxScroll - 2;

		prevBtn.hidden = !hasPrev;
		nextBtn.hidden = !hasNext;
		viewport.classList.toggle('has-prev', hasPrev);
		viewport.classList.toggle('has-next', hasNext);
	}

	if (prevBtn) {
		prevBtn.addEventListener('click', function () {
			step(-1);
		});
	}
	if (nextBtn) {
		nextBtn.addEventListener('click', function () {
			step(1);
		});
	}

	scroller.addEventListener('scroll', syncArrows, { passive: true });

	// Un click en un link deja esa sección activa de inmediato, sin esperar
	// a que el scroll de la página dispare el observer.
	links.forEach(function (link) {
		link.addEventListener('click', function () {
			setActive(link);
		});
	});

	var resizeTimer = null;
	window.addEventListener(
		'resize',
		function () {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(function () {
				syncArrows();
				moveRail(activeLink);
				observeSections();
			}, 150);
		},
		{ passive: true }
	);

	syncArrows();
	if (entries.length && 'IntersectionObserver' in window) {
		observeSections();
	}
})();
