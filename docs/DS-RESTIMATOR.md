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

### Defectos de la fuente, corregidos en la captura

1. **Los iframes de la comparación son `loading="lazy"`** y Chromium no rasteriza
   lo que queda fuera del viewport — tampoco alcanza con recorrer la página
   scrolleando. Sin corregirlo, 4 de los 5 pares salían en blanco. Se resuelve
   agrandando el viewport hasta cubrir el documento entero
   (`shotTallViewport()`).
2. **El tema light de la columna derecha lo aplica el build**, no la página: bajo
   `file://` el origen es opaco y el script de la fuente no puede entrar al
   iframe. Ver §12.

La fila "01 Dashboard" de la comparación apuntaba a un stub de redirección
(*"Dashboard removed from V1 · scope lock · Decision 1"*) y se borraba en la
captura. Ya no existe: la reemplazó Catálogos, que sí es una de las cinco
pantallas documentadas.

Ninguna de estas correcciones modifica el Design System: son ajustes del proceso
de captura.

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
- En desktop el ancho de trabajo arranca en `min(anchoReal, anchoDisponible)`,
  así que al abrir **nunca** hay scroll horizontal. Recién con zoom deliberado
  puede aparecer. En táctil no: ver §15, "El visor en un teléfono".
- `<dialog>.showModal()` aporta foco atrapado, capa superior y Escape. El área
  de scroll es focusable, así que las flechas y PageUp/PageDown recorren la
  pantalla sin mouse.

El lightbox compartido **no se toca ni se carga acá**: sigue igual en los case
studies.


---

## 12. La exploración de tema light

**Light NO es un tema del sistema.** Se publica como exploración y se anuncia
así en los tres lugares donde aparece: el pill `Exploración / Exploration` en la
tarjeta, la nota que la acompaña, y `System evolution`, donde figura como
*Planificado / Planned*. El hero, el pie del rail y el contador de temas no la
mencionan.

### De dónde sale

De `tokens/theme-light.css`, que ya existía en la fuente: un remap de color bajo
`[data-theme="light"]`. Nada de lo que hay ahí puede alcanzar al render por
defecto — verificado en cada build recapturando las cinco pantallas dark y
comparando hashes.

Esta pasada corrigió tres cosas del remap.

**1. La rampa de tinta estaba elegida a ojo.** Los cuatro pasos se abrían
demasiado arriba y no dejaban lugar abajo: `--re-ink-4` caía a 2.68:1 sobre
blanco y 2.38:1 sobre el canvas — exactamente el error que el tema oscuro ya
había corregido (`colors.css` documenta haber subido su propio `--re-ink-4` de
`#686d76` a `#7f858e` para pasar AA). Ahora los cuatro pasos reproducen las
RELACIONES de contraste de dark:

| paso | dark | light |
|---|---|---|
| `--re-ink` | 16.0:1 | 16.9:1 |
| `--re-ink-2` | 10.4:1 | 10.4:1 |
| `--re-ink-3` | 6.0:1 | 6.0:1 |
| `--re-ink-4` | 4.8:1 | 4.8:1 |

Sólo se movió la luminosidad; tono y saturación quedaron intactos.

**2. Los semánticos tampoco pasaban como texto.** `ok`, `warn`, `crit` e `info`
se usan como etiqueta sobre su propio tinte, y ahí daban 3.00 / 2.90 / 3.93 /
4.26:1 contra los 5.41 / 6.03 / 4.41 / 5.04:1 que las mismas badges alcanzan en
dark. Se oscurecieron hasta 4.5:1 sobre su tinte. Los `-soft` y `-line` **no**
se re-derivaron, así que el fondo del tinte no cambió: sólo se oscureció el
texto.

**3. Dos tokens de panel oscuro usados como texto sobre una card blanca.**
`.kv .v.amber` (el "3 · por recalibrar") y `.kv .v.ok-c` (el "14 / 18") pintan
con `--re-amber` y `--re-ok-bright`, que están pensados para el tile charcoal.
Sobre blanco daban 2.55:1 y 1.90:1. Son las **dos únicas** reglas de componente
del archivo, y sólo redirigen al token que el sistema ya tiene para ese trabajo
(`--re-amber-ink`, `--re-ok`). No se tocó el componente base porque eso cambiaría
dark.

### Lo que queda por debajo de AA, y por qué se deja

Medido nodo por nodo sobre el render real de las cinco pantallas: **light 56,
dark 47**. La diferencia no es un componente roto, es la asimetría del propio
esquema:

- en dark el canvas es MÁS oscuro que las cards, así que el texto gana contraste
  al caer sobre él;
- en light el canvas es más oscuro que las cards blancas, así que lo pierde.

`--re-ink-4` da 4.75:1 sobre la card y 4.03:1 sobre el inset. Llevarlo a 4.5:1
también en el inset exige `#676b76`, y ahí la separación con `--re-ink-3` cae de
1.27x a 1.13x — la jerarquía de cuatro pasos se aplana y light pasaría a ser más
estricto que dark. Se prioriza que los dos temas se lean igual. En dark ese mismo
inset da 4.39:1, o sea que tampoco llegaba.

Los 26 nodos de "Resumen cliente" que aparecen en las dos listas son la escala
propia del documento al cliente (`--p-*`), que por diseño **no** se re-tematiza:
es una hoja blanca en los dos temas.

`.arrow` (el "→" de `Escaleras → Recta`) usa `--re-line-strong` como color de
texto y falla en ambos temas (2.13:1 dark, 1.68:1 light). Es un defecto del kit,
no del remap, y arreglarlo cambiaría dark.

### La comparación estaba mostrando dark contra dark

`light-dark-comparison.html` activa la columna derecha con un script que hace
`f.contentDocument.documentElement.setAttribute('data-theme','light')`. Correcto
si el documento se sirve por http, donde padre e iframe comparten origen — pero
el build abre todo con `file://`, y ahí Chromium le da a cada documento un
**origen opaco**: el acceso falla, el `catch(e){}` del script se lo traga, y la
columna "Light" venía renderizando el tema oscuro. La comparación publicada era
dark contra dark.

Ahora el tema lo aplica el build, frame por frame (Playwright sí puede evaluar
dentro de un frame de origen opaco), y después **verifica el píxel**: si el
`background-color` computado del `<body>` de un frame light no es claro, el build
rompe. También verifica que ninguna columna dark haya recibido el atributo.

### Las cinco pantallas

La comparación tenía una fila "01 Dashboard" que apuntaba a un stub de
redirección, y le faltaba Catálogos. Ahora son las **mismas cinco** de §08, y el
build lo verifica contra `DESKTOP_SHOTS`: si alguna vez dejan de coincidir, falla
en vez de publicar una comparación incompleta.

---

## 13. Cache busting de las capturas

Las capturas se sirven desde una URL fija (`…/screens/light-dark.webp`). Cuando
el build las regenera, el archivo cambia pero la URL no, así que ni el navegador
ni el CDN tienen forma de saber que hay algo nuevo. Medido en producción: en
incógnito se veía la captura nueva y en una sesión existente seguía apareciendo
la vieja **incluso después de hard refresh** — la respuesta venía del edge de
Cloudflare, no del navegador.

`es_ds_restimator_screen_url()` arma la URL con la versión del propio archivo:

```
…/screens/light-dark.webp?v=1788790826
                              └─ filemtime()
```

Es el mismo mecanismo que `es_asset_ver()` usa para el CSS y el JS del tema. Se
aplica a **las dos** URLs de cada tarjeta: la preview del `<img>` y la que abre
el visor (`data-es-screen-src`). Alcance: sólo estas capturas.

No se usa `ES_CHILD_VERSION` porque obligaría a subirla a mano cada vez que se
recapturan pantallas, que es justo el paso que se olvida.

Verificado sobre la función real:

| | |
|---|---|
| dos llamadas sin tocar el archivo | misma URL (el caché sigue sirviendo) |
| después de reescribir el archivo | URL nueva |
| mtime restaurado | vuelve a la URL original |
| archivo inexistente | cae a `?v=<ES_CHILD_VERSION>`, nunca un `?v=` vacío |

> **Cloudflare.** Con el cache level en **Standard** (el default) el query string
> forma parte de la clave de caché, así que un `?v=` nuevo es un objeto nuevo en
> el edge. Si la zona estuviera en **Ignore Query String**, esto no alcanzaría y
> habría que versionar en la ruta del archivo, no en la query.

> **Nota operativa.** WordPress no restaura los timestamps del ZIP al instalar
> un tema: escribe los archivos, así que el `mtime` pasa a ser el momento de la
> instalación. O sea que cada reinstalación cambia la versión de todas las
> capturas, aunque la imagen sea la misma. Es una invalidación de más —una
> descarga extra, nunca una imagen vieja— y es el mismo comportamiento que ya
> tenía el resto de los assets del tema.

---

## 14. Cómo se sale del documento

Hay dos salidas y son **excluyentes**, según el toggle del header institucional.

**Header OFF** — la barra mínima sticky, con `← Volver al caso REstimator`
(§2 de `doc-overrides.css`). Sin breadcrumb.

**Header ON** — no se agrega una segunda barra. Va un breadcrumb contextual
dentro de la columna principal, justo antes del hero:

```
Proyectos / REstimator / Design System      (ES)
Work / REstimator / Design System           (EN)
```

Lo imprime `es_ds_restimator_breadcrumb()`, que **retorna temprano si el header
está apagado**. La llamada vive en el documento generado (una línea PHP que
inserta el build antes del hero), así que hereda la columna principal de la
grilla y queda alineada con el eyebrow sin repetir ningún cálculo de layout.

Reusa `template-parts/breadcrumbs.php` — el mismo partial y las mismas clases
que el Case Study y las páginas fijas — así que el color, el separador, el hover
y el truncado del último nivel ya vienen de `site.css`, que en esta página se
carga junto con el header. Lo único propio es neutralizar el `.es-container` del
partial y darle el padding lateral del documento.

- **Work / Proyectos** → `es_page_url_by_template( 'templates/page-work.php' )`,
  que resuelve por idioma (la query lleva `suppress_filters => false`, que es lo
  que habilita el filtro de Polylang).
- **REstimator** → `es_ds_restimator_case_url()`, que ya traducía vía
  `pll_get_post()`.
- **Design System** → nivel actual, sin link, con `aria-current="page"`.

Los textos salen de `es_ds_text()` y no de `es__()`, por la misma razón que la
barra mínima: `es__()` depende de que alguien cargue las String translations de
Polylang a mano.

Si el Case Study todavía no existe (plugin inactivo, caso sin publicar) ese
nivel se omite: antes que un link muerto, un nivel menos. Con un solo nivel el
breadcrumb no se imprime — sería un rótulo, no una navegación.

---

## 15. Presentación responsive: hero, subnav y visor táctil

Cuatro cosas de esta pasada. Ninguna toca el rail ni el visor de desktop.

### El hero a dos columnas

Medido a 1920 px con el contenedor ya ensanchado a 1556: todo el peso del hero
caía en los primeros ~700 px y quedaban ~800 vacíos a la derecha. Las cuatro
métricas eran columnas separadas por hairlines, que a ese ancho se leían como
una tabla a medio terminar.

Ahora el hero es `copy | art` arriba y las métricas a ancho completo abajo
(`grid-template-areas`), y las métricas son **tarjetas** con icono, dato y pie.

La composición de la derecha **no es una imagen y no inventa UI**: son las
clases reales de los especímenes del propio documento —`.sw`, `.b`, `.b.acc`,
`.st`, los `.d-*` de estado— más dos capturas que ya existían en
`assets/ds/restimator/screens/`. Sale de `heroComposition()` en
`tools/build-ds.mjs`, así que el texto sigue siendo texto: nítido a cualquier
DPI y traducido por el mismo diccionario que el resto del documento.

- La pila se inclina con **una** `transform` en `.hc-stage`
  (`perspective + rotateX/rotateY`), no con una por tarjeta.
- Sangra fuera de su columna a propósito; el recorte lo pone
  `.hero { overflow: hidden }`, así que nunca genera scroll horizontal.
- `prefers-reduced-motion` la deja plana.
- La tarjeta de controles mide 38% del escenario y no 34%: con 34% a 1440 px
  medía ~180 px contra ~190 px de los dos botones, y el `overflow:hidden` de la
  tarjeta cortaba "Secundario" al medio — se leía como un bug, no como
  profundidad. Los botones además llevan `flex-wrap`, por si un idioma trae
  etiquetas más largas.

Bajo 1101 px la composición **se saca** en vez de comprimirse: es decoración, a
media columna deja de leerse como una pila, y son dos descargas arriba del fold
justo en el viewport donde más caro salen. Las métricas pasan a 2×2.

> ⚠️ `.re-doc .hero .facts div { border-top: 1px }` del Design System matchea
> **cualquier** div descendiente, no sólo la columna. Dentro de una tarjeta eso
> volvía a dibujar la línea sobre el label, sobre el valor y sobre el pie. Se
> apagan todas y la tarjeta pone su propio borde.

### La subnav de mobile

Bajo 1100 px el DS ya soltaba el rail y lo convertía en un bloque estático: con
12 secciones eso son ~560 px de links antes del primer contenido, o sea que en
un teléfono el documento empezaba fuera de la primera pantalla (el hero
arrancaba en y≈620).

`assets/js/ds-restimator.js` lo reemplaza por una **barra sticky de una línea**
que dice en qué sección estás y despliega el resto. El hero arranca en y≈164.

- **1100 px no es un breakpoint nuevo**: es el que el propio DS ya usaba para
  soltar el rail (`assets/css/ds-restimator/doc.css`).
- La subnav **se construye a partir del rail**, no de una lista propia: si el
  documento gana o pierde una sección aparece sola, y no hay dos fuentes que
  mantener sincronizadas.
- Un solo cálculo de "qué sección está activa" alimenta al rail y a la subnav.
- El offset del scroll-spy **se mide**, no se adivina: es el borde inferior de
  la barra + 8. Con el 120 fijo del script original, en mobile la sección
  aterrizaba a ~122 px y el trigger seguía marcando la sección anterior justo
  después de saltar.
- No hay bottom nav. La navegación de una documentación es un índice, no un
  conmutador de vistas.

Mejora progresiva: **el rail se oculta únicamente cuando la subnav ya está
montada** (`body.es-ds-subnav-on`), así un fallo de JS nunca deja la página sin
navegación.

### El breadcrumb en mobile

Con el header institucional activo el breadcrumb es de tres niveles
(§14). En mobile el primero se cae: `Proyectos` ya está en el menú del header y
a 320 px los tres niveles competían con el título. Queda
`REstimator / Design System`, que es el único salto que la página no ofrece de
otra forma.

### El visor en un teléfono

Ajustar una captura de 1440 px a una ventana de 374 la dejaba **al 26%**: la
pantalla entra entera y no se lee una palabra — exactamente el "chorizo" que
este visor existe para evitar.

Con puntero grueso el visor abre a `min(anchoReal, anchoDisponible × 2)`, o sea
~52%: se ve una parte de la pantalla a un tamaño en el que el texto se lee, y el
resto se recorre con el dedo. Nunca por encima del ancho real, así que una
captura de mobile (390 px) sigue entrando completa.

Lo demás de la geometría táctil:

- La ventana es una **hoja a pantalla completa** con `100dvh` y
  `env(safe-area-inset-*)`. El CSS la dimensiona; el JS no le fija ancho ni
  alto, porque si lo hiciera la ventana cambiaría de tamaño durante el pinch
  —al crecer la imagen crece el lienzo— y la barra de título se movería bajo los
  dedos.
- El espacio disponible se mide con `visualViewport` y no con `innerHeight`: en
  iOS Safari `innerHeight` no cambia cuando la barra de URL se colapsa. En
  desktop los dos valores coinciden.
- Los controles pasan a 40 px.
- El corte es `@media (pointer: coarse)`, **no** un ancho: con `max-width:700px`
  un teléfono apaisado (844×390) caía de vuelta en la ventana de desktop. Así
  además el CSS coincide con el `coarse()` del JS.

### Pinch-to-zoom

Sin ningún handler, un pinch sobre la pantalla ampliada lo toma el navegador y
hace zoom de la **página** de WordPress: el visor se agranda entero, incluida su
barra, y el documento queda desencuadrado.

Las dos mitades hacen falta:

- **`touch-action` en el lienzo.** Al ancho de ajuste vale `pan-y`; con la
  captura más ancha que el lienzo (`.is-wide`) vale `pan-x pan-y`. En los dos
  casos el scroll de un dedo lo sigue haciendo el navegador —nativo, con
  inercia— y sólo el gesto de dos dedos queda para nosotros. **Nunca `none`**:
  eso mataría la forma principal de recorrer una captura alta.
- **Pointer Events.** Con dos punteros, la distancia entre ellos controla la
  escala y el punto medio hace de ancla, así el zoom tira de donde están los
  dedos y no del centro. `preventDefault()` se llama **sólo** con dos punteros.

Los listeners van en el lienzo y no en el `<dialog>`, así la barra, el %, los
botones y el cerrar nunca escalan.

---

## 16. Estados interactivos: de dónde salía el azul

En producción aparecían superficies **azules** al interactuar con la subnav de
mobile y con el botón de cerrar del visor. No es un color de este proyecto: lo
pone el tema padre, que estila `<button>` por nombre de elemento y le pinta
`background`, `border` y `box-shadow` propios en `:hover` y en `:focus`. Es el
mismo mecanismo ya documentado en vivo en dos lugares del repo —el círculo azul
de las flechas del índice del Case Study (`case-figure-lightbox.css`) y el texto
azul de los botones sin clase (`components.css`)—, sólo que acá golpeaba a
controles agregados después.

### Los dos huecos

| Hueco | Por qué pasaba |
|---|---|
| La armadura de `.re-doc button:hover/:focus` | Sólo neutralizaba `box-shadow`. Nunca tocó `background-color` ni `border-color`. |
| El visor entero | `ds-screen-viewer.js` hace `document.body.appendChild(dialog)`: el `<dialog>` **no está dentro de `.re-doc` ni de `.es-page`**, así que no le llega ni la armadura del documento ni la red de foco de `base.css`. Y como el JS le da el foco al abrir (`closeBtn.focus()`), el azul era lo primero que se veía. |

Además, en la barra mínima los links sólo declaraban `:hover` y
`:focus-visible`: en el `:focus` **plano** —un click con mouse— quedaban con el
`a:focus` del tema padre.

### Por qué no se arregla en la raíz

Poner `background: none !important` sobre `.re-doc button:hover` (0,1,1) le
ganaría también a `.re-doc .b:hover` (0,2,1) de `doc.css`, que es el hover real
de los botones de demostración del sistema: se apagarían los especímenes de §02
para arreglar la subnav. Por eso las reglas son **por control**, como ya lo hace
`.es-lightbox__close` para este mismo problema.

### Qué token se reusa

El verde interactivo del portfolio, sin inventar nada:

| Token | Valor (dark) | Dónde ya se usaba |
|---|---|---|
| `--es-accent` | `#58b183` | `.es-btn`, links, y la red de foco de `base.css` |
| `--es-accent-soft` | `rgba(88,177,131,.12)` | el relleno de `.es-btn` en hover |

Siguen al Customizer (`body.es-accent--orange`) y al modo claro como el resto
del sitio. El patrón también es el de `.es-btn`: **hover, `:focus-visible` y
`:active` comparten superficie** —fondo del acento con transparencia y borde del
acento— y `:focus-visible` **suma** el anillo. El `:focus` plano no dibuja
anillo, igual que en todo el sitio, pero sí limpia el relleno del tema padre.

`!important` sólo en las propiedades que pinta el tema padre: sin él,
`button:hover` (0,1,1) empata con `.esv__btn:hover` y decide el orden de carga,
que contra un tema padre no está garantizado. **Nunca `outline: none`**.

### Lo que NO cambió

- El marcador ámbar del ítem actual de la subnav (`box-shadow: inset 3px`) —
  es estado, no interacción, y espeja el marcador del rail.
- El chip "Ampliar" de las previews, que sigue en ámbar: es la afordancia
  propia del Design System, no un estado de foco.
- Los hovers de los botones de demostración del documento (`.b`, `.b.acc`).
- Geometría: los botones ya eran `border-box`, así que el borde de 1px del
  hover **no mueve nada** — medido en reposo y en hover, mismo tamaño.

### Cómo se verifica

`state-qa.mjs` levanta la página con una **simulación adversarial del CSS del
tema padre** (`kadence-sim.css`, que reproduce el mecanismo documentado arriba a
la misma especificidad), recorre cada control nuevo en reposo / hover / foco de
teclado / activo y falla si algún color computado cae en la zona azul. Antes del
arreglo: 51 fallos. Después: 0.
