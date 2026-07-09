# Estavillo Child — child theme de Kadence

Foundation técnica del nuevo portfolio ESTAVILLO. **No toca el sitio en vivo**: es un
child theme que se instala aparte y un page template que se asigna a una página en
borrador para previsualizar.

## V1 / V2

Este repo es explícitamente **V1: un sistema rápido y publicable**, no la
versión final de la arquitectura. Todo el contenido nuevo (páginas fijas,
timeline/educación de About, hero variants) se edita hoy vía page
templates + PHP/CSS del tema y campos/filtros del plugin (meta boxes,
options page) — el mecanismo más rápido y seguro disponible con el stack
actual (sin ACF, sin builder de terceros).

**V2** (futuro, no planeado en detalle todavía) es la migración de estas
mismas secciones a **bloques/patrones de Gutenberg nativos**, para que
editar dentro de wp-admin se sienta como editar cualquier página de
WordPress en vez de llenar campos en una options page. Nada de lo
construido en V1 es descartable: los datos (post meta, el option
`es_portfolio_home_content`) y los filtros (`es_home_*`, `es_about_*`,
`es_case_*`) son el contrato estable que un V2 basado en bloques puede
seguir leyendo — ver `docs/EDITABILITY-PLAN.md` para el detalle de esta
estrategia de migración.

## Qué incluye

```
estavillo-child/
├── style.css                      → cabecera del child theme (Template: kadence)
├── functions.php                  → constantes, includes, helpers Polylang (es__, es_nav_links)
├── screenshot.png                 → miniatura en Apariencia → Temas
├── assets/
│   ├── css/
│   │   ├── tokens.css             → design tokens --es-* (dark-first, light, acento, font preset)
│   │   ├── base.css               → tipografía y utilidades bajo .es-page
│   │   ├── layout.css             → contenedores, secciones, grillas
│   │   ├── components.css         → botones, pills, cards (+ wide), status pill, reveal
│   │   ├── site.css               → chrome: header sticky + menú mobile + footer (Home, Case Study Y páginas fijas)
│   │   ├── hero.css               → hero: visual detrás/al costado del copy (desktop y mobile, solo Home)
│   │   ├── pages-home.css         → secciones de la home (featured, process, work, about, connect) — Home Y páginas fijas
│   │   ├── pages.css              → page-head, timeline/educación/hobbies de About, grilla de Contact (solo páginas fijas)
│   │   └── case-study.css         → single de Case Study: breadcrumbs + índice sticky + hero (4 variantes) + prose + librería .es-case-*
│   └── js/
│       ├── hero-system-map.js     → motores de hero (registry, SVG + rAF, 0 librerías)
│       ├── motion.js              → reveal on-scroll (IntersectionObserver)
│       └── nav.js                 → menú mobile (overlay accesible)
├── template-parts/
│   ├── site-header.php            → nav sticky ESTAVILLO + menú mobile + switcher de idioma (Polylang)
│   ├── hero-home.php              → hero (copy placeholder, editable por filtros)
│   ├── featured-case.php          → 01 Main case (filtro es_home_featured)
│   ├── how-i-work.php             → 02 How I work · 6 pasos (filtro es_home_process_steps), reusado en la página fija
│   ├── selected-work.php          → 03 Selected work: card ancha + 2-up (es_home_selected_work) — teaser de Home
│   ├── about-teaser.php           → 04 About (filtros es_home_about_*) — teaser de Home
│   ├── footer-cta.php             → 05 Connect · "Let's talk" (es_home_cta_*, es_contact_email) — teaser de Home
│   ├── site-footer.php            → footer ESTAVILLO
│   ├── breadcrumbs.php            → Home / Work / título — genérico, hoy solo en Case Study
│   ├── page-head.php              → cabecera compartida (eyebrow + h1 + lead) de las 4 páginas fijas
│   ├── work-cases.php             → listado completo de Work: selected + archive
│   ├── about-content.php          → intro + timeline + educación + hobbies + CV de la página About
│   └── contact-content.php        → email + ubicación + redes de la página Contact
├── templates/
│   ├── page-home-estavillo.php    → Template Name: "Estavillo — Home (Draft)" (standalone)
│   ├── page-work.php              → Template Name: "Estavillo — Work" (standalone)
│   ├── page-about.php             → Template Name: "Estavillo — About" (standalone)
│   ├── page-how-i-work.php        → Template Name: "Estavillo — How I Work" (standalone)
│   └── page-contact.php           → Template Name: "Estavillo — Contact" (standalone)
├── single-es_case_study.php       → single de Case Study (standalone, reusa el chrome ESTAVILLO)
└── inc/
    ├── enqueue.php                  → carga condicional de assets (Home + Case Study + páginas fijas) + config localizada
    ├── theme-options.php            → Customizer: acento + variantes de hero + font preset
    ├── selected-work-fallback.php   → Selected Work: placeholders + puente por filtro hacia el plugin + es_work_media()
    ├── featured-case-fallback.php   → Featured Case: placeholder + puente por filtro hacia el plugin
    └── work-page-fallback.php       → Work: placeholders (selected/archive) + puente por filtro hacia el plugin
```

> El **Case Study CPT** (registro, meta box, queries) vive en el plugin
> companion **Estavillo Portfolio Core** (`estavillo-portfolio-core/` en la
> raíz del repo, `dist/estavillo-portfolio-core.zip`), no en este tema —
> ver "Selected Work — editable vía Case Studies" más abajo.

### Home v1 — estructura y edición

El template **"Estavillo — Home (Draft)"** es *standalone*: renderiza su propio
chrome dark premium (nav sticky + footer ESTAVILLO) vía `wp_head()`/`wp_footer()`,
sin usar el header/footer de Kadence — el resto del sitio sigue con Kadence intacto.

**Narrativa (orden de secciones):** Hero → 01 How I Work → 02 Featured Case →
03 Selected Work → 04 About → 05 Connect. *(Primero explica CÓMO trabajo, después
lo prueba con el caso, luego muestra más trabajo.)* La numeración sigue el orden
automáticamente (se pasa por `$args['num']` desde el loop del template).

**Fundación de editabilidad** — el orden y las secciones son *data-driven* vía el
filtro `es_home_sections()`: un mapa `clave de sección → template part`. El
template recorre ese mapa; **reordenar / quitar / insertar secciones no requiere
tocar PHP**. La clave de sección es el contrato estable: a futuro cada clave puede
apuntar a un **block/pattern reutilizable** en vez de un template part, migrando
sección por sección sin reescribir el template.

```php
// Reordenar / quitar / insertar secciones sin tocar archivos:
add_filter( 'es_home_sections', function ( $s ) {
    // p.ej. mover About antes de Selected Work, o quitar una sección:
    // unset( $s['about'] );  return $s;
    return $s;
} );
```

Todo el copy es **placeholder editable por filtros** (Code Snippets), sin tocar
archivos:

```php
add_filter( 'es_nav_links',            function ( $l ) { /* label + url por item */ return $l; } );
add_filter( 'es_home_featured',        function ( $c ) { $c['url'] = '/work/gv/'; return $c; } );
add_filter( 'es_home_process_steps',   function ( $s ) { $s[0]['icon'] = '<svg…>'; return $s; } ); // slot de ícono reservado
add_filter( 'es_home_selected_work',   function ( $cases ) { return $cases; } ); // 1º = card ancha
add_filter( 'es_home_about_text',      fn() => 'Nuevo texto de about.' );
add_filter( 'es_home_about_portrait',  fn() => 'https://…/retrato.jpg' );
add_filter( 'es_contact_email',        fn() => 'hola@dominio.com' ); // default: hello@ramiroestavillo.com
add_filter( 'es_footer_location',      fn() => 'Montevideo, Uruguay' );
add_filter( 'es_social_links',         fn() => array( 'LinkedIn' => 'https://…', 'Behance' => 'https://…' ) );
```

**Chrome & UX:** header alineado (logo · nav · EN/ES · **slot reservado** para un
futuro toggle Light/Dark, sin funcionalidad todavía). Nav mobile premium: botón
"Menu" + hamburguesa que **morfa a X**, overlay con micro-animación, scroll lock,
y cierre por X / Escape / click en link / click afuera. Estados interactivos solo
en **verde / tinta / opacidad — nunca azul** (se neutraliza cualquier `a:hover` o
`button:hover` heredado de Kadence). How I Work deja un **slot de ícono reservado**
por paso para sumar íconos/motion/ilustración a futuro sin cambiar el layout.

### Selected Work — editable vía Case Studies (Sprint 3)

**Selected Work** ya no depende de editar PHP: es un custom post type
**"Case Studies"**, sin ACF ni dependencias nuevas — solo campos nativos de
WordPress + un meta box propio y chico.

**Arquitectura (Sprint 3 extraction):** el CPT vive en un plugin separado,
**Estavillo Portfolio Core** (`estavillo-portfolio-core/`), no en este
tema. El tema y el plugin **no se llaman directamente**: el único puente es
el filtro `es_portfolio_case_studies_for_home`. El tema lo dispara con sus
3 casos placeholder como default; si el plugin está activo, engancha ese
filtro y devuelve Case Studies reales cuando existen. Por eso:

- **Con el plugin activo y con contenido** → Selected Work muestra los
  Case Studies reales.
- **Con el plugin activo pero sin ningún Case Study publicado (o todos
  marcados "no mostrar en Home")** → Selected Work muestra los 3
  placeholders de siempre.
- **Con el plugin inactivo o no instalado** → exactamente lo mismo: 3
  placeholders de siempre, **sin ningún error** — el tema nunca llama una
  función del plugin directamente, solo dispara un filtro que, sin el
  plugin, simplemente no tiene ningún callback enganchado.

**Instalación del plugin:** Plugins → Añadir nuevo → Subir plugin →
`estavillo-portfolio-core.zip` (en `dist/` del repo) → Instalar → **Activar**.
Al activarlo aparece **Case Studies** en el menú de wp-admin (ícono de
portfolio) y se flushean las reglas de reescritura automáticamente (no hace
falta ir a Ajustes → Enlaces permanentes a mano).

**Cómo crear/editar un caso (p. ej. Trazur o Presupuestador):**

1. wp-admin → **Case Studies → Add New**.
2. **Título** = título del caso (p. ej. "Presupuestador").
3. **Extracto** (panel "Excerpt" del editor/sidebar) = la descripción corta
   que se muestra en la card. Si tu editor no muestra el panel, activalo
   desde Preferencias del editor (los tres puntos → Preferencias → Paneles).
4. **Imagen destacada** = la imagen de la card. Si no subís ninguna, la card
   muestra el marco placeholder del design system (como ahora).
5. **Case Tags** (panel lateral) = tags de la card (UX Research, Fintech,
   etc.), igual que Categorías/Tags de un post normal.
6. **Atributos de página / Order** (panel lateral) = orden en Home. El caso
   con el número más bajo se muestra como card ancha destacada; el resto
   entra en la grilla de 2 columnas, ordenados por este número.
7. Debajo del editor, meta box **"Case details (Selected Work)"**:
   - **Eyebrow / category** → la línea sobre el título (p. ej. "Fintech ·
     Budgeting tool").
   - **Case link (URL)** → adónde apunta la card (Selected Work / Featured
     Case). Si lo dejás vacío, usa la URL propia del Case Study — es decir
     su página individual (ver "Single Case Study page" más abajo).
   - **Label / status** (opcional) → la etiqueta chica mono junto al eyebrow
     en las cards, el pill de Featured Case, y el meta "Status" de la
     página individual. Vacío = no se muestra en ningún lado.
   - **Placeholder tag text** (opcional) → solo se usa si NO subiste imagen
     destacada; reemplaza el texto `{asset: …}` del marco placeholder (en
     Selected Work, Featured Case, y la página individual).
   - **Source / context line** (opcional) → solo la usa Featured Case (ver
     abajo); no aparece en las cards de Selected Work ni en la página
     individual.
   - **Role / Tools / Period** (opcionales) → solo los usa la página
     individual del caso (ver "Single Case Study page" más abajo); no
     aparecen en ningún otro lado.
   - **Show this case in Home → Selected Work** → tildado por defecto.
     Destildalo para tener el caso guardado sin que aparezca en Home todavía.
   - **Feature this case on Home (Featured Case section)** → ver la sección
     de Featured Case más abajo. Independiente del checkbox anterior: un
     caso puede estar en Selected Work, en Featured Case, en ambos, o en
     ninguno.
8. **Publicar.** La sección Selected Work de Home lee automáticamente todos
   los Case Studies publicados con el checkbox tildado, ordenados por
   "Order" — sin tocar ningún archivo del tema.

En cuanto publiques el primer Case Study marcado para Home, los 3
placeholders (Trazur / French Bakery / Samic) desaparecen y se muestran tus
casos reales — ver la sección de arquitectura arriba para el detalle del
fallback.

El filtro `es_home_selected_work` documentado abajo sigue funcionando igual
sobre el resultado final (Case Studies reales o fallback) — no fue
reemplazado, solo tiene una fuente de datos nueva por defecto.

> Si un link a un Case Study individual da 404 igual: Ajustes → Enlaces
> permanentes → Guardar cambios (refresca las reglas de reescritura de
> WordPress). No debería hacer falta — el plugin ya flushea al activarse —
> pero es el primer lugar para mirar si pasa.

### Featured Case — editable vía Case Studies (Sprint 3, siguiente ticket)

**Featured Case** usa el mismo mecanismo y el mismo CPT que Selected Work —
no es contenido separado. Cualquier Case Study existente puede marcarse
como el caso destacado con un solo checkbox.

**Cómo destacar un caso:**

1. wp-admin → **Case Studies** → abrí el caso que querés destacar (puede
   ser uno que ya esté en Selected Work, o uno nuevo).
2. En el meta box **"Case details"**, tildá **"Feature this case on Home
   (Featured Case section)"**.
3. Completá también, si hace falta:
   - **Extracto** → se convierte en el párrafo de cuerpo de Featured Case
     (el texto largo, no el resumen corto de una card). Si el mismo caso
     también está en Selected Work, ese mismo Extracto es lo que se
     muestra en ambos lugares — escribilo pensando en los dos usos, o no
     marques el mismo caso para las dos secciones a la vez.
   - **Label / status** → se muestra como el pill con punto verde animado
     (p. ej. "In progress", "Live").
   - **Source / context line** → la línea chica debajo del párrafo (p. ej.
     "Developed and implemented at ..."). Solo la usa Featured Case.
4. **Publicar/Actualizar.**

**Si marcás más de un caso como featured:** gana el de menor **Order**
(Atributos de página → Order), el mismo campo que ordena Selected Work.
No es un error ni algo indefinido — es la regla de desempate.

**Fallback (igual que Selected Work):** con el plugin inactivo, o activo
pero sin ningún caso marcado "featured", Featured Case muestra exactamente
el mismo contenido placeholder de siempre (el caso de Guzmán Villalba) —
mismo markup, mismo CSS, cero cambio visual. El puente es un segundo
filtro independiente, `es_portfolio_featured_case_for_home`, con la misma
garantía que `es_portfolio_case_studies_for_home`: el tema nunca llama una
función del plugin directamente, así que desactivar el plugin no puede
romper esta sección.

El filtro `es_home_featured` documentado abajo sigue funcionando igual
sobre el resultado final.

### Single Case Study page (Sprint 4B)

Cada Case Study publicado ya tiene una página individual real — WordPress
la resuelve solo (`single-es_case_study.php`), no hace falta activar nada.

**Qué se muestra**, todo opcional salvo el título:

- **Título**, **Extracto** (como bajada), **Tags**, **Imagen destacada**
  (o el marco placeholder si no subiste ninguna) — los mismos campos
  nativos que ya usás para Selected Work/Featured Case.
- **Eyebrow / category** → del meta box "Case details".
- Una fila de meta con **Status**, **Role**, **Tools** y **Period** — cada
  uno del meta box "Case details", cada uno opcional; si dejás los 4
  vacíos, la fila entera no se muestra.
- **Contenido principal** → el editor estándar de WordPress (el mismo
  cuadro grande de siempre, bloques o clásico). Esto NO es un campo nuevo:
  es el contenido normal del post — escribí ahí el desarrollo completo del
  caso (texto, imágenes, encabezados, lo que necesites). No hay un "case
  builder" con secciones fijas.

**Hero editorial de 2 columnas (Sprint 4D):** en desktop (≥1000px) el
título/eyebrow/extracto/tags/meta va a la izquierda (siempre alineado a la
izquierda, nunca centrado) y la imagen destacada (o el placeholder) a la
derecha, en un marco 4:5. En mobile (<1000px) se apila: primero el texto,
después la imagen — igual que antes de este ticket. Es solo el hero; el
resto de la página (índice sticky, cuerpo del editor con `.es-case-*`) no
cambió.

**Cómo escribir el caso completo:**

1. wp-admin → **Case Studies** → abrí (o creá) el caso.
2. Completá el contenido principal en el editor grande, igual que
   escribirías cualquier post — encabezados (H2/H3), párrafos, imágenes,
   lo que haga falta.
3. Completá **Role**, **Tools**, **Period** en el meta box "Case details"
   si querés que aparezcan en la fila de meta de la página.
4. **Publicar.** La URL del caso (visible arriba del editor, o en **Case
   link (URL)** si lo dejaste vacío) ya es una página real con el chrome
   ESTAVILLO completo — header, menú mobile, footer, todo igual que Home.

### Sistema de formato de Case Study — índice sticky + librería `.es-case-*` (Sprint 4C)

Sprint 4C suma dos cosas sobre la página individual de Sprint 4B: un
**índice sticky opcional** y una **librería de clases CSS** para dar
estructura visual (secciones, stats, timeline, decisiones, quote, status,
accordion, marco tipo browser…) al contenido que ya escribís en el editor
estándar. Nada de esto es un campo nuevo de contenido ni un page builder —
seguís escribiendo todo en el mismo cuadro grande del editor; estas clases
solo le dan estilo a lo que ya está ahí.

> Inspirado visualmente en un case study de referencia (formato "REstimator
> Dark") que se usó únicamente como guía de composición — el CSS de acá es
> 100% propio del design system ESTAVILLO, reimplementado con los tokens
> `--es-*` existentes (cero colores/tipografías nuevos). El runtime de esa
> referencia (`support.js`) nunca se copió, encoló ni ejecutó en el sitio.

**Cómo usar el editor nativo con estas clases:**

Con el editor de bloques, cada bloque (Group, Paragraph, Image, Heading…)
tiene un panel **Avanzado → "Clases CSS adicionales"** donde pegás el
nombre de la clase (p. ej. `es-case-section`). Para estructuras con varios
elementos anidados (stats, decision cards, status grid) es más simple usar
un bloque **HTML personalizado** y pegar el fragmento directo — ver el
ejemplo completo más abajo. El editor clásico también sirve: son clases
CSS comunes, no bloques especiales.

**Clases disponibles** (todas se aplican dentro del cuerpo del post, o sea
dentro de lo que imprime `the_content()`):

| Clase | Para qué |
|---|---|
| `es-case-section` | Envuelve un "capítulo" del caso. Agrega una línea divisoria arriba y espaciado generoso — **excepto la primera** de la página, que no lleva línea. Ponele `id="loquesea"` si querés que el índice sticky pueda linkear a esta sección. |
| `es-case-label` | Eyebrow mono chico arriba de un heading de sección (p. ej. "Fig. 01 — Contexto"). |
| `es-case-heading` | Título de sección (serif, grande). Va después de `es-case-label`. |
| `es-case-lead` | Párrafo grande de apertura de una sección (serif, más grande que el body normal). |
| `es-case-cols` | Grilla de 2 columnas (texto/imagen, texto/texto). Se apila a 1 columna en mobile automáticamente. |
| `es-case-figure` (en un `<figure>`) + `es-case-caption` / `es-case-caption__tag` (en el `<figcaption>`) | Imagen con marco y caption mono con un "tag" de acento (p. ej. "FIG. 1.1"). |
| `es-case-browser` / `es-case-browser__bar` / `es-case-browser__dot` / `es-case-browser__label` | Marco tipo ventana de navegador (barra falsa con 3 puntos + label) para encuadrar screenshots de producto. |
| `es-case-stats` (contenedor) + `es-case-stat` / `es-case-stat__num` / `es-case-stat__label` (cada celda) | Grilla de estadísticas, 4 columnas en desktop → 2 en mobile. |
| `es-case-timeline` (`<ul>`) + `es-case-timeline__item` / `__title` / `__text` | Timeline vertical con puntos de acento, para procesos/evolución en fases. |
| `es-case-decisions` (contenedor) + `es-case-decision` / `__num` / `__title` / `__row` (`<dl>` con `dt`/`dd`) | Cards de decisiones numeradas (evidencia → resultado), 3 columnas → 1 en mobile. |
| `es-case-quote` (en un `<blockquote>`) + `<cite>` adentro | Pullquote grande con borde de acento. |
| `es-case-status` (contenedor) + `es-case-status__col` (agregale `--done` o `--attention`) + `__head` / `__list` | Grilla de status (4 → 2 en mobile). `--done` pinta el punto en verde (`--es-accent`), `--attention` en naranja (`--es-decision`) — mismos dos colores que ya usa el resto del sistema, ninguno nuevo. |
| `es-case-details` (en un `<details>`) + `<summary>` + `es-case-details__body` | Accordion nativo del navegador — **sin JavaScript**, con un `+` que rota a 45° al abrirse. |

**Ejemplo completo para pegar en un bloque HTML personalizado:**

```html
<div class="es-case-section" id="context">
  <div class="es-case-label">Fig. 01 — El contexto</div>
  <h2 class="es-case-heading">Todo empieza en un taller real.</h2>
  <p class="es-case-lead">Un párrafo grande que abre la sección y resume la idea central del capítulo.</p>

  <div class="es-case-cols">
    <div><p>Columna de texto uno.</p></div>
    <div><p>Columna de texto dos, o una imagen normal de WordPress.</p></div>
  </div>

  <figure class="es-case-figure">
    <img src="https://tu-sitio.com/wp-content/uploads/imagen.jpg" alt="Descripción de la imagen">
    <figcaption class="es-case-caption">
      <span class="es-case-caption__tag">FIG. 1.1</span>
      <span>Texto de caption explicando la imagen.</span>
    </figcaption>
  </figure>

  <div class="es-case-stats">
    <div class="es-case-stat"><div class="es-case-stat__num">~650</div><div class="es-case-stat__label">archivos leídos</div></div>
    <div class="es-case-stat"><div class="es-case-stat__num">1,600+</div><div class="es-case-stat__label">registros</div></div>
  </div>
</div>

<div class="es-case-section" id="decisions">
  <h2 class="es-case-heading">Decisiones clave.</h2>

  <blockquote class="es-case-quote">
    <p>La pregunta nunca fue calcular un precio.</p>
    <cite>Un hallazgo del proceso</cite>
  </blockquote>

  <div class="es-case-decisions">
    <article class="es-case-decision">
      <span class="es-case-decision__num">01</span>
      <h3 class="es-case-decision__title">Capturar el conocimiento como taxonomía</h3>
      <dl class="es-case-decision__row">
        <dt>Evidencia</dt><dd>Texto de evidencia.</dd>
        <dt>Resultado</dt><dd>Texto de resultado.</dd>
      </dl>
    </article>
  </div>

  <details class="es-case-details">
    <summary>Detalle técnico</summary>
    <div class="es-case-details__body"><p>Texto extendido opcional.</p></div>
  </details>
</div>
```

**Cómo subir imágenes:** igual que cualquier imagen de WordPress — bloque
Imagen normal, o `<img src="…">` dentro de un bloque HTML apuntando a una
URL de la Biblioteca de medios (Añadir nuevo → subís el archivo → copiás la
URL). No hace falta ningún campo especial; `es-case-figure`/`es-case-browser`
solo agregan el marco visual alrededor de la imagen que ya subiste.

**Índice sticky (opcional, manual para V1):**

1. wp-admin → **Case Studies** → abrí el caso → meta box **"Case details"**
   → campo **"Case index (optional)"**.
2. Un renglón por entrada, formato `Label|#ancla` — p. ej.:
   ```
   Context|#context
   Problem|#problem
   Decisions|#decisions
   ```
3. Cada `#ancla` tiene que coincidir con un `id="ancla"` que le pusiste a un
   `es-case-section` (o a cualquier elemento) dentro del contenido — es el
   mismo `id` del ejemplo HTML de arriba (`id="context"`, `id="decisions"`).
4. Si dejás el campo vacío, el índice **no se muestra** — no es un
   requisito, es 100% opcional. No hay repeater ni ACF: es una sola
   textarea, deliberadamente simple para V1 (si a futuro hace falta
   generarlo automáticamente desde los headings, es un cambio contenido a
   ese único campo).
5. En desktop el índice queda pegado (sticky) debajo del header al hacer
   scroll. En mobile es una tira horizontal scrolleable, sin scrollbar
   visible — no ocupa una fila completa de la pantalla.
6. Los links del índice (como cualquier link del template de Case Study)
   nunca cambian a azul en ningún estado — hover/focus/active/visited usan
   siempre los tokens de tinta/acento del sistema.

**Cómo traducir un caso con Polylang:** el campo "Case index" y el
contenido con estas clases se traducen exactamente igual que el resto del
Case Study (ver "Polylang" más abajo) — son campos nativos del post
(meta box + `the_content()`), así que "Add translation" los copia como
punto de partida y los editás en el otro idioma con libertad, incluyendo
anclas/labels distintas si hace falta.

**Alcance de este archivo:** `case-study.css` solo se encola en
`single-es_case_study.php` (ver `inc/enqueue.php`) — nunca afecta Home ni
ninguna otra página del sitio.

### Hero layout options (mega sprint)

El hero del Case Study admite 4 layouts, elegibles por caso desde el campo
**"Hero layout"** del meta box (Case details), sin tocar código:

| Opción (label en el select)                  | Clase modificadora            | Cuándo usarla |
|-----------------------------------------------|--------------------------------|---------------|
| Split — image right (default)                  | *(ninguna — es el default)*    | El caso normal: texto a la izquierda, imagen 4:5 a la derecha. |
| Split — image left                             | `es-case__hero--split-left`    | Variedad visual entre casos consecutivos, o cuando la imagen "lee" mejor primero. |
| Compact — horizontal image, shorter frame       | `es-case__hero--compact`       | Cuando el extracto es largo — el marco de imagen es más corto (16:10) en vez de un retrato alto, así no queda un hueco raro al lado de mucho texto. |
| Stacked — text first, image below (full width) | `es-case__hero--stacked`       | Cuando la imagen destacada es un screenshot ancho (no un retrato) — pasa a ancho completo debajo del texto, incluso en desktop. |

En **mobile (<1000px) las 4 opciones se ven exactamente igual**: apiladas,
texto primero, imagen después — la variante solo cambia el desktop. Esto es
intencional (ver "Fix" del ticket original) y está reforzado en CSS para
que ninguna variante pueda romper el apilado mobile por accidente.

También se corrigió la alineación vertical del hero para los 4 layouts:
antes el bloque de texto y la imagen se alineaban por arriba
(`align-items: start`), así que un extracto largo dejaba un hueco vacío
abajo de la imagen. Ahora se centran (`align-items: center`), repartiendo
ese espacio arriba y abajo — se ve mejor con cualquier largo de extracto.

### Breadcrumbs (mega sprint)

Cada Case Study muestra una tira de breadcrumbs sutil arriba del índice
sticky: **Home / Work / título del caso**. Es automática, no requiere
ningún campo nuevo:

- El link **"Home"** usa `home_url()`.
- El link **"Work"** reusa el primer nav link cuya etiqueta coincide con
  "Work" (mismo array que ya alimenta el header, el menú mobile y el
  footer — `es_nav_links()`). Si repuntás ese nav link a una página Work
  real desde **Case Studies → Home Content → Header**, el breadcrumb la
  sigue automáticamente.
- El último elemento (el título del caso) no es un link.
- Con Polylang activo, `home_url()` y los permalinks ya resuelven al
  idioma actual de forma nativa — no hace falta código extra acá.

Template part: `template-parts/breadcrumbs.php` (genérico — recibe un
`trail` de `{label, url}`, así que puede reusarse en otras páginas más
adelante sin cambiar el archivo).

### Páginas fijas — Work / About / How I Work / Contact (mega sprint)

Cuatro páginas nuevas, seleccionables como **Template** al crear/editar una
Página en wp-admin (**Páginas → Agregar nueva → panel "Página" → Plantilla
de página**):

| Template (nombre visible en wp-admin) | Archivo                              | Qué muestra |
|----------------------------------------|---------------------------------------|-------------|
| Estavillo — Work                      | `templates/page-work.php`            | Todos los Case Studies publicados, separados en "Selected work" (card ancha + grilla — los mismos marcados "Show this case in Home") y "Archive / older work" (los marcados explícitamente "no mostrar en Home"), en un bloque con fondo distinto para que la separación sea clara. Sin Case Studies reales, usa el mismo fallback de siempre. |
| Estavillo — About                     | `templates/page-about.php`           | Intro grande (retrato + texto — mismos campos que el teaser de Home) + botón de descarga de CV + experiencia (timeline) + educación/certificados + hobbies. Las 4 secciones nuevas son opcionales: si no cargaste nada todavía, esa sección no se imprime — la página nunca se ve rota. |
| Estavillo — How I Work                | `templates/page-how-i-work.php`      | Los mismos 6 pasos de Home (reusa el template part), con soporte de ícono por paso. |
| Estavillo — Contact                   | `templates/page-contact.php`         | Los mismos datos "Connect" de Home (título, lead, email) + los de Footer (redes, ubicación), en una presentación más grande y dedicada. |

**Todas son standalone** (imprimen su propio `wp_head()`/`wp_footer()` y
reusan el chrome ESTAVILLO — header sticky + footer — igual que Home y el
single de Case Study). Ninguna toca Home ni cambia su template.

**De dónde sale el contenido — todo editable desde wp-admin, nada nuevo
que aprender:**

- **Work**: mismo dato que ya alimentaba Home → Selected Work (el checkbox
  "Show this case in Home → Selected Work" de cada Case Study). Sin campo
  nuevo: casos marcados sí = "Selected work" en esta página; casos
  marcados explícitamente que no = "Archive".
- **About**: los campos "About text" / "Portrait image URL" de siempre
  (Sprint 3) más 4 campos nuevos en la misma página **Case Studies → Home
  Content → About**: "CV / résumé URL" y las tablas "Career timeline" (4
  filas: año, rol, texto) y "Education & certificates" (4 filas: título,
  institución, año) y "Hobbies / interests" (una lista separada por
  comas). Dejar una fila con el título vacío la excluye — no hace falta
  llenar las 4.
- **How I Work**: los mismos 6 pasos de siempre (**Home Content → How I
  Work**), ahora con un selector de ícono opcional por paso (librería
  curada de 8 íconos — ver siguiente sección).
- **Contact**: los mismos campos "Connect" y "Footer" de siempre (**Home
  Content → Connect** y **→ Footer**) — ningún campo nuevo.

**Íconos de "How I Work" (mega sprint):** cada paso tiene ahora un select
opcional ("— No icon —" por defecto) con 8 íconos de línea fina
(brújula, flujo, blanco, capas, check, documento, foco/bombilla, cohete).
Es una librería **curada y cerrada**, no un uploader — el admin solo
guarda la CLAVE elegida (whitelist), nunca HTML/SVG libre, así que no hay
superficie de XSS nueva. El SVG real vive en
`functions.php` → `es_process_icon_svg()`. Sin ícono elegido, el paso se
ve exactamente igual que antes (el marcador vacío reservado desde Home
v1).

**CSS de estas páginas:** `assets/css/pages.css`, encolado solo en estas 4
plantillas (ver `inc/enqueue.php` → `es_is_estavillo_static_page()`).
Reusa la mayoría de sus estilos de `pages-home.css` (cards, process grid,
about grid, footer CTA — las mismas reglas que ya usaba Home), así que
`pages.css` solo tiene lo genuinamente nuevo: la cabecera compartida
(`.es-page-head`), el timeline/educación/hobbies de About, y la grilla de
Contact.

### Polylang (Sprint 4A)

El sitio soporta EN/ES real vía [Polylang](https://wordpress.org/plugins/polylang/)
(plugin externo, no incluido acá). Con Polylang **instalado y activo**:

- **Case Studies son traducibles como cualquier post.** Al abrir/crear un
  Case Study vas a ver la caja de idioma normal de Polylang. "Add
  translation" crea un caso hermano en el otro idioma con sus propios
  campos (título, extracto, imagen, meta box, todo) — no hace falta
  ninguna configuración extra, el tema/plugin ya lo declara traducible.
- **Los tags (Case Tags) NO se traducen** — quedan compartidos entre EN y
  ES a propósito (decisión V1: mantenerlo simple). Si escribís un tag en
  inglés, se va a ver en inglés también en la versión en español del caso.
  Si más adelante hace falta traducirlos, es un cambio chico.
- **El switcher de idioma del header y del menú mobile ahora es real** —
  antes era un texto fijo "EN / ES". Con Polylang activo y al menos 2
  idiomas configurados, muestra links reales a la traducción de la página
  actual. Sin Polylang (o con un solo idioma configurado), se ve
  exactamente igual que antes: "EN / ES" fijo, sin romper nada.
- **Home Content (About, How I Work, Connect, Header, Footer) NO es
  bilingüe todavía** — es un solo set de textos compartido entre EN y ES
  (decisión V1 aprobada, ver `docs/EDITABILITY-PLAN.md`). Escribí esos
  campos en el idioma que prefieras por ahora; la versión completamente
  bilingüe de Home queda para cuando esas secciones migren a bloques
  (V2).

**Cómo armar Home en los dos idiomas:**

1. Instalá y configurá Polylang normalmente (idiomas activos, estructura
   de URL, etc. — eso es 100% configuración de Polylang, no del tema).
2. La página "Home" que ya tenés (con el template **Estavillo — Home
   (Draft)**) queda como tu Home en el primer idioma.
3. Creá una segunda página, asignale el mismo template, y usá la caja de
   idioma de Polylang para vincularla como traducción de la primera.
4. Repetí para cada Case Study que quieras en los dos idiomas.

**Cómo armar las 5 páginas fijas (Home / Work / About / How I Work /
Contact) en los dos idiomas — mismos pasos para las 5, una a la vez:**

1. **Páginas → Agregar nueva.** Escribí el título en el idioma que
   corresponda (p. ej. "Work" o "Trabajo").
2. En el panel lateral **Página → Plantilla**, elegí el template fijo
   (**Estavillo — Home (Draft)** / **Work** / **About** / **How I Work**
   / **Contact**, según cuál estés armando).
3. Arriba a la derecha (o en el panel lateral, según tu versión de
   Polylang), elegí el **idioma** de esta página en el selector de
   Polylang, y publicá.
4. Repetí los pasos 1–3 para el segundo idioma — mismo template, título e
   idioma distintos.
5. En cualquiera de las dos páginas, usá el ícono **"+" ("Add
   translation")** de la caja de idioma de Polylang para vincularlas
   entre sí como traducciones una de la otra (si todavía no quedaron
   vinculadas automáticamente al crearlas).
6. El contenido de cada página (About: texto/timeline/educación/hobbies,
   How I Work: los 6 pasos, Contact/Work: los mismos datos de Home
   Content) sale de los filtros documentados arriba — **hoy son
   compartidos entre EN y ES** (mismo alcance que el resto de Home
   Content, ver nota más abajo), así que cargalos en el idioma que
   prefieras por ahora. El texto propio de cada página (`the_content()`,
   si le agregás bloques extra en el editor) sí es 100% independiente por
   idioma, como cualquier página de WordPress.

No hace falta ningún paso de configuración adicional en Polylang para que
esto funcione — el template seleccionado es un meta del post
(`_wp_page_template`), y cada traducción (cada idioma) es su propio post
de WordPress con su propio meta, así que cada versión de idioma puede
usar el mismo template de forma completamente independiente.

### Home Content — About, How I Work, Connect, Header, Footer (Sprint 3)

Estas secciones son singulares (no repetibles como Case Study), así que no
usan un CPT — usan una página de opciones nueva en el plugin: **Case
Studies → Home Content** (wp-admin).

**Cómo funciona:** estas secciones ya se editaban desde siempre por
filtros PHP (`es_home_about_text`, `es_home_process_steps`,
`es_contact_email`, `es_nav_links`, etc. — ver la lista completa más
abajo). La página **Home Content** no inventa un mecanismo nuevo: le da
una interfaz de wp-admin a esos mismos filtros. Por eso ningún template
del tema cambió para estos tickets — el punto de extensión ya existía.

**About** (primer campo disponible):

1. wp-admin → **Case Studies → Home Content**.
2. Sección **About**: completá **About text**, **About link (CTA URL)**
   y/o **Portrait image URL**.
3. Dejá cualquier campo vacío para mantener el placeholder actual de ese
   campo — Home nunca se rompe. **Guardar Home Content.**

**How I Work:**

1. Misma página, sección **How I Work**: 6 filas, una por paso (**Step
   1**–**Step 6**), cada una con título + descripción.
2. Podés editar **un solo paso** y dejar los otros 5 en blanco — cada paso
   se reemplaza de forma independiente; los que dejes vacíos siguen
   mostrando su placeholder actual. No hace falta completar los 6 a la vez.
3. **Link CTA de How I Work** (URL) es un campo aparte, opcional.
4. **Guardar Home Content.**

**Connect:**

1. Misma página, sección **Connect**: **Connect title** (acepta `<em>` para
   la palabra en cursiva, igual que el placeholder actual), **Connect lead
   text**, **Contact email** y **Connect link (CTA URL)**.
2. El **Contact email** es el mismo dato que usa el Footer — completarlo
   acá actualiza el mailto en las dos secciones a la vez (Connect y
   Footer), no hay que repetirlo.
3. **Guardar Home Content.**

**Header (nav links):**

1. Misma página, sección **Header (navigation links)**: 4 filas (**Nav
   link 1**–**4**), cada una con **Label** + **URL**.
2. Estos 4 links alimentan el nav de escritorio, el menú mobile y el nav
   del footer al mismo tiempo — son el mismo array (`es_nav_links()`) que
   ya compartían las tres secciones antes de este ticket. Editar un link
   acá lo cambia en los tres lugares.
3. Podés editar **una sola fila** y dejar las otras 3 en blanco; las que
   dejes vacías mantienen su label y URL actuales. Si completás el Label
   pero dejás la URL vacía, esa fila conserva su URL actual (nunca queda
   un link roto).
4. **Guardar Home Content.**

**Footer:**

1. Misma página, sección **Footer**: **LinkedIn URL**, **Behance URL** y
   **Location**.
2. Los nav links y el email de contacto del footer **ya se editan desde
   las secciones Header y Connect** de arriba (son el mismo dato
   compartido) — acá solo quedan los dos campos exclusivos del footer.
3. **Guardar Home Content.**

> Si además tenés un filtro por Code Snippets sobre el mismo hook (p. ej.
> `es_home_about_text`), gana el que se registre último — es el
> comportamiento normal de los filtros de WordPress, no un bug. No uses
> las dos vías para el mismo campo a la vez.

## Instalación (sin riesgo para el sitio actual)

1. **Subir el tema**: Apariencia → Temas → Añadir nuevo → Subir tema →
   `estavillo-child.zip` (está en `dist/` del repo). Instalar **sin activar** todavía.
2. **Previsualizar**: en Apariencia → Temas, botón **Vista previa en vivo** sobre
   Estavillo Child. El sitio en vivo no cambia.
3. **Probar la home nueva**: crear una página nueva (estado *Borrador*),
   en Atributos de página elegir el template **Estavillo — Home (Draft)** y usar
   *Previsualizar*. (Para que el template aparezca, el child theme debe estar activo
   o en vista previa del Customizer.)
4. Cuando se decida el switch definitivo: activar el child theme. Kadence sigue
   siendo el parent, así que header/footer/ajustes de Kadence se conservan.
5. **(Opcional, para editar Selected Work y/o Featured Case desde
   wp-admin)** instalar y activar el plugin **Estavillo Portfolio Core**
   (`dist/estavillo-portfolio-core.zip`) — ver "Selected Work — editable vía
   Case Studies" y "Featured Case — editable vía Case Studies" arriba. No
   es requisito para que la Home funcione: sin este plugin, ambas secciones
   simplemente muestran su contenido placeholder de siempre.

> Requiere el tema **Kadence** instalado (es el parent). WordPress 6.3+ recomendado
> (usa la estrategia `defer` para scripts; en versiones anteriores carga igual en footer).

## Opciones (Apariencia → Personalizar → Estavillo)

| Opción | Valores | Default |
|---|---|---|
| Accent color | green / orange | green |
| Desktop hero variant | network_constellation / blueprint_flow / static_fallback | network_constellation |
| Mobile hero variant | network_constellation_subtle / blueprint_flow / static_fallback | network_constellation_subtle |
| Font preset | design_system / classic_mockup | design_system |

El acento global cambia el *chrome* de marca (eyebrow, palabra destacada del
titular, botones, links) vía `--es-accent` (clase `es-accent--orange` en `<body>`).

### Preset tipográfico (Font preset)

- **`design_system`** (default) — Newsreader / Instrument Sans / Spline Sans Mono
  (las fuentes actuales; **no cambia nada** respecto de la versión anterior).
- **`classic_mockup`** — stack de sistema (serif Georgia / sans system-ui / mono
  ui-monospace), sin web fonts. Con este preset **no se piden Google Fonts**
  (más liviano). Si tu sitio previo usaba otras fuentes exactas, reemplazá los
  tres valores en `body.es-font--classic_mockup` (assets/css/tokens.css) por las
  tuyas — mismos tres roles. El switch es una clase `es-font--…` en `<body>`; los
  tamaños/escala no cambian, solo las familias.

### Semántica de color: verde + naranja coexisten (§05 del sistema visual)

La regla de marca preferida no es "verde o naranja según la página", sino
**verde primario con naranja usado con moderación para el punto de decisión**,
ambos en la misma pantalla:

- **`--es-signal` (verde)** = el sistema: camino activo/resuelto, señal viva, éxito.
- **`--es-decision` (naranja)** = la decisión: el único punto de foco/juicio humano
  (como máximo uno por vista).

Estos tokens son **fijos**, independientes del toggle de acento global. Por eso
en el hero conviven el camino verde y el diamante de decisión naranja aunque el
acento global esté en verde. El toggle green/orange se mantiene por ahora
(pedido explícito), pero la lógica de marca recomendada es la de arriba.

## Hero variants (motores)

Los motores viven en `assets/js/hero-system-map.js` (un dispatcher elige según la
variante; solo se construye la geometría que se usa). Todos: SVG + rAF, cero
librerías, `prefers-reduced-motion` → frame final instantáneo, y el rAF duerme
fuera de viewport / con la pestaña oculta.

- **`network_constellation`** (default) — red viva de nodos (constelación),
  adaptada del **Home v4**. Se **ensambla una vez**: los nodos aparecen sueltos,
  las conexiones se dibujan propagándose por BFS desde un nodo raíz, el foco se
  ilumina en **verde** con halo + anillo, y todo se asienta. Idle: respiración muy
  sutil (halo + 2–3 nodos) y un glow tipo radar. **Hover** (desktop, pointer fino):
  campo de proximidad amortiguado — brillo, tamaño y un *lean* ≤3px, **sin
  perseguir el cursor**. **Mobile** (`network_constellation_subtle`): el scroll
  desliza la atención + parallax leve; capa **detrás del texto**, opacidad baja,
  nunca un bloque aparte. El SVG es a sangre completa (`preserveAspectRatio:none`,
  viewBox en px) y una **zona de exclusión** evita poblar sobre el texto
  (legibilidad). Sin naranja: no hay punto de decisión en esta pieza.
- **`blueprint_flow`** (opcional) — el motivo **Fig.00** del sistema visual: un
  flujo `inputs → decide → resolve` sobre retícula blueprint. Se **ensambla una vez
  y se mantiene** (§17: estructura → el camino verde se dibuja → el diamante naranja
  se enciende con un pulso único → la resolución cierra → los labels mono al final).
  **Sin loop, sin persecución de cursor.** Mobile: versión simplificada.
- **`static_fallback`** — dibuja el motor por defecto en su frame final estático,
  sin listeners ni rAF.

> `system_map_nodes` (el motor de nodos con diamante de decisión naranja de la
> iteración anterior) **sigue registrado** en el JS pero ya no aparece en el
> Customizer. Se puede reactivar sumándolo al filtro `es_hero_variants`.

### Cómo se pasa la variante al frontend (y troubleshooting)

La variante seleccionada llega al motor por **dos vías redundantes**, ambas desde
`es_get_option()`:

1. **Data attribute** en el `<section class="es-hero">`: `data-hero-desktop` /
   `data-hero-mobile`.
2. **Config localizada** `window.EstavilloHeroConfig` (`wp_localize_script`), por
   si un plugin de caché/optimización reescribe el HTML y borra el atributo.

El JS lee: atributo → config → default `network_constellation`. Una variante
desconocida siempre cae al default (nunca rompe).

Los assets se versionan con **`filemtime()`** (`?ver=<mtime>`): cada cambio de un
archivo fuerza la recarga en navegador/CDN. Esto resuelve el bug donde, al
actualizar el tema, el HTML del servidor cambiaba pero el **JS/CSS cacheado
quedaba viejo** (el hero mostraba el motor anterior aunque el Customizer emitiera
la variante correcta).

**Si el hero no cambia al elegir una variante:**
- Mirá el código fuente de la página (Ctrl/Cmd+U): hay un comentario
  `<!-- Estavillo hero: desktop=… mobile=… font=… theme_v=… -->`. Si dice la
  variante correcta, WordPress está bien y el problema es **caché**.
- Purgá la caché del sitio (plugin de caché / CDN) y hacé un **hard refresh**
  (Ctrl/Cmd+Shift+R). Con el versionado por `filemtime` esto ya no debería pasar
  tras re-subir el tema.

### Layout del hero (ambos motores animados)

- **Desktop**: el SVG ocupa el área derecha/fondo, con mask de degradado hacia la
  columna de texto (legibilidad). En `blueprint_flow` los inputs emergen difusos
  desde la izquierda y las beats DECIDE/RESOLVE quedan nítidas a la derecha.
- **Mobile**: capa absoluta detrás del texto, opacidad baja, sin empujar contenido.
  El visual **nunca** es un bloque separado — vive detrás del texto (`z-index`).

### Arquitectura de variants — agregar una nueva sin reescribir el tema

Los motores se registran por nombre en un registry global. Hay **dos puntos de
extensión** y ninguno requiere tocar los archivos del tema:

1. **JS** — registrar el motor (en un archivo propio encolado *después* de
   `es-hero-system-map`, o desde el `functions.php` de un plugin):

   ```js
   window.EstavilloHero
     .register('mi_variante', function (host, ctx) {
       // host = elemento [data-es-hero-map]
       // ctx  = { hero, reduced, isMobile(), variant(), isStatic }
       // respetar ctx.isStatic (frame final) y dormir el rAF fuera de viewport
     })
     .alias('nombre_viejo', 'mi_variante'); // opcional
   ```

2. **PHP** — sumar la clave al registro filtrable para que aparezca en el
   Customizer (p. ej. desde Code Snippets):

   ```php
   add_filter( 'es_hero_variants', function ( $v ) {
       $v['mi_variante'] = array(
           'label'    => 'Mi variante',
           'contexts' => array( 'desktop', 'mobile' ),
       );
       return $v;
   } );
   ```

Garantías del dispatcher: una variante **desconocida cae al default**
(`system_map_nodes`) sin romper; `static_fallback` dibuja el default en frame
estático; los aliases resuelven valores viejos guardados. API del registry:
`register(name, fn)`, `alias(from, to)`, `resolve(name)`, `has(name)`,
`get(name)`, `list()`, `init()`. Estas verificaciones corren en las checks de
navegador de cada iteración.

## Editar contenido placeholder sin tocar archivos

Todo el copy actual es **placeholder de los mockups**. Con Code Snippets:

```php
add_filter( 'es_home_hero_title', fn() => 'Nuevo título con <em>énfasis</em>.' );
add_filter( 'es_home_hero_lead',  fn() => 'Nuevo lead.' );
add_filter( 'es_contact_email',   fn() => 'correo@dominio.com' );
add_filter( 'es_home_selected_work', function ( $cases ) {
    $cases[0]['url'] = '/work/presupuestador/';
    return $cases;
} );
```

## Compatibilidades

- **Kadence**: child theme estándar; no pisa hooks ni estilos del parent
  (todo scopeado bajo `.es-page` y variables `--es-*`).
- **Polylang**: strings de interfaz registrados en Idiomas → Traducciones de
  cadenas (grupo "Estavillo Child"); el template se asigna por página traducida.
- **WP Dark Mode**: el tema es dark-first; el modo claro está tokenizado bajo
  `[data-theme="light"]` en `<html>` para conectarlo cuando se decida.
- **prefers-reduced-motion**: hero estático (frame final) y reveals visibles
  sin animación. Sin listeners ni rAF en ese modo.

## Material de referencia — `reference/design-system/`

La carpeta `reference/design-system/` (en la raíz del repo, **fuera del tema**)
guarda el output del Claude Design Visual System v1.0 como **material fuente, no
código de producción**:

- `Estavillo-Visual-Design-System.dc.html` — el sistema visual completo.
- `support.js` — runtime del canvas de Claude Design. **Nunca se encola ni se
  ejecuta en el sitio.** Vive fuera del tema, así que WordPress no lo carga.
- `NOTES.md` — las reglas útiles extraídas (tokens, tipografía, grilla, gramática
  de diagrama, uso de verde/naranja, hero/motion), para no depender del HTML de
  213 KB al implementar.

Las reglas de ese sistema ya están aplicadas en el tema: tokens semánticos
`--es-signal`/`--es-decision`, el diamante de decisión naranja, y el motor
`blueprint_flow` (motivo Fig.00).
