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

/*
 * "All work" apuntaba a '#' — un href literal que no lleva a ningún lado.
 * El filtro existía desde Home v1 pero nadie lo puenteaba nunca, ni el theme
 * ni el plugin, así que el default crudo era lo que se servía: un link muerto
 * en la sección más importante de la Home.
 *
 * Se resuelve igual que el menú (COMMIT 2): por TEMPLATE, no por ruta. La
 * página Work de cada idioma es la que la jerarquía de WordPress devuelve
 * para su propio idioma con Polylang, así que ES lleva a la página en español
 * y EN a la inglesa sin ninguna ruta escrita a mano. El filtro sigue ahí para
 * poder apuntarlo a otro lado.
 *
 * Si esa página todavía no existe en este idioma, el resolver devuelve
 * cadena vacía y el link NO se imprime. Un "All work" que apunta a la misma
 * sección que ya estás mirando es peor que no tenerlo.
 */
$es_view_all_url = apply_filters( 'es_home_view_all_url', es_page_url_by_template( 'templates/page-work.php' ) );

$es_wide = array_shift( $es_cases );
?>

<section class="es-section es-work" id="work">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num"><?php echo esc_html( $es_num ); ?></span>
				<h2 class="es-label"><?php echo esc_html( es__( 'work_label' ) ); ?></h2>
			</div>
			<?php if ( '' !== trim( (string) $es_view_all_url ) && '#' !== $es_view_all_url ) : ?>
				<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_view_all_url ); ?>">
					<?php echo esc_html( es__( 'work_view_all' ) ); ?>
				</a>
			<?php endif; ?>
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
