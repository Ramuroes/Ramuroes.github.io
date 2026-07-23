<?php
/**
 * Hero de la home — system map animado detrás/al costado del copy.
 *
 * Home migration ticket (revisado): este archivo es solo la rama LEGACY
 * FALLBACK (página Home sin contenido Gutenberg real). En la rama
 * Gutenberg — el estado normal — el Hero entero lo arma el bloque
 * estavillo/home-hero del plugin: el copy es InnerBlocks editable inline
 * (guardado en post_content, sin depender de Portfolio Content) y el
 * cascarón (fondo animado + variantes) lo emite su render.php reusando
 * este mismo sistema (es_get_option/es_hero_variants, Customizer). Los
 * apply_filters de abajo quedan como extension points a nivel código
 * (defaults idénticos al comportamiento histórico, es__() incluido para
 * la traducción Polylang) — ya no tienen UI de admin propia.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_hero_eyebrow = apply_filters( 'es_home_hero_eyebrow', es__( 'hero_eyebrow' ) );

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

$es_hero_cta_primary_label   = apply_filters( 'es_home_hero_cta_primary_label', es__( 'hero_cta_primary' ) );
$es_hero_cta_secondary_label = apply_filters( 'es_home_hero_cta_secondary_label', es__( 'hero_cta_secondary' ) );

$es_hero_desktop = es_get_option( 'es_hero_variant_desktop' );
$es_hero_mobile  = es_get_option( 'es_hero_variant_mobile' );
?>

<?php
// Debug temporal: ver el código fuente de la página (Ctrl+U) para confirmar qué
// variante emite WordPress. Si acá dice network_constellation pero el hero se ve
// distinto, es caché de JS/CSS del navegador o de un plugin de optimización.
printf(
	"\n<!-- Estavillo hero: desktop=%s mobile=%s font=%s theme_v=%s -->\n",
	esc_html( $es_hero_desktop ),
	esc_html( $es_hero_mobile ),
	esc_html( es_get_option( 'es_font_preset' ) ),
	esc_html( defined( 'ES_CHILD_VERSION' ) ? ES_CHILD_VERSION : '?' )
);
?>
<section
	class="es-hero"
	data-hero-desktop="<?php echo esc_attr( $es_hero_desktop ); ?>"
	data-hero-mobile="<?php echo esc_attr( $es_hero_mobile ); ?>"
>
	<div class="es-hero__visual" data-es-hero-map aria-hidden="true"></div>

	<div class="es-container es-hero__inner">
		<div class="es-hero__content">
			<p class="es-eyebrow es-hero__eyebrow"><?php echo esc_html( $es_hero_eyebrow ); ?></p>

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
					<?php echo esc_html( $es_hero_cta_primary_label ); ?>
					<span class="es-btn__arrow" aria-hidden="true">&rarr;</span>
				</a>
				<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_hero_secondary_url ); ?>">
					<?php echo esc_html( $es_hero_cta_secondary_label ); ?>
					<span class="es-live-dot" aria-hidden="true"></span>
				</a>
			</div>
		</div>
	</div>
</section>
