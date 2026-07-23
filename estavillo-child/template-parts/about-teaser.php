<?php
/**
 * About teaser — retrato (placeholder) + párrafo editorial.
 *
 * Home migration ticket: 'es_home_about_text'/'es_home_about_portrait' ya
 * son EXACTAMENTE los mismos filtros que about-content.php usa para el
 * cuerpo real de la página About (puenteados por el plugin al mismo campo
 * de opción about_text/about_portrait — confirmado en
 * includes/home-content-options.php) — cero contenido duplicado, ya
 * comparten una única fuente. Lo único que faltaba: este teaser mostraba
 * el texto COMPLETO de About como un solo párrafo (con los saltos de
 * línea colapsados). Ahora usa es_about_intro_paragraphs() — la misma
 * función que la página About usa para partir su intro en párrafos — y
 * solo muestra el primero, un excerpt genuino en vez de una copia
 * independiente.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num = isset( $args['num'] ) ? $args['num'] : '04';

$es_about_text_full = apply_filters( 'es_home_about_text', es_about_intro_default() );
$es_about_paragraphs = es_about_intro_paragraphs( $es_about_text_full );
$es_about_text       = ! empty( $es_about_paragraphs[0] ) ? $es_about_paragraphs[0] : $es_about_text_full;

$es_about_url      = apply_filters( 'es_home_about_url', '#about' );
$es_about_portrait = apply_filters( 'es_home_about_portrait', '' );
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
				<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
			</a>
		</div>
	</div>
</section>
