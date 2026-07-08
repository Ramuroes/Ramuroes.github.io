<?php
/**
 * Featured case (Main case) — sección destacada, dos columnas.
 *
 * CONTENIDO PLACEHOLDER (copy del Home v4). Editable sin tocar markup vía el
 * filtro 'es_home_featured'. Si 'image' es null se muestra el marco placeholder.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num = isset( $args['num'] ) ? $args['num'] : '02';

$es_featured = apply_filters(
	'es_home_featured',
	array(
		'kicker'   => 'Product + System Design · In progress',
		'title'    => 'A decision system for <em>metal workshop budgeting.</em>',
		'body'     => "A metal fabrication workshop that quotes well — but slowly, and from one person's head. Estimates queue up, and hours go into requests that end up not closing. I'm designing a system that turns that tacit knowledge into explicit, versioned criteria: consistent, transferable, and able to answer early with an orientative range before hours are committed.",
		'source'   => 'Developed and implemented at Guzmán Villalba — metal fabrication workshop, Montevideo.',
		'status'   => 'In progress',
		'url'      => '#',
		'image'    => null,
	)
);
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
					<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
				</a>
			</div>

			<div class="es-featured__media" data-es-reveal style="--es-reveal-delay: 90ms">
				<?php if ( ! empty( $es_featured['image'] ) ) : ?>
					<img src="<?php echo esc_url( $es_featured['image'] ); ?>" alt="" loading="lazy" />
				<?php else : ?>
					<div class="es-placeholder es-featured__placeholder" role="img" aria-label="<?php esc_attr_e( 'Placeholder for the featured case visual', 'estavillo-child' ); ?>">
						<span class="es-placeholder__tag">{asset: featured-gv}</span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
