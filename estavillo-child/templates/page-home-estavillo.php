<?php
/**
 * Template Name: Estavillo — Home (Draft)
 * Description: Home del nuevo portfolio ESTAVILLO. Hero system map animado + selected work + footer CTA. Pensado para asignarse a una página en borrador sin tocar la home en vivo.
 *
 * Uso: crear una página nueva (borrador), asignarle este template y
 * previsualizar. El contenido de bloques que se agregue en el editor se
 * imprime entre el hero y Selected Work, así se pueden sumar secciones
 * con Kadence Blocks sin editar archivos.
 *
 * Compatible con Polylang: asignar el mismo template a la página
 * traducida; los strings de interfaz se registran como "Estavillo Child"
 * en Idiomas → Traducciones de cadenas.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/hero-home' ); ?>

	<?php
	// Contenido de la propia página (bloques del editor), si lo hay.
	while ( have_posts() ) :
		the_post();
		$es_page_content = trim( get_the_content() );
		if ( '' !== $es_page_content ) :
			?>
			<section class="es-section es-page-content">
				<div class="es-container">
					<?php the_content(); ?>
				</div>
			</section>
			<?php
		endif;
	endwhile;
	?>

	<?php get_template_part( 'template-parts/selected-work' ); ?>

	<?php get_template_part( 'template-parts/footer-cta' ); ?>

</div>

<?php
get_footer();
