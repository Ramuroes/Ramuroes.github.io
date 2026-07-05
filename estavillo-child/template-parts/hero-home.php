<?php
/**
 * Hero de la home — system map animado detrás/al costado del copy.
 *
 * TODO EL COPY DE ESTE ARCHIVO ES PLACEHOLDER (tomado de los mockups).
 * El copy final se define después; es editable sin tocar el markup vía
 * los filtros es_home_hero_title / es_home_hero_lead / es_home_hero_*_url
 * (por ejemplo desde Code Snippets).
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_hero_title = apply_filters(
	'es_home_hero_title',
	'I design systems that turn <em>complexity</em> into <em class="es-accent-word">clarity</em>.'
);
$es_hero_lead  = apply_filters(
	'es_home_hero_lead',
	'AI-assisted tools and workflows for real operations. From estimating to execution.'
);

$es_hero_primary_url   = apply_filters( 'es_home_hero_primary_url', '#work' );
$es_hero_secondary_url = apply_filters( 'es_home_hero_secondary_url', '#process' );
?>

<section
	class="es-hero"
	data-hero-desktop="<?php echo esc_attr( es_get_option( 'es_hero_variant_desktop' ) ); ?>"
	data-hero-mobile="<?php echo esc_attr( es_get_option( 'es_hero_variant_mobile' ) ); ?>"
>
	<div class="es-hero__visual" data-es-hero-map aria-hidden="true"></div>

	<div class="es-container es-hero__inner">
		<div class="es-hero__content">
			<p class="es-eyebrow es-hero__eyebrow"><?php echo esc_html( es__( 'hero_eyebrow' ) ); ?></p>

			<h1 class="es-h1 es-hero__title">
				<?php
				echo wp_kses(
					$es_hero_title,
					array(
						'em'   => array( 'class' => array() ),
						'span' => array( 'class' => array() ),
					)
				);
				?>
			</h1>

			<p class="es-lead es-hero__lead"><?php echo esc_html( $es_hero_lead ); ?></p>

			<div class="es-hero__actions">
				<a class="es-btn" href="<?php echo esc_url( $es_hero_primary_url ); ?>">
					<?php echo esc_html( es__( 'hero_cta_primary' ) ); ?>
					<span class="es-btn__arrow" aria-hidden="true">&rarr;</span>
				</a>
				<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_hero_secondary_url ); ?>">
					<?php echo esc_html( es__( 'hero_cta_secondary' ) ); ?>
					<span class="es-live-dot" aria-hidden="true"></span>
				</a>
			</div>
		</div>
	</div>
</section>
