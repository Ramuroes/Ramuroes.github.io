/**
 * ESTAVILLO — REstimator Design System: visor de pantallas
 * ---------------------------------------------------------------------------
 * Abre las capturas de §08 Screen Examples como se pensaron en el kit: una
 * VENTANA DE APLICACIÓN que se recorre verticalmente, no una imagen suelta.
 *
 * Por qué no se reusa el lightbox del portfolio (assets/js/case-figure-lightbox.js):
 * ese visor es de fotos. Ajusta la imagen entera al viewport y después deja
 * hacer pan y zoom sobre ella. Con una captura full-page de 1440×3224 el
 * "ajustar entero" la reduce a ~28%: la pantalla entra completa pero no se lee
 * nada, y lo que se ve es una infografía vertical gigante en vez de una
 * interfaz. Acá el criterio es el contrario: la pantalla se muestra a su ANCHO
 * REAL (o al máximo que entre) y el alto lo limita el navegador; lo que sobra
 * se recorre con scroll, igual que se recorrería la aplicación de verdad.
 *
 * El lightbox del portfolio queda intacto y se sigue usando en los case
 * studies: son dos problemas distintos con dos visores distintos.
 *
 * Cómo se dimensiona:
 *  - `data-es-screen-cssw` trae el ancho de la INTERFAZ (1440 desktop, 390
 *    mobile), no el del archivo — la captura está tomada a 2×/3×;
 *  - el ancho de trabajo arranca en min(anchoReal, anchoDisponible), así nunca
 *    hay scroll horizontal accidental al abrir;
 *  - el zoom mueve ese ancho entre el de ajuste y 2× el real. Recién ahí, con
 *    zoom deliberado, puede aparecer scroll horizontal.
 *
 * Accesibilidad: <dialog>.showModal() aporta el foco atrapado, la capa
 * superior y el cierre por Escape del propio navegador. El foco vuelve al
 * botón que abrió. El área de scroll es focusable por teclado, así que las
 * flechas / PageUp / PageDown recorren la pantalla sin tocar el mouse.
 *
 * Mejora progresiva: sin este script las tarjetas siguen mostrando la preview
 * y el documento se lee igual; sólo no se puede abrir la pantalla completa.
 *
 * @package estavillo-child
 */
(function () {
	'use strict';

	var MIN_MARGIN = 24; // aire mínimo entre la ventana y el borde del viewport
	var MIN_MARGIN_TOUCH = 8; // en un teléfono cada píxel de ancho cuenta
	var STEP = 1.25;
	var MAX_FACTOR = 2; // tope de zoom: 2× el ancho real de la interfaz

	/*
	 * Los controles son iconos: el texto accesible llega desde PHP, ya resuelto
	 * al idioma del documento (inc/enqueue.php → wp_localize_script). Los
	 * fallbacks en inglés cubren el caso de que el script se cargue suelto.
	 */
	var L10N = window.esDsViewerL10n || {};
	var TXT = {
		close: L10N.close || 'Close the screen',
		zoomIn: L10N.zoomIn || 'Zoom in',
		zoomOut: L10N.zoomOut || 'Zoom out',
		scroll: L10N.scroll || 'Full screen — use the arrow keys to scroll through it',
	};

	var dialog = null;
	var win, bar, titleEl, metaEl, scroller, img, levelEl;
	var zoomInBtn, zoomOutBtn, closeBtn;
	var lastFocused = null;

	// Ancho de la interfaz y ancho mostrado, en px CSS.
	var baseW = 0;
	var shownW = 0;

	function el(tag, cls, parent) {
		var node = document.createElement(tag);
		if (cls) {
			node.className = cls;
		}
		if (parent) {
			parent.appendChild(node);
		}
		return node;
	}

	function build() {
		if (dialog) {
			return;
		}

		dialog = el('dialog', 'esv');
		win = el('div', 'esv__win', dialog);

		bar = el('div', 'esv__bar', win);
		var idc = el('div', 'esv__id', bar);
		titleEl = el('span', 'esv__title', idc);
		metaEl = el('span', 'esv__meta', idc);

		var tools = el('div', 'esv__tools', bar);
		zoomOutBtn = el('button', 'esv__btn', tools);
		zoomOutBtn.type = 'button';
		zoomOutBtn.setAttribute('aria-label', TXT.zoomOut);
		zoomOutBtn.innerHTML = '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M5 10h10"/></svg>';

		levelEl = el('span', 'esv__level', tools);

		zoomInBtn = el('button', 'esv__btn', tools);
		zoomInBtn.type = 'button';
		zoomInBtn.setAttribute('aria-label', TXT.zoomIn);
		zoomInBtn.innerHTML = '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M5 10h10M10 5v10"/></svg>';

		closeBtn = el('button', 'esv__btn esv__btn--close', tools);
		closeBtn.type = 'button';
		closeBtn.setAttribute('aria-label', TXT.close);
		closeBtn.innerHTML = '<svg viewBox="0 0 20 20" aria-hidden="true"><path d="M5 5l10 10M15 5L5 15"/></svg>';

		scroller = el('div', 'esv__scroll', win);
		// tabindex para que las flechas y PageUp/PageDown recorran la pantalla:
		// un contenedor con overflow no recibe foco de teclado por su cuenta.
		// Con rol y nombre, un lector de pantalla lo anuncia como región
		// recorrible en vez de como un div sin sentido que recibe foco.
		scroller.tabIndex = 0;
		scroller.setAttribute('role', 'group');
		scroller.setAttribute('aria-label', TXT.scroll);
		img = el('img', 'esv__img', scroller);
		img.alt = '';
		img.decoding = 'async';

		document.body.appendChild(dialog);
		wire();
	}

	/**
	 * Espacio utilizable para la ventana, descontando márgenes y la barra.
	 *
	 * Se mide con visualViewport y no con window.innerHeight: en iOS Safari
	 * innerHeight NO cambia cuando la barra de URL se colapsa, así que la
	 * ventana se calculaba contra un alto que en pantalla no existía y el
	 * lienzo terminaba empujado fuera del viewport. visualViewport sí sigue el
	 * alto realmente visible. En desktop los dos valores coinciden, así que no
	 * cambia nada.
	 */
	function room() {
		var vv = window.visualViewport;
		var vw = vv ? vv.width : window.innerWidth;
		var vh = vv ? vv.height : window.innerHeight;
		var margin = coarse() ? MIN_MARGIN_TOUCH : MIN_MARGIN;
		return {
			w: Math.max(240, vw - margin * 2),
			h: Math.max(200, vh - margin * 2 - bar.offsetHeight),
		};
	}

	/** ¿Puntero grueso? Decide sólo el comportamiento TÁCTIL; desktop no cambia. */
	function coarse() {
		return !!(window.matchMedia && window.matchMedia('(pointer: coarse)').matches);
	}

	/**
	 * Aplica un ancho de imagen y ajusta la ventana.
	 *
	 * La ventana nunca es más ancha que la imagen mostrada: así una pantalla de
	 * mobile (390px) se ve como un teléfono centrado y no como una franja
	 * angosta perdida en una ventana de 1600px.
	 */
	function apply(width) {
		var r = room();
		shownW = Math.max(80, Math.min(width, baseW * MAX_FACTOR));

		img.style.width = shownW + 'px';

		/*
		 * En táctil la ventana es una hoja a pantalla completa y la dimensiona
		 * el CSS. Si el JS le fijara un ancho y un alto, la ventana cambiaría de
		 * tamaño DURANTE el pinch —al crecer la imagen crece el lienzo— y la
		 * barra de título se movería bajo los dedos. Con la hoja fija, lo único
		 * que escala es la captura.
		 */
		var winW;
		if (coarse()) {
			win.style.width = '';
			scroller.style.maxHeight = '';
			winW = r.w;
		} else {
			winW = Math.min(shownW, r.w);
			win.style.width = winW + 'px';
			scroller.style.maxHeight = r.h + 'px';
		}

		// Con zoom por encima del ancho disponible el scroll horizontal es
		// deliberado; al ajuste o por debajo no debe existir.
		scroller.classList.toggle('is-wide', shownW > winW + 1);

		levelEl.textContent = baseW ? Math.round((shownW / baseW) * 100) + '%' : '';
		zoomOutBtn.disabled = shownW <= fitWidth() + 1;
		zoomInBtn.disabled = shownW >= baseW * MAX_FACTOR - 1;
	}

	/** Ancho de arranque: el real de la interfaz, o el que entre si no cabe. */
	function fitWidth() {
		return Math.min(baseW, room().w);
	}

	/**
	 * Ancho con el que ABRE el visor.
	 *
	 * En desktop es el de ajuste. En un teléfono no: ajustar una captura de
	 * 1440px de ancho a una ventana de 374 la deja al 26%, que es exactamente
	 * el "chorizo" ilegible que este visor existe para evitar — la pantalla
	 * entra entera y no se lee una palabra.
	 *
	 * Con puntero grueso abre a ~2× el ancho de la ventana: se ve una parte de
	 * la pantalla a un tamaño en el que el texto se lee, y el resto se recorre
	 * con el dedo. Nunca por encima del ancho real de la interfaz, así que una
	 * captura de mobile (390px) sigue entrando completa y sin scroll lateral.
	 */
	function openWidth() {
		var r = room();
		if (!coarse()) {
			return fitWidth();
		}
		return Math.max(fitWidth(), Math.min(baseW, r.w * 2));
	}

	function zoom(factor) {
		var next = shownW * factor;
		var fit = fitWidth();
		apply(Math.max(fit, next));
	}

	function open(trigger) {
		build();
		lastFocused = trigger;

		var natW = parseInt(trigger.getAttribute('data-es-screen-w'), 10) || 0;
		var natH = parseInt(trigger.getAttribute('data-es-screen-h'), 10) || 0;
		var name = trigger.getAttribute('data-es-screen-name') || '';
		var meta = trigger.getAttribute('data-es-screen-meta') || '';

		baseW = parseInt(trigger.getAttribute('data-es-screen-cssw'), 10) || natW || 1200;

		titleEl.textContent = name;
		metaEl.textContent = meta;
		dialog.setAttribute('aria-label', trigger.getAttribute('aria-label') || name);

		// width/height reales evitan que la ventana salte cuando termina de
		// cargar la imagen: el alto ya queda reservado por el aspect-ratio.
		if (natW && natH) {
			img.width = natW;
			img.height = natH;
		}
		img.alt = name;
		img.src = trigger.getAttribute('data-es-screen-src') || '';

		document.body.style.overflow = 'hidden';
		// showModal() PRIMERO: hasta que el <dialog> está abierto sigue en
		// display:none y medir la barra o el viewport da valores basura.
		dialog.showModal();

		scroller.scrollTop = 0;
		scroller.scrollLeft = 0;
		apply(openWidth());

		closeBtn.focus();
	}

	function close() {
		if (dialog && dialog.open) {
			dialog.close();
		}
	}

	/* ======================================================================
	   Pinch-to-zoom (sólo puntero grueso)
	   ----------------------------------------------------------------------
	   El problema: sin ningún handler, un pinch sobre la pantalla ampliada lo
	   toma el navegador y hace zoom de la PÁGINA de WordPress — el visor se
	   agranda entero, incluida su barra, y el documento queda desencuadrado.

	   La solución tiene dos mitades, y las dos hacen falta:

	    · `touch-action` en el lienzo (doc-overrides.css §14). Al ancho de ajuste
	      vale `pan-y` y con la captura más ancha que el lienzo —clase
	      `is-wide`— vale `pan-x pan-y`: en los dos casos el scroll de un dedo
	      lo sigue haciendo el navegador, nativo y con inercia, y sólo el gesto
	      de dos dedos queda para nosotros. Nunca `none`: eso mataría el scroll
	      natural, que es la forma principal de recorrer una captura alta.
	    · Pointer Events. Se rastrean los punteros activos; con dos, la
	      distancia entre ellos controla la escala y el punto medio hace de
	      ancla, así el zoom "tira" de donde están los dedos y no del centro.

	   `preventDefault()` se llama SÓLO con dos punteros. Con uno, el evento
	   sigue su curso y el scroll nativo funciona como siempre.

	   El chrome del visor queda fuera: los listeners van en el lienzo, no en el
	   diálogo, así la barra, el %, los botones y el cerrar nunca escalan.
	   ====================================================================== */

	var pointers = new Map();
	var pinch = null; // { dist, width, cx, cy, sl, st }

	function pointerDistance() {
		var pts = Array.from(pointers.values());
		var dx = pts[0].x - pts[1].x;
		var dy = pts[0].y - pts[1].y;
		return Math.sqrt(dx * dx + dy * dy);
	}

	function pointerMid() {
		var pts = Array.from(pointers.values());
		return { x: (pts[0].x + pts[1].x) / 2, y: (pts[0].y + pts[1].y) / 2 };
	}

	function onPointerDown(e) {
		if (e.pointerType === 'mouse') {
			return;
		}
		pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
		if (pointers.size === 2) {
			var mid = pointerMid();
			var rect = scroller.getBoundingClientRect();
			pinch = {
				dist: pointerDistance(),
				width: shownW,
				// Punto del CONTENIDO bajo los dedos, en coordenadas del lienzo:
				// es lo que hay que mantener quieto mientras cambia la escala.
				cx: (scroller.scrollLeft + mid.x - rect.left) / shownW,
				cy: (scroller.scrollTop + mid.y - rect.top) / shownW,
				mx: mid.x - rect.left,
				my: mid.y - rect.top,
			};
		}
	}

	function onPointerMove(e) {
		if (!pointers.has(e.pointerId)) {
			return;
		}
		pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
		if (pointers.size !== 2 || !pinch) {
			return;
		}
		// Recién acá se corta el gesto nativo: con un solo dedo nunca.
		e.preventDefault();

		var next = pinch.width * (pointerDistance() / pinch.dist);
		apply(Math.max(fitWidth(), next));

		// Reancla el punto que estaba bajo los dedos.
		scroller.scrollLeft = pinch.cx * shownW - pinch.mx;
		scroller.scrollTop = pinch.cy * shownW - pinch.my;
	}

	function onPointerUp(e) {
		pointers.delete(e.pointerId);
		if (pointers.size < 2) {
			pinch = null;
		}
	}

	function wirePinch() {
		if (!coarse() || !window.PointerEvent) {
			return;
		}
		scroller.addEventListener('pointerdown', onPointerDown);
		scroller.addEventListener('pointermove', onPointerMove, { passive: false });
		scroller.addEventListener('pointerup', onPointerUp);
		scroller.addEventListener('pointercancel', onPointerUp);
		scroller.addEventListener('pointerleave', onPointerUp);
	}

	function wire() {
		wirePinch();
		closeBtn.addEventListener('click', close);
		zoomInBtn.addEventListener('click', function () {
			zoom(STEP);
		});
		zoomOutBtn.addEventListener('click', function () {
			zoom(1 / STEP);
		});

		// Click fuera de la ventana cierra. El <dialog> ocupa toda la pantalla,
		// así que "afuera" es cualquier punto que no caiga dentro de .esv__win.
		dialog.addEventListener('click', function (e) {
			if (!win.contains(e.target)) {
				close();
			}
		});

		dialog.addEventListener('close', function () {
			document.body.style.overflow = '';
			img.removeAttribute('src');
			pointers.clear();
			pinch = null;
			if (lastFocused) {
				lastFocused.focus();
				lastFocused = null;
			}
		});

		var reflow = function () {
			if (dialog && dialog.open) {
				apply(shownW);
			}
		};
		window.addEventListener('resize', reflow, { passive: true });
		// En iOS la barra de URL cambia el alto visible SIN disparar 'resize'
		// en window: el evento que sí llega es el de visualViewport.
		if (window.visualViewport) {
			window.visualViewport.addEventListener('resize', reflow, { passive: true });
		}
	}

	function init() {
		if (!document.querySelector('[data-es-screen-trigger]')) {
			return;
		}
		// <dialog> con showModal es lo que aporta foco atrapado y Escape. Sin
		// soporte, mejor no interceptar el click: la tarjeta queda inerte pero
		// la preview se sigue viendo, en vez de abrir un modal a medias.
		if (typeof HTMLDialogElement === 'undefined' || !HTMLDialogElement.prototype.showModal) {
			return;
		}

		document.addEventListener('click', function (e) {
			var trigger = e.target.closest && e.target.closest('[data-es-screen-trigger]');
			if (trigger) {
				e.preventDefault();
				open(trigger);
			}
		});
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
