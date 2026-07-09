<?php
/**
 * Fuente de datos de Selected Work + fallback placeholder.
 *
 * El Case Study CPT (registro, meta box, queries) vive en el plugin
 * "Estavillo Portfolio Core" (Sprint 3 extraction) — el tema ya no lo
 * registra directamente. El único puente entre plugin y tema es el filtro
 * 'es_portfolio_case_studies_for_home': el tema lo dispara con su propio
 * fallback como default, y si el plugin está activo agrega su callback y
 * devuelve Case Studies reales cuando existen.
 *
 * Sin llamadas directas a funciones del plugin, así que si el plugin está
 * inactivo (o no instalado) esto simplemente no tiene ningún callback
 * enganchado y cae directo al fallback — cero riesgo de fatal error por
 * función no definida.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Casos placeholder (los mismos 3 de siempre) usados cuando no hay ningún
 * Case Study publicado todavía, o cuando el plugin "Estavillo Portfolio
 * Core" está inactivo — la Home nunca queda vacía.
 *
 * @return array<int,array>
 */
function es_home_selected_work_fallback_cases() {
	return array(
		array(
			'label'   => 'Case 02',
			'kicker'  => 'Thesis · Digital education',
			'title'   => 'Trazur',
			'excerpt' => 'Degree thesis turned case study: research-driven redesign of an e-learning product — UX research, service design and applied AI in digital education.',
			'tags'    => array( 'UX Research', 'Service Design', 'Applied AI', 'E-learning' ),
			'url'     => '#',
			'image'   => null,
		),
		array(
			'label'   => 'Case 03',
			'kicker'  => 'Academic UX Case · Google UX Design Certificate',
			'title'   => 'French Bakery',
			'excerpt' => 'End-to-end UX process — research, wireframes, usability testing — from the Google UX Design Professional Certificate.',
			'tags'    => array(),
			'url'     => '#',
			'image'   => null,
		),
		array(
			'label'   => 'Case 04',
			'kicker'  => 'Legacy · Industrial e-commerce',
			'title'   => 'Samic',
			'excerpt' => 'E-commerce and visual systems for an industrial parts distributor.',
			'tags'    => array(),
			'url'     => '#',
			'image'   => null,
		),
	);
}

/**
 * Fuente de datos para 'es_home_selected_work' (selected-work.php): Case
 * Studies reales del plugin si existe y tiene contenido, si no los
 * placeholders de siempre. Se pasa por apply_filters() en el template, así
 * el filtro documentado en el README sigue funcionando igual (puede seguir
 * sobreescribiendo el resultado final).
 *
 * @return array<int,array>
 */
function es_home_selected_work_source() {
	$cases = apply_filters( 'es_portfolio_case_studies_for_home', array() );
	return $cases ? $cases : es_home_selected_work_fallback_cases();
}
