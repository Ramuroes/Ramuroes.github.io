/**
 * ESTAVILLO — Case Figure media (video: lazy-load + autoplay condicional)
 * -----------------------------------------------------------------------
 * blocks/case-figure/render.php nunca imprime un <video src="…"> ni un
 * atributo autoplay crudo: el archivo pesado se defiere con
 * IntersectionObserver (data-es-src → src + .load() recién cerca del
 * viewport, "no cargar videos pesados innecesariamente antes de que sean
 * relevantes") y el autoplay respeta prefers-reduced-motion — algo que PHP
 * no puede saber en el servidor, sólo el navegador del visitante.
 *
 * Sin JS (o en un navegador sin IntersectionObserver) el
 * <noscript><source></noscript> que ya imprime render.php sigue siendo un
 * video reproducible a mano — nada esencial depende de este script.
 *
 * Este script sólo se encola cuando existe al menos un case-figure con
 * mediaType "video" en el post actual (ver inc/enqueue.php — mismo patrón
 * de encolado condicional que case-figure-lightbox.js).
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
		var videos = document.querySelectorAll('.es-case-figure__video[data-es-src]');
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
