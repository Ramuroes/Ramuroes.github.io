<?php
/**
 * Render dinámico de estavillo/case-stats.
 *
 * Íconos: misma librería lineal curada del child theme
 * (es_process_icon_library() / es_process_icon_svg()) que ya usan How I Work
 * y el bloque Tools — nunca HTML libre, sólo la CLAVE elegida en el <select>
 * del editor. function_exists() como guarda: si el child theme no está
 * activo, el stat sale sin ícono y nada más.
 *
 * Animación de conteo: el número FINAL siempre se imprime server-side, tal
 * cual, esté la animación activada o no. El JS (assets/js/case-stats.js del
 * theme) es mejora progresiva pura — sin él, o con prefers-reduced-motion,
 * lo que se ve es exactamente este HTML. Con `animate` activo el número se
 * duplica en un par visual/lector: el <span> visible es el que cuenta (y
 * queda aria-hidden mientras tanto), y un .es-visually-hidden gemelo
 * mantiene el valor final estable para lectores de pantalla, que si no
 * podrían anunciar un valor intermedio del conteo.
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

$es_animate = ! empty( $attributes['animate'] );
$es_classes = 'es-case-stats' . ( $es_animate ? ' es-case-stats--animate' : '' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $es_classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?><?php echo $es_animate ? ' data-es-stats-animate=""' : ''; ?>>
	<?php foreach ( $es_items as $es_item ) : ?>
		<?php
		$es_icon_key = isset( $es_item['icon'] ) ? sanitize_key( (string) $es_item['icon'] ) : '';
		$es_icon_svg = ( '' !== $es_icon_key && function_exists( 'es_process_icon_svg' ) ) ? es_process_icon_svg( $es_icon_key ) : '';
		$es_num      = (string) ( $es_item['num'] ?? '' );
		?>
		<div class="es-case-stat">
			<?php if ( '' !== $es_icon_svg ) : ?>
				<span class="es-case-stat__icon" aria-hidden="true">
					<?php
					$es_kses_rules = function_exists( 'es_icon_svg_kses_rules' )
						? es_icon_svg_kses_rules()
						: array(
							'svg'    => array(
								'xmlns'           => true,
								'width'           => true,
								'height'          => true,
								'viewbox'         => true,
								'fill'            => true,
								'stroke'          => true,
								'stroke-width'    => true,
								'stroke-linecap'  => true,
								'stroke-linejoin' => true,
								'aria-hidden'     => true,
								'focusable'       => true,
							),
							'path'   => array( 'd' => true ),
							'circle' => array(
								'cx'   => true,
								'cy'   => true,
								'r'    => true,
								'fill' => true,
							),
							'rect'   => array(
								'x'      => true,
								'y'      => true,
								'width'  => true,
								'height' => true,
								'rx'     => true,
							),
						);
					echo wp_kses( $es_icon_svg, $es_kses_rules ); // phpcs:ignore WordPress.Security.EscapeOutput -- whitelisted SVG, wp_kses aplicado acá mismo.
					?>
				</span>
			<?php endif; ?>
			<div class="es-case-stat__num"><?php
			if ( $es_animate ) {
				// Sin espacios entre los dos <span>: el que cuenta lee su
				// propio textContent para parsear el valor, y un salto de
				// línea acá se lo metería adentro.
				echo '<span class="es-case-stat__num-value" data-es-count aria-hidden="true">' . wp_kses_post( $es_num ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_kses_post aplicado al único dato variable.
				echo '<span class="es-visually-hidden">' . wp_kses_post( $es_num ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- idem.
			} else {
				echo wp_kses_post( $es_num );
			}
			?></div>
			<div class="es-case-stat__label"><?php echo wp_kses_post( (string) ( $es_item['label'] ?? '' ) ); ?></div>
		</div>
	<?php endforeach; ?>
</div>
