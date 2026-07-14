<?php
/**
 * Render dinámico de estavillo/case-stats.
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
		return is_array( $es_item ) && ( '' !== trim( (string) ( $es_item['num'] ?? '' ) ) || '' !== trim( (string) ( $es_item['label'] ?? '' ) ) );
	}
);

if ( empty( $es_items ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-stats' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php foreach ( $es_items as $es_item ) : ?>
		<div class="es-case-stat">
			<div class="es-case-stat__num"><?php echo wp_kses_post( (string) ( $es_item['num'] ?? '' ) ); ?></div>
			<div class="es-case-stat__label"><?php echo wp_kses_post( (string) ( $es_item['label'] ?? '' ) ); ?></div>
		</div>
	<?php endforeach; ?>
</div>
