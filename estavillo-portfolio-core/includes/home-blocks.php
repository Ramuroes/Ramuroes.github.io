<?php
/**
 * Librería de bloques Gutenberg "Estavillo — Home" — Home migration ticket.
 *
 * Cinco bloques, mismas convenciones que how-i-work-blocks.php/
 * about-blocks.php (bloque dinámico, render.php server-side, JS del
 * editor sin build step, ServerSideRender para el preview real donde
 * aplica). Cuatro son hoja sin atributos: "qué caso se muestra" ya es
 * 100% resuelto por el CPT Case Study (casillas "Feature this case on
 * Home" / "Show in Home → Selected Work" + el campo nativo "Order",
 * includes/case-study-cpt.php) y el retrato del About teaser sale del
 * campo compartido con la página About — estos bloques son solo el puente
 * Gutenberg hacia ese dato, delegando en las mismas funciones del child
 * theme que ya usaban los template-parts de Home, así el fallback
 * placeholder y el markup nunca se duplican. El quinto (home-hero) es el
 * híbrido tipo case-section: copy = InnerBlocks editable en post_content,
 * cascarón (fondo animado + variantes del Customizer) = render.php.
 *
 * Reusa la categoría "Estavillo — Pages" ya registrada por
 * how-i-work-blocks.php (mismo slug 'estavillo-pages' — no hace falta
 * re-registrarla, WP dedupe por slug de cualquier forma).
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
function es_home_blocks_list() {
	return array(
		'home-hero',
		'featured-case',
		'selected-work',
		'about-teaser-text',
		'about-teaser-portrait',
	);
}

/**
 * Registra los scripts del editor (con wp-server-side-render como
 * dependencia, para el preview real) y los bloques.
 */
function es_home_blocks_register() {
	$v = ES_PORTFOLIO_CORE_VERSION;

	foreach ( es_home_blocks_list() as $block ) {
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
add_action( 'init', 'es_home_blocks_register' );

/**
 * Puente de estilos del theme hacia el editor para esta librería: ambos
 * bloques se apoyan en pages-home.css (.es-featured__*, .es-work__*,
 * .es-card*, .es-grid--2). tokens.css ya lo puentea
 * es_case_blocks_editor_theme_css(); pages-home.css todavía no lo puenteaba
 * ningún archivo (about-blocks.php puentea pages.css, how-i-work-blocks.php
 * puentea pages-home.css también — declarar el mismo enqueue acá de nuevo
 * es inofensivo, wp_enqueue_style() no duplica salida por handle repetido).
 */
function es_home_blocks_editor_theme_css() {
	if ( ! is_admin() ) {
		return;
	}

	// base.css (tipografía es-h1/es-lead) y hero.css (copy del hero) se
	// suman por el bloque home-hero — el copy del hero en el editor usa
	// esas clases y sin ellas se vería como texto plano sin jerarquía.
	$es_editor_css = array(
		'es-editor-base'       => 'assets/css/base.css',
		'es-editor-hero'       => 'assets/css/hero.css',
		'es-editor-pages-home' => 'assets/css/pages-home.css',
	);

	foreach ( $es_editor_css as $es_handle => $es_rel ) {
		$path = get_stylesheet_directory() . '/' . $es_rel;
		if ( file_exists( $path ) ) {
			wp_enqueue_style(
				$es_handle,
				get_stylesheet_directory_uri() . '/' . $es_rel,
				array(),
				(string) filemtime( $path )
			);
		}
	}
}
add_action( 'enqueue_block_assets', 'es_home_blocks_editor_theme_css' );
