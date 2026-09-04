/**
 * ESTAVILLO — i18n del REstimator Design System
 * ---------------------------------------------------------------------------
 * Extrae los segmentos traducibles del documento del Design System y los
 * reemplaza por su versión en el idioma pedido, para que master-es.php y
 * master-en.php salgan de UNA sola fuente estructural en vez de ser dos
 * documentos mantenidos a mano.
 *
 * Por qué a nivel de BLOQUE y no de nodo de texto: una frase del documento
 * suele venir partida en varios nodos por el markup inline
 * (`El token real es <span class="mono">#7f858e</span> (elevado para AA)`).
 * Traducir fragmento por fragmento daría un inglés con el orden de palabras
 * del español. Tomando el innerHTML del bloque hoja completo, la traducción
 * controla el orden y conserva los tramos técnicos intactos.
 *
 * Qué NUNCA se traduce (queda idéntico en los dos idiomas):
 *  - `<span class="mono">`, `<code>`, `.tok` — tokens, rutas, identificadores;
 *  - `<svg>` y todo su subárbol;
 *  - segmentos sin letras (números, `5 + 3`, `·`), que no tienen nada que traducir;
 *  - segmentos que, sacándoles los tramos técnicos, quedan vacíos.
 *
 * @package estavillo-child
 */

/* ==========================================================================
   Parser HTML mínimo
   --------------------------------------------------------------------------
   El documento es HTML bien formado y sin scripts (el build ya lo separa),
   así que alcanza con un tokenizador + pila. No se usa un DOM de navegador
   para que el stage `html` del build no dependa de Playwright.
   ========================================================================== */

/** Elementos vacíos: no abren nivel en la pila. */
const VOID = new Set(['br', 'hr', 'img', 'input', 'meta', 'link', 'source', 'col', 'area', 'base', 'embed', 'param', 'track', 'wbr']);

/** Subárboles opacos: se copian tal cual, nunca se miran adentro. */
const OPAQUE = new Set(['svg', 'code', 'pre', 'script', 'style']);

/**
 * Encuentra el `>` que CIERRA la etiqueta abierta en `lt`, ignorando los que
 * viven dentro del valor entrecomillado de un atributo.
 *
 * No es un refinamiento teórico. El documento que produce el build lleva PHP
 * dentro de los atributos de las tarjetas de pantalla:
 *
 *     <button … data-es-zoom-src="<?php echo esc_url( … ); ?>" …>
 *
 * Con un `indexOf('>')` pelado, la etiqueta se cortaba en el `>` del `?>`: el
 * parser veía un `<button>` truncado y después trataba cada `<?php` restante
 * como un elemento sin nombre, que al serializar escupía un `</>` de más y
 * dejaba un nivel abierto en la pila. Los cierres siguientes se aplicaban al
 * elemento equivocado y el árbol terminaba mal anidado: `</div></section></div>`
 * caía justo después de la grilla de §08 y cerraba `.pad` 11 KB antes de tiempo,
 * así que "Otras piezas", §09 y §10 se publicaban FUERA del contenedor y perdían
 * el padding lateral del documento.
 *
 * @param {string} html
 * @param {number} lt Posición del `<`.
 * @returns {number} Índice del `>`, o -1.
 */
function findTagEnd(html, lt) {
	let quote = null;
	for (let i = lt + 1; i < html.length; i++) {
		const c = html[i];
		if (quote) {
			if (c === quote) quote = null;
			continue;
		}
		if (c === '"' || c === "'") {
			quote = c;
			continue;
		}
		if (c === '>') return i;
	}
	return -1;
}

/**
 * Parsea HTML a un árbol liviano.
 *
 * Nodos: {type:'text', value} | {type:'el', tag, attrs, raw, children, selfClose}
 * `raw` conserva la etiqueta de apertura literal, así el serializado devuelve
 * exactamente los mismos atributos, comillas y espacios que la fuente.
 */
export function parseHtml(html) {
	const root = { type: 'el', tag: '#root', attrs: {}, raw: '', children: [] };
	const stack = [root];
	let i = 0;

	const push = (node) => stack[stack.length - 1].children.push(node);

	while (i < html.length) {
		const lt = html.indexOf('<', i);
		if (lt === -1) {
			if (i < html.length) push({ type: 'text', value: html.slice(i) });
			break;
		}
		if (lt > i) push({ type: 'text', value: html.slice(i, lt) });

		// Comentario
		if (html.startsWith('<!--', lt)) {
			const end = html.indexOf('-->', lt);
			const stop = end === -1 ? html.length : end + 3;
			push({ type: 'text', value: html.slice(lt, stop) });
			i = stop;
			continue;
		}

		// Bloque PHP suelto (fuera de un atributo): opaco, como un comentario.
		// Dentro de un atributo lo absorbe findTagEnd() al buscar el `>`.
		if (html.startsWith('<?', lt)) {
			const end = html.indexOf('?>', lt + 2);
			const stop = end === -1 ? html.length : end + 2;
			push({ type: 'text', value: html.slice(lt, stop) });
			i = stop;
			continue;
		}

		const gt = findTagEnd(html, lt);
		if (gt === -1) {
			push({ type: 'text', value: html.slice(lt) });
			break;
		}
		const rawTag = html.slice(lt, gt + 1);

		// Cierre
		if (rawTag[1] === '/') {
			const name = rawTag.slice(2, -1).trim().toLowerCase();
			for (let s = stack.length - 1; s > 0; s--) {
				if (stack[s].tag === name) {
					stack.length = s;
					break;
				}
			}
			i = gt + 1;
			continue;
		}

		const name = (rawTag.slice(1).match(/^[a-zA-Z0-9-]+/) || [''])[0].toLowerCase();

		// Subárbol opaco: se traga entero como texto.
		if (OPAQUE.has(name) && !rawTag.endsWith('/>')) {
			const close = html.toLowerCase().indexOf(`</${name}>`, gt);
			const stop = close === -1 ? html.length : close + name.length + 3;
			push({ type: 'text', value: html.slice(lt, stop) });
			i = stop;
			continue;
		}

		const node = {
			type: 'el',
			tag: name,
			attrs: parseAttrs(rawTag),
			raw: rawTag,
			children: [],
			selfClose: rawTag.endsWith('/>') || VOID.has(name),
		};
		push(node);
		if (!node.selfClose) stack.push(node);
		i = gt + 1;
	}
	return root;
}

function parseAttrs(rawTag) {
	const attrs = {};
	const body = rawTag.replace(/^<[a-zA-Z0-9-]+/, '').replace(/\/?>$/, '');
	for (const m of body.matchAll(/([a-zA-Z0-9_:-]+)(?:\s*=\s*("([^"]*)"|'([^']*)'|([^\s"'>]+)))?/g)) {
		attrs[m[1].toLowerCase()] = m[3] ?? m[4] ?? m[5] ?? '';
	}
	return attrs;
}

/** Serializa el árbol de vuelta a HTML, byte por byte igual a la fuente. */
export function serialize(node) {
	if (node.type === 'text') return node.value;
	const inner = node.children.map(serialize).join('');
	if (node.tag === '#root') return inner;
	if (node.selfClose) return node.raw;
	return node.raw + inner + `</${node.tag}>`;
}

function classesOf(node) {
	return (node.attrs?.class || '').trim().split(/\s+/).filter(Boolean);
}

/* ==========================================================================
   Segmentos traducibles
   ========================================================================== */

/**
 * Elementos INLINE: nunca son un segmento por sí mismos, siempre forman parte
 * del innerHTML del bloque que los contiene.
 *
 * Es la distinción que hace que la traducción funcione a nivel de frase: en
 * `<p>Texto con <b>énfasis</b> adentro</p>` el <b> no se traduce suelto (daría
 * un inglés con el orden de palabras del español), se traduce el <p> entero
 * conservando el <b> en su lugar.
 */
const INLINE = new Set(['a', 'b', 'i', 'em', 'strong', 'span', 'small', 'sup', 'sub', 'abbr', 'u', 's', 'mark', 'br', 'wbr', 'svg', 'code', 'kbd', 'samp', 'var', 'time', 'label']);

/**
 * Clases que marcan contenido técnico: el elemento entero se saltea aunque
 * tenga texto (una `<td class="tok">--re-canvas</td>` es un token, no una
 * celda de texto).
 */
const SKIP_CLASSES = new Set(['mono', 'tok', 'code']);

/** ¿Tiene texto propio (no sólo espacios) contando el de sus hijos inline? */
function hasOwnText(node) {
	for (const child of node.children || []) {
		if (child.type === 'text' && child.value.trim() && !child.value.trim().startsWith('<!--')) return true;
		if (child.type === 'el' && INLINE.has(child.tag) && hasOwnText(child)) return true;
	}
	return false;
}

/**
 * ¿Este elemento delimita un segmento traducible?
 *
 * Un segmento es un elemento de BLOQUE que contiene texto propio. No hay lista
 * blanca de clases: el documento usa `div`/`span` con estilo inline en las
 * maquetas de especímenes, y una lista blanca dejaba ese texto sin traducir.
 */
function isSegment(node) {
	if (node.type !== 'el') return false;
	if (INLINE.has(node.tag)) return false;
	if (classesOf(node).some((c) => SKIP_CLASSES.has(c))) return false;
	return hasOwnText(node);
}

/**
 * ¿Tiene algún descendiente de BLOQUE que sea a su vez un segmento? Si lo
 * tiene, este elemento no es hoja y hay que bajar en vez de traducirlo entero.
 */
function hasSegmentDescendant(node) {
	for (const child of node.children || []) {
		if (child.type !== 'el' || INLINE.has(child.tag)) continue;
		if (isSegment(child) || hasSegmentDescendant(child)) return true;
	}
	return false;
}

/** Normaliza espacios para que la clave del diccionario sea estable. */
export function normalize(html) {
	return html.replace(/\s+/g, ' ').trim();
}

/**
 * ¿Vale la pena traducir esto? Descarta lo que no tiene texto real: números,
 * separadores, y todo lo que quede vacío al sacarle los tramos técnicos
 * (`.mono`, `<code>`, `.tok`).
 */
export function isTranslatable(innerHtml) {
	const withoutTechnical = innerHtml
		.replace(/<span[^>]*class="[^"]*\b(?:mono|tok)\b[^"]*"[^>]*>[\s\S]*?<\/span>/g, ' ')
		.replace(/<code[\s\S]*?<\/code>/g, ' ')
		.replace(/<svg[\s\S]*?<\/svg>/g, ' ')
		.replace(/<[^>]+>/g, ' ')
		.replace(/&[a-z]+;|&#\d+;/gi, ' ')
		.replace(/\s+/g, ' ')
		.trim();

	// Al menos dos letras seguidas: descarta "147", "5 + 3", "·", "—".
	if (!/[a-zA-ZáéíóúñÁÉÍÓÚÑüÜ]{2,}/.test(withoutTechnical)) return false;

	// Identificadores que quedaron sin envolver en .mono/.tok en la fuente:
	// custom properties, nombres de archivo y rutas de carpeta.
	const IDENTIFIER = /^(?:--[a-z0-9-]+|[A-Za-z0-9_.\/-]+\.(?:css|html|js|mjs|json|md|jsx|tsx?|png|webp|svg)|[a-z][a-z0-9_-]*\/[A-Za-z0-9_./-]*)$/;
	return !IDENTIFIER.test(withoutTechnical);
}

/**
 * Recorre el árbol y devuelve los segmentos hoja, en orden de documento.
 *
 * @param {object} root Árbol de parseHtml().
 * @returns {Array<{node:object, key:string}>}
 */
export function collectSegments(root) {
	const out = [];
	const walk = (node) => {
		/*
		 * ¿Este contenedor tiene texto suelto propio? Decide qué hacer con sus
		 * hijos INLINE:
		 *  - con texto suelto (un <p> con un <a> en medio de la frase) el inline
		 *    es parte de la frase y se traduce junto con el bloque;
		 *  - sin texto suelto (el <nav class="rail"> con sus <a class="nv">) el
		 *    inline es un ítem independiente y necesita su propia entrada, si no
		 *    quedaría sin traducir.
		 */
		const looseText = (node.children || []).some((c) => c.type === 'text' && c.value.trim());

		for (const child of node.children || []) {
			if (child.type !== 'el') continue;
			if (INLINE.has(child.tag) && !looseText && !('data-i18n-skip' in (child.attrs || {}))) {
				const inner = child.children.map(serialize).join('');
				const key = normalize(inner);
				if (key && isTranslatable(inner) && !hasSegmentDescendant(child)) {
					out.push({ node: child, key });
				}
				continue;
			}
			/*
			 * Bloques que escribe el propio build (tarjetas de pantalla,
			 * inventario de artefactos, System Evolution): ya vienen en el
			 * idioma destino, así que no se buscan en el diccionario — si no,
			 * el inglés se reportaría como "sin traducir" contra claves que
			 * están en español.
			 */
			if ('data-i18n-skip' in (child.attrs || {})) continue;
			if (isSegment(child) && !hasSegmentDescendant(child)) {
				const inner = child.children.map(serialize).join('');
				const key = normalize(inner);
				if (key && isTranslatable(inner)) out.push({ node: child, key });
				continue; // hoja: no se sigue bajando
			}
			walk(child);
		}
	};
	walk(root);
	return out;
}

/**
 * Aplica el diccionario al documento y devuelve el HTML del idioma pedido.
 *
 * @param {string} docHtml HTML del documento (post-transformaciones estructurales).
 * @param {object} dict    Diccionario { claveES: { es?:string, en:string } }.
 * @param {string} lang    'es' | 'en'.
 * @returns {{html:string, missing:string[], total:number, changed:number}}
 */
export function applyI18n(docHtml, dict, lang) {
	const root = parseHtml(docHtml);
	const segments = collectSegments(root);
	const missing = [];
	let changed = 0;

	for (const { node, key } of segments) {
		const entry = dict[key];
		if (!entry) {
			// Sólo el inglés exige cobertura total: en español, un segmento sin
			// entrada es simplemente texto que ya está bien como está.
			if (lang === 'en') missing.push(key);
			continue;
		}
		const replacement = lang === 'en' ? entry.en : entry.es;
		if (typeof replacement !== 'string' || replacement === '') {
			if (lang === 'en') missing.push(key);
			continue;
		}
		node.children = [{ type: 'text', value: replacement }];
		changed++;
	}

	// data-i18n-skip es andamiaje del build: no llega al HTML publicado.
	const html = serialize(root).replace(/\s*data-i18n-skip(?:="[^"]*")?/g, '');

	return { html, missing, total: segments.length, changed };
}
