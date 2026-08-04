<?php
/**
 * Render dinámico de estavillo/case-section.
 *
 * Contenedor de capítulo FLEXIBLE: label/heading/lead (RichText dedicados,
 * siempre a ancho completo) + InnerBlocks SIN RESTRICCIONES en flujo
 * plano — nada de regiones fijas ni grilla de 12 columnas. La composición
 * interna (texto+figura lado a lado, comparaciones, etc.) la arma el editor
 * con los bloques Columns/Column NATIVOS de Gutenberg; este bloque solo
 * decide tres cosas a nivel capítulo:
 *
 * - layout ("Width" en el inspector — solo ofrece Content/Reading; ver
 *   nota de compatibilidad más abajo): content (default, ancho completo
 *   del padre inmediato — el body del caso O una columna/Group/Row/Stack
 *   nativo si está anidado, sin tope de medida) | reading (el CAPÍTULO
 *   ENTERO se limita a ~72ch, nunca más ancho que el padre, pensado para
 *   prosa larga autocontenida).
 * - spacing: espacio total entre capítulos (compact/standard/spacious).
 * - divider: si se ve la línea divisoria de arriba (aparte de spacing).
 *
 * Compatibilidad "wide": el valor legacy "wide" (ancho completo, mismo
 * resultado visual que "content", distinción solo de documentación) ya
 * NO se ofrece en el inspector para selecciones nuevas — se NORMALIZA acá
 * a "content" antes de armar la clase, así que ningún bloque emite
 * `es-case-section--wide` nunca más. Los bloques YA GUARDADOS con
 * "layout":"wide" siguen aceptándose tal cual (el atributo no se
 * reescribe ni se invalida) y renderizan IDÉNTICO a Content.
 *
 * Corrección de ancho (sprint de corrección arquitectónica, sobre
 * WordPress real): un selector heredado de la librería de prosa
 * (`.es-case-section > p`) le ponía max-width:820px a CUALQUIER párrafo
 * hijo directo de la sección sin importar el layout, angostando el
 * contenido "Content" contra el borde izquierdo y dejando un hueco vacío
 * a la derecha — corregido con un override explícito en case-study.css.
 *
 * Corrección de anidamiento (sprint de compatibilidad, sobre WordPress
 * real): Case Section ahora puede vivir directo en el body del caso O
 * anidado adentro de un Column/Group/Row/Stack nativo de Gutenberg (p.
 * ej. Columns 40/60 con Case Section en una columna). "Ancho completo"
 * siempre es relativo al padre inmediato — la única responsabilidad de
 * Case Section es su presentación INTERNA (label/heading/lead,
 * InnerBlocks, medida de lectura, spacing, divisor); la composición
 * externa (proporciones de columnas, mobile stacking) es 100% de los
 * bloques Columns/Column nativos, nunca de este bloque.
 *
 * El anchor alimenta el Case Index del meta box ("Label|#anchor" por
 * línea) — mismo contrato que el HTML manual.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    InnerBlocks ya renderizados.
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_anchor  = ! empty( $attributes['anchor'] ) ? sanitize_title( $attributes['anchor'] ) : '';
$es_label   = isset( $attributes['label'] ) ? trim( (string) $attributes['label'] ) : '';
$es_heading = isset( $attributes['heading'] ) ? trim( (string) $attributes['heading'] ) : '';
$es_lead    = isset( $attributes['lead'] ) ? trim( (string) $attributes['lead'] ) : '';

$es_layout = isset( $attributes['layout'] ) ? (string) $attributes['layout'] : 'content';
if ( 'wide' === $es_layout ) {
	// "Wide" consolidado en "Content": mismo resultado visual, ya no se
	// ofrece como opción nueva. El atributo guardado NO se reescribe —
	// solo se normaliza acá, en cada render, para la clase de salida.
	$es_layout = 'content';
}
if ( ! in_array( $es_layout, array( 'content', 'reading' ), true ) ) {
	// Valor legacy (split-left / split-right / split-balanced de una
	// corrección anterior) u otro desconocido: cae a "content" — sigue
	// mostrando todo el contenido, ya sin la grilla que causaba el bug.
	$es_layout = 'content';
}
$es_spacing = isset( $attributes['spacing'] ) ? (string) $attributes['spacing'] : 'standard';
if ( ! in_array( $es_spacing, array( 'compact', 'standard', 'spacious' ), true ) ) {
	$es_spacing = 'standard';
}
$es_divider = ! isset( $attributes['divider'] ) || (bool) $attributes['divider'];

$es_classes = 'es-case-section es-case-section--' . $es_layout . ' es-case-section--sp-' . $es_spacing;
if ( ! $es_divider ) {
	$es_classes .= ' es-case-section--no-divider';
}

$es_wrapper_args = array( 'class' => $es_classes );
if ( '' !== $es_anchor ) {
	$es_wrapper_args['id'] = $es_anchor;
}
?>
<div <?php echo get_block_wrapper_attributes( $es_wrapper_args ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php if ( '' !== $es_label ) : ?>
		<div class="es-case-label"><?php echo wp_kses_post( $es_label ); ?></div>
	<?php endif; ?>
	<?php if ( '' !== $es_heading ) : ?>
		<h2 class="es-case-heading"><?php echo wp_kses_post( $es_heading ); ?></h2>
	<?php endif; ?>
	<?php if ( '' !== $es_lead ) : ?>
		<p class="es-case-lead"><?php echo wp_kses_post( $es_lead ); ?></p>
	<?php endif; ?>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
</div>
