<?php
/**
 * Navegación mínima de la página del Design System — FALLBACK.
 *
 * Sólo se imprime cuando el header institucional está apagado para esta página
 * (meta box "REstimator Design System"). Con el header activo no aparece: sería
 * una segunda barra redundante encima de la real.
 *
 * Su única razón de ser es que nunca haya una página sin salida: si alguien
 * apaga el header, tiene que quedar igual un camino de vuelta al caso.
 *
 * Deliberadamente mínima y secundaria — el protagonista de esta página es la
 * documentación de REstimator. No es sticky: el rail del Design System ya da
 * navegación permanente en la columna izquierda.
 *
 * Usa tokens --es-* (los del portfolio), no --re-*: es la única franja de la
 * página que pertenece al portfolio y no al Design System. Los textos salen de
 * es_ds_text(), que resuelve por el idioma del propio documento y no depende de
 * que haya una traducción cargada a mano en Polylang.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="es-ds-topbar">
	<div class="es-ds-topbar__in">
		<a class="es-ds-topbar__back" href="<?php echo esc_url( es_ds_restimator_back_url() ); ?>">
			<span class="es-ds-topbar__arrow" aria-hidden="true">&larr;</span>
			<?php echo esc_html( es_ds_text( 'back_to_case' ) ); ?>
		</a>
		<a class="es-ds-topbar__owner" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html( es_ds_text( 'owner' ) ); ?>
		</a>
	</div>
</div>
