<?php
/**
 * REstimator Design System — cuerpo del documento (EN).
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
  <div class="rail-grp">Documentation</div>
  <a class="nv on" href="#s00"><span class="n">00</span> Overview</a>
  <a class="nv" href="#s01"><span class="n">01</span> Foundations</a>
  <a class="nv" href="#s02"><span class="n">02</span> Components</a>
  <a class="nv" href="#s03"><span class="n">03</span> Navigation &amp; App Shell</a>
  <a class="nv" href="#s04"><span class="n">04</span> Forms &amp; Data Entry</a>
  <a class="nv" href="#s05"><span class="n">05</span> Data Display</a>
  <a class="nv" href="#s06"><span class="n">06</span> Product Patterns</a>
  <a class="nv" href="#s07"><span class="n">07</span> Responsive System</a>
  <a class="nv" href="#s08"><span class="n">08</span> Screen Examples</a>
  <a class="nv" href="#s09"><span class="n">09</span> Component Inventory</a>
  <a class="nv" href="#s10"><span class="n">10</span> Design → Implementation</a>
  <div class="rail-grp">Status</div>
  <a class="nv" href="#needs-review"><span class="n">SE</span> System evolution</a>
  <div class="rail-foot">Master documentation for the system.<br>Dark theme.<br>Typography frozen 2026‑06‑16.</div>
</nav>

<main class="main">

<!-- ================= HERO / PORTFOLIO ENTRY ================= -->
<header class="hero">
  <div class="in">
    <div class="eyebrow">Design System · Master documentation</div>
    <h1>REstimator Design System</h1>
    <p class="sub">The design system behind <b style="color:#fff">Presupuestador RE</b>, a B2B quoting tool for metalwork and ironwork shops. Dark, industrial, dense, desktop-first, with a single rationed amber accent. This documentation gathers its foundations, its components, its product patterns and its responsive system.</p>
    <div class="facts">
      <div><div class="k">Tokens</div><div class="v num">147</div><div class="d">colour · type · space · elevation</div></div>
      <div><div class="k">Components</div><div class="v num">33</div><div class="d">exports in the namespace</div></div>
      <div><div class="k">Screens</div><div class="v v--pair"><span class="n num">5</span> desktop <span class="n num">3</span> mobile</div><div class="d">from the UI kit and the Mobile v1 spec</div></div>
      <div><div class="k">Theme</div><div class="v">Dark</div><div class="d">the system’s only theme</div></div>
    </div>
  </div>
</header>

<div class="pad">

<!-- ============================================================ 00 -->
<section class="sec" id="s00">
  <div class="sec-h"><span class="num-badge">00</span><h2>Overview</h2></div>
  <p class="lede">Presupuestador RE helps estimators and shop owners produce faster, more consistent quotes from standardised criteria: families → subcategories → dimensions, calibrated price ranges, labour days and material references. It is a tool for daily work.</p>

  <h3 class="sub"><span class="tick"></span>What the system is for</h3>
  <p class="body">To codify that product as reusable foundations, components and screen recreations, so any new surface is built on-brand in minutes. The <b>frozen V1</b> mid-fi prototype is the reference: the information architecture, the flows and the screen structure are already settled. The system raises <b>visual quality only</b>; it does not redesign flows or change the IA.</p>

  <h3 class="sub"><span class="tick"></span>Principles</h3>
  <div class="prin">
    <div class="p"><div class="k">Speed</div><div class="d">Operational density. The estimator enters measurements and reads the recommended value in a loop; nothing gets in the way.</div></div>
    <div class="p"><div class="k">Clarity</div><div class="d">Structure drawn with 1px hairlines, not heavy fills. Typographic hierarchy before colour.</div></div>
    <div class="p"><div class="k">Trust</div><div class="d">Every number explains where it comes from. Warnings are warn-only and never block the calculation.</div></div>
    <div class="p"><div class="k">Efficiency</div><div class="d">No gradients, glass, decorative blur or entrance choreography. Motion is minimal and functional.</div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Overall architecture</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Layer</th><th>Location</th><th>What it holds</th></tr></thead>
    <tbody>
      <tr><td>Global entry point</td><td class="tok">styles.css</td><td>An <span class="mono">@import</span> manifest only. Consumers link this file.</td></tr>
      <tr><td>Tokens</td><td class="tok">tokens/</td><td><span class="mono">fonts · colors · typography · spacing · elevation · theme-light · base</span></td></tr>
      <tr><td>Components</td><td class="tok">components/</td><td>React, 6 categories. Namespace <span class="mono">window.PresupuestadorREDesignSystem_84f335</span></td></tr>
      <tr><td>Foundation cards</td><td class="tok">guidelines/</td><td>16 specimens of colour, type, space, brand and theme validation.</td></tr>
      <tr><td>UI kit</td><td class="tok">ui_kits/presupuestador/</td><td>5 interactive hi-fi screens of the frozen product, dark. Token-driven via <span class="mono">kit.css</span> + <span class="mono">shell.js</span></td></tr>
      <tr><td>Mobile spec</td><td class="tok">Mobile v1 - Spec de implementación.html</td><td>3 final mobile screens with measurements, spacing, sticky behaviour and components.</td></tr>
      <tr><td>Agent skill</td><td class="tok">SKILL.md</td><td>Makes the folder usable as an Agent Skill in Claude Code.</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Status and versioning</h3>
  <div class="grid3">
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Frozen</h4>
      <ul class="body" style="margin:0"><li>IA, flows and screen structure (V1).</li><li>Type pairing (2026‑06‑16) — not to be re-explored.</li><li>The 5 product rules (below).</li></ul></div></div>
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Stable and in use</h4>
      <ul class="body" style="margin:0"><li>147 tokens · dark theme.</li><li>33 exported components.</li><li>5 desktop screens from the kit.</li></ul></div></div>
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Specified, not built</h4>
      <ul class="body" style="margin:0"><li>6 mobile components (Mobile v1).</li><li>Tablet layer (documented, no artifact).</li><li><span class="mono">templates/</span> — 0 templates.</li></ul></div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Non-negotiable product rules</h3>
  <p class="body">They come from S12 and are respected on every new surface. They are rules of the business domain, not design criteria.</p>
  <div class="rules">
    <div class="rule"><span class="n">1</span><div class="tx"><b>Freeze</b>Every quote freezes the parameter version it was calculated with; published ones are immutable.</div></div>
    <div class="rule"><span class="n">2</span><div class="tx"><b>QA gate</b>A parameter or product change only reaches production through a QA Run.</div></div>
    <div class="rule"><span class="n">3</span><div class="tx"><b>Currency</b>The product operates in UYU; the exchange rate only converts internal costs.</div></div>
    <div class="rule"><span class="n">4</span><div class="tx"><b>Editor</b>S5A (<span class="mono">ProductEditor</span>) is the only product editor.</div></div>
    <div class="rule"><span class="n">5</span><div class="tx"><b>Privacy</b>The client summary never serialises margins, labour days or calibration.</div></div>
  </div>
</section>

<!-- ============================================================ 01 -->
<section class="sec" id="s01">
  <div class="sec-h"><span class="num-badge">01</span><h2>Foundations</h2><span class="cnt">147 tokens</span></div>
  <p class="lede">Every value in this section is a real system token, read from <span class="mono">tokens/</span>. Components always read <span class="mono">var(--re-*)</span>, so nothing hardcodes a value.</p>

  <h3 class="sub"><span class="tick"></span>Colour · graphite ramp (dark base)</h3>
  <div class="sw-grid">
    <div class="sw"><div class="chip" style="background:#0d0e11"></div><div class="meta"><div class="nm">--re-canvas</div><div class="hx">#0d0e11</div><div class="use">App background — near black</div></div></div>
    <div class="sw"><div class="chip" style="background:#16181d"></div><div class="meta"><div class="nm">--re-surface</div><div class="hx">#16181d</div><div class="use">Cards, panels</div></div></div>
    <div class="sw"><div class="chip" style="background:#1d2026"></div><div class="meta"><div class="nm">--re-surface-2</div><div class="hx">#1d2026</div><div class="use">Inset / hover, inputs</div></div></div>
    <div class="sw"><div class="chip" style="background:#262a31"></div><div class="meta"><div class="nm">--re-surface-3</div><div class="hx">#262a31</div><div class="use">Deep inset, tracks</div></div></div>
    <div class="sw"><div class="chip" style="background:#f2f3f4"></div><div class="meta"><div class="nm">--re-ink</div><div class="hx">#f2f3f4</div><div class="use">Primary text</div></div></div>
    <div class="sw"><div class="chip" style="background:#c2c6cc"></div><div class="meta"><div class="nm">--re-ink-2</div><div class="hx">#c2c6cc</div><div class="use">Secondary text</div></div></div>
    <div class="sw"><div class="chip" style="background:#9197a0"></div><div class="meta"><div class="nm">--re-ink-3</div><div class="hx">#9197a0</div><div class="use">Tertiary / hints</div></div></div>
    <div class="sw"><div class="chip" style="background:#7f858e"></div><div class="meta"><div class="nm">--re-ink-4</div><div class="hx">#7f858e</div><div class="use">Faint labels, placeholders — raised for AA</div></div></div>
    <div class="sw"><div class="chip" style="background:#262a31"></div><div class="meta"><div class="nm">--re-line</div><div class="hx">#262a31</div><div class="use">Hairlines, card borders</div></div></div>
    <div class="sw"><div class="chip" style="background:#313641"></div><div class="meta"><div class="nm">--re-line-2</div><div class="hx">#313641</div><div class="use">Input borders</div></div></div>
    <div class="sw"><div class="chip" style="background:#434954"></div><div class="meta"><div class="nm">--re-line-strong</div><div class="hx">#434954</div><div class="use">Emphasised borders, dashed</div></div></div>
  </div>

  <h4 class="mini">Charcoal — deeper panels</h4>
  <div class="sw-grid">
    <div class="sw"><div class="chip" style="background:#0a0b0e"></div><div class="meta"><div class="nm">--re-charcoal</div><div class="hx">#0a0b0e</div><div class="use">Recommended block, document total</div></div></div>
    <div class="sw"><div class="chip" style="background:#14161b"></div><div class="meta"><div class="nm">--re-charcoal-2</div><div class="hx">#14161b</div><div class="use">Sub-block on charcoal</div></div></div>
    <div class="sw"><div class="chip" style="background:#2b2f37"></div><div class="meta"><div class="nm">--re-charcoal-line</div><div class="hx">#2b2f37</div><div class="use">Hairline on charcoal</div></div></div>
  </div>

  <h4 class="mini">Amber — the only brand colour</h4>
  <div class="sw-grid">
    <div class="sw"><div class="chip" style="background:#e0911e"></div><div class="meta"><div class="nm">--re-amber</div><div class="hx">#e0911e</div><div class="use">Primary CTA, active rail</div></div></div>
    <div class="sw"><div class="chip" style="background:#f0a634"></div><div class="meta"><div class="nm">--re-amber-strong</div><div class="hx">#f0a634</div><div class="use">Hover / press (lightens on dark)</div></div></div>
    <div class="sw"><div class="chip" style="background:#f5b342"></div><div class="meta"><div class="nm">--re-amber-bright</div><div class="hx">#f5b342</div><div class="use">Recommended value, headline figures</div></div></div>
    <div class="sw"><div class="chip" style="background:rgba(224,145,30,.13)"></div><div class="meta"><div class="nm">--re-amber-soft</div><div class="hx">rgba(224,145,30,.13)</div><div class="use">Tinted background</div></div></div>
    <div class="sw"><div class="chip" style="background:rgba(224,145,30,.34)"></div><div class="meta"><div class="nm">--re-amber-line</div><div class="hx">rgba(224,145,30,.34)</div><div class="use">Tinted border</div></div></div>
    <div class="sw"><div class="chip" style="background:#f0b45e"></div><div class="meta"><div class="nm">--re-amber-ink</div><div class="hx">#f0b45e</div><div class="use">Amber text on tint</div></div></div>
  </div>
  <div class="callout"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18.9 6.1 22l1.2-6.5L2.5 9.4l6.6-.9z"/></svg>
    <p><b>Amber rationing rule.</b> Amber appears in exactly four kinds of moment: the <b>recommended price</b> (amber figure on the deepest charcoal), the <b>active navigation rail</b>, the <b>primary / submit CTA</b> and the <b>logo</b>. Everything else is resolved with graphite and hairlines. See <a href="#needs-review">NR‑08</a> for how this rule extends to mobile.</p></div>

  <h4 class="mini">Semantic (tuned for dark)</h4>
  <div class="sw-grid">
    <div class="sw"><div class="chip" style="background:#52b06e"></div><div class="meta"><div class="nm">--re-ok</div><div class="hx">#52b06e</div><div class="use">+ <span class="mono">ok-bright #6fd08a</span>, soft, line</div></div></div>
    <div class="sw"><div class="chip" style="background:#d99a3a"></div><div class="meta"><div class="nm">--re-warn</div><div class="hx">#d99a3a</div><div class="use">+ <span class="mono">warn-ink #e6c188</span>, soft, line</div></div></div>
    <div class="sw"><div class="chip" style="background:#d86a52"></div><div class="meta"><div class="nm">--re-crit</div><div class="hx">#d86a52</div><div class="use">+ soft, line</div></div></div>
    <div class="sw"><div class="chip" style="background:#5b9be0"></div><div class="meta"><div class="nm">--re-info</div><div class="hx">#5b9be0</div><div class="use">+ soft, line</div></div></div>
  </div>

  <h4 class="mini">Quote lifecycle (History)</h4>
  <div class="stage" style="gap:var(--re-s4)"><span class="st"><span class="d d-draft"></span> Draft</span> <span class="st"><span class="d d-sent"></span> Sent</span> <span class="st"><span class="d d-appr"></span> Approved</span> <span class="st"><span class="d d-rej"></span> Rejected</span> <span class="st"><span class="d d-cancel"></span> Cancelled</span></div>

  <h4 class="mini">Semantic aliases — prefer these in components and screens</h4>
  <div class="scrollx"><table class="tb dense">
    <thead><tr><th>Alias</th><th>Resolves to</th><th>Alias</th><th>Resolves to</th></tr></thead>
    <tbody>
      <tr><td class="tok">--re-bg</td><td class="mono">--re-canvas</td><td class="tok">--re-border</td><td class="mono">--re-line</td></tr>
      <tr><td class="tok">--re-text</td><td class="mono">--re-ink</td><td class="tok">--re-border-field</td><td class="mono">--re-line-2</td></tr>
      <tr><td class="tok">--re-text-muted</td><td class="mono">--re-ink-2</td><td class="tok">--re-border-strong</td><td class="mono">--re-line-strong</td></tr>
      <tr><td class="tok">--re-text-subtle</td><td class="mono">--re-ink-3</td><td class="tok">--re-focus</td><td class="mono">--re-amber</td></tr>
      <tr><td class="tok">--re-text-faint</td><td class="mono">--re-ink-4</td><td class="tok">--re-accent / --re-primary</td><td class="mono">--re-amber</td></tr>
      <tr><td class="tok">--re-accent-hover</td><td class="mono">--re-amber-strong</td><td class="tok">--re-primary-hover</td><td class="mono">--re-amber-strong</td></tr>
    </tbody>
  </table></div>

  <h4 class="mini">Light theme — planned</h4>
  <p class="body"><span class="art-kind">Planned</span> The published system has <b>a single theme: dark</b>. A colour remap has been explored —warm white surfaces, graphite ink, warm grey borders— which would leave spacing, typography, radii, hierarchy and interaction untouched, with amber as the only accent. It is not part of the system yet and is not documented as available.</p>

  <h3 class="sub"><span class="tick"></span>Typography · FROZEN</h3>
  <p class="body">The pairing is final and is not re-explored. An alternative with Plus Jakarta Sans was evaluated against the real screens (<span class="mono">Typography Comparison.html</span>) and rejected: Hanken won on density, legibility at small sizes and industrial tone.</p>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Primary UI — Hanken Grotesk</h4>
      <div style="font-size:34px;font-weight:700;letter-spacing:-.025em;line-height:1.1">Recommended quote</div>
      <div style="font-size:13px;color:var(--re-ink-3);margin-top:10px">400 · 500 · 600 · 700 — humanist grotesque, industrial-neutral, not “rounded startup”. Used for everything: headings, body, controls, tables.</div>
      <div class="mono" style="font-size:11.5px;color:var(--re-amber-ink);margin-top:10px">--re-font-sans</div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Technical — JetBrains Mono</h4>
      <div class="mono" style="font-size:26px;font-weight:600;line-height:1.15">GV-A-00148<br>PRE-2026-0148</div>
      <div style="font-size:13px;color:var(--re-ink-3);margin-top:10px">400 · 500 · 600 — codes, IDs, dates and tabular figures. Product codes <span class="mono">ESC-RECT</span>.</div>
      <div class="mono" style="font-size:11.5px;color:var(--re-amber-ink);margin-top:10px">--re-font-mono</div>
    </div></div>
  </div>

  <h4 class="mini">Actual scale</h4>
  <div class="scrollx"><table class="tb dense">
    <thead><tr><th>Token</th><th>Value</th><th>Use</th><th>Sample</th></tr></thead>
    <tbody>
      <tr><td class="tok">--re-text-display</td><td class="mono">40px</td><td>The hero “recommended” figure</td><td><span style="font-size:34px;font-weight:700;letter-spacing:-.02em;color:var(--re-amber-bright)" class="num">$135.599</span></td></tr>
      <tr><td class="tok">--re-text-2xl</td><td class="mono">23px</td><td>Page H1</td><td><span style="font-size:23px;font-weight:600;letter-spacing:-.02em">Calculator</span></td></tr>
      <tr><td class="tok">--re-text-xl</td><td class="mono">21px</td><td>Compact H1</td><td><span style="font-size:21px;font-weight:600;letter-spacing:-.02em">History</span></td></tr>
      <tr><td class="tok">--re-text-lg</td><td class="mono">17px</td><td>Section H2</td><td><span style="font-size:17px;font-weight:600">Dimensions</span></td></tr>
      <tr><td class="tok">--re-text-md</td><td class="mono">14.5px</td><td>Card titles</td><td><span style="font-size:14.5px;font-weight:600">Quote header</span></td></tr>
      <tr><td class="tok">--re-text-base</td><td class="mono">14px</td><td>Body / controls</td><td><span style="font-size:14px">Tread width</span></td></tr>
      <tr><td class="tok">--re-text-sm</td><td class="mono">13px</td><td>Secondary body, cells</td><td><span style="font-size:13px;color:var(--re-ink-2)">Pedro Núñez · Railings</span></td></tr>
      <tr><td class="tok">--re-text-xs</td><td class="mono">12px</td><td>Hints, captions</td><td><span style="font-size:12px;color:var(--re-ink-3)">decimals with a comma</span></td></tr>
      <tr><td class="tok">--re-text-2xs</td><td class="mono">11px</td><td>Eyebrows, uppercase labels</td><td><span class="re-eyebrow">ESTIMATED FABRICATION</span></td></tr>
    </tbody>
  </table></div>

  <h4 class="mini">Weights · line-heights · tracking · helpers</h4>
  <div class="grid3">
    <div class="card"><div class="card-b"><dl class="spec">
      <dt>Regular</dt><dd>400</dd><dt>Medium</dt><dd>500</dd><dt>Semibold</dt><dd>600</dd><dt>Bold</dt><dd>700</dd></dl></div></div>
    <div class="card"><div class="card-b"><dl class="spec">
      <dt>tight</dt><dd>1.15</dd><dt>snug</dt><dd>1.35</dd><dt>normal</dt><dd>1.45</dd><dt>relaxed</dt><dd>1.6</dd></dl></div></div>
    <div class="card"><div class="card-b"><dl class="spec">
      <dt>ls-body</dt><dd>-0.005em</dd><dt>ls-heading</dt><dd>-0.02em</dd><dt>ls-tight</dt><dd>-0.01em</dd><dt>ls-eyebrow</dt><dd>0.06em</dd><dt>ls-caps</dt><dd>0.05em</dd></dl></div></div>
  </div>
  <div class="scrollx" style="margin-top:var(--re-s4)"><table class="tb dense">
    <thead><tr><th>Class</th><th>What it does</th></tr></thead>
    <tbody>
      <tr><td class="tok">.re-root</td><td>Base document type: family, 14px, lh 1.45, tracking, colour, antialiasing, <span class="mono">font-feature-settings:"tnum" 1,"ss01" 1</span>. Opt-in on <span class="mono">&lt;body&gt;</span> or a wrapper.</td></tr>
      <tr><td class="tok">.re-num</td><td>Tabular figures — money, measurements, codes, dates.</td></tr>
      <tr><td class="tok">.re-mono</td><td>Switches to JetBrains Mono.</td></tr>
      <tr><td class="tok">.re-eyebrow</td><td>Eyebrow / kicker: 11px, 600, +0.06em, uppercase, faint colour.</td></tr>
    </tbody>
  </table></div>

  <h4 class="mini">Numbers and units — Uruguayan format, always tabular</h4>
  <div class="scrollx"><table class="tb dense">
    <thead><tr><th>Type</th><th>Format</th><th>Example</th></tr></thead>
    <tbody>
      <tr><td>Money</td><td>dot as thousands separator, no decimals</td><td class="mono">$168.000</td></tr>
      <tr><td>Measurements</td><td>decimal comma + unit</td><td class="mono">0,80 m · 2,90 m</td></tr>
      <tr><td>Labour days</td><td>decimal comma, one decimal place</td><td class="mono">7,0</td></tr>
      <tr><td>Percentages</td><td>sign + value</td><td class="mono">+ 8% · IVA 22%</td></tr>
      <tr><td>Codes</td><td>mono, uppercase</td><td class="mono">GV-A-00148 · ESC-RECT</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Spacing · 4px grid</h3>
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

  <h3 class="sub"><span class="tick"></span>Sizing · layout constants (from the frozen V1 shell)</h3>
  <div class="grid3">
    <div class="card"><div class="card-b"><dl class="spec"><dt>Sidebar</dt><dd class="am">248px</dd><dt>Topbar</dt><dd class="am">60px</dd></dl>
      <div style="font-size:11.5px;color:var(--re-ink-4);margin-top:11px"><span class="mono">--re-sidebar-w · --re-topbar-h</span></div></div></div>
    <div class="card"><div class="card-b"><dl class="spec"><dt>Document width</dt><dd class="am">1080px</dd></dl>
      <div style="font-size:11.5px;color:var(--re-ink-4);margin-top:11px"><span class="mono">--re-content-max</span> — document / reading</div></div></div>
    <div class="card"><div class="card-b"><dl class="spec"><dt>Standard control</dt><dd class="am">40px</dd><dt>Compact control</dt><dd class="am">32px</dd></dl>
      <div style="font-size:11.5px;color:var(--re-ink-4);margin-top:11px"><span class="mono">--re-control-h · --re-control-h-sm</span> · see <a href="#needs-review">NR‑04</a></div></div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Radius</h3>
  <div class="stage">
    <div style="text-align:center"><div style="width:74px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:7px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r-sm · 7px</div></div>
    <div style="text-align:center"><div style="width:74px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:9px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r · 9px</div></div>
    <div style="text-align:center"><div style="width:74px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:13px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r-lg · 13px</div></div>
    <div style="text-align:center"><div style="width:74px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:30px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r-pill · 30px</div></div>
    <div style="text-align:center"><div style="width:56px;height:56px;background:var(--re-surface-2);border:1px solid var(--re-line-2);border-radius:999px"></div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:7px">r-full · 999px</div></div>
    <div style="font-size:12.5px;color:var(--re-ink-3);max-width:24ch">Consistent and modest — nothing fully rounded except avatars and dots.</div>
  </div>

  <h3 class="sub"><span class="tick"></span>Borders</h3>
  <p class="body">Structure is drawn with 1px lines, not heavy fills. Hairlines everywhere: <span class="mono">--re-line</span> for dividers and card borders, <span class="mono">--re-line-2</span> on inputs, <span class="mono">--re-line-strong</span> for emphasis and dashed rules.</p>

  <h3 class="sub"><span class="tick"></span>Elevation</h3>
  <p class="body">In dark, depth is <b>a lighter surface plus a 1px border</b>; shadows sit deep and quiet underneath. No glow, no coloured shadow.</p>
  <div class="grid3">
    <div class="card" style="box-shadow:var(--re-shadow-sm)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow-sm</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Hairline lift — cards by default, tiles</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 1px 2px rgba(0,0,0,.40)</div></div></div>
    <div class="card" style="box-shadow:var(--re-shadow)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Card at rest</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 1px 3px / 0 1px 2px</div></div></div>
    <div class="card" style="box-shadow:var(--re-shadow-panel)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow-panel</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Floating panel — sticky result, preview, menus</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 8px 24px / 0 2px 6px</div></div></div>
    <div class="card" style="box-shadow:var(--re-shadow-paper)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow-paper</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">The “paper” of the client-facing document</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 16px 44px / 0 3px 10px</div></div></div>
    <div class="card"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-shadow-bar</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Fixed save bar — the shadow points upwards</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">0 -2px 16px rgba(0,0,0,.50)</div></div></div>
    <div class="card" style="border-color:var(--re-amber);box-shadow:var(--re-ring)"><div class="card-b"><div class="mono" style="font-size:11.5px;color:var(--re-amber-ink)">--re-ring / --re-ring-amber</div><div style="font-size:12.5px;color:var(--re-ink-3);margin-top:6px">Focus — 3px amber on dark</div><div class="mono" style="font-size:10.5px;color:var(--re-ink-4);margin-top:6px">.26 / .32 alpha</div></div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Iconography</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Rules</h4>
      <ul class="body" style="margin:0"><li>Inline line SVG only. viewBox <span class="mono">24×24</span>, <span class="mono">stroke="currentColor"</span>, <span class="mono">stroke-width</span> ~1.7–1.8 (1.7 for nav/UI, 2 for chevrons/arrows), round caps and joins.</li>
        <li>They inherit text colour and sit at <b>15–18px</b>.</li>
        <li>No icon font, no sprite, no PNG, no emoji, no unicode pictograms. A “✓” in transient toasts is the only exception.</li>
        <li>For new surfaces, copy the shapes from the kit before reaching for a library. There is no bundled CDN set: the set is small and hand-maintained.</li></ul></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Recurring glyphs</h4>
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
      <div style="font-size:12px;color:var(--re-ink-4);margin-top:11px">calculator · clock · cube · sliders · shield-check · warning · star (recommended) · pencil · duplicate · document · bubble · magnifier</div></div></div>
  </div>
  <h4 class="mini">Product taxonomy family — <span class="mono" style="text-transform:none;letter-spacing:0">TaxonomyIcon</span></h4>
  <p class="body">10 fabrication-category glyphs drawn in the same language (24×24, stroke 1.7, no fills, no external library): <span class="mono">stairs · railings · grilles · enclosures · doors · gates · furniture · structures · roofs · grills</span>. They are used in category rails, dropdowns, catalogue cards and chips, so the product reads as a specialised metalwork tool and not as a generic business app. API: <span class="mono">&lt;TaxonomyIcon name="rejas" size={18} strokeWidth={1.7}/&gt;</span>; iterate <span class="mono">TAXONOMY_ICONS</span> for menus; colour via <span class="mono">currentColor</span> (amber when active).</p>

  <h3 class="sub"><span class="tick"></span>Motion</h3>
  <p class="body">Minimal and functional. <span class="mono">.12–.15s ease</span> on hover / border / background; toggles slide their knob; the only ambient movement is the slow pulse of the “Live” dot in live panels. No bounces, no parallax, no entrance choreography, no scale effects.</p>

  <h3 class="sub"><span class="tick"></span>Breakpoints</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Layer</th><th>Reference width</th><th>Role</th></tr></thead>
    <tbody>
      <tr><td>Desktop</td><td class="mono">≥ 1200 · design 1440</td><td>Primary layer. Fixed sidebar + side result panel.</td></tr>
      <tr><td>Tablet</td><td class="mono">834</td><td>Intermediate layer with an identity of its own: top tabs + bottom sheet.</td></tr>
      <tr><td>Mobile</td><td class="mono">390 · fluido 360–430</td><td>Designed, not adapted. Bottom tab bar + pinned summary.</td></tr>
    </tbody>
  </table></div>
  <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑05</span><p>Breakpoints are <b>not tokenised</b>: they live in prose and in the specs, not in <span class="mono">tokens/</span>. Any implementation is hardcoding them.</p></div>

  <h3 class="sub"><span class="tick"></span>Accessibility foundations</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Decision</th><th>Value</th><th>Why</th></tr></thead>
    <tbody>
      <tr><td>Contrast of faint labels</td><td class="mono">--re-ink-4 #7f858e</td><td>Raised from <span class="mono">#686d76</span> (≈3.4:1) to clear WCAG AA 4.5:1 on surfaces.</td></tr>
      <tr><td>Visible focus</td><td class="mono">--re-ring 3px amber</td><td>A 3px ring + amber border on every focusable control. The outline is never removed without a replacement.</td></tr>
      <tr><td>Touch targets</td><td class="mono">≥ 44px</td><td>Minimum touch size on mobile/tablet; 42px visual controls carry an extended hit area.</td></tr>
      <tr><td>Icons without text</td><td class="mono">title obligatorio</td><td><span class="mono">IconButton</span> requires <span class="mono">title</span>, used both as tooltip and <span class="mono">aria-label</span>.</td></tr>
      <tr><td>Figures</td><td class="mono">tabular-nums</td><td>Money and measurements never “dance” between states; vertical comparison stays reliable.</td></tr>
      <tr><td>State never by colour alone</td><td>dot + label</td><td>The lifecycle uses a coloured dot <b>and</b> a written label.</td></tr>
    </tbody>
  </table></div>
</section>

<!-- ============================================================ 02 -->
<section class="sec" id="s02">
  <div class="sec-h"><span class="num-badge">02</span><h2>Components</h2><span class="cnt">33 exports · 6 categories</span></div>
  <p class="lede">The 33 components exported by <span class="mono">window.PresupuestadorREDesignSystem_84f335</span>. Each directory holds <span class="mono">&lt;Name&gt;.jsx</span> + <span class="mono">.d.ts</span> + <span class="mono">.prompt.md</span> and a <span class="mono">*.card.html</span> specimen. The specimens below are token-driven replicas of the real components (same tokens, same anatomy), so this document does not depend on a React runtime.</p>

  <div class="callout"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
    <p><b>How to read the subcomponents.</b> Several exports live in one parent file: <span class="mono">Field/Input/Select/Textarea</span> ← <span class="mono">forms/Input.jsx</span> · <span class="mono">Card/CardHead/CardBody/SectionHead</span> ← <span class="mono">layout/Card.jsx</span> · <span class="mono">AppShell/LogoMark/Sidebar/NavGroup/NavItem/Topbar/Breadcrumbs/UserChip</span> ← <span class="mono">navigation/AppShell.jsx</span> · <span class="mono">Table/TableToolbar/RowActions/Pagination</span> ← <span class="mono">data/Table.jsx</span> · <span class="mono">Badge/StatusBadge</span> and <span class="mono">Alert/AlertGroup</span> ← their own <span class="mono">feedback/</span> files.</p></div>

  <!-- Button -->
  <h3 class="sub"><span class="tick"></span>Button</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="8" width="18" height="8" rx="3"/></svg></span><span class="tt">Variants</span><span class="hint">secondary · primary · accent · ghost</span></div>
      <div class="card-b"><div class="stage">
        <button class="b">Secondary</button><button class="b pri">Primary</button><button class="b acc">Accent</button><button class="b gh">Ghost</button>
      </div>
      <div class="stage" style="margin-top:var(--re-s3)"><span class="stage-lab" style="width:100%;margin:0 0 4px">Sizes · sm 32 · md 40 · lg 46</span>
        <button class="b sm">sm</button><button class="b">md</button><button class="b lg">lg</button>
      </div>
      <div class="stage" style="margin-top:var(--re-s3)"><span class="stage-lab" style="width:100%;margin:0 0 4px">States</span>
        <button class="b">Default</button><button class="b foc">Focus</button><button class="b dis">Disabled</button>
        <button class="b acc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12l16-8-6 16-2-6z"/></svg> With icon</button>
      </div></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Purpose</h4>
      <p class="body" style="margin-bottom:var(--re-s4)">The main action control. <b>Exactly one</b> <span class="mono">primary</span> (or <span class="mono">accent</span>) per view; everything else is <span class="mono">secondary</span> or <span class="mono">ghost</span>.</p>
      <h4 class="mini">API</h4>
      <dl class="spec"><dt>variant</dt><dd>secondary | primary | accent | ghost</dd><dt>size</dt><dd>sm | md | lg</dd><dt>block</dt><dd>boolean</dd><dt>iconLeft / iconRight</dt><dd>ReactNode (16px)</dd></dl>
      <h4 class="mini">Variant semantics</h4>
      <ul class="body" style="margin:0"><li><b>secondary</b> — neutral outline, the workhorse.</li>
        <li><b>primary</b> — solid charcoal fill, the screen's single main action.</li>
        <li><b>accent</b> — brand amber fill, reserved for hero send/share actions.</li>
        <li><b>ghost</b> — no border, low-emphasis inline actions.</li></ul>
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
      <dl class="spec" style="margin-top:var(--re-s4)"><dt>variant</dt><dd>default 34 | bare 30</dd><dt>danger</dt><dd>boolean</dd><dt>title</dt><dd class="am">required</dd></dl>
      <p class="body" style="margin:var(--re-s3) 0 0;font-size:12.5px">Icon-only, for the topbar and row actions. <span class="mono">title</span> is used both as tooltip and <span class="mono">aria-label</span>.</p></div></div>
    <div class="card"><div class="card-h"><span class="tt">Toggle</span></div><div class="card-b">
      <div class="stage col" style="gap:var(--re-s3)">
        <div style="display:flex;align-items:center;gap:11px"><span class="tg"><i></i></span><span style="font-size:13px;color:var(--re-ink-3)">off · md 38</span></div>
        <div style="display:flex;align-items:center;gap:11px"><span class="tg on"><i></i></span><span style="font-size:13px;color:var(--re-ink-2)">on · charcoal track</span></div>
        <div style="display:flex;align-items:center;gap:11px"><span class="tg on am"><i></i></span><span style="font-size:13px;color:var(--re-ink-2)">on · amber accent</span></div>
        <div style="display:flex;align-items:center;gap:11px"><span class="tg sm on"><i></i></span><span style="font-size:13px;color:var(--re-ink-3)">sm 32 · table rows</span></div>
      </div>
      <dl class="spec" style="margin-top:var(--re-s4)"><dt>pressed / defaultPressed</dt><dd>boolean</dd><dt>size</dt><dd>md | sm</dd><dt>accent</dt><dd>boolean</dd><dt>disabled</dt><dd>boolean</dd></dl></div></div>
    <div class="card"><div class="card-h"><span class="tt">SegmentedControl</span></div><div class="card-b">
      <div class="stage col"><div class="seg"><span class="on">Estimate</span><span>Formal</span></div>
        <div class="seg" style="margin-top:var(--re-s3)"><span class="on"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="18" height="16" rx="2"/></svg> Table</span><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="8" height="8" rx="1.5"/><rect x="13" y="4" width="8" height="8" rx="1.5"/></svg> Cards</span></div></div>
      <dl class="spec" style="margin-top:var(--re-s4)"><dt>options</dt><dd>2–3 items</dd><dt>value</dt><dd>string</dd><dt>onChange</dt><dd>(value) =&gt; void</dd></dl>
      <p class="body" style="margin:var(--re-s3) 0 0;font-size:12.5px">More than 3 options → use <span class="mono">Select</span>.</p></div></div>
  </div>

  <!-- Field / Input / Select / Textarea -->
  <h3 class="sub"><span class="tick"></span>Field · Input · Select · Textarea</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="tt">Anatomy and states</span><span class="hint">label + hint + control + note</span></div><div class="card-b stack">
      <div class="fld"><span class="lb">Tread width <span class="op">m</span></span><div class="ctl"><span class="val num">0,62</span><span class="sfx">m</span></div></div>
      <div class="fld"><span class="lb">Labour day rate</span><div class="ctl foc"><span class="pfx">$</span><span class="val num r">14.360</span></div>
        <div class="note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg> Reference and control — it does not set the price.</div></div>
      <div class="fld"><span class="lb">Family</span><div class="ctl sel">Stairs <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></div></div>
      <div class="fld"><span class="lb">Search</span><div class="ctl ph"><svg style="width:16px;height:16px;color:var(--re-ink-4);flex:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg><span class="val">Search by number, client, project…</span></div></div>
      <div class="fld"><span class="lb">Notes <span class="op">optional</span></span><div class="ctl ta ph"><span class="val">Add notes for the client…</span></div></div>
      <div class="fld"><span class="lb">Locked by freeze</span><div class="ctl dis"><span class="val">v37377</span></div></div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <div class="scrollx"><table class="tb dense">
        <thead><tr><th>Export</th><th>Relevant props</th></tr></thead>
        <tbody>
          <tr><td>Field</td><td class="mono">label · optional · note · htmlFor</td></tr>
          <tr><td>Input</td><td class="mono">prefix · suffix · leadIcon · num</td></tr>
          <tr><td>Select</td><td class="mono">children (&lt;option&gt;) · chevron propio</td></tr>
          <tr><td>Textarea</td><td class="mono">nativo · resize vertical</td></tr>
        </tbody></table></div>
      <h4 class="mini">Behaviour</h4>
      <ul class="body" style="margin:0"><li><b>Field</b> wraps: label + inline hint (“optional”) + control + note with icon.</li>
        <li><b>num</b> switches on tabular figures and alignment for money and measurements.</li>
        <li><b>prefix/suffix</b> for <span class="mono">$</span> and units (<span class="mono">m</span>, <span class="mono">%</span>).</li>
        <li>Focus: amber border + <span class="mono">--re-ring</span>. Disabled: opacity .45.</li></ul>
      <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑06</span><p>There is no <b>error/invalid</b> prop on <span class="mono">Field</span> or <span class="mono">Input</span>. The product's validation is warn-only via <span class="mono">Alert</span>, so field-level inline errors are <b>undefined</b> in the system.</p></div>
    </div></div>
  </div>

  <!-- Badge / StatusBadge -->
  <h3 class="sub"><span class="tick"></span>Badge · StatusBadge</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="tt">Tones and shapes</span></div><div class="card-b">
      <div class="stage"><span class="bdg">neutral</span><span class="bdg ok">ok</span><span class="bdg wn">warn</span><span class="bdg cr">crit</span><span class="bdg inf">info</span></div>
      <div class="stage" style="margin-top:var(--re-s3)"><span class="bdg"><span class="d"></span> with dot</span><span class="bdg sq">ESC-RECT</span><span class="bdg sq">GV-A-00148</span></div>
      <div class="stage" style="margin-top:var(--re-s3)"><span class="stage-lab" style="width:100%;margin:0 0 4px">StatusBadge — lifecycle</span> <span class="st"><span class="d d-draft"></span> Draft</span><span class="st"><span class="d d-sent"></span> Sent</span><span class="st"><span class="d d-appr"></span> Approved</span><span class="st"><span class="d d-rej"></span> Rejected</span><span class="st"><span class="d d-cancel"></span> Cancelled</span></div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <dl class="spec"><dt>Badge · tone</dt><dd>neutral | ok | warn | crit | info</dd><dt>Badge · dot</dt><dd>boolean</dd><dt>Badge · square</dt><dd>mono code chip</dd><dt>Badge · icon</dt><dd>ReactNode</dd><dt>StatusBadge · status</dt><dd>draft | sent | appr | rej | cancel</dd><dt>StatusBadge · label</dt><dd>overrides the default label</dd></dl>
      <h4 class="mini">Usage</h4>
      <ul class="body" style="margin:0"><li><b>StatusBadge</b> maps dot colour + written label automatically. Do not assemble the pill by hand.</li>
        <li><b>square</b> for codes (<span class="mono">GV-A-00148</span>, <span class="mono">ESC-RECT</span>): mono, 5px radius.</li>
        <li>State is never communicated by colour alone: always dot + text.</li></ul>
    </div></div>
  </div>

  <!-- Alert -->
  <h3 class="sub"><span class="tick"></span>Alert · AlertGroup</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="tt">Tones</span><span class="hint">warn is the default — the engine never blocks</span></div><div class="card-b stack">
      <div class="al"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4l9 16H3z"/><path d="M12 10v4M12 17h.01"/></svg><div><b>Pitch outside the comfortable range</b>Check height and run. It is a warning: it does not block the calculation.</div></div>
      <div class="al ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg><div><b>Recommended pitch</b>≈ 14 steps · 17.4 cm riser · 34°</div></div>
      <div class="al inf"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><div>Changes apply only to new calculations.</div></div>
      <div class="al cr"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/></svg><div>The parameter version needed to freeze the quote is missing.</div></div>
      <div class="al nt"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 11V7a5 5 0 0 1 10 0v4"/><rect x="5" y="11" width="14" height="9" rx="2"/></svg><div>These are not included in the commercial output sent to the client.</div></div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <dl class="spec"><dt>Alert · tone</dt><dd>warn | ok | info | crit | neutral</dd><dt>Alert · icon</dt><dd>ReactNode · null hides it</dd><dt>Alert · title</dt><dd>bold lead line</dd><dt>AlertGroup · title</dt><dd>“Alerts and reminders”</dd><dt>AlertGroup · items</dt><dd>ReactNode[]</dd></dl>
      <h4 class="mini">Behaviour</h4>
      <ul class="body" style="margin:0"><li>The engine is <b>warn-only and never blocks</b>; that is why <span class="mono">warn</span> is the default tone and the triangle is the default icon.</li>
        <li><b>AlertGroup</b> is the titled reminders panel with bullets.</li>
        <li>Microcopy: info/lock icon + a single sentence.</li></ul>
      <h4 class="mini">Tokens</h4>
      <div class="mono" style="font-size:11.5px;color:var(--re-amber-ink);line-height:1.7">--re-warn-soft/-line · --re-ok-soft/-line · --re-info-soft/-line · --re-crit-soft/-line · --re-r</div>
    </div></div>
  </div>

  <!-- Card family -->
  <h3 class="sub"><span class="tick"></span>Card · CardHead · CardBody · SectionHead</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/></svg></span><span class="tt">Quote header</span><span class="hint">optional</span><span class="r"><button class="b sm">Collapse</button></span></div>
      <div class="card-b">
        <p class="body" style="margin:0 0 var(--re-s4)">This card <b>is</b> the specimen: <span class="mono">CardHead</span> (icon tile + title + hint + actions) over a divider, then <span class="mono">CardBody</span>.</p>
        <div class="stage col"><div class="sech"><span class="ix">1</span><span class="tt">Product</span></div>
          <div class="sech" style="margin-top:var(--re-s4)"><span class="ix">2</span><span class="tt">Dimensions</span><span class="hn">decimals with a comma</span></div>
          <div class="sech" style="margin-top:var(--re-s4)"><span class="ix">3</span><span class="tt">Construction options</span></div>
          <div class="stage-lab" style="margin:var(--re-s3) 0 0">SectionHead — numbered steps inside the calculator / editor</div></div>
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
      <h4 class="mini">Anatomy</h4>
      <p class="body" style="margin:0">Graphite surface, 1px <span class="mono">--re-line</span> border, <span class="mono">--re-shadow-sm</span>, <span class="mono">--re-r-lg 13px</span> radius. <span class="mono">flush</span> when the card wraps a full-bleed table.</p>
      <h4 class="mini">Do / Don't</h4>
      <ul class="body" style="margin:0"><li><b>Do</b> — use a numbered <span class="mono">SectionHead</span> for the steps of a long flow (calculator, editor).</li>
        <li><b>Don't</b> — nest cards inside cards; to group content within a card use <span class="mono">SectionHead</span> + dividers.</li></ul>
    </div></div>
  </div>

  <!-- StatTile -->
  <h3 class="sub"><span class="tick"></span>StatTile</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <div class="grid2" style="gap:var(--re-s3)">
        <div class="stt"><div class="k">Quotes this month</div><div class="v">24</div><div class="s">+3 vs. last month</div></div>
        <div class="stt"><div class="k">Approved</div><div class="v">9</div><div class="s">37.5% conversion</div></div>
        <div class="stt acc"><div class="k">Quoted amount</div><div class="v am">$2.480.000</div><div class="s">month to date</div></div>
        <div class="stt acc"><div class="k">Engine</div><div class="v" style="font-size:18px;display:flex;align-items:center;gap:9px"><span style="width:7px;height:7px;border-radius:50%;background:var(--re-ok-bright)"></span> v1.5</div><div class="s">QA up to date</div></div>
      </div></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <dl class="spec"><dt>label</dt><dd>uppercase</dd><dt>value</dt><dd>tabular</dd><dt>sub</dt><dd>supporting line</dd><dt>accent</dt><dd>charcoal variant</dd><dt>dot</dt><dd>live status dot</dd><dt>amberValue</dt><dd>figure in amber</dd></dl>
      <h4 class="mini">Usage</h4>
      <ul class="body" style="margin:0"><li>Dashboard KPI row and status grids.</li>
        <li><b>accent</b> is for <b>one single</b> highlighted tile per row: it is the charcoal anchor.</li>
        <li><b>amberValue</b> sparingly: headline figures only (it respects amber rationing).</li>
        <li><b>dot</b> combines with <span class="mono">accent</span> for “Live” panels.</li></ul>
    </div></div>
  </div>

  <!-- TechnicalPreviewCard -->
  <h3 class="sub"><span class="tick"></span>TechnicalPreviewCard · TechnicalRenderers</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <p class="body" style="margin:0 0 var(--re-s4)">The <b>only sanctioned drawing</b> in the system: schematic plans and elevations generated parametrically from the quote’s own dimensions. They are measured diagrams, with 1px graphite structure lines, token-driven dimension overlays and amber reserved for the entry/railing cue.</p>
      <h4 class="mini">Registry</h4>
      <p class="body" style="margin:0">Registry-based: <span class="mono">family</span>/<span class="mono">variant</span> select a renderer. Stairs ships four: <span class="mono">straight</span> (side elevation), <span class="mono">L</span>, <span class="mono">U</span> and <span class="mono">spiral</span> (plan views). Unknown families fall back to a “no technical view” placeholder.</p>
      <h4 class="mini">Permitted use</h4>
      <ul class="body" style="margin:0"><li>Calculator and editor previews, history rows, the client-facing document.</li>
        <li><b>Never</b> hand-draw decorative product art.</li></ul>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">API</h4>
      <dl class="spec"><dt>family</dt><dd>Stairs | others → placeholder</dd><dt>variant</dt><dd>straight | L | U | spiral</dd><dt>dims</dt><dd>width · height · run · steps · tread · riser · diameter</dd><dt>config</dt><dd>string[] of chips</dd><dt>size</dt><dd>compact | standard | large</dd><dt>title / code</dt><dd>override · mono code</dd><dt>showDims</dt><dd>default true</dd><dt>live</dt><dd>“Live” indicator</dd></dl>
      <h4 class="mini">Densities</h4>
      <ul class="body" style="margin:0"><li><b>compact</b> — head + drawing + footer.</li>
        <li><b>standard</b> — + 4 dimensions + chips.</li>
        <li><b>large</b> — bigger stage + 6 dimensions.</li></ul>
      <h4 class="mini">Extending</h4>
      <p class="body" style="margin:0"><span class="mono">TechnicalRenderers.register(family, variant, def)</span> · <span class="mono">setFamilyLabel</span> · <span class="mono">has</span> · <span class="mono">variants</span> · <span class="mono">families</span>, all chainable. <span class="mono">escalones</span> is clamped to 4–16; a chip matching <span class="mono">/baranda|pasamano/</span> draws the railing on <span class="mono">recta</span>.</p>
    </div></div>
  </div>
</section>

<!-- ============================================================ 03 -->
<section class="sec" id="s03">
  <div class="sec-h"><span class="num-badge">03</span><h2>Navigation &amp; App Shell</h2></div>
  <p class="lede">The desktop shell is the primary layer and has been frozen since V1: a 248px sticky sidebar + a 60px sticky topbar + a scrolling main. Mobile does not compress that shell: it changes pattern.</p>

  <h3 class="sub"><span class="tick"></span>Desktop — AppShell</h3>
  <div class="grid2">
    <div class="card"><div class="card-h"><span class="tt">Sidebar · LogoMark · NavGroup · NavItem</span><span class="hint">248px</span></div>
      <div class="card-b" style="background:var(--re-surface);padding:var(--re-s4)">
        <div style="display:flex;align-items:center;gap:12px;padding:6px 8px var(--re-s5)"><div class="lm">RE</div><div><div style="font-weight:600;font-size:14.5px;line-height:1.2">Presupuestador</div><div style="font-size:11px;color:var(--re-ink-4);font-weight:500">RE · v1.0</div></div></div>
        <div style="font-size:10px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--re-ink-4);padding:0 12px 8px">Operations</div>
        <div class="navi on"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2.5"/><path d="M8 7h8M8 11h3" stroke-linecap="round"/><rect x="13.5" y="11" width="3" height="6" rx="1"/></svg> Calculator</div>
        <div class="navi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round"/></svg> History</div>
        <div style="font-size:10px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--re-ink-4);padding:var(--re-s4) 12px 8px">Configuration</div>
        <div class="navi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 2.5l8 4.2v10.6L12 21.5 4 17.3V6.7z"/><path d="M4 6.7l8 4.3 8-4.3M12 11v10.5"/></svg> Catalogues</div>
        <div class="navi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M4 7h10M18 7h2M4 17h2M10 17h10"/><circle cx="16" cy="7" r="2.3"/><circle cx="8" cy="17" r="2.3"/></svg> Parameters</div>
        <div class="navi"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4" stroke-linecap="round"/></svg> QA</div>
      </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Topbar · Breadcrumbs · UserChip</h4>
      <div class="stage col" style="gap:var(--re-s4)">
        <div style="display:flex;align-items:center;gap:14px"><div class="crumb"><span>Operations</span><span class="sp">/</span><b>Calculator</b><span class="mono" style="font-size:12px;color:var(--re-ink-4)">(unsaved)</span></div></div>
        <div style="display:flex;gap:10px;align-items:center"><span class="ib" title="Search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></span> <span class="uchip"><span class="av">RE</span><span class="w"><b>Ramiro</b><span>Estimator</span></span></span></div>
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
      <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑02</span><p><span class="mono">LogoMark</span> has <b>the default tagline <span class="mono">'RE · v1.0'</span></b>, but the kit screens and the mockups show <span class="mono">“REstimator · motor v1.5”</span>. The default needs to be unified, or passed explicitly every time.</p></div>
    </div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Mobile — bottom navigation + compact header</h3>
  <p class="body">Mobile is <b>designed, not adapted</b>: the sidebar is not “hidden”, it is replaced by a bottom tab bar of 5 families. Breadcrumbs disappear (title + back replace them) and branding appears only once per header.</p>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Element</th><th>Size</th><th>Behaviour</th><th>Status in the DS</th></tr></thead>
    <tbody>
      <tr><td>BottomTabBar</td><td class="mono">h 64 + safe-area</td><td>fixed bottom. 5 tabs: Home · Calc · History · Catalogues · Params. Icons 21, label 10. Active <span class="mono">--re-amber-ink</span>.</td><td><span class="tag new">New</span></td></tr>
      <tr><td>MobileHeader</td><td class="mono">h ~62</td><td>sticky top. Back + title (Calculator) or brand + title (Home / History). No breadcrumbs, no sub-copy.</td><td><span class="tag new">New</span></td></tr>
      <tr><td>BottomActionBar</td><td class="mono">h 62 · bottom 64</td><td>fixed above the tab bar. Calculator only: Save (primary) · Estimate · Quote.</td><td><span class="tag new">New</span></td></tr>
      <tr><td>AppShell (mobile mode)</td><td class="mono">—</td><td>Same route tree, three navigation modes (sidebar / tabs / bottom bar).</td><td><span class="tag ext">Extended</span></td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Sticky / fixed behaviour</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Layer (bottom to top)</th><th>Height</th><th>Position</th><th>z-index</th><th>Where</th></tr></thead>
    <tbody>
      <tr><td>Bottom tab bar</td><td class="mono">64</td><td class="mono">fixed bottom 0</td><td class="mono">40</td><td>Mobile</td></tr>
      <tr><td>Action bar</td><td class="mono">62</td><td class="mono">fixed bottom 64</td><td class="mono">35</td><td>Mobile · Calculator</td></tr>
      <tr><td>Compact header</td><td class="mono">~62</td><td class="mono">sticky top 0</td><td class="mono">30</td><td>Mobile</td></tr>
      <tr><td>Recommended summary</td><td class="mono">auto ~108</td><td class="mono">sticky bajo header</td><td class="mono">28</td><td>Mobile · Calculator</td></tr>
      <tr><td>Bottom sheet (breakdown)</td><td class="mono">max 78%</td><td class="mono">overlay + scrim</td><td class="mono">60</td><td>Mobile · Tablet</td></tr>
      <tr><td>Sidebar / Topbar</td><td class="mono">248 / 60</td><td class="mono">sticky</td><td class="mono">—</td><td>Desktop</td></tr>
      <tr><td>Result panel</td><td class="mono">380 col</td><td class="mono">sticky lateral</td><td class="mono">—</td><td>Desktop · Calculator</td></tr>
      <tr><td>Save bar</td><td class="mono">auto</td><td class="mono">fixed bottom · shadow-bar</td><td class="mono">—</td><td>Desktop · Editor / Parameters</td></tr>
    </tbody>
  </table></div>
</section>

<!-- ============================================================ 04 -->
<section class="sec" id="s04">
  <div class="sec-h"><span class="num-badge">04</span><h2>Forms &amp; Data Entry</h2></div>
  <p class="lede">Forms are the heart of the product: the calculator is a long form with a live result. Data entry favours typing speed and tabular reading over ornament.</p>

  <h3 class="sub"><span class="tick"></span>Control inventory</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Control</th><th>Export</th><th>Variants / sizes</th><th>Defined states</th></tr></thead>
    <tbody>
      <tr><td>Text input</td><td class="mono">Input</td><td>prefix · suffix · leadIcon · num</td><td>default · focus · disabled · placeholder</td></tr>
      <tr><td>Select</td><td class="mono">Select</td><td>native + custom chevron</td><td>default · focus · disabled</td></tr>
      <tr><td>Textarea</td><td class="mono">Textarea</td><td>vertical resize</td><td>default · focus · disabled</td></tr>
      <tr><td>Search</td><td class="mono">Input + leadIcon</td><td>a pattern, not a separate component</td><td>default · focus · with a value</td></tr>
      <tr><td>Toggle</td><td class="mono">Toggle</td><td>md 38 · sm 32 · accent</td><td>on · off · disabled</td></tr>
      <tr><td>Segmented</td><td class="mono">SegmentedControl</td><td>2–3 options, with or without icon</td><td>selected · unselected</td></tr>
      <tr><td>Field wrapper</td><td class="mono">Field</td><td>label · optional · note</td><td>—</td></tr>
      <tr><td>Checkbox · Radio</td><td class="mono">—</td><td colspan="2"><b>They do not exist as components.</b> The product resolves choice with <span class="mono">Select</span>, <span class="mono">SegmentedControl</span> and <span class="mono">Toggle</span>. See <a href="#needs-review">NR‑13</a>.</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Control states</h3>
  <div class="card"><div class="card-b">
    <div class="grid3">
      <div><div class="stage-lab">Default</div><div class="ctl"><span class="val num">2,43</span><span class="sfx">m</span></div></div>
      <div><div class="stage-lab">Focus — amber border + 3px ring</div><div class="ctl foc"><span class="val num">2,43</span><span class="sfx">m</span></div></div>
      <div><div class="stage-lab">Disabled — opacity .45</div><div class="ctl dis"><span class="val num">2,43</span><span class="sfx">m</span></div></div>
      <div><div class="stage-lab">Placeholder — ink-4</div><div class="ctl ph"><span class="val">0,00</span><span class="sfx">m</span></div></div>
      <div><div class="stage-lab">With currency prefix</div><div class="ctl"><span class="pfx">$</span><span class="val num r">14.360</span></div></div>
      <div><div class="stage-lab">Select</div><div class="ctl sel">Montevideo <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg></div></div>
    </div>
    <h4 class="mini">Hover · focus · pressed · disabled — global rules</h4>
    <div class="scrollx"><table class="tb dense">
      <thead><tr><th>State</th><th>Treatment</th></tr></thead>
      <tbody>
        <tr><td>Hover</td><td>fill to <span class="mono">--re-surface-2</span> + border to <span class="mono">--re-line-strong</span> (or amber-darken on amber CTAs)</td></tr>
        <tr><td>Focus</td><td>3px amber ring (<span class="mono">--re-ring</span>) + amber border</td></tr>
        <tr><td>Pressed / active</td><td>toggles and active pages <b>flip to amber</b></td></tr>
        <tr><td>Disabled</td><td>opacity <span class="mono">.45</span>, no events</td></tr>
        <tr><td>Transition</td><td><span class="mono">.12–.15s ease</span> on background / border. No shrink, no scale.</td></tr>
      </tbody></table></div>
  </div></div>

  <h3 class="sub"><span class="tick"></span>Validation · errors · success</h3>
  <p class="body">The calculation engine is <b>warn-only and never blocks</b>. That product decision defines all validation: warnings are grouped as an <span class="mono">Alert</span> next to the result, not as field errors that stop you from moving on.</p>
  <div class="grid2">
    <div class="card"><div class="card-b stack">
      <div class="stage-lab" style="margin:0">Warning — does not block</div>
      <div class="al"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4l9 16H3z"/><path d="M12 10v4M12 17h.01"/></svg><div><b>Run outside the comfortable range</b>You can continue: the calculation still runs.</div></div>
      <div class="stage-lab" style="margin:var(--re-s3) 0 0">Success / calm confirmation</div>
      <div class="al ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg><div>Saved to history · <span class="mono">GV-A-00159</span></div></div>
      <div class="stage-lab" style="margin:var(--re-s3) 0 0">Field note — icon + one sentence</div>
      <div class="note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 11V7a5 5 0 0 1 10 0v4"/><rect x="5" y="11" width="14" height="9" rx="2"/></svg> These are not included in the commercial output sent to the client.</div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Data entry pattern — long forms</h4>
      <ul class="body" style="margin:0"><li><b>Numbered steps</b> with <span class="mono">SectionHead</span>: Product → Dimensions → Construction options → Finish / installation / zone.</li>
        <li><b>One card per step</b>; a 2–3 column grid on desktop, 1–2 on tablet, 1 on mobile.</li>
        <li><b>Units always visible</b> as a <span class="mono">suffix</span>, never only in the label.</li>
        <li><b>Decimal comma</b> on measurements — the step hint is a reminder (“decimals with a comma”).</li>
        <li><b>Live recalculation</b>: there is no “calculate” button; the result panel shows the “Live” dot.</li>
        <li><b>Optional fields</b> marked with an inline <span class="mono">optional</span> hint, rather than an asterisk on the required ones.</li></ul>
      <h4 class="mini">Save bar</h4>
      <p class="body" style="margin:0">The Editor and Parameters screens use a fixed bottom bar with a change count (“1 local calibration change”) + <span class="mono">Restore defaults</span> + <span class="mono">Save changes</span>. Shadow cast upwards (<span class="mono">--re-shadow-bar</span>).</p>
    </div></div>
  </div>
</section>

<!-- ============================================================ 05 -->
<section class="sec" id="s05">
  <div class="sec-h"><span class="num-badge">05</span><h2>Data Display</h2></div>
  <p class="lede">History is the main data surface: a dense table, filters that work, state as a coloured dot on a neutral pill, and row actions revealed on hover.</p>

  <h3 class="sub"><span class="tick"></span>Table · TableToolbar · RowActions · Pagination</h3>
  <div class="card" style="overflow:hidden">
    <div class="tbar"><span class="tt">Saved quotes</span><span class="ct">58 results</span> <span class="r"><button class="b sm">Export</button><button class="b sm">Columns</button></span></div>
    <div class="scrollx"><table class="dt-tb">
      <thead><tr><th>Code</th><th>Client / project</th><th>Family</th><th>State</th><th class="r" style="text-align:right">Amount</th><th></th></tr></thead>
      <tbody>
        <tr><td class="mono" style="font-size:11.5px;color:var(--re-ink-3)">GV-A-00159</td>
          <td><div class="strong">Balcony railing</div><div class="sub">Pedro Núñez · 15/06/2026</div></td>
          <td>Railings</td><td><span class="st"><span class="d d-draft"></span> Draft</span></td>
          <td class="r strong">$96.000</td>
          <td><div style="display:flex;gap:4px;justify-content:flex-end"><span class="ib bare" title="Editar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9a2.1 2.1 0 0 0-3-3L5 17z"/></svg></span><span class="ib bare" title="Duplicar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg></span></div></td></tr>
        <tr><td class="mono" style="font-size:11.5px;color:var(--re-ink-3)">EST-2026-014</td>
          <td><div class="strong">Balcony railing — initial</div><div class="sub">Pedro Núñez · 13/06/2026</div></td>
          <td>Railings</td><td><span class="st"><span class="d d-sent"></span> Sent</span></td>
          <td class="r strong">$96.000</td><td></td></tr>
        <tr><td class="mono" style="font-size:11.5px;color:var(--re-ink-3)">EST-2026-013</td>
          <td><div class="strong">Straight staircase — first contact</div><div class="sub">Valentina Cruz · 12/06/2026</div></td>
          <td>Stairs</td><td><span class="st"><span class="d d-appr"></span> Approved</span></td>
          <td class="r strong">$175.000</td><td></td></tr>
      </tbody></table></div>
    <div style="display:flex;align-items:center;gap:14px;padding:13px 15px;border-top:1px solid var(--re-line)"><span style="font-size:12.5px;color:var(--re-ink-4)">Showing 1–12 of 58</span> <span class="pgn" style="margin-left:auto"><span class="pg">‹</span><span class="pg on">1</span><span class="pg">2</span><span class="pg">3</span><span class="pg dots">…</span><span class="pg">5</span><span class="pg">›</span></span></div>
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
      <ul class="body" style="margin:0"><li>Header classes: <span class="mono">is-right</span> · <span class="mono">is-center</span> · <span class="mono">is-sortable</span>.</li>
        <li>Cell classes: <span class="mono">re-cell-strong</span> (primary) · <span class="mono">re-cell-sub</span> (secondary line).</li>
        <li><b>Wrap in <span class="mono">&lt;Card flush&gt;</span></b>: the card clips the table’s overflow.</li>
        <li><span class="mono">RowActions</span> is revealed on row hover.</li></ul></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Amounts and figures</h4>
      <ul class="body" style="margin:0 0 var(--re-s4)"><li>The amount column is right-aligned, <span class="mono">tabular-nums</span>, weight 600.</li>
        <li>Code in mono at 11.5px, colour <span class="mono">--re-ink-3</span>.</li>
        <li>Two-line row: primary (<span class="mono">re-cell-strong</span>) + secondary client · date.</li></ul>
      <h4 class="mini">Empty state — the product's real pattern</h4>
      <div class="al nt"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg><div>Only saved quotes are listed. There are no hard deletes: the state is changed softly.</div></div>
      <p class="body" style="margin:var(--re-s3) 0 0;font-size:12.5px">Empty states <b>explain the rule</b>, they do not just say “no results”.</p>
      <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑07</span><p>There is no <b>loading / skeleton</b> component in the system, and empty states exist only as copy inside screens, not as a reusable component.</p></div>
    </div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Cards, tiles and badges as display</h3>
  <p class="body">Already documented in §02: the <span class="mono">Card</span> family for containers, <span class="mono">StatTile</span> for KPIs and status grids, <span class="mono">Badge</span>/<span class="mono">StatusBadge</span> for labels, codes and lifecycle, <span class="mono">TechnicalPreviewCard</span> for the product's schematic drawing.</p>
</section>

<!-- ============================================================ 06 -->
<section class="sec" id="s06">
  <div class="sec-h"><span class="num-badge">06</span><h2>Product Patterns</h2></div>
  <p class="lede">Patterns specific to REstimator. They are not components: they are compositions with business meaning, and several are governed by the non-negotiable rules in §00.</p>

  <h3 class="sub"><span class="tick"></span>Recommended value and ranges</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <div style="background:var(--re-charcoal);border:1px solid var(--re-charcoal-line);border-radius:var(--re-r);padding:var(--re-s5);text-align:center">
        <div style="display:flex;align-items:center;justify-content:center;gap:7px;font-size:10.5px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--re-on-dark-3)"><svg style="width:13px;height:13px;color:var(--re-amber)" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 18.9 6.1 22l1.2-6.5L2.5 9.4l6.6-.9z"/></svg> Recommended</div>
        <div class="num" style="font-size:40px;font-weight:700;letter-spacing:-.02em;line-height:1.05;margin:6px 0 2px;color:var(--re-amber-bright)">$135.599</div>
        <div style="font-size:11.5px;color:var(--re-on-dark-3)">fabrication value · + VAT</div></div>
      <div style="margin-top:var(--re-s5)">
        <div style="display:flex;justify-content:space-between;align-items:flex-end">
          <div><div style="font-size:10px;letter-spacing:.04em;text-transform:uppercase;color:var(--re-ink-4)">Minimum</div><div class="num" style="font-size:16px;font-weight:600">$127.463</div></div>
          <div style="text-align:right"><div style="font-size:10px;letter-spacing:.04em;text-transform:uppercase;color:var(--re-ink-4)">Maximum</div><div class="num" style="font-size:16px;font-weight:600">$143.735</div></div></div>
        <div style="position:relative;height:6px;border-radius:4px;background:var(--re-surface-3);margin-top:12px">
          <div style="position:absolute;left:8%;right:8%;top:0;bottom:0;border-radius:4px;background:var(--re-line-strong)"></div>
          <div style="position:absolute;left:46%;top:-3px;width:3px;height:12px;border-radius:2px;background:var(--re-amber)"></div></div>
      </div></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Pattern rules</h4>
      <ul class="body" style="margin:0"><li>The <b>recommended value</b> is the only amber figure on the screen and sits on the deepest charcoal.</li>
        <li>It is always shown <b>with its range</b> (min / max) and a pin on the track: the number never appears alone.</li>
        <li>The price comes from calibrated <span class="mono">Fab_min/Fab_max</span>, <b>not</b> from labour days × rate.</li>
        <li>Always labelled <b>“fabrication value · + VAT”</b>: it is never implied to be a final price.</li>
        <li>The panel carries the “Live” dot: continuous recalculation, no calculate button.</li></ul>
      <h4 class="mini">Labour days — a control, not a price</h4>
      <div class="stage tight" style="gap:8px">
        <div style="flex:1;border:1px solid var(--re-line);border-radius:var(--re-r-sm);padding:9px 6px;text-align:center;background:var(--re-surface)"><div class="num" style="font-size:18px;font-weight:700">5,3</div><div style="font-size:9.5px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--re-ink-4);margin-top:2px">Min</div></div>
        <div style="flex:1;border:1px solid var(--re-amber-line);border-radius:var(--re-r-sm);padding:9px 6px;text-align:center;background:var(--re-amber-soft)"><div class="num" style="font-size:18px;font-weight:700;color:var(--re-amber-ink)">6,5</div><div style="font-size:9.5px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--re-ink-4);margin-top:2px">Rec.</div></div>
        <div style="flex:1;border:1px solid var(--re-line);border-radius:var(--re-r-sm);padding:9px 6px;text-align:center;background:var(--re-surface)"><div class="num" style="font-size:18px;font-weight:700">7,7</div><div style="font-size:9.5px;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--re-ink-4);margin-top:2px">Max</div></div>
      </div>
      <div class="note" style="margin-top:var(--re-s3)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg> Workload check — <b style="color:var(--re-ink-3)">it does not set the price</b>. Literal product copy.</div>
    </div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Result steps</h3>
  <p class="body">The result panel always presents the same numbered steps, in the same order, across all three breakpoints:</p>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Step</th><th>Block</th><th>What it communicates</th></tr></thead>
    <tbody>
      <tr><td class="mono">1</td><td>Estimated fabrication</td><td>Min–max range + track with the recommended pin.</td></tr>
      <tr><td class="mono">—</td><td>Recommended</td><td>Charcoal block with the amber figure. The visual anchor.</td></tr>
      <tr><td class="mono">2</td><td>Labour days · workforce</td><td>Min / Rec. / Max as a workload check.</td></tr>
      <tr><td class="mono">3</td><td>Installation (suggested)</td><td>Range + calculation basis (~10% of fabrication) + zone.</td></tr>
      <tr><td class="mono">4</td><td>Subtotal</td><td>Fabrication + installation → subtotal, with an explicit “+ VAT”.</td></tr>
      <tr><td class="mono">5</td><td>Override</td><td>Manual adjustment of the final price, always visible as a separate field.</td></tr>
      <tr><td class="mono">6</td><td>Alerts</td><td>A warn-only <span class="mono">AlertGroup</span> at the foot of the panel.</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Creating and editing quotes</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Calculator flow</h4>
      <ul class="body" style="margin:0"><li><b>1 Product</b> — family → subcategory (10-category taxonomy).</li>
        <li><b>2 Dimensions</b> — measurements in metres, decimal comma.</li>
        <li><b>3 Construction options</b> — structure, riser, anti-slip, railing.</li>
        <li><b>4 Finish · installation · zone</b> — DTM, installation type, geographic zone.</li>
        <li>The quote header is <b>optional and collapsible</b>: it does not steal focus from the form.</li></ul>
      <h4 class="mini">Classification</h4>
      <p class="body" style="margin:0">Families → subcategories is the backbone. <span class="mono">TaxonomyIcon</span> accompanies each category in rails, dropdowns, catalogue cards and chips.</p>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Lifecycle and actions</h4>
      <ul class="body" style="margin:0"><li><b>States</b>: Draft → Sent → Approved / Rejected / Cancelled.</li>
        <li><b>No hard deletes</b>: the state change is soft and reversible.</li>
        <li><b>Duplicate</b> is first-class: picking up a previous quote is the most common path.</li>
        <li><b>Freeze</b>: on publication, the quote freezes its version of the parameters and becomes immutable.</li>
        <li><b>Override</b>: the manual adjustment is recorded; it does not silently replace the recommended value.</li></ul>
      <h4 class="mini">Filters and search</h4>
      <ul class="body" style="margin:0"><li>Search by number, client or project in a single input with a <span class="mono">leadIcon</span>.</li>
        <li>Type/state filters on one line; on mobile they scroll horizontally, with a solid amber active state.</li>
        <li>The result count is always visible (“58 results”).</li></ul>
    </div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>The client-facing document — the only light surface</h3>
  <p class="body">The client summary is rendered as <b>a real sheet of white paper</b> (it is the printable / WhatsApp / PDF artifact) resting on the dark workbench; its total row is charcoal with the total in amber. Everything else in the product is dark. <b>Estimate</b> and <b>formal</b> modes via <span class="mono">SegmentedControl</span>. Formal third-person voice (“Please find attached an estimate…”), never informal address. It <b>never serialises ranges, labour days, margins or calibration</b> (rule 5).</p>

  <h3 class="sub"><span class="tick"></span>Voice and microcopy</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Rule</th><th>Application</th></tr></thead>
    <tbody>
      <tr><td>Language</td><td>Uruguayan Spanish, Rioplatense <i>voseo</i> in the imperative: <i>“Agregá notas…”, “buscá, filtrá y retomá”</i>. Never “añade/busca”.</td></tr>
      <tr><td>Tone</td><td>Plain, operational, trustworthy. Short declaratives. It explains why a number exists without hedging.</td></tr>
      <tr><td>Casing</td><td>Sentence case for everything readable. UPPERCASE + tracking only on small eyebrows.</td></tr>
      <tr><td>Person</td><td>The app speaks to you (the estimator). The client-facing document is formal and third-person.</td></tr>
      <tr><td>Emoji</td><td>Never. A “✓” in transient confirmations is the only decorative character.</td></tr>
      <tr><td>Help notes</td><td>Info/lock icon + a single sentence.</td></tr>
    </tbody>
  </table></div>
</section>

<!-- ============================================================ 07 -->
<section class="sec" id="s07">
  <div class="sec-h"><span class="num-badge">07</span><h2>Responsive System</h2></div>
  <p class="lede">The same system at three densities. Mobile is <b>not another product</b>: same tokens, same taxonomy, same result steps and same vocabulary, with the navigation and result pattern that suits the available space.</p>

  <h3 class="sub"><span class="tick"></span>Transformation by aspect</h3>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Aspect</th><th>Mobile · 390</th><th>Tablet · 834</th><th>Desktop · 1440</th></tr></thead>
    <tbody>
      <tr><td>Navigation</td><td>Bottom tab bar (5 families) + contextual title</td><td>Top family tabs under the header</td><td>Fixed 248px left sidebar</td></tr>
      <tr><td>Branding</td><td>RE monogram in the header (avatar = user, not brand)</td><td>Compact monogram + name</td><td>Logo + name + engine version in the sidebar</td></tr>
      <tr><td>Breadcrumbs</td><td>Hidden — replaced by title + back</td><td>Compact, optional</td><td>Complete, in the topbar</td></tr>
      <tr><td>Header</td><td>Compact sticky: back + title; no sub-copy</td><td>Sticky: title + action</td><td>Topbar + page head</td></tr>
      <tr><td>Result</td><td>Pinned summary on top + sheet on demand</td><td>Collapsible bottom sheet with a peek</td><td>380px sticky side panel</td></tr>
      <tr><td>Primary actions</td><td>Fixed bar above the tab bar</td><td>In the sheet's peek + inside the panel</td><td>Inside the result panel</td></tr>
      <tr><td>Sticky</td><td>Header + summary + action bar</td><td>Header + sheet peek</td><td>Result panel + save bar</td></tr>
      <tr><td>Forms</td><td>1 field per row; numbered sections with anchors</td><td>2-column grid with breathing room</td><td>2–3 column grid</td></tr>
      <tr><td>Density</td><td>Stacked cards, gap 14, padding 16</td><td>Intermediate, padding 22</td><td>Dense, padding 20/24, gap 16–20</td></tr>
      <tr><td>Tables / lists</td><td>Row = card; tap opens it; secondary actions in ⋯</td><td>Reduced table or cards depending on width</td><td>Full table + RowActions on hover</td></tr>
      <tr><td>Control</td><td class="mono">42px</td><td class="mono">42px</td><td class="mono">40px (--re-control-h)</td></tr>
    </tbody>
  </table></div>

  <h3 class="sub"><span class="tick"></span>Result pattern — a decision per breakpoint</h3>
  <p class="body">Four options were evaluated (sticky side · summarised top · bottom sheet · hybrid) and the <b>hybrid composition</b> was chosen: no single option wins at all three sizes, so the pattern adapts to the space. The recommended value stays <b>glanceable at all times</b>; the breakdown is <b>one tap away</b>.</p>
  <div class="grid3">
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Desktop ≥ 1200</h4><div style="font-size:15px;font-weight:600;margin-bottom:6px">Side panel</div>
      <p class="body" style="margin:0;font-size:13px">Horizontal space to spare: the full breakdown stays sticky beside the form, as it already worked.</p></div></div>
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Tablet 834</h4><div style="font-size:15px;font-weight:600;margin-bottom:6px">Bottom sheet</div>
      <p class="body" style="margin:0;font-size:13px">Form at a comfortable width; a bottom peek bar with the recommended value + range + CTA. It expands without covering fields.</p></div></div>
    <div class="card"><div class="card-b"><h4 class="mini" style="margin-top:0">Mobile 390</h4><div style="font-size:15px;font-weight:600;margin-bottom:6px">Summary on top + sheet</div>
      <p class="body" style="margin:0;font-size:13px">Pinned result header (recommended + range + size); “View breakdown” opens the sheet. Actions live in a fixed bar.</p></div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>Mobile v1 constants</h3>
  <div class="grid2">
    <div class="card"><div class="card-b"><dl class="spec">
      <dt>Base viewport</dt><dd class="am">390 × 844</dd><dt>Fluid range</dt><dd>360–430</dd><dt>Grid</dt><dd>4px (--re-s*)</dd><dt>Screen padding</dt><dd>16 (s4)</dd><dt>Gap between cards</dt><dd>14</dd><dt>Card</dt><dd>r13 · pad 16</dd><dt>Control</dt><dd>h42 · r7</dd><dt>Touch target</dt><dd class="am">≥ 44</dd><dt>Safe area</dt><dd class="mono">env(safe-area-inset-bottom)</dd></dl></div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Component adaptation rules</h4>
      <ul class="body" style="margin:0"><li><b>Nothing is merely “hidden”.</b> If an element does not fit, it is replaced by its touch equivalent.</li>
        <li><b>One dominant action per tap</b>: the whole row opens; secondary actions move to a ⋯ overflow.</li>
        <li><b>Chips on one line</b> with horizontal scroll; the active one is solid amber, never wrapping to two lines.</li>
        <li><b>No sub-copy in touch headers</b>: title + back and nothing else.</li>
        <li><b>One brand mark per header</b>: monogram = app, avatar = user.</li>
        <li>The bottom sheet carries <b>only</b> the breakdown: it never becomes navigation.</li></ul>
      <p class="body" style="margin:var(--re-s4) 0 0;font-size:12.5px">Full spec: <span class="art-name">Mobile v1 — Implementation spec</span></p>
    </div></div>
  </div>
  <div class="nr" style="margin-top:var(--re-s4)"><span class="id">NR‑12</span><p>The <b>tablet</b> layer is documented and specified, but <b>there is no tablet screen artifact</b> in <span class="mono">ui_kits/</span>. The tablet values come from the refinement mockups, not from a screen in the kit.</p></div>
</section>

<!-- ============================================================ 08 -->
<section class="sec" id="s08">
  <div class="sec-h"><span class="num-badge">08</span><h2>Screen Examples</h2><span class="cnt">5 desktop · 3 mobile</span></div>
  <p class="lede">The product screens built with the system. The five desktop ones come from the UI kit; the three mobile ones come from the Mobile v1 implementation spec. Each is captured at high resolution and can be opened to scroll through.</p>

  <h3 class="sub"><span class="tick"></span>Desktop — UI kit</h3>
  <div class="shots">
    <figure class="shot">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'calculator.webp' ); ?>"
        data-es-screen-w="2880" data-es-screen-h="3224" data-es-screen-cssw="1440"
        data-es-screen-name="Calculator"
        data-es-screen-meta="Calculator.html"
        aria-label="Open screen: Calculator">
        <span class="cap">
          <span class="nm">Calculator</span><span class="fl">Calculator.html</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'calculator-preview.webp' ); ?>"
               alt="Calculator — Presupuestador RE"
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
        data-es-screen-name="History"
        data-es-screen-meta="History.html"
        aria-label="Open screen: History">
        <span class="cap">
          <span class="nm">History</span><span class="fl">History.html</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'history-preview.webp' ); ?>"
               alt="History — Presupuestador RE"
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
        data-es-screen-name="Product editor"
        data-es-screen-meta="ProductEditor.html"
        aria-label="Open screen: Product editor">
        <span class="cap">
          <span class="nm">Product editor</span><span class="fl">ProductEditor.html</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'product-editor-preview.webp' ); ?>"
               alt="Product editor — Presupuestador RE"
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
        data-es-screen-name="Client summary"
        data-es-screen-meta="ClientSummary.html"
        aria-label="Open screen: Client summary">
        <span class="cap">
          <span class="nm">Client summary</span><span class="fl">ClientSummary.html</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'client-summary-preview.webp' ); ?>"
               alt="Client summary — Presupuestador RE"
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
        data-es-screen-name="Catalogues"
        data-es-screen-meta="Catalogs.html"
        aria-label="Open screen: Catalogues">
        <span class="cap">
          <span class="nm">Catalogues</span><span class="fl">Catalogs.html</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'catalogs-preview.webp' ); ?>"
               alt="Catalogues — Presupuestador RE"
               width="1520" height="1728" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
  </div>
  <p class="body" style="margin-top:var(--re-s4);font-size:12.5px;color:var(--re-ink-4)">Captures of the kit’s real screens. Click any of them to open it and scroll through the full screen.</p>

  <h3 class="sub"><span class="tick"></span>Mobile v1 — final screens</h3>
  <p class="body">Calculator, Home and History were frozen for implementation, with measurements, spacing, components and sticky behaviour per screen. They are documented in their own file so they are not duplicated here.</p>
  <div class="shots shots--mobile">
    <figure class="shot shot--mobile">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'mobile-calculator.webp' ); ?>"
        data-es-screen-w="1176" data-es-screen-h="2349" data-es-screen-cssw="392"
        data-es-screen-name="Calculator"
        data-es-screen-meta="390 · default — form + pinned summary"
        aria-label="Open screen: Calculator">
        <span class="cap">
          <span class="nm">Calculator</span><span class="fl">390 · default — form + pinned summary</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'mobile-calculator.webp' ); ?>"
               alt="Calculator — Presupuestador RE"
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
        data-es-screen-name="Home"
        data-es-screen-meta="390 · default"
        aria-label="Open screen: Home">
        <span class="cap">
          <span class="nm">Home</span><span class="fl">390 · default</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'mobile-home.webp' ); ?>"
               alt="Home — Presupuestador RE"
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
        data-es-screen-name="History"
        data-es-screen-meta="390 · default"
        aria-label="Open screen: History">
        <span class="cap">
          <span class="nm">History</span><span class="fl">390 · default</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'mobile-history.webp' ); ?>"
               alt="History — Presupuestador RE"
               width="1176" height="2349" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
  </div>

  <h3 class="sub"><span class="tick"></span>Colour exploration</h3>
  <p class="body" style="max-width:78ch">A colour remap tried while the system was being designed, kept as a record. The published system has one theme, dark; this comparison is not an available theme.</p>
  <div class="shots shots--single">
    <figure class="shot">
      <button type="button" class="vp"
        data-es-screen-trigger
        data-es-screen-src="<?php echo esc_url( $es_ds_screens . 'light-dark.webp' ); ?>"
        data-es-screen-w="2880" data-es-screen-h="6224" data-es-screen-cssw="1440"
        data-es-screen-name="Light / dark comparison"
        data-es-screen-meta="Light &amp; Dark Comparison.html"
        aria-label="Open screen: Light / dark comparison">
        <span class="cap">
          <span class="nm">Light / dark comparison</span><span class="art-kind">Exploration</span><span class="fl">Light &amp; Dark Comparison.html</span>
          <span class="r"><span class="zoom-hint">Expand</span></span>
        </span>
        <span class="vp-media">
          <img src="<?php echo esc_url( $es_ds_screens . 'light-dark-preview.webp' ); ?>"
               alt="Light / dark comparison — Presupuestador RE"
               width="1520" height="3285" loading="lazy" decoding="async">
          <span class="vp-expand" aria-hidden="true">
            <svg viewBox="0 0 20 20"><path d="M12 3h5v5M8 17H3v-5M17 3l-6 6M3 17l6-6"/></svg>
          </span>
        </span>
      </button>
    </figure>
  </div>

  <h3 class="sub"><span class="tick"></span>Supporting documentation</h3>
  <p class="lede">The documents where the things this system takes as settled were decided: audits, the V1 scope lock and the handoff package.</p>
  <div class="scrollx"><table class="tb">
    <thead><tr><th>Artifact</th><th>Type</th><th>What it contains</th></tr></thead>
    <tbody>
      <tr><td><span class="art-name">Refinamiento HiFi — Auditoría y Decisiones</span></td><td><span class="art-kind">Audit</span></td><td>Wireframe vs. hi-fi audit, UX and visual issues, A/B/C/D comparison of the result panel.</td></tr>
      <tr><td><span class="art-name">Typography Comparison</span></td><td><span class="art-kind">Audit</span></td><td>Hanken vs. Plus Jakarta against real screens — the decision to freeze Hanken.</td></tr>
      <tr><td><span class="art-name">V1 Design Freeze · Readiness Audit</span></td><td><span class="art-kind">Freeze</span></td><td>V1 scope lock, and the check that the system was ready to be frozen.</td></tr>
      <tr><td><span class="art-name">Accessibility Review · UX Review</span></td><td><span class="art-kind">Review</span></td><td>Results of the accessibility and UX reviews of the frozen kit.</td></tr>
      <tr><td><span class="art-name">Claude Code Handoff Package</span></td><td><span class="art-kind">Handoff</span></td><td>Handoff package for development.</td></tr>
    </tbody></table></div>
  <p class="body" style="margin-top:var(--re-s4);font-size:12.5px;color:var(--re-ink-4)">Listed as an inventory, not published. Names are each document’s original title, in the language it was written in.</p>
</section>

<!-- ============================================================ 09 -->
<section class="sec" id="s09">
  <div class="sec-h"><span class="num-badge">09</span><h2>Component Inventory</h2><span class="cnt">33 built · 6 specified</span></div>
  <p class="lede">The 33 components that were built and the 6 that were specified, with what each one covers today on desktop and on mobile. The <b>Status</b> column says where in the system it stands:</p>
  <dl class="ivt-legend"><div><dt><span class="tag stable">Stable</span></dt><dd>built and in use.</dd></div><div><dt><span class="tag ext">Extended</span></dt><dd>exists, and still needs mobile coverage.</dd></div><div><dt><span class="tag new">New</span></dt><dd>specified in Mobile v1, not built yet.</dd></div><div><dt><span class="tag rev">Needs review</span></dt><dd>has a documented inconsistency.</dd></div></dl>
  <div class="scrollx"><table class="tb dense">
    <thead><tr><th>Component</th><th>Category</th><th>Variants</th><th>States</th><th>Desktop</th><th>Mobile</th><th>Status</th><th>Implementation notes</th></tr></thead>
    <tbody>
      <tr><td>Button</td><td>Forms</td><td>secondary · primary · accent · ghost / sm·md·lg</td><td>default · hover · focus · disabled</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>One primary/accent per view. Mobile uses h42 in action bars.</td></tr>
      <tr><td>IconButton</td><td>Forms</td><td>default 34 · bare 30 · danger</td><td>default · hover · focus · disabled</td><td>Yes</td><td>Partial</td><td><span class="tag stable">Stable</span></td><td><span class="mono">title</span> required. On mobile, raise the hit area to ≥44.</td></tr>
      <tr><td>Field</td><td>Forms</td><td>label · optional · note</td><td>—</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>Wrapper for every control.</td></tr>
      <tr><td>Input</td><td>Forms</td><td>prefix · suffix · leadIcon · num</td><td>default · focus · disabled · placeholder</td><td>Yes</td><td>Yes</td><td><span class="tag rev">Needs review</span></td><td>No error/invalid state (see NR‑06).</td></tr>
      <tr><td>Select</td><td>Forms</td><td>native + chevron</td><td>default · focus · disabled</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>Pass <span class="mono">&lt;option&gt;</span> as children.</td></tr>
      <tr><td>Textarea</td><td>Forms</td><td>vertical resize</td><td>default · focus · disabled</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>Quote notes.</td></tr>
      <tr><td>Toggle</td><td>Forms</td><td>md 38 · sm 32 · accent</td><td>on · off · disabled</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>Charcoal track when on; <span class="mono">sm</span> for table rows.</td></tr>
      <tr><td>SegmentedControl</td><td>Forms</td><td>2–3 options, with or without icon</td><td>selected · unselected</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>&gt;3 options → Select.</td></tr>
      <tr><td>Badge</td><td>Feedback</td><td>5 tones · dot · square</td><td>static</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td><span class="mono">square</span> for mono codes.</td></tr>
      <tr><td>StatusBadge</td><td>Feedback</td><td>draft·sent·appr·rej·cancel</td><td>static</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>Maps colour + Spanish label automatically.</td></tr>
      <tr><td>Alert</td><td>Feedback</td><td>warn·ok·info·crit·neutral</td><td>static · with or without title</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>warn is the default: the engine never blocks.</td></tr>
      <tr><td>AlertGroup</td><td>Feedback</td><td>title · items</td><td>static</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>“Alerts and reminders” panel.</td></tr>
      <tr><td>Card</td><td>Layout</td><td>pad · flush</td><td>static · hover on rows</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td><span class="mono">flush</span> for full-bleed tables.</td></tr>
      <tr><td>CardHead</td><td>Layout</td><td>icon · title · hint · actions</td><td>static</td><td>Yes</td><td>Partial</td><td><span class="tag stable">Stable</span></td><td>On mobile the actions usually move to an overflow.</td></tr>
      <tr><td>CardBody</td><td>Layout</td><td>default · tight</td><td>—</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td><span class="mono">tight</span> for tables and option lists.</td></tr>
      <tr><td>SectionHead</td><td>Layout</td><td>index · title · hint · tools</td><td>static</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>Numbered steps in the calculator / editor.</td></tr>
      <tr><td>StatTile</td><td>Layout</td><td>default · accent · dot · amberValue</td><td>static</td><td>Yes</td><td>Partial</td><td><span class="tag stable">Stable</span></td><td>One accent per row.</td></tr>
      <tr><td>TechnicalPreviewCard</td><td>Layout</td><td>compact · standard · large / 4 renderers</td><td>live · placeholder</td><td>Yes</td><td>Not documented</td><td><span class="tag ext">Extended</span></td><td>Extensible registry. Mobile behaviour unspecified.</td></tr>
      <tr><td>TechnicalRenderers</td><td>Layout</td><td>registry API</td><td>—</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td><span class="mono">register · setFamilyLabel · has · variants · families</span></td></tr>
      <tr><td>AppShell</td><td>Navigation</td><td>sidebar + topbar + main</td><td>—</td><td>Yes</td><td>Mode required</td><td><span class="tag ext">Extended</span></td><td>Needs 3 modes: sidebar / tabs / bottom bar.</td></tr>
      <tr><td>Sidebar</td><td>Navigation</td><td>logo · footer</td><td>—</td><td>Yes</td><td>Not applicable</td><td><span class="tag stable">Stable</span></td><td>248px fixed, sticky.</td></tr>
      <tr><td>LogoMark</td><td>Navigation</td><td>mark · name · tagline</td><td>—</td><td>Yes</td><td>Yes</td><td><span class="tag rev">Needs review</span></td><td>Default tagline is misaligned (see NR‑02).</td></tr>
      <tr><td>NavGroup</td><td>Navigation</td><td>label · first</td><td>—</td><td>Yes</td><td>Not applicable</td><td><span class="tag stable">Stable</span></td><td>The sidebar's uppercase eyebrow.</td></tr>
      <tr><td>NavItem</td><td>Navigation</td><td>icon · label · as</td><td>default · hover · active</td><td>Yes</td><td>Replaced</td><td><span class="tag stable">Stable</span></td><td>Active = 3px amber rail + bold label.</td></tr>
      <tr><td>Topbar</td><td>Navigation</td><td>breadcrumbs · actions</td><td>—</td><td>Yes</td><td>Compact variant</td><td><span class="tag ext">Extended</span></td><td>Mobile: back + title, no breadcrumbs.</td></tr>
      <tr><td>Breadcrumbs</td><td>Navigation</td><td>items[]</td><td>—</td><td>Yes</td><td>Hidden</td><td><span class="tag stable">Stable</span></td><td>Last item in bold.</td></tr>
      <tr><td>UserChip</td><td>Navigation</td><td>name · role · initials</td><td>—</td><td>Yes</td><td>Avatar only</td><td><span class="tag stable">Stable</span></td><td>On mobile it collapses to the avatar.</td></tr>
      <tr><td>Table</td><td>Data</td><td>is-right/center/sortable helpers</td><td>row hover</td><td>Yes</td><td>Row → card</td><td><span class="tag stable">Stable</span></td><td>Wrap in <span class="mono">Card flush</span>.</td></tr>
      <tr><td>TableToolbar</td><td>Data</td><td>title · count · actions</td><td>—</td><td>Yes</td><td>Simplified</td><td><span class="tag stable">Stable</span></td><td>Result count pill.</td></tr>
      <tr><td>RowActions</td><td>Data</td><td>cluster of IconButtons</td><td>revealed on hover</td><td>Yes</td><td>⋯ overflow</td><td><span class="tag ext">Extended</span></td><td>There is no hover on touch: it needs a ⋯ menu.</td></tr>
      <tr><td>Pagination</td><td>Data</td><td>showing · pages · current</td><td>current · hover</td><td>Yes</td><td>Not documented</td><td><span class="tag ext">Extended</span></td><td>On mobile it will be infinite scroll or “load more”, still undefined.</td></tr>
      <tr><td>TaxonomyIcon</td><td>Icons</td><td>10 categories · size · strokeWidth</td><td>currentColor · amber when active</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>Also used in the bottom tab bar.</td></tr>
      <tr><td>TAXONOMY_ICONS</td><td>Icons</td><td>ordered array</td><td>—</td><td>Yes</td><td>Yes</td><td><span class="tag stable">Stable</span></td><td>Iterate it for menus and catalogues.</td></tr>
      <tr><td>BottomTabBar</td><td>Navigation</td><td>5 tabs</td><td>active · inactive</td><td>Not applicable</td><td>Yes</td><td><span class="tag new">New</span></td><td>h64 + safe area. Icons 21, label 10.</td></tr>
      <tr><td>MobileHeader</td><td>Navigation</td><td>back+title · brand+title</td><td>sticky</td><td>Not applicable</td><td>Yes</td><td><span class="tag new">New</span></td><td>Compact variant of Topbar.</td></tr>
      <tr><td>BottomActionBar</td><td>Navigation</td><td>1 primary + 2 secondary</td><td>fixed</td><td>Not applicable</td><td>Yes</td><td><span class="tag new">New</span></td><td>h62, bottom 64, shadow cast upwards.</td></tr>
      <tr><td>ResultSummary</td><td>Product</td><td>pinned charcoal</td><td>sticky · live</td><td>Not applicable</td><td>Yes</td><td><span class="tag new">New</span></td><td>A ResultPanel variant: recommended + range + size + “View breakdown”.</td></tr>
      <tr><td>ResultSheet</td><td>Product</td><td>bottom sheet</td><td>open · closed</td><td>Not applicable</td><td>Yes</td><td><span class="tag new">New</span></td><td>Top radius 18, max 78%, grab 38×4, scrim .6.</td></tr>
      <tr><td>FilterChips</td><td>Data</td><td>one line, scroll-x</td><td>active solid amber · inactive</td><td>Partial</td><td>Yes</td><td><span class="tag new">New</span></td><td>Never wraps to two lines.</td></tr>
      <tr><td>Checkbox · Radio</td><td>Forms</td><td>—</td><td>—</td><td>No</td><td>No</td><td><span class="tag rev">Needs review</span></td><td>They do not exist in the system (see NR‑13).</td></tr>
      <tr><td>Skeleton · Spinner</td><td>Feedback</td><td>—</td><td>—</td><td>No</td><td>No</td><td><span class="tag rev">Needs review</span></td><td>No loading component (see NR‑07).</td></tr>
    </tbody>
  </table></div>
</section>

<!-- ============================================================ 10 -->
<section class="sec" id="s10">
  <div class="sec-h"><span class="num-badge">10</span><h2>Design → Implementation</h2></div>
  <p class="lede">How the system scales: a foundation decision becomes a token, the token dresses a component, the component composes a product pattern, and the pattern is instanced on a screen. The chain is the same for everything.</p>

  <div class="chain">
    <div class="lk"><div class="st">01 · Foundation</div><div class="k">Decision</div><div class="ex">A single warm accent, rationed</div><div class="d">Amber marks only the moments that matter; everything else is graphite.</div></div>
    <div class="lk"><div class="st">02 · Token</div><div class="k">Variable</div><div class="ex">--re-amber #e0911e<br>--re-amber-bright #f5b342</div><div class="d">It lives in <span class="mono">tokens/colors.css</span>. Everything else references it.</div></div>
    <div class="lk"><div class="st">03 · Component</div><div class="k">Primitive</div><div class="ex">Button variant="accent"<br>StatTile amberValue</div><div class="d">The component never hardcodes the hex: it reads the variable.</div></div>
    <div class="lk"><div class="st">04 · Pattern</div><div class="k">Composition</div><div class="ex">Recommended value<br>+ range + track</div><div class="d">The amber figure on charcoal with its range: business meaning.</div></div>
    <div class="lk"><div class="st">05 · Screen</div><div class="k">Instance</div><div class="ex">Calculator.html<br>ResultSummary (mobile)</div><div class="d">The same pattern at three densities, without deciding anything again.</div></div>
  </div>

  <h3 class="sub"><span class="tick"></span>How to consume the system</h3>
  <div class="grid2">
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">1 · Styles</h4>
      <p class="body" style="margin:0 0 var(--re-s4)">Link <b>one single file</b>. It is an <span class="mono">@import</span> manifest: it brings fonts, colours, typography, spacing, elevation and the base reset.</p>
      <div class="stage" style="display:block"><code class="mono" style="font-size:12px;color:var(--re-ink-2);line-height:1.7">&lt;link rel="stylesheet" href="styles.css"&gt;<br>&lt;body class="re-root"&gt;</code></div>
      <h4 class="mini">2 · Theme</h4>
      <p class="body" style="margin:0 0 var(--re-s4)">Nothing to configure: dark is the system’s only theme, and it is what <span class="mono">:root</span> applies by default.</p>
      <h4 class="mini">3 · Components</h4>
      <div class="stage" style="display:block"><code class="mono" style="font-size:12px;color:var(--re-ink-2);line-height:1.7">const { Button, Card, StatusBadge } =<br>&nbsp;&nbsp;window.PresupuestadorREDesignSystem_84f335;</code></div>
    </div></div>
    <div class="card"><div class="card-b">
      <h4 class="mini" style="margin-top:0">Rules for new surfaces</h4>
      <ul class="body" style="margin:0"><li><b>Never</b> a literal hex: always <span class="mono">var(--re-*)</span>. If a token is missing, it is added to the system, not to the screen.</li>
        <li><b>Never</b> re-explore typography: it is frozen.</li>
        <li><b>Respect amber rationing</b>: recommended value · active rail/tab · CTA · logo.</li>
        <li><b>Icons</b>: copy the shapes from the kit; 24×24, stroke 1.7–1.8, round caps.</li>
        <li><b>No emoji</b>, no gradients, no glass, no decorative blur.</li>
        <li><b>The 5 product rules</b> in §00 are not negotiable from design.</li>
        <li>Figures: <span class="mono">tabular-nums</span> and Uruguayan format, always.</li></ul>
      <h4 class="mini">Migrating to Figma</h4>
      <ul class="body" style="margin:0"><li>The §01 tokens map 1:1 to Figma variables (colour, number for spacing/radius, string for families).</li>
        <li>The §02 variants map to component properties (<span class="mono">variant</span>, <span class="mono">size</span>, <span class="mono">tone</span>, <span class="mono">status</span>).</li>
        <li>The three breakpoints in §07 map to page/frame widths 390 · 834 · 1440.</li></ul>
    </div></div>
  </div>

  <!-- NEEDS REVIEW REGISTER -->
  <h3 class="sub" id="needs-review"><span class="tick"></span>System evolution</h3>
  <p class="lede">The system’s open workstreams and the state each one is in.</p>
  <div class="ev-grid">
      <div class="ev">
        <span class="ev-name">Tablet</span>
        <span class="ev-state ev-state--progress">In progress</span>
      </div>
      <div class="ev">
        <span class="ev-name">V2 expansion</span>
        <span class="ev-state ev-state--progress">In development</span>
      </div>
      <div class="ev">
        <span class="ev-name">Light theme</span>
        <span class="ev-state ev-state--planned">Planned</span>
      </div>
  </div>

  <details class="ev-audit">
    <summary>Internal audit log (13 notes)</summary>
    <p class="body">Inconsistencies and gaps found while auditing the system. They stay written down so each is resolved as an explicit decision rather than patched along the way. This is internal working material.</p>
    <div class="stack">
    <div class="nr"><span class="id">NR‑01</span><p><b>Prose vs. token in <span class="mono">--re-ink-4</span>.</b> The readme describes <span class="mono">#686d76</span>; the real token is <span class="mono">#7f858e</span> (raised to meet WCAG AA). The readme's prose fell out of date with the token file.</p></div>
    <div class="nr"><span class="id">NR‑02</span><p><b><span class="mono">LogoMark</span> default tagline.</b> The component ships <span class="mono">'RE · v1.0'</span>; the kit and the mockups show <span class="mono">“REstimator · motor v1.5”</span>. Decide which one is canonical.</p></div>
    <div class="nr"><span class="id">NR‑03</span><p><b><span class="mono">--re-text-xl (21px)</span> undocumented.</b> It exists in <span class="mono">tokens/typography.css</span> but is missing from the scale described in the readme (40 · 23 · 17 · 14.5 · 14 · 13 · 12 · 11).</p></div>
    <div class="nr"><span class="id">NR‑04</span><p><b>Control height per breakpoint.</b> <span class="mono">--re-control-h</span> is 40px (desktop) but the Mobile v1 spec uses 42px. There is no mobile control token: today the 42 is hardcoded.</p></div>
    <div class="nr"><span class="id">NR‑05</span><p><b>Breakpoints not tokenised.</b> 390 / 834 / 1200–1440 live in prose and specs, not in <span class="mono">tokens/</span>.</p></div>
    <div class="nr"><span class="id">NR‑06</span><p><b>No field error state.</b> <span class="mono">Field</span>/<span class="mono">Input</span> do not expose <span class="mono">error</span>/<span class="mono">invalid</span>. Consistent with the warn-only engine, but it leaves inline validation undefined should it ever be needed.</p></div>
    <div class="nr"><span class="id">NR‑07</span><p><b>No loading or empty state as components.</b> There is no skeleton/spinner; empty states are copy inside screens.</p></div>
    <div class="nr"><span class="id">NR‑08</span><p><b>Amber rationing on mobile.</b> The rule defines four moments; mobile adds the active tab (amber-ink) and the active filter chip (solid amber). The rule needs restating for touch, or those uses reclassifying as “active rail”.</p></div>
    <div class="nr"><span class="id">NR‑09</span><p><b>6 mobile components specified but not built.</b> <span class="mono">BottomTabBar · MobileHeader · BottomActionBar · ResultSummary · ResultSheet · FilterChips</span> exist in Mobile v1 but not in <span class="mono">components/</span>.</p></div>
    <div class="nr"><span class="id">NR‑10</span><p><b>Obsolete starting points.</b> The kit’s 4 screens are marked <span class="mono">@startingPoint</span>, a mechanism consuming projects no longer offer. <span class="mono">templates/</span> replaced it, and today there are 0 templates.</p></div>
    <div class="nr"><span class="id">NR‑11</span><p><b>Fonts over CDN.</b> <span class="mono">tokens/fonts.css</span> imports Hanken Grotesk and JetBrains Mono from Google Fonts. A truly offline standalone export would need them self-hosted or inlined.</p></div>
    <div class="nr"><span class="id">NR‑12</span><p><b>Tablet without an artifact.</b> The layer is documented but there is no tablet screen in <span class="mono">ui_kits/</span>; its values come from the refinement mockups.</p></div>
    <div class="nr"><span class="id">NR‑13</span><p><b>No Checkbox or Radio.</b> The product avoids them (it uses Select, SegmentedControl and Toggle), but this is worth stating as an explicit system decision rather than leaving it as a gap.</p></div>
  </div>
  </details>
</section>

<footer style="border-top:1px solid var(--re-line);margin-top:var(--re-s9);padding-top:var(--re-s5);display:flex;justify-content:space-between;flex-wrap:wrap;gap:16px;font-size:12px;color:var(--re-ink-4)"><span>REstimator Design System — Master Documentation</span> <span class="mono">147 tokens · 33 components</span></footer>

</div>
</main>
</div>
