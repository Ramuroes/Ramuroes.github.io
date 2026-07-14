<?php
/**
 * Render dinámico de estavillo/case-taxonomy.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_root  = isset( $attributes['root'] ) ? trim( (string) $attributes['root'] ) : '';
$es_items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$es_items = array_filter(
	$es_items,
	function ( $es_item ) {
		return is_array( $es_item ) && '' !== trim( (string) ( $es_item['title'] ?? '' ) );
	}
);
$es_mods_label = isset( $attributes['modsLabel'] ) ? trim( (string) $attributes['modsLabel'] ) : '';
$es_mods       = isset( $attributes['mods'] ) && is_array( $attributes['mods'] ) ? array_filter( array_map( 'trim', array_map( 'strval', $attributes['mods'] ) ) ) : array();

if ( '' === $es_root && empty( $es_items ) && empty( $es_mods ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-taxonomy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php if ( '' !== $es_root ) : ?>
		<div class="es-case-taxonomy__root"><?php echo wp_kses_post( $es_root ); ?></div>
	<?php endif; ?>
	<?php if ( ! empty( $es_items ) ) : ?>
		<div class="es-case-taxonomy__grid">
			<?php foreach ( $es_items as $es_item ) : ?>
				<div class="es-case-taxonomy__item">
					<div class="es-case-taxonomy__item-title"><?php echo wp_kses_post( (string) ( $es_item['title'] ?? '' ) ); ?></div>
					<?php if ( '' !== trim( (string) ( $es_item['meta'] ?? '' ) ) ) : ?>
						<div class="es-case-taxonomy__item-meta"><?php echo wp_kses_post( (string) $es_item['meta'] ); ?></div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if ( ! empty( $es_mods ) ) : ?>
		<div class="es-case-taxonomy__mods">
			<?php if ( '' !== $es_mods_label ) : ?>
				<div class="es-case-taxonomy__mods-label"><?php echo wp_kses_post( $es_mods_label ); ?></div>
			<?php endif; ?>
			<div class="es-case-taxonomy__mods-tags">
				<?php foreach ( $es_mods as $es_mod ) : ?>
					<span class="es-case-taxonomy__tag"><?php echo wp_kses_post( $es_mod ); ?></span>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
