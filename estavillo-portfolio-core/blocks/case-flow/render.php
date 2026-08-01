<?php
/**
 * Render dinámico de estavillo/case-flow.
 *
 * Un flujo de proceso editorial, generado 100% desde el atributo `nodes`
 * — nunca desde SVG escrito a mano. Cada nodo declara su `kind` (start /
 * step / decision / milestone / end), su acento y sus filas de detalle;
 * el conector, la forma (rombo para decisión), el orden y las ramas salen
 * del dato. Cambiar el orden o sumar un nodo en el editor no obliga a
 * tocar ningún path SVG.
 *
 * PROGRESSIVE ENHANCEMENT (requisito del ticket): el panel de detalle se
 * emite SIEMPRE en el HTML, visible por CSS por defecto. El JS del theme
 * (assets/js/case-flow.js) marca el contenedor con .is-enhanced y recién
 * ahí los paneles pasan a ser popovers/acordeones. Sin JS el flujo se lee
 * completo, en orden, con todo el contenido — nada esencial depende del
 * script. Por eso el <button> del trigger se emite sin `hidden` y el
 * panel no arranca con display:none inline.
 *
 * Semántica: <ol> — un flujo ES una secuencia ordenada. Las ramas de una
 * decisión van en una <ul> anidada dentro del propio <li> del nodo.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Acentos permitidos. Mapean 1:1 a tokens que YA existen en el theme
 * (tokens.css): 'signal' → --es-signal (verde, el sistema/camino
 * resuelto), 'decision' → --es-decision (naranja, el punto de juicio
 * humano), 'muted' → tinta apagada. No hay colores nuevos.
 */
if ( ! function_exists( 'es_flow_accent_class' ) ) {
	function es_flow_accent_class( $accent ) {
		$accent = sanitize_key( (string) $accent );
		return in_array( $accent, array( 'signal', 'decision', 'muted' ), true ) ? ' is-accent-' . $accent : '';
	}
}

/**
 * Tipos de nodo permitidos. 'step' es el default seguro para cualquier
 * valor desconocido (un dato viejo nunca rompe el render).
 */
if ( ! function_exists( 'es_flow_node_kind' ) ) {
	function es_flow_node_kind( $kind ) {
		$kind = sanitize_key( (string) $kind );
		return in_array( $kind, array( 'start', 'step', 'decision', 'milestone', 'end' ), true ) ? $kind : 'step';
	}
}

/**
 * Conector inline entre dos nodos. Emite UN svg con dos paths — uno
 * horizontal y uno vertical — y el CSS muestra el que corresponde al
 * layout activo (desktop horizontal / mobile vertical). Así el conector
 * es siempre vectorial y nítido en los dos ejes sin duplicar markup por
 * viewport ni recalcular nada en JS.
 *
 * `vector-effect="non-scaling-stroke"` mantiene el grosor real en 1.25px
 * aunque el svg se estire con preserveAspectRatio="none".
 * Sin <marker>/<defs>: los IDs de marker colisionarían si hay más de un
 * flujo en la misma página — la punta de flecha es un polyline común.
 */
if ( ! function_exists( 'es_flow_connector_svg' ) ) {
	function es_flow_connector_svg() {
		return '<svg class="es-flow__connector" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true" focusable="false">'
			. '<path class="es-flow__line es-flow__line--h" d="M0 50 H92" vector-effect="non-scaling-stroke" />'
			. '<polyline class="es-flow__head es-flow__head--h" points="86,44 93,50 86,56" vector-effect="non-scaling-stroke" />'
			. '<path class="es-flow__line es-flow__line--v" d="M50 0 V92" vector-effect="non-scaling-stroke" />'
			. '<polyline class="es-flow__head es-flow__head--v" points="44,86 50,93 56,86" vector-effect="non-scaling-stroke" />'
			. '</svg>';
	}
}

$es_nodes = isset( $attributes['nodes'] ) && is_array( $attributes['nodes'] ) ? $attributes['nodes'] : array();
$es_nodes = array_values(
	array_filter(
		$es_nodes,
		function ( $es_node ) {
			return is_array( $es_node ) && '' !== trim( (string) ( $es_node['title'] ?? '' ) );
		}
	)
);

if ( empty( $es_nodes ) ) {
	return;
}

$es_start   = trim( (string) ( $attributes['startLabel'] ?? '' ) );
$es_end     = trim( (string) ( $attributes['endLabel'] ?? '' ) );
$es_detail  = trim( (string) ( $attributes['detailLabel'] ?? '' ) );
$es_close   = trim( (string) ( $attributes['closeLabel'] ?? '' ) );
$es_step_lb = trim( (string) ( $attributes['stepLabel'] ?? '' ) );
$es_ai_legend = trim( (string) ( $attributes['aiLegend'] ?? '' ) );
$es_density = sanitize_key( (string) ( $attributes['density'] ?? 'comfortable' ) );
$es_density = in_array( $es_density, array( 'comfortable', 'compact' ), true ) ? $es_density : 'comfortable';

// ID base único por instancia: permite varios flujos en una misma página
// sin colisión de aria-controls.
$es_uid   = wp_unique_id( 'es-flow-' );
$es_total = count( $es_nodes );

$es_classes = 'es-flow es-flow--' . $es_density;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $es_classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?> data-es-flow>

	<?php if ( '' !== $es_start ) : ?>
		<p class="es-flow__marker es-flow__marker--start"><span><?php echo esc_html( $es_start ); ?></span></p>
	<?php endif; ?>

	<?php // Indicador de progreso: sólo se muestra en mobile (CSS). Lo llena el JS; sin JS queda el total, que ya es información útil. ?>
	<p class="es-flow__progress" data-es-flow-progress aria-hidden="true">
		<span class="es-flow__progress-current" data-es-flow-current>01</span><span class="es-flow__progress-sep">/</span><span class="es-flow__progress-total"><?php echo esc_html( sprintf( '%02d', $es_total ) ); ?></span>
	</p>

	<ol class="es-flow__track">
		<?php foreach ( $es_nodes as $es_i => $es_node ) : ?>
			<?php
			$es_kind     = es_flow_node_kind( $es_node['kind'] ?? 'step' );
			$es_accent   = es_flow_accent_class( $es_node['accent'] ?? '' );
			$es_num      = trim( (string) ( $es_node['num'] ?? '' ) );
			$es_title    = trim( (string) ( $es_node['title'] ?? '' ) );
			$es_text     = trim( (string) ( $es_node['text'] ?? '' ) );
			$es_rows     = isset( $es_node['detail'] ) && is_array( $es_node['detail'] ) ? $es_node['detail'] : array();
			$es_branches = isset( $es_node['branches'] ) && is_array( $es_node['branches'] ) ? $es_node['branches'] : array();

			$es_rows = array_values(
				array_filter(
					$es_rows,
					function ( $es_row ) {
						return is_array( $es_row ) && '' !== trim( (string) ( $es_row['text'] ?? '' ) );
					}
				)
			);
			$es_branches = array_values(
				array_filter(
					$es_branches,
					function ( $es_branch ) {
						return is_array( $es_branch ) && '' !== trim( (string) ( $es_branch['label'] ?? '' ) );
					}
				)
			);

			$es_panel_id  = $es_uid . '-panel-' . $es_i;
			$es_has_panel = ! empty( $es_rows );
			?>
			<li class="es-flow__item es-flow__item--<?php echo esc_attr( $es_kind ); ?><?php echo esc_attr( $es_accent ); ?>" data-es-flow-item data-es-flow-index="<?php echo esc_attr( (string) ( $es_i + 1 ) ); ?>">

				<div class="es-flow__node">
					<?php
					// El trigger es un <button> real sólo si hay detalle que
					// revelar. Si un nodo no tiene filas de detalle, el mismo
					// contenido se emite en un <div> — no se ofrece un control
					// interactivo que no haga nada (regla de a11y, no adorno).
					$es_tag   = $es_has_panel ? 'button' : 'div';
					$es_attrs = $es_has_panel
						? ' type="button" class="es-flow__trigger" aria-expanded="false" aria-controls="' . esc_attr( $es_panel_id ) . '" data-es-flow-trigger'
						: ' class="es-flow__trigger es-flow__trigger--static"';
					?>
					<<?php echo $es_tag . $es_attrs; // phpcs:ignore WordPress.Security.EscapeOutput -- tag de whitelist + atributos ya escapados. ?>>
						<?php
						// LA FORMA ES LA CAJA. Igual que en el diagrama de
						// referencia: pastilla (inicio/fin), rectángulo (paso)
						// o rombo (decisión) con el título ADENTRO; la
						// descripción va afuera, debajo, en tinta apagada.
						?>
						<span class="es-flow__shape">
							<?php if ( $es_i > 0 ) : ?>
								<?php
								/*
								 * El conector es hijo de LA FORMA, no del <li>. Así queda
								 * centrado sobre la forma por CSS puro (top:50% / left:50%)
								 * sin ningún número mágico, y sigue estando bien tanto en un
								 * rectángulo de una línea como en un rombo mucho más alto o
								 * en un título que envuelve a dos líneas. Cuando colgaba del
								 * <li> había que adivinar la altura y se desalineaba.
								 */
								$es_edge = trim( (string) ( $es_node['edgeLabel'] ?? '' ) );
								?>
								<span class="es-flow__rail" aria-hidden="true">
									<?php echo es_flow_connector_svg(); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG del propio plugin, sin dato de usuario. ?>
								</span>
								<?php if ( '' !== $es_edge ) : ?>
									<?php
									/*
									 * La etiqueta de la flecha es hermana del riel, no hija:
									 * el riel está centrado verticalmente sobre la forma, así
									 * que cualquier cosa adentro caía ENCIMA de la caja. Como
									 * hija de la forma puede colocarse arriba de ella y no
									 * pisa ni el número ni el título.
									 */
									?>
									<span class="es-flow__edge"><?php echo esc_html( $es_edge ); ?></span>
								<?php endif; ?>
							<?php endif; ?>
							<span class="es-flow__shape-inner">
								<?php if ( '' !== $es_num ) : ?>
									<span class="es-flow__num">
										<?php if ( '' !== $es_step_lb ) : ?>
											<span class="es-visually-hidden"><?php echo esc_html( $es_step_lb ); ?> </span>
										<?php endif; ?>
										<?php echo esc_html( $es_num ); ?>
									</span>
								<?php endif; ?>
								<span class="es-flow__title"><?php echo wp_kses_post( $es_title ); ?></span>
							</span>
							<?php if ( ! empty( $es_node['ai'] ) ) : ?>
								<?php // Marcador (IA) del diagrama de referencia: punto donde interviene el asistente. La leyenda al pie lo explica una sola vez. ?>
								<span class="es-flow__ai" title="<?php echo esc_attr( $es_ai_legend ); ?>"><span class="es-visually-hidden"><?php echo esc_html( $es_ai_legend ); ?></span><span aria-hidden="true">IA</span></span>
							<?php endif; ?>
						</span>

						<span class="es-flow__body">
							<?php if ( '' !== $es_text ) : ?>
								<span class="es-flow__text"><?php echo wp_kses_post( $es_text ); ?></span>
							<?php endif; ?>

							<?php if ( $es_has_panel && '' !== $es_detail ) : ?>
								<?php
								/*
								 * En mobile no hay hover: esto es el ÚNICO indicador de que
								 * el nodo se puede abrir, así que lleva un ícono de flecha
								 * (no sólo texto) y gira al abrirse — misma lógica que un
								 * desplegable. El <button> ya cubre toda la tarjeta como
								 * área táctil; esto es la señal visual, no un segundo target.
								 */
								?>
								<span class="es-flow__more">
									<span class="es-flow__more-label"><?php echo esc_html( $es_detail ); ?></span>
									<svg class="es-flow__more-icon" viewBox="0 0 12 12" aria-hidden="true" focusable="false">
										<polyline points="2.5,4.5 6,8 9.5,4.5" />
									</svg>
								</span>
							<?php endif; ?>
						</span>
					</<?php echo esc_html( $es_tag ); ?>>

					<?php if ( ! empty( $es_branches ) ) : ?>
						<?php // Las ramas son ESTRUCTURA (sí/no de una decisión), no detalle: siempre visibles, nunca escondidas tras el popover. ?>
						<ul class="es-flow__branches">
							<?php foreach ( $es_branches as $es_branch ) : ?>
								<li class="es-flow__branch">
									<span class="es-flow__branch-label"><?php echo esc_html( trim( (string) $es_branch['label'] ) ); ?></span>
									<?php if ( '' !== trim( (string) ( $es_branch['text'] ?? '' ) ) ) : ?>
										<span class="es-flow__branch-text"><?php echo wp_kses_post( trim( (string) $es_branch['text'] ) ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $es_has_panel ) : ?>
						<div class="es-flow__panel" id="<?php echo esc_attr( $es_panel_id ); ?>" data-es-flow-panel>
							<dl class="es-flow__detail">
								<?php foreach ( $es_rows as $es_row ) : ?>
									<?php $es_row_label = trim( (string) ( $es_row['label'] ?? '' ) ); ?>
									<?php if ( '' !== $es_row_label ) : ?>
										<dt><?php echo esc_html( $es_row_label ); ?></dt>
									<?php endif; ?>
									<dd><?php echo wp_kses_post( trim( (string) $es_row['text'] ) ); ?></dd>
								<?php endforeach; ?>
							</dl>
							<?php if ( '' !== $es_close ) : ?>
								<button type="button" class="es-flow__close" data-es-flow-close><?php echo esc_html( $es_close ); ?></button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>

	<?php if ( '' !== $es_end ) : ?>
		<p class="es-flow__marker es-flow__marker--end"><span><?php echo esc_html( $es_end ); ?></span></p>
	<?php endif; ?>

	<?php
	// Leyenda del marcador (IA), una sola vez al pie — igual que el diagrama
	// de referencia. Sólo se imprime si algún nodo realmente lo usa.
	$es_uses_ai = false;
	foreach ( $es_nodes as $es_n ) {
		if ( ! empty( $es_n['ai'] ) ) {
			$es_uses_ai = true;
			break;
		}
	}
	?>
	<?php if ( $es_uses_ai && '' !== $es_ai_legend ) : ?>
		<p class="es-flow__legend"><span class="es-flow__ai es-flow__ai--legend" aria-hidden="true">IA</span> <?php echo esc_html( $es_ai_legend ); ?></p>
	<?php endif; ?>
</div>
