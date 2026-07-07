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

$es_about_text = apply_filters(
	'es_home_about_text',
	"I'm Ramiro Estavillo, Product Designer. I studied Industrial Design and have spent over a decade designing across physical products, communication systems, digital interfaces and operational workflows. What connects all of it is a preference for understanding how things actually work before proposing how they could work better."
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
					<span class="es-section-head__num">04</span>
					<h2 class="es-label"><?php echo esc_html( es__( 'about_label' ) ); ?></h2>
				</div>
			</div>
			<p class="es-about__text" data-es-reveal><?php echo esc_html( $es_about_text ); ?></p>
			<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_about_url ); ?>" data-es-reveal>
				<?php echo esc_html( es__( 'about_cta' ) ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		</div>
	</div>
</section>
