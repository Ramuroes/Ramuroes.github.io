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
 * Compatible con Polylang (strings en "Estavillo Child") y con el editor:
 * el contenido de bloques de la página se imprime entre el hero y Featured.
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

		<?php get_template_part( 'template-parts/hero-home' ); ?>

		<?php
		// Contenido de bloques del editor, si lo hay (entre hero y Featured).
		while ( have_posts() ) :
			the_post();
			$es_page_content = trim( get_the_content() );
			if ( '' !== $es_page_content ) :
				?>
				<section class="es-section es-page-content">
					<div class="es-container"><?php the_content(); ?></div>
				</section>
				<?php
			endif;
		endwhile;
		?>

		<?php get_template_part( 'template-parts/featured-case' ); ?>
		<?php get_template_part( 'template-parts/how-i-work' ); ?>
		<?php get_template_part( 'template-parts/selected-work' ); ?>
		<?php get_template_part( 'template-parts/about-teaser' ); ?>
		<?php get_template_part( 'template-parts/footer-cta' ); ?>

	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
