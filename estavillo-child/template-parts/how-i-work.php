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
 * Teaser compacto a propósito: solo título+texto+ícono, aunque el paso
 * tenga 'why'/'example'/'tools' cargados — esos 3 campos opcionales solo
 * los muestra la página dedicada (how-i-work-detail.php). Datos y default
 * compartidos vía es_home_process_steps() (functions.php), así que el
 * teaser y la página dedicada siempre ven el mismo contenido base.
 */
$es_steps = es_home_process_steps();

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
						<?php $es_icon_markup = es_process_step_icon_markup( $es_step ); ?>
						<span class="es-process__icon<?php echo '' === $es_icon_markup ? ' es-process__icon--empty' : ''; ?>" aria-hidden="true">
							<?php echo $es_icon_markup; // phpcs:ignore -- ya pasado por wp_kses en es_process_step_icon_markup(). ?>
						</span>
					</div>
					<h3 class="es-process__title"><?php echo esc_html( $es_step['title'] ); ?></h3>
					<p class="es-process__text"><?php echo esc_html( $es_step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
