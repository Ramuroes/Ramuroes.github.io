<?php
/**
 * Template Name: Estavillo — Contact
 * Description: Página Connect — Gutenberg migration (ticket Connect) +
 * revisión visual/estructural (ticket Connect — revision). El H1 de esta
 * página decía "Contact." mientras que nav/breadcrumb siempre dijeron
 * "Connect" — inconsistencia real, corregida en el ticket original: eyebrow
 * "Get in touch" + título (es_connect_eyebrow/es_connect_title, editables).
 *
 * Revisión: título por defecto pasa de "Let's connect." a "Start a
 * conversation." — la página ya usa "Let's talk." más abajo (encabezado de
 * la columna izquierda, dentro del cuerpo Gutenberg/fallback) y repetir
 * "Let's" en dos lugares se sentía redundante. page-head (eyebrow+H1+lead)
 * se sigue imprimiendo SIEMPRE — el ticket de revisión pide explícitamente
 * "Preserve the approved page-head system", así que esta pieza NO se migra
 * a bloques Gutenberg (sería inconsistente con About/How I Work/Work, que
 * comparten el mismo componente) — solo cambia el copy por defecto. El
 * cuerpo de abajo (layout de contacto de 2 columnas + formulario) sigue
 * siendo la única parte migrada, en la misma rama explícita either/or de
 * siempre (real content -> the_content(); sin contenido real ->
 * template-parts/contact-content.php).
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
				'title'   => apply_filters( 'es_connect_title', 'Start a conversation.' ),
				'lead'    => apply_filters( 'es_connect_intro', "I'm open to Product Design, Design Systems and UX Research roles — anywhere the goal is making a real system work better, not just look better." ),
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
