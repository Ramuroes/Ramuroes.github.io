<?php
/**
 * Librería de bloques Gutenberg "Estavillo Case Study".
 *
 * Sprint Gutenberg. 12 bloques dinámicos (render.php server-side, save
 * nulo o solo InnerBlocks.Content) registrados vía block.json, uno por
 * carpeta en blocks/. case-section es un contenedor de capítulo flexible
 * (InnerBlocks libre — composición interna vía Columns/Column nativos de
 * Gutenberg). case-split-content/case-split-media son LEGACY: quedaron de
 * una corrección de arquitectura anterior (regiones fijas con
 * templateLock, revertida por ser demasiado rígida en uso real) — siguen
 * registrados (parent + inserter:false: nunca se insertan a mano) solo
 * para que los posts ya guardados con esos bloques sigan renderizando sin
 * error; el contenido nuevo no los usa. La presentación frontend reusa 1:1
 * la librería de clases
 * .es-case-* de case-study.css del CHILD THEME — el plugin no duplica esos
 * estilos: en el front los sirve el theme (solo carga en el single del CPT),
 * y en el editor este archivo puentea las mismas hojas del theme hacia el
 * iframe del editor (ver es_case_blocks_editor_theme_css()).
 *
 * Decisiones:
 * - Bloques dinámicos (no estáticos): los atributos viven en el comentario
 *   del bloque y el markup se genera SIEMPRE server-side — cero riesgo de
 *   invalidación de bloque al evolucionar el markup, y todo el output pasa
 *   por esc_*()/wp_kses_post().
 * - JS del editor SIN build step: archivos planos con wp.element.createElement
 *   (nada de JSX), dependencias declaradas al registrar el handle. El zip del
 *   plugin es exactamente lo que hay en el repo.
 * - Cero JS en el frontend: ningún bloque lo necesita (case-details usa
 *   <details>/<summary> nativos).
 * - Sin ACF, sin librerías remotas.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lista canónica de los bloques de la librería (slug de carpeta).
 *
 * @return string[]
 */
function es_case_blocks_list() {
	return array(
		'case-section',
		'case-split-content',
		'case-split-media',
		'case-figure',
		'case-stats',
		'case-ladder',
		'case-taxonomy',
		'case-timeline',
		'case-decisions',
		'case-status',
		'case-quote',
		'case-details',
		'case-flow',
		'case-flow-v2',
		'case-findings-list',
	);
}

/**
 * Registra la categoría "Estavillo Case Study" en el inserter.
 *
 * @param array $categories Categorías existentes.
 * @return array
 */
function es_case_blocks_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'estavillo-case',
			'title' => __( 'Estavillo Case Study', 'estavillo-portfolio-core' ),
			'icon'  => null,
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'es_case_blocks_category' );

/**
 * Registra scripts/estilos del editor y los 10 bloques.
 */
function es_case_blocks_register() {
	$v = ES_PORTFOLIO_CORE_VERSION;

	// Helpers compartidos del editor (listas repetibles, toolbar de ítem,
	// scope visual). Cada edit.js lo declara como dependencia.
	wp_register_script(
		'es-case-blocks-ui',
		ES_PORTFOLIO_CORE_URI . 'assets/js/case-blocks-ui.js',
		array( 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n' ),
		$v,
		true
	);

	// Estilos de soporte SOLO del editor (scope oscuro por bloque, spacing de
	// controles). En el front no se encola nunca: ahí manda el theme.
	wp_register_style(
		'es-case-blocks-editor',
		ES_PORTFOLIO_CORE_URI . 'assets/css/case-blocks-editor.css',
		array(),
		$v
	);

	foreach ( es_case_blocks_list() as $block ) {
		$handle = 'es-case-block-' . $block;
		wp_register_script(
			$handle,
			ES_PORTFOLIO_CORE_URI . 'blocks/' . $block . '/edit.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'es-case-blocks-ui' ),
			$v,
			true
		);
		register_block_type( ES_PORTFOLIO_CORE_DIR . 'blocks/' . $block );
	}
}
add_action( 'init', 'es_case_blocks_register' );

/**
 * Puente de estilos del theme hacia el editor: dentro del iframe del editor
 * de bloques se cargan los tokens (--es-*) y la librería .es-case-* del
 * child theme, así el preview de cada bloque se ve como el frontend real.
 * Cada edit.js envuelve su preview en un div .es-case__body (el mismo scope
 * que usa el single del CPT), de modo que los selectores del theme matchean
 * sin duplicar CSS en el plugin.
 *
 * enqueue_block_assets corre tanto en el front como en el editor; el guard
 * is_admin() limita esto al editor. Si el child theme no está activo (los
 * archivos no existen), simplemente no se encola nada y el bloque cae al
 * estilo estructural mínimo de case-blocks-editor.css.
 */
function es_case_blocks_editor_theme_css() {
	if ( ! is_admin() ) {
		return;
	}

	$theme_css = array(
		'es-editor-tokens'     => 'assets/css/tokens.css',
		'es-editor-case-study' => 'assets/css/case-study.css',
		'es-editor-case-flow'  => 'assets/css/case-flow.css',
		// La v2 son sólo los deltas de geometría: DEPENDE de que case-flow.css
		// ya esté encolado, y este array conserva el orden de inserción.
		'es-editor-case-flow-v2' => 'assets/css/case-flow-v2.css',
	);

	foreach ( $theme_css as $handle => $rel ) {
		$path = get_stylesheet_directory() . '/' . $rel;
		if ( file_exists( $path ) ) {
			wp_enqueue_style(
				$handle,
				get_stylesheet_directory_uri() . '/' . $rel,
				array(),
				(string) filemtime( $path )
			);
		}
	}

	wp_enqueue_style( 'es-case-blocks-editor' );
}
add_action( 'enqueue_block_assets', 'es_case_blocks_editor_theme_css' );
