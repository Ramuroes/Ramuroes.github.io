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
| Desktop hero variant | system_map / static_fallback | system_map |
| Mobile hero variant | system_map_subtle / static_fallback | system_map_subtle |

El acento cambia todo el sitio vía `--es-accent` (clase `es-accent--orange` en `<body>`).

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

## Hero system map — comportamiento

- **Carga**: líneas y nodos aparecen en ~2.5s con barrido; el camino de decisión
  (acento) se dibuja al final.
- **Reposo**: casi estático; respiración sutil de opacidad en algunos nodos.
  El rAF duerme fuera de viewport y con pestaña oculta.
- **Hover** (desktop): nodos/conexiones cercanas se iluminan con el acento, con
  inercia. **Touch** (mobile): pulso local que decae solo. Sin partículas.
- **Desktop**: el SVG ocupa el área derecha/fondo, con mask de degradado hacia
  la columna de texto. **Mobile**: capa absoluta detrás del texto, opacidad baja,
  sin empujar contenido.
