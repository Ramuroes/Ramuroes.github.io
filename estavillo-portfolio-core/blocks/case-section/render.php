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
 * - layout ("Width" en el inspector): content (default, ancho completo del
 *   container editorial de 1320px, sin tope de medida) | reading (el
 *   CAPÍTULO ENTERO se limita a ~72ch, pensado para prosa larga
 *   autocontenida) | wide (idéntico a content — mismo ancho completo — la
 *   distinción es de intención/documentación: Wide es para artefactos,
 *   Content es el default mixto).
 * - spacing: espacio total entre capítulos (compact/standard/spacious).
 * - divider: si se ve la línea divisoria de arriba (aparte de spacing).
 *
 * Corrección (sprint de corrección arquitectónica, sobre WordPress real):
 * la versión anterior envolvía reading/split en un grid de 12 columnas y
 * — el bug real — un selector heredado de la librería de prosa
 * (`.es-case-section > p`) le ponía max-width:820px a CUALQUIER párrafo
 * hijo directo de la sección sin importar el layout, angostando el
 * contenido "Content" contra el borde izquierdo y dejando un hueco vacío
 * a la derecha. Esta versión no envuelve nada: layouts viejos
 * (split-left/split-right/split-balanced, ya no soportados como opción)
 * caen a "content" — siguen renderizando, ya sin la grilla rígida — y el
 * cap de medida ahora es opt-in real vía la clase --reading, con un
 * override explícito para --content/--wide en case-study.css.
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
if ( ! in_array( $es_layout, array( 'content', 'reading', 'wide' ), true ) ) {
	// Valor legacy (split-left / split-right / split-balanced de la
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
