<?php
/**
 * Compatibilidad con Polylang.
 *
 * Registra el CPT Case Study como traducible vía el filtro nativo de
 * Polylang 'pll_get_post_types' — el mismo mecanismo que cualquier plugin
 * usa para integrarse. La taxonomía es_case_tag queda deliberadamente
 * AFUERA de este registro: decisión V1 aprobada — tags language-neutral,
 * compartidos entre EN/ES (ver docs/EDITABILITY-PLAN.md, "Polylang"). Si
 * más adelante se decide lo contrario, sumar 'es_case_tag' acá mismo vía
 * 'pll_get_taxonomies' es todo lo que hace falta.
 *
 * Todo esto es inerte si Polylang no está instalado: add_filter() sobre
 * un hook que nadie dispara nunca no hace nada — no hace falta ningún
 * function_exists() acá. La única llamada DIRECTA a una función de la API
 * de Polylang (pll_the_languages) vive en el tema
 * (template-parts/site-header.php), guardada con function_exists() ahí.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Suma el CPT Case Study a la lista de post types traducibles de Polylang.
 *
 * @param array $post_types Post types ya traducibles (post, page, ...).
 * @return array
 */
function es_portfolio_pll_post_types( $post_types ) {
	$post_types[ ES_CASE_STUDY_CPT ] = ES_CASE_STUDY_CPT;
	return $post_types;
}
add_filter( 'pll_get_post_types', 'es_portfolio_pll_post_types' );
