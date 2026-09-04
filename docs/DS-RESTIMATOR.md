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
| `node tools/build-ds.mjs html` | Regenera `master-es.php` **y** `master-en.php`. **Corre después de `shots`**: lee las dimensiones que deja el manifest. |
| `node tools/build-ds.mjs` | Los tres, en el orden correcto. |

El script **falla fuerte** —no genera nada a medias— si:

- queda algún `href`/`src` a un archivo `.html` local;
- queda algún `<iframe>`;
- ninguna transformación aplicó (señal de que la fuente cambió de estructura);
- las pantallas mobile de la spec dejan de ser 3;
- `inc/enqueue.php` no encola las familias tipográficas que declara el DS;
- **queda algún segmento sin traducir al inglés** (escribe la lista completa en
  `docs/ds-src/restimator/missing-en.txt`). Para iterar sin que falle, exportá
  `DS_ALLOW_MISSING=1`.

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
| `docs/ds-src/restimator/i18n.json` | ❌ | ✅ **el diccionario de traducción** |
| `estavillo-child/ds/restimator/master-en.php` | ✅ | ❌ se sobrescribe |
| `estavillo-child/inc/ds-restimator.php` | ❌ | ✅ |

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
- **+1 exploración** — la comparación light/dark. No es una pantalla de
  producto, así que **no entra en la grilla ni en el contador**: sale abajo, en
  su propio bloque, con el pill `Exploración` y una línea que aclara que el
  sistema publicado tiene un solo tema. Antes iba mezclada en la grilla, o sea
  que la sección mostraba 6 tarjetas bajo un contador que dice "5 desktop", y la
  exploración se leía como una pantalla más (y como prueba de que existe un tema
  light). El build lo garantiza: `DESKTOP_SHOTS` se filtra por `kind`, y falla
  si las de `kind:'screen'` dejan de ser 5.

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

### El defecto que rompía el contenedor (y el guard que lo impide)

Durante meses el documento se publicó con `.pad` cerrando 20 KB antes de
tiempo: el final de §08 ("Otras piezas"), §09 y §10 quedaban **fuera** del
contenedor y se veían pegados a los bordes. Nada lo detectaba — el HTML tenía
todo el contenido, sin refs rotas ni iframes, y el navegador lo renderizaba sin
quejarse.

Eran **dos bugs distintos que producían el mismo síntoma**:

1. **El parser de `ds-i18n.mjs` no entendía `<?php … ?>` dentro de un
   atributo.** Buscaba el `>` de cierre con un `indexOf` pelado, y lo
   encontraba dentro de `data-es-screen-src="<?php … ?>"`. La etiqueta quedaba
   truncada, cada `<?php` siguiente se trataba como un elemento sin nombre, y al
   serializar aparecían 36 `</>` y niveles abiertos de más. Se arregla con
   `findTagEnd()`, que ignora los `>` que viven dentro de comillas.
2. **El splice del tile de mobile dejaba un `</div>` huérfano.** Cerraba el
   tramo buscando el primer `'</div></div>'`, que en
   `…<div class="ov"></div></div></div>` matchea los cierres de `.ov` y `.vp` y
   deja sin consumir el de `.shot`. Se arregla contando anidamiento
   (`endOfDiv()`).

Ahora `verifyStructure()` corre en cada build y **rompe** si el documento no
está bien anidado, si aparece un `</>`, o si alguna `<section class="sec">` cae
fuera de `.pad`. Verificado contra el archivo que estaba publicado: lo detecta.

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

---

## 7. Los dos idiomas

`master-es.php` y `master-en.php` **salen de la misma fuente**. El build hace
dos pasadas sobre el mismo documento: primero las transformaciones estructurales
(que emiten ya en el idioma destino), después el diccionario de
`docs/ds-src/restimator/i18n.json`.

El diccionario está indexado por el **HTML interno del bloque en español**, y
cada entrada tiene:

```json
"Densidad operativa. El estimador carga medidas…": {
  "en": "Operational density. The estimator enters measurements…",
  "es": "…"   // opcional: sólo si el español de la fuente hay que corregirlo
}
```

Se traduce **a nivel de bloque, no de nodo de texto**: una frase suele venir
partida por markup inline (`El token real es <span class="mono">#7f858e</span>`),
y traducir fragmento por fragmento daría un inglés con el orden de palabras del
español. Tomando el bloque hoja entero, la traducción controla el orden y
conserva los tramos técnicos intactos.

**Qué nunca se traduce**, en ninguno de los dos idiomas: `<span class="mono">`,
`<code>`, celdas `.tok`, `<svg>`, y todo segmento sin letras. Ahí viven los
tokens, las rutas y los identificadores: traducirlos los rompería.

### Cuando el Design System se actualiza

1. Reemplazá la fuente y corré `node tools/build-ds.mjs`.
2. Si hay texto nuevo, el build **falla** y lista los segmentos sin traducir en
   `docs/ds-src/restimator/missing-en.txt`.
3. Agregá esas claves a `i18n.json` con su `en` y volvé a correr.

Los segmentos que no cambiaron conservan su traducción: la clave es el texto
español, no una posición.

> ⚠️ Nunca regeneres `i18n.json` desde cero: perdés las traducciones. Toda
> herramienta que lo reescriba tiene que preservar las entradas existentes.

### Reglas de idioma aplicadas

Se traduce navegación, títulos, labels, explicaciones, captions, ayudas,
botones y todo el texto editorial. **No** se traducen nombres de componentes
(`Button`, `AppShell`, `StatTile`), tokens (`--re-*`), nombres de archivo, APIs
ni identificadores del producto.

Quedan a propósito en español dentro de la versión inglesa: los nombres de
archivo reales de los artefactos del proyecto, un nombre propio en datos de
ejemplo, y el microcopy español citado como **ejemplo** en la sección de Voz
(“Agregá notas…”, “buscá, filtrá y retomá”) — ahí el español ES el contenido.

---

## 8. Chrome institucional por página

El header y el footer del portfolio son opcionales **por página**, desde el meta
box “REstimator Design System” en el editor. Los dos vienen **activos por
defecto**, sin sembrar nada en la base de datos: la ausencia de meta se lee como
activo, así que una página ya publicada no necesita ninguna migración.

Cuando están activos se imprimen los `template-parts/site-header.php` y
`site-footer.php` **reales** del portfolio — no una copia—, dentro del wrapper
`.es-page` que `base.css` documenta como ancestro necesario del header sticky.

Con el header apagado queda `template-parts/ds-topbar.php`: una barra mínima
con “← Volver al caso REstimator”, para que nunca haya una página sin salida.

### Strings de esta página

Viven en `es_ds_text()` (`inc/ds-restimator.php`), resueltos por
`es_ds_restimator_lang()`. **No** usan `es__()`/Polylang a propósito: esa tabla
guarda su texto en inglés y depende de que alguien cargue la traducción a mano
en wp-admin; sin ese paso, la página en español mostraba el chrome en inglés.
El sistema de traducciones del resto del portfolio no cambia.

---

## 9. Rail sticky y header

El rail del Design System es `position:sticky; top:0; height:100vh`. Con el
header institucional activo hay que descontarle su altura, o la metadata del pie
del rail queda empujada fuera de la vista.

El offset usa los **mismos valores que `.es-case-index`** en `case-study.css`,
que ya tenía este problema resuelto: 66px de header, 98px con la admin bar de
escritorio, 112px con la de mobile. Todo gateado por `.es-header-sticky`, la
clase que el propio tema imprime vía `body_class`.

Si el header cambia de altura, hay **un solo** número que actualizar, y está en
los dos archivos (`case-study.css` y `doc-overrides.css`).

La navegación mínima también es sticky, así que tiene su propio offset
(`--es-ds-topbar-h`, 48px) gateado por `.es-ds-minimal-nav` — una clase que
imprime el template según qué barra se haya renderizado. Sin ella el CSS no
puede distinguir "sin header" de "header estático".

La metadata del pie del rail lleva además `position: sticky; bottom: 0`.
`margin-top:auto` sólo la manda al fondo cuando sobra alto; el rail es un
scroller, y con el viewport bajo o con zoom la lista de secciones no entra y el
pie se iba con el scroll.

---

## 10. Una sola geometría para toda la página

El header institucional, la navegación mínima, el documento y el footer
comparten la grilla del Design System: `[rail 264px][área principal]`.

Antes cada franja traía la suya. El chrome del portfolio es un `.es-container`
(max-width 1140px, centrado); el documento arranca pegado a la izquierda con el
rail de 264px. Medido a 1920px: la marca ESTAVILLO empezaba en x=438 y la del
rail en x=22 — 416px de desfase entre dos elementos que el ojo lee como la misma
columna. La página se veía como dos sitios pegados.

Los números viven en variables sobre `body.es-ds-page`, y **no son nuevos**: son
los que el Design System ya usaba.

| Variable | Valor | De dónde sale |
|---|---|---|
| `--es-ds-rail` | 264px | `.doc { grid-template-columns: 264px … }` |
| `--es-ds-rail-pad` | 22px | `.rail` (12px) + `.rail-brand` (10px) |
| `--es-ds-gutter` | 40px | `--re-s8`, el padding lateral de `.pad` y `.hero` |
| `--es-ds-content` | 1180px, fluido ≥1500px | el `max-width` de `.pad` |
| `--es-ds-topbar-h` | 48px | alto de la navegación mínima |

`.pad`, `.hero .in`, el header, la barra mínima y el footer derivan todos de
ahí, así que **se ensanchan juntos**. Debajo de 1100px el rail deja de existir
(regla del propio DS) y el chrome vuelve a un contenedor normal, con el mismo
margen lateral que toma el documento ahí (`--re-s5`).

---

## 11. Visor de pantallas

`assets/js/ds-screen-viewer.js` — **propio de esta página**, no el lightbox de
Case Figure.

Los dos son `<dialog>` modales, pero resuelven cosas distintas. El del portfolio
ajusta la imagen entera al viewport y después hace pan/zoom, que es lo correcto
para una foto; con una captura full-page de 1440×3224 eso la reducía al 28%: la
pantalla entraba completa pero no se leía nada, y lo que se veía era una
infografía vertical, no una interfaz.

El visor del DS abre la pantalla **a su ancho real** y limita el alto al del
navegador: lo que sobra se recorre con scroll vertical, como se recorrería la
aplicación.

- `data-es-screen-cssw` trae el ancho de la **interfaz** (1440 desktop, 390
  mobile), no el del archivo — las capturas están tomadas a 2× y 3×. Sin ese
  dato el visor no puede saber a qué escala es "100%".
- El ancho de trabajo arranca en `min(anchoReal, anchoDisponible)`, así que al
  abrir **nunca** hay scroll horizontal. Recién con zoom deliberado puede
  aparecer.
- `<dialog>.showModal()` aporta foco atrapado, capa superior y Escape. El área
  de scroll es focusable, así que las flechas y PageUp/PageDown recorren la
  pantalla sin mouse.

El lightbox compartido **no se toca ni se carga acá**: sigue igual en los case
studies.

