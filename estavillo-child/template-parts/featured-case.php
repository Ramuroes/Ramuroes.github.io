<?php
/**
 * Featured case (Main case) — sección destacada, dos columnas.
 *
 * Editable sin tocar markup vía el filtro 'es_home_featured'. La fuente de
 * datos es es_home_featured_source() (inc/featured-case-fallback.php): el
 * Case Study marcado "featured" del plugin "Estavillo Portfolio Core" si
 * existe y está activo, si no el placeholder de siempre — ver ese archivo
 * para el detalle. Si 'image' es null se muestra el marco placeholder.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num = isset( $args['num'] ) ? $args['num'] : '02';

$es_featured = apply_filters( 'es_home_featured', es_home_featured_source() );

$es_featured_tag = ! empty( $es_featured['placeholder_label'] ) ? $es_featured['placeholder_label'] : 'featured-gv';
?>

<section class="es-section es-section--block es-featured" id="featured">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num"><?php echo esc_html( $es_num ); ?></span>
				<h2 class="es-label"><?php echo esc_html( es__( 'featured_label' ) ); ?></h2>
			</div>
		</div>

		<div class="es-featured__grid">
			<div class="es-featured__text" data-es-reveal>
				<div class="es-eyebrow es-featured__kicker"><?php echo esc_html( $es_featured['kicker'] ); ?></div>
				<h3 class="es-featured__title">
					<?php echo wp_kses( $es_featured['title'], array( 'em' => array() ) ); ?>
				</h3>
				<p class="es-featured__body"><?php echo esc_html( $es_featured['body'] ); ?></p>
				<?php if ( ! empty( $es_featured['source'] ) ) : ?>
					<p class="es-featured__source"><?php echo esc_html( $es_featured['source'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $es_featured['status'] ) ) : ?>
					<div class="es-featured__status">
						<span class="es-status-pill"><span class="es-live-dot" aria-hidden="true"></span><?php echo esc_html( $es_featured['status'] ); ?></span>
					</div>
				<?php endif; ?>
				<a class="es-link-arrow es-featured__cta" href="<?php echo esc_url( $es_featured['url'] ); ?>">
					<?php echo esc_html( es__( 'featured_cta' ) ); ?>
				</a>
			</div>

			<div class="es-featured__media" data-es-reveal style="--es-reveal-delay: 90ms">
				<?php if ( ! empty( $es_featured['image'] ) ) : ?>
					<img src="<?php echo esc_url( $es_featured['image'] ); ?>" alt="" loading="lazy" />
				<?php else : ?>
					<div class="es-placeholder es-featured__placeholder" role="img" aria-label="<?php esc_attr_e( 'Placeholder for the featured case visual', 'estavillo-child' ); ?>">
						<span class="es-placeholder__tag">{asset: <?php echo esc_html( $es_featured_tag ); ?>}</span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
