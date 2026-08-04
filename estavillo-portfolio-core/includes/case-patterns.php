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

	// Sistema editorial v2 (spec "Grid System v1"): una página de
	// laboratorio con los cinco layouts en el orden canónico + un arranque
	// corto para casos nuevos. Copy ficticio/andamiaje — nunca contenido
	// real de un caso.
	register_block_pattern(
		'estavillo/case-editorial-demo',
		array(
			'title'       => __( 'Case Study — Editorial System Demo', 'estavillo-portfolio-core' ),
			'description' => __( 'Demo del sistema editorial: Reading, Split 5/7, Wide (figura), Wide (Stats+Ladder), Split 7/5, Split balanceado 6/6 y cierre en Reading con cita y detalle. Contenido 100% ficticio para explorar los presets.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-case' ),
			'postTypes'   => array( 'es_case_study' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/editorial-demo.php',
		)
	);

	register_block_pattern(
		'estavillo/case-canonical-starter',
		array(
			'title'       => __( 'Case Study — Canonical Starter', 'estavillo-portfolio-core' ),
			'description' => __( 'Arranque limpio para un caso nuevo: Reading → Split → Wide → Reading de cierre, con copy de andamiaje entre {llaves} para reemplazar.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-case' ),
			'postTypes'   => array( 'es_case_study' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/canonical-starter.php',
		)
	);

	// Phase 0 of the second-pass architecture review (Trazur artifact audit,
	// see estavillo-child/README.md, "Patterns Phase 0"): three reusable
	// compositions built ONLY from
	// native Gutenberg blocks + existing estavillo/* Case Study blocks —
	// no new block.json/render.php/edit.js anywhere. Patterns, not blocks,
	// on purpose: editors can freely ungroup/move/replace anything inside
	// them, and none of the three shapes has been validated by more than
	// one real case yet (priority: build new blocks only after multiple
	// projects prove the same structure repeats).
	register_block_pattern(
		'estavillo/case-persona',
		array(
			'title'       => __( 'Case Study — Persona', 'estavillo-portfolio-core' ),
			'description' => __( 'Persona card: photo (Case Figure), name/role/demographics, biography, goals/frustrations in two columns, and a pull-quote (Case Quote). Fictional placeholder copy — replace before publishing.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-case' ),
			'postTypes'   => array( 'es_case_study' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/case-persona.php',
		)
	);

	register_block_pattern(
		'estavillo/case-comparison-table',
		array(
			'title'       => __( 'Case Study — Comparison Table', 'estavillo-portfolio-core' ),
			'description' => __( 'A Case Section (eyebrow/heading/lead) with a native Table (header row, 4 generic columns, 3 placeholder rows) and a caption below. Generic enough for tool comparisons, heuristic evaluations, before/after, or research findings — add/remove rows and columns with the Table block\'s own controls.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-case' ),
			'postTypes'   => array( 'es_case_study' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/case-comparison-table.php',
		)
	);

	register_block_pattern(
		'estavillo/case-callout-panel',
		array(
			'title'       => __( 'Case Study — Callout Panel', 'estavillo-portfolio-core' ),
			'description' => __( 'A tinted panel (native Group, .es-case-callout — existing tokens only, no new colors) with an eyebrow, heading, short paragraph and an optional list. For context notes, key learnings, warnings, design principles or limitations.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-case' ),
			'postTypes'   => array( 'es_case_study' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/case-callout-panel.php',
		)
	);
}
add_action( 'init', 'es_case_patterns_register', 20 );
