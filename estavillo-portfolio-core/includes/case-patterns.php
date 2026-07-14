<?php
/**
 * Patterns de Case Study — "Estavillo — Presupuestador Case Structure".
 *
 * Dos patterns (ES y EN), uno por idioma del contenido maestro
 * (docs/content/presupuestador-case-study-{es,en}.html). Decisión de
 * idioma: dos patterns siempre visibles en vez de detección automática de
 * idioma — Polylang define el idioma del POST, no del insertador de
 * patterns, y atar el pattern al idioma detectado escondería el que falta
 * cuando se está armando la traducción. El título de cada pattern dice
 * (ES)/(EN); elegir el que corresponde al idioma del post.
 *
 * El contenido reproduce las 13 secciones del caso con los bloques
 * estavillo/* + core (paragraph/list), mismos anchors, mismos textos
 * honestos y mismos placeholders [DATO PENDIENTE DE VALIDACIÓN — …] /
 * {asset: …} que los archivos maestros. Diferencia deliberada con el HTML
 * manual: las grillas de dos columnas (.es-case-cols) del maestro se
 * reemplazan por flujo secuencial texto → figura — más editable en
 * Gutenberg y idéntico en mobile.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra la categoría de patterns y los dos patterns Presupuestador.
 */
function es_case_patterns_register() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'estavillo-case',
		array( 'label' => __( 'Estavillo Case Study', 'estavillo-portfolio-core' ) )
	);

	register_block_pattern(
		'estavillo/presupuestador-case-es',
		array(
			'title'       => __( 'Estavillo — Presupuestador Case Structure (ES)', 'estavillo-portfolio-core' ),
			'description' => __( 'Las 13 secciones del caso Presupuestador en español, armadas con los bloques Estavillo Case Study. Anchors compatibles con el Case Index recomendado en docs/content/presupuestador-case-study-fields.md.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-case' ),
			'postTypes'   => array( 'es_case_study' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/presupuestador-es.php',
		)
	);

	register_block_pattern(
		'estavillo/presupuestador-case-en',
		array(
			'title'       => __( 'Estavillo — Presupuestador Case Structure (EN)', 'estavillo-portfolio-core' ),
			'description' => __( 'The 13 sections of the Presupuestador case in English, built with the Estavillo Case Study blocks. Anchors match the Case Index recommended in docs/content/presupuestador-case-study-fields.md.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-case' ),
			'postTypes'   => array( 'es_case_study' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/presupuestador-en.php',
		)
	);
}
add_action( 'init', 'es_case_patterns_register', 20 );
