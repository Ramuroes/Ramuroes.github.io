<?php
/**
 * Render dinámico de estavillo/case-findings-list.
 *
 * Reemplaza la tabla nativa de dos columnas (herramienta / hallazgo) que
 * se usaba para la evidencia de investigación: una tabla angosta de dos
 * columnas no tiene una historia responsive real (columnas ínfimas,
 * "Synthetic Users" partido en dos líneas, mobile con scroll horizontal o
 * texto microscópico). Esto es una lista semántica (<ul>/<li>), no una
 * tabla: en desktop cada fila es una grilla de tres columnas (ícono /
 * herramienta / hallazgo); en mobile la misma fila se apila (ícono +
 * nombre arriba, hallazgo debajo) — ver case-study.css ".es-findings".
 *
 * Los íconos son un set fijo, pequeño y decorativo (el título ya
 * identifica la herramienta): la clave elegida en el editor resuelve acá
 * a un <svg> embebido, igual que el conector de estavillo/case-flow — no
 * hay SVG de usuario que sanear, así que no hace falta wp_kses en el
 * propio marcado del ícono.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'es_findings_icon_svg' ) ) {
	/**
	 * Set fijo de íconos de método/herramienta de investigación, 32x32,
	 * un solo path evenodd con currentColor — misma convención que los
	 * íconos de línea del theme (assets/icons/*.svg).
	 *
	 * @param string $key Clave del ícono.
	 * @return string Markup <svg> o cadena vacía si la clave no matchea.
	 */
	function es_findings_icon_svg( $key ) {
		$paths = array(
			// Heurísticas — checklist (tres líneas de largo desigual).
			'heuristics'      => 'M6,9 H26 V11.4 H6 Z M6,15.3 H22 V17.7 H6 Z M6,21.6 H26 V24 H6 Z',
			// Clarity — cursor (mapas de calor / clicks).
			'clarity'         => 'M7,5 L7,27 L12.5,21.8 L16.3,29 L20,27.1 L16.2,20 L24,19.6 Z',
			// Analytics — barras ascendentes.
			'analytics'       => 'M6,20 H11 V26 H6 Z M13.5,14 H18.5 V26 H13.5 Z M21,8 H26 V26 H21 Z',
			// Synthetic Users — persona (cabeza + hombros).
			'synthetic-users' => 'M11,10 A5,5 0 1,0 21,10 A5,5 0 1,0 11,10 Z M6,27 C6,20 10,17 16,17 C22,17 26,20 26,27 Z',
			// Benchmarking — balanza.
			'benchmarking'    => 'M15,6 H17 V24 H15 Z M6,10 H26 V12 H6 Z M9,26 H23 V28.4 H9 Z M4.8,16 A3.2,3.2 0 1,0 11.2,16 A3.2,3.2 0 1,0 4.8,16 Z M20.8,16 A3.2,3.2 0 1,0 27.2,16 A3.2,3.2 0 1,0 20.8,16 Z',
			// Comparativa IA vs. manual — dos anillos superpuestos (Venn).
			'compare'         => 'M2.5,16 A7.5,7.5 0 1,0 17.5,16 A7.5,7.5 0 1,0 2.5,16 Z M4.2,16 A5.8,5.8 0 1,0 15.8,16 A5.8,5.8 0 1,0 4.2,16 Z M14.5,16 A7.5,7.5 0 1,0 29.5,16 A7.5,7.5 0 1,0 14.5,16 Z M16.2,16 A5.8,5.8 0 1,0 27.8,16 A5.8,5.8 0 1,0 16.2,16 Z',
		);

		$key = sanitize_key( (string) $key );
		if ( ! isset( $paths[ $key ] ) ) {
			return '';
		}

		return '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" d="' . $paths[ $key ] . '" fill="currentColor"/></svg>';
	}
}

$es_title    = trim( (string) ( $attributes['title'] ?? '' ) );
$es_subtitle = trim( (string) ( $attributes['subtitle'] ?? '' ) );
$es_items    = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();

$es_items = array_values(
	array_filter(
		$es_items,
		function ( $es_item ) {
			if ( ! is_array( $es_item ) || false === ( $es_item['visible'] ?? true ) ) {
				return false;
			}
			return '' !== trim( (string) ( $es_item['title'] ?? '' ) ) || '' !== trim( (string) ( $es_item['finding'] ?? '' ) );
		}
	)
);

if ( empty( $es_items ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-findings' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php if ( '' !== $es_title ) : ?>
		<h3 class="es-findings__title"><?php echo wp_kses_post( $es_title ); ?></h3>
	<?php endif; ?>
	<?php if ( '' !== $es_subtitle ) : ?>
		<p class="es-findings__subtitle"><?php echo wp_kses_post( $es_subtitle ); ?></p>
	<?php endif; ?>
	<ul class="es-findings__list">
		<?php foreach ( $es_items as $es_item ) : ?>
			<?php
			$es_icon_key = trim( (string) ( $es_item['icon'] ?? '' ) );
			$es_icon_svg = '' !== $es_icon_key ? es_findings_icon_svg( $es_icon_key ) : '';
			$es_row_class = 'es-findings__row' . ( '' === $es_icon_svg ? ' es-findings__row--no-icon' : '' );
			?>
			<li class="<?php echo esc_attr( $es_row_class ); ?>" tabindex="0">
				<?php if ( '' !== $es_icon_svg ) : ?>
					<span class="es-findings__icon" aria-hidden="true">
						<?php echo $es_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG fijo del propio plugin (es_findings_icon_svg), sin dato de usuario. ?>
					</span>
				<?php endif; ?>
				<span class="es-findings__tool"><?php echo wp_kses_post( trim( (string) ( $es_item['title'] ?? '' ) ) ); ?></span>
				<p class="es-findings__finding"><?php echo wp_kses_post( trim( (string) ( $es_item['finding'] ?? '' ) ) ); ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
