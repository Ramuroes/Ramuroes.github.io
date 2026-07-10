<?php
/**
 * Template Name: Estavillo — How I Work
 * Description: Página dedicada al proceso — versión editorial completa
 * (template-parts/how-i-work-detail.php) de los mismos 6 pasos que Home
 * (mismo dato, es_home_process_steps() en functions.php), con "Why it
 * matters" / "Example" / "Tools" opcionales por paso, ícono por paso
 * (librería curada — functions.php → es_process_icon_choices()), y el
 * mismo scroll-reveal sutil de siempre ([data-es-reveal], sin
 * dependencias nuevas). Distinto del teaser compacto de Home a propósito
 * — ver how-i-work-detail.php.
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
				'lead'    => __( 'The same process, every time — understand the system before proposing how it could work better.', 'estavillo-child' ),
			)
		);

		get_template_part( 'template-parts/how-i-work-detail' );

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
