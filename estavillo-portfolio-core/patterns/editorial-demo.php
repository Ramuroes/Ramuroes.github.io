<?php
/**
 * Pattern content — Case Study — Editorial System Demo.
 *
 * Página de laboratorio del sistema editorial CORREGIDO: siete capítulos
 * que demuestran los tres anchos del case-section (Content/Reading/Wide)
 * y la composición interna con bloques Columns/Column NATIVOS de
 * Gutenberg (40/60, 60/40, 50/50) — no hay grilla propia ni regiones
 * fijas. Copy CLARAMENTE ficticio ("Faro de Cabo Verne", inventado) y
 * media placeholder — no reutiliza ni pisa contenido real de ningún caso.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:estavillo/case-section {"anchor":"demo-content","label":"Demo 01 — Content","heading":"Un capítulo Content: ancho completo, flexible.","lead":"Todo este pattern es una demo con contenido ficticio (el \"Faro de Cabo Verne\" no existe). Content es el ancho por defecto — label, heading y lead ya ocupan el capítulo entero, y este párrafo también: nada lo achica contra el borde izquierdo."} -->
<!-- wp:paragraph -->
<p>Este párrafo es un bloque normal de Gutenberg, escrito directo adentro del capítulo — sin Columns, sin ningún bloque contenedor. Antes, un bug de CSS heredado lo angostaba a ~820px sin importar el ancho del capítulo, dejando un hueco vacío a la derecha; ahora usa el ancho completo del container editorial de 1320px, igual que el heading de arriba.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-40-60","label":"Demo 02 — Columns 40/60","heading":"Texto a la izquierda, evidencia a la derecha."} -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:paragraph -->
<p>El equipo ficticio del faro necesitaba registrar cada encendido manual en un cuaderno que solo entendía la farera de turno — un criterio operativo que vivía en una sola cabeza. Esta columna usa el 40% del ancho; movela, cambiá la proporción o reordená los bloques con los controles normales de Gutenberg.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:estavillo/case-figure {"placeholderLabel":"panel de encendidos (demo)","tag":"{asset: demo-40-60}","caption":"Figura placeholder al 60% — en un caso real acá va la pantalla o el diagrama que sostiene el punto."} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-60-40","label":"Demo 03 — Columns 60/40","heading":"Imagen a la izquierda, texto a la derecha."} -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:estavillo/case-figure {"variant":"browser","browserLabel":"faro-verne.demo","placeholderLabel":"panel en vivo (demo)","tag":"{asset: demo-60-40}"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:paragraph -->
<p>El espejo de la composición anterior: la evidencia primero (60%), la conclusión después (40%). Alternar los lados a lo largo del caso evita que la página se lea volcada hacia un solo costado.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-wide","label":"Demo 04 — Wide","heading":"Artefactos a ancho completo.","spacing":"spacious"} -->
<!-- wp:estavillo/case-figure {"variant":"wide","placeholderLabel":"mapa de mareas 16:7 (demo)","tag":"{asset: demo-wide}","caption":"Figura wide placeholder — este capítulo usa además el preset de spacing \"Spacious\" (144px) para respirar."} /-->

<!-- wp:estavillo/case-stats {"items":[{"num":"312","label":"encendidos (demo)"},{"num":"4","label":"fareras de turno (demo)"},{"num":"−38%","label":"tiempo de registro (demo)"},{"num":"1","label":"cuaderno jubilado (demo)"}]} /-->

<!-- wp:estavillo/case-ladder {"steps":[{"label":"Cuaderno en papel","state":"done"},{"label":"Planilla compartida","state":"done"},{"label":"Panel en vivo","state":"active"},{"label":"Encendido autónomo","state":"future"}]} /-->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">Números y escalera 100% inventados para la demo — Wide es para artefactos estructurados como estos; para prosa larga corresponde el ancho Reading.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-reading","label":"Demo 05 — Reading","heading":"Un capítulo de lectura: todo el contenido a medida.","layout":"reading"} -->
<!-- wp:paragraph -->
<p>Reading es la única variante donde el ancho del CAPÍTULO ENTERO —label, heading, lead y contenido— se limita a ~72 caracteres por línea. Es la opción correcta para narrativa larga que no necesita columnas ni figuras al lado: la farera jefa ficticia de este caso describe acá, en un párrafo largo, por qué el cuaderno en papel dejó de alcanzar cuando el equipo creció.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Ningún otro capítulo de esta demo angosta su prosa así — Content y Wide dejan el párrafo a ancho completo, a propósito.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-50-50","label":"Demo 06 — Columns 50/50","heading":"Comparación con el mismo peso."} -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:estavillo/case-figure {"placeholderLabel":"antes — cuaderno (demo)","tag":"{asset: demo-antes}","caption":"Antes (demo)."} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:estavillo/case-figure {"placeholderLabel":"después — panel (demo)","tag":"{asset: demo-despues}","caption":"Después (demo)."} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-close","label":"Demo 07 — Cierre","heading":"Cierre en Reading, con cita y detalle plegable.","layout":"reading","spacing":"compact"} -->
<!-- wp:estavillo/case-quote {"quote":"Ahora cualquiera del equipo puede encender el faro sin llamarme a las tres de la mañana.","cite":"Farera jefa ficticia, demo"} /-->

<!-- wp:paragraph -->
<p>El caso vuelve al reposo de lectura para cerrar. Este capítulo usa el preset "Compact" (96px) — la transición corta entre dos momentos de texto no necesita más aire que eso.</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-details {"summary":"Nota metodológica (demo)"} -->
<!-- wp:paragraph -->
<p>Todo lo que leíste es utilería para probar el sistema editorial: proyecto, números y citas son inventados. Borrá este pattern del post cuando termines de explorar.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-details -->
<!-- /wp:estavillo/case-section -->
CONTENT;
