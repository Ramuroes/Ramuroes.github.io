<?php
/**
 * Template Name: Estavillo — REstimator Design System
 * Description: Publica la documentación maestra del Design System de
 * REstimator dentro del portfolio. Pensada para /lab/restimator-design-system/,
 * enlazada desde el Case Study de REstimator.
 *
 * Standalone igual que page-home-estavillo.php, page-work.php y
 * single-es_case_study.php: imprime su propio wp_head()/wp_footer() y NO usa
 * get_header(). Acá esa decisión pesa más que en el resto del tema — la página
 * no imprime el chrome ESTAVILLO (header + menú + footer) a propósito: el
 * Design System trae su propio rail de navegación sticky en la columna
 * izquierda, y un header del portfolio encima competiría de frente con él. Lo
 * único del portfolio que se imprime es la barra institucional mínima
 * (template-parts/ds-topbar.php).
 *
 * El cuerpo del documento NO sale de post_content: son ~130 KB de markup
 * generados por tools/build-ds.mjs desde la fuente en docs/ds-src/restimator/.
 * Lo que se escriba en el editor de esta página se ignora deliberadamente; la
 * página existe para fijar la ruta, el título y el idioma (Polylang).
 *
 * Compatible con Polylang: duplicar esta página vía "+ Agregar traducción",
 * mismo template. El documento cae a español mientras no exista
 * ds/restimator/master-en.php — ver es_ds_restimator_lang().
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( have_posts() ) {
	the_post();
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'es-ds-page' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<?php get_template_part( 'template-parts/ds-topbar' ); ?>

<?php
/*
 * .re-doc es la raíz de scope del Design System: TODO el CSS del DS
 * (assets/css/ds-restimator/*.css) cuelga de esta clase, así que nada de lo
 * que traiga el documento puede escaparse al resto del sitio. .re-root es la
 * clase que el propio sistema de tokens espera en su contenedor (ver
 * tokens/base.css: "add class='re-root' to <body> (or a wrapper)").
 */
?>
<main id="top" class="re-doc re-root">
	<?php
	if ( ! es_ds_restimator_render_document() ) {
		// El partial no está: el theme se instaló sin la carpeta ds/, o el
		// build todavía no corrió. Mensaje honesto en vez de una página en
		// blanco.
		echo '<div class="es-ds-missing"><p>' . esc_html( es__( 'ds_missing' ) ) . '</p></div>';
	}
	?>
</main>

<?php wp_footer(); ?>
</body>
</html>
