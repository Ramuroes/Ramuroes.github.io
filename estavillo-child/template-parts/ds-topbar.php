<?php
/**
 * Barra institucional de la página del Design System.
 *
 * Deliberadamente mínima y secundaria: el protagonista de esta página es la
 * documentación de REstimator, no el portfolio. Por eso NO se imprime el
 * chrome completo (site-header + menú + footer) — competiría de frente con el
 * rail de navegación del propio Design System, que ya ocupa la columna
 * izquierda y ya resuelve la navegación interna.
 *
 * Tampoco es sticky: el rail del DS es position:sticky con height:100vh, y una
 * barra fija arriba obligaría a recalcular su top y su alto. Como el rail ya da
 * navegación permanente, la barra puede quedarse arriba del documento y salir
 * de escena al scrollear.
 *
 * Usa tokens --es-* (los del portfolio), no --re-*: es la única franja de la
 * página que pertenece al portfolio y no al Design System.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_ds_back = es_ds_restimator_case_url();
if ( '' === $es_ds_back ) {
	// Sin Case Study publicado (o plugin inactivo): el "volver" cae al listado
	// de trabajos, y si esa página tampoco existe, a la Home. Nunca imprime un
	// enlace roto.
	$es_ds_back = es_page_url_by_template( 'templates/page-work.php' );
}
if ( '' === $es_ds_back ) {
	$es_ds_back = home_url( '/' );
}
?>
<div class="es-ds-topbar">
	<div class="es-ds-topbar__in">
		<a class="es-ds-topbar__back" href="<?php echo esc_url( $es_ds_back ); ?>">
			<span class="es-ds-topbar__arrow" aria-hidden="true">&larr;</span>
			<?php echo esc_html( es__( 'ds_back_to_case' ) ); ?>
		</a>
		<a class="es-ds-topbar__owner" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html( es__( 'ds_owner' ) ); ?>
		</a>
	</div>
</div>
