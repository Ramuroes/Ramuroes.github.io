<?php
/**
 * Template Name: Estavillo — REstimator Design System
 * Description: Publica la documentación maestra del Design System de
 * REstimator dentro del portfolio. Pensada para /lab/restimator-design-system/,
 * enlazada desde el Case Study de REstimator.
 *
 * Standalone igual que page-home-estavillo.php, page-work.php y
 * single-es_case_study.php: imprime su propio wp_head()/wp_footer() y NO usa
 * get_header().
 *
 * Header y footer institucionales son opcionales POR PÁGINA (meta box
 * "REstimator Design System", ambos activos por defecto). Cuando están
 * activos se imprimen los template-parts REALES del portfolio, no una copia:
 * es el mismo header y el mismo footer que el resto del sitio. Con el header
 * apagado queda una barra mínima de salida (template-parts/ds-topbar.php), así
 * nunca hay una página sin manera de volver.
 *
 * El wrapper .es-page NO es decorativo: assets/css/base.css lo documenta como
 * ancestro esperado de .es-site-header (que es position:sticky) y ahí viven
 * los resets de box-sizing/headings/controles que el chrome necesita para
 * verse bien. Por eso envuelve toda la página, incluido el documento — y por
 * eso doc-overrides.css refuerza el blindaje de .re-doc contra las reglas
 * .es-page * (ver el comentario ahí).
 *
 * El cuerpo del documento NO sale de post_content: son ~130 KB de markup
 * generados por tools/build-ds.mjs desde la fuente en docs/ds-src/restimator/.
 * Lo que se escriba en el editor de esta página se ignora deliberadamente; la
 * página existe para fijar la ruta, el título y el idioma (Polylang).
 *
 * Compatible con Polylang: duplicar esta página vía "+ Agregar traducción",
 * mismo template. El documento se sirve en el idioma de la página
 * (master-es.php / master-en.php) — ver es_ds_restimator_lang().
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( have_posts() ) {
	the_post();
}

$es_ds_header = es_ds_show_header();
$es_ds_footer = es_ds_show_footer();

/*
 * Qué barra superior queda arriba del documento. El rail del Design System es
 * sticky y tiene que arrancar JUSTO debajo de ella, así que su offset depende
 * de cuál de las dos se imprimió: el header institucional (66px, marcado por el
 * propio tema con .es-header-sticky) o la navegación mínima (48px). Sin esta
 * clase el CSS no puede distinguir "sin header" de "header estático", y el rail
 * quedaría tapado por la barra mínima.
 */
$es_ds_body_class = $es_ds_header ? array( 'es-ds-page' ) : array( 'es-ds-page', 'es-ds-minimal-nav' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( $es_ds_body_class ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php if ( $es_ds_header ) : ?>
		<?php get_template_part( 'template-parts/site-header' ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/ds-topbar' ); ?>
	<?php endif; ?>

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
			echo '<div class="es-ds-missing"><p>' . esc_html( es_ds_text( 'missing' ) ) . '</p></div>';
		}
		?>
	</main>

	<?php if ( $es_ds_footer ) : ?>
		<?php get_template_part( 'template-parts/site-footer' ); ?>
	<?php endif; ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
