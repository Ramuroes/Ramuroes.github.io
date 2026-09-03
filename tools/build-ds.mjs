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

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const SRC = join(ROOT, 'docs/ds-src/restimator');
const THEME = join(ROOT, 'estavillo-child');
const CSS_OUT = join(THEME, 'assets/css/ds-restimator');
const PHP_OUT = join(THEME, 'ds/restimator');
const SHOT_OUT = join(THEME, 'assets/ds/restimator/screens');

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
		// Comentarios: se copian tal cual.
		if (css.startsWith('/*', i)) {
			const end = css.indexOf('*/', i + 2);
			const stop = end === -1 ? css.length : end + 2;
			out += css.slice(i, stop);
			i = stop;
			continue;
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
	if (fontImport) writeFileSync(join(CSS_OUT, '.fonts-url.txt'), fontImport + '\n');

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
	{ id: 'calculator',      name: 'Calculadora',              file: 'Calculator.html',    kind: 'screen' },
	{ id: 'history',         name: 'Historial',                file: 'History.html',       kind: 'screen' },
	{ id: 'product-editor',  name: 'Editor de producto',       file: 'ProductEditor.html', kind: 'screen' },
	{ id: 'client-summary',  name: 'Resumen cliente',          file: 'ClientSummary.html', kind: 'screen' },
	{ id: 'catalogs',        name: 'Catálogos',                file: 'Catalogs.html',      kind: 'screen' },
	{ id: 'light-dark',      name: 'Comparación light / dark', file: 'Light & Dark Comparison.html', kind: 'view', prepare: prepareLightDark, tallViewport: true },
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
	{ id: 'mobile-calculator', name: 'Calculadora', note: '390 · default — form + resumen pinneado' },
	{ id: 'mobile-home',       name: 'Inicio',      note: '390 · default' },
	{ id: 'mobile-history',    name: 'Historial',   note: '390 · default' },
];

/** Inventario de artefactos del proyecto (ex-tabla "Otras piezas"), sin links. */
const ARTIFACTS = [
	['Refinamiento HiFi — Auditoría y Decisiones', 'Auditoría', 'Auditoría wireframe vs HiFi, problemas UX/visuales, comparación A/B/C/D del panel de resultado.'],
	['Typography Comparison',                      'Auditoría', 'Hanken vs Plus Jakarta contra pantallas reales — decisión de congelar Hanken.'],
	['V1 Design Freeze · Readiness Audit',         'Freeze',    'Cierre de alcance V1 y verificación de que el sistema estaba listo para congelarse.'],
	['Accessibility Review · UX Review',           'Review',    'Resultados de las revisiones de accesibilidad y de UX sobre el kit congelado.'],
	['Claude Code Handoff Package',                'Handoff',   'Paquete de handoff a desarrollo.'],
];

const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

/** Lee las dimensiones reales de un PNG/WebP generado, para emitir width/height. */
function readSize(id) {
	const meta = join(SHOT_OUT, `${id}.json`);
	if (!existsSync(meta)) return null;
	return JSON.parse(readFileSync(meta, 'utf8'));
}

/**
 * Emite una tarjeta de pantalla: toda la preview es el trigger del lightbox
 * compartido del portfolio (assets/js/case-figure-lightbox.js). Sin <a>, sin
 * iframe y sin ninguna ruta a .html local.
 *
 * El visor lee data-es-zoom-src/-w/-h, el alt del <img> interno y el
 * .es-case-caption del <figure> contenedor — por eso el markup es un
 * <figure> con <button> adentro y no un <div>.
 */
function shotCard(shot, { mobile = false } = {}) {
	const full = readSize(shot.id) || { w: 0, h: 0, pw: 0, ph: 0 };
	const cls = mobile ? 'shot shot--mobile' : 'shot';
	const meta = mobile ? shot.note : shot.file;
	return `
    <figure class="${cls}">
      <figcaption class="cap">
        <span class="nm">${esc(shot.name)}</span><span class="fl">${esc(meta)}</span>
        <span class="r"><span class="zoom-hint" aria-hidden="true">Ampliar</span></span>
      </figcaption>
      <button type="button" class="vp"
        data-es-zoom-trigger
        data-es-zoom-src="<?php echo esc_url( $es_ds_screens . '${shot.id}.webp' ); ?>"
        data-es-zoom-w="${full.w}" data-es-zoom-h="${full.h}"
        data-es-zoom-close-label="<?php echo esc_attr( es__( 'lightbox_close' ) ); ?>"
        data-es-zoom-in-label="<?php echo esc_attr( es__( 'lightbox_zoom_in' ) ); ?>"
        data-es-zoom-out-label="<?php echo esc_attr( es__( 'lightbox_zoom_out' ) ); ?>"
        data-es-zoom-reset-label="<?php echo esc_attr( es__( 'lightbox_reset' ) ); ?>"
        aria-label="<?php echo esc_attr( sprintf( es__( 'ds_expand_screen' ), '${esc(shot.name).replace(/'/g, "\\'")}' ) ); ?>">
        <img src="<?php echo esc_url( $es_ds_screens . '${shot.id}-preview.webp' ); ?>"
             alt="${esc(shot.name)} — Presupuestador RE"
             width="${full.pw}" height="${full.ph}" loading="lazy" decoding="async">
        <span class="vp-expand" aria-hidden="true">
          <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
        </span>
      </button>
    </figure>`;
}

function buildHtml() {
	ensure(PHP_OUT);
	let html = readFileSync(join(SRC, 'master-documentation.html'), 'utf8');

	// --- cuerpo del documento --------------------------------------------
	const start = html.indexOf('<div class="doc">');
	const scriptAt = html.indexOf('<script>', start);
	if (start === -1 || scriptAt === -1) throw new Error('master-documentation.html: no se encontró <div class="doc"> / <script>');
	const lastClose = html.lastIndexOf('</div>', scriptAt);
	let doc = html.slice(start, lastClose + 6);

	const before = doc;

	// --- §08 Screen Examples: desktop -------------------------------------
	const gridStart = doc.indexOf('<div class="shots">');
	const gridEnd = doc.indexOf('</div>\n  <p class="body"', gridStart);
	if (gridStart === -1 || gridEnd === -1) throw new Error('§08: no se encontró la grilla .shots');
	doc = doc.slice(0, gridStart)
		+ '<div class="shots">' + DESKTOP_SHOTS.map((s) => shotCard(s)).join('') + '\n  </div>'
		+ doc.slice(gridEnd + 6);

	// Lede de la sección: describía los embeds vivos, que ya no existen.
	doc = doc.replace(
		/<p class="lede">Pantallas reales construidas con el sistema\.[\s\S]*?<\/p>/,
		'<p class="lede">Pantallas reales construidas con el sistema. Las cinco de desktop son las del UI kit; las tres de mobile vienen de la spec de implementación Mobile v1. Cada una se muestra como captura de alta resolución y se puede ampliar para inspeccionarla en detalle.</p>'
	);

	// Nota bajo la grilla: describía los embeds interactivos y "Abrir ↗".
	doc = doc.replace(
		/<p class="body" style="margin-top:var\(--re-s4\);font-size:12\.5px;color:var\(--re-ink-4\)">Los embeds[\s\S]*?<\/p>/,
		'<p class="body" style="margin-top:var(--re-s4);font-size:12.5px;color:var(--re-ink-4)">Capturas de alta resolución de las pantallas reales del kit. Hacé click en cualquiera para ampliarla e inspeccionarla en detalle.</p>'
	);

	// --- §08 Screen Examples: mobile --------------------------------------
	// El original embebía la spec entera en un solo tile ilegible; acá pasan a
	// ser las TRES pantallas que la propia spec documenta (01 Calculadora,
	// 02 Inicio, 03 Historial), que es lo que anuncia el contador "3 mobile".
	const mobStart = doc.indexOf('<div class="shot"><div class="cap"><span class="nm">Mobile v1');
	if (mobStart === -1) throw new Error('§08: no se encontró el tile de mobile');
	const mobEnd = doc.indexOf('</div></div>', doc.indexOf('<div class="vp"', mobStart)) + 12;
	doc = doc.slice(0, mobStart)
		+ '<div class="shots shots--mobile">' + MOBILE_SHOTS.map((s) => shotCard(s, { mobile: true })).join('') + '\n  </div>'
		+ doc.slice(mobEnd);

	// --- §08 "Otras piezas": tabla de links -> inventario sin navegación ---
	const tblStart = doc.indexOf('<div class="scrollx"><table class="tb">\n    <thead><tr><th>Archivo</th>');
	if (tblStart === -1) throw new Error('§08: no se encontró la tabla de otras piezas');
	const tblEnd = doc.indexOf('</table></div>', tblStart) + 14;
	const rows = ARTIFACTS.map(([name, kind, what]) =>
		`      <tr><td><span class="art-name">${esc(name)}</span></td><td><span class="art-kind">${esc(kind)}</span></td><td>${esc(what)}</td></tr>`
	).join('\n');
	doc = doc.slice(0, tblStart)
		+ `<div class="scrollx"><table class="tb">\n    <thead><tr><th>Artefacto</th><th>Tipo</th><th>Qué contiene</th></tr></thead>\n    <tbody>\n${rows}\n    </tbody></table></div>\n  <p class="body" style="margin-top:var(--re-s4);font-size:12.5px;color:var(--re-ink-4)">Documentos internos del proyecto. Se listan como inventario: no forman parte de esta publicación.</p>`
		+ doc.slice(tblEnd);

	// --- §07: link suelto a la spec mobile --------------------------------
	doc = doc.replace(
		/Spec completa: <a href="[^"]*\.html">([^<]*)<\/a>/,
		'Spec completa: <span class="art-name">$1</span>'
	);

	if (doc === before) throw new Error('ninguna transformación se aplicó — ¿cambió la fuente?');

	// --- verificación dura: cero navegación a archivos locales ------------
	const leftovers = [...doc.matchAll(/(?:href|src)="([^"]*\.html[^"]*)"/g)].map((m) => m[1]);
	if (leftovers.length) throw new Error('quedaron refs a .html locales:\n  ' + leftovers.join('\n  '));
	if (/<iframe/i.test(doc)) throw new Error('quedaron <iframe> en el documento');

	const php = `<?php
/**
 * REstimator Design System — cuerpo del documento (ES).
 *
 * GENERADO POR tools/build-ds.mjs — NO EDITAR A MANO.
 * Fuente: docs/ds-src/restimator/master-documentation.html
 *
 * Diferencias con la fuente, todas producidas por el script:
 *  - §08 Screen Examples: los <iframe> a archivos .html locales y los enlaces
 *    "Abrir ↗" se reemplazan por previews estáticas que abren el lightbox
 *    compartido del portfolio ([data-es-zoom-trigger]).
 *  - §08 "Otras piezas": la tabla de enlaces pasa a inventario de texto plano.
 *  - Cero href/src a archivos .html locales (verificado por el script).
 *
 * El markup NO se reordena ni se simplifica: es el mismo documento.
 *
 * @package estavillo-child
 * @var string $es_ds_screens URI base de assets/ds/restimator/screens/.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
${doc}
`;
	writeFileSync(join(PHP_OUT, 'master-es.php'), php);
	log(`master-es.php  ${(php.length / 1024).toFixed(1)} KB  · 0 refs .html · 0 iframes`);
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

	for (const [suffix, data] of [['', out.full], ['-preview', out.preview]]) {
		if (!data.startsWith('data:image/webp')) throw new Error(`${id}${suffix}: Chromium no devolvió WebP`);
		writeFileSync(join(SHOT_OUT, `${id}${suffix}.webp`), Buffer.from(data.split(',')[1], 'base64'));
	}
	// Dimensiones para que buildHtml() emita width/height reales (sin CLS).
	writeFileSync(join(SHOT_OUT, `${id}.json`), JSON.stringify({ w: out.w, h: out.h, pw: out.pw, ph: out.ph }));
	return {
		id, w: out.w, h: out.h, bytes: out.full.length * 0.75,
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
