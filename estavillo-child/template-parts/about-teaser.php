<?php
/**
 * About teaser — retrato (marco reservado) + párrafo editorial.
 *
 * Esta es la rama LEGACY FALLBACK de Home (página Home sin contenido
 * Gutenberg). Refinement ticket §5: la Home About teaser ya NO sincroniza
 * con la página About — en la rama Gutenberg se edita directo con bloques
 * core (core/image + core/paragraph + CTA). Como el fallback no tiene
 * edición por-página, muestra un default estático propio del tema
 * (es_about_intro_default(), primer párrafo) — sin leer la página About ni
 * ninguna opción/filtro de sincronización.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num = isset( $args['num'] ) ? $args['num'] : '04';

// Default estático del tema — primer párrafo del intro por defecto. Sin
// sincronización con la página About (refinement ticket §5).
$es_about_text = '';
if ( function_exists( 'es_about_intro_default' ) ) {
	$es_about_default = (string) es_about_intro_default();
	$es_about_paras   = preg_split( '/\r\n\r\n|\n\n+/', trim( $es_about_default ) );
	$es_about_text    = ! empty( $es_about_paras[0] ) ? trim( $es_about_paras[0] ) : trim( $es_about_default );
}

$es_about_url      = apply_filters( 'es_home_about_url', es_nav_page_or_anchor( 'templates/page-about.php', '#about' ) );
$es_about_portrait = '';
?>

<section class="es-section es-about" id="about">
	<div class="es-container es-about__grid">
		<div class="es-about__portrait" data-es-reveal>
			<?php if ( ! empty( $es_about_portrait ) ) : ?>
				<img src="<?php echo esc_url( $es_about_portrait ); ?>" alt="" loading="lazy" />
			<?php else : ?>
				<?php // Marco reservado limpio, sin tag {asset:…} — alineado con estavillo/about-teaser-portrait (revisión de Home: sin marcadores de desarrollo visibles). ?>
				<div class="es-placeholder es-about__placeholder" role="img" aria-label="<?php esc_attr_e( 'Reserved space for the editorial portrait', 'estavillo-child' ); ?>"></div>
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
			</a>
		</div>
	</div>
</section>
