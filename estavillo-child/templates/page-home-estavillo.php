<?php
/**
 * Template Name: Estavillo — Home (Draft)
 * Description: Home del nuevo portfolio ESTAVILLO. Chrome propio (nav + footer
 * dark premium) + hero (network_constellation por defecto) + secciones Home v1:
 * Featured, How I Work, Selected Work, About, Connect. Pensado para asignarse a
 * una página en borrador sin tocar la home en vivo.
 *
 * Template "standalone": renderiza wp_head()/wp_footer() (así cargan todos los
 * assets encolados, el admin bar, WP Dark Mode, etc.) pero NO usa el header/
 * footer de Kadence — imprime el chrome propio de ESTAVILLO. El resto del sitio
 * sigue usando el header/footer de Kadence sin cambios.
 *
 * Home migration ticket (revisado): mismo patrón either/or que ya usan
 * page-about.php/page-how-i-work.php/page-contact.php — si la página
 * tiene contenido real de Gutenberg, ESE es el que se imprime
 * (the_content()), INCLUIDO el Hero, que en esa rama es el bloque
 * estavillo/home-hero (copy = InnerBlocks editable; cascarón con el fondo
 * animado y las variantes desktop/mobile del Customizer = render.php del
 * plugin — la animación sigue siendo 100% el sistema PHP existente). Si
 * la página está vacía, cae al loop de template-parts de siempre
 * (es_home_sections(), hero PHP incluido, sin cambios). Nunca los dos a
 * la vez.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'es-home' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>

	<main id="top" class="es-main">

		<?php
		/*
		 * Either/or, igual que las otras 3 páginas fijas: contenido real de
		 * Gutenberg (the_content(), Hero incluido como bloque
		 * estavillo/home-hero) o el loop completo de template-parts de
		 * siempre (hero PHP incluido). A cada sección de contenido se le
		 * sigue pasando su número correlativo ('num') en la rama fallback.
		 */
		$es_home_content = '';
		if ( have_posts() ) :
			the_post();
			$es_home_content = trim( get_the_content() );
		endif;

		if ( '' !== $es_home_content ) :
			the_content();
		else :
			$es_section_num = 0;
			foreach ( es_home_sections() as $es_key => $es_part ) {
				/*
				 * 'tools' no lleva numeración (ver es_home_sections()):
				 * mismo tratamiento que 'hero', así que tampoco consume un
				 * número — el contador sigue igual para About/Connect.
				 */
				if ( 'hero' === $es_key || 'tools' === $es_key ) {
					get_template_part( $es_part );
					continue;
				}
				$es_section_num++;
				get_template_part( $es_part, null, array( 'num' => sprintf( '%02d', $es_section_num ) ) );
			}
		endif;
		?>

	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
