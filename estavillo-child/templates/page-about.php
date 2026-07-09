<?php
/**
 * Template Name: Estavillo — About
 * Description: Página About dedicada — intro más grande, retrato, texto
 * editable, experiencia, educación/certificados, hobbies y descarga de CV.
 * Reusa los mismos campos de "Home Content" (about_text/about_portrait) que
 * ya existían desde Sprint 3, más los nuevos de esta página (CV, timeline,
 * educación, hobbies) — todos editables desde el mismo lugar en wp-admin.
 *
 * Standalone igual que templates/page-home-estavillo.php: imprime su
 * propio wp_head()/wp_footer() y reusa el chrome ESTAVILLO.
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
<body <?php body_class( 'es-page es-about-page' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>

	<main id="top" class="es-main">
		<?php
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'eyebrow' => es__( 'about_label' ),
				'title'   => __( 'About.', 'estavillo-child' ),
			)
		);

		get_template_part( 'template-parts/about-content' );

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
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
