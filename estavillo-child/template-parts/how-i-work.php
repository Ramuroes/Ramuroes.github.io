<?php
/**
 * How I work — teaser compacto en Home.
 *
 * Reescrito para docs/HOW-I-WORK-CONTENT-SPEC.md §1A: ya no es una
 * versión más chica de la grilla de 6 pasos (esa grilla se retira de acá
 * por completo) — es un preview genuino de 3 ideas (Understand/Explore/
 * Improve), con su propio copy (es_home_process_teaser(), functions.php),
 * deliberadamente distinto del copy de cada uno de los 6 pasos
 * individuales. La página dedicada (how-i-work-detail.php / la versión
 * Gutenberg de templates/page-how-i-work.php) sigue siendo la única
 * fuente de los 6 pasos completos.
 *
 * Cada idea lleva una ilustración chica y secundaria (integración de
 * assets — estavillo-how-i-work-staging.zip): Understand -> paso 1,
 * Explore -> paso 4, Improve -> paso 6, elegidas a mano por orden de idea,
 * no configurables desde wp-admin (es una decisión de layout, no de
 * contenido — el copy de cada grupo sigue viniendo 100% de
 * es_home_process_teaser()). Mismo renderer compartido que el bloque de
 * Gutenberg de la página dedicada (es_how_work_illustration_svg(),
 * inc/how-i-work-illustrations.php) — si el archivo SVG faltara, la
 * función devuelve '' y el layout no se rompe, sin necesidad de manejar
 * ese caso acá.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num = isset( $args['num'] ) ? $args['num'] : '01';

$es_teaser       = es_home_process_teaser();
$es_process_url  = apply_filters( 'es_home_process_url', '#process' );
$es_teaser_steps = array( 1, 4, 6 );
?>

<section class="es-section es-process-teaser" id="process">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num"><?php echo esc_html( $es_num ); ?></span>
				<h2 class="es-label"><?php echo esc_html( es__( 'process_label' ) ); ?></h2>
			</div>
			<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_process_url ); ?>">
				<?php echo esc_html( es__( 'process_cta' ) ); ?>
			</a>
		</div>

		<?php if ( ! empty( $es_teaser['headline'] ) ) : ?>
			<h3 class="es-process-teaser__headline" data-es-reveal><?php echo esc_html( $es_teaser['headline'] ); ?></h3>
		<?php endif; ?>

		<?php if ( ! empty( $es_teaser['lead'] ) ) : ?>
			<p class="es-process-teaser__lead" data-es-reveal><?php echo esc_html( $es_teaser['lead'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $es_teaser['groups'] ) && is_array( $es_teaser['groups'] ) ) : ?>
			<ol class="es-process-teaser__row" data-es-reveal>
				<?php foreach ( array_values( $es_teaser['groups'] ) as $es_i => $es_group ) : ?>
					<?php if ( empty( $es_group['title'] ) ) { continue; } ?>
					<li class="es-process-teaser__group">
						<?php
						$es_step = isset( $es_teaser_steps[ $es_i ] ) ? $es_teaser_steps[ $es_i ] : 0;
						if ( $es_step && function_exists( 'es_how_work_illustration_svg' ) ) :
							$es_illustration = es_how_work_illustration_svg(
								$es_step,
								array(
									'context'      => 'home',
									'show_accents' => true,
									'decorative'   => true,
									'class'        => 'es-process-teaser__group-illustration',
								)
							);
							if ( '' !== $es_illustration ) :
								echo $es_illustration; // phpcs:ignore WordPress.Security.EscapeOutput -- ya sanitizado en es_how_work_illustration_svg().
							endif;
						endif;
						?>
						<h4 class="es-process-teaser__group-title"><?php echo esc_html( $es_group['title'] ); ?></h4>
						<?php if ( ! empty( $es_group['text'] ) ) : ?>
							<p class="es-process-teaser__group-text"><?php echo esc_html( $es_group['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
