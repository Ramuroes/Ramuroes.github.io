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
?>

<section class="es-section es-process" id="process">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num"><?php echo esc_html( $es_num ); ?></span>
				<h2 class="es-label"><?php echo esc_html( es__( 'process_label' ) ); ?></h2>
			</div>
			<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_process_url ); ?>">
				<?php echo esc_html( es__( 'process_cta' ) ); ?>
				<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
			</a>
		</div>

		<ol class="es-process__grid">
			<?php foreach ( $es_steps as $es_i => $es_step ) : ?>
				<li class="es-process__step" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( ( $es_i % 3 ) * 80 ); ?>ms">
					<div class="es-process__head">
						<span class="es-process__num"><?php echo esc_html( sprintf( '%02d', $es_i + 1 ) ); ?></span>
						<?php
						// Slot reservado para ícono/motion/ilustración futura. Si no hay
						// ícono, queda un marcador sutil que no altera el layout.
						$es_has_icon = ! empty( $es_step['icon'] );
						?>
						<span class="es-process__icon<?php echo $es_has_icon ? '' : ' es-process__icon--empty'; ?>" aria-hidden="true">
							<?php
							if ( $es_has_icon ) {
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
