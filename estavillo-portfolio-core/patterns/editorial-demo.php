<?php
/**
 * Pattern content — Case Study — Editorial System Demo.
 *
 * Página de laboratorio del sistema editorial v2: demuestra los cinco
 * layouts del case-section (reading / split-left / wide / split-right /
 * split-balanced) y los presets de spacing, en el orden canónico de la
 * spec "Grid System v1". Copy CLARAMENTE ficticio ("Faro de Cabo Verne",
 * un proyecto inventado) y media placeholder — no reutiliza ni pisa
 * contenido real de ningún caso.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:estavillo/case-section {"anchor":"demo-reading","label":"Demo 01 — Reading","heading":"Un capítulo de lectura: la variante por defecto para narrativa larga.","lead":"Todo este pattern es una demo con contenido ficticio (el \"Faro de Cabo Verne\" no existe). El layout Reading ubica el texto en la banda de lectura de la grilla — columnas 3 a 10 — con un tope duro de 72 caracteres por línea: nunca de borde a borde del container de 1320px.","layout":"reading"} -->
<!-- wp:paragraph -->
<p>Este es el estado de reposo de un caso: párrafos a medida de lectura, sin competencia visual. El equipo ficticio del faro necesitaba registrar cada encendido manual en un cuaderno que solo entendía la farera de turno — un criterio operativo que vivía en una sola cabeza, imposible de discutir o versionar.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Cuando un capítulo necesita evidencia, sale de Reading hacia un Split o un Wide — y vuelve. Esa alternancia es el ritmo editorial completo: ningún otro control de composición existe, a propósito.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-split-left","label":"Demo 02 — Split 5/7","heading":"Texto a la izquierda, evidencia a la derecha.","layout":"split-left"} -->
<!-- wp:estavillo/case-split-content -->
<!-- wp:paragraph -->
<p>El punto va antes que su evidencia: una idea corta — cuatro a seis líneas — en la columna de texto (5/12), y la figura al lado (7/12). La proporción está bloqueada: el editor elige la variante, nunca las columnas.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-split-content -->

<!-- wp:estavillo/case-split-media -->
<!-- wp:estavillo/case-figure {"placeholderLabel":"panel de encendidos (demo)","tag":"{asset: demo-split}","caption":"Figura placeholder — en un caso real acá va la pantalla o el diagrama que sostiene el punto."} /-->
<!-- /wp:estavillo/case-split-media -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-wide-figure","label":"Demo 03 — Wide","heading":"Un artefacto que gana el ancho completo.","lead":"Wide usa las 12 columnas del container de 1320px. Está reservado para artefactos — capturas, diagramas, grillas estructuradas — nunca para texto solo.","spacing":"spacious"} -->
<!-- wp:estavillo/case-figure {"variant":"wide","placeholderLabel":"mapa de mareas 16:7 (demo)","tag":"{asset: demo-wide}","caption":"Figura wide placeholder — este capítulo usa además el preset de spacing \"Spacious\" (144px) para respirar."} /-->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-wide-data","label":"Demo 04 — Wide · Stats + Ladder","heading":"Datos estructurados a ancho completo."} -->
<!-- wp:estavillo/case-stats {"items":[{"num":"312","label":"encendidos (demo)"},{"num":"4","label":"fareras de turno (demo)"},{"num":"−38%","label":"tiempo de registro (demo)"},{"num":"1","label":"cuaderno jubilado (demo)"}]} /-->

<!-- wp:estavillo/case-ladder {"steps":[{"label":"Cuaderno en papel","state":"done"},{"label":"Planilla compartida","state":"done"},{"label":"Panel en vivo","state":"active"},{"label":"Encendido autónomo","state":"future"}]} /-->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">Números y escalera 100% inventados para la demo — los componentes Stats y Ladder son artefactos estructurados: por eso pueden vivir en un capítulo Wide.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-split-right","label":"Demo 05 — Split 7/5","heading":"Imagen a la izquierda, texto a la derecha.","layout":"split-right","mobileOrder":"content-first"} -->
<!-- wp:estavillo/case-split-content -->
<!-- wp:paragraph -->
<p>El espejo del Split anterior: la evidencia primero (7/12), la conclusión después (5/12). Alternar los lados a lo largo del caso evita que la página se lea volcada hacia un solo costado.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Este capítulo además demuestra el preset de orden mobile: al apilarse, el contenido va primero ("Contenido primero"), pisando el orden visual de desktop.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-split-content -->

<!-- wp:estavillo/case-split-media -->
<!-- wp:estavillo/case-figure {"variant":"browser","browserLabel":"faro-verne.demo","placeholderLabel":"panel en vivo (demo)","tag":"{asset: demo-split-right}"} /-->
<!-- /wp:estavillo/case-split-media -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"demo-balanced","label":"Demo 06 — Split 6/6","heading":"Balanceado: antes y después, con el mismo peso.","layout":"split-balanced"} -->
<!-- wp:estavillo/case-split-content -->
<!-- wp:estavillo/case-figure {"placeholderLabel":"antes — cuaderno (demo)","tag":"{asset: demo-antes}","caption":"Antes (demo)."} /-->
<!-- /wp:estavillo/case-split-content -->

<!-- wp:estavillo/case-split-media -->
<!-- wp:estavillo/case-figure {"placeholderLabel":"después — panel (demo)","tag":"{asset: demo-despues}","caption":"Después (demo)."} /-->
<!-- /wp:estavillo/case-split-media -->
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
