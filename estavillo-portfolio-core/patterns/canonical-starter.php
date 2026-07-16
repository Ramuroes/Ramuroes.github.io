<?php
/**
 * Pattern content — Case Study — Canonical Starter.
 *
 * Arranque corto y flexible para un caso NUEVO (Trazur, French Bakery,
 * Samic…): Content (intro) → Content con Columns 40/60 → Wide (artefacto)
 * → Reading (cierre). Composición interna con bloques Columns/Column
 * NATIVOS de Gutenberg. Copy de andamiaje entre {llaves} para
 * reemplazar — sin contenido real de ningún caso.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:estavillo/case-section {"anchor":"overview","label":"Fig. 00 — Resumen","heading":"{Una frase que resuma el resultado del caso.}","lead":"{Dos o tres oraciones: qué es el proyecto, para quién, y qué problema real resuelve. Honesto — sin inventar números.}"} -->
<!-- wp:paragraph -->
<p>{Primer párrafo de contexto: dónde arranca la historia y por qué importa. A ancho completo — sin Columns todavía.}</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"decision","label":"Fig. 01 — La decisión","heading":"{El punto clave, antes de su evidencia.}"} -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:paragraph -->
<p>{Una idea corta — cuatro a seis líneas. Un solo punto por columna, no un capítulo entero. Cambiá la proporción 40/60 desde los controles de Gutenberg si el caso lo pide.}</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:estavillo/case-figure {"placeholderLabel":"evidencia del punto","tag":"{asset: por definir}","caption":"{Caption corto, pegado a su figura.}"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"system","label":"Fig. 02 — El sistema","heading":"{El artefacto central del caso, a ancho completo.}"} -->
<!-- wp:estavillo/case-figure {"variant":"wide","placeholderLabel":"pantalla o diagrama principal","tag":"{asset: por definir}","caption":"{Qué está mostrando esta captura/diagrama y por qué importa.}"} /-->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"learnings","label":"Fig. 03 — Cierre","heading":"{Qué quedó demostrado y qué sigue.}","layout":"reading"} -->
<!-- wp:paragraph -->
<p>{Cierre honesto: resultados reales si los hay, límites si los hay, próximo paso concreto.}</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-quote {"quote":"{Una cita real del cliente o del equipo — o borrá este bloque.}","cite":"{Quién lo dijo}"} /-->
<!-- /wp:estavillo/case-section -->
CONTENT;
