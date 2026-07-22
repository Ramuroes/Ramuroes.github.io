<?php
/**
 * Template Name: Estavillo — How I Work
 * Description: Página dedicada al proceso.
 *
 * Gutenberg migration (docs/HOW-I-WORK-STRATEGY.md +
 * docs/HOW-I-WORK-CONTENT-SPEC.md): the page body is real, editable Page
 * content — same explicit, mutually exclusive pattern already proven on
 * templates/page-about.php. Real content → the_content(); no real
 * content yet → the existing template-parts/how-i-work-detail.php
 * fallback (same 6-step data model, es_home_process_steps() in
 * functions.php, completely unchanged). Never both in the same request.
 * Do not remove the fallback branch until both languages are live and
 * this has been explicitly approved — see docs/BACKLOG.md.
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
<body <?php body_class( 'es-page es-how-page' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>
	<?php get_template_part( 'template-parts/breadcrumbs', null, array( 'trail' => es_breadcrumb_trail( 'nav_how' ) ) ); ?>

	<main id="top" class="es-main">
		<?php
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'eyebrow' => es__( 'process_label' ),
				'title'   => __( 'How I work.', 'estavillo-child' ),
				'lead'    => __( "I don't start with interfaces. I start by understanding the system.", 'estavillo-child' ),
			)
		);

		$es_how_content = '';
		if ( have_posts() ) :
			the_post();
			$es_how_content = trim( get_the_content() );
		endif;

		if ( '' !== $es_how_content ) :
			the_content();
		else :
			get_template_part( 'template-parts/how-i-work-detail' );
		endif;
		?>
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
