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
 * Placeholder de Featured Work (ticket "Refine Work archive hierarchy") —
 * usado cuando no hay ningún Case Study marcado featured, o el plugin está
 * inactivo. Adapta el MISMO placeholder de siempre de Home
 * (es_home_featured_fallback_case(), inc/featured-case-fallback.php) al
 * shape de card ancha (label/kicker/title/excerpt/tags/url/image) que usa
 * esta página — no es copy nuevo, es el mismo texto aprobado, re-formado.
 *
 * El título de Home trae <em> para el énfasis tipográfico de esa sección
 * (.es-featured__title); acá se imprime con esc_html() como cualquier otro
 * card de esta página, así que hace falta texto plano — de ahí el
 * wp_strip_all_tags().
 *
 * @return array
 */
function es_work_featured_fallback_case() {
	$es_case = es_home_featured_fallback_case();
	return array(
		'label'             => $es_case['status'],
		'kicker'            => $es_case['kicker'],
		'title'             => wp_strip_all_tags( $es_case['title'] ),
		'excerpt'           => $es_case['body'],
		'tags'              => array(),
		'category'          => '',
		'url'               => $es_case['url'],
		'image'             => $es_case['image'],
		'placeholder_label' => '',
		// Featured Media: mismos defaults que el placeholder de Home — ver
		// el comentario en es_home_featured_fallback_case().
		'media_type'        => $es_case['media_type'],
		'layout'            => $es_case['layout'],
		'object_fit'        => $es_case['object_fit'],
		'focal'             => $es_case['focal'],
		'video_url'         => $es_case['video_url'],
		'video_poster'      => $es_case['video_poster'],
	);
}

/**
 * Grupos placeholder de Work (featured + los mismos 3 casos de siempre
 * como "selected", sin archivo todavía) usados cuando no hay ningún Case
 * Study publicado, o cuando el plugin "Estavillo Portfolio Core" está
 * inactivo.
 *
 * @return array{featured:array,selected:array<int,array>,archive:array<int,array>}
 */
function es_work_page_fallback_cases() {
	return array(
		'featured' => es_work_featured_fallback_case(),
		'selected' => es_home_selected_work_fallback_cases(),
		'archive'  => array(),
	);
}

/**
 * Fuente de datos para template-parts/work-cases.php: Case Studies reales
 * del plugin (featured/selected/archive) si existen y el plugin está
 * activo, si no los placeholders de siempre.
 *
 * @return array{featured:array,selected:array<int,array>,archive:array<int,array>}
 */
function es_work_page_source() {
	$data = apply_filters( 'es_portfolio_case_studies_for_work', array() );
	if ( empty( $data['featured'] ) && empty( $data['selected'] ) && empty( $data['archive'] ) ) {
		return es_work_page_fallback_cases();
	}
	return array(
		'featured' => $data['featured'] ?? array(),
		'selected' => $data['selected'] ?? array(),
		'archive'  => $data['archive'] ?? array(),
	);
}
