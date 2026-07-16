<?php
/**
 * Pattern content — Case Study — Canonical Starter.
 *
 * Arranque limpio y corto para un caso NUEVO (Trazur, French Bakery,
 * Samic…): Reading → Split → Wide → Reading de cierre. Copy de andamiaje
 * entre {llaves} para reemplazar — sin contenido real de ningún caso.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:estavillo/case-section {"anchor":"overview","label":"Fig. 00 — Resumen","heading":"{Una frase que resuma el resultado del caso.}","lead":"{Dos o tres oraciones: qué es el proyecto, para quién, y qué problema real resuelve. Honesto — sin inventar números.}","layout":"reading"} -->
<!-- wp:paragraph -->
<p>{Primer párrafo de contexto: dónde arranca la historia y por qué importa.}</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"decision","label":"Fig. 01 — La decisión","heading":"{El punto clave, antes de su evidencia.}","layout":"split-left"} -->
<!-- wp:estavillo/case-split-content -->
<!-- wp:paragraph -->
<p>{Una idea corta — cuatro a seis líneas. Un solo punto por Split, no un capítulo entero.}</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-split-content -->

<!-- wp:estavillo/case-split-media -->
<!-- wp:estavillo/case-figure {"placeholderLabel":"evidencia del punto","tag":"{asset: por definir}","caption":"{Caption corto, pegado a su figura.}"} /-->
<!-- /wp:estavillo/case-split-media -->
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
