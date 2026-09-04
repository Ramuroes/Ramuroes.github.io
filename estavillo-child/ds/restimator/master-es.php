<?php
/**
 * REstimator Design System — cuerpo del documento (ES).
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
<div class="doc">

<!-- ================= PERSISTENT NAV ================= -->
<nav class="rail">
  <div class="rail-brand"><div class="lm">RE</div><div class="t">REstimator<span>Design System · master doc</span></div></div>
  <div class="rail-grp">Documentación</div>
  <a class="nv on" href="#s00"><span class="n">00</span> Visión general</a>
  <a class="nv" href="#s01"><span class="n">01</span> Fundamentos</a>
  <a class="nv" href="#s02"><span class="n">02</span> Componentes</a>
  <a class="nv" href="#s03"><span class="n">03</span> Navegación y App Shell</a>
  <a class="nv" href="#s04"><span class="n">04</span> Formularios y entrada de datos</a>
  <a class="nv" href="#s05"><span class="n">05</span> Visualización de datos</a>
  <a class="nv" href="#s06"><span class="n">06</span> Patrones de producto</a>
  <a class="nv" href="#s07"><span class="n">07</span> Sistema responsive</a>
  <a class="nv" href="#s08"><span class="n">08</span> Ejemplos de pantalla</a>
  <a class="nv" href="#s09"><span class="n">09</span> Inventario de componentes</a>
  <a class="nv" href="#s10"><span class="n">10</span> Diseño → Implementación</a>
  <div class="rail-grp">Estado</div>
  <a class="nv" href="#needs-review"><span class="n">EV</span> Evolución del sistema</a>
  <div class="rail-foot">Documentación maestra del sistema.<br>Tema dark.<br>Tipografía congelada 2026‑06‑16.</div>
</nav>

<main class="main">

<!-- ================= HERO / PORTFOLIO ENTRY ================= -->
<header class="hero">
  <div class="in">
    <div class="eyebrow">Design System · Documentación maestra</div>
    <h1>REstimator Design System</h1>
    <p class="sub">El sistema de diseño de <b style="color:#fff">Presupuestador RE</b>, una herramienta B2B de presupuestación para talleres de herrería y metalurgia. Dark industrial, denso, desktop‑first y con un único acento ámbar racionado. Esta documentación reúne sus foundations, sus componentes, sus patrones de producto y su sistema responsive.</p>
    <div class="facts">
      <div><div class="k">Tokens</div><div class="v num">147</div><div class="d">color · tipo · espacio · elevación</div></div>
      <div><div class="k">Componentes</div><div class="v num">33</div><div class="d">exports en el namespace</div></div>
      <div><div class="k">Pantallas</div><div class="v v--pair"><span class="n num">5</span> desktop <span class="n num">3</span> mobile</div><div class="d">del UI kit y de la spec Mobile v1</div></div>
      <div><div class="k">Tema</div><div class="v">Dark</div><div class="d">el único tema del sistema</div></div>
    </div>
  </div>
</header>

<div class="pad">

<!-- ============================================================ 00 -->
<section class="sec" id="s00">
  <div class="sec-h"><span class="num-badge">00</span><h2>Visión general</h2></div>
  <p class="lede">Presupuestador RE ayuda a presupuestadores y dueños de taller a producir cotizaciones más rápidas y consistentes con criterios estandarizados: familias → subcategorías → dimensiones, rangos de precio calibrados, jornales y referencias de material. Es una herramienta de trabajo diario.</p>

  <h3 class="sub"><span class="tick"></span>Propósito del sistema</h3>
  <p class="body">Codificar ese producto como foundations, componentes y recreaciones de pantalla reutilizables, para que cualquier superficie nueva se construya on‑brand en minutos. El prototipo mid‑fi <b>V1 congelado</b> es la referencia: la arquitectura de información, los flujos y la estructura de pantallas ya están decididos. El sistema eleva <b>únicamente la calidad visual</b>; no rediseña flujos ni cambia la IA.</p>

  <h3 class="sub"><span class="tick"></span>Principios</h3>
  <div class="prin">
    <div class="p"><div class="k">Velocidad</div><div class="d">Densidad operativa. El estimador carga medidas y lee el recomendado en bucle; nada se interpone.</div></div>
    <div class="p"><div class="k">Claridad</div><div class="d">Estructura dibujada con hairlines de 1px, no con fills pesados. Jerarquía tipográfica antes que color.</div></div>
    <div class="p"><div class="k">Confianza</div><div class="d">Cada número explica de dónde sale. Los avisos son warn‑only y nunca bloquean el cálculo.</div></div>
    <div class="p"><div class="k">Eficiencia</div><div class="d">Sin gradientes, glass, blur decorativo ni coreografía de entrada. Motion mínimo y funcional.</div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Arquitectura general</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Capa</th><th>Ubicación</th><th>Qué contiene</th></tr></thead>
    <tbody>
      <tr><td>Entrada global</td><td class="tok">styles.css</td><td>Manifiesto de <span class="mono">@import</span> únicamente. Los consumidores enlazan este archivo.</td></tr>
      <tr><td>Tokens</td><td class="tok">tokens/</td><td><span class="mono">fonts · colors · typography · spacing · elevation · theme-light · base</span></td></tr>
      <tr><td>Componentes</td><td class="tok">components/</td><td>React, 6 categorías. Namespace <span class="mono">window.PresupuestadorREDesignSystem_84f335</span></td></tr>
      <tr><td>Foundation cards</td><td class="tok">guidelines/</td><td>16 especímenes de color, tipo, espacio, marca y validación de tema.</td></tr>
      <tr><td>UI kit</td><td class="tok">ui_kits/presupuestador/</td><td>5 pantallas hi‑fi interactivas del producto congelado, dark. Token‑driven vía <span class="mono">kit.css</span> + <span class="mono">shell.js</span></td></tr>
      <tr><td>Spec mobile</td><td class="tok">Mobile v1 - Spec de implementación.html</td><td>3 pantallas mobile finales con medidas, spacing, sticky y componentes.</td></tr>
      <tr><td>Agent skill</td><td class="tok">SKILL.md</td><td>Hace la carpeta usable como Agent Skill en Claude Code.</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Estado y versionado</h3>
  <div class="grid3">
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Congelado</h4>
      <ul class="body" style="margin:0"><li>IA, flujos y estructura de pantallas (V1).</li><li>Pairing tipográfico (2026‑06‑16) — no re‑explorar.</li><li>Las 5 reglas de producto (abajo).</li></ul></div></div>
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Estable y en uso</h4>
      <ul class="body" style="margin:0"><li>147 tokens · tema dark.</li><li>33 componentes exportados.</li><li>5 pantallas desktop del kit.</li></ul></div></div>
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Especificado, sin construir</h4>
      <ul class="body" style="margin:0"><li>6 componentes mobile (Mobile v1).</li><li>Capa tablet (documentada, sin artefacto).</li><li><span class="mono">templates/</span> — 0 templates.</li></ul></div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Reglas de producto no negociables</h3>
  <p class="body">Vienen de S12 y se respetan en toda superficie nueva. Son reglas del dominio del negocio, no criterios de diseño.</p>
  <div class="rules">
    <div class="rule"><span class="n">1</span><div class="tx"><b>Freeze</b>Cada presupuesto congela la versión de parámetros con la que fue calculado; los publicados son inmutables.</div></div>
    <div class="rule"><span class="n">2</span><div class="tx"><b>QA‑gate</b>Un cambio de parámetro o producto llega a producción sólo a través de un QA Run.</div></div>
    <div class="rule"><span class="n">3</span><div class="tx"><b>Moneda</b>El producto opera en UYU; el tipo de cambio sólo convierte costos internos.</div></div>
    <div class="rule"><span class="n">4</span><div class="tx"><b>Editor</b>S5A (<span class="mono">ProductEditor</span>) es el único editor de producto.</div></div>
    <div class="rule"><span class="n">5</span><div class="tx"><b>Privacidad</b>El resumen al cliente nunca serializa márgenes, jornales ni calibración.</div></div>
  </div>
</section>

<!-- ============================================================ 01 -->
<section class="sec" id="s01">
  <div class="sec-h"><span class="num-badge">01</span><h2>Fundamentos</h2><span class="cnt">147 tokens</span></div>
  <p class="lede">Todos los valores de esta sección son los tokens reales del sistema, leídos de <span class="mono">tokens/</span>. Los componentes leen siempre <span class="mono">var(--re-*)</span>, así que ninguno fija un valor a mano.</p>

  <h3 class="sub"><span class="tick"></span>Color · rampa grafito (base dark)</h3>
  <div class="sw-grid">
    <div class="sw"><div class="chip" style="background:#0d0e11"></div><div class="meta"><div class="nm">--re-canvas</div><div class="hx">#0d0e11</div><div class="use">Fondo de app — casi negro</div></div></div>
    <div class="sw"><div class="chip" style="background:#16181d"></div><div class="meta"><div class="nm">--re-surface</div><div class="hx">#16181d</div><div class="use">Cards, paneles</div></div></div>
    <div class="sw"><div class="chip" style="background:#1d2026"></div><div class="meta"><div class="nm">--re-surface-2</div><div class="hx">#1d2026</div><div class="use">Inset / hover, inputs</div></div></div>
    <div class="sw"><div class="chip" style="background:#262a31"></div><div class="meta"><div class="nm">--re-surface-3</div><div class="hx">#262a31</div><div class="use">Inset profundo, tracks</div></div></div>
    <div class="sw"><div class="chip" style="background:#f2f3f4"></div><div class="meta"><div class="nm">--re-ink</div><div class="hx">#f2f3f4</div><div class="use">Texto primario</div></div></div>
    <div class="sw"><div class="chip" style="background:#c2c6cc"></div><div class="meta"><div class="nm">--re-ink-2</div><div class="hx">#c2c6cc</div><div class="use">Texto secundario</div></div></div>
    <div class="sw"><div class="chip" style="background:#9197a0"></div><div class="meta"><div class="nm">--re-ink-3</div><div class="hx">#9197a0</div><div class="use">Terciario / hints</div></div></div>
    <div class="sw"><div class="chip" style="background:#7f858e"></div><div class="meta"><div class="nm">--re-ink-4</div><div class="hx">#7f858e</div><div class="use">Labels tenues, placeholders — elevado para AA</div></div></div>
    <div class="sw"><div class="chip" style="background:#262a31"></div><div class="meta"><div class="nm">--re-line</div><div class="hx">#262a31</div><div class="use">Hairlines, bordes de card</div></div></div>
    <div class="sw"><div class="chip" style="background:#313641"></div><div class="meta"><div class="nm">--re-line-2</div><div class="hx">#313641</div><div class="use">Bordes de input</div></div></div>
    <div class="sw"><div class="chip" style="background:#434954"></div><div class="meta"><div class="nm">--re-line-strong</div><div class="hx">#434954</div><div class="use">Bordes enfatizados, dashed</div></div></div>
  </div>

  <h4 class="mini">Charcoal — paneles más profundos</h4>
  <div class="sw-grid">
    <div class="sw"><div class="chip" style="background:#0a0b0e"></div><div class="meta"><div class="nm">--re-charcoal</div><div class="hx">#0a0b0e</div><div class="use">Bloque de recomendado, total del documento</div></div></div>
    <div class="sw"><div class="chip" style="background:#14161b"></div><div class="meta"><div class="nm">--re-charcoal-2</div><div class="hx">#14161b</div><div class="use">Sub‑bloque sobre charcoal</div></div></div>
    <div class="sw"><div class="chip" style="background:#2b2f37"></div><div class="meta"><div class="nm">--re-charcoal-line</div><div class="hx">#2b2f37</div><div class="use">Hairline sobre charcoal</div></div></div>
  </div>

  <h4 class="mini">Ámbar — el único color de marca</h4>
  <div class="sw-grid">
    <div class="sw"><div class="chip" style="background:#e0911e"></div><div class="meta"><div class="nm">--re-amber</div><div class="hx">#e0911e</div><div class="use">CTA primario, rail activo</div></div></div>
    <div class="sw"><div class="chip" style="background:#f0a634"></div><div class="meta"><div class="nm">--re-amber-strong</div><div class="hx">#f0a634</div><div class="use">Hover / press (aclara en dark)</div></div></div>
    <div class="sw"><div class="chip" style="background:#f5b342"></div><div class="meta"><div class="nm">--re-amber-bright</div><div class="hx">#f5b342</div><div class="use">Valor recomendado, cifras headline</div></div></div>
    <div class="sw"><div class="chip" style="background:rgba(224,145,30,.13)"></div><div class="meta"><div class="nm">--re-amber-soft</div><div class="hx">rgba(224,145,30,.13)</div><div class="use">Fondo tintado</div></div></div>
    <div class="sw"><div class="chip" style="background:rgba(224,145,30,.34)"></div><div class="meta"><div class="nm">--re-amber-line</div><div class="hx">rgba(224,145,30,.34)</div><div class="use">Borde tintado</div></div></div>
    <div class="sw"><div class="chip" style="background:#f0b45e"></div><div class="meta"><div class="nm">--re-amber-ink</div><div class="hx">#f0b45e</div><div class="use">Texto ámbar sobre tinte</div></div></div>
  </div>
  <div class="callout"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18.9 6.1 22l1.2-6.5L2.5 9.4l6.6-.9z"/></svg>
    <p><b>Regla de racionamiento del ámbar.</b> El ámbar aparece en exactamente cuatro clases de momento: el <b>precio recomendado</b>
      (cifra ámbar sobre el charcoal más profundo), el <b>rail de navegación activo</b>, el <b>CTA primario / de envío</b> y el <b>logo</b>.
      Todo lo demás se resuelve con grafito y hairlines. Ver <a href="#needs-review">NR‑08</a> por la extensión de esta regla a mobile.</p></div>

  <h4 class="mini">Semánticos (ajustados para dark)</h4>
  <div class="sw-grid">
    <div class="sw"><div class="chip" style="background:#52b06e"></div><div class="meta"><div class="nm">--re-ok</div><div class="hx">#52b06e</div><div class="use">+ <span class="mono">ok-bright #6fd08a</span>, soft, line</div></div></div>
    <div class="sw"><div class="chip" style="background:#d99a3a"></div><div class="meta"><div class="nm">--re-warn</div><div class="hx">#d99a3a</div><div class="use">+ <span class="mono">warn-ink #e6c188</span>, soft, line</div></div></div>
    <div class="sw"><div class="chip" style="background:#d86a52"></div><div class="meta"><div class="nm">--re-crit</div><div class="hx">#d86a52</div><div class="use">+ soft, line</div></div></div>
    <div class="sw"><div class="chip" style="background:#5b9be0"></div><div class="meta"><div class="nm">--re-info</div><div class="hx">#5b9be0</div><div class="use">+ soft, line</div></div></div>
  </div>

  <h4 class="mini">Ciclo de vida del presupuesto (Historial)</h4>
  <div class="stage" style="gap:var(--re-s4)">
    <span class="st"><span class="d d-draft"></span> Borrador</span>
    <span class="st"><span class="d d-sent"></span> Enviado</span>
    <span class="st"><span class="d d-appr"></span> Aprobado</span>
    <span class="st"><span class="d d-rej"></span> Rechazado</span>
    <span class="st"><span class="d d-cancel"></span> Cancelado</span>
    <span style="font-size:12px;color:var(--re-ink-4)">punto de color sobre pill neutra · <span class="mono">--re-st-draft/sent/appr/rej/cancel</span></span>
  </div>

  <h4 class="mini">Aliases semánticos — preferilos en componentes y pantallas</h4>
  <div class="scrollx"><table class="tb dense">
    <thead><tr><th>Alias</th><th>Resuelve a</th><th>Alias</th><th>Resuelve a</th></tr></thead>
    <tbody>
      <tr><td class="tok">--re-bg</td><td class="mono">--re-canvas</td><td class="tok">--re-border</td><td class="mono">--re-line</td></tr>
      <tr><td class="tok">--re-text</td><td class="mono">--re-ink</td><td class="tok">--re-border-field</td><td class="mono">--re-line-2</td></tr>
      <tr><td class="tok">--re-text-muted</td><td class="mono">--re-ink-2</td><td class="tok">--re-border-strong</td><td class="mono">--re-line-strong</td></tr>
      <tr><td class="tok">--re-text-subtle</td><td class="mono">--re-ink-3</td><td class="tok">--re-focus</td><td class="mono">--re-amber</td></tr>
      <tr><td class="tok">--re-text-faint</td><td class="mono">--re-ink-4</td><td class="tok">--re-accent / --re-primary</td><td class="mono">--re-amber</td></tr>
      <tr><td class="tok">--re-accent-hover</td><td class="mono">--re-amber-strong</td><td class="tok">--re-primary-hover</td><td class="mono">--re-amber-strong</td></tr>
    </tbody>
  </table></div>

  <h4 class="mini">Tema light — planificado</h4>
  <p class="body"><span class="art-kind">Planificado</span> El sistema publicado tiene <b>un solo tema: dark</b>. Existe un remap de color explorado —superficies blanco cálido, tinta grafito, bordes gris cálido— que dejaría intactos espaciado, tipografía, radios, jerarquía e interacción, con el ámbar como único acento. Todavía no forma parte del sistema y no se documenta como disponible.</p>

  <h3 class="sub"><span class="tick"></span>Tipografía · CONGELADA</h3>
  <p class="body">El pairing es final y no se re‑explora. Se evaluó una alternativa con Plus Jakarta Sans contra las pantallas
    reales (<span class="mono">Typography Comparison.html</span>) y fue rechazada: Hanken ganó en densidad, legibilidad a tamaño chico
    y tono industrial.</p>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Primary UI — Hanken Grotesk</h4>
      <div style="font-size:34px;font-weight:700;letter-spacing:-.025em;line-height:1.1">Presupuesto recomendado</div>
      <div style="font-size:13px;color:var(--re-ink-3);margin-top:10px">400 · 500 · 600 · 700 — grotesca humanista, industrial‑neutra, no “startup redondeada”. Se usa para todo: headings, body, controles, tablas.</div>
      <div class="mono" style="font-size:11.5px;color:var(--re-amber-ink);margin-top:10px">--re-font-sans</div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Technical — JetBrains Mono</h4>
      <div class="mono" style="font-size:26px;font-weight:600;line-height:1.15">GV-A-00148<br>PRE-2026-0148</div>
      <div style="font-size:13px;color:var(--re-ink-3);margin-top:10px">400 · 500 · 600 — códigos, IDs, fechas y cifras tabulares. Códigos de producto <span class="mono">ESC-RECT</span>.</div>
      <div class="mono" style="font-size:11.5px;color:var(--re-amber-ink);margin-top:10px">--re-font-mono</div>
    </div></div>
  </div>

  <h4 class="mini">Escala real</h4>
  <div class="scrollx"><table class="tb dense">
    <thead><tr><th>Token</th><th>Valor</th><th>Uso</th><th>Muestra</th></tr></thead>
    <tbody>
      <tr><td class="tok">--re-text-display</td><td class="mono">40px</td><td>La cifra hero de “recomendado”</td><td><span style="font-size:34px;font-weight:700;letter-spacing:-.02em;color:var(--re-amber-bright)" class="num">$135.599</span></td></tr>
      <tr><td class="tok">--re-text-2xl</td><td class="mono">23px</td><td>H1 de página</td><td><span style="font-size:23px;font-weight:600;letter-spacing:-.02em">Calculadora</span></td></tr>
      <tr><td class="tok">--re-text-xl</td><td class="mono">21px</td><td>H1 compacto</td><td><span style="font-size:21px;font-weight:600;letter-spacing:-.02em">Historial</span></td></tr>
      <tr><td class="tok">--re-text-lg</td><td class="mono">17px</td><td>H2 de sección</td><td><span style="font-size:17px;font-weight:600">Dimensiones</span></td></tr>
      <tr><td class="tok">--re-text-md</td><td class="mono">14.5px</td><td>Títulos de card</td><td><span style="font-size:14.5px;font-weight:600">Encabezado del presupuesto</span></td></tr>
      <tr><td class="tok">--re-text-base</td><td class="mono">14px</td><td>Body / controles</td><td><span style="font-size:14px">Ancho de paso</span></td></tr>
      <tr><td class="tok">--re-text-sm</td><td class="mono">13px</td><td>Body secundario, celdas</td><td><span style="font-size:13px;color:var(--re-ink-2)">Pedro Núñez · Barandas</span></td></tr>
      <tr><td class="tok">--re-text-xs</td><td class="mono">12px</td><td>Hints, captions</td><td><span style="font-size:12px;color:var(--re-ink-3)">decimales con coma</span></td></tr>
      <tr><td class="tok">--re-text-2xs</td><td class="mono">11px</td><td>Eyebrows, labels uppercase</td><td><span class="re-eyebrow">FABRICACIÓN ESTIMADA</span></td></tr>
    </tbody>
  </table></div>

  <h4 class="mini">Pesos · line-heights · tracking · helpers</h4>
  <div class="grid3">
    <div class="card"><div class="card-b"><dl class="spec">
      <dt>Regular</dt><dd>400</dd><dt>Medium</dt><dd>500</dd><dt>Semibold</dt><dd>600</dd><dt>Bold</dt><dd>700</dd></dl></div></div>
    <div class="card"><div class="card-b"><dl class="spec">
      <dt>tight</dt><dd>1.15</dd><dt>snug</dt><dd>1.35</dd><dt>normal</dt><dd>1.45</dd><dt>relaxed</dt><dd>1.6</dd></dl></div></div>
    <div class="card"><div class="card-b"><dl class="spec">
      <dt>ls-body</dt><dd>-0.005em</dd><dt>ls-heading</dt><dd>-0.02em</dd><dt>ls-tight</dt><dd>-0.01em</dd><dt>ls-eyebrow</dt><dd>0.06em</dd><dt>ls-caps</dt><dd>0.05em</dd></dl></div></div>
  </div>
  <div class="scrollx" style="margin-top:var(--re-s4)"><table class="tb dense">
    <thead><tr><th>Clase</th><th>Qué hace</th></tr></thead>
    <tbody>
      <tr><td class="tok">.re-root</td><td>Tipo base del documento: family, 14px, lh 1.45, tracking, color, antialiasing, <span class="mono">font-feature-settings:"tnum" 1,"ss01" 1</span>. Opt‑in en <span class="mono">&lt;body&gt;</span> o wrapper.</td></tr>
      <tr><td class="tok">.re-num</td><td>Cifras tabulares — dinero, medidas, códigos, fechas.</td></tr>
      <tr><td class="tok">.re-mono</td><td>Cambia a JetBrains Mono.</td></tr>
      <tr><td class="tok">.re-eyebrow</td><td>Eyebrow / kicker: 11px, 600, +0.06em, uppercase, color faint.</td></tr>
    </tbody>
  </table></div>

  <h4 class="mini">Números y unidades — formato uruguayo, siempre tabular</h4>
  <div class="scrollx"><table class="tb dense">
    <thead><tr><th>Tipo</th><th>Formato</th><th>Ejemplo</th></tr></thead>
    <tbody>
      <tr><td>Dinero</td><td>punto de miles, sin decimales</td><td class="mono">$168.000</td></tr>
      <tr><td>Medidas</td><td>coma decimal + unidad</td><td class="mono">0,80 m · 2,90 m</td></tr>
      <tr><td>Jornales</td><td>coma decimal, un decimal</td><td class="mono">7,0</td></tr>
      <tr><td>Porcentajes</td><td>signo + valor</td><td class="mono">+ 8% · IVA 22%</td></tr>
      <tr><td>Códigos</td><td>mono, mayúsculas</td><td class="mono">GV-A-00148 · ESC-RECT</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Spacing · grilla de 4px</h3>
  <div class="card"><div class="card-b" style="display:flex;flex-direction:column;gap:9px">
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s1</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">4px</span><span style="height:13px;width:4px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s2</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">8px</span><span style="height:13px;width:8px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s3</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">12px</span><span style="height:13px;width:12px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s4</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">16px</span><span style="height:13px;width:16px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s5</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">20px</span><span style="height:13px;width:20px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s6</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">24px</span><span style="height:13px;width:24px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s7</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">32px</span><span style="height:13px;width:32px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s8</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">40px</span><span style="height:13px;width:40px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s9</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">48px</span><span style="height:13px;width:48px;background:var(--re-amber);border-radius:2px"></span></div>
    <div style="display:flex;align-items:center;gap:14px"><span class="mono" style="width:88px;font-size:11.5px;color:var(--re-amber-ink)">--re-s10</span><span class="mono" style="width:44px;font-size:11.5px;color:var(--re-ink-3)">64px</span><span style="height:13px;width:64px;background:var(--re-amber);border-radius:2px"></span></div>
  </div></div>

  <h3 class="sub"><span class="tick"></span>Sizing · constantes de layout (del shell V1 congelado)</h3>
  <div class="grid3">
    <div class="card"><div class="card-b"><dl class="spec"><dt>Sidebar</dt><dd class="am">248px</dd><dt>Topbar</dt><dd class="am">60px</dd></dl>
      <div style="font-size:11.5px;color:var(--re-ink-4);margin-top:11px"><span class="mono">--re-sidebar-w · --re-topbar-h</span></div></div></div>
    <div class="card"><div class="card-b"><dl class="spec"><dt>Ancho de documento</dt><dd class="am">1080px</dd></dl>
      <div style="font-size:11.5px;color:var(--re-ink-4);margin-top:11px"><span class="mono">--re-content-max</span> — documento / lectura</div></div></div>
    <div class="card"><div class="card-b"><dl class="spec"><dt>Control estándar</dt><dd class="am">40px</dd><dt>Control compacto</dt><dd class="am">32px</dd></dl>
      <div style="font-size:11.5px;color:var(--re-ink-4);margin-top:11px"><span class="mono">--re-control-h · --re-control-h-sm</span> · ver <a href="#needs-review">NR‑04</a></div></div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Radius</h3>
  <div class="stage">
    <div style="text-align:center"><div style="width:74px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:7px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r-sm · 7px</div></div>
    <div style="text-align:center"><div style="width:74px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:9px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r · 9px</div></div>
    <div style="text-align:center"><div style="width:74px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:13px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r-lg · 13px</div></div>
    <div style="text-align:center"><div style="width:74px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:30px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r-pill · 30px</div></div>
    <div style="text-align:center"><div style="width:56px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:999px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r-full · 999px</div></div>
    <div style="font-size:12.5px;color:var(--re-ink-3);max-width:24ch">Consistente y modesto — nada completamente redondeado excepto avatares y puntos.</div>
  </div>

  <h3 class="sub"><span class="tick"></span>Borders</h3>
  <p class="body">La estructura se dibuja con líneas de 1px, no con fills pesados. Hairlines en todas partes:
    <span class="mono">--re-line</span> para divisores y bordes de card, <span class="mono">--re-line-2</span> en inputs,
    <span class="mono">--re-line-strong</span> para énfasis y dashed.</p>

  <h3 class="sub"><span class="tick"></span>Elevation</h3>
  <p class="body">En dark la profundidad es <b>una superficie más clara + un borde de 1px</b>; las sombras son profundas y calladas
    por debajo. Sin glow, sin sombra de color.</p>
  <div class="grid3">
    <div class="card" style="box-shadow:var(--re-shadow-sm)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow-sm</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Lift hairline — cards por defecto, tiles</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 1px 2px rgba(0,0,0,.40)</div></div></div>
    <div class="card" style="box-shadow:var(--re-shadow)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Card en reposo</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 1px 3px / 0 1px 2px</div></div></div>
    <div class="card" style="box-shadow:var(--re-shadow-panel)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow-panel</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Panel flotante — result sticky, preview, menús</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 8px 24px / 0 2px 6px</div></div></div>
    <div class="card" style="box-shadow:var(--re-shadow-paper)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow-paper</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">El “papel” del documento al cliente</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 16px 44px / 0 3px 10px</div></div></div>
    <div class="card"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow-bar</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Save bar fija — la sombra apunta hacia arriba</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 -2px 16px rgba(0,0,0,.50)</div></div></div>
    <div class="card" style="border-color:var(--re-amber);box-shadow:var(--re-ring)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-ring / --re-ring-amber</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Focus — 3px ámbar sobre dark</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">.26 / .32 alpha</div></div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Iconography</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Reglas</h4>
      <ul class="body" style="margin:0"><li>SVG de línea inline únicamente. viewBox <span class="mono">24×24</span>, <span class="mono">stroke="currentColor"</span>, <span class="mono">stroke-width</span> ~1.7–1.8 (1.7 nav/UI, 2 chevrons/flechas), caps y joins redondos.</li>
        <li>Heredan el color del texto y se sitúan a <b>15–18px</b>.</li>
        <li>Sin icon font, sin sprite, sin PNG, sin emoji, sin pictogramas unicode. Un “✓” en toasts transitorios es la única excepción.</li>
        <li>Para superficies nuevas, copiá las formas del kit antes de recurrir a una librería. No hay set de CDN incluido: el set es chico y mantenido a mano.</li></ul></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Glifos recurrentes</h4>
      <div class="stage tight" style="gap:var(--re-s4)">
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2.5"/><path d="M8 7h8M8 11h3" stroke-linecap="round"/><rect x="13.5" y="11" width="3" height="6" rx="1"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 2.5l8 4.2v10.6L12 21.5 4 17.3V6.7z"/><path d="M4 6.7l8 4.3 8-4.3M12 11v10.5"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M4 7h10M18 7h2M4 17h2M10 17h10"/><circle cx="16" cy="7" r="2.3"/><circle cx="8" cy="17" r="2.3"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ok-bright)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4" stroke-linecap="round"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-warn)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4l9 16H3z"/><path d="M12 10v4M12 17h.01"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-amber)" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18.9 6.1 22l1.2-6.5L2.5 9.4l6.6-.9z"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9a2.1 2.1 0 0 0-3-3L5 17z"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h8l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path d="M14 3v5h5"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4a8 8 0 0 0-6.9 12l-1 4 4.1-1A8 8 0 1 0 12 4z"/></svg>
        <svg style="width:20px;height:20px;color:var(--re-ink-2)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
      </div>
      <div style="font-size:12px;color:var(--re-ink-4);margin-top:11px">calculadora · reloj · cubo · sliders · shield‑check · warning · estrella (recomendado) · lápiz · duplicar · documento · burbuja · lupa</div></div></div>
  </div>
  <h4 class="mini">Familia de taxonomía de producto — <span class="mono" style="text-transform:none;letter-spacing:0">TaxonomyIcon</span></h4>
  <p class="body">10 glifos de categoría de fabricación dibujados en el mismo lenguaje (24×24, stroke 1.7, sin fills, sin librería externa):
    <span class="mono">escaleras · barandas · rejas · cerramientos · puertas · portones · mobiliario · estructuras · techos · parrilleros</span>.
    Se usan en rails de rubro, dropdowns, cards de catálogo y chips para que el producto lea como herramienta especializada de
    metalurgia y no como app de negocio genérica. API: <span class="mono">&lt;TaxonomyIcon name="rejas" size={18} strokeWidth={1.7}/&gt;</span>;
    iterá <span class="mono">TAXONOMY_ICONS</span> para menús; color vía <span class="mono">currentColor</span> (ámbar en activo).</p>

  <h3 class="sub"><span class="tick"></span>Motion</h3>
  <p class="body">Mínimo y funcional. <span class="mono">.12–.15s ease</span> en hover / border / background; los toggles deslizan su knob;
    el único movimiento ambiente es el pulso lento del punto “En vivo” en paneles live. Sin bounces, sin parallax, sin coreografía
    de entrada, sin efectos de escala.</p>

  <h3 class="sub"><span class="tick"></span>Breakpoints</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Capa</th><th>Ancho de referencia</th><th>Rol</th></tr></thead>
    <tbody>
      <tr><td>Desktop</td><td class="mono">≥ 1200 · diseño 1440</td><td>Capa primaria. Sidebar fijo + panel de resultado lateral.</td></tr>
      <tr><td>Tablet</td><td class="mono">834</td><td>Capa intermedia con identidad propia: tabs superiores + bottom sheet.</td></tr>
      <tr><td>Mobile</td><td class="mono">390 · fluido 360–430</td><td>Diseñada, no adaptada. Bottom tab bar + resumen pinneado.</td></tr>
    </tbody>
  </table></div>
  <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑05</span><p>Los breakpoints <b>no están tokenizados</b>: viven en prosa y en las specs, no en <span class="mono">tokens/</span>. Cualquier implementación los está hardcodeando.</p></div>

  <h3 class="sub"><span class="tick"></span>Accessibility foundations</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Decisión</th><th>Valor</th><th>Por qué</th></tr></thead>
    <tbody>
      <tr><td>Contraste de labels tenues</td><td class="mono">--re-ink-4 #7f858e</td><td>Elevado desde <span class="mono">#686d76</span> (≈3.4:1) para superar WCAG AA 4.5:1 sobre superficies.</td></tr>
      <tr><td>Focus visible</td><td class="mono">--re-ring 3px ámbar</td><td>Anillo de 3px + borde ámbar en todo control enfocable. Nunca se remueve el outline sin reemplazo.</td></tr>
      <tr><td>Touch targets</td><td class="mono">≥ 44px</td><td>Mínimo táctil en mobile/tablet; los controles de 42px visuales llevan área extendida.</td></tr>
      <tr><td>Iconos sin texto</td><td class="mono">title obligatorio</td><td><span class="mono">IconButton</span> exige <span class="mono">title</span>, usado como tooltip y <span class="mono">aria-label</span>.</td></tr>
      <tr><td>Cifras</td><td class="mono">tabular-nums</td><td>Dinero y medidas nunca “bailan” entre estados; comparación vertical fiable.</td></tr>
      <tr><td>Estado nunca sólo por color</td><td>punto + etiqueta</td><td>El ciclo de vida usa punto de color <b>y</b> etiqueta en español.</td></tr>
    </tbody>
  </table></div>
</section>

<!-- ============================================================ 02 -->
<section class="sec" id="s02">
  <div class="sec-h"><span class="num-badge">02</span><h2>Componentes</h2><span class="cnt">33 exports · 6 categorías</span></div>
  <p class="lede">Los 33 componentes exportados por <span class="mono">window.PresupuestadorREDesignSystem_84f335</span>.
    Cada directorio tiene <span class="mono">&lt;Name&gt;.jsx</span> + <span class="mono">.d.ts</span> + <span class="mono">.prompt.md</span>
    y un specimen <span class="mono">*.card.html</span>. Los especímenes de abajo son réplicas token‑driven de los componentes reales
    (mismos tokens, misma anatomía) para que este documento no dependa de un runtime React.</p>

  <div class="callout"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
    <p><b>Cómo leer los subcomponentes.</b> Varios exports viven en un archivo padre: <span class="mono">Field/Input/Select/Textarea</span> ← <span class="mono">forms/Input.jsx</span> ·
      <span class="mono">Card/CardHead/CardBody/SectionHead</span> ← <span class="mono">layout/Card.jsx</span> ·
      <span class="mono">AppShell/LogoMark/Sidebar/NavGroup/NavItem/Topbar/Breadcrumbs/UserChip</span> ← <span class="mono">navigation/AppShell.jsx</span> ·
      <span class="mono">Table/TableToolbar/RowActions/Pagination</span> ← <span class="mono">data/Table.jsx</span> ·
      <span class="mono">Badge/StatusBadge</span> y <span class="mono">Alert/AlertGroup</span> ← sus archivos de <span class="mono">feedback/</span>.</p></div>

  <!-- Button -->
  <h3 class="sub"><span class="tick"></span>Button</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="8" width="18" height="8" rx="3"/></svg></span><span class="tt">Variantes</span><span class="hint">secondary · primary · accent · ghost</span></div>
      <div class="card-b"><div class="stage">
        <button class="b">Secondary</button><button class="b pri">Primary</button><button class="b acc">Accent</button><button class="b gh">Ghost</button>
      </div>
      <div class="stage" style="margin-top:var(--re-s3)"><span class="stage-lab" style="width:100%;margin:0 0 4px">Tamaños · sm 32 · md 40 · lg 46</span>
        <button class="b sm">sm</button><button class="b">md</button><button class="b lg">lg</button>
      </div>
      <div class="stage" style="margin-top:var(--re-s3)"><span class="stage-lab" style="width:100%;margin:0 0 4px">Estados</span>
        <button class="b">Default</button><button class="b foc">Focus</button><button class="b dis">Disabled</button>
        <button class="b acc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l16-8-6 16-2-6z"/></svg> Con icono</button>
      </div></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Propósito</h4>
      <p class="body" style="margin-bottom:var(--re-s4)">Control de acción principal. <b>Exactamente un</b> <span class="mono">primary</span> (o <span class="mono">accent</span>) por vista; todo lo demás es <span class="mono">secondary</span> o <span class="mono">ghost</span>.</p>
      <h4 class="mini">API</h4>
      <dl class="spec"><dt>variant</dt><dd>secondary | primary | accent | ghost</dd><dt>size</dt><dd>sm | md | lg</dd><dt>block</dt><dd>boolean</dd><dt>iconLeft / iconRight</dt><dd>ReactNode (16px)</dd></dl>
      <h4 class="mini">Semántica de variantes</h4>
      <ul class="body" style="margin:0"><li><b>secondary</b> — outline neutro, el caballo de batalla.</li>
        <li><b>primary</b> — fill charcoal sólido, la acción principal única de la pantalla.</li>
        <li><b>accent</b> — fill ámbar de marca, reservado a acciones hero de envío/compartir.</li>
        <li><b>ghost</b> — sin borde, acciones inline de baja énfasis.</li></ul>
      <h4 class="mini">Tokens</h4>
      <div class="mono" style="font-size:11.5px;color:var(--re-amber-ink);line-height:1.7">--re-control-h · --re-r-sm · --re-line-2 · --re-charcoal · --re-amber · --re-amber-strong · --re-ring</div>
    </div></div>
  </div>

  <!-- IconButton + Toggle + Segmented -->
  <h3 class="sub"><span class="tick"></span>IconButton · Toggle · SegmentedControl</h3>
  <div class="grid3">
    <div class="card"><div class="card-h"><span class="tt">IconButton</span></div><div class="card-b">
      <div class="stage"><span class="ib" title="Buscar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></span>
        <span class="ib bare" title="Editar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9a2.1 2.1 0 0 0-3-3L5 17z"/></svg></span>
        <span class="ib bare dgr" title="Eliminar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M5 7h14M9 7V5h6v2M7 7l1 13h8l1-13"/></svg></span></div>
      <dl class="spec" style="margin-top:var(--re-s4)"><dt>variant</dt><dd>default 34 | bare 30</dd><dt>danger</dt><dd>boolean</dd><dt>title</dt><dd class="am">obligatorio</dd></dl>
      <p class="body" style="margin:var(--re-s3) 0 0;font-size:12.5px">Icon‑only para topbar y acciones de fila. <span class="mono">title</span> se usa como tooltip y <span class="mono">aria-label</span>.</p></div></div>
    <div class="card"><div class="card-h"><span class="tt">Toggle</span></div><div class="card-b">
      <div class="stage col" style="gap:var(--re-s3)">
        <div style="display:flex;align-items:center;gap:11px"><span class="tg"><i></i></span><span style="font-size:13px;color:var(--re-ink-3)">off · md 38</span></div>
        <div style="display:flex;align-items:center;gap:11px"><span class="tg on"><i></i></span><span style="font-size:13px;color:var(--re-ink-2)">on · track charcoal</span></div>
        <div style="display:flex;align-items:center;gap:11px"><span class="tg on am"><i></i></span><span style="font-size:13px;color:var(--re-ink-2)">on · accent ámbar</span></div>
        <div style="display:flex;align-items:center;gap:11px"><span class="tg sm on"><i></i></span><span style="font-size:13px;color:var(--re-ink-3)">sm 32 · filas de tabla</span></div>
      </div>
      <dl class="spec" style="margin-top:var(--re-s4)"><dt>pressed / defaultPressed</dt><dd>boolean</dd><dt>size</dt><dd>md | sm</dd><dt>accent</dt><dd>boolean</dd><dt>disabled</dt><dd>boolean</dd></dl></div></div>
    <div class="card"><div class="card-h"><span class="tt">SegmentedControl</span></div><div class="card-b">
      <div class="stage col"><div class="seg"><span class="on">Estimación</span><span>Formal</span></div>
        <div class="seg" style="margin-top:var(--re-s3)"><span class="on"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="18" height="16" rx="2"/></svg> Tabla</span><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="8" height="8" rx="1.5"/><rect x="13" y="4" width="8" height="8" rx="1.5"/></svg> Cards</span></div></div>
      <dl class="spec" style="margin-top:var(--re-s4)"><dt>options</dt><dd>2–3 items</dd><dt>value</dt><dd>string</dd><dt>onChange</dt><dd>(value) =&gt; void</dd></dl>
      <p class="body" style="margin:var(--re-s3) 0 0;font-size:12.5px">Más de 3 opciones → usar <span class="mono">Select</span>.</p></div></div>
  </div>

  <!-- Field / Input / Select / Textarea -->
  <h3 class="sub"><span class="tick"></span>Field · Input · Select · Textarea</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="tt">Anatomía y estados</span><span class="hint">label + hint + control + note</span></div><div class="card-b stack">
      <div class="fld"><span class="lb">Ancho de paso <span class="op">m</span></span><div class="ctl"><span class="val num">0,62</span><span class="sfx">m</span></div></div>
      <div class="fld"><span class="lb">Valor del jornal</span><div class="ctl foc"><span class="pfx">$</span><span class="val num r">14.360</span></div>
        <div class="note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg> Referencia y control — no define el precio.</div></div>
      <div class="fld"><span class="lb">Familia</span><div class="ctl sel">Escaleras <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></div></div>
      <div class="fld"><span class="lb">Buscar</span><div class="ctl ph"><svg style="width:16px;height:16px;color:var(--re-ink-4);flex:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg><span class="val">Buscar por N°, cliente, obra…</span></div></div>
      <div class="fld"><span class="lb">Notas <span class="op">opcional</span></span><div class="ctl ta ph"><span class="val">Agregá notas para el cliente…</span></div></div>
      <div class="fld"><span class="lb">Bloqueado por freeze</span><div class="ctl dis"><span class="val">v37377</span></div></div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <div class="scrollx"><table class="tb dense">
        <thead><tr><th>Export</th><th>Props relevantes</th></tr></thead>
        <tbody>
          <tr><td>Field</td><td class="mono">label · optional · note · htmlFor</td></tr>
          <tr><td>Input</td><td class="mono">prefix · suffix · leadIcon · num</td></tr>
          <tr><td>Select</td><td class="mono">children (&lt;option&gt;) · chevron propio</td></tr>
          <tr><td>Textarea</td><td class="mono">nativo · resize vertical</td></tr>
        </tbody></table></div>
      <h4 class="mini">Comportamiento</h4>
      <ul class="body" style="margin:0"><li><b>Field</b> envuelve: label + hint inline (“opcional”) + control + note con icono.</li>
        <li><b>num</b> activa cifras tabulares y alineación para dinero y medidas.</li>
        <li><b>prefix/suffix</b> para <span class="mono">$</span> y unidades (<span class="mono">m</span>, <span class="mono">%</span>).</li>
        <li>Focus: borde ámbar + <span class="mono">--re-ring</span>. Disabled: opacidad .45.</li></ul>
      <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑06</span><p>No existe prop de <b>error/invalid</b> en <span class="mono">Field</span> ni <span class="mono">Input</span>. La validación del producto es warn‑only vía <span class="mono">Alert</span>, así que el error inline a nivel campo <b>no está definido</b> en el sistema.</p></div>
    </div></div>
  </div>

  <!-- Badge / StatusBadge -->
  <h3 class="sub"><span class="tick"></span>Badge · StatusBadge</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="tt">Tonos y formas</span></div><div class="card-b">
      <div class="stage"><span class="bdg">neutral</span><span class="bdg ok">ok</span><span class="bdg wn">warn</span><span class="bdg cr">crit</span><span class="bdg inf">info</span></div>
      <div class="stage" style="margin-top:var(--re-s3)"><span class="bdg"><span class="d"></span> con dot</span><span class="bdg sq">ESC-RECT</span><span class="bdg sq">GV-A-00148</span></div>
      <div class="stage" style="margin-top:var(--re-s3)"><span class="stage-lab" style="width:100%;margin:0 0 4px">StatusBadge — ciclo de vida</span>
        <span class="st"><span class="d d-draft"></span> Borrador</span><span class="st"><span class="d d-sent"></span> Enviado</span><span class="st"><span class="d d-appr"></span> Aprobado</span><span class="st"><span class="d d-rej"></span> Rechazado</span><span class="st"><span class="d d-cancel"></span> Cancelado</span></div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <dl class="spec"><dt>Badge · tone</dt><dd>neutral | ok | warn | crit | info</dd><dt>Badge · dot</dt><dd>boolean</dd><dt>Badge · square</dt><dd>chip mono de código</dd><dt>Badge · icon</dt><dd>ReactNode</dd><dt>StatusBadge · status</dt><dd>draft | sent | appr | rej | cancel</dd><dt>StatusBadge · label</dt><dd>override del español</dd></dl>
      <h4 class="mini">Uso</h4>
      <ul class="body" style="margin:0"><li><b>StatusBadge</b> mapea automáticamente color de punto + etiqueta en español. No armes el pill a mano.</li>
        <li><b>square</b> para códigos (<span class="mono">GV-A-00148</span>, <span class="mono">ESC-RECT</span>): mono, radio 5px.</li>
        <li>El estado nunca se comunica sólo por color: siempre punto + texto.</li></ul>
    </div></div>
  </div>

  <!-- Alert -->
  <h3 class="sub"><span class="tick"></span>Alert · AlertGroup</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="tt">Tonos</span><span class="hint">warn es el default — el motor nunca bloquea</span></div><div class="card-b stack">
      <div class="al"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4l9 16H3z"/><path d="M12 10v4M12 17h.01"/></svg><div><b>Inclinación fuera de rango cómodo</b>Revisá altura y avance. Es un aviso: no bloquea el cálculo.</div></div>
      <div class="al ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg><div><b>Inclinación recomendada</b>≈ 14 escalones · contrahuella 17,4 cm · 34°</div></div>
      <div class="al inf"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><div>Los cambios aplican sólo a nuevos cálculos.</div></div>
      <div class="al cr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/></svg><div>Falta la versión de parámetros para congelar el presupuesto.</div></div>
      <div class="al nt"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 11V7a5 5 0 0 1 10 0v4"/><rect x="5" y="11" width="14" height="9" rx="2"/></svg><div>No se incluyen en la salida comercial al cliente.</div></div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <dl class="spec"><dt>Alert · tone</dt><dd>warn | ok | info | crit | neutral</dd><dt>Alert · icon</dt><dd>ReactNode · null oculta</dd><dt>Alert · title</dt><dd>lead line en negrita</dd><dt>AlertGroup · title</dt><dd>“Alertas y recordatorios”</dd><dt>AlertGroup · items</dt><dd>ReactNode[]</dd></dl>
      <h4 class="mini">Comportamiento</h4>
      <ul class="body" style="margin:0"><li>El motor es <b>warn‑only y nunca bloquea</b>; por eso <span class="mono">warn</span> es el tono por defecto y el icono default es el triángulo.</li>
        <li><b>AlertGroup</b> es el panel titulado de recordatorios con bullets.</li>
        <li>Microcopy: icono info/lock + una sola oración.</li></ul>
      <h4 class="mini">Tokens</h4>
      <div class="mono" style="font-size:11.5px;color:var(--re-amber-ink);line-height:1.7">--re-warn-soft/-line · --re-ok-soft/-line · --re-info-soft/-line · --re-crit-soft/-line · --re-r</div>
    </div></div>
  </div>

  <!-- Card family -->
  <h3 class="sub"><span class="tick"></span>Card · CardHead · CardBody · SectionHead</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span><span class="tt">Encabezado del presupuesto</span><span class="hint">opcional</span><span class="r"><button class="b sm">Compactar</button></span></div>
      <div class="card-b">
        <p class="body" style="margin:0 0 var(--re-s4)">Esta card <b>es</b> el especímen: <span class="mono">CardHead</span> (tile de icono + título + hint + acciones) sobre un divisor, luego <span class="mono">CardBody</span>.</p>
        <div class="stage col"><div class="sech"><span class="ix">1</span><span class="tt">Producto</span></div>
          <div class="sech" style="margin-top:var(--re-s4)"><span class="ix">2</span><span class="tt">Dimensiones</span><span class="hn">decimales con coma</span></div>
          <div class="sech" style="margin-top:var(--re-s4)"><span class="ix">3</span><span class="tt">Opciones constructivas</span></div>
          <div class="stage-lab" style="margin:var(--re-s3) 0 0">SectionHead — pasos numerados dentro de calculadora / editor</div></div>
      </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <div class="scrollx"><table class="tb dense">
        <thead><tr><th>Export</th><th>Props</th></tr></thead>
        <tbody>
          <tr><td>Card</td><td class="mono">pad (20/24 interno) · flush (clip para tablas)</td></tr>
          <tr><td>CardHead</td><td class="mono">icon · title · hint · actions</td></tr>
          <tr><td>CardBody</td><td class="mono">tight (menos padding para tablas/listas)</td></tr>
          <tr><td>SectionHead</td><td class="mono">index · title · hint · tools</td></tr>
        </tbody></table></div>
      <h4 class="mini">Anatomía</h4>
      <p class="body" style="margin:0">Superficie grafito, borde <span class="mono">--re-line</span> de 1px, <span class="mono">--re-shadow-sm</span>, radio <span class="mono">--re-r-lg 13px</span>.
        <span class="mono">flush</span> cuando la card envuelve una tabla a sangre.</p>
      <h4 class="mini">Hacer / No hacer</h4>
      <ul class="body" style="margin:0"><li><b>Do</b> — usar <span class="mono">SectionHead</span> numerado para pasos de un flujo largo (calculadora, editor).</li>
        <li><b>Don't</b> — anidar cards dentro de cards; para agrupar dentro de una card usá <span class="mono">SectionHead</span> + divisores.</li></ul>
    </div></div>
  </div>

  <!-- StatTile -->
  <h3 class="sub"><span class="tick"></span>StatTile</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <div class="grid2" style="gap:var(--re-s3)">
        <div class="stt"><div class="k">Presupuestos del mes</div><div class="v">24</div><div class="s">+3 vs. mes anterior</div></div>
        <div class="stt"><div class="k">Aprobados</div><div class="v">9</div><div class="s">37,5% de conversión</div></div>
        <div class="stt acc"><div class="k">Monto cotizado</div><div class="v am">$2.480.000</div><div class="s">acumulado del mes</div></div>
        <div class="stt acc"><div class="k">Motor</div><div class="v" style="font-size:18px;display:flex;align-items:center;gap:9px"><span style="width:7px;height:7px;border-radius:50%;background:var(--re-ok-bright)"></span> v1.5</div><div class="s">QA al día</div></div>
      </div></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <dl class="spec"><dt>label</dt><dd>uppercase</dd><dt>value</dt><dd>tabular</dd><dt>sub</dt><dd>línea de apoyo</dd><dt>accent</dt><dd>variante charcoal</dd><dt>dot</dt><dd>punto de estado live</dd><dt>amberValue</dt><dd>cifra en ámbar</dd></dl>
      <h4 class="mini">Uso</h4>
      <ul class="body" style="margin:0"><li>Fila de KPI del dashboard y grillas de estado.</li>
        <li><b>accent</b> es para <b>un solo</b> tile destacado por fila: es el ancla charcoal.</li>
        <li><b>amberValue</b> con moderación: sólo cifras headline (respeta el racionamiento del ámbar).</li>
        <li><b>dot</b> se combina con <span class="mono">accent</span> para paneles “En vivo”.</li></ul>
    </div></div>
  </div>

  <!-- TechnicalPreviewCard -->
  <h3 class="sub"><span class="tick"></span>TechnicalPreviewCard · TechnicalRenderers</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <p class="body" style="margin:0 0 var(--re-s4)">El <b>único dibujo sancionado</b> del sistema: planos y elevaciones esquemáticos generados paramétricamente desde las dimensiones del propio presupuesto. Son diagramas medidos, con líneas de estructura grafito de 1px, overlays de dimensión token‑driven y ámbar reservado al indicio de entrada/baranda.</p>
      <h4 class="mini">Registro</h4>
      <p class="body" style="margin:0">Basado en registro: <span class="mono">family</span>/<span class="mono">variant</span> seleccionan un renderer. Escaleras trae cuatro: <span class="mono">recta</span> (elevación lateral), <span class="mono">L</span>, <span class="mono">U</span> y <span class="mono">caracol</span> (vistas en planta). Las familias desconocidas caen a un placeholder “sin vista técnica”.</p>
      <h4 class="mini">Uso permitido</h4>
      <ul class="body" style="margin:0"><li>Previews de calculadora y editor, filas de historial, documento al cliente.</li>
        <li><b>Nunca</b> dibujar arte de producto decorativo a mano.</li></ul>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <dl class="spec"><dt>family</dt><dd>Escaleras | otras → placeholder</dd><dt>variant</dt><dd>recta | L | U | caracol</dd><dt>dims</dt><dd>ancho · altura · avance · escalones · huella · contrahuella · diametro</dd><dt>config</dt><dd>string[] de chips</dd><dt>size</dt><dd>compact | standard | large</dd><dt>title / code</dt><dd>override · código mono</dd><dt>showDims</dt><dd>default true</dd><dt>live</dt><dd>indicador “En vivo”</dd></dl>
      <h4 class="mini">Densidades</h4>
      <ul class="body" style="margin:0"><li><b>compact</b> — head + dibujo + footer.</li>
        <li><b>standard</b> — + 4 dimensiones + chips.</li>
        <li><b>large</b> — stage más grande + 6 dimensiones.</li></ul>
      <h4 class="mini">Extender</h4>
      <p class="body" style="margin:0"><span class="mono">TechnicalRenderers.register(family, variant, def)</span> · <span class="mono">setFamilyLabel</span> · <span class="mono">has</span> · <span class="mono">variants</span> · <span class="mono">families</span>, todos encadenables. <span class="mono">escalones</span> se clampea a 4–16; un chip que matchee <span class="mono">/baranda|pasamano/</span> dibuja la baranda en <span class="mono">recta</span>.</p>
    </div></div>
  </div>
</section>

<!-- ============================================================ 03 -->
<section class="sec" id="s03">
  <div class="sec-h"><span class="num-badge">03</span><h2>Navegación y App Shell</h2></div>
  <p class="lede">El shell desktop es la capa primaria y está congelado desde V1: sidebar sticky de 248px + topbar sticky de 60px +
    main con scroll. Mobile no comprime ese shell: cambia de patrón.</p>

  <h3 class="sub"><span class="tick"></span>Desktop — AppShell</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="tt">Sidebar · LogoMark · NavGroup · NavItem</span><span class="hint">248px</span></div>
      <div class="card-b" style="background:var(--re-surface);padding:var(--re-s4)">
        <div style="display:flex;align-items:center;gap:12px;padding:6px 8px var(--re-s5)"><div class="lm">RE</div><div><div style="font-weight:600;font-size:14.5px;line-height:1.2">Presupuestador</div><div style="font-size:11px;color:var(--re-ink-4);font-weight:500">RE · v1.0</div></div></div>
        <div style="font-size:10px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--re-ink-4);padding:0 12px 8px">Operación</div>
        <div class="navi on"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2.5"/><path d="M8 7h8M8 11h3" stroke-linecap="round"/><rect x="13.5" y="11" width="3" height="6" rx="1"/></svg> Calculadora</div>
        <div class="navi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg> Historial</div>
        <div style="font-size:10px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--re-ink-4);padding:var(--re-s4) 12px 8px">Configuración</div>
        <div class="navi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 2.5l8 4.2v10.6L12 21.5 4 17.3V6.7z"/><path d="M4 6.7l8 4.3 8-4.3M12 11v10.5"/></svg> Catálogos</div>
        <div class="navi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M4 7h10M18 7h2M4 17h2M10 17h10"/><circle cx="16" cy="7" r="2.3"/><circle cx="8" cy="17" r="2.3"/></svg> Parámetros</div>
        <div class="navi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4" stroke-linecap="round"/></svg> QA</div>
      </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Topbar · Breadcrumbs · UserChip</h4>
      <div class="stage col" style="gap:var(--re-s4)">
        <div style="display:flex;align-items:center;gap:14px"><div class="crumb"><span>Operación</span><span class="sp">/</span><b>Calculadora</b><span class="mono" style="font-size:12px;color:var(--re-ink-4)">(sin guardar)</span></div></div>
        <div style="display:flex;gap:10px;align-items:center"><span class="ib" title="Buscar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></span>
          <span class="uchip"><span class="av">RE</span><span class="w"><b>Ramiro</b><span>Presupuestador</span></span></span></div>
      </div>
      <h4 class="mini">API</h4>
      <div class="scrollx"><table class="tb dense">
        <thead><tr><th>Export</th><th>Props</th></tr></thead>
        <tbody>
          <tr><td>AppShell</td><td class="mono">sidebar · topbar · children</td></tr>
          <tr><td>LogoMark</td><td class="mono">mark ("RE") · name · tagline</td></tr>
          <tr><td>Sidebar</td><td class="mono">logo · footer · children</td></tr>
          <tr><td>NavGroup</td><td class="mono">label · first</td></tr>
          <tr><td>NavItem</td><td class="mono">icon · label · active · as</td></tr>
          <tr><td>Topbar</td><td class="mono">breadcrumbs · actions</td></tr>
          <tr><td>Breadcrumbs</td><td class="mono">items: {label, href}[]</td></tr>
          <tr><td>UserChip</td><td class="mono">name · role · initials</td></tr>
        </tbody></table></div>
      <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑02</span><p><span class="mono">LogoMark</span> tiene <b>tagline por defecto <span class="mono">'RE · v1.0'</span></b>, pero las pantallas del kit y los mockups muestran <span class="mono">“REstimator · motor v1.5”</span>. Hay que unificar el default o pasarlo siempre explícito.</p></div>
    </div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Mobile — navegación inferior y header compacto</h3>
  <p class="body">Mobile es <b>diseñada, no adaptada</b>: el sidebar no se “oculta”, se reemplaza por una bottom tab bar de 5 familias.
    Los breadcrumbs desaparecen (los sustituye título + back) y el branding aparece una sola vez por header.</p>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Elemento</th><th>Medida</th><th>Comportamiento</th><th>Estado en el DS</th></tr></thead>
    <tbody>
      <tr><td>BottomTabBar</td><td class="mono">h 64 + safe-area</td><td>fixed bottom. 5 tabs: Inicio · Calc · Historial · Catálogos · Parám. Iconos 21, label 10. Activo <span class="mono">--re-amber-ink</span>.</td><td><span class="tag new">Nuevo</span></td></tr>
      <tr><td>MobileHeader</td><td class="mono">h ~62</td><td>sticky top. Back + título (Calculadora) o marca + título (Inicio / Historial). Sin breadcrumbs, sin sub‑copy.</td><td><span class="tag new">Nuevo</span></td></tr>
      <tr><td>BottomActionBar</td><td class="mono">h 62 · bottom 64</td><td>fixed sobre la tab bar. Sólo Calculadora: Guardar (primary) · Estimación · Presupuesto.</td><td><span class="tag new">Nuevo</span></td></tr>
      <tr><td>AppShell (modo mobile)</td><td class="mono">—</td><td>Mismo árbol de rutas, tres modos de navegación (sidebar / tabs / bottom bar).</td><td><span class="tag ext">Extendido</span></td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Comportamiento sticky / fijo</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Capa (de abajo hacia arriba)</th><th>Alto</th><th>Posición</th><th>z-index</th><th>Dónde</th></tr></thead>
    <tbody>
      <tr><td>Bottom tab bar</td><td class="mono">64</td><td class="mono">fixed bottom 0</td><td class="mono">40</td><td>Mobile</td></tr>
      <tr><td>Action bar</td><td class="mono">62</td><td class="mono">fixed bottom 64</td><td class="mono">35</td><td>Mobile · Calculadora</td></tr>
      <tr><td>Header compacto</td><td class="mono">~62</td><td class="mono">sticky top 0</td><td class="mono">30</td><td>Mobile</td></tr>
      <tr><td>Resumen recomendado</td><td class="mono">auto ~108</td><td class="mono">sticky bajo header</td><td class="mono">28</td><td>Mobile · Calculadora</td></tr>
      <tr><td>Bottom sheet (desglose)</td><td class="mono">máx 78%</td><td class="mono">overlay + scrim</td><td class="mono">60</td><td>Mobile · Tablet</td></tr>
      <tr><td>Sidebar / Topbar</td><td class="mono">248 / 60</td><td class="mono">sticky</td><td class="mono">—</td><td>Desktop</td></tr>
      <tr><td>Panel de resultado</td><td class="mono">380 col</td><td class="mono">sticky lateral</td><td class="mono">—</td><td>Desktop · Calculadora</td></tr>
      <tr><td>Save bar</td><td class="mono">auto</td><td class="mono">fixed bottom · shadow-bar</td><td class="mono">—</td><td>Desktop · Editor / Parámetros</td></tr>
    </tbody>
  </table></div>
</section>

<!-- ============================================================ 04 -->
<section class="sec" id="s04">
  <div class="sec-h"><span class="num-badge">04</span><h2>Formularios y entrada de datos</h2></div>
  <p class="lede">Los formularios son el corazón del producto: la calculadora es un formulario largo con resultado en vivo.
    La entrada de datos privilegia velocidad de tipeo y lectura tabular por encima de adornos.</p>

  <h3 class="sub"><span class="tick"></span>Inventario de controles</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Control</th><th>Export</th><th>Variantes / tamaños</th><th>Estados definidos</th></tr></thead>
    <tbody>
      <tr><td>Text input</td><td class="mono">Input</td><td>prefix · suffix · leadIcon · num</td><td>default · focus · disabled · placeholder</td></tr>
      <tr><td>Select</td><td class="mono">Select</td><td>native + chevron propio</td><td>default · focus · disabled</td></tr>
      <tr><td>Textarea</td><td class="mono">Textarea</td><td>resize vertical</td><td>default · focus · disabled</td></tr>
      <tr><td>Search</td><td class="mono">Input + leadIcon</td><td>patrón, no componente aparte</td><td>default · focus · con valor</td></tr>
      <tr><td>Toggle</td><td class="mono">Toggle</td><td>md 38 · sm 32 · accent</td><td>on · off · disabled</td></tr>
      <tr><td>Segmented</td><td class="mono">SegmentedControl</td><td>2–3 opciones, con/sin icono</td><td>selected · unselected</td></tr>
      <tr><td>Wrapper de campo</td><td class="mono">Field</td><td>label · optional · note</td><td>—</td></tr>
      <tr><td>Checkbox · Radio</td><td class="mono">—</td><td colspan="2"><b>No existen como componentes.</b> El producto resuelve la elección con <span class="mono">Select</span>, <span class="mono">SegmentedControl</span> y <span class="mono">Toggle</span>. Ver <a href="#needs-review">NR‑13</a>.</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Estados de control</h3>
  <div class="card"><div class="card-b">
    <div class="grid3">
      <div><div class="stage-lab">Default</div><div class="ctl"><span class="val num">2,43</span><span class="sfx">m</span></div></div>
      <div><div class="stage-lab">Focus — borde ámbar + ring 3px</div><div class="ctl foc"><span class="val num">2,43</span><span class="sfx">m</span></div></div>
      <div><div class="stage-lab">Disabled — opacidad .45</div><div class="ctl dis"><span class="val num">2,43</span><span class="sfx">m</span></div></div>
      <div><div class="stage-lab">Placeholder — ink-4</div><div class="ctl ph"><span class="val">0,00</span><span class="sfx">m</span></div></div>
      <div><div class="stage-lab">Con prefijo monetario</div><div class="ctl"><span class="pfx">$</span><span class="val num r">14.360</span></div></div>
      <div><div class="stage-lab">Select</div><div class="ctl sel">Montevideo <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></div></div>
    </div>
    <h4 class="mini">Hover · focus · pressed · disabled — reglas globales</h4>
    <div class="scrollx"><table class="tb dense">
      <thead><tr><th>Estado</th><th>Tratamiento</th></tr></thead>
      <tbody>
        <tr><td>Hover</td><td>fill a <span class="mono">--re-surface-2</span> + borde a <span class="mono">--re-line-strong</span> (o amber‑darken en CTAs ámbar)</td></tr>
        <tr><td>Focus</td><td>ring ámbar de 3px (<span class="mono">--re-ring</span>) + borde ámbar</td></tr>
        <tr><td>Pressed / activo</td><td>toggles y páginas activas <b>voltean a ámbar</b></td></tr>
        <tr><td>Disabled</td><td>opacidad <span class="mono">.45</span>, sin eventos</td></tr>
        <tr><td>Transición</td><td><span class="mono">.12–.15s ease</span> en background / border. Sin shrink ni scale.</td></tr>
      </tbody></table></div>
  </div></div>

  <h3 class="sub"><span class="tick"></span>Validación · errores · éxito</h3>
  <p class="body">El motor de cálculo es <b>warn‑only y nunca bloquea</b>. Esa decisión de producto define toda la validación:
    los avisos se agrupan como <span class="mono">Alert</span> junto al resultado, no como errores de campo que impidan avanzar.</p>
  <div class="grid2">
    <div class="card"><div class="card-b stack">
      <div class="stage-lab" style="margin:0">Aviso — no bloquea</div>
      <div class="al"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4l9 16H3z"/><path d="M12 10v4M12 17h.01"/></svg><div><b>Avance fuera del rango cómodo</b>Podés continuar: el cálculo se realiza igual.</div></div>
      <div class="stage-lab" style="margin:var(--re-s3) 0 0">Éxito / confirmación calma</div>
      <div class="al ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg><div>Guardado en historial · <span class="mono">GV-A-00159</span></div></div>
      <div class="stage-lab" style="margin:var(--re-s3) 0 0">Nota de campo — icono + una oración</div>
      <div class="note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 11V7a5 5 0 0 1 10 0v4"/><rect x="5" y="11" width="14" height="9" rx="2"/></svg> No se incluyen en la salida comercial al cliente.</div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Patrón de data entry — formularios largos</h4>
      <ul class="body" style="margin:0"><li><b>Pasos numerados</b> con <span class="mono">SectionHead</span>: Producto → Dimensiones → Opciones constructivas → Terminación / colocación / zona.</li>
        <li><b>Una card por paso</b>; grilla de 2–3 columnas en desktop, 1–2 en tablet, 1 en mobile.</li>
        <li><b>Unidades siempre visibles</b> como <span class="mono">suffix</span>, nunca sólo en el label.</li>
        <li><b>Coma decimal</b> en medidas — el hint del paso lo recuerda (“decimales con coma”).</li>
        <li><b>Recálculo en vivo</b>: no hay botón “calcular”; el panel de resultado muestra el punto “En vivo”.</li>
        <li><b>Campos opcionales</b> marcados con hint inline <span class="mono">optional</span>, no con asterisco en los obligatorios.</li></ul>
      <h4 class="mini">Save bar</h4>
      <p class="body" style="margin:0">Editor y Parámetros usan una barra fija inferior con recuento de cambios (“1 cambio de calibración local”)
        + <span class="mono">Restaurar por defecto</span> + <span class="mono">Guardar cambios</span>. Sombra hacia arriba (<span class="mono">--re-shadow-bar</span>).</p>
    </div></div>
  </div>
</section>

<!-- ============================================================ 05 -->
<section class="sec" id="s05">
  <div class="sec-h"><span class="num-badge">05</span><h2>Visualización de datos</h2></div>
  <p class="lede">Historial es la superficie de datos principal: tabla densa, filtros que funcionan, estado como punto de color
    sobre pill neutra y acciones de fila reveladas en hover.</p>

  <h3 class="sub"><span class="tick"></span>Table · TableToolbar · RowActions · Pagination</h3>
  <div class="card" style="overflow:hidden">
    <div class="tbar"><span class="tt">Presupuestos guardados</span><span class="ct">58 resultados</span>
      <span class="r"><button class="b sm">Exportar</button><button class="b sm">Columnas</button></span></div>
    <div class="scrollx"><table class="dt-tb">
      <thead><tr><th>Código</th><th>Cliente / obra</th><th>Familia</th><th>Estado</th><th class="r" style="text-align:right">Monto</th><th></th></tr></thead>
      <tbody>
        <tr><td class="mono" style="font-size:11.5px;color:var(--re-ink-3)">GV-A-00159</td>
          <td><div class="strong">Baranda de balcón</div><div class="sub">Pedro Núñez · 15/06/2026</div></td>
          <td>Barandas</td><td><span class="st"><span class="d d-draft"></span> Borrador</span></td>
          <td class="r strong">$96.000</td>
          <td><div style="display:flex;gap:4px;justify-content:flex-end"><span class="ib bare" title="Editar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9a2.1 2.1 0 0 0-3-3L5 17z"/></svg></span><span class="ib bare" title="Duplicar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg></span></div></td></tr>
        <tr><td class="mono" style="font-size:11.5px;color:var(--re-ink-3)">EST-2026-014</td>
          <td><div class="strong">Baranda de balcón — inicial</div><div class="sub">Pedro Núñez · 13/06/2026</div></td>
          <td>Barandas</td><td><span class="st"><span class="d d-sent"></span> Enviado</span></td>
          <td class="r strong">$96.000</td><td></td></tr>
        <tr><td class="mono" style="font-size:11.5px;color:var(--re-ink-3)">EST-2026-013</td>
          <td><div class="strong">Escalera recta — 1er contacto</div><div class="sub">Valentina Cruz · 12/06/2026</div></td>
          <td>Escaleras</td><td><span class="st"><span class="d d-appr"></span> Aprobado</span></td>
          <td class="r strong">$175.000</td><td></td></tr>
      </tbody></table></div>
    <div style="display:flex;align-items:center;gap:14px;padding:13px 15px;border-top:1px solid var(--re-line)">
      <span style="font-size:12.5px;color:var(--re-ink-4)">Mostrando 1–12 de 58</span>
      <span class="pgn" style="margin-left:auto"><span class="pg">‹</span><span class="pg on">1</span><span class="pg">2</span><span class="pg">3</span><span class="pg dots">…</span><span class="pg">5</span><span class="pg">›</span></span></div>
  </div>
  <div class="grid2" style="margin-top:var(--re-s4)">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <div class="scrollx"><table class="tb dense">
        <thead><tr><th>Export</th><th>Props</th></tr></thead>
        <tbody>
          <tr><td>Table</td><td class="mono">children (thead/tbody propios)</td></tr>
          <tr><td>TableToolbar</td><td class="mono">title · count · actions</td></tr>
          <tr><td>RowActions</td><td class="mono">children (IconButtons)</td></tr>
          <tr><td>Pagination</td><td class="mono">showing · pages · current · onPage</td></tr>
        </tbody></table></div>
      <h4 class="mini">Helpers</h4>
      <ul class="body" style="margin:0"><li>Clases de header: <span class="mono">is-right</span> · <span class="mono">is-center</span> · <span class="mono">is-sortable</span>.</li>
        <li>Clases de celda: <span class="mono">re-cell-strong</span> (primaria) · <span class="mono">re-cell-sub</span> (línea secundaria).</li>
        <li><b>Envolver en <span class="mono">&lt;Card flush&gt;</span></b>: la card recorta el desborde de la tabla.</li>
        <li><span class="mono">RowActions</span> se revela en hover de fila.</li></ul></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Montos y cifras</h4>
      <ul class="body" style="margin:0 0 var(--re-s4)"><li>Columna de monto alineada a la derecha, <span class="mono">tabular-nums</span>, peso 600.</li>
        <li>Código en mono a 11,5px, color <span class="mono">--re-ink-3</span>.</li>
        <li>Fila de dos líneas: primaria (<span class="mono">re-cell-strong</span>) + secundaria cliente · fecha.</li></ul>
      <h4 class="mini">Empty state — el patrón real del producto</h4>
      <div class="al nt"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><div>Sólo se listan presupuestos guardados. No hay borrado físico: el estado se cambia de forma suave.</div></div>
      <p class="body" style="margin:var(--re-s3) 0 0;font-size:12.5px">Los estados vacíos <b>explican la regla</b>, no sólo dicen “sin resultados”.</p>
      <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑07</span><p>No hay componente de <b>loading / skeleton</b> en el sistema, y los empty states existen sólo como copy dentro de pantallas, no como componente reutilizable.</p></div>
    </div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Cards, tiles y badges como display</h3>
  <p class="body">Ya documentados en §02: <span class="mono">Card</span> family para contenedores, <span class="mono">StatTile</span> para KPI y
    grillas de estado, <span class="mono">Badge</span>/<span class="mono">StatusBadge</span> para etiquetas, códigos y ciclo de vida,
    <span class="mono">TechnicalPreviewCard</span> para el dibujo esquemático del producto.</p>
</section>

<!-- ============================================================ 06 -->
<section class="sec" id="s06">
  <div class="sec-h"><span class="num-badge">06</span><h2>Patrones de producto</h2></div>
  <p class="lede">Patrones propios de REstimator. No son componentes: son composiciones con significado de negocio, y varias
    están gobernadas por las reglas no negociables del §00.</p>

  <h3 class="sub"><span class="tick"></span>Valor recomendado y rangos</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <div style="background:var(--re-charcoal);border:1px solid var(--re-charcoal-line);border-radius:var(--re-r);padding:var(--re-s5);text-align:center">
        <div style="display:flex;align-items:center;justify-content:center;gap:7px;font-size:10.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--re-on-dark-3)">
          <svg style="width:13px;height:13px;color:var(--re-amber)" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18.9 6.1 22l1.2-6.5L2.5 9.4l6.6-.9z"/></svg> Recomendado</div>
        <div class="num" style="font-size:40px;font-weight:700;letter-spacing:-.02em;line-height:1.05;margin:6px 0 2px;color:var(--re-amber-bright)">$135.599</div>
        <div style="font-size:11.5px;color:var(--re-on-dark-3)">valor de fabricación · + IVA</div></div>
      <div style="margin-top:var(--re-s5)">
        <div style="display:flex;justify-content:space-between;align-items:flex-end">
          <div><div style="font-size:10px;letter-spacing:.04em;text-transform:uppercase;color:var(--re-ink-4)">Mínimo</div><div class="num" style="font-size:16px;font-weight:600">$127.463</div></div>
          <div style="text-align:right"><div style="font-size:10px;letter-spacing:.04em;text-transform:uppercase;color:var(--re-ink-4)">Máximo</div><div class="num" style="font-size:16px;font-weight:600">$143.735</div></div></div>
        <div style="position:relative;height:6px;border-radius:4px;background:var(--re-surface-3);margin-top:12px">
          <div style="position:absolute;left:8%;right:8%;top:0;bottom:0;border-radius:4px;background:var(--re-line-strong)"></div>
          <div style="position:absolute;left:46%;top:-3px;width:3px;height:12px;border-radius:2px;background:var(--re-amber)"></div></div>
      </div></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Reglas del patrón</h4>
      <ul class="body" style="margin:0"><li>El <b>recomendado</b> es la única cifra ámbar de la pantalla y vive sobre el charcoal más profundo.</li>
        <li>Siempre se muestra <b>con su rango</b> (mín / máx) y un pin en el track: el número nunca aparece solo.</li>
        <li>El precio sale de <span class="mono">Fab_min/Fab_max</span> calibrados, <b>no</b> de jornales × valor.</li>
        <li>Siempre etiquetado <b>“valor de fabricación · + IVA”</b>: nunca se insinúa que es precio final.</li>
        <li>El panel lleva el punto “En vivo”: recálculo continuo, sin botón calcular.</li></ul>
      <h4 class="mini">Jornales — control, no precio</h4>
      <div class="stage tight" style="gap:8px">
        <div style="flex:1;border:1px solid var(--re-line);border-radius:var(--re-r-sm);padding:9px 6px;text-align:center;background:var(--re-surface)"><div class="num" style="font-size:18px;font-weight:700">5,3</div><div style="font-size:9.5px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--re-ink-4);margin-top:2px">Mín</div></div>
        <div style="flex:1;border:1px solid var(--re-amber-line);border-radius:var(--re-r-sm);padding:9px 6px;text-align:center;background:var(--re-amber-soft)"><div class="num" style="font-size:18px;font-weight:700;color:var(--re-amber-ink)">6,5</div><div style="font-size:9.5px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--re-ink-4);margin-top:2px">Rec.</div></div>
        <div style="flex:1;border:1px solid var(--re-line);border-radius:var(--re-r-sm);padding:9px 6px;text-align:center;background:var(--re-surface)"><div class="num" style="font-size:18px;font-weight:700">7,7</div><div style="font-size:9.5px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--re-ink-4);margin-top:2px">Máx</div></div>
      </div>
      <div class="note" style="margin-top:var(--re-s3)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg> Verificación de carga de trabajo — <b style="color:var(--re-ink-3)">no define el precio</b>. Copy literal del producto.</div>
    </div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Pasos del resultado</h3>
  <p class="body">El panel de resultado siempre presenta los mismos pasos numerados, en el mismo orden, en los tres breakpoints:</p>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Paso</th><th>Bloque</th><th>Qué comunica</th></tr></thead>
    <tbody>
      <tr><td class="mono">1</td><td>Fabricación estimada</td><td>Rango mín–máx + track con pin del recomendado.</td></tr>
      <tr><td class="mono">—</td><td>Recomendado</td><td>Bloque charcoal con la cifra ámbar. El ancla visual.</td></tr>
      <tr><td class="mono">2</td><td>Jornales · mano de obra</td><td>Mín / Rec. / Máx como control de carga.</td></tr>
      <tr><td class="mono">3</td><td>Colocación (sugerida)</td><td>Rango + base de cálculo (~10% de fabricación) + zona.</td></tr>
      <tr><td class="mono">4</td><td>Subtotal</td><td>Fabricación + colocación → subtotal, con “+ IVA” explícito.</td></tr>
      <tr><td class="mono">5</td><td>Override</td><td>Ajuste manual del precio final, siempre visible como campo aparte.</td></tr>
      <tr><td class="mono">6</td><td>Alertas</td><td><span class="mono">AlertGroup</span> warn‑only al pie del panel.</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Creación y edición de presupuestos</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Flujo de la calculadora</h4>
      <ul class="body" style="margin:0"><li><b>1 Producto</b> — familia → subcategoría (taxonomía de 10 rubros).</li>
        <li><b>2 Dimensiones</b> — medidas en metros, coma decimal.</li>
        <li><b>3 Opciones constructivas</b> — estructura, contrahuella, antideslizante, baranda.</li>
        <li><b>4 Terminación · colocación · zona</b> — DTM, tipo de colocación, zona geográfica.</li>
        <li>Encabezado del presupuesto <b>opcional y compactable</b>: no roba protagonismo al formulario.</li></ul>
      <h4 class="mini">Clasificación</h4>
      <p class="body" style="margin:0">Familias → subcategorías es la columna vertebral. <span class="mono">TaxonomyIcon</span> acompaña
        cada rubro en rails, dropdowns, cards de catálogo y chips.</p>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Ciclo de vida y acciones</h4>
      <ul class="body" style="margin:0"><li><b>Estados</b>: Borrador → Enviado → Aprobado / Rechazado / Cancelado.</li>
        <li><b>Sin borrado físico</b>: el cambio de estado es suave y reversible.</li>
        <li><b>Duplicar</b> es de primera clase: retomar una cotización previa es el camino más común.</li>
        <li><b>Freeze</b>: al publicarse, el presupuesto congela su versión de parámetros y queda inmutable.</li>
        <li><b>Override</b>: el ajuste manual queda registrado, no reemplaza silenciosamente al recomendado.</li></ul>
      <h4 class="mini">Filtros y búsqueda</h4>
      <ul class="body" style="margin:0"><li>Búsqueda por N°, cliente u obra en un único input con <span class="mono">leadIcon</span>.</li>
        <li>Filtros por tipo/estado en una línea; en mobile con scroll horizontal y activo ámbar sólido.</li>
        <li>Contador de resultados siempre visible (“58 resultados”).</li></ul>
    </div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>El documento al cliente — la única superficie clara</h3>
  <p class="body">El resumen al cliente se renderiza como una <b>hoja de papel blanca real</b> (es el artefacto imprimible / WhatsApp / PDF)
    apoyada sobre el banco de trabajo oscuro; su fila de total es charcoal con el total en ámbar. Todo lo demás del producto es oscuro.
    Modos <b>estimación</b> y <b>formal</b> vía <span class="mono">SegmentedControl</span>. Voz en tercera persona formal
    (“Le compartimos una estimación…”), nunca voseo. <b>Nunca serializa rangos, jornales, márgenes ni calibración</b> (regla 5).</p>

  <h3 class="sub"><span class="tick"></span>Voz y microcopy</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Regla</th><th>Aplicación</th></tr></thead>
    <tbody>
      <tr><td>Idioma</td><td>Español de Uruguay, voseo rioplatense en imperativo: <i>“Agregá notas…”, “buscá, filtrá y retomá”</i>. Nunca “añade/busca”.</td></tr>
      <tr><td>Tono</td><td>Llano, operativo, confiable. Declarativas cortas. Explica por qué existe un número sin titubear.</td></tr>
      <tr><td>Casing</td><td>Sentence case en todo lo legible. UPPERCASE + tracking sólo en eyebrows chicos.</td></tr>
      <tr><td>Persona</td><td>La app te habla a vos (el presupuestador). El documento al cliente es formal en tercera persona.</td></tr>
      <tr><td>Emoji</td><td>Nunca. Un “✓” en confirmaciones transitorias es el único carácter decorativo.</td></tr>
      <tr><td>Notas de ayuda</td><td>Icono info/lock + una sola oración.</td></tr>
    </tbody>
  </table></div>
</section>

<!-- ============================================================ 07 -->
<section class="sec" id="s07">
  <div class="sec-h"><span class="num-badge">07</span><h2>Sistema responsive</h2></div>
  <p class="lede">El mismo sistema en tres densidades. Mobile <b>no es otro producto</b>: son los mismos tokens, la misma taxonomía, los mismos pasos de resultado y el mismo vocabulario, con el patrón de navegación y de resultado que corresponde al espacio disponible.</p>

  <h3 class="sub"><span class="tick"></span>Transformación por aspecto</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Aspecto</th><th>Mobile · 390</th><th>Tablet · 834</th><th>Desktop · 1440</th></tr></thead>
    <tbody>
      <tr><td>Navegación</td><td>Bottom tab bar (5 familias) + título contextual</td><td>Tabs superiores de familia bajo el header</td><td>Sidebar izquierdo fijo 248px</td></tr>
      <tr><td>Branding</td><td>Monograma RE en header (avatar = usuario, no marca)</td><td>Monograma + nombre compactos</td><td>Logo + nombre + versión de motor en sidebar</td></tr>
      <tr><td>Breadcrumbs</td><td>Ocultos — los reemplaza título + back</td><td>Compactos, opcionales</td><td>Completos en el topbar</td></tr>
      <tr><td>Header</td><td>Sticky compacto: back + título; sin sub‑copy</td><td>Sticky: título + acción</td><td>Topbar + page‑head</td></tr>
      <tr><td>Resultado</td><td>Resumen pinneado arriba + sheet a demanda</td><td>Bottom sheet colapsable con peek</td><td>Panel lateral sticky 380px</td></tr>
      <tr><td>Acciones primarias</td><td>Barra fija sobre el tab bar</td><td>En el peek del sheet + dentro del panel</td><td>Dentro del panel de resultado</td></tr>
      <tr><td>Sticky</td><td>Header + resumen + barra de acciones</td><td>Header + peek del sheet</td><td>Panel de resultado + save bar</td></tr>
      <tr><td>Formularios</td><td>1 campo por fila; secciones numeradas con anclas</td><td>Grilla de 2 columnas con respiración</td><td>Grilla de 2–3 columnas</td></tr>
      <tr><td>Densidad</td><td>Cards apiladas, gap 14, padding 16</td><td>Intermedia, padding 22</td><td>Densa, padding 20/24, gap 16–20</td></tr>
      <tr><td>Tablas / listas</td><td>Fila = card; tap abre; secundarias en ⋯</td><td>Tabla reducida o cards según ancho</td><td>Tabla completa + RowActions en hover</td></tr>
      <tr><td>Control</td><td class="mono">42px</td><td class="mono">42px</td><td class="mono">40px (--re-control-h)</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Patrón de resultado — decisión por breakpoint</h3>
  <p class="body">Se evaluaron cuatro opciones (lateral sticky · superior resumido · bottom sheet · híbrida) y se eligió la
    <b>composición híbrida</b>: ninguna opción única gana en los tres tamaños, así que el patrón se adapta al espacio.
    El recomendado queda <b>siempre glanceable</b>; el desglose está <b>a un tap</b>.</p>
  <div class="grid3">
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Desktop ≥ 1200</h4><div style="font-size:15px;font-weight:600;margin-bottom:6px">Panel lateral</div>
      <p class="body" style="margin:0;font-size:13px">Espacio horizontal de sobra: el desglose completo queda sticky junto al formulario, como ya venía funcionando.</p></div></div>
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Tablet 834</h4><div style="font-size:15px;font-weight:600;margin-bottom:6px">Bottom sheet</div>
      <p class="body" style="margin:0;font-size:13px">Formulario a ancho cómodo; barra‑peek inferior con recomendado + rango + CTA. Se expande sin tapar campos.</p></div></div>
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Mobile 390</h4><div style="font-size:15px;font-weight:600;margin-bottom:6px">Resumen arriba + sheet</div>
      <p class="body" style="margin:0;font-size:13px">Header de resultado pinneado (recomendado + rango + tamaño); “Ver desglose” abre el sheet. Acciones en barra fija.</p></div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Constantes mobile v1</h3>
  <div class="grid2">
    <div class="card"><div class="card-b"><dl class="spec">
      <dt>Viewport base</dt><dd class="am">390 × 844</dd><dt>Rango fluido</dt><dd>360–430</dd><dt>Grilla</dt><dd>4px (--re-s*)</dd><dt>Padding de pantalla</dt><dd>16 (s4)</dd><dt>Gap entre cards</dt><dd>14</dd><dt>Card</dt><dd>r13 · pad 16</dd><dt>Control</dt><dd>h42 · r7</dd><dt>Touch target</dt><dd class="am">≥ 44</dd><dt>Safe area</dt><dd class="mono">env(safe-area-inset-bottom)</dd></dl></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Reglas de adaptación de componentes</h4>
      <ul class="body" style="margin:0"><li><b>Nada se “oculta” solamente.</b> Si un elemento no cabe, se reemplaza por su equivalente táctil.</li>
        <li><b>Acción dominante por tap</b>: la fila entera abre; las secundarias van a un overflow ⋯.</li>
        <li><b>Chips en una línea</b> con scroll horizontal; el activo es ámbar sólido, nunca wrap a dos líneas.</li>
        <li><b>Sin sub‑copy en headers</b> táctiles: título + back y nada más.</li>
        <li><b>Una sola marca por header</b>: monograma = app, avatar = usuario.</li>
        <li>El bottom sheet <b>sólo</b> transporta el desglose: nunca se convierte en navegación.</li></ul>
      <p class="body" style="margin:var(--re-s4) 0 0;font-size:12.5px">Spec completa: <span class="art-name">Mobile v1 — Spec de implementación</span></p>
    </div></div>
  </div>
  <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑12</span><p>La capa <b>tablet</b> está documentada y especificada, pero <b>no existe artefacto de pantalla tablet</b> en <span class="mono">ui_kits/</span>. Los valores tablet provienen de los mockups de refinamiento, no de una pantalla del kit.</p></div>
</section>

<!-- ============================================================ 08 -->
<section class="sec" id="s08">
  <div class="sec-h"><span class="num-badge">08</span><h2>Ejemplos de pantalla</h2><span class="cnt">5 desktop · 3 mobile</span></div>
  <p class="lede">Las pantallas del producto construidas con el sistema. Las cinco de desktop son las del UI kit; las tres de mobile vienen de la spec de implementación Mobile v1. Cada una está capturada en alta resolución y se puede abrir para recorrerla.</p>

  <h3 class="sub"><span class="tick"></span>Desktop — UI kit</h3>
  <div class="shots">
    <figure class="shot">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'calculator.webp' ); ?>"
        data-es-screen-w="2880" data-es-screen-h="3224" data-es-screen-cssw="1440"
        data-es-screen-name="Calculadora"
        data-es-screen-meta="Calculator.html"
        aria-label="Abrir pantalla: Calculadora">
        <span class="cap">
          <span class="nm">Calculadora</span><span class="fl">Calculator.html</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'calculator-preview.webp' ); ?>"
               alt="Calculadora — Presupuestador RE"
               width="1520" height="1702" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
    <figure class="shot">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'history.webp' ); ?>"
        data-es-screen-w="2880" data-es-screen-h="3086" data-es-screen-cssw="1440"
        data-es-screen-name="Historial"
        data-es-screen-meta="History.html"
        aria-label="Abrir pantalla: Historial">
        <span class="cap">
          <span class="nm">Historial</span><span class="fl">History.html</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'history-preview.webp' ); ?>"
               alt="Historial — Presupuestador RE"
               width="1520" height="1629" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
    <figure class="shot">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'product-editor.webp' ); ?>"
        data-es-screen-w="2880" data-es-screen-h="4592" data-es-screen-cssw="1440"
        data-es-screen-name="Editor de producto"
        data-es-screen-meta="ProductEditor.html"
        aria-label="Abrir pantalla: Editor de producto">
        <span class="cap">
          <span class="nm">Editor de producto</span><span class="fl">ProductEditor.html</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'product-editor-preview.webp' ); ?>"
               alt="Editor de producto — Presupuestador RE"
               width="1520" height="2424" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
    <figure class="shot">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'client-summary.webp' ); ?>"
        data-es-screen-w="2880" data-es-screen-h="4098" data-es-screen-cssw="1440"
        data-es-screen-name="Resumen cliente"
        data-es-screen-meta="ClientSummary.html"
        aria-label="Abrir pantalla: Resumen cliente">
        <span class="cap">
          <span class="nm">Resumen cliente</span><span class="fl">ClientSummary.html</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'client-summary-preview.webp' ); ?>"
               alt="Resumen cliente — Presupuestador RE"
               width="1520" height="2163" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
    <figure class="shot">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'catalogs.webp' ); ?>"
        data-es-screen-w="2880" data-es-screen-h="3274" data-es-screen-cssw="1440"
        data-es-screen-name="Catálogos"
        data-es-screen-meta="Catalogs.html"
        aria-label="Abrir pantalla: Catálogos">
        <span class="cap">
          <span class="nm">Catálogos</span><span class="fl">Catalogs.html</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'catalogs-preview.webp' ); ?>"
               alt="Catálogos — Presupuestador RE"
               width="1520" height="1728" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
  </div>
  <p class="body" style="margin-top:var(--re-s4);font-size:12.5px;color:var(--re-ink-4)">Capturas de las pantallas reales del kit. Hacé click en cualquiera para abrirla y recorrerla completa.</p>

  <h3 class="sub"><span class="tick"></span>Mobile v1 — pantallas finales</h3>
  <p class="body">Calculadora, Inicio e Historial quedaron congeladas para implementación, con medidas, spacing, componentes y comportamiento sticky por pantalla. Se documentan en su propio archivo para no duplicarlas acá.</p>
  <div class="shots shots--mobile">
    <figure class="shot shot--mobile">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'mobile-calculator.webp' ); ?>"
        data-es-screen-w="1176" data-es-screen-h="2349" data-es-screen-cssw="392"
        data-es-screen-name="Calculadora"
        data-es-screen-meta="390 · por defecto — formulario + resumen fijo"
        aria-label="Abrir pantalla: Calculadora">
        <span class="cap">
          <span class="nm">Calculadora</span><span class="fl">390 · por defecto — formulario + resumen fijo</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'mobile-calculator.webp' ); ?>"
               alt="Calculadora — Presupuestador RE"
               width="1176" height="2349" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
    <figure class="shot shot--mobile">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'mobile-home.webp' ); ?>"
        data-es-screen-w="1176" data-es-screen-h="2349" data-es-screen-cssw="392"
        data-es-screen-name="Inicio"
        data-es-screen-meta="390 · por defecto"
        aria-label="Abrir pantalla: Inicio">
        <span class="cap">
          <span class="nm">Inicio</span><span class="fl">390 · por defecto</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'mobile-home.webp' ); ?>"
               alt="Inicio — Presupuestador RE"
               width="1176" height="2349" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
    <figure class="shot shot--mobile">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'mobile-history.webp' ); ?>"
        data-es-screen-w="1176" data-es-screen-h="2349" data-es-screen-cssw="392"
        data-es-screen-name="Historial"
        data-es-screen-meta="390 · por defecto"
        aria-label="Abrir pantalla: Historial">
        <span class="cap">
          <span class="nm">Historial</span><span class="fl">390 · por defecto</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'mobile-history.webp' ); ?>"
               alt="Historial — Presupuestador RE"
               width="1176" height="2349" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
  </div>

  <h3 class="sub"><span class="tick"></span>Exploración de color</h3>
  <p class="body" style="max-width:78ch">Una prueba de remap de color hecha durante el diseño del sistema, guardada como registro. El sistema publicado tiene un solo tema, dark; esta comparación no es un tema disponible.</p>
  <div class="shots shots--single">
    <figure class="shot">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'light-dark.webp' ); ?>"
        data-es-screen-w="2880" data-es-screen-h="6224" data-es-screen-cssw="1440"
        data-es-screen-name="Comparación light / dark"
        data-es-screen-meta="Light &amp; Dark Comparison.html"
        aria-label="Abrir pantalla: Comparación light / dark">
        <span class="cap">
          <span class="nm">Comparación light / dark</span><span class="art-kind">Exploración</span><span class="fl">Light &amp; Dark Comparison.html</span>
          <span class="r"><span class="zoom-hint">Ampliar</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'light-dark-preview.webp' ); ?>"
               alt="Comparación light / dark — Presupuestador RE"
               width="1520" height="3285" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
  </div>

  <h3 class="sub"><span class="tick"></span>Documentación de respaldo</h3>
  <p class="lede">Los documentos donde se decidieron las cosas que este sistema da por resueltas: auditorías, el cierre de alcance de la V1 y el paquete de handoff.</p>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Artefacto</th><th>Tipo</th><th>Qué contiene</th></tr></thead>
    <tbody>
      <tr><td><span class="art-name">Refinamiento HiFi — Auditoría y Decisiones</span></td><td><span class="art-kind">Auditoría</span></td><td>Auditoría wireframe vs HiFi, problemas UX/visuales, comparación A/B/C/D del panel de resultado.</td></tr>
      <tr><td><span class="art-name">Typography Comparison</span></td><td><span class="art-kind">Auditoría</span></td><td>Hanken vs Plus Jakarta contra pantallas reales — decisión de congelar Hanken.</td></tr>
      <tr><td><span class="art-name">V1 Design Freeze · Readiness Audit</span></td><td><span class="art-kind">Freeze</span></td><td>Cierre de alcance V1 y verificación de que el sistema estaba listo para congelarse.</td></tr>
      <tr><td><span class="art-name">Accessibility Review · UX Review</span></td><td><span class="art-kind">Review</span></td><td>Resultados de las revisiones de accesibilidad y de UX sobre el kit congelado.</td></tr>
      <tr><td><span class="art-name">Claude Code Handoff Package</span></td><td><span class="art-kind">Handoff</span></td><td>Paquete de handoff a desarrollo.</td></tr>
    </tbody></table></div>
  <p class="body" style="margin-top:var(--re-s4);font-size:12.5px;color:var(--re-ink-4)">Se listan como inventario, sin publicarse. Los nombres son los originales de cada documento.</p>
</section>

<!-- ============================================================ 09 -->
<section class="sec" id="s09">
  <div class="sec-h"><span class="num-badge">09</span><h2>Inventario de componentes</h2><span class="cnt">33 construidos · 6 especificados</span></div>
  <p class="lede">Los 33 componentes construidos y los 6 que quedaron especificados, con lo que cada uno cubre hoy en desktop y en mobile. La columna <b>Estado</b> dice en qué punto del sistema está:</p>
  <dl class="ivt-legend"><div><dt><span class="tag stable">Estable</span></dt><dd>construido y en uso.</dd></div><div><dt><span class="tag ext">Extendido</span></dt><dd>existe, y le falta cobertura para mobile.</dd></div><div><dt><span class="tag new">Nuevo</span></dt><dd>especificado en Mobile v1, todavía sin construir.</dd></div><div><dt><span class="tag rev">Requiere revisión</span></dt><dd>tiene una inconsistencia documentada.</dd></div></dl>
  <div class="scrollx"><table class="tb dense">
    <thead><tr><th>Componente</th><th>Categoría</th><th>Variantes</th><th>Estados de UI</th><th>Desktop</th><th>Mobile</th><th>Estado</th><th>Notas de implementación</th></tr></thead>
    <tbody>
      <tr><td>Button</td><td>Formularios</td><td>secondary · primary · accent · ghost / sm·md·lg</td><td>default · hover · focus · disabled</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Un solo primary/accent por vista. Mobile usa h42 en barras de acción.</td></tr>
      <tr><td>IconButton</td><td>Formularios</td><td>default 34 · bare 30 · danger</td><td>default · hover · focus · disabled</td><td>Sí</td><td>Parcial</td><td><span class="tag stable">Estable</span></td><td><span class="mono">title</span> obligatorio. En mobile subir a ≥44 de área.</td></tr>
      <tr><td>Field</td><td>Formularios</td><td>label · optional · note</td><td>—</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Wrapper de todos los controles.</td></tr>
      <tr><td>Input</td><td>Formularios</td><td>prefix · suffix · leadIcon · num</td><td>default · focus · disabled · placeholder</td><td>Sí</td><td>Sí</td><td><span class="tag rev">Requiere revisión</span></td><td>Sin estado de error/invalid (ver NR‑06).</td></tr>
      <tr><td>Select</td><td>Formularios</td><td>native + chevron</td><td>default · focus · disabled</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Pasar <span class="mono">&lt;option&gt;</span> como children.</td></tr>
      <tr><td>Textarea</td><td>Formularios</td><td>resize vertical</td><td>default · focus · disabled</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Notas del presupuesto.</td></tr>
      <tr><td>Toggle</td><td>Formularios</td><td>md 38 · sm 32 · accent</td><td>on · off · disabled</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Track charcoal on; <span class="mono">sm</span> para filas de tabla.</td></tr>
      <tr><td>SegmentedControl</td><td>Formularios</td><td>2–3 opciones, con/sin icono</td><td>selected · unselected</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>&gt;3 opciones → Select.</td></tr>
      <tr><td>Badge</td><td>Feedback</td><td>5 tonos · dot · square</td><td>estático</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td><span class="mono">square</span> para códigos mono.</td></tr>
      <tr><td>StatusBadge</td><td>Feedback</td><td>draft·sent·appr·rej·cancel</td><td>estático</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Mapea color + etiqueta español automáticamente.</td></tr>
      <tr><td>Alert</td><td>Feedback</td><td>warn·ok·info·crit·neutral</td><td>estático · con/sin título</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>warn es default: el motor nunca bloquea.</td></tr>
      <tr><td>AlertGroup</td><td>Feedback</td><td>title · items</td><td>estático</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Panel “Alertas y recordatorios”.</td></tr>
      <tr><td>Card</td><td>Layout</td><td>pad · flush</td><td>estático · hover en filas</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td><span class="mono">flush</span> para tablas a sangre.</td></tr>
      <tr><td>CardHead</td><td>Layout</td><td>icon · title · hint · actions</td><td>estático</td><td>Sí</td><td>Parcial</td><td><span class="tag stable">Estable</span></td><td>En mobile las actions suelen pasar a overflow.</td></tr>
      <tr><td>CardBody</td><td>Layout</td><td>default · tight</td><td>—</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td><span class="mono">tight</span> para tablas y listas de opciones.</td></tr>
      <tr><td>SectionHead</td><td>Layout</td><td>index · title · hint · tools</td><td>estático</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Pasos numerados de calculadora / editor.</td></tr>
      <tr><td>StatTile</td><td>Layout</td><td>default · accent · dot · amberValue</td><td>estático</td><td>Sí</td><td>Parcial</td><td><span class="tag stable">Estable</span></td><td>Un solo accent por fila.</td></tr>
      <tr><td>TechnicalPreviewCard</td><td>Layout</td><td>compact · standard · large / 4 renderers</td><td>live · placeholder</td><td>Sí</td><td>No documentado</td><td><span class="tag ext">Extendido</span></td><td>Registro extensible. Comportamiento mobile sin especificar.</td></tr>
      <tr><td>TechnicalRenderers</td><td>Layout</td><td>registry API</td><td>—</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td><span class="mono">register · setFamilyLabel · has · variants · families</span></td></tr>
      <tr><td>AppShell</td><td>Navegación</td><td>sidebar + topbar + main</td><td>—</td><td>Sí</td><td>Requiere modo</td><td><span class="tag ext">Extendido</span></td><td>Necesita 3 modos: sidebar / tabs / bottom bar.</td></tr>
      <tr><td>Sidebar</td><td>Navegación</td><td>logo · footer</td><td>—</td><td>Sí</td><td>No aplica</td><td><span class="tag stable">Estable</span></td><td>248px fijo, sticky.</td></tr>
      <tr><td>LogoMark</td><td>Navegación</td><td>mark · name · tagline</td><td>—</td><td>Sí</td><td>Sí</td><td><span class="tag rev">Requiere revisión</span></td><td>Tagline default desalineado (ver NR‑02).</td></tr>
      <tr><td>NavGroup</td><td>Navegación</td><td>label · first</td><td>—</td><td>Sí</td><td>No aplica</td><td><span class="tag stable">Estable</span></td><td>Eyebrow uppercase del sidebar.</td></tr>
      <tr><td>NavItem</td><td>Navegación</td><td>icon · label · as</td><td>default · hover · active</td><td>Sí</td><td>Sustituido</td><td><span class="tag stable">Estable</span></td><td>Active = rail ámbar de 3px + label bold.</td></tr>
      <tr><td>Topbar</td><td>Navegación</td><td>breadcrumbs · actions</td><td>—</td><td>Sí</td><td>Variante compacta</td><td><span class="tag ext">Extendido</span></td><td>Mobile: back + título, sin breadcrumbs.</td></tr>
      <tr><td>Breadcrumbs</td><td>Navegación</td><td>items[]</td><td>—</td><td>Sí</td><td>Ocultos</td><td><span class="tag stable">Estable</span></td><td>Último item en bold.</td></tr>
      <tr><td>UserChip</td><td>Navegación</td><td>name · role · initials</td><td>—</td><td>Sí</td><td>Sólo avatar</td><td><span class="tag stable">Estable</span></td><td>En mobile colapsa al avatar.</td></tr>
      <tr><td>Table</td><td>Datos</td><td>helpers is-right/center/sortable</td><td>row hover</td><td>Sí</td><td>Fila → card</td><td><span class="tag stable">Estable</span></td><td>Envolver en <span class="mono">Card flush</span>.</td></tr>
      <tr><td>TableToolbar</td><td>Datos</td><td>title · count · actions</td><td>—</td><td>Sí</td><td>Simplificado</td><td><span class="tag stable">Estable</span></td><td>Pill de recuento de resultados.</td></tr>
      <tr><td>RowActions</td><td>Datos</td><td>cluster de IconButtons</td><td>revelado en hover</td><td>Sí</td><td>Overflow ⋯</td><td><span class="tag ext">Extendido</span></td><td>En táctil no hay hover: requiere menú ⋯.</td></tr>
      <tr><td>Pagination</td><td>Datos</td><td>showing · pages · current</td><td>current · hover</td><td>Sí</td><td>No documentado</td><td><span class="tag ext">Extendido</span></td><td>En mobile va a ser scroll infinito o “cargar más”, todavía sin definir.</td></tr>
      <tr><td>TaxonomyIcon</td><td>Iconos</td><td>10 categorías · size · strokeWidth</td><td>currentColor · activo ámbar</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Usado también en el bottom tab bar.</td></tr>
      <tr><td>TAXONOMY_ICONS</td><td>Iconos</td><td>array ordenado</td><td>—</td><td>Sí</td><td>Sí</td><td><span class="tag stable">Estable</span></td><td>Iterar para menús y catálogos.</td></tr>
      <tr><td>BottomTabBar</td><td>Navegación</td><td>5 tabs</td><td>active · inactive</td><td>No aplica</td><td>Sí</td><td><span class="tag new">Nuevo</span></td><td>h64 + safe-area. Iconos 21, label 10.</td></tr>
      <tr><td>MobileHeader</td><td>Navegación</td><td>back+título · marca+título</td><td>sticky</td><td>No aplica</td><td>Sí</td><td><span class="tag new">Nuevo</span></td><td>Variante compacta de Topbar.</td></tr>
      <tr><td>BottomActionBar</td><td>Navegación</td><td>1 primary + 2 secundarias</td><td>fixed</td><td>No aplica</td><td>Sí</td><td><span class="tag new">Nuevo</span></td><td>h62, bottom 64, shadow hacia arriba.</td></tr>
      <tr><td>ResultSummary</td><td>Producto</td><td>pinned charcoal</td><td>sticky · live</td><td>No aplica</td><td>Sí</td><td><span class="tag new">Nuevo</span></td><td>Variante de ResultPanel: recomendado + rango + tamaño + “Ver desglose”.</td></tr>
      <tr><td>ResultSheet</td><td>Producto</td><td>bottom sheet</td><td>abierto · cerrado</td><td>No aplica</td><td>Sí</td><td><span class="tag new">Nuevo</span></td><td>Radio sup. 18, máx 78%, grab 38×4, scrim .6.</td></tr>
      <tr><td>FilterChips</td><td>Datos</td><td>una línea scroll-x</td><td>active ámbar sólido · inactive</td><td>Parcial</td><td>Sí</td><td><span class="tag new">Nuevo</span></td><td>Nunca wrap a dos líneas.</td></tr>
      <tr><td>Checkbox · Radio</td><td>Formularios</td><td>—</td><td>—</td><td>No</td><td>No</td><td><span class="tag rev">Requiere revisión</span></td><td>No existen en el sistema (ver NR‑13).</td></tr>
      <tr><td>Skeleton · Spinner</td><td>Feedback</td><td>—</td><td>—</td><td>No</td><td>No</td><td><span class="tag rev">Requiere revisión</span></td><td>Sin componente de loading (ver NR‑07).</td></tr>
    </tbody>
  </table></div>
</section>

<!-- ============================================================ 10 -->
<section class="sec" id="s10">
  <div class="sec-h"><span class="num-badge">10</span><h2>Diseño → Implementación</h2></div>
  <p class="lede">Cómo escala el sistema: una decisión de foundation se convierte en token, el token viste un componente,
    el componente compone un patrón de producto y el patrón se instancia en una pantalla. La cadena es la misma para todo.</p>

  <div class="chain">
    <div class="lk"><div class="st">01 · Foundation</div><div class="k">Decisión</div><div class="ex">Un solo acento cálido, racionado</div><div class="d">El ámbar marca sólo los momentos que importan; el resto es grafito.</div></div>
    <div class="lk"><div class="st">02 · Token</div><div class="k">Variable</div><div class="ex">--re-amber #e0911e<br>--re-amber-bright #f5b342</div><div class="d">Vive en <span class="mono">tokens/colors.css</span>. Todo lo demás lo referencia.</div></div>
    <div class="lk"><div class="st">03 · Component</div><div class="k">Primitivo</div><div class="ex">Button variant="accent"<br>StatTile amberValue</div><div class="d">El componente nunca hardcodea el hex: lee la variable.</div></div>
    <div class="lk"><div class="st">04 · Pattern</div><div class="k">Composición</div><div class="ex">Valor recomendado<br>+ rango + track</div><div class="d">La cifra ámbar sobre charcoal con su rango: significado de negocio.</div></div>
    <div class="lk"><div class="st">05 · Screen</div><div class="k">Instancia</div><div class="ex">Calculator.html<br>ResultSummary (mobile)</div><div class="d">El mismo patrón, tres densidades, sin volver a decidir nada.</div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Cómo consumir el sistema</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">1 · Estilos</h4>
      <p class="body" style="margin:0 0 var(--re-s4)">Enlazá <b>un solo archivo</b>. Es un manifiesto de <span class="mono">@import</span>: trae fuentes, colores, tipografía, spacing, elevación y el reset base.</p>
      <div class="stage" style="display:block"><code class="mono" style="font-size:12px;color:var(--re-ink-2);line-height:1.7">&lt;link rel="stylesheet" href="styles.css"&gt;<br>&lt;body class="re-root"&gt;</code></div>
      <h4 class="mini">2 · Tema</h4>
      <p class="body" style="margin:0 0 var(--re-s4)">No hay nada que configurar: dark es el único tema del sistema, y es el que aplica <span class="mono">:root</span> por defecto.</p>
      <h4 class="mini">3 · Componentes</h4>
      <div class="stage" style="display:block"><code class="mono" style="font-size:12px;color:var(--re-ink-2);line-height:1.7">const { Button, Card, StatusBadge } =<br>&nbsp;&nbsp;window.PresupuestadorREDesignSystem_84f335;</code></div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Reglas para superficies nuevas</h4>
      <ul class="body" style="margin:0"><li><b>Nunca</b> un hex literal: siempre <span class="mono">var(--re-*)</span>. Si falta un token, se agrega al sistema, no a la pantalla.</li>
        <li><b>Nunca</b> re‑explorar tipografía: está congelada.</li>
        <li><b>Respetá el racionamiento del ámbar</b>: recomendado · rail/tab activo · CTA · logo.</li>
        <li><b>Iconos</b>: copiá las formas del kit; 24×24, stroke 1.7–1.8, round caps.</li>
        <li><b>Sin emoji</b>, sin gradientes, sin glass, sin blur decorativo.</li>
        <li><b>Las 5 reglas de producto</b> del §00 no se negocian desde diseño.</li>
        <li>Cifras: <span class="mono">tabular-nums</span> y formato uruguayo siempre.</li></ul>
      <h4 class="mini">Migración a Figma</h4>
      <ul class="body" style="margin:0"><li>Los tokens de §01 mapean 1:1 a variables de Figma (color, number para spacing/radius, string para families).</li>
        <li>Las variantes de §02 mapean a component properties (<span class="mono">variant</span>, <span class="mono">size</span>, <span class="mono">tone</span>, <span class="mono">status</span>).</li>
        <li>Los tres breakpoints de §07 mapean a page/frame widths 390 · 834 · 1440.</li></ul>
    </div></div>
  </div>

  <!-- NEEDS REVIEW REGISTER -->
  <h3 class="sub" id="needs-review"><span class="tick"></span>Evolución del sistema</h3>
  <p class="lede">Las líneas de trabajo abiertas del sistema y en qué estado está cada una.</p>
  <div class="ev-grid">
      <div class="ev">
        <span class="ev-name">Tablet</span>
        <span class="ev-state ev-state--progress">En progreso</span>
      </div>
      <div class="ev">
        <span class="ev-name">Expansión V2</span>
        <span class="ev-state ev-state--progress">En desarrollo</span>
      </div>
      <div class="ev">
        <span class="ev-name">Tema light</span>
        <span class="ev-state ev-state--planned">Planificado</span>
      </div>
  </div>

  <details class="ev-audit">
    <summary>Registro interno de auditoría (13 notas)</summary>
    <p class="body">Inconsistencias y huecos detectados al auditar el sistema. Quedan escritos para resolverse como decisión explícita en vez de corregirse sobre la marcha. Es material de trabajo interno.</p>
    <div class="stack">
    <div class="nr"><span class="id">NR‑01</span><p><b>Prosa vs token en <span class="mono">--re-ink-4</span>.</b> El readme describe <span class="mono">#686d76</span>; el token real es <span class="mono">#7f858e</span> (elevado para cumplir WCAG AA). La prosa del readme quedó desactualizada respecto del archivo de tokens.</p></div>
    <div class="nr"><span class="id">NR‑02</span><p><b>Tagline por defecto de <span class="mono">LogoMark</span>.</b> El componente trae <span class="mono">'RE · v1.0'</span>; el kit y los mockups muestran <span class="mono">“REstimator · motor v1.5”</span>. Definir cuál es el canónico.</p></div>
    <div class="nr"><span class="id">NR‑03</span><p><b><span class="mono">--re-text-xl (21px)</span> no documentado.</b> Existe en <span class="mono">tokens/typography.css</span> pero falta en la escala descrita en el readme (40 · 23 · 17 · 14.5 · 14 · 13 · 12 · 11).</p></div>
    <div class="nr"><span class="id">NR‑04</span><p><b>Altura de control por breakpoint.</b> <span class="mono">--re-control-h</span> es 40px (desktop) pero la spec Mobile v1 usa 42px. No hay token de control mobile: hoy el 42 está hardcodeado.</p></div>
    <div class="nr"><span class="id">NR‑05</span><p><b>Breakpoints sin tokenizar.</b> 390 / 834 / 1200–1440 viven en prosa y specs, no en <span class="mono">tokens/</span>.</p></div>
    <div class="nr"><span class="id">NR‑06</span><p><b>Sin estado de error de campo.</b> <span class="mono">Field</span>/<span class="mono">Input</span> no exponen <span class="mono">error</span>/<span class="mono">invalid</span>. Coherente con el motor warn‑only, pero deja sin definir la validación inline si alguna vez se necesita.</p></div>
    <div class="nr"><span class="id">NR‑07</span><p><b>Sin loading ni empty state como componentes.</b> No existe skeleton/spinner; los empty states son copy dentro de pantallas.</p></div>
    <div class="nr"><span class="id">NR‑08</span><p><b>Racionamiento del ámbar en mobile.</b> La regla define cuatro momentos; mobile agrega el tab activo (ámbar‑ink) y el filter chip activo (ámbar sólido). Hay que reformular la regla para táctil o reclasificar esos usos como “rail activo”.</p></div>
    <div class="nr"><span class="id">NR‑09</span><p><b>6 componentes mobile especificados sin construir.</b> <span class="mono">BottomTabBar · MobileHeader · BottomActionBar · ResultSummary · ResultSheet · FilterChips</span> existen en Mobile v1 pero no en <span class="mono">components/</span>.</p></div>
    <div class="nr"><span class="id">NR‑10</span><p><b>Starting points obsoletos.</b> Las 4 pantallas del kit están marcadas <span class="mono">@startingPoint</span>, un mecanismo que los proyectos consumidores ya no ofrecen. Lo reemplazaron los <span class="mono">templates/</span>, y hoy hay 0 templates.</p></div>
    <div class="nr"><span class="id">NR‑11</span><p><b>Fuentes por CDN.</b> <span class="mono">tokens/fonts.css</span> importa Hanken Grotesk y JetBrains Mono desde Google Fonts. Para un export standalone verdaderamente offline hay que auto‑hospedarlas o inlinearlas.</p></div>
    <div class="nr"><span class="id">NR‑12</span><p><b>Tablet sin artefacto.</b> La capa está documentada pero no hay pantalla tablet en <span class="mono">ui_kits/</span>; sus valores vienen de los mockups de refinamiento.</p></div>
    <div class="nr"><span class="id">NR‑13</span><p><b>Sin Checkbox ni Radio.</b> El producto los evita (usa Select, SegmentedControl y Toggle), pero conviene declararlo como decisión explícita del sistema en vez de un hueco.</p></div>
  </div>
  </details>
</section>

<footer style="border-top:1px solid var(--re-line);margin-top:var(--re-s9);padding-top:var(--re-s5);display:flex;justify-content:space-between;flex-wrap:wrap;gap:16px;font-size:12px;color:var(--re-ink-4)">
  <span>REstimator Design System — Master Documentation</span>
  <span class="mono">147 tokens · 33 componentes</span>
</footer>

</div>
</main>
</div>
