<?php
/**
 * Template Name: Estavillo — Contact
 * Description: Página de contacto dedicada — mismos datos "Connect" de
 * Home (título, lead, email) más los de Footer (redes, ubicación), todos
 * editables desde Home Content. Ningún filtro/campo nuevo — ver
 * template-parts/contact-content.php.
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
<body <?php body_class( 'es-page es-contact-page-body' ); ?>>
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
				'title' => __( 'Contact.', 'estavillo-child' ),
			)
		);

		get_template_part( 'template-parts/contact-content' );

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
