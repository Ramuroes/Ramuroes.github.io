<?php
/**
 * Render dinámico de estavillo/hobby-list.
 *
 * Reusa 1:1 la resolución de íconos del theme (es_hobby_icon_resolve_key()/
 * es_hobby_icon_svg(), la misma librería file-based que Home Content y el
 * fallback PHP legacy en template-parts/about-content.php) — el bloque
 * nunca guarda ni duplica markup SVG, solo la clave elegida en el <select>
 * del editor. Si el theme no expone esas funciones (child theme inactivo),
 * el ítem simplemente cae al estado --empty ya soportado por el CSS.
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
$es_items = array_values(
	array_filter(
		$es_items,
		function ( $es_item ) {
			return is_array( $es_item ) && '' !== trim( (string) ( $es_item['label'] ?? '' ) );
		}
	)
);

if ( empty( $es_items ) ) {
	return;
}
?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'es-hobbies__list' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php
	foreach ( $es_items as $es_item ) :
		$es_icon_key = ! empty( $es_item['icon'] ) ? (string) $es_item['icon'] : '';
		if ( $es_icon_key && function_exists( 'es_hobby_icon_resolve_key' ) ) {
			$es_icon_key = es_hobby_icon_resolve_key( $es_icon_key );
		}
		$es_icon_svg   = $es_icon_key && function_exists( 'es_hobby_icon_svg' ) ? es_hobby_icon_svg( $es_icon_key ) : '';
		$es_icon_class = 'es-hobby-item__icon es-hobby-icon';
		if ( '' === $es_icon_svg ) {
			$es_icon_class .= ' es-hobby-item__icon--empty';
		} else {
			$es_icon_class .= ' es-hobby-icon--' . $es_icon_key;
		}
		?>
		<li class="es-hobby-item" tabindex="0" data-icon="<?php echo esc_attr( $es_icon_key ? $es_icon_key : 'none' ); ?>">
			<span class="<?php echo esc_attr( $es_icon_class ); ?>" aria-hidden="true">
				<?php
				if ( '' !== $es_icon_svg ) {
					echo function_exists( 'es_icon_svg_kses_rules' ) ? wp_kses( $es_icon_svg, es_icon_svg_kses_rules() ) : wp_kses( $es_icon_svg, array( 'svg' => array( 'xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'aria-hidden' => true, 'focusable' => true ), 'path' => array( 'd' => true, 'fill' => true, 'fill-rule' => true, 'clip-rule' => true ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG whitelisted vía es_icon_svg_kses_rules()/fallback kses explícito, librería curada del theme.
				}
				?>
			</span>
			<span class="es-hobby-item__label"><?php echo esc_html( (string) $es_item['label'] ); ?></span>
		</li>
	<?php endforeach; ?>
</ul>
