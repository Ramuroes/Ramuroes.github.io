<?php
/**
 * How I work — teaser de proceso (6 pasos).
 *
 * CONTENIDO PLACEHOLDER (Home v4). Editable vía filtro 'es_home_process_steps'.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num = isset( $args['num'] ) ? $args['num'] : '01';

/*
 * Cada paso admite (opcional, a futuro) una clave 'icon' — HTML de un
 * ícono/ilustración. Mientras esté vacío se reserva el slot .es-process__icon
 * para que agregar íconos o motion luego NO cambie el layout.
 */
$es_steps = apply_filters(
	'es_home_process_steps',
	array(
		array(
			'title' => 'Understand the system',
			'text'  => 'Map the operational context before touching any screen.',
			'icon'  => '',
		),
		array(
			'title' => 'Map the flows',
			'text'  => 'Make the invisible process visible and shared.',
			'icon'  => '',
		),
		array(
			'title' => 'Identify bottlenecks',
			'text'  => 'Find where decisions, information or knowledge break down.',
			'icon'  => '',
		),
		array(
			'title' => 'Design interventions',
			'text'  => 'Prototype the logic before the interface.',
			'icon'  => '',
		),
		array(
			'title' => 'Validate in the real world',
			'text'  => 'Real users, real data, real pressure.',
			'icon'  => '',
		),
		array(
			'title' => 'Document and scale',
			'text'  => 'Good design must survive the designer.',
			'icon'  => '',
		),
	)
);

$es_process_url = apply_filters( 'es_home_process_url', '#process' );

// En Home este CTA lleva a la sección completa (ancla #process en la misma
// página de una sola scroll). En la página dedicada How I Work ya ESTAMOS
// ahí, así que templates/page-how-i-work.php pasa 'hide_cta' para no
// mostrar un link que apunte a sí mismo.
$es_hide_cta = ! empty( $args['hide_cta'] );
?>

<section class="es-section es-process" id="process">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num"><?php echo esc_html( $es_num ); ?></span>
				<h2 class="es-label"><?php echo esc_html( es__( 'process_label' ) ); ?></h2>
			</div>
			<?php if ( ! $es_hide_cta ) : ?>
				<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_process_url ); ?>">
					<?php echo esc_html( es__( 'process_cta' ) ); ?>
					<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>

		<ol class="es-process__grid">
			<?php foreach ( $es_steps as $es_i => $es_step ) : ?>
				<li class="es-process__step" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( ( $es_i % 3 ) * 80 ); ?>ms">
					<div class="es-process__head">
						<span class="es-process__num"><?php echo esc_html( sprintf( '%02d', $es_i + 1 ) ); ?></span>
						<?php
						// Ícono: primero la librería curada (clave whitelisted, elegida
						// desde Home Content en wp-admin), si no la clave 'icon' legacy
						// (HTML libre, wp_kses_post) por compatibilidad hacia atrás. Sin
						// ninguno de los dos, queda el marcador vacío de siempre — el
						// layout no cambia en ningún caso.
						$es_icon_key  = ! empty( $es_step['icon_key'] ) ? $es_step['icon_key'] : '';
						$es_icon_svg  = $es_icon_key && function_exists( 'es_process_icon_svg' ) ? es_process_icon_svg( $es_icon_key ) : '';
						$es_has_icon  = '' !== $es_icon_svg || ! empty( $es_step['icon'] );
						?>
						<span class="es-process__icon<?php echo $es_has_icon ? '' : ' es-process__icon--empty'; ?>" aria-hidden="true">
							<?php
							if ( '' !== $es_icon_svg ) {
								echo wp_kses(
									$es_icon_svg,
									array(
										'svg'    => array(
											'width'   => true,
											'height'  => true,
											'viewbox' => true,
											'fill'    => true,
											'stroke'  => true,
											'stroke-width' => true,
											'stroke-linecap' => true,
											'stroke-linejoin' => true,
										),
										'circle' => array(
											'cx' => true,
											'cy' => true,
											'r'  => true,
											'fill' => true,
										),
										'path'   => array(
											'd'    => true,
											'fill' => true,
										),
										'rect'   => array(
											'x'      => true,
											'y'      => true,
											'width'  => true,
											'height' => true,
											'rx'     => true,
										),
									)
								);
							} elseif ( ! empty( $es_step['icon'] ) ) {
								echo wp_kses_post( $es_step['icon'] );
							}
							?>
						</span>
					</div>
					<h3 class="es-process__title"><?php echo esc_html( $es_step['title'] ); ?></h3>
					<p class="es-process__text"><?php echo esc_html( $es_step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
