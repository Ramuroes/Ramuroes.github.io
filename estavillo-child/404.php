<?php
/**
 * 404 — página no encontrada.
 *
 * Antes de este archivo, un 404 caía al template del padre (Kadence): fondo
 * claro, el logo y el menú viejos ("Home / About / My Work / Contact") que ya
 * no existen en el resto del sitio, y el copy en inglés incluso llegando
 * desde una URL /es/. Era la vista que más delataba que el sitio estaba a
 * medio migrar.
 *
 * Mismo patrón "standalone" que los otros seis templates del theme: imprime
 * su propio <head>/<body> con wp_head()/wp_footer() y reusa el chrome
 * ESTAVILLO (site-header / site-footer), en vez de get_header()/get_footer()
 * de Kadence. Nada de diseño nuevo: usa las mismas voces tipográficas
 * (.es-eyebrow, .es-h1, .es-lead) y el mismo botón que el resto del sistema.
 *
 * Idioma: todo el texto sale de es__() (registrado en Polylang, ver
 * es_child_ui_strings() en functions.php), así que responde en el idioma de
 * la request como cualquier otra página. home_url() ya resuelve a la Home del
 * idioma activo con Polylang, sin código extra.
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
<body <?php body_class( 'es-page es-404-body' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>

	<main id="top" class="es-main">
		<section class="es-section es-404">
			<div class="es-container">
				<p class="es-eyebrow es-404__code">404</p>
				<h1 class="es-h1 es-404__title"><?php echo esc_html( es__( 'error_404_title' ) ); ?></h1>
				<p class="es-lead es-404__lead"><?php echo esc_html( es__( 'error_404_lead' ) ); ?></p>
				<p class="es-404__actions">
					<a class="es-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<span><?php echo esc_html( es__( 'error_404_cta' ) ); ?></span>
						<span class="es-btn__arrow" aria-hidden="true">&rarr;</span>
					</a>
				</p>
			</div>
		</section>
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
