<?php
/**
 * Template Name: Estavillo — Contact
 * Description: Página Connect — Gutenberg migration (ticket Connect). El
 * H1 de esta página decía "Contact." mientras que nav/breadcrumb siempre
 * dijeron "Connect" — inconsistencia real, corregida acá: eyebrow "Get in
 * touch" + título "Let's connect." (es_connect_eyebrow/es_connect_title,
 * editables). page-head (eyebrow+H1+lead) se imprime SIEMPRE, tanto si la
 * Page ya tiene contenido Gutenberg real como si todavía cae al fallback
 * — mismo criterio que About/How I Work (ver ahí para el precedente): el
 * cuerpo de abajo (métodos de contacto + formulario) es la única parte
 * migrada, en una rama explícita either/or (real content -> the_content();
 * sin contenido real -> template-parts/contact-content.php). Antes esta
 * página imprimía contact-content.php SIEMPRE y además el post content
 * real si existía (nunca eran mutuamente excluyentes) — corregido acá.
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
	<?php get_template_part( 'template-parts/breadcrumbs', null, array( 'trail' => es_breadcrumb_trail( 'nav_connect' ) ) ); ?>

	<main id="top" class="es-main">
		<?php
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'eyebrow' => apply_filters( 'es_connect_eyebrow', 'Get in touch' ),
				'title'   => apply_filters( 'es_connect_title', "Let's connect." ),
				'lead'    => apply_filters( 'es_connect_intro', "If you'd like to discuss a product, a process, a collaboration or simply say hello, I'd be glad to hear from you." ),
			)
		);

		$es_connect_content = '';
		if ( have_posts() ) :
			the_post();
			$es_connect_content = trim( get_the_content() );
		endif;

		if ( '' !== $es_connect_content ) :
			the_content();
		else :
			get_template_part( 'template-parts/contact-content' );
		endif;
		?>
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
