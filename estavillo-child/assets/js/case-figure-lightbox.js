/**
 * ESTAVILLO — Case Figure lightbox (visor compartido, .es-lightbox)
 * -----------------------------------------------------------------------
 * Un solo <dialog>, inyectado una única vez la primera vez que hace
 * falta — no uno por figura, sin importar cuántos Case Figure con zoom
 * (`enableZoom`) haya en la página. Cualquier
 * `.es-case-figure__zoom-trigger` (emitido por blocks/case-figure/
 * render.php sólo cuando el atributo está activo) lo abre con la imagen
 * de máxima calidad disponible ('full', no la 'large' que ya se ve en la
 * página).
 *
 * Este script sólo se encola cuando existe al menos una figura con zoom
 * en el post actual (ver inc/enqueue.php — lee post_content con
 * parse_blocks(), no sólo "¿existe el bloque?"): en cualquier otra página
 * ni siquiera se pide.
 *
 * Zoom/pan (MVP del ticket): doble click/tap alterna fit↔lectura, rueda
 * en desktop, botones +/−/Reset, arrastre con un dedo/mouse cuando está
 * ampliado, y pinch de dos dedos vía Pointer Events (sin librerías: un
 * solo listener set cubre mouse, touch y pen).
 *
 * Accesibilidad: <dialog>.showModal() vuelve inerte todo lo de afuera
 * (el navegador ya hace el "focus trap" — no hay que reimplementarlo), y
 * Escape ya cierra el <dialog> nativamente (evento 'cancel'). Lo que SÍ
 * hace este script: mover el foco al abrir, devolverlo al trigger al
 * cerrar, bloquear el scroll del body como refuerzo cross-browser, y
 * exponer los controles con aria-label reales (vienen del bloque, así
 * que respetan el idioma del post — Polylang).
 *
 * @package estavillo-child
 */
(function () {
	'use strict';

	if (!('HTMLDialogElement' in window)) {
		// Navegador sin <dialog>: la imagen sigue viéndose igual que
		// siempre, sólo no se ofrece el visor — no hay nada roto.
		return;
	}

	var dialog = null;
	var viewport, img, closeBtn, zoomInBtn, zoomOutBtn, resetBtn, captionEl, captionToggle;
	var lastFocused = null;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	var MAX_SCALE_MULT = 6; // tope de zoom, relativo al "fit" inicial
	var READ_SCALE_MULT = 2.5; // a qué escala salta el doble tap/click
	var STEP_MULT = 1.5; // cuánto suman los botones +/− por click

	var state = { fitScale: 1, scale: 1, tx: 0, ty: 0, natW: 0, natH: 0 };
	var pointers = new Map();
	var dragStart = null;
	var pinchStart = null;
	var movedDuringGesture = false;
	var lastTapTime = 0;
	var lastTapPos = null;
	var pointerDownTarget = null;

	function clamp(v, min, max) {
		return Math.max(min, Math.min(max, v));
	}

	function distance(a, b) {
		var dx = a.x - b.x;
		var dy = a.y - b.y;
		return Math.sqrt(dx * dx + dy * dy);
	}

	function minScale() {
		return state.fitScale;
	}

	function maxScale() {
		return Math.max(state.fitScale * MAX_SCALE_MULT, 2);
	}

	function icon(points, viewBox) {
		var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
		svg.setAttribute('viewBox', viewBox || '0 0 20 20');
		svg.setAttribute('aria-hidden', 'true');
		svg.setAttribute('focusable', 'false');
		points.forEach(function (p) {
			var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
			line.setAttribute('x1', p[0]);
			line.setAttribute('y1', p[1]);
			line.setAttribute('x2', p[2]);
			line.setAttribute('y2', p[3]);
			svg.appendChild(line);
		});
		return svg;
	}

	function buildDialog() {
		if (dialog) {
			return dialog;
		}

		dialog = document.createElement('dialog');
		dialog.className = 'es-lightbox';

		viewport = document.createElement('div');
		viewport.className = 'es-lightbox__viewport';

		img = document.createElement('img');
		img.className = 'es-lightbox__img';
		img.alt = '';
		viewport.appendChild(img);

		closeBtn = document.createElement('button');
		closeBtn.type = 'button';
		closeBtn.className = 'es-lightbox__close';
		closeBtn.appendChild(
			icon(
				[
					[4, 4, 16, 16],
					[16, 4, 4, 16],
				]
			)
		);

		var controls = document.createElement('div');
		controls.className = 'es-lightbox__controls';

		zoomOutBtn = document.createElement('button');
		zoomOutBtn.type = 'button';
		zoomOutBtn.className = 'es-lightbox__zoom-out';
		zoomOutBtn.appendChild(icon([[4, 10, 16, 10]]));

		resetBtn = document.createElement('button');
		resetBtn.type = 'button';
		resetBtn.className = 'es-lightbox__reset';

		zoomInBtn = document.createElement('button');
		zoomInBtn.type = 'button';
		zoomInBtn.className = 'es-lightbox__zoom-in';
		zoomInBtn.appendChild(
			icon(
				[
					[4, 10, 16, 10],
					[10, 4, 10, 16],
				]
			)
		);

		controls.appendChild(zoomOutBtn);
		controls.appendChild(resetBtn);
		controls.appendChild(zoomInBtn);

		captionEl = document.createElement('p');
		captionEl.className = 'es-lightbox__caption';

		captionToggle = document.createElement('button');
		captionToggle.type = 'button';
		captionToggle.className = 'es-lightbox__caption-toggle';
		captionToggle.appendChild(
			icon(
				[
					[2, 3, 12, 3],
					[2, 7, 12, 7],
					[2, 11, 8, 11],
				],
				'0 0 14 14'
			)
		);

		dialog.appendChild(viewport);
		dialog.appendChild(closeBtn);
		dialog.appendChild(controls);
		dialog.appendChild(captionEl);
		dialog.appendChild(captionToggle);
		document.body.appendChild(dialog);

		wireEvents();
		return dialog;
	}

	function setTransform(tx, ty, scale, animate) {
		state.tx = tx;
		state.ty = ty;
		state.scale = scale;
		viewport.classList.toggle('is-animating', !!animate && !reduceMotion);
		img.style.transform = 'translate(' + tx + 'px,' + ty + 'px) scale(' + scale + ')';
		viewport.classList.toggle('is-zoomed', scale > state.fitScale + 0.001);
		zoomOutBtn.disabled = scale <= minScale() + 0.001;
		zoomInBtn.disabled = scale >= maxScale() - 0.001;
	}

	/** Nunca deja al usuario arrastrar la imagen entera fuera de la vista. */
	function clampToBounds(animate) {
		var rect = viewport.getBoundingClientRect();
		var w = state.natW * state.scale;
		var h = state.natH * state.scale;
		var minTx, maxTx, minTy, maxTy;

		if (w <= rect.width) {
			minTx = maxTx = (rect.width - w) / 2;
		} else {
			maxTx = 0;
			minTx = rect.width - w;
		}
		if (h <= rect.height) {
			minTy = maxTy = (rect.height - h) / 2;
		} else {
			maxTy = 0;
			minTy = rect.height - h;
		}

		var tx = clamp(state.tx, minTx, maxTx);
		var ty = clamp(state.ty, minTy, maxTy);
		if (Math.abs(tx - state.tx) > 0.5 || Math.abs(ty - state.ty) > 0.5) {
			setTransform(tx, ty, state.scale, animate);
		}
	}

	function fitAndCenter() {
		var rect = viewport.getBoundingClientRect();
		var pad = 24;
		var availW = Math.max(50, rect.width - pad * 2);
		var availH = Math.max(50, rect.height - pad * 2);
		var fit = Math.min(availW / state.natW, availH / state.natH, 1);
		state.fitScale = fit > 0 ? fit : 1;
		var tx = (rect.width - state.natW * state.fitScale) / 2;
		var ty = (rect.height - state.natH * state.fitScale) / 2;
		setTransform(tx, ty, state.fitScale, false);
	}

	/** Zoom hacia un punto de pantalla (cursor, tap, centro del viewport si no se pasa ninguno). */
	function zoomTo(newScale, clientX, clientY, animate) {
		newScale = clamp(newScale, minScale(), maxScale());
		var rect = viewport.getBoundingClientRect();
		var localX = (clientX != null ? clientX : rect.left + rect.width / 2) - rect.left;
		var localY = (clientY != null ? clientY : rect.top + rect.height / 2) - rect.top;
		var ratio = newScale / state.scale;
		var newTx = localX - (localX - state.tx) * ratio;
		var newTy = localY - (localY - state.ty) * ratio;
		setTransform(newTx, newTy, newScale, animate);
		clampToBounds(animate);
	}

	function toggleZoomAt(clientX, clientY) {
		var target = state.scale > state.fitScale + 0.001 ? state.fitScale : Math.min(maxScale(), state.fitScale * READ_SCALE_MULT);
		zoomTo(target, clientX, clientY, true);
	}

	function onPointerDown(e) {
		if (e.pointerType === 'mouse' && e.button !== 0) {
			return;
		}
		e.preventDefault();
		// setPointerCapture retarga los click/dblclick nativos posteriores
		// al elemento que capturó (viewport), sin importar sobre qué hijo
		// haya caído el puntero — por eso el target real se guarda ACÁ,
		// mientras e.target todavía es el del hit-test verdadero, y el
		// toggle de zoom / cierre por click se resuelve a mano en
		// onPointerUp en vez de depender de listeners 'click'/'dblclick'.
		viewport.setPointerCapture(e.pointerId);
		pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
		movedDuringGesture = false;

		if (pointers.size === 1) {
			pointerDownTarget = e.target === img ? 'image' : 'viewport';
			dragStart = { x: e.clientX, y: e.clientY, tx: state.tx, ty: state.ty };
			pinchStart = null;
		} else if (pointers.size === 2) {
			dragStart = null;
			var pts = Array.from(pointers.values());
			pinchStart = {
				dist: distance(pts[0], pts[1]),
				scale: state.scale,
				midX: (pts[0].x + pts[1].x) / 2,
				midY: (pts[0].y + pts[1].y) / 2,
				tx: state.tx,
				ty: state.ty,
			};
		}
	}

	function onPointerMove(e) {
		if (!pointers.has(e.pointerId)) {
			return;
		}
		pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });

		if (pointers.size === 1 && dragStart) {
			var dx = e.clientX - dragStart.x;
			var dy = e.clientY - dragStart.y;
			if (Math.abs(dx) > 3 || Math.abs(dy) > 3) {
				movedDuringGesture = true;
				viewport.classList.add('is-dragging');
			}
			setTransform(dragStart.tx + dx, dragStart.ty + dy, state.scale, false);
		} else if (pointers.size === 2 && pinchStart) {
			movedDuringGesture = true;
			var pts2 = Array.from(pointers.values());
			var dist = distance(pts2[0], pts2[1]);
			var newScale = clamp(pinchStart.scale * (dist / pinchStart.dist), minScale(), maxScale());
			var mid = { x: (pts2[0].x + pts2[1].x) / 2, y: (pts2[0].y + pts2[1].y) / 2 };
			var rect = viewport.getBoundingClientRect();
			var localX = pinchStart.midX - rect.left;
			var localY = pinchStart.midY - rect.top;
			var ratio = newScale / pinchStart.scale;
			var newTx = localX - (localX - pinchStart.tx) * ratio + (mid.x - pinchStart.midX);
			var newTy = localY - (localY - pinchStart.ty) * ratio + (mid.y - pinchStart.midY);
			setTransform(newTx, newTy, newScale, false);
		}
	}

	function onPointerUp(e) {
		// Click/tap simple (sin arrastre) sobre un único puntero: mouse Y
		// touch se resuelven acá igual, ya que el 'click'/'dblclick' nativo
		// del navegador queda retargeteado a `viewport` por la captura de
		// puntero (ver onPointerDown) y no sirve para distinguir imagen vs.
		// fondo de forma confiable.
		var wasSingleTap = pointers.size === 1 && !movedDuringGesture;
		var downTarget = pointerDownTarget;
		pointers.delete(e.pointerId);
		viewport.classList.remove('is-dragging');

		if (pointers.size === 0) {
			dragStart = null;
			pinchStart = null;
			clampToBounds(true);

			if (wasSingleTap && downTarget === 'image') {
				var now = Date.now();
				var pos = { x: e.clientX, y: e.clientY };
				if (lastTapTime && now - lastTapTime < 320 && lastTapPos && distance(lastTapPos, pos) < 30) {
					toggleZoomAt(pos.x, pos.y);
					lastTapTime = 0;
					lastTapPos = null;
				} else {
					lastTapTime = now;
					lastTapPos = pos;
				}
			} else if (wasSingleTap && downTarget === 'viewport') {
				// Click/tap fuera de la imagen (el fondo) cierra de una:
				// no hace falta esperar un segundo tap acá.
				lastTapTime = 0;
				lastTapPos = null;
				closeLightbox();
			}
		} else if (pointers.size === 1) {
			// quedó un solo dedo tras un pinch: pasa a ser un drag normal
			var pt = Array.from(pointers.values())[0];
			dragStart = { x: pt.x, y: pt.y, tx: state.tx, ty: state.ty };
			pinchStart = null;
		}
	}

	function onWheel(e) {
		e.preventDefault();
		var factor = e.deltaY < 0 ? 1.12 : 1 / 1.12;
		zoomTo(state.scale * factor, e.clientX, e.clientY, false);
	}

	function openLightbox(trigger) {
		buildDialog();
		lastFocused = trigger;

		var src = trigger.getAttribute('data-es-zoom-src');
		var triggerImg = trigger.querySelector('img');
		var alt = triggerImg ? triggerImg.alt : '';
		var natW = parseInt(trigger.getAttribute('data-es-zoom-w'), 10) || 0;
		var natH = parseInt(trigger.getAttribute('data-es-zoom-h'), 10) || 0;

		var closeLabel = trigger.getAttribute('data-es-zoom-close-label') || 'Close image';
		closeBtn.setAttribute('aria-label', closeLabel);
		zoomInBtn.setAttribute('aria-label', trigger.getAttribute('data-es-zoom-in-label') || 'Zoom in');
		zoomOutBtn.setAttribute('aria-label', trigger.getAttribute('data-es-zoom-out-label') || 'Zoom out');
		var resetLabel = trigger.getAttribute('data-es-zoom-reset-label') || 'Reset zoom';
		resetBtn.textContent = resetLabel;
		resetBtn.setAttribute('aria-label', resetLabel);
		dialog.setAttribute('aria-label', trigger.getAttribute('aria-label') || alt || closeLabel);

		img.src = src;
		img.alt = alt;

		// El caption se CLONA del propio <figure> — no se vuelve a escribir
		// contenido nuevo, así un lector de pantalla no lo escucha dos veces.
		var figure = trigger.closest('figure');
		var sourceCaption = figure ? figure.querySelector('.es-case-caption') : null;
		if (sourceCaption && sourceCaption.textContent.trim()) {
			captionEl.innerHTML = sourceCaption.innerHTML;
			dialog.classList.add('has-caption');
			captionEl.classList.toggle('is-hidden', window.innerHeight < 420);
		} else {
			captionEl.innerHTML = '';
			dialog.classList.remove('has-caption');
		}

		function onImgReady() {
			state.natW = img.naturalWidth || natW || 1;
			state.natH = img.naturalHeight || natH || 1;
			fitAndCenter();
		}

		// showModal() DEBE ejecutarse antes de cualquier fitAndCenter(): el
		// <dialog> está en display:none hasta que está open, así que medir
		// el viewport antes de abrirlo da un rect casi nulo y una escala de
		// ajuste basura (visto con imágenes data: URI, que reportan
		// img.complete/naturalWidth de forma síncrona).
		document.body.style.overflow = 'hidden';
		dialog.showModal();

		if (img.complete && img.naturalWidth) {
			onImgReady();
		} else {
			if (natW && natH) {
				// Adelanto con las dimensiones ya conocidas (metadata del
				// attachment) para que el "fit" inicial no salte cuando
				// termine de cargar la imagen real — se recalcula igual.
				state.natW = natW;
				state.natH = natH;
				fitAndCenter();
			}
			img.addEventListener('load', onImgReady, { once: true });
		}

		closeBtn.focus();
	}

	function closeLightbox() {
		if (dialog && dialog.open) {
			dialog.close();
		}
	}

	function wireEvents() {
		closeBtn.addEventListener('click', closeLightbox);
		zoomInBtn.addEventListener('click', function () {
			zoomTo(state.scale * STEP_MULT, null, null, true);
		});
		zoomOutBtn.addEventListener('click', function () {
			zoomTo(state.scale / STEP_MULT, null, null, true);
		});
		resetBtn.addEventListener('click', function () {
			zoomTo(state.fitScale, null, null, true);
		});
		captionToggle.addEventListener('click', function () {
			captionEl.classList.toggle('is-hidden');
		});

		// El toggle de zoom por doble click/tap y el cierre por click en el
		// fondo se resuelven en onPointerUp (ver comentario ahí): el click/
		// dblclick nativo queda retargeteado a `viewport` por la captura de
		// puntero y no distingue imagen vs. fondo de forma confiable.
		viewport.addEventListener('pointerdown', onPointerDown);
		viewport.addEventListener('pointermove', onPointerMove);
		viewport.addEventListener('pointerup', onPointerUp);
		viewport.addEventListener('pointercancel', onPointerUp);
		viewport.addEventListener('wheel', onWheel, { passive: false });

		dialog.addEventListener('close', function () {
			document.body.style.overflow = '';
			img.removeAttribute('src');
			pointers.clear();
			dragStart = null;
			pinchStart = null;
			if (lastFocused) {
				lastFocused.focus();
				lastFocused = null;
			}
		});

		window.addEventListener(
			'resize',
			function () {
				if (!dialog || !dialog.open) {
					return;
				}
				var wasAtFit = Math.abs(state.scale - state.fitScale) < 0.01;
				if (wasAtFit) {
					fitAndCenter();
				} else {
					clampToBounds(false);
				}
			},
			{ passive: true }
		);
	}

	function init() {
		var triggers = document.querySelectorAll('[data-es-zoom-trigger]');
		if (!triggers.length) {
			return;
		}
		document.addEventListener('click', function (e) {
			var trigger = e.target.closest && e.target.closest('[data-es-zoom-trigger]');
			if (trigger) {
				e.preventDefault();
				openLightbox(trigger);
			}
		});
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
