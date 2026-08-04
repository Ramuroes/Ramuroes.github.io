# Backup — Case Flow v1 (modelo de grafo, previo a la v2 de vértices)

Copia completa de `estavillo/case-flow` tal como quedó en el commit `9784d2b`,
**antes** de agregar `estavillo/case-flow-v2`.

## Importante: no hace falta restaurar nada para seguir usando la v1

La v2 es un **bloque nuevo y separado**. La v1 sigue registrada, sigue
enqueueando su propio CSS y su propio JS, y sigue renderizando exactamente
igual — está verificado con geometría computada en 1440 / 1280 / 900 / 390 px,
con el CSS de la v2 cargado al lado (ver `flow2-engine.mjs`, sección 1).

Este backup existe sólo por si alguna vez hay que volver al estado exacto de
`9784d2b`, o para comparar implementaciones.

## Qué hay acá

| Archivo | Va a |
|---|---|
| `render.php` | `estavillo-portfolio-core/blocks/case-flow/render.php` |
| `edit.js` | `estavillo-portfolio-core/blocks/case-flow/edit.js` |
| `block.json` | `estavillo-portfolio-core/blocks/case-flow/block.json` |
| `case-flow.css` | `estavillo-child/assets/css/case-flow.css` |
| `case-flow.js` | `estavillo-child/assets/js/case-flow.js` |
| `trazur-gutenberg-es.html` | `docs/content/trazur-gutenberg-es.html` |
| `trazur-gutenberg-en.html` | `docs/content/trazur-gutenberg-en.html` |

## Restaurar

Desde la raíz del repo:

```bash
cp docs/backups/case-flow-v1-graph/render.php   estavillo-portfolio-core/blocks/case-flow/render.php
cp docs/backups/case-flow-v1-graph/edit.js      estavillo-portfolio-core/blocks/case-flow/edit.js
cp docs/backups/case-flow-v1-graph/block.json   estavillo-portfolio-core/blocks/case-flow/block.json
cp docs/backups/case-flow-v1-graph/case-flow.css estavillo-child/assets/css/case-flow.css
cp docs/backups/case-flow-v1-graph/case-flow.js  estavillo-child/assets/js/case-flow.js
cp docs/backups/case-flow-v1-graph/trazur-gutenberg-es.html docs/content/trazur-gutenberg-es.html
cp docs/backups/case-flow-v1-graph/trazur-gutenberg-en.html docs/content/trazur-gutenberg-en.html
```

Equivalente por git:

```bash
git checkout 9784d2b -- estavillo-portfolio-core/blocks/case-flow \
                        estavillo-child/assets/css/case-flow.css \
                        estavillo-child/assets/js/case-flow.js \
                        docs/content/trazur-gutenberg-es.html \
                        docs/content/trazur-gutenberg-en.html
```

## Quitar la v2 del todo

Si además hay que sacar la v2 de circulación:

1. Borrar `estavillo-portfolio-core/blocks/case-flow-v2/`.
2. Sacar `'case-flow-v2'` de `es_case_blocks_list()` y la línea
   `es-editor-case-flow-v2` de `es_case_blocks_editor_theme_css()`, ambas en
   `estavillo-portfolio-core/includes/case-blocks.php`.
3. Borrar `estavillo-child/assets/css/case-flow-v2.css` y el bloque
   `if ( $es_has_flow_v2 )` de `estavillo-child/inc/enqueue.php`.
4. Borrar `docs/content/trazur-gutenberg-{es,en}-flow-v2.html`.

Nada de eso toca la v1: los archivos son disjuntos salvo `case-blocks.php` y
`enqueue.php`, donde la v2 sólo agrega líneas.

## Nota sobre el contenido

Los archivos de contenido de la v1 (`trazur-gutenberg-es.html` / `-en.html`)
**no fueron modificados** por la v2 — siguen en el repo tal cual. La v2 vive en
`trazur-gutenberg-es-flow-v2.html` / `-en-flow-v2.html`, que son copias del
mismo case study con el bloque de flujo cambiado. Las dos versiones se pueden
pegar en WordPress y comparar.
