<?php
/**
 * Fuente de datos de Featured Case + fallback placeholder.
 *
 * Mismo patrón que inc/selected-work-fallback.php: el Case Study marcado
 * "Feature this case on Home" vive en el plugin "Estavillo Portfolio
 * Core" — el tema no lo consulta directamente. El único puente es el
 * filtro 'es_portfolio_featured_case_for_home': el tema lo dispara con su
 * propio fallback como default, y si el plugin está activo y tiene un caso
 * marcado como featured, agrega su callback y devuelve esos datos.
 *
 * Sin llamadas directas a funciones del plugin, así que si el plugin está
 * inactivo (o no instalado) esto cae directo al fallback — cero riesgo de
 * fatal error por función no definida.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Caso placeholder de Featured Case (el mismo de siempre, copy del Home
 * v4) usado cuando no hay ningún Case Study marcado "featured" todavía, o
 * cuando el plugin "Estavillo Portfolio Core" está inactivo.
 *
 * @return array
 */
function es_home_featured_fallback_case() {
	return array(
		'kicker'       => 'Product + System Design · In progress',
		'title'        => 'A decision system for <em>metal workshop budgeting.</em>',
		'body'         => "A metal fabrication workshop that quotes well — but slowly, and from one person's head. Estimates queue up, and hours go into requests that end up not closing. I'm designing a system that turns that tacit knowledge into explicit, versioned criteria: consistent, transferable, and able to answer early with an orientative range before hours are committed.",
		'source'       => 'Developed and implemented at Guzmán Villalba — metal fabrication workshop, Montevideo.',
		'status'       => 'In progress',
		'url'          => '#',
		'image'        => null,
		// Featured Media (ticket "Featured media del Case Study"): el
		// placeholder siempre es imagen/standard — mismo shape que el caso
		// real (es_case_get_featured_media() en el plugin) para que
		// inc/featured-media.php no tenga que distinguir origen real vs.
		// fallback.
		'media_type'   => 'image',
		'layout'       => 'standard',
		'object_fit'   => 'cover',
		'focal'        => 'center',
		'video_url'    => '',
		'video_poster' => '',
	);
}

/**
 * Fuente de datos para 'es_home_featured' (featured-case.php): el Case
 * Study marcado como featured del plugin si existe, si no el placeholder
 * de siempre. Se pasa por apply_filters() en el template, así el filtro
 * documentado en el README sigue funcionando igual (puede seguir
 * sobreescribiendo el resultado final).
 *
 * @return array
 */
function es_home_featured_source() {
	$case = apply_filters( 'es_portfolio_featured_case_for_home', array() );
	return $case ? $case : es_home_featured_fallback_case();
}
