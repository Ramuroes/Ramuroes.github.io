<?php
/**
 * Render dinámico de estavillo/case-status.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_columns = isset( $attributes['columns'] ) && is_array( $attributes['columns'] ) ? $attributes['columns'] : array();
$es_columns = array_filter(
	$es_columns,
	function ( $es_col ) {
		if ( ! is_array( $es_col ) ) {
			return false;
		}
		$es_has_items = ! empty( array_filter( array_map( 'trim', array_map( 'strval', (array) ( $es_col['items'] ?? array() ) ) ) ) );
		return '' !== trim( (string) ( $es_col['heading'] ?? '' ) ) || $es_has_items;
	}
);

if ( empty( $es_columns ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-status' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php
	foreach ( $es_columns as $es_col ) :
		$es_style = (string) ( $es_col['style'] ?? '' );
		$es_class = 'es-case-status__col';
		if ( 'done' === $es_style ) {
			$es_class .= ' es-case-status__col--done';
		} elseif ( 'attention' === $es_style ) {
			$es_class .= ' es-case-status__col--attention';
		}
		$es_bullets = array_filter( array_map( 'trim', array_map( 'strval', (array) ( $es_col['items'] ?? array() ) ) ) );
		?>
		<div class="<?php echo esc_attr( $es_class ); ?>">
			<?php if ( '' !== trim( (string) ( $es_col['heading'] ?? '' ) ) ) : ?>
				<div class="es-case-status__head"><?php echo wp_kses_post( (string) $es_col['heading'] ); ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $es_bullets ) ) : ?>
				<ul class="es-case-status__list">
					<?php foreach ( $es_bullets as $es_bullet ) : ?>
						<li><?php echo wp_kses_post( $es_bullet ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
