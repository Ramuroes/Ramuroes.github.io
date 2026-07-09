<?php
/**
 * Selected work — card ancha destacada + grilla 2-up (layout Home v4).
 *
 * Editable sin tocar markup vía el filtro 'es_home_selected_work'. La
 * fuente de datos es es_home_selected_work_source()
 * (inc/selected-work-fallback.php): Case Studies reales del plugin
 * "Estavillo Portfolio Core" si existen y está activo, si no los
 * placeholders de siempre — ver ese archivo para el detalle. El primer
 * caso se muestra como card ancha; el resto en una grilla de dos columnas.
 * Si 'image' es null se muestra el marco placeholder del design system.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num = isset( $args['num'] ) ? $args['num'] : '03';

$es_cases = apply_filters( 'es_home_selected_work', es_home_selected_work_source() );

$es_view_all_url = apply_filters( 'es_home_view_all_url', '#' );

$es_wide = array_shift( $es_cases );
?>

<section class="es-section es-work" id="work">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num"><?php echo esc_html( $es_num ); ?></span>
				<h2 class="es-label"><?php echo esc_html( es__( 'work_label' ) ); ?></h2>
			</div>
			<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_view_all_url ); ?>">
				<?php echo esc_html( es__( 'work_view_all' ) ); ?>
				<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
			</a>
		</div>

		<div class="es-work__stack">
			<?php if ( $es_wide ) : ?>
				<a class="es-card es-card--wide" href="<?php echo esc_url( $es_wide['url'] ); ?>" data-es-reveal>
					<div class="es-card__media"><?php es_work_media( $es_wide ); ?></div>
					<div class="es-card__body">
						<div class="es-card__meta">
							<?php if ( ! empty( $es_wide['label'] ) ) : ?>
								<span class="es-card__label"><?php echo esc_html( $es_wide['label'] ); ?></span>
							<?php endif; ?>
							<span class="es-card__kicker"><?php echo esc_html( $es_wide['kicker'] ); ?></span>
						</div>
						<div class="es-card__title"><?php echo esc_html( $es_wide['title'] ); ?></div>
						<div class="es-card__excerpt"><?php echo esc_html( $es_wide['excerpt'] ); ?></div>
						<?php if ( ! empty( $es_wide['tags'] ) ) : ?>
							<div class="es-card__tags">
								<?php foreach ( $es_wide['tags'] as $es_tag ) : ?>
									<span class="es-pill"><?php echo esc_html( $es_tag ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<span class="es-card__cta"><?php echo esc_html( es__( 'work_view_case' ) ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
					</div>
				</a>
			<?php endif; ?>

			<div class="es-grid es-grid--2">
				<?php foreach ( $es_cases as $es_i => $es_case ) : ?>
					<a class="es-card" href="<?php echo esc_url( $es_case['url'] ); ?>" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( $es_i * 90 ); ?>ms">
						<div class="es-card__media"><?php es_work_media( $es_case ); ?></div>
						<div class="es-card__body">
							<div class="es-card__meta">
								<?php if ( ! empty( $es_case['label'] ) ) : ?>
									<span class="es-card__label"><?php echo esc_html( $es_case['label'] ); ?></span>
								<?php endif; ?>
								<span class="es-card__kicker"><?php echo esc_html( $es_case['kicker'] ); ?></span>
							</div>
							<div class="es-card__title"><?php echo esc_html( $es_case['title'] ); ?></div>
							<div class="es-card__excerpt"><?php echo esc_html( $es_case['excerpt'] ); ?></div>
							<span class="es-card__cta"><?php echo esc_html( es__( 'work_view_case' ) ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
