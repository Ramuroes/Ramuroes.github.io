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
 * ES STICKY, igual que el header institucional. Antes no lo era, con el
 * argumento de que el rail ya da navegación permanente — pero el rail navega
 * DENTRO del documento y esta barra es la única salida HACIA AFUERA: si se va
 * con el scroll, a mitad del documento no queda ninguna. Al ser sticky también
 * deja de aparecer y desaparecer, que es lo que la hacía leer como una barra de
 * debug pegada arriba de todo.
 *
 * Geometría: la MISMA grilla del documento —[columna del rail][área principal]—
 * así el "volver" cae sobre la marca del rail y la atribución sobre el borde
 * derecho del contenido. Con dos barras distintas alineadas a dos grillas
 * distintas, la página se leía como dos sitios pegados.
 *
 * Usa tokens --re-* (los del Design System) y no --es-*: es la barra de ESTA
 * página y tiene que pertenecer visualmente al documento que encabeza. Los
 * textos salen de es_ds_text(), que resuelve por el idioma del propio documento
 * y no depende de que haya una traducción cargada a mano en Polylang.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="es-ds-topbar">
	<nav class="es-ds-topbar__in" aria-label="<?php echo esc_attr( es_ds_text( 'nav_aria' ) ); ?>">
		<a class="es-ds-topbar__back" href="<?php echo esc_url( es_ds_restimator_back_url() ); ?>">
			<span class="es-ds-topbar__arrow" aria-hidden="true">&larr;</span>
			<span class="es-ds-topbar__back-text"><?php echo esc_html( es_ds_text( 'back_to_case' ) ); ?></span>
		</a>
		<a class="es-ds-topbar__owner" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html( es_ds_text( 'owner' ) ); ?>
		</a>
	</nav>
</div>
