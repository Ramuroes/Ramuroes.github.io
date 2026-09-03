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
		'tools',
	);
}

/**
 * Registra la categoría en el inserter.
 *
 * Título "Estavillo — Content" (antes "Estavillo — About"): esta librería
 * ya nacía pensada para crecer más allá de About — ver el docblock de este
 * archivo ("Home/How I Work/Connect podrán sumar bloques acá a futuro sin
 * tocar case-blocks.php"). El bloque Tools es el primer caso real de eso
 * (Design System ticket: reutilizable en Home/About/Case Studies/landings),
 * así que el título deja de nombrar una sola página. Mismo slug de siempre
 * ('estavillo-about'): las categorías del inserter son sólo agrupación
 * visual en el editor, no un dato guardado por bloque — renombrar el
 * título no afecta ningún contenido ya publicado.
 *
 * @param array $categories Categorías existentes.
 * @return array
 */
function es_about_blocks_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'estavillo-about',
			'title' => __( 'Estavillo — Content', 'estavillo-portfolio-core' ),
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
 * bloques usan clases de assets/css/pages.css y, desde el bloque Tools,
 * también assets/css/tools.css (Design System — vive suelto del resto de
 * páginas, ver inc/enqueue.php del theme) — tokens.css ya lo puentea
 * es_case_blocks_editor_theme_css(), así que acá sólo faltan esas dos.
 * Mismo guard is_admin(); si el child theme no está activo, no se encola
 * nada y el bloque cae al estilo estructural mínimo del navegador.
 */
function es_about_blocks_editor_theme_css() {
	if ( ! is_admin() ) {
		return;
	}

	$es_sheets = array(
		'es-editor-pages' => 'assets/css/pages.css',
		'es-editor-tools' => 'assets/css/tools.css',
	);

	foreach ( $es_sheets as $es_handle => $es_rel ) {
		$es_path = get_stylesheet_directory() . '/' . $es_rel;
		if ( file_exists( $es_path ) ) {
			wp_enqueue_style(
				$es_handle,
				get_stylesheet_directory_uri() . '/' . $es_rel,
				array(),
				(string) filemtime( $es_path )
			);
		}
	}
}
add_action( 'enqueue_block_assets', 'es_about_blocks_editor_theme_css' );
