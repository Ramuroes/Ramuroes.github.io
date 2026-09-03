<?php
/**
 * Render dinámico de estavillo/tools.
 *
 * Bloque de sistema (Design System), no exclusivo de About: la misma
 * instancia funciona en Home, About, Case Studies o una landing futura sin
 * ningún cambio de código — no hay copy ni numeración de sección
 * hardcodeados acá. El encabezado de sección ("06", "Tools"/"Herramientas",
 * etc.) lo aporta SIEMPRE quien use el bloque (ver about-content.php y los
 * archivos Gutenberg del About), nunca este render.php.
 *
 * Íconos: misma librería lineal curada que ya usa How I Work
 * (es_process_icon_library() / es_process_icon_svg(), definidas en el
 * child theme) — nunca logos de herramientas, sólo la figura de la
 * categoría. function_exists() como guarda: si el child theme no está
 * activo, el grupo simplemente sale sin ícono.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja, sin InnerBlocks).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_layout     = isset( $attributes['layout'] ) && 'inline' === $attributes['layout'] ? 'inline' : 'editorial';
$es_show_icons = ! empty( $attributes['showIcons'] );
$es_groups_raw = isset( $attributes['groups'] ) && is_array( $attributes['groups'] ) ? $attributes['groups'] : array();

// Normaliza + descarta filas vacías: un grupo sin título o sin ninguna
// herramienta real no se imprime — mismo criterio "vacío no rompe nada"
// que el resto de los bloques (case-stats, hobby-list).
$es_groups = array();
foreach ( $es_groups_raw as $es_group ) {
	if ( ! is_array( $es_group ) || '' === trim( (string) ( $es_group['title'] ?? '' ) ) ) {
		continue;
	}
	$es_items = array();
	if ( ! empty( $es_group['items'] ) && is_array( $es_group['items'] ) ) {
		foreach ( $es_group['items'] as $es_item ) {
			$es_item = trim( (string) $es_item );
			if ( '' !== $es_item ) {
				$es_items[] = $es_item;
			}
		}
	}
	if ( empty( $es_items ) ) {
		continue;
	}
	$es_groups[] = array(
		'title'               => (string) $es_group['title'],
		'icon'                => isset( $es_group['icon'] ) ? sanitize_key( $es_group['icon'] ) : '',
		'items'               => $es_items,
		// Preparado para una evolución futura del bloque (subtítulo corto
		// bajo el título de categoría) — no usado por ningún contenido
		// actual; se imprime sólo si algún día deja de estar vacío.
		'categoryDescription' => isset( $es_group['categoryDescription'] ) ? trim( (string) $es_group['categoryDescription'] ) : '',
	);
}

if ( empty( $es_groups ) ) {
	return;
}

$es_classes = 'es-tools es-tools--' . $es_layout;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $es_classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<div class="es-tools__grid">
		<?php foreach ( $es_groups as $es_group ) : ?>
			<div class="es-tools__group">
				<div class="es-tools__head">
					<?php if ( $es_show_icons && '' !== $es_group['icon'] && function_exists( 'es_process_icon_svg' ) ) : ?>
						<?php $es_icon_svg = es_process_icon_svg( $es_group['icon'] ); ?>
						<?php if ( '' !== $es_icon_svg ) : ?>
							<span class="es-tools__icon" aria-hidden="true">
								<?php
								$es_kses_rules = function_exists( 'es_icon_svg_kses_rules' )
									? es_icon_svg_kses_rules()
									: array(
										'svg'  => array(
											'xmlns'       => true,
											'width'       => true,
											'height'      => true,
											'viewbox'     => true,
											'fill'        => true,
											'stroke'      => true,
											'stroke-width' => true,
											'stroke-linecap' => true,
											'stroke-linejoin' => true,
											'aria-hidden' => true,
											'focusable'   => true,
										),
										'path' => array( 'd' => true ),
										'circle' => array(
											'cx' => true,
											'cy' => true,
											'r'  => true,
											'fill' => true,
										),
										'rect' => array(
											'x' => true,
											'y' => true,
											'width' => true,
											'height' => true,
											'rx' => true,
										),
									);
								echo wp_kses( $es_icon_svg, $es_kses_rules ); // phpcs:ignore WordPress.Security.EscapeOutput -- whitelisted SVG, wp_kses aplicado acá mismo.
								?>
							</span>
						<?php endif; ?>
					<?php endif; ?>
					<h3 class="es-tools__title"><?php echo esc_html( $es_group['title'] ); ?></h3>
				</div>
				<?php if ( '' !== $es_group['categoryDescription'] ) : ?>
					<p class="es-tools__description"><?php echo esc_html( $es_group['categoryDescription'] ); ?></p>
				<?php endif; ?>
				<ul class="es-tools__list">
					<?php foreach ( $es_group['items'] as $es_item ) : ?>
						<li><?php echo esc_html( $es_item ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>
	</div>
</div>
