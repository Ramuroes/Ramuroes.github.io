# REstimator Design System — integración en el portfolio

Publica la documentación maestra del Design System de REstimator dentro del
portfolio, en `/lab/restimator-design-system/`, enlazada desde el Case Study de
REstimator.

El objetivo es que **una actualización del Design System no obligue a rehacer la
integración**: se reemplaza la fuente y se vuelve a correr un script.

---

## 1. Cómo actualizar el Design System

```bash
# 1. Reemplazar la fuente (mismos nombres de archivo)
#    docs/ds-src/restimator/
#      master-documentation.html      <- el HTML LIMPIO del proyecto
#      light-dark-comparison.html
#      mobile-v1-spec.html
#      styles.css  tokens/*.css  docs/ds-master.css
#      ui_kits/presupuestador/*.html + kit.css + quotes.js + shell.js

# 2. Regenerar todo
NODE_PATH=/opt/node22/lib/node_modules node tools/build-ds.mjs

# 3. Revisar el diff y rearmar el ZIP del theme
git diff --stat
```

Eso es todo. No hay que tocar el template, ni el CSS, ni el PHP.

> **Importante:** la fuente es el HTML **limpio** (~126 KB), *no* el
> `… (standalone).html` de ~1,8 MB. Ese último no es un documento: es un bundle
> auto-extraíble que desempaqueta assets en base64 con JavaScript y reemplaza el
> DOM. No sirve como fuente ni es portable.

### Stages sueltos

| Comando | Qué hace |
|---|---|
| `node tools/build-ds.mjs css` | Regenera `tokens.css` + `doc.css` scopeados. |
| `node tools/build-ds.mjs shots` | Recaptura las pantallas (necesita Playwright + red para las fuentes). |
| `node tools/build-ds.mjs html` | Regenera `master-es.php`. **Corre después de `shots`**: lee las dimensiones que deja el manifest. |
| `node tools/build-ds.mjs` | Los tres, en el orden correcto. |

El script **falla fuerte** —no genera nada a medias— si:

- queda algún `href`/`src` a un archivo `.html` local;
- queda algún `<iframe>`;
- ninguna transformación aplicó (señal de que la fuente cambió de estructura);
- las pantallas mobile de la spec dejan de ser 3;
- `inc/enqueue.php` no encola las familias tipográficas que declara el DS.

---

## 2. Qué genera, y qué NO hay que editar a mano

| Archivo | Generado | Editar a mano |
|---|---|---|
| `estavillo-child/assets/css/ds-restimator/tokens.css` | ✅ | ❌ se sobrescribe |
| `estavillo-child/assets/css/ds-restimator/doc.css` | ✅ | ❌ se sobrescribe |
| `estavillo-child/assets/css/ds-restimator/doc-overrides.css` | ❌ | ✅ **único CSS propio** |
| `estavillo-child/ds/restimator/master-es.php` | ✅ | ❌ se sobrescribe |
| `estavillo-child/assets/ds/restimator/screens/*.webp` | ✅ | ❌ se sobrescriben |
| `estavillo-child/templates/page-restimator-ds.php` | ❌ | ✅ |
| `estavillo-child/template-parts/ds-topbar.php` | ❌ | ✅ |
| `estavillo-child/inc/ds-restimator.php` | ❌ | ✅ |
| `estavillo-child/assets/js/ds-restimator.js` | ❌ | ✅ |
| `docs/ds-src/restimator/screens-meta.json` | ✅ | ❌ metadata de build |

`docs/` **no** entra al ZIP del theme, así que ni la fuente ni el manifest viajan
a producción.

---

## 3. Publicar la página en WordPress

1. **Páginas → Añadir nueva**, título *REstimator Design System*.
2. Plantilla: **Estavillo — REstimator Design System**.
3. Slug `restimator-design-system`, con página superior `Lab` (crear la página
   `Lab` si no existe) → queda en `/lab/restimator-design-system/`.
4. Publicar. **El cuerpo se deja vacío**: el contenido no sale de `post_content`
   (ver §4).
5. Enlazar desde el Case Study de REstimator con un CTA
   *"Explore the Design System"*.

### Idiomas (Polylang)

Duplicar la página con **"+ Agregar traducción"**, misma plantilla.

El **chrome** (barra superior, labels del visor) ya está traducido: son strings
del tema registrados en Polylang por `es_child_ui_strings()`.

El **contenido del Design System está en español y no se traduce**. La
arquitectura ya soporta inglés: en cuanto exista
`estavillo-child/ds/restimator/master-en.php`, `es_ds_restimator_lang()` lo toma
solo. Mientras no exista, la página en inglés sirve el documento en español —
fallback deliberado, no se inventan traducciones.

---

## 4. Decisiones de arquitectura

**El documento no vive en `post_content`.** Son ~130 KB de markup muy específico
(rail de navegación, 11 secciones, tablas densas, demos de componentes hechas con
HTML+CSS). Gutenberg lo rompería al parsearlo, sería inmanejable en el editor, y
—lo más importante— no se podría regenerar desde la fuente. Vive como partial del
tema.

**No se usa iframe.** Se evaluó y no hay razón técnica que lo justifique: el DS
es HTML semántico + 37 KB de CSS + un scroll-spy de 12 líneas. Un iframe rompería
los anchors, el scroll-spy, el sticky del rail, el deep-linking y el SEO, y
agregaría scroll anidado.

**La página no imprime el chrome del portfolio.** El Design System trae su propio
rail sticky en la columna izquierda; un header del portfolio encima competiría de
frente. Sólo se imprime la barra institucional mínima, no sticky.

**Aislamiento CSS, en las dos direcciones.**

- *Hacia afuera*: el CSS original trae `html`, `body`, `*`, `a`, `svg` y clases
  genéricas (`.hero .body .main .sub .card-h .tag .on .b .k .d .r .v`) que
  romperían el portfolio. El build scopea los 256 selectores bajo `.re-doc`.
- *Hacia adentro*: la página imprime `wp_head()`, así que el CSS global de
  Kadence se carga igual. `doc-overrides.css` neutraliza con `:where()` lo que
  Kadence impone sobre elementos genéricos (headings, listas, tablas, enlaces y
  sobre todo `<button>`, que Kadence pinta en `:hover`/`:focus`).
- Los tokens `--re-*` quedan en `:root`: son custom properties con prefijo propio
  y no colisionan con `--es-*`.

**El visor de pantallas es el del portfolio.** `assets/{js,css}/case-figure-lightbox.*`,
sin una línea nueva: se dispara con `[data-es-zoom-trigger]`, que no está acoplado
a ningún bloque.

---

## 5. Pantallas: qué se captura y por qué

El contador del documento dice **5 desktop · 3 mobile**, y eso es lo que se
captura:

- **5 desktop** — las pantallas de producto del UI kit: Calculadora, Historial,
  Editor de producto, Resumen cliente, Catálogos.
- **3 mobile** — `01 Calculadora`, `02 Inicio`, `03 Historial` de la spec Mobile v1.
  El cuarto `device-cap` de la spec es un **estado** de Calculadora (desglose en
  bottom sheet), no una pantalla.
- **+1 vista** — la comparación light/dark. Es una vista de auditoría, no una
  pantalla de producto: por eso no entra en el contador, pero sí se publica
  porque ya era uno de los tiles del documento.

No se capturan pantallas extra sólo porque existan archivos HTML en el proyecto
(hay variantes `.light.html` y un `QA.html` que el documento no expone).

### Dos defectos de la fuente, corregidos en la captura

1. **Los iframes de la comparación son `loading="lazy"`** y Chromium no rasteriza
   lo que queda fuera del viewport — tampoco alcanza con recorrer la página
   scrolleando. Sin corregirlo, 4 de los 5 pares salían en blanco. Se resuelve
   agrandando el viewport hasta cubrir el documento entero
   (`shotTallViewport()`).
2. **Su fila "01 Dashboard" apunta a un stub de redirección.** El propio kit lo
   marca: *"Dashboard removed from V1 (scope lock · Decision 1)"*. Es una fila
   obsoleta de la fuente y se excluye de la captura, para no publicar un panel
   vacío. El resto del documento no se toca.

Ninguna de las dos correcciones modifica el Design System: son ajustes del
proceso de captura.

---

## 6. Arreglos responsive

El documento original tenía **overflow horizontal real en mobile**: a 390 px el
`scrollWidth` daba 632 px contra un `clientWidth` de 390. Las causas no eran las
previews (ya estaban contenidas) sino cadenas largas sin corte en
`<span class="mono">` de §02 y las tarjetas de §10, que no encogían.

Se arregla en `doc-overrides.css`, **en el elemento que desborda**:
`overflow-wrap` en `.mono` y `min-width: 0` en los ítems de grilla.

> ⚠️ **Nunca** poner `overflow-x: hidden` en un ancestro. El rail del DS es
> `position: sticky` y un `overflow` en un ancestro lo rompe — es la misma razón
> por la que en este repo `.es-page` nunca lleva `overflow-x`.

El anillo de foco de los triggers va con `outline-offset` **negativo**:
`assets/css/base.css` impone un offset positivo con `!important` para todo el
sitio, y `.shot { overflow: hidden }` del DS lo recortaría, dejando el foco de
teclado invisible.
