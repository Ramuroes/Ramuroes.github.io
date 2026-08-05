/**
 * ESTAVILLO — Case Flow (.es-flow) interaction layer.
 *
 * Vanilla, sin dependencias, ~4 KB. Todo lo que hace es OPCIONAL: el HTML
 * que emite el bloque ya se lee completo sin este archivo (los paneles de
 * detalle son bloques estáticos visibles por CSS). Recién cuando este
 * script corre marca el contenedor con .is-enhanced y los paneles pasan a
 * ser popover (desktop) o acordeón (mobile). Si el JS falla o está
 * bloqueado, no se pierde ni una línea de contenido.
 *
 * Comportamiento por dispositivo (requisito del ticket):
 *  - desktop (>=1280px, el mismo corte que el grid en serpentina de
 *    case-flow.css): hover y foco abren; salir del nodo cierra.
 *  - touch / mobile / tablet (<1280px, narrativa vertical): tap abre,
 *    segundo tap cierra, tap afuera cierra.
 *  - teclado, en los dos: el trigger es un <button> nativo (Enter/Espacio),
 *    Escape cierra y devuelve el foco al trigger.
 *
 * El popover se mantiene SIEMPRE dentro del viewport midiendo su rect real
 * y escribiendo --es-flow-shift (desplazamiento lateral) o .is-flip
 * (abrir hacia arriba) — no se confía en que "seguro entra".
 *
 * @package estavillo-child
 */
(function () {
	'use strict';

	var DESKTOP = '(min-width: 1280px)';
	var EDGE = 12; // margen mínimo contra el borde del viewport

	function isDesktop() {
		return window.matchMedia && window.matchMedia(DESKTOP).matches;
	}

	/* ---------------------------------------------------------------------
	 * Conectores medidos contra las formas visibles
	 * ------------------------------------------------------------------ */
	var SVG_NS = 'http://www.w3.org/2000/svg';

	function localRect(element, origin) {
		var rect = element.getBoundingClientRect();
		var left = rect.left - origin.left;
		var top = rect.top - origin.top;
		return {
			left: left,
			right: rect.right - origin.left,
			top: top,
			bottom: rect.bottom - origin.top,
			width: rect.width,
			height: rect.height,
			cx: left + rect.width / 2,
			cy: top + rect.height / 2,
		};
	}

	function edgePoint(rect, side) {
		if ('top' === side) {
			return { x: rect.cx, y: rect.top };
		}
		if ('right' === side) {
			return { x: rect.right, y: rect.cy };
		}
		if ('bottom' === side) {
			return { x: rect.cx, y: rect.bottom };
		}
		return { x: rect.left, y: rect.cy };
	}

	function compactPoints(points) {
		return points.filter(function (point, index) {
			if (!index) {
				return true;
			}
			var previous = points[index - 1];
			return Math.abs(point.x - previous.x) > 0.1 || Math.abs(point.y - previous.y) > 0.1;
		});
	}

	function pathData(points) {
		return points
			.map(function (point, index) {
				return (index ? 'L' : 'M') + Math.round(point.x * 10) / 10 + ' ' + Math.round(point.y * 10) / 10;
			})
			.join(' ');
	}

	function finishRoute(route) {
		var points = compactPoints(route.points);
		if (points.length < 2) {
			return null;
		}

		// La punta termina a 4px de la forma: alcanza visualmente el borde sin
		// pintar encima del contorno aprobado. En el indicador local de bucle
		// mobile no existe un destino geométrico, así que no se aplica el gap.
		if (!route.localLoop) {
			var last = points[points.length - 1];
			var previous = points[points.length - 2];
			var dx = last.x - previous.x;
			var dy = last.y - previous.y;
			var length = Math.sqrt(dx * dx + dy * dy) || 1;
			points[points.length - 1] = {
				x: last.x - (dx / length) * 4,
				y: last.y - (dy / length) * 4,
			};
		}

		route.points = points;
		return route;
	}

	/**
	 * Las pantallas intermedias alineadas con la decisión pertenecen al
	 * mismo eje: la rama secundaria sale por el vértice superior/inferior y
	 * continúa sin codos hasta el nodo siguiente. Las rutas no alineadas
	 * conservan el ruteo ortogonal genérico existente.
	 */
	function verticalBranchRoute(source, destination, route) {
		var isBranchRoute = ['branch', 'rejoin', 'loop'].indexOf(route) !== -1;
		if (!isBranchRoute || Math.abs(source.shape.cx - destination.shape.cx) >= 8) {
			return null;
		}

		var movesDown = destination.shape.cy > source.shape.cy;
		var start = edgePoint(source.shape, movesDown ? 'bottom' : 'top');
		var end = edgePoint(destination.shape, movesDown ? 'top' : 'bottom');
		// Los dos tramos de una rama comparten exactamente el centro de su
		// pantalla intermedia. Así entrada y salida conservan el mismo x aun
		// cuando el navegador redondea anchos impares a medios píxeles.
		var axis = source.branch
			? source.shape.cx
			: destination.branch
				? destination.shape.cx
				: (start.x + end.x) / 2;
		start.x = axis;
		end.x = axis;

		return {
			points: [start, end],
			label: {
				x: axis + 12,
				y: start.y + (movesDown ? 18 : -18),
				side: 'right',
			},
		};
	}

	function desktopRoute(source, destination, route, track) {
		var sameRow = Math.abs(source.shape.cy - destination.shape.cy) < 8;
		var label;
		var straightBranch = verticalBranchRoute(source, destination, route);
		if (straightBranch) {
			return straightBranch;
		}

		// Una salida condicional secundaria no comparte el vértice lateral con
		// la entrada o con la salida principal del rombo. Si el destino está
		// abajo nace del vértice inferior; si está arriba, del superior. Desde
		// ahí gira hacia el corredor libre entre columnas y llega al lateral de
		// la tarjeta condicional.
		if ('branch' === route && 'decision' === source.kind) {
			var branchBelow = destination.shape.cy > source.shape.cy;
			var branchMovesRight = destination.item.cx > source.item.cx;
			var branchStart = edgePoint(source.shape, branchBelow ? 'bottom' : 'top');
			var branchEnd = edgePoint(destination.shape, branchMovesRight ? 'left' : 'right');
			var branchCorridor = branchMovesRight
				? (source.item.right + destination.item.left) / 2
				: (destination.item.right + source.item.left) / 2;
			label = {
				x: branchCorridor + (branchMovesRight ? -16 : 16),
				y: branchStart.y + (branchBelow ? 22 : -22),
				side: branchMovesRight ? 'left' : 'right',
			};
			return {
				points: [branchStart, { x: branchCorridor, y: branchStart.y }, { x: branchCorridor, y: branchEnd.y }, branchEnd],
				label: label,
			};
		}

		if (sameRow) {
			var goesRight = destination.shape.cx > source.shape.cx;
			var start = edgePoint(source.shape, goesRight ? 'right' : 'left');
			var end = edgePoint(destination.shape, goesRight ? 'left' : 'right');
			label = { x: (start.x + end.x) / 2, y: start.y - 15, side: 'above' };
			return { points: [start, end], label: label };
		}

		// Misma columna, distinta fila: se sale por el lateral exterior de la
		// forma y se baja/sube por fuera del cuerpo. La descripción puede crecer
		// cuanto quiera: sólo modifica la longitud del tramo, nunca sus anclas.
		var sameColumn = Math.abs(source.item.cx - destination.item.cx) < Math.min(source.item.width, destination.item.width) * 0.45;
		if (sameColumn) {
			var useRight = source.item.cx >= (track.left + track.right) / 2;
			var side = useRight ? 'right' : 'left';
			var outside = useRight
				? Math.max(source.item.right, destination.item.right) + 14
				: Math.min(source.item.left, destination.item.left) - 14;
			var verticalStart = edgePoint(source.shape, side);
			var verticalEnd = edgePoint(destination.shape, side);
			label = { x: outside + (useRight ? -18 : 18), y: verticalStart.y + (verticalEnd.y > verticalStart.y ? 18 : -18), side: useRight ? 'left' : 'right' };
			return {
				points: [verticalStart, { x: outside, y: verticalStart.y }, { x: outside, y: verticalEnd.y }, verticalEnd],
				label: label,
			};
		}

		// Filas y columnas distintas: el tramo vertical vive en el gap REAL
		// entre los items, pero los extremos salen de los bordes de las formas.
		var movesRight = destination.item.cx > source.item.cx;
		var fromSide = movesRight ? 'right' : 'left';
		var toSide = movesRight ? 'left' : 'right';
		var corridor = movesRight
			? (source.item.right + destination.item.left) / 2
			: (destination.item.right + source.item.left) / 2;
		var diagonalStart = edgePoint(source.shape, fromSide);
		var diagonalEnd = edgePoint(destination.shape, toSide);
		label = { x: (diagonalStart.x + corridor) / 2, y: diagonalStart.y - 15, side: 'above' };
		return {
			points: [diagonalStart, { x: corridor, y: diagonalStart.y }, { x: corridor, y: diagonalEnd.y }, diagonalEnd],
			label: label,
		};
	}

	function mobileRoute(source, destination, route, track) {
		var straightBranch = verticalBranchRoute(source, destination, route);
		if (straightBranch) {
			return straightBranch;
		}

		// Un retorno a un nodo muy anterior no dibuja una línea gigante por la
		// página: muestra un codo local con flecha hacia arriba y el título real
		// del destino. El grafo y el texto accesible siguen describiendo la
		// conexión completa; sólo su representación decorativa se compacta.
		if ('loop' === route) {
			var loopStart = edgePoint(source.shape, 'left');
			var loopX = track.left - 12;
			return {
				localLoop: true,
				points: [loopStart, { x: loopX, y: loopStart.y }, { x: loopX, y: loopStart.y - 38 }],
				label: { x: loopX + 10, y: loopStart.y - 26, side: 'right' },
			};
		}

		// La entrada a una decisión usa normalmente su vértice superior. Cuando
		// ese vértice ya pertenece a una rama secundaria que vuelve hacia arriba,
		// la entrada principal llega por el vértice izquierdo: así ambas flechas
		// conservan anclas distintas sin alterar el eje vertical del retorno.
		if ('main' === route && 'decision' === destination.kind) {
			var decisionCorridor = track.left - 12;
			var decisionStart = edgePoint(source.shape, 'left');
			var entersFromLeft = destination.hasUpwardBranch;
			var decisionEnd = edgePoint(destination.shape, entersFromLeft ? 'left' : 'top');
			var decisionApproach = entersFromLeft ? decisionEnd.y : destination.shape.top - 12;
			var decisionPoints = [
				decisionStart,
				{ x: decisionCorridor, y: decisionStart.y },
				{ x: decisionCorridor, y: decisionApproach },
			];
			if (!entersFromLeft) {
				decisionPoints.push({ x: decisionEnd.x, y: decisionApproach });
			}
			decisionPoints.push(decisionEnd);
			return {
				points: decisionPoints,
				label: { x: decisionCorridor + 17, y: decisionStart.y + 18, side: 'right' },
			};
		}

		// La primera salida de una decisión salta la pantalla condicional en el
		// DOM. Se abre por la derecha; la segunda salida y el reingreso usan la
		// izquierda. Así Sí/No son dos caminos visibles, no dos textos flotantes.
		var skipsConditional = 'decision' === source.kind && 'main' === route && destination.order > source.order + 1;
		var useRight = skipsConditional;
		var side = useRight ? 'right' : 'left';
		var corridor = useRight ? track.right + 10 : track.left - 12;
		var start = edgePoint(source.shape, side);
		var end = edgePoint(destination.shape, side);
		return {
			points: [start, { x: corridor, y: start.y }, { x: corridor, y: end.y }, end],
			label: {
				x: corridor + (useRight ? -17 : 17),
				y: start.y + (end.y >= start.y ? 18 : -18),
				side: useRight ? 'left' : 'right',
			},
		};
	}

	function arrowPoints(points) {
		var tip = points[points.length - 1];
		var previous = points[points.length - 2];
		var dx = tip.x - previous.x;
		var dy = tip.y - previous.y;
		var length = Math.sqrt(dx * dx + dy * dy) || 1;
		var ux = dx / length;
		var uy = dy / length;
		var baseX = tip.x - ux * 7;
		var baseY = tip.y - uy * 7;
		var px = -uy * 4;
		var py = ux * 4;
		return [
			{ x: baseX + px, y: baseY + py },
			tip,
			{ x: baseX - px, y: baseY - py },
		];
	}

	function setupConnectors(flow, items) {
		var diagram = flow.querySelector('[data-es-flow-diagram]');
		var trackElement = flow.querySelector('.es-flow__track');
		var svg = flow.querySelector('[data-es-flow-connectors]');
		var labels = flow.querySelector('[data-es-flow-connector-labels]');
		var edgeElements = Array.prototype.slice.call(flow.querySelectorAll('[data-es-flow-edge]'));
		if (!diagram || !trackElement || !svg || !labels || !edgeElements.length) {
			return;
		}

		var nodes = {};
		items.forEach(function (item, index) {
			var id = item.getAttribute('data-es-flow-node-id');
			var shape = item.querySelector('[data-es-flow-shape]');
			item.style.setProperty('--es-flow-mobile-order', index * 2);
			if (id && shape) {
				nodes[id] = {
					item: item,
					shape: shape,
					order: index,
					kind: item.getAttribute('data-es-flow-node-kind') || 'step',
					branch: item.classList.contains('es-flow__item--branch'),
					hasUpwardBranch: false,
				};
			}
		});

		// El render conserva el orden semántico en el DOM y ubica las pantallas
		// de rama en un carril vecino como fallback. Cuando decisión y destino
		// comparten columna, el overlay aprobado necesita que la pantalla
		// intermedia se apoye sobre ese mismo eje. La clase sólo altera la
		// geometría visual; los datos, enlaces y orden accesible permanecen.
		var edgeData = edgeElements.map(function (edge) {
			return {
				from: edge.getAttribute('data-es-flow-from'),
				to: edge.getAttribute('data-es-flow-to'),
				route: edge.getAttribute('data-es-flow-route') || 'main',
			};
		});
		edgeData.forEach(function (edge) {
			if ('branch' !== edge.route || !nodes[edge.from] || !nodes[edge.to]) {
				return;
			}

			var continuation = edgeData.find(function (candidate) {
				return candidate.from === edge.to && ['rejoin', 'loop'].indexOf(candidate.route) !== -1;
			});
			if (!continuation || !nodes[continuation.to]) {
				return;
			}

			var source = nodes[edge.from];
			var branch = nodes[edge.to];
			var destination = nodes[continuation.to];
			var sourceColumn = source.item.style.getPropertyValue('--c').trim();
			var destinationColumn = destination.item.style.getPropertyValue('--c').trim();
			if (!sourceColumn || sourceColumn !== destinationColumn) {
				return;
			}

			branch.item.classList.add('is-axis-aligned-branch');
			branch.item.style.setProperty('--es-flow-branch-column', sourceColumn);

			if ('rejoin' === continuation.route) {
				source.item.classList.add('has-axis-branch-below');
				branch.item.classList.add('has-axis-branch-below');
			}

			// En la narrativa de una columna, un retorno al paso inmediatamente
			// anterior se muestra entre ese paso y la decisión. El DOM no cambia:
			// lectores de pantalla conservan Decisión → rama → salida principal.
			if ('loop' === continuation.route && source.order === destination.order + 1) {
				source.hasUpwardBranch = true;
				branch.item.classList.add('has-axis-loop-text-clearance');
				destination.item.classList.add('has-axis-loop-text-clearance');
				branch.item.style.setProperty('--es-flow-mobile-order', source.order * 2 - 1);
			}
		});

		var frame = 0;
		function render() {
			frame = 0;
			var origin = diagram.getBoundingClientRect();
			if (!origin.width || !origin.height) {
				flow.classList.remove('has-svg-connectors');
				return;
			}

			var track = localRect(trackElement, origin);
			var geometry = {};
			Object.keys(nodes).forEach(function (id) {
				geometry[id] = {
					shape: localRect(nodes[id].shape, origin),
					item: localRect(nodes[id].item, origin),
					order: nodes[id].order,
					kind: nodes[id].kind,
					branch: nodes[id].branch,
					hasUpwardBranch: nodes[id].hasUpwardBranch,
					title: (nodes[id].shape.querySelector('.es-flow__title') || {}).textContent || id,
				};
			});

			svg.setAttribute('viewBox', '0 0 ' + origin.width + ' ' + origin.height);
			svg.setAttribute('width', origin.width);
			svg.setAttribute('height', origin.height);
			while (svg.firstChild) {
				svg.removeChild(svg.firstChild);
			}
			while (labels.firstChild) {
				labels.removeChild(labels.firstChild);
			}

			var drawn = 0;
			edgeElements.forEach(function (edge, index) {
				var from = edge.getAttribute('data-es-flow-from');
				var to = edge.getAttribute('data-es-flow-to');
				var routeName = edge.getAttribute('data-es-flow-route') || 'main';
				var labelText = edge.getAttribute('data-es-flow-label') || '';
				var source = geometry[from];
				var destination = geometry[to];
				if (!source || !destination) {
					return;
				}

				var route = isDesktop()
					? desktopRoute(source, destination, routeName, track)
					: mobileRoute(source, destination, routeName, track);
				route = finishRoute(route);
				if (!route) {
					return;
				}

				var group = document.createElementNS(SVG_NS, 'g');
				group.setAttribute('class', 'es-flow__connection is-route-' + routeName);
				group.setAttribute('data-flow-from', from);
				group.setAttribute('data-flow-to', to);
				group.setAttribute('data-flow-route', routeName);
				group.style.setProperty('--i', index);

				var line = document.createElementNS(SVG_NS, 'path');
				line.setAttribute('class', 'es-flow__connection-line');
				line.setAttribute('d', pathData(route.points));
				line.setAttribute('pathLength', '1');
				line.setAttribute('vector-effect', 'non-scaling-stroke');
				group.appendChild(line);

				var head = document.createElementNS(SVG_NS, 'polyline');
				head.setAttribute('class', 'es-flow__connection-head');
				head.setAttribute(
					'points',
					arrowPoints(route.points)
						.map(function (point) { return point.x + ',' + point.y; })
						.join(' ')
				);
				head.setAttribute('vector-effect', 'non-scaling-stroke');
				group.appendChild(head);
				svg.appendChild(group);

				if (labelText || route.localLoop) {
					var label = document.createElement('span');
					label.className = 'es-flow__connection-label' + (route.localLoop ? ' is-loop-label' : '');
					label.textContent = route.localLoop ? '\u21a9 ' + destination.title.trim() : labelText;
					label.setAttribute('data-flow-from', from);
					label.setAttribute('data-flow-to', to);
					label.setAttribute('data-flow-route', routeName);
					label.style.left = route.label.x + 'px';
					label.style.top = route.label.y + 'px';
					label.setAttribute('data-label-side', route.label.side || 'above');
					labels.appendChild(label);
				}
				drawn += 1;
			});

			flow.classList.toggle('has-svg-connectors', drawn > 0);
		}

		function schedule() {
			if (frame) {
				window.cancelAnimationFrame(frame);
			}
			frame = window.requestAnimationFrame(function () {
				frame = window.requestAnimationFrame(render);
			});
		}

		if ('ResizeObserver' in window) {
			var resizeObserver = new ResizeObserver(schedule);
			resizeObserver.observe(diagram);
			resizeObserver.observe(trackElement);
			Object.keys(nodes).forEach(function (id) {
				resizeObserver.observe(nodes[id].shape);
			});
		}
		window.addEventListener('resize', schedule, { passive: true });
		window.addEventListener('load', schedule, { once: true });
		flow.addEventListener('animationend', function (event) {
			if (event.target && event.target.matches && event.target.matches('[data-es-flow-item]')) {
				schedule();
			}
		});
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(schedule);
		}
		// Mobile no espera dos frames ni una animación de dibujo: el overlay se
		// calcula en el mismo ciclo de inicialización. Los observadores siguen
		// corrigiendo cualquier cambio posterior de fuentes o dimensiones.
		if (isDesktop()) {
			schedule();
		} else {
			render();
		}
	}

	function setup(flow) {
		var items = Array.prototype.slice.call(flow.querySelectorAll('[data-es-flow-item]'));
		if (!items.length) {
			return;
		}

		// A partir de acá el CSS puede esconder paneles con seguridad.
		flow.classList.add('is-enhanced');

		// --i alimenta el retraso escalonado de la animación de entrada
		// (case-flow.css, .es-flow.is-revealed) — mismo orden que el DOM,
		// que ya es el orden de lectura real de la narrativa serpenteada.
		items.forEach(function (item, idx) {
			item.style.setProperty('--i', idx);
		});

		setupConnectors(flow, items);

		var openItem = null;

		function panelOf(item) {
			return item.querySelector('[data-es-flow-panel]');
		}

		function triggerOf(item) {
			return item.querySelector('[data-es-flow-trigger]');
		}

		/**
		 * Mantiene el popover dentro del viewport: primero resetea, mide, y
		 * recién ahí corrige. Sólo aplica en desktop (en mobile el panel es
		 * un bloque en flujo, no puede salirse).
		 */
		function place(item) {
			var panel = panelOf(item);
			if (!panel || !isDesktop()) {
				return;
			}

			item.classList.remove('is-flip');
			panel.style.setProperty('--es-flow-shift', '0px');

			var rect = panel.getBoundingClientRect();
			var shift = 0;

			if (rect.left < EDGE) {
				shift = EDGE - rect.left;
			} else if (rect.right > window.innerWidth - EDGE) {
				shift = window.innerWidth - EDGE - rect.right;
			}

			if (shift) {
				panel.style.setProperty('--es-flow-shift', Math.round(shift) + 'px');
			}

			// Si no entra abajo pero sí arriba, se abre hacia arriba.
			var after = panel.getBoundingClientRect();
			if (after.bottom > window.innerHeight - EDGE && after.height + EDGE < item.getBoundingClientRect().top) {
				item.classList.add('is-flip');
			}
		}

		function close(item) {
			if (!item) {
				return;
			}
			item.classList.remove('is-open', 'is-flip');
			var trigger = triggerOf(item);
			if (trigger) {
				trigger.setAttribute('aria-expanded', 'false');
			}
			if (openItem === item) {
				openItem = null;
				flow.classList.remove('has-open-panel');
			}
		}

		function open(item) {
			if (openItem && openItem !== item) {
				close(openItem);
			}
			item.classList.add('is-open');
			flow.classList.add('has-open-panel');
			var trigger = triggerOf(item);
			if (trigger) {
				trigger.setAttribute('aria-expanded', 'true');
			}
			openItem = item;
			place(item);
		}

		function toggle(item) {
			if (item.classList.contains('is-open')) {
				close(item);
			} else {
				open(item);
			}
		}

		items.forEach(function (item) {
			var trigger = triggerOf(item);
			if (!trigger || !panelOf(item)) {
				return; // nodo sin detalle: no hay nada que abrir
			}

			// Click / tap / Enter / Espacio — un <button> nativo cubre los tres.
			trigger.addEventListener('click', function () {
				toggle(item);
			});

			// Desktop: hover abre sin pedir click.
			item.addEventListener('mouseenter', function () {
				if (isDesktop()) {
					open(item);
				}
			});

			item.addEventListener('mouseleave', function () {
				// No se cierra si el foco de teclado sigue adentro del nodo.
				if (isDesktop() && !item.contains(document.activeElement)) {
					close(item);
				}
			});

			// Foco de teclado: abre al entrar, cierra al salir del nodo entero.
			item.addEventListener('focusin', function () {
				if (isDesktop()) {
					open(item);
				}
			});

			item.addEventListener('focusout', function (ev) {
				if (!isDesktop()) {
					return;
				}
				if (!item.contains(ev.relatedTarget)) {
					close(item);
				}
			});

			var closeBtn = item.querySelector('[data-es-flow-close]');
			if (closeBtn) {
				closeBtn.addEventListener('click', function () {
					close(item);
					if (trigger) {
						trigger.focus();
					}
				});
			}
		});

		// Escape cierra y devuelve el foco al trigger (comportamiento predecible).
		flow.addEventListener('keydown', function (ev) {
			if ('Escape' === ev.key && openItem) {
				var trigger = triggerOf(openItem);
				close(openItem);
				if (trigger) {
					trigger.focus();
				}
			}
		});

		// Tap/click afuera cierra (requisito explícito en touch).
		document.addEventListener('click', function (ev) {
			if (openItem && !openItem.contains(ev.target)) {
				close(openItem);
			}
		});

		// Reposicionar si cambia el viewport con un popover abierto.
		window.addEventListener(
			'resize',
			function () {
				if (openItem) {
					place(openItem);
				}
			},
			{ passive: true }
		);

		// ---- indicador de progreso 01/08 (mobile) ----
		var current = flow.querySelector('[data-es-flow-current]');
		if (current && 'IntersectionObserver' in window) {
			var pad = function (n) {
				return (n < 10 ? '0' : '') + n;
			};
			var observer = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							var idx = entry.target.getAttribute('data-es-flow-index');
							if (idx) {
								current.textContent = pad(parseInt(idx, 10));
							}
						}
					});
				},
				{ rootMargin: '-45% 0px -45% 0px' }
			);
			items.forEach(function (item) {
				observer.observe(item);
			});
		}

		// ---- animación de entrada, una sola vez ----
		// Se dispara cuando ~30% del componente entra en el viewport y
		// nunca se repite (se desconecta después del primer disparo).
		// En mobile/tablet el Case Flow no usa motion: se revela completo en
		// el primer ciclo, sin esperar al IntersectionObserver. Desktop conserva
		// la secuencia aprobada salvo que el sistema pida reduced motion.
		var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		if (!isDesktop() || reduceMotion || !('IntersectionObserver' in window)) {
			flow.classList.add('is-revealed');
		} else {
			var revealObserver = new IntersectionObserver(
				function (entries, obs) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							flow.classList.add('is-revealed');
							obs.disconnect();
						}
					});
				},
				{ threshold: 0.3 }
			);
			revealObserver.observe(flow);
		}
	}

	function init() {
		Array.prototype.slice.call(document.querySelectorAll('[data-es-flow]')).forEach(setup);
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
