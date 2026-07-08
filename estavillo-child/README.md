# Estavillo Child — child theme de Kadence

Foundation técnica del nuevo portfolio ESTAVILLO. **No toca el sitio en vivo**: es un
child theme que se instala aparte y un page template que se asigna a una página en
borrador para previsualizar.

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
│   │   ├── site.css               → chrome: header sticky + menú mobile + footer
│   │   ├── hero.css               → hero: visual detrás/al costado del copy (desktop y mobile)
│   │   └── pages-home.css         → secciones de la home (featured, process, work, about, connect)
│   └── js/
│       ├── hero-system-map.js     → motores de hero (registry, SVG + rAF, 0 librerías)
│       ├── motion.js              → reveal on-scroll (IntersectionObserver)
│       └── nav.js                 → menú mobile (overlay accesible)
├── template-parts/
│   ├── site-header.php            → nav sticky ESTAVILLO + menú mobile
│   ├── hero-home.php              → hero (copy placeholder, editable por filtros)
│   ├── featured-case.php          → 01 Main case (filtro es_home_featured)
│   ├── how-i-work.php             → 02 How I work · 6 pasos (filtro es_home_process_steps)
│   ├── selected-work.php          → 03 Selected work: card ancha + 2-up (es_home_selected_work)
│   ├── about-teaser.php           → 04 About (filtros es_home_about_*)
│   ├── footer-cta.php             → 05 Connect · "Let's talk" (es_home_cta_*, es_contact_email)
│   └── site-footer.php            → footer ESTAVILLO
├── templates/
│   └── page-home-estavillo.php    → Template Name: "Estavillo — Home (Draft)" (standalone)
└── inc/
    ├── enqueue.php                → carga condicional de assets (home-only) + config localizada
    └── theme-options.php          → Customizer: acento + variantes de hero + font preset
```

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
