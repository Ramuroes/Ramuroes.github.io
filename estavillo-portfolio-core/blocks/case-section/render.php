<?php
/**
 * Render dinámico de estavillo/case-section.
 *
 * Markup de la librería .es-case-* del theme (case-study.css) + el sistema
 * editorial v2 (spec "Case Study Grid System v1"): el editor elige presets
 * BLOQUEADOS con nombre humano — layout (reading / splits / wide), spacing
 * de capítulo (compact/standard/spacious) y orden mobile de los splits —
 * y este render los traduce a clases; nunca se exponen columnas ni px.
 *
 * Estructura por layout (idéntica al preview del editor):
 * - wide:    label/heading/lead + InnerBlocks en flujo plano (el markup de
 *            siempre — compat total con el contenido existente).
 * - reading: TODO (header incluido) dentro de .es-case-section__grid, para
 *            que el header alinee con la banda de lectura (cols 3–10).
 * - split-*: header en flujo plano (ancho completo) + las dos regiones
 *            (case-split-content / case-split-media) dentro de la grilla.
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

$es_layout = isset( $attributes['layout'] ) ? (string) $attributes['layout'] : 'wide';
if ( ! in_array( $es_layout, array( 'wide', 'reading', 'split-left', 'split-right', 'split-balanced' ), true ) ) {
	$es_layout = 'wide';
}
$es_spacing = isset( $attributes['spacing'] ) ? (string) $attributes['spacing'] : 'standard';
if ( ! in_array( $es_spacing, array( 'compact', 'standard', 'spacious' ), true ) ) {
	$es_spacing = 'standard';
}
$es_mobile   = isset( $attributes['mobileOrder'] ) ? (string) $attributes['mobileOrder'] : '';
$es_is_split = 0 === strpos( $es_layout, 'split-' );

$es_classes = 'es-case-section es-case-section--sp-' . $es_spacing;
if ( 'reading' === $es_layout ) {
	$es_classes .= ' es-case-section--reading';
} elseif ( $es_is_split ) {
	$es_classes .= ' es-case-section--split es-case-section--' . $es_layout;
	if ( in_array( $es_mobile, array( 'content-first', 'media-first' ), true ) ) {
		$es_classes .= ' es-case-section--m-' . $es_mobile;
	}
}

$es_wrapper_args = array( 'class' => $es_classes );
if ( '' !== $es_anchor ) {
	$es_wrapper_args['id'] = $es_anchor;
}

$es_header_html = '';
if ( '' !== $es_label ) {
	$es_header_html .= '<div class="es-case-label">' . wp_kses_post( $es_label ) . '</div>';
}
if ( '' !== $es_heading ) {
	$es_header_html .= '<h2 class="es-case-heading">' . wp_kses_post( $es_heading ) . '</h2>';
}
if ( '' !== $es_lead ) {
	$es_header_html .= '<p class="es-case-lead">' . wp_kses_post( $es_lead ) . '</p>';
}
?>
<div <?php echo get_block_wrapper_attributes( $es_wrapper_args ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php if ( 'reading' === $es_layout ) : ?>
		<div class="es-case-section__grid">
			<?php echo $es_header_html; // phpcs:ignore WordPress.Security.EscapeOutput -- construido arriba con wp_kses_post(). ?>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
		</div>
	<?php elseif ( $es_is_split ) : ?>
		<?php echo $es_header_html; // phpcs:ignore WordPress.Security.EscapeOutput -- construido arriba con wp_kses_post(). ?>
		<div class="es-case-section__grid">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
		</div>
	<?php else : ?>
		<?php echo $es_header_html; // phpcs:ignore WordPress.Security.EscapeOutput -- construido arriba con wp_kses_post(). ?>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
	<?php endif; ?>
</div>
