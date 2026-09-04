#!/usr/bin/env node
/**
 * ESTAVILLO — build del REstimator Design System portfolio-ready
 * ---------------------------------------------------------------------------
 * Toma la fuente VENDORIZADA en docs/ds-src/restimator/ (el HTML limpio del
 * proyecto original, NO el standalone self-extracting de 1,8 MB) y genera los
 * artefactos que consume el child theme:
 *
 *   css    docs/ds-src/restimator/{styles.css,tokens/*,docs/ds-master.css}
 *          -> estavillo-child/assets/css/ds-restimator/{tokens.css,doc.css}
 *          Todos los selectores quedan scopeados bajo `.re-doc`, así el CSS
 *          del DS no puede tocar nada del portfolio (el original trae `html`,
 *          `body`, `*`, `a`, `svg` y clases genéricas como .hero/.body/.main).
 *
 *   html   docs/ds-src/restimator/master-documentation.html
 *          -> estavillo-child/ds/restimator/master-es.php
 *          Extrae el cuerpo del documento, lo envuelve en `.re-doc`, y
 *          reemplaza TODA navegación a archivos .html locales: las pantallas
 *          pasan a previews estáticas con el lightbox del portfolio
 *          ([data-es-zoom-trigger]) y los documentos internos a inventario
 *          de texto plano con badge.
 *
 *   shots  Captura las pantallas documentadas con Chromium y las escribe como
 *          WebP en estavillo-child/assets/ds/restimator/screens/.
 *
 * Uso:  node tools/build-ds.mjs [css|html|shots|all]     (default: all)
 *
 * Por qué existe este script en vez de haber editado los archivos a mano: una
 * actualización futura del Design System se resuelve reemplazando la fuente en
 * docs/ds-src/restimator/ y volviendo a correrlo — no rehaciendo la
 * integración. Ver docs/DS-RESTIMATOR.md.
 */

import { readFileSync, writeFileSync, mkdirSync, existsSync, readdirSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { applyI18n, parseHtml, collectSegments } from './ds-i18n.mjs';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const SRC = join(ROOT, 'docs/ds-src/restimator');
const THEME = join(ROOT, 'estavillo-child');
const CSS_OUT = join(THEME, 'assets/css/ds-restimator');
const PHP_OUT = join(THEME, 'ds/restimator');
const SHOT_OUT = join(THEME, 'assets/ds/restimator/screens');
/* Dimensiones de cada captura. Vive en docs/ (fuera del ZIP): es metadata de
   build que buildHtml() necesita para emitir width/height reales, no un asset
   que el sitio sirva. Un solo archivo en vez de un .json por pantalla. */
const SHOT_META = join(SRC, 'screens-meta.json');

/** Raíz de scope: toda regla del DS cuelga de acá. */
const SCOPE = '.re-doc';

const log = (...a) => console.log('[build-ds]', ...a);
const ensure = (d) => { if (!existsSync(d)) mkdirSync(d, { recursive: true }); };

/* ==========================================================================
   1. CSS — scoping
   ========================================================================== */

/**
 * Parte una lista de selectores por comas de nivel superior (respeta
 * paréntesis de :is()/:where()/:has(), que el DS no usa hoy pero podría
 * traer en una actualización).
 */
function splitSelectors(list) {
	const out = [];
	let depth = 0, buf = '';
	for (const ch of list) {
		if (ch === '(') depth++;
		else if (ch === ')') depth--;
		if (ch === ',' && depth === 0) { out.push(buf.trim()); buf = ''; continue; }
		buf += ch;
	}
	if (buf.trim()) out.push(buf.trim());
	return out;
}

/**
 * Scopea UN selector bajo `.re-doc`.
 *
 * Casos especiales, en orden:
 *  - `:root` / `[data-theme=…]`: sólo declaran custom properties `--re-*`.
 *    `:root` se deja intacto (no colisiona: prefijo propio, y así los tokens
 *    siguen disponibles para el lightbox compartido). `[data-theme]` se acota
 *    al scope para que un plugin ajeno que ponga data-theme en <html> no
 *    pueda voltear la paleta del DS.
 *  - `html`: el contenedor de scroll real. Se resuelve con :has() contra la
 *    body class del template (mismo criterio ya usado en pages-home.css).
 *  - `body` / `body.re-root`: el wrapper del documento pasa a ser `.re-doc`.
 *  - `*`: se expande a la raíz + descendientes.
 */
function scopeSelector(sel) {
	const s = sel.trim();
	if (!s) return s;

	if (s === ':root') return ':root';
	if (s.startsWith('[data-theme')) return `${SCOPE}${s}, ${SCOPE} ${s}`;

	if (s === 'html') return 'html:has(> body.es-ds-page)';
	if (s.startsWith('html ')) return `html:has(> body.es-ds-page) ${s.slice(5)}`;

	if (s === 'body' || s === 'body.re-root' || s === '.re-root') return SCOPE;
	if (s.startsWith('body.re-root ')) return `${SCOPE} ${s.slice(13)}`;
	if (s.startsWith('body ')) return `${SCOPE} ${s.slice(5)}`;
	/*
	 * `.re-root <algo>` — p. ej. `.re-root ::selection` de tokens/base.css.
	 * Sin este caso caía en el catch-all y salía `.re-doc .re-root ::selection`,
	 * que pide que .re-root sea DESCENDIENTE de .re-doc; en el DOM real las dos
	 * clases van en el MISMO elemento (<main class="re-doc re-root">), así que
	 * esa regla no matcheaba nunca y el ::selection del Design System quedaba
	 * muerto — dejando ganar al ::selection global del portfolio.
	 */
	if (s.startsWith('.re-root ')) return `${SCOPE} ${s.slice(9)}`;

	// `*`, `*::before`, `*::after`: la raíz también tiene que recibirlos.
	const star = s.match(/^\*(::?[a-z-]+)?$/);
	if (star) {
		const pseudo = star[1] || '';
		return `${SCOPE}${pseudo}, ${SCOPE} *${pseudo}`;
	}

	return `${SCOPE} ${s}`;
}

/**
 * Recorre el CSS a nivel de bloque y scopea cada lista de selectores.
 * El CSS del DS no usa nesting nativo, sólo @media/@supports, así que alcanza
 * con recursión sobre las at-rules condicionales.
 */
function scopeCss(css) {
	let out = '';
	let i = 0;

	while (i < css.length) {
		// Espacios y comentarios ANTES de una regla se copian tal cual y no
		// entran al prelude. Sin este paso, un comentario en la línea previa
		// al selector queda dentro del prelude y se scopea junto con él
		// (producía `.re-doc /* … */ .shots{…}`, que deja la regla sin scope).
		const ws = css.slice(i).match(/^(?:\s+|\/\*[\s\S]*?\*\/)+/);
		if (ws) {
			out += ws[0];
			i += ws[0].length;
			if (i >= css.length) break;
		}

		const brace = css.indexOf('{', i);
		if (brace === -1) { out += css.slice(i); break; }

		const prelude = css.slice(i, brace);
		const preludeTrim = prelude.trim();

		// Encontrar el cierre del bloque.
		let depth = 0, j = brace;
		for (; j < css.length; j++) {
			if (css[j] === '{') depth++;
			else if (css[j] === '}') { depth--; if (depth === 0) break; }
		}
		const body = css.slice(brace + 1, j);

		if (preludeTrim.startsWith('@media') || preludeTrim.startsWith('@supports')) {
			out += prelude + '{' + scopeCss(body) + '}';
		} else if (preludeTrim.startsWith('@')) {
			// @font-face, @keyframes, @import…: sin selectores que scopear.
			out += prelude + '{' + body + '}';
		} else {
			const scoped = splitSelectors(preludeTrim).map(scopeSelector).join(',');
			const lead = prelude.slice(0, prelude.length - prelude.trimStart().length);
			out += lead + scoped + '{' + body + '}';
		}
		i = j + 1;
	}
	return out;
}

function buildCss() {
	ensure(CSS_OUT);

	// --- tokens: se concatenan en el orden del manifest styles.css ---------
	const manifest = readFileSync(join(SRC, 'styles.css'), 'utf8');
	const order = [...manifest.matchAll(/@import\s+url\(["']\.\/tokens\/([^"']+)["']\)/g)].map((m) => m[1]);
	if (!order.length) throw new Error('styles.css: no se pudo leer el orden de los tokens');

	let tokens = '';
	let fontImport = null;
	for (const file of order) {
		let css = readFileSync(join(SRC, 'tokens', file), 'utf8');
		// El @import de Google Fonts sale del CSS: en WordPress las fuentes se
		// encolan con wp_enqueue_style() (un @import anidado bloquea el render
		// y no se puede versionar ni desactivar por filtro).
		css = css.replace(/@import\s+url\(['"](https:\/\/fonts\.googleapis\.com[^'"]+)['"]\);?/g, (_, url) => {
			fontImport = url;
			return `/* @import movido a wp_enqueue_style(): ${url} */`;
		});
		tokens += `\n/* ---------- tokens/${file} ---------- */\n${css}\n`;
	}

	const tokensOut =
		`/* GENERADO POR tools/build-ds.mjs — NO EDITAR A MANO.\n` +
		`   Fuente: docs/ds-src/restimator/tokens/ (orden de styles.css).\n` +
		`   Los custom properties --re-* viven en :root y no colisionan con --es-*. */\n` +
		scopeCss(tokens);
	writeFileSync(join(CSS_OUT, 'tokens.css'), tokensOut);
	log(`tokens.css  ${(tokensOut.length / 1024).toFixed(1)} KB  (${order.length} archivos)`);

	// Las fuentes se encolan desde inc/enqueue.php. Si el Design System cambia
	// de familias, el build lo avisa acá en vez de dejar la página cargando en
	// silencio unas fuentes y usando otras.
	if (fontImport) {
		const enqueue = readFileSync(join(THEME, 'inc/enqueue.php'), 'utf8');
		const families = [...fontImport.matchAll(/family=([^&:]+)/g)].map((m) => decodeURIComponent(m[1]));
		const missing = families.filter((f) => !enqueue.includes(f));
		if (missing.length) {
			throw new Error(
				`inc/enqueue.php no encola estas familias del DS: ${missing.join(', ')}\n` +
				`  URL en la fuente: ${fontImport}`
			);
		}
		log(`fuentes verificadas contra inc/enqueue.php: ${families.join(', ')}`);
	}

	// --- documentación: ds-master.css scopeado ----------------------------
	const master = readFileSync(join(SRC, 'docs/ds-master.css'), 'utf8');
	const docOut =
		`/* GENERADO POR tools/build-ds.mjs — NO EDITAR A MANO.\n` +
		`   Fuente: docs/ds-src/restimator/docs/ds-master.css.\n` +
		`   Todos los selectores scopeados bajo ${SCOPE}: el original trae html,\n` +
		`   body, *, a, svg y clases genéricas (.hero .body .main .sub .card-h\n` +
		`   .tag .on .b .k .d .r .v) que romperían el portfolio sin scope.\n` +
		`   Los ajustes propios NO van acá — van en doc-overrides.css. */\n` +
		scopeCss(master);
	writeFileSync(join(CSS_OUT, 'doc.css'), docOut);
	log(`doc.css     ${(docOut.length / 1024).toFixed(1)} KB`);
}

/* ==========================================================================
   2. HTML — cuerpo del documento + reemplazo de navegación local
   ========================================================================== */

/** Pantallas documentadas en §08. `screens` = producto; `view` = vista de auditoría. */
const DESKTOP_SHOTS = [
	{ id: 'calculator',     name: { es: 'Calculadora', en: 'Calculator' },                                file: 'Calculator.html',    kind: 'screen' },
	{ id: 'history',        name: { es: 'Historial', en: 'History' },                                     file: 'History.html',       kind: 'screen' },
	{ id: 'product-editor', name: { es: 'Editor de producto', en: 'Product editor' },                     file: 'ProductEditor.html', kind: 'screen' },
	{ id: 'client-summary', name: { es: 'Resumen cliente', en: 'Client summary' },                        file: 'ClientSummary.html', kind: 'screen' },
	{ id: 'catalogs',       name: { es: 'Catálogos', en: 'Catalogues' },                                  file: 'Catalogs.html',      kind: 'screen' },
	{
		id: 'light-dark',
		name: { es: 'Comparación light / dark', en: 'Light / dark comparison' },
		file: 'Light & Dark Comparison.html',
		// No es una pantalla del producto: es el registro de una exploración. Va
		// fuera de la grilla de Screen Examples y etiquetada como tal, para que
		// el contador "5 desktop" siga siendo cierto y para que nadie la lea
		// como prueba de que existe un tema light disponible.
		kind: 'view',
		tag: { es: 'Exploración', en: 'Exploration' },
		prepare: prepareLightDark,
		tallViewport: true,
	},
];

/**
 * La comparación light/dark es un composite de 10 <iframe> a las pantallas
 * reales. Dos correcciones necesarias para capturarla:
 *
 *  1. Los iframes son loading="lazy": el script de la página les asigna src de
 *     entrada, pero Chromium igual difiere los que están fuera del viewport, y
 *     una captura fullPage no los despierta. Se fuerzan a eager y se espera a
 *     que carguen los diez (más el remapeo a data-theme="light" de la columna
 *     derecha, que corre en el 'load' de cada frame).
 *
 *  2. La fila "01 Dashboard" apunta a ui_kits/presupuestador/index.html, que es
 *     un stub de redirección: el propio kit la marca "Dashboard removed from V1
 *     (scope lock · Decision 1)". Es una fila obsoleta de la fuente. Se excluye
 *     de la captura para no publicar un panel vacío o una redirección; el resto
 *     del documento no se toca. Queda anotado en docs/DS-RESTIMATOR.md.
 */
async function prepareLightDark(page) {
	// Los iframes son file:// desde un padre file://: contentDocument es origen
	// opaco y no sirve para saber si terminaron. Se cuentan eventos 'load'
	// reales, enganchados ANTES de reasignar src.
	const removed = await page.evaluate(() => {
		let dropped = 0;
		window.__loaded = 0;
		window.__expected = 0;
		document.querySelectorAll('iframe[data-src]').forEach((f) => {
			if (/\/index\.html$/.test(f.getAttribute('data-src') || '')) {
				const section = f.closest('section');
				if (section) { section.remove(); dropped++; }
				return;
			}
			window.__expected++;
			f.addEventListener('load', () => { window.__loaded++; }, { once: true });
			f.loading = 'eager';
			f.removeAttribute('loading');
			// Reasignar src fuerza la carga aunque el lazy la haya diferido.
			f.src = f.dataset.src;
		});
		return dropped;
	});
	if (removed === 0) throw new Error('light-dark: no se encontró la fila obsoleta del Dashboard');

	await page.waitForFunction(() => window.__loaded >= window.__expected, null, { timeout: 60000 });
	// El remapeo a data-theme="light" corre en requestAnimationFrame anidado
	// dentro del handler 'load' de cada frame de la columna derecha.
	await page.waitForTimeout(2500);
}

/**
 * Agranda el viewport hasta cubrir el documento entero y captura sin fullPage.
 *
 * Chromium no rasteriza los iframes que quedan fuera del viewport, y una
 * captura `fullPage` NO los despierta (tampoco alcanza con recorrer la página
 * scrolleando: se vuelven a vaciar al salir de pantalla). Con todo el
 * documento dentro del viewport no hay nada offscreen que pueda quedar en
 * blanco. Sólo se usa donde hace falta —la comparación light/dark, que es un
 * composite de iframes—: las pantallas sueltas usan fullPage normal, que
 * respeta su layout sticky.
 */
async function shotTallViewport(page, width) {
	const h = await page.evaluate(() => document.documentElement.scrollHeight);
	await page.setViewportSize({ width, height: Math.min(h, 9000) });
	await page.waitForTimeout(1800);
	return page.screenshot();
}

const MOBILE_SHOTS = [
	{ id: 'mobile-calculator', name: { es: 'Calculadora', en: 'Calculator' }, note: { es: '390 · por defecto — formulario + resumen fijo', en: '390 · default — form + pinned summary' } },
	{ id: 'mobile-home',       name: { es: 'Inicio',      en: 'Home' },       note: { es: '390 · por defecto', en: '390 · default' } },
	{ id: 'mobile-history',    name: { es: 'Historial',   en: 'History' },    note: { es: '390 · por defecto', en: '390 · default' } },
];

/**
 * Inventario de artefactos del proyecto (ex-tabla "Otras piezas"), sin links.
 * Los nombres son los de los archivos reales del proyecto: no se traducen.
 */
const ARTIFACTS = [
	{
		name: 'Refinamiento HiFi — Auditoría y Decisiones',
		kind: { es: 'Auditoría', en: 'Audit' },
		what: {
			es: 'Auditoría wireframe vs HiFi, problemas UX/visuales, comparación A/B/C/D del panel de resultado.',
			en: 'Wireframe vs. hi-fi audit, UX and visual issues, A/B/C/D comparison of the result panel.',
		},
	},
	{
		name: 'Typography Comparison',
		kind: { es: 'Auditoría', en: 'Audit' },
		what: {
			es: 'Hanken vs Plus Jakarta contra pantallas reales — decisión de congelar Hanken.',
			en: 'Hanken vs. Plus Jakarta against real screens — the decision to freeze Hanken.',
		},
	},
	{
		name: 'V1 Design Freeze · Readiness Audit',
		kind: { es: 'Freeze', en: 'Freeze' },
		what: {
			es: 'Cierre de alcance V1 y verificación de que el sistema estaba listo para congelarse.',
			en: 'V1 scope lock, and the check that the system was ready to be frozen.',
		},
	},
	{
		name: 'Accessibility Review · UX Review',
		kind: { es: 'Review', en: 'Review' },
		what: {
			es: 'Resultados de las revisiones de accesibilidad y de UX sobre el kit congelado.',
			en: 'Results of the accessibility and UX reviews of the frozen kit.',
		},
	},
	{
		name: 'Claude Code Handoff Package',
		kind: { es: 'Handoff', en: 'Handoff' },
		what: {
			es: 'Paquete de handoff a desarrollo.',
			en: 'Handoff package for development.',
		},
	},
];

/**
 * "System evolution" — presentación pública del roadmap, en lugar del registro
 * completo de auditoría.
 *
 * Los 13 NR siguen publicándose, pero dentro de un <details> secundario: son
 * notas internas de auditoría y no deberían dominar el cierre del documento ni
 * leerse como una lista de defectos del producto. Acá arriba queda sólo el
 * estado de alto nivel de las tres líneas de trabajo abiertas.
 */
const SYSTEM_EVOLUTION = [
	{ name: { es: 'Tablet', en: 'Tablet' },           state: { es: 'En progreso', en: 'In progress' },   tone: 'progress' },
	{ name: { es: 'Expansión V2', en: 'V2 expansion' }, state: { es: 'En desarrollo', en: 'In development' }, tone: 'progress' },
	{ name: { es: 'Tema light', en: 'Light theme' },  state: { es: 'Planificado', en: 'Planned' },       tone: 'planned' },
];

/**
 * Textos que escribe el propio build (no vienen de la fuente), en los dos
 * idiomas. Todo lo que sale de acá se emite ya traducido y marcado con
 * data-i18n-skip, así el diccionario no lo vuelve a tocar.
 */
const UI = {
	es: {
		shotsLede: 'Las pantallas del producto construidas con el sistema. Las cinco de desktop son las del UI kit; las tres de mobile vienen de la spec de implementación Mobile v1. Cada una está capturada en alta resolución y se puede abrir para recorrerla.',
		shotsNote: 'Capturas de las pantallas reales del kit. Hacé click en cualquiera para abrirla y recorrerla completa.',
		zoomHint: 'Ampliar',
		expandAria: 'Abrir pantalla: %s',
		explorationTitle: 'Exploración de color',
		explorationNote: 'Una prueba de remap de color hecha durante el diseño del sistema, guardada como registro. El sistema publicado tiene un solo tema, dark; esta comparación no es un tema disponible.',
		artifactsTitle: 'Documentación de respaldo',
		artifactsLede: 'Los documentos donde se decidieron las cosas que este sistema da por resueltas: auditorías, el cierre de alcance de la V1 y el paquete de handoff.',
		artifactsHead: ['Artefacto', 'Tipo', 'Qué contiene'],
		artifactsNote: 'Se listan como inventario, sin publicarse. Los nombres son los originales de cada documento.',
		specFull: 'Spec completa:',
		inventoryLede: 'Los 33 componentes construidos y los 6 que quedaron especificados, con lo que cada uno cubre hoy en desktop y en mobile. La columna <b>Estado</b> dice en qué punto del sistema está:',
		inventoryStates: [
			['Estable', 'stable', 'construido y en uso.'],
			['Extendido', 'ext', 'existe, y le falta cobertura para mobile.'],
			['Nuevo', 'new', 'especificado en Mobile v1, todavía sin construir.'],
			['Requiere revisión', 'rev', 'tiene una inconsistencia documentada.'],
		],
		evolutionTitle: 'Evolución del sistema',
		evolutionLede: 'Las líneas de trabajo abiertas del sistema y en qué estado está cada una.',
		auditTitle: 'Notas de auditoría',
		auditSummary: 'Registro interno de auditoría (13 notas)',
		auditIntro: 'Inconsistencias y huecos detectados al auditar el sistema. Quedan escritos para resolverse como decisión explícita en vez de corregirse sobre la marcha. Es material de trabajo interno.',
		railFoot: 'Documentación maestra del sistema.<br>Tema dark.<br>Tipografía congelada 2026‑06‑16.',
		screensFactLabel: 'Pantallas',
		screensDesktop: 'desktop',
		screensMobile: 'mobile',
		screensFactNote: 'del UI kit y de la spec Mobile v1',
		themeFactLabel: 'Tema',
		themeFactValue: 'Dark',
		themeFactNote: 'el único tema del sistema',
	},
	en: {
		shotsLede: 'The product screens built with the system. The five desktop ones come from the UI kit; the three mobile ones come from the Mobile v1 implementation spec. Each is captured at high resolution and can be opened to scroll through.',
		shotsNote: 'Captures of the kit’s real screens. Click any of them to open it and scroll through the full screen.',
		zoomHint: 'Expand',
		expandAria: 'Open screen: %s',
		explorationTitle: 'Colour exploration',
		explorationNote: 'A colour remap tried while the system was being designed, kept as a record. The published system has one theme, dark; this comparison is not an available theme.',
		artifactsTitle: 'Supporting documentation',
		artifactsLede: 'The documents where the things this system takes as settled were decided: audits, the V1 scope lock and the handoff package.',
		artifactsHead: ['Artifact', 'Type', 'What it contains'],
		artifactsNote: 'Listed as an inventory, not published. Names are each document’s original title, in the language it was written in.',
		specFull: 'Full spec:',
		inventoryLede: 'The 33 components that were built and the 6 that were specified, with what each one covers today on desktop and on mobile. The <b>Status</b> column says where in the system it stands:',
		inventoryStates: [
			['Stable', 'stable', 'built and in use.'],
			['Extended', 'ext', 'exists, and still needs mobile coverage.'],
			['New', 'new', 'specified in Mobile v1, not built yet.'],
			['Needs review', 'rev', 'has a documented inconsistency.'],
		],
		evolutionTitle: 'System evolution',
		evolutionLede: 'The system’s open workstreams and the state each one is in.',
		auditTitle: 'Audit notes',
		auditSummary: 'Internal audit log (13 notes)',
		auditIntro: 'Inconsistencies and gaps found while auditing the system. They stay written down so each is resolved as an explicit decision rather than patched along the way. This is internal working material.',
		railFoot: 'Master documentation for the system.<br>Dark theme.<br>Typography frozen 2026‑06‑16.',
		screensFactLabel: 'Screens',
		screensDesktop: 'desktop',
		screensMobile: 'mobile',
		screensFactNote: 'from the UI kit and the Mobile v1 spec',
		themeFactLabel: 'Theme',
		themeFactValue: 'Dark',
		themeFactNote: 'the system’s only theme',
	},
};

const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

/** Lee las dimensiones reales de un PNG/WebP generado, para emitir width/height. */
function readSize(id) {
	if (!existsSync(SHOT_META)) return null;
	return JSON.parse(readFileSync(SHOT_META, 'utf8'))[id] || null;
}

/**
 * Emite una tarjeta de pantalla: TODA la tarjeta es el trigger del lightbox
 * compartido del portfolio (assets/js/case-figure-lightbox.js). Sin <a>, sin
 * iframe y sin ninguna ruta a .html local.
 *
 * La cabecera (nombre · archivo · "Ampliar") va DENTRO del <button>, no al
 * lado. Antes era hermana del trigger, y como el visor se dispara por
 * delegación (`e.target.closest('[data-es-zoom-trigger]')`), un click sobre
 * la palabra "Ampliar" no encontraba trigger y no abría nada: se veía como
 * un botón muerto. Con la cabecera adentro hay un único elemento
 * interactivo, nativo, que cubre la tarjeta entera — el teclado sigue
 * funcionando igual porque sigue siendo el mismo <button> de siempre.
 *
 * Es un <div class="cap"> y no un <figcaption>: <figcaption> pertenece al
 * <figure> y no debe anidarse dentro de un control. Las clases son las
 * mismas, así que el CSS del Design System no cambia.
 *
 * El visor lee data-es-zoom-src/-w/-h y el alt del <img> interno.
 */
function shotCard(shot, lang, { mobile = false } = {}) {
	const full = readSize(shot.id) || { w: 0, h: 0, pw: 0, ph: 0, samePreview: false };
	const previewFile = full.samePreview ? `${shot.id}.webp` : `${shot.id}-preview.webp`;
	const cls = mobile ? 'shot shot--mobile' : 'shot';
	const name = typeof shot.name === 'string' ? shot.name : shot.name[lang];
	const meta = mobile ? shot.note[lang] : shot.file;
	const t = UI[lang];

	/*
	 * Ancho de la INTERFAZ, no de la captura. El visor abre la pantalla a su
	 * tamaño real —1440px de diseño para desktop, 390 para mobile— y no a los
	 * 2880/1170 px del archivo, que son el ancho multiplicado por el
	 * deviceScaleFactor con el que se capturó (2× desktop, 3× mobile, ver
	 * buildShots). Sin este dato el visor no puede saber a qué escala es "100%"
	 * y tendría que adivinarlo.
	 */
	const dpr = mobile ? 3 : 2;
	const cssW = full.w ? Math.round(full.w / dpr) : 0;

	return `
    <figure class="${cls}" data-i18n-skip>
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . '${shot.id}.webp' ); ?>"
        data-es-screen-w="${full.w}" data-es-screen-h="${full.h}" data-es-screen-cssw="${cssW}"
        data-es-screen-name="${esc(name)}"
        data-es-screen-meta="${esc(meta)}"
        aria-label="${esc(t.expandAria.replace('%s', name))}">
        <span class="cap">
          <span class="nm">${esc(name)}</span>${shot.tag ? `<span class="art-kind">${esc(shot.tag[lang])}</span>` : ''}<span class="fl">${esc(meta)}</span>
          <span class="r"><span class="zoom-hint">${esc(t.zoomHint)}</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . '${previewFile}' ); ?>"
               alt="${esc(name)} — Presupuestador RE"
               width="${full.pw}" height="${full.ph}" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>`;
}

/**
 * Reemplaza un tramo delimitado por dos anclas, fallando fuerte si el ancla no
 * está. El build NO debe generar a medias: si la fuente cambió de estructura,
 * es preferible que se rompa acá y no que publique un documento con una
 * sección vieja mezclada con una nueva.
 */
function spliceBetween(doc, startAnchor, endAnchor, replacement, label) {
	const a = doc.indexOf(startAnchor);
	if (a === -1) throw new Error(`${label}: no se encontró el ancla inicial`);
	const b = doc.indexOf(endAnchor, a + startAnchor.length);
	if (b === -1) throw new Error(`${label}: no se encontró el ancla final`);
	return doc.slice(0, a) + replacement + doc.slice(b + endAnchor.length);
}

function mustReplace(doc, pattern, replacement, label) {
	if (!pattern.test(doc)) throw new Error(`${label}: el patrón no matcheó`);
	return doc.replace(pattern, replacement);
}

/**
 * Índice del carácter siguiente al `</div>` que cierra el `<div>` que empieza en
 * `start`, contando anidamiento.
 *
 * Antes el tramo del tile de mobile se cerraba buscando el primer
 * `'</div></div>'`, que en `…<div class="ov"></div></div></div>` matchea los
 * cierres de `.ov` y `.vp` y deja SIN consumir el de `.shot`. Ese `</div>`
 * huérfano quedaba en el documento y terminaba cerrando `.pad`: "Otras piezas",
 * §09 y §10 se publicaban fuera del contenedor y perdían el padding lateral.
 * Contar el anidamiento no depende de cuántos niveles tenga el tile por dentro.
 */
function endOfDiv(doc, start, label) {
	const re = /<\/?div\b/g;
	re.lastIndex = start;
	let depth = 0;
	let m;
	while ((m = re.exec(doc)) !== null) {
		depth += m[0][1] === '/' ? -1 : 1;
		if (depth === 0) {
			const gt = doc.indexOf('>', m.index);
			if (gt === -1) break;
			return gt + 1;
		}
	}
	throw new Error(`${label}: el <div> no cierra`);
}

/**
 * Cierre público de la documentación: el estado de las líneas de trabajo
 * abiertas, y el registro de auditoría completo detrás de un <details>.
 */
function systemEvolutionBlock(lang) {
	const t = UI[lang];
	const items = SYSTEM_EVOLUTION.map((i) => `
      <div class="ev">
        <span class="ev-name">${esc(i.name[lang])}</span>
        <span class="ev-state ev-state--${i.tone}">${esc(i.state[lang])}</span>
      </div>`).join('');

	return `<h3 class="sub" id="needs-review" data-i18n-skip><span class="tick"></span>${esc(t.evolutionTitle)}</h3>
  <p class="lede" data-i18n-skip>${esc(t.evolutionLede)}</p>
  <div class="ev-grid" data-i18n-skip>${items}
  </div>`;
}

/**
 * Todas las transformaciones estructurales, aplicadas sobre la fuente ES
 * pristina y emitiendo ya en el idioma destino.
 */
function transformDoc(doc, lang) {
	const t = UI[lang];
	const skip = ' data-i18n-skip';

	/*
	 * --- §08 Screen Examples: desktop --------------------------------------
	 *
	 * Sólo las pantallas del producto entran en la grilla. La comparación
	 * light/dark es kind:'view' —el registro de una exploración de color, no una
	 * pantalla— y sale abajo, en su propio bloque etiquetado. Antes iba mezclada
	 * en la grilla, así que la sección mostraba 6 tarjetas bajo un contador que
	 * dice "5 desktop", y la exploración se leía como una pantalla más.
	 */
	const productShots = DESKTOP_SHOTS.filter((s) => s.kind === 'screen');
	if (productShots.length !== 5) throw new Error(`§08: esperaba 5 pantallas de producto, hay ${productShots.length}`);
	doc = spliceBetween(
		doc,
		'<div class="shots">',
		'</div>\n  <p class="body"',
		'<div class="shots">' + productShots.map((s) => shotCard(s, lang)).join('') + '\n  </div>\n  <p class="body"',
		'§08 grilla .shots'
	);

	// Lede de la sección: describía los embeds vivos, que ya no existen.
	doc = mustReplace(
		doc,
		/<p class="lede">Pantallas reales construidas con el sistema\.[\s\S]*?<\/p>/,
		`<p class="lede"${skip}>${t.shotsLede}</p>`,
		'§08 lede'
	);

	// Nota bajo la grilla: describía los embeds interactivos y "Abrir ↗".
	doc = mustReplace(
		doc,
		/<p class="body" style="margin-top:var\(--re-s4\);font-size:12\.5px;color:var\(--re-ink-4\)">Los embeds[\s\S]*?<\/p>/,
		`<p class="body" style="margin-top:var(--re-s4);font-size:12.5px;color:var(--re-ink-4)"${skip}>${t.shotsNote}</p>`,
		'§08 nota de embeds'
	);

	// --- §08 Screen Examples: mobile --------------------------------------
	// El original embebía la spec entera en un solo tile ilegible; acá pasan a
	// ser las TRES pantallas que la propia spec documenta (01 Calculadora,
	// 02 Inicio, 03 Historial), que es lo que anuncia el contador "3 mobile".
	const mobStart = doc.indexOf('<div class="shot"><div class="cap"><span class="nm">Mobile v1');
	if (mobStart === -1) throw new Error('§08 mobile: no se encontró el tile');
	const mobEnd = endOfDiv(doc, mobStart, '§08 mobile');
	const exploration = DESKTOP_SHOTS.filter((s) => s.kind === 'view');
	doc = doc.slice(0, mobStart)
		+ '<div class="shots shots--mobile">' + MOBILE_SHOTS.map((s) => shotCard(s, lang, { mobile: true })).join('') + '\n  </div>'
		+ `\n\n  <h3 class="sub"${skip}><span class="tick"></span>${esc(t.explorationTitle)}</h3>`
		+ `\n  <p class="body" style="max-width:78ch"${skip}>${esc(t.explorationNote)}</p>`
		+ '\n  <div class="shots shots--single">' + exploration.map((s) => shotCard(s, lang)).join('') + '\n  </div>'
		+ doc.slice(mobEnd);

	/*
	 * --- §08 "Otras piezas" -> documentación de respaldo -------------------
	 *
	 * Era una tabla de enlaces a los .html del proyecto y se leía como un
	 * volcado de archivos internos. Sigue publicándose entera —es lo que
	 * respalda las decisiones del sistema— pero con un título que dice qué es,
	 * una línea que la enmarca antes de leerla, y sin enlaces.
	 *
	 * Los nombres NO se traducen: son los nombres propios de documentos que
	 * existen, en el idioma en que se escribieron. La nota lo dice, para que en
	 * inglés un nombre en español se lea como lo que es y no como un descuido.
	 */
	const rows = ARTIFACTS.map((a) =>
		`      <tr><td><span class="art-name">${esc(a.name)}</span></td><td><span class="art-kind">${esc(a.kind[lang])}</span></td><td>${esc(a.what[lang])}</td></tr>`
	).join('\n');
	doc = mustReplace(
		doc,
		/<h3 class="sub"><span class="tick"><\/span>Otras piezas del proyecto<\/h3>/,
		`<h3 class="sub"${skip}><span class="tick"></span>${esc(t.artifactsTitle)}</h3>\n  <p class="lede"${skip}>${esc(t.artifactsLede)}</p>`,
		'§08 título de otras piezas'
	);
	doc = spliceBetween(
		doc,
		'<div class="scrollx"><table class="tb">\n    <thead><tr><th>Archivo</th>',
		'</table></div>',
		`<div class="scrollx"${skip}><table class="tb">\n    <thead><tr>${t.artifactsHead.map((h) => `<th>${esc(h)}</th>`).join('')}</tr></thead>\n    <tbody>\n${rows}\n    </tbody></table></div>\n  <p class="body" style="margin-top:var(--re-s4);font-size:12.5px;color:var(--re-ink-4)"${skip}>${esc(t.artifactsNote)}</p>`,
		'§08 tabla de otras piezas'
	);

	/*
	 * --- §09 Component Inventory: encabezado ------------------------------
	 *
	 * El lede era una sola línea con los cuatro estados encadenados por "·":
	 * legible como referencia, pero leído de corrido sonaba a salida de un
	 * script. Misma información exacta, presentada como lo que es —la leyenda de
	 * la columna Estado— en una frase por estado.
	 */
	doc = spliceBetween(
		doc,
		'<p class="lede">Inventario completo.',
		'</p>',
		`<p class="lede"${skip}>${t.inventoryLede}</p>\n  <dl class="ivt-legend"${skip}>${t.inventoryStates
			.map(([tag, cls, meaning]) => `<div><dt><span class="tag ${cls}">${esc(tag)}</span></dt><dd>${esc(meaning)}</dd></div>`)
			.join('')}</dl>`,
		'§09 lede del inventario'
	);

	// --- §07: link suelto a la spec mobile --------------------------------
	doc = mustReplace(
		doc,
		/Spec completa: <a href="[^"]*\.html">([^<]*)<\/a>/,
		'Spec completa: <span class="art-name">$1</span>',
		'§07 link a la spec mobile'
	);

	/*
	 * Dos celdas `.mono` mezclan un token con una palabra en español. El
	 * diccionario nunca toca `.mono` —ahí viven tokens e identificadores, y
	 * traducirlos los rompería—, así que estas dos se resuelven acá. Al no ser
	 * segmentos traducibles, no afectan la paridad de claves entre idiomas.
	 */
	if (lang === 'en') {
		doc = mustReplace(doc, /<td class="mono">≥ 1200 · diseño 1440<\/td>/, '<td class="mono">≥ 1200 · design 1440</td>', 'celda mono: diseño 1440');
		doc = mustReplace(doc, /<td class="mono">--re-ring 3px ámbar<\/td>/, '<td class="mono">--re-ring 3px amber</td>', 'celda mono: 3px ámbar');
		doc = mustReplace(doc, /<td class="mono">máx 78%<\/td>/, '<td class="mono">max 78%</td>', 'celda mono: máx 78%');
	}

	/*
	 * --- Hero: las cuatro métricas -----------------------------------------
	 *
	 * "5 + 3" no se entiende sin leer el pie de la métrica: un visitante no
	 * tiene por qué saber que el primer número son pantallas de desktop y el
	 * segundo de mobile. El valor pasa a decirlo solo, con los números todavía
	 * como el elemento dominante (.n los mantiene grandes y en ámbar) y las
	 * unidades en texto chico. No cambia ningún dato: siguen siendo 5 y 3.
	 */
	doc = mustReplace(
		doc,
		/<div><div class="k">Pantallas<\/div><div class="v num">5 \+ 3<\/div><div class="d">desktop kit \+ mobile v1<\/div><\/div>/,
		`<div${skip}><div class="k">${esc(t.screensFactLabel)}</div>` +
		`<div class="v v--pair"><span class="n num">5</span> ${esc(t.screensDesktop)} <span class="n num">3</span> ${esc(t.screensMobile)}</div>` +
		`<div class="d">${esc(t.screensFactNote)}</div></div>`,
		'hero: fact de pantallas'
	);

	// --- Tema: la publicación no afirma que existan dos temas -------------
	// Sólo existe dark. Light es trabajo planificado y se anuncia UNA sola vez,
	// en System evolution — no acá.
	doc = mustReplace(
		doc,
		/<div><div class="k">Temas<\/div><div class="v num">2<\/div><div class="d">dark \(default\) · light<\/div><\/div>/,
		`<div${skip}><div class="k">${esc(t.themeFactLabel)}</div><div class="v">${esc(t.themeFactValue)}</div><div class="d">${esc(t.themeFactNote)}</div></div>`,
		'hero: fact de temas'
	);

	doc = mustReplace(
		doc,
		/<li>147 tokens · 2 temas \(dark default, light\)\.<\/li>/,
		`<li${skip}>${lang === 'es' ? '147 tokens · tema dark.' : '147 tokens · dark theme.'}</li>`,
		'§00: "2 temas"'
	);

	doc = mustReplace(
		doc,
		/<div class="rail-foot">Fuente de verdad[\s\S]*?<\/div>/,
		`<div class="rail-foot"${skip}>${t.railFoot}</div>`,
		'rail-foot: claim de light theme'
	);

	// §01: la subsección describía el tema light en presente, como si estuviera
	// en uso. Pasa a clave de trabajo planificado, y se quita el caveat de
	// implementación (era guía para algo que no está publicado).
	doc = spliceBetween(
		doc,
		'<h4 class="mini">Tema light — remap de color puro</h4>',
		'y un flip de variables puede dejarlos a mitad de transición.</p></div>',
		lang === 'es'
			? `<h4 class="mini"${skip}>Tema light — planificado</h4>\n  <p class="body"${skip}><span class="art-kind">Planificado</span> El sistema publicado tiene <b>un solo tema: dark</b>. Existe un remap de color explorado —superficies blanco cálido, tinta grafito, bordes gris cálido— que dejaría intactos espaciado, tipografía, radios, jerarquía e interacción, con el ámbar como único acento. Todavía no forma parte del sistema y no se documenta como disponible.</p>`
			: `<h4 class="mini"${skip}>Light theme — planned</h4>\n  <p class="body"${skip}><span class="art-kind">Planned</span> The published system has <b>a single theme: dark</b>. A colour remap has been explored —warm white surfaces, graphite ink, warm grey borders— which would leave spacing, typography, radii, hierarchy and interaction untouched, with amber as the only accent. It is not part of the system yet and is not documented as available.</p>`,
		'§01 subsección de tema light'
	);

	// §10: el paso "2 · Tema" mostraba cómo activar light como si existiera.
	doc = spliceBetween(
		doc,
		'<h4 class="mini">2 · Tema</h4>',
		'<h4 class="mini">3 · Componentes</h4>',
		lang === 'es'
			? `<h4 class="mini"${skip}>2 · Tema</h4>\n      <p class="body" style="margin:0 0 var(--re-s4)"${skip}>No hay nada que configurar: dark es el único tema del sistema, y es el que aplica <span class="mono">:root</span> por defecto.</p>\n      <h4 class="mini">3 · Componentes</h4>`
			: `<h4 class="mini"${skip}>2 · Theme</h4>\n      <p class="body" style="margin:0 0 var(--re-s4)"${skip}>Nothing to configure: dark is the system’s only theme, and it is what <span class="mono">:root</span> applies by default.</p>\n      <h4 class="mini">3 · Componentes</h4>`,
		'§10 paso de tema'
	);

	/*
	 * --- Needs review -> System evolution + disclosure secundario ----------
	 *
	 * Los 13 NR no se borran ni se reescriben: se mueven tal cual adentro de un
	 * <details>, y el cierre del documento pasa a ser el estado de las líneas
	 * de trabajo abiertas. Son notas internas de auditoría; presentarlas como
	 * el cierre del documento las hacía leer como una lista de defectos.
	 */
	const nrStart = doc.indexOf('<h3 class="sub" id="needs-review">');
	if (nrStart === -1) throw new Error('Needs review: no se encontró el encabezado');
	const STACK_OPEN = '<div class="stack">';
	const STACK_CLOSE = '</div>\n</section>';
	const stackStart = doc.indexOf(STACK_OPEN, nrStart);
	const stackEnd = doc.indexOf(STACK_CLOSE, stackStart);
	if (stackStart === -1 || stackEnd === -1) throw new Error('Needs review: no se encontró el .stack de notas');
	const nrItems = doc.slice(stackStart + STACK_OPEN.length, stackEnd);
	if (!/NR‑13/.test(nrItems)) throw new Error('Needs review: el .stack capturado no contiene las 13 notas');

	doc = doc.slice(0, nrStart)
		+ systemEvolutionBlock(lang)
		+ `\n\n  <details class="ev-audit">`
		+ `\n    <summary${skip}>${esc(t.auditSummary)}</summary>`
		+ `\n    <p class="body"${skip}>${esc(t.auditIntro)}</p>`
		+ `\n    <div class="stack">${nrItems}</div>`
		+ `\n  </details>\n</section>`
		+ doc.slice(stackEnd + STACK_CLOSE.length);

	// El pie del documento contaba los 13 items de auditoría como métrica.
	/*
	 * El pie contaba los 13 items de auditoría como si fueran una métrica del
	 * sistema. Se emite SIEMPRE en español y lo traduce el diccionario: el
	 * <footer> es un bloque traducible y, si el texto variara por idioma, su
	 * clave cambiaría y el diccionario dejaría de matchear.
	 */
	doc = mustReplace(
		doc,
		/<span class="mono">147 tokens · 33 componentes · 13 items en Needs review<\/span>/,
		'<span class="mono">147 tokens · 33 componentes</span>',
		'footer del documento'
	);

	return doc;
}

/**
 * Verifica que el documento emitido esté BIEN ANIDADO y que todas las secciones
 * compartan el mismo contenedor.
 *
 * Esto existe porque el defecto que arregló esta pasada era invisible para el
 * resto de las verificaciones: el HTML seguía teniendo todo el contenido, sin
 * refs rotas ni iframes, y el navegador lo renderizaba sin quejarse — pero
 * `.pad` cerraba 20 KB antes de tiempo y §08(final), §09 y §10 se publicaban
 * fuera del contenedor, sin el padding lateral del documento. Un splice mal
 * cerrado o un atributo que confunda al parser vuelven a producir exactamente
 * lo mismo, así que a partir de acá el build se rompe en vez de publicarlo.
 *
 * @param {string} html HTML final del idioma.
 * @param {string} lang Idioma, para el mensaje de error.
 */
function verifyStructure(html, lang) {
	const VOID = new Set(['br', 'hr', 'img', 'input', 'meta', 'link', 'source', 'col', 'area', 'base', 'embed', 'param', 'track', 'wbr']);
	const SELF_CLOSING_SVG = new Set(['path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'ellipse', 'stop', 'use']);

	if (html.includes('</>')) {
		throw new Error(`master-${lang}: hay cierres sin nombre (</>), señal de que el parser no entendió una etiqueta`);
	}

	const stack = [];
	let padDepth = null;
	let padOpen = -1;
	let padClose = -1;

	for (const m of html.matchAll(/<(\/?)([a-zA-Z][a-zA-Z0-9]*)\b([^>]*?)(\/?)>/g)) {
		const [, closing, rawName, attrs, selfClose] = m;
		const name = rawName.toLowerCase();
		if (VOID.has(name) || SELF_CLOSING_SVG.has(name) || selfClose) continue;

		if (closing) {
			if (!stack.length || stack[stack.length - 1].name !== name) {
				const open = stack.length ? stack[stack.length - 1].name : '(nada)';
				throw new Error(`master-${lang}: </${name}> cierra fuera de orden en ${m.index} (abierto: <${open}>)`);
			}
			stack.pop();
			if (padDepth !== null && stack.length < padDepth) {
				padClose = m.index;
				padDepth = null;
			}
		} else {
			stack.push({ name, at: m.index });
			if (padDepth === null && padClose === -1 && /\bclass="pad"/.test(attrs)) {
				padDepth = stack.length;
				padOpen = m.index;
			}
		}
	}

	if (stack.length) {
		throw new Error(`master-${lang}: quedaron etiquetas sin cerrar: ` + stack.map((s) => `<${s.name}>@${s.at}`).join(', '));
	}
	if (padOpen === -1) throw new Error(`master-${lang}: no se encontró el contenedor .pad`);
	if (padClose === -1) throw new Error(`master-${lang}: .pad no cierra`);

	const outside = [];
	for (const m of html.matchAll(/<section class="sec" id="(s\d+)"/g)) {
		if (m.index < padOpen || m.index > padClose) outside.push(m[1]);
	}
	if (outside.length) {
		throw new Error(
			`master-${lang}: ${outside.length} sección(es) quedaron FUERA de .pad y se publicarían sin padding lateral: ` +
			outside.join(', ')
		);
	}
}

function buildHtml() {
	ensure(PHP_OUT);
	const html = readFileSync(join(SRC, 'master-documentation.html'), 'utf8');

	// --- cuerpo del documento --------------------------------------------
	const start = html.indexOf('<div class="doc">');
	const scriptAt = html.indexOf('<script>', start);
	if (start === -1 || scriptAt === -1) throw new Error('master-documentation.html: no se encontró <div class="doc"> / <script>');
	const lastClose = html.lastIndexOf('</div>', scriptAt);
	const source = html.slice(start, lastClose + 6);

	const dict = JSON.parse(readFileSync(join(SRC, 'i18n.json'), 'utf8'));
	const report = [];

	for (const lang of ['es', 'en']) {
		const doc = transformDoc(source, lang);
		if (process.env.DS_DUMP_TRANSFORMED) writeFileSync(join(SRC, `transformed-${lang}.html`), doc);
		const { html: localized, missing, total, changed } = applyI18n(doc, dict, lang);

		if (missing.length) {
			writeFileSync(join(SRC, `missing-${lang}.txt`), missing.join('\n') + '\n');
			const preview = missing.slice(0, 10).map((m) => '    · ' + m.slice(0, 96)).join('\n');
			const msg = `master-${lang}: ${missing.length} de ${total} segmentos sin traducir:\n${preview}` +
				(missing.length > 10 ? `\n    … y ${missing.length - 10} más` : '') +
				`\n  Lista completa en docs/ds-src/restimator/missing-${lang}.txt`;
			if (process.env.DS_ALLOW_MISSING) log('AVISO ' + msg);
			else throw new Error(msg);
		}

		// --- verificación dura --------------------------------------------
		const leftovers = [...localized.matchAll(/(?:href|src)="([^"]*\.html[^"]*)"/g)].map((m) => m[1]);
		if (leftovers.length) throw new Error(`master-${lang}: quedaron refs a .html locales:\n  ` + leftovers.join('\n  '));
		if (/<iframe/i.test(localized)) throw new Error(`master-${lang}: quedaron <iframe>`);
		if (/data-i18n-skip/.test(localized)) throw new Error(`master-${lang}: quedó andamiaje data-i18n-skip en el HTML`);
		verifyStructure(localized, lang);

		const php = `<?php
/**
 * REstimator Design System — cuerpo del documento (${lang.toUpperCase()}).
 *
 * GENERADO POR tools/build-ds.mjs — NO EDITAR A MANO.
 * Fuente:  docs/ds-src/restimator/master-documentation.html
 * Idioma:  docs/ds-src/restimator/i18n.json
 *
 * Los dos idiomas salen de la MISMA fuente estructural: el documento se
 * transforma una vez y después se le aplica el diccionario del idioma, así no
 * hay dos documentos que mantener a mano y una actualización del Design System
 * se propaga a los dos.
 *
 * Diferencias con la fuente, todas producidas por el script:
 *  - §08 Screen Examples: los <iframe> a archivos .html locales y los enlaces
 *    "Abrir ↗" se reemplazan por previews estáticas que abren el visor de
 *    pantallas de esta página ([data-es-screen-trigger], ver
 *    assets/js/ds-screen-viewer.js). Toda la tarjeta es el trigger, incluida
 *    la palabra "Ampliar".
 *  - §08: la comparación light/dark sale de la grilla y pasa a un bloque
 *    propio, etiquetado como exploración.
 *  - §08 "Otras piezas": la tabla de enlaces pasa a inventario de texto plano.
 *  - Tema: la publicación declara un solo tema (dark). Light figura como
 *    planificado, nunca como disponible.
 *  - Cierre: "Needs review" pasa a "System evolution" (estado de las líneas de
 *    trabajo abiertas) con el registro de auditoría completo en un <details>.
 *  - Cero href/src a archivos .html locales (verificado por el script).
 *
 * El resto del markup NO se reordena ni se simplifica: es el mismo documento.
 *
 * @package estavillo-child
 * @var string $es_ds_screens URI base de assets/ds/restimator/screens/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
${localized}
`;
		writeFileSync(join(PHP_OUT, `master-${lang}.php`), php);
		report.push(`master-${lang}.php  ${(php.length / 1024).toFixed(1)} KB · ${changed}/${total} segmentos traducidos`);
	}

	report.forEach((r) => log(r));
	log('0 refs .html · 0 iframes · 0 andamiaje i18n');
}

/* ==========================================================================
   3. SHOTS — captura de pantallas
   ========================================================================== */

const FONTS_CSS = 'https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap';

/** Ancho del preview embebido en la página (el full es el que abre el visor). */
const PREVIEW_W = 760;

/**
 * Playwright sólo hace falta para `shots`. Se resuelve a mano porque los
 * módulos ESM ignoran NODE_PATH, y acá no hay node_modules local: el paquete
 * vive en la instalación global del entorno.
 */
async function loadPlaywright() {
	const candidates = [
		'playwright',
		...(process.env.NODE_PATH || '').split(':').filter(Boolean).map((p) => join(p, 'playwright/index.js')),
		'/opt/node22/lib/node_modules/playwright/index.js',
		'/usr/lib/node_modules/playwright/index.js',
	];
	for (const c of candidates) {
		try {
			// Playwright es CommonJS: al importarlo desde ESM los named exports
			// pueden quedar sólo bajo .default según cómo se resuelva.
			const mod = await import(c.startsWith('/') ? 'file://' + c : c);
			const pw = mod.chromium ? mod : mod.default;
			if (pw && pw.chromium) return pw;
		} catch { /* siguiente candidato */ }
	}
	throw new Error('playwright no encontrado. Instalalo o exportá NODE_PATH al directorio de módulos globales.');
}

async function buildShots() {
	const { chromium } = await loadPlaywright();
	ensure(SHOT_OUT);

	// Las dos familias del DS se bajan una vez y se sirven desde memoria: la
	// captura tiene que salir con la tipografía real, no con el fallback del
	// sistema (si no, el documento en vivo y sus capturas no coincidirían).
	const fontCss = await fetchText(FONTS_CSS);
	const fontFiles = new Map();
	for (const url of [...fontCss.matchAll(/url\((https:\/\/fonts\.gstatic\.com[^)]+)\)/g)].map((m) => m[1])) {
		fontFiles.set(url, await fetchBuffer(url));
	}
	log(`fuentes: ${fontFiles.size} archivos woff2 precargados`);

	const browser = await chromium.launch({ executablePath: chromiumPath() });

	const route = async (ctx) => {
		await ctx.route('**/*', async (r) => {
			const url = r.request().url();
			if (url.startsWith('file:')) return r.continue();
			if (url.startsWith(FONTS_CSS.split('?')[0])) return r.fulfill({ contentType: 'text/css', body: fontCss });
			if (fontFiles.has(url)) return r.fulfill({ contentType: 'font/woff2', body: fontFiles.get(url) });
			return r.abort();
		});
	};

	const encoder = await browser.newPage();
	const results = [];

	// --- desktop ----------------------------------------------------------
	for (const shot of DESKTOP_SHOTS) {
		const ctx = await browser.newContext({ viewport: { width: 1440, height: 980 }, deviceScaleFactor: 2 });
		await route(ctx);
		const page = await ctx.newPage();
		const file = shot.kind === 'view' ? 'light-dark-comparison.html' : `ui_kits/presupuestador/${shot.file}`;
		await page.goto('file://' + join(SRC, file), { waitUntil: 'domcontentloaded' });
		await page.evaluate(() => document.fonts.ready);
		await page.waitForTimeout(1200);
		if (shot.prepare) await shot.prepare(page);
		const png = shot.tallViewport
			? await shotTallViewport(page, 1440)
			: await page.screenshot({ fullPage: true });
		results.push(await emit(encoder, shot.id, png));
		await ctx.close();
	}

	// --- mobile: los tres .screen de la spec ------------------------------
	const ctx = await browser.newContext({ viewport: { width: 1400, height: 1200 }, deviceScaleFactor: 3 });
	await route(ctx);
	const page = await ctx.newPage();
	await page.goto('file://' + join(SRC, 'mobile-v1-spec.html'), { waitUntil: 'domcontentloaded' });
	await page.evaluate(() => document.fonts.ready);
	await page.waitForTimeout(1200);
	const screens = await page.$$('.screen');
	if (screens.length !== MOBILE_SHOTS.length) {
		throw new Error(`mobile: esperaba ${MOBILE_SHOTS.length} .screen, encontré ${screens.length}`);
	}
	for (let i = 0; i < screens.length; i++) {
		const png = await screens[i].screenshot();
		results.push(await emit(encoder, MOBILE_SHOTS[i].id, png));
	}
	await ctx.close();

	await browser.close();

	// Dimensiones para que buildHtml() emita width/height reales (sin CLS).
	const meta = {};
	for (const r of results) meta[r.id] = { w: r.w, h: r.h, pw: r.pw, ph: r.ph, samePreview: r.samePreview };
	writeFileSync(SHOT_META, JSON.stringify(meta, null, 2) + '\n');

	const total = results.reduce((n, r) => n + r.bytes, 0);
	log(`${results.length} pantallas · ${(total / 1024 / 1024).toFixed(2)} MB total`);
	for (const r of results) log(`  ${r.id.padEnd(20)} ${r.w}x${r.h}  full ${String(r.full).padStart(5)} KB  preview ${String(r.prev).padStart(4)} KB`);
}

/**
 * Escribe el par full/preview en WebP. La re-codificación la hace el propio
 * Chromium (canvas.toDataURL) — así el build no depende de cwebp/ImageMagick,
 * que no están garantizados en el entorno.
 */
async function emit(encoder, id, png) {
	const out = await encoder.evaluate(async ({ b64, previewW }) => {
		const img = new Image();
		img.src = 'data:image/png;base64,' + b64;
		await img.decode();
		const draw = (w, h) => {
			const c = document.createElement('canvas');
			c.width = w; c.height = h;
			const cx = c.getContext('2d');
			cx.imageSmoothingQuality = 'high';
			cx.drawImage(img, 0, 0, w, h);
			return c.toDataURL('image/webp', 0.82);
		};
		const pw = Math.min(previewW, img.naturalWidth);
		const ph = Math.round(img.naturalHeight * (pw / img.naturalWidth));
		return {
			w: img.naturalWidth, h: img.naturalHeight, pw, ph,
			full: draw(img.naturalWidth, img.naturalHeight),
			preview: draw(pw, ph),
		};
	}, { b64: png.toString('base64'), previewW: PREVIEW_W * 2 });

	// Cuando la captura ya es más angosta que el ancho de preview (las
	// pantallas de mobile), la derivada saldría byte por byte igual que la
	// original: se omite y la tarjeta referencia la misma imagen.
	const samePreview = out.pw >= out.w;
	const files = samePreview ? [['', out.full]] : [['', out.full], ['-preview', out.preview]];
	for (const [suffix, data] of files) {
		if (!data.startsWith('data:image/webp')) throw new Error(`${id}${suffix}: Chromium no devolvió WebP`);
		writeFileSync(join(SHOT_OUT, `${id}${suffix}.webp`), Buffer.from(data.split(',')[1], 'base64'));
	}
	return {
		id, w: out.w, h: out.h, pw: out.pw, ph: out.ph, samePreview, bytes: out.full.length * 0.75,
		full: Math.round(out.full.length * 0.75 / 1024),
		prev: Math.round(out.preview.length * 0.75 / 1024),
	};
}

function chromiumPath() {
	const base = '/opt/pw-browsers';
	if (!existsSync(base)) return undefined;
	const dir = readdirSync(base).find((d) => /^chromium-\d+$/.test(d));
	return dir ? join(base, dir, 'chrome-linux/chrome') : undefined;
}

async function fetchText(url) {
	const r = await fetch(url, { headers: { 'user-agent': 'Mozilla/5.0 Chrome/120 Safari/537.36' } });
	if (!r.ok) throw new Error(`${url}: HTTP ${r.status}`);
	return r.text();
}
async function fetchBuffer(url) {
	const r = await fetch(url);
	if (!r.ok) throw new Error(`${url}: HTTP ${r.status}`);
	return Buffer.from(await r.arrayBuffer());
}

/* ========================================================================== */

const task = process.argv[2] || 'all';
if (task === 'css' || task === 'all') buildCss();
if (task === 'shots' || task === 'all') await buildShots();
// html va DESPUÉS de shots: lee las dimensiones que shots deja en *.json.
if (task === 'html' || task === 'all') buildHtml();
log('ok');
