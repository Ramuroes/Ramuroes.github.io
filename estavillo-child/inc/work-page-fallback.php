<?php
/**
 * Fuente de datos de la página Work (templates/page-work.php) + fallback
 * placeholder.
 *
 * Mismo patrón que inc/selected-work-fallback.php: el listado real vive en
 * el plugin "Estavillo Portfolio Core" — el tema no lo consulta
 * directamente. El único puente es el filtro
 * 'es_portfolio_case_studies_for_work': el tema lo dispara con su propio
 * fallback como default, y si el plugin está activo agrega su callback y
 * devuelve los dos grupos reales ('selected' y 'archive') cuando hay Case
 * Studies publicados.
 *
 * Sin llamadas directas a funciones del plugin, así que si el plugin está
 * inactivo (o no instalado) esto cae directo al fallback — cero riesgo de
 * fatal error por función no definida, misma garantía que el resto del
 * tema ("Home nunca se rompe", extendida acá a "Work nunca se rompe").
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Grupos placeholder de Work (mismos 3 casos de siempre como "selected",
 * sin archivo todavía) usados cuando no hay ningún Case Study publicado, o
 * cuando el plugin "Estavillo Portfolio Core" está inactivo.
 *
 * @return array{selected:array<int,array>,archive:array<int,array>}
 */
function es_work_page_fallback_cases() {
	return array(
		'selected' => es_home_selected_work_fallback_cases(),
		'archive'  => array(),
	);
}

/**
 * Fuente de datos para template-parts/work-cases.php: Case Studies reales
 * del plugin (separados selected/archive) si existen y el plugin está
 * activo, si no los placeholders de siempre.
 *
 * @return array{selected:array<int,array>,archive:array<int,array>}
 */
function es_work_page_source() {
	$data = apply_filters( 'es_portfolio_case_studies_for_work', array() );
	if ( empty( $data['selected'] ) && empty( $data['archive'] ) ) {
		return es_work_page_fallback_cases();
	}
	return array(
		'selected' => $data['selected'] ?? array(),
		'archive'  => $data['archive'] ?? array(),
	);
}
