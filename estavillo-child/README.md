# Estavillo Child — child theme de Kadence

Foundation técnica del nuevo portfolio ESTAVILLO. **No toca el sitio en vivo**: es un
child theme que se instala aparte y un page template que se asigna a una página en
borrador para previsualizar.

## Qué incluye

```
estavillo-child/
├── style.css                      → cabecera del child theme (Template: kadence)
├── functions.php                  → constantes, includes, helpers Polylang (es__)
├── screenshot.png                 → miniatura en Apariencia → Temas
├── assets/
│   ├── css/
│   │   ├── tokens.css             → design tokens --es-* (dark-first, light, acento green/orange)
│   │   ├── base.css               → tipografía y utilidades bajo .es-page
│   │   ├── layout.css             → contenedores, secciones, grillas
│   │   ├── components.css         → botones, pills, cards, reveal
│   │   ├── hero.css               → hero: visual detrás/al costado del copy (desktop y mobile)
│   │   └── pages-home.css         → secciones específicas de la home
│   └── js/
│       ├── hero-system-map.js     → mapa de sistema animado (SVG + rAF, 0 librerías)
│       └── motion.js              → reveal on-scroll (IntersectionObserver)
├── template-parts/
│   ├── hero-home.php              → hero (copy placeholder, editable por filtros)
│   ├── selected-work.php          → grilla de casos (editable por filtro es_home_selected_work)
│   └── footer-cta.php             → "Let's talk" + banda de intersección
├── templates/
│   └── page-home-estavillo.php    → Template Name: "Estavillo — Home (Draft)"
└── inc/
    ├── enqueue.php                → carga condicional de assets (home-only para hero/JS)
    └── theme-options.php          → Customizer: acento + variantes de hero
```

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
