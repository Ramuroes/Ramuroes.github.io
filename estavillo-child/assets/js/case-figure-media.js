/**
 * ESTAVILLO — video lazy-load + autoplay condicional (data-es-src)
 * -----------------------------------------------------------------------
 * Compartido por DOS componentes que nunca imprimen un <video src="…"> ni
 * un atributo autoplay crudo: blocks/case-figure/render.php (plugin) y
 * estavillo-child/inc/featured-media.php (Featured Media del Case Study —
 * Home/Work). El archivo pesado se defiere con IntersectionObserver
 * (data-es-src → src + .load() recién cerca del viewport, "no cargar
 * videos pesados innecesariamente antes de que sean relevantes") y el
 * autoplay respeta prefers-reduced-motion — algo que PHP no puede saber en
 * el servidor, sólo el navegador del visitante.
 *
 * El selector es genérico (`video[data-es-src]`, no atado a la clase de
 * ningún componente en particular) a propósito: Case Figure y Featured
 * Media son conceptualmente distintos y no comparten clase CSS, pero SÍ
 * comparten exactamente este mismo contrato de atributos — data-es-src +
 * data-es-video-autoplay opcional — así que comparten este único módulo
 * sin que ninguno de los dos tenga que conocer al otro.
 *
 * Sin JS (o en un navegador sin IntersectionObserver) el
 * <noscript><source></noscript> que ya imprime cada componente sigue
 * siendo un video reproducible a mano — nada esencial depende de este
 * script.
 *
 * Este script sólo se encola cuando hace falta: ver
 * es_post_has_case_figure_video() (Case Study individual) y
 * es_home_or_work_has_featured_video() (Home/Work) en inc/enqueue.php.
 *
 * @package estavillo-child
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function loadVideo(video) {
		var src = video.getAttribute('data-es-src');
		if (!src) {
			return;
		}
		video.removeAttribute('data-es-src');
		video.src = src;
		video.load();

		if (video.hasAttribute('data-es-video-autoplay') && !reduceMotion) {
			// 'canplay' (no 'canplaythrough'): alcanza con el primer frame
			// disponible para arrancar, no hace falta esperar el archivo
			// entero — mismo criterio que usa el navegador para el
			// atributo autoplay nativo.
			video.addEventListener(
				'canplay',
				function () {
					// play() devuelve una Promise que puede rechazar (el
					// visitante navegó lejos antes de que cargara, el
					// navegador igual bloqueó el autoplay pese a muted,
					// etc.) — no es un error del sitio: no hay nada que
					// reportar ni un fallback distinto del poster que ya
					// se ve.
					var p = video.play();
					if (p && p.catch) {
						p.catch(function () {});
					}
				},
				{ once: true }
			);
		}
	}

	function init() {
		var videos = document.querySelectorAll('video[data-es-src]');
		if (!videos.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			// Progressive enhancement: sin IntersectionObserver, cargar de
			// una — sigue siendo mejor que un <video> sin src.
			videos.forEach(loadVideo);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						observer.unobserve(entry.target);
						loadVideo(entry.target);
					}
				});
			},
			// Margen chico antes de entrar en viewport: evita el "pop-in"
			// de un archivo pesado apareciendo recién a mitad de scroll.
			{ rootMargin: '200px 0px' }
		);

		videos.forEach(function (video) {
			observer.observe(video);
		});
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
