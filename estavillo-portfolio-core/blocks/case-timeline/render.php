<?php
/**
 * Render dinámico de estavillo/case-timeline.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$es_items = array_filter(
	$es_items,
	function ( $es_item ) {
		return is_array( $es_item ) && ( '' !== trim( (string) ( $es_item['title'] ?? '' ) ) || '' !== trim( (string) ( $es_item['text'] ?? '' ) ) );
	}
);

if ( empty( $es_items ) ) {
	return;
}
?>
<ol <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-timeline' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php foreach ( $es_items as $es_item ) : ?>
		<li class="es-case-timeline__item">
			<?php if ( '' !== trim( (string) ( $es_item['title'] ?? '' ) ) ) : ?>
				<div class="es-case-timeline__title"><?php echo wp_kses_post( (string) $es_item['title'] ); ?></div>
			<?php endif; ?>
			<?php if ( '' !== trim( (string) ( $es_item['text'] ?? '' ) ) ) : ?>
				<div class="es-case-timeline__text"><?php echo wp_kses_post( (string) $es_item['text'] ); ?></div>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>
