<?php
/**
 * Render dinámico de estavillo/case-section.
 *
 * Markup idéntico al de la librería .es-case-* del theme (case-study.css):
 * <div class="es-case-section" id="{anchor}"> + label + heading + lead +
 * InnerBlocks. El anchor alimenta el Case Index del meta box
 * ("Label|#anchor" por línea) — mismo contrato que el HTML manual.
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
$es_reading = isset( $attributes['layout'] ) && 'reading' === $attributes['layout'];

$es_wrapper_args = array(
	'class' => 'es-case-section' . ( $es_reading ? ' es-case-section--reading' : '' ),
);
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
