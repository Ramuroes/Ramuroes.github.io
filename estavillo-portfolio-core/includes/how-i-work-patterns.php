<?php
/**
 * Patterns de página — "Estavillo — Pages" (arranca con How I Work).
 *
 * Categoría separada de 'estavillo-case' (case-patterns.php, scopeada a
 * es_case_study) — estos patterns son para contenido de página fija
 * (How I Work hoy; Connect/Home más adelante podrían sumar acá sin tocar
 * case-patterns.php), por eso postTypes => array('page').
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra la categoría de patterns de página y el pattern de How I Work.
 */
function es_page_patterns_register() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'estavillo-pages',
		array( 'label' => __( 'Estavillo — Pages', 'estavillo-portfolio-core' ) )
	);

	register_block_pattern(
		'estavillo/how-i-work-move',
		array(
			'title'       => __( 'How I Work — Move', 'estavillo-portfolio-core' ),
			'description' => __( 'Un "process move" completo: número + título, ilustración editable (core/image via Media Library), descripción corta, y las 4 secciones de detalle expandible (Why it matters / How I approach it / AI and human judgment / In practice) dentro de estavillo/case-details. Copy 100% placeholder — ver docs/HOW-I-WORK-CONTENT-SPEC.md para el copy final ya integrado en docs/content/how-i-work-gutenberg-en.html.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-pages' ),
			'postTypes'   => array( 'page' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/how-i-work-move.php',
		)
	);
}
add_action( 'init', 'es_page_patterns_register', 20 );
