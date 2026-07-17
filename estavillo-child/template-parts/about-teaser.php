<?php
/**
 * About teaser — retrato (placeholder) + párrafo editorial.
 *
 * CONTENIDO PLACEHOLDER (Home v4). Editable vía filtros 'es_home_about_text',
 * 'es_home_about_url' y 'es_home_about_portrait' (URL de imagen).
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num = isset( $args['num'] ) ? $args['num'] : '04';

$es_about_text = apply_filters(
	'es_home_about_text',
	'I hold a Bachelor\'s Degree in Industrial Design (Product Design) — not a past title, but the foundation this practice still runs on. It\'s why interfaces are never my starting point: the system behind them is, with products, services and operations treated as one connected structure, understood before any part of it changes. Research stays part of the decision itself, not a report attached to it afterward, and the goal is always the same — turn a complex, often invisible process into something practical enough to use. AI increasingly helps accelerate that research, synthesis and documentation, but the decisions themselves stay grounded in human judgment.'
);
$es_about_url      = apply_filters( 'es_home_about_url', '#about' );
$es_about_portrait = apply_filters( 'es_home_about_portrait', '' );
?>

<section class="es-section es-about" id="about">
	<div class="es-container es-about__grid">
		<div class="es-about__portrait" data-es-reveal>
			<?php if ( ! empty( $es_about_portrait ) ) : ?>
				<img src="<?php echo esc_url( $es_about_portrait ); ?>" alt="" loading="lazy" />
			<?php else : ?>
				<div class="es-placeholder es-about__placeholder" role="img" aria-label="<?php esc_attr_e( 'Placeholder for the editorial portrait', 'estavillo-child' ); ?>">
					<span class="es-placeholder__tag">{asset: retrato}</span>
				</div>
			<?php endif; ?>
		</div>

		<div class="es-about__body">
			<div class="es-section-head" data-es-reveal>
				<div class="es-section-head__title">
					<span class="es-section-head__num"><?php echo esc_html( $es_num ); ?></span>
					<h2 class="es-label"><?php echo esc_html( es__( 'about_label' ) ); ?></h2>
				</div>
			</div>
			<p class="es-about__text" data-es-reveal><?php echo esc_html( $es_about_text ); ?></p>
			<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_about_url ); ?>" data-es-reveal>
				<?php echo esc_html( es__( 'about_cta' ) ); ?>
				<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
			</a>
		</div>
	</div>
</section>
