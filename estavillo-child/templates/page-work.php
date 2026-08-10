<?php
/**
 * Template Name: Estavillo — Work
 * Description: 01 Featured Work / 02 Selected Work / 03 More Work (Archive)
 * — jerarquía única (ticket "Refine Work archive hierarchy"), Product
 * Design primero. Asignado a las páginas canónicas /my-work/ y
 * /es/trabajos/.
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

/*
 * El cuerpo Gutenberg de la página (el contenido viejo del portfolio —
 * "Algunos de mis trabajos" / SAMIC / French Bakery / webs anteriores /
 * industrial / 3D / visual / motion) se captura ACÁ, antes de imprimir
 * nada, en vez de imprimirse suelto al final como hasta este ticket.
 *
 * Por qué: el ticket pide que ese material quede DENTRO de la sección
 * "03 — More Work / Archive", con el mismo encabezado que los Case Studies
 * de archivo del CPT, no como un bloque sin numerar y sin relación visual
 * con el resto de la página (que es exactamente el problema que reportó:
 * "contenido viejo... archivo sin jerarquía"). template-parts/work-cases.php
 * es quien decide el número de esa sección (dinámico: depende de si hay
 * Featured Work), así que tiene que ser también quien imprime este HTML.
 *
 * the_content() normalmente ECOA directo — se envuelve en un buffer para
 * poder pasarlo como string en $args en vez de imprimirlo acá. Es el MISMO
 * contenido, ya pasado por todos los filtros de bloques/shortcodes de
 * siempre; no se reescribe ni se reordena nada de lo que el editor pegó.
 * No se toca ni se borra nada: sigue viviendo en post_content, esto sólo
 * decide DÓNDE se imprime.
 */
$es_work_legacy_html = '';
if ( have_posts() ) {
	the_post();
	ob_start();
	the_content();
	$es_work_legacy_html = trim( (string) ob_get_clean() );
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
			es_page_hero_args(
				array(
					'eyebrow' => es__( 'work_eyebrow' ),
					'title'   => es__( 'work_title' ),
					'lead'    => es__( 'work_lead' ),
				)
			)
		);

		get_template_part(
			'template-parts/work-cases',
			null,
			array( 'legacy' => $es_work_legacy_html )
		);
		?>
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
