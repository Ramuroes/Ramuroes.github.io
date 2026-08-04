<?php
/**
 * Librería de bloques Gutenberg "Estavillo — How I Work".
 *
 * Arranca con un único bloque: estavillo/how-i-work-illustration. Mismas
 * convenciones que case-blocks.php/about-blocks.php (bloque dinámico,
 * render.php server-side, save() nulo, JS del editor sin build step) con
 * una diferencia: el preview del editor usa ServerSideRender (paquete
 * núcleo de Gutenberg) en vez del patrón "caja vacía + <select>" de
 * hobby-list — acá vale la pena el render real porque cada bloque muestra
 * una de seis ilustraciones grandes distintas, no un ícono chico de 8
 * opciones.
 *
 * Categoría de bloque separada de 'estavillo-case'/'estavillo-about':
 * reusa el nombre "Estavillo — Pages" ya usado por la categoría de
 * PATTERNS en how-i-work-patterns.php (registro de bloques vs. registro de
 * patterns son dos listas separadas en Gutenberg — mismo slug/label en
 * ambas no colisiona, y mantiene la agrupación visual consistente para
 * contenido de página fija).
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lista canónica de los bloques de esta librería (slug de carpeta).
 *
 * @return string[]
 */
function es_how_work_blocks_list() {
	return array(
		'how-i-work-illustration',
	);
}

/**
 * Registra la categoría de bloque "Estavillo — Pages" en el inserter (si
 * about-blocks.php/case-blocks.php ya no la registraron con este slug —
 * no es el caso hoy, pero array_unshift duplicado no rompe nada, WP
 * deduplica por slug).
 *
 * @param array $categories Categorías existentes.
 * @return array
 */
function es_how_work_blocks_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'estavillo-pages',
			'title' => __( 'Estavillo — Pages', 'estavillo-portfolio-core' ),
			'icon'  => null,
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'es_how_work_blocks_category' );

/**
 * Registra el script del editor (con wp-server-side-render como
 * dependencia, para el preview real) y el bloque.
 */
function es_how_work_blocks_register() {
	$v = ES_PORTFOLIO_CORE_VERSION;

	foreach ( es_how_work_blocks_list() as $block ) {
		$handle = 'es-pages-block-' . $block;
		wp_register_script(
			$handle,
			ES_PORTFOLIO_CORE_URI . 'blocks/' . $block . '/edit.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render', 'es-case-blocks-ui' ),
			$v,
			true
		);
		register_block_type( ES_PORTFOLIO_CORE_DIR . 'blocks/' . $block );
	}
}
add_action( 'init', 'es_how_work_blocks_register' );

/**
 * Puente de estilos del theme hacia el editor para esta librería: el
 * bloque se apoya en pages-home.css (mapeo de tokens + accent toggle,
 * compartido con el teaser de Home) y pages.css (tamaño/posición dentro
 * de un process-move). tokens.css ya lo puentea
 * es_case_blocks_editor_theme_css(); pages.css ya lo puentea
 * es_about_blocks_editor_theme_css() (ambas corren siempre en el admin,
 * sin condicionar a qué bloque hay en pantalla) — acá solo falta
 * pages-home.css.
 */
function es_how_work_blocks_editor_theme_css() {
	if ( ! is_admin() ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/pages-home.css';
	if ( file_exists( $path ) ) {
		wp_enqueue_style(
			'es-editor-pages-home',
			get_stylesheet_directory_uri() . '/assets/css/pages-home.css',
			array(),
			(string) filemtime( $path )
		);
	}
}
add_action( 'enqueue_block_assets', 'es_how_work_blocks_editor_theme_css' );
