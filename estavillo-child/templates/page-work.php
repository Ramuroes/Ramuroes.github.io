<?php
/**
 * Template Name: Estavillo — Work
 * Description: Listado completo de Case Studies — trabajo seleccionado
 * primero (mismos casos que Home → Selected Work), archivo/trabajo más
 * antiguo debajo, claramente separado. Asignar a una página real (p. ej.
 * "Work") para reemplazar el ancla #work de la home de una sola página.
 *
 * Standalone igual que templates/page-home-estavillo.php y
 * single-es_case_study.php: imprime su propio wp_head()/wp_footer() y
 * reusa el chrome ESTAVILLO (site-header + site-footer), no el de Kadence.
 *
 * Compatible con Polylang: duplicar esta página vía "+ Agregar traducción"
 * en el editor de Páginas, mismo template, contenido propio por idioma —
 * ver README ("Páginas fijas y Polylang").
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
<body <?php body_class( 'es-page es-work-page' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>
	<?php get_template_part( 'template-parts/breadcrumbs', null, array( 'trail' => es_breadcrumb_trail( 'nav_work' ) ) ); ?>

	<main id="top" class="es-main">
		<?php
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'eyebrow' => es__( 'work_label' ),
				'title'   => es__( 'work_title' ),
				'lead'    => es__( 'work_lead' ),
			)
		);

		get_template_part( 'template-parts/work-cases' );

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
