<?php
/**
 * Librería de bloques Gutenberg "Estavillo — About" — bloques de contenido
 * de página reusables fuera del Case Study CPT (arranca con About; Home/
 * How I Work/Connect podrán sumar bloques acá a futuro sin tocar
 * case-blocks.php, que queda scopeado a Case Study).
 *
 * Mismas convenciones que case-blocks.php: bloque dinámico (render.php
 * server-side, save() nulo), JS del editor sin build step (wp.element.
 * createElement, reusa los helpers compartidos de case-blocks-ui.js), sin
 * ACF ni librerías remotas. La presentación frontend reusa 1:1 la clase
 * .es-hobbies__list/.es-hobby-item de assets/css/pages.css del CHILD
 * THEME — el plugin no duplica ese CSS.
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
function es_about_blocks_list() {
	return array(
		'hobby-list',
	);
}

/**
 * Registra la categoría "Estavillo — About" en el inserter.
 *
 * @param array $categories Categorías existentes.
 * @return array
 */
function es_about_blocks_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'estavillo-about',
			'title' => __( 'Estavillo — About', 'estavillo-portfolio-core' ),
			'icon'  => null,
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'es_about_blocks_category' );

/**
 * Registra scripts y los bloques de esta librería. Depende del handle
 * 'es-case-blocks-ui' registrado por case-blocks.php (basta con declararlo
 * como dependencia del script: wp_register_script no exige que ya exista,
 * solo que esté registrado antes de encolarse — y ambos archivos corren en
 * 'init', antes de cualquier encolado real).
 */
function es_about_blocks_register() {
	$v = ES_PORTFOLIO_CORE_VERSION;

	foreach ( es_about_blocks_list() as $block ) {
		$handle = 'es-about-block-' . $block;
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
add_action( 'init', 'es_about_blocks_register' );

/**
 * Puente de estilos del theme hacia el editor para esta librería: a
 * diferencia de case-blocks.php (que puentea case-study.css), estos
 * bloques usan clases de assets/css/pages.css — tokens.css ya lo puentea
 * es_case_blocks_editor_theme_css(), así que acá solo falta pages.css.
 * Mismo guard is_admin(); si el child theme no está activo, no se encola
 * nada y el bloque cae al estilo estructural mínimo del navegador.
 */
function es_about_blocks_editor_theme_css() {
	if ( ! is_admin() ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/pages.css';
	if ( file_exists( $path ) ) {
		wp_enqueue_style(
			'es-editor-pages',
			get_stylesheet_directory_uri() . '/assets/css/pages.css',
			array(),
			(string) filemtime( $path )
		);
	}
}
add_action( 'enqueue_block_assets', 'es_about_blocks_editor_theme_css' );
