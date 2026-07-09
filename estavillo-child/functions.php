<?php
/**
 * Estavillo Child — funciones del tema.
 *
 * Foundation técnica del nuevo portfolio ESTAVILLO sobre Kadence.
 * Todo lo específico vive en inc/ para mantener este archivo mínimo.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ES_CHILD_VERSION', '0.2.0' );
define( 'ES_CHILD_DIR', get_stylesheet_directory() );
define( 'ES_CHILD_URI', get_stylesheet_directory_uri() );

require ES_CHILD_DIR . '/inc/enqueue.php';
require ES_CHILD_DIR . '/inc/theme-options.php';
require ES_CHILD_DIR . '/inc/selected-work-fallback.php';
require ES_CHILD_DIR . '/inc/featured-case-fallback.php';
require ES_CHILD_DIR . '/inc/work-page-fallback.php';

/**
 * Textdomain del child theme.
 */
function es_child_setup() {
	load_child_theme_textdomain( 'estavillo-child', ES_CHILD_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'es_child_setup' );

/* -------------------------------------------------------------------------
 * Compatibilidad Polylang
 * -------------------------------------------------------------------------
 * Los strings de interfaz del tema (labels de secciones, CTAs placeholder)
 * se registran en Polylang cuando el plugin está activo, y es__() los
 * resuelve con pll__() si existe. Sin Polylang, cae en __() estándar.
 * El copy final NO se hardcodea acá: estas son cadenas provisionales
 * marcadas como editables en los template parts.
 * ---------------------------------------------------------------------- */

/**
 * Strings de interfaz registrables/traducibles del tema.
 *
 * @return string[] Mapa clave => texto por defecto (EN provisional).
 */
function es_child_ui_strings() {
	return array(
		'hero_eyebrow'        => 'Product Designer · Systems & Operations',
		'hero_cta_primary'    => 'View featured case',
		'hero_cta_secondary'  => 'See how I work',
		'nav_work'            => 'Work',
		'nav_how'             => 'How I Work',
		'nav_about'           => 'About',
		'nav_connect'         => 'Connect',
		'featured_label'      => 'Featured case',
		'featured_cta'        => 'Read the case study',
		'process_label'       => 'How I work',
		'process_cta'         => 'See my process',
		'work_label'          => 'Selected work',
		'work_view_all'       => 'All work',
		'work_view_case'      => 'View case study',
		'about_label'         => 'About',
		'about_cta'           => 'More about me',
		'cta_label'           => 'Connect',
		'cta_button'          => 'Write me',
		'lang_switch_label'   => 'Language switch',
		'case_meta_status'    => 'Status',
		'case_meta_role'      => 'Role',
		'case_meta_tools'     => 'Tools',
		'case_meta_period'    => 'Period',
	);
}

/**
 * Email de contacto por defecto (editable por filtro es_contact_email).
 *
 * @return string
 */
function es_contact_email() {
	return apply_filters( 'es_contact_email', 'hello@ramiroestavillo.com' );
}

/**
 * Registro ordenado de secciones de la Home (fundación para editabilidad).
 *
 * Fuente única de la NARRATIVA de la home. Cada entrada mapea una clave de
 * sección → su template part. El orden del array es el orden de render:
 *
 *   Hero → How I Work → Featured Case → Selected Work → About → Connect
 *
 * Reordenar / quitar / insertar secciones = filtrar 'es_home_sections' (p. ej.
 * desde Code Snippets), sin editar el template PHP:
 *
 *   add_filter( 'es_home_sections', function ( $s ) {
 *       // mover About antes de Selected Work, por ejemplo:
 *       $about = $s['about']; unset( $s['about'] );
 *       // ...reordenar el array y devolverlo
 *       return $s;
 *   } );
 *
 * MIGRACIÓN FUTURA: cada valor es hoy un template part; mañana cada clave puede
 * apuntar a un block/pattern reutilizable sin reescribir el template — la clave
 * de sección es el contrato estable. Ver README (Foundation for editability).
 *
 * @return array<string,string> clave de sección => slug del template part
 */
function es_home_sections() {
	return apply_filters(
		'es_home_sections',
		array(
			'hero'          => 'template-parts/hero-home',
			'how-i-work'    => 'template-parts/how-i-work',
			'featured'      => 'template-parts/featured-case',
			'selected-work' => 'template-parts/selected-work',
			'about'         => 'template-parts/about-teaser',
			'connect'       => 'template-parts/footer-cta',
		)
	);
}

/**
 * Enlaces de navegación (header y footer). Editable por filtro.
 * Por defecto anclan a las secciones de la home de una sola página; se
 * pueden apuntar a páginas reales (Work/About/…) vía el filtro cuando existan.
 *
 * @return array<int,array{label:string,url:string}>
 */
function es_nav_links() {
	return apply_filters(
		'es_nav_links',
		array(
			array(
				'label' => es__( 'nav_work' ),
				'url'   => '#work',
			),
			array(
				'label' => es__( 'nav_how' ),
				'url'   => '#process',
			),
			array(
				'label' => es__( 'nav_about' ),
				'url'   => '#about',
			),
			array(
				'label' => es__( 'nav_connect' ),
				'url'   => '#connect',
			),
		)
	);
}

/**
 * Librería curada de íconos para los pasos de "How I Work" (línea fina,
 * 16x16, currentColor — mismo lenguaje visual que el ícono del toggle
 * Light/Dark en el header). Whitelist deliberada: el plugin solo guarda la
 * CLAVE elegida en un <select> (nunca HTML libre), así que no hay superficie
 * de XSS acá — ver es_portfolio_home_content_page() en el plugin, sección
 * "How I Work".
 *
 * @return array<string,string> clave => markup SVG completo.
 */
function es_process_icon_library() {
	$stroke = 'fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"';
	return array(
		'compass'  => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6.2"/><path d="M10.2 5.8 8.9 8.9 5.8 10.2 7.1 7.1z"/></svg>',
		'flow'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="3" cy="8" r="1.4"/><circle cx="13" cy="8" r="1.4"/><path d="M4.4 8h7.2M9 5.2 11.8 8 9 10.8"/></svg>',
		'target'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6"/><circle cx="8" cy="8" r="3"/><circle cx="8" cy="8" r="0.6" fill="currentColor"/></svg>',
		'layers'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2 14 5.2 8 8.4 2 5.2Z"/><path d="M2 9.4 8 12.6 14 9.4"/></svg>',
		'check'    => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><circle cx="8" cy="8" r="6.2"/><path d="M5.2 8.2 7.2 10.2 10.8 6"/></svg>',
		'document' => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><rect x="3.2" y="2" width="9.6" height="12" rx="1"/><path d="M5.6 5.8h4.8M5.6 8.4h4.8M5.6 11h3"/></svg>',
		'bulb'     => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2.2a4 4 0 0 1 2.2 7.3c-.4.3-.7.9-.7 1.4v.3H6.5v-.3c0-.5-.3-1.1-.7-1.4A4 4 0 0 1 8 2.2Z"/><path d="M6.6 13.4h2.8M7 14.8h2"/></svg>',
		'rocket'   => '<svg width="16" height="16" viewBox="0 0 16 16" ' . $stroke . '><path d="M8 2c1.8 1.4 2.8 3.6 2.8 6.2 0 1-.2 2-.6 2.9H5.8c-.4-.9-.6-1.9-.6-2.9C5.2 5.6 6.2 3.4 8 2Z"/><circle cx="8" cy="7" r="1"/><path d="M5.8 11.1 4.4 13M10.2 11.1l1.4 1.9M6.6 13.6v1.2M9.4 13.6v1.2"/></svg>',
	);
}

/**
 * Choices para el <select> de ícono por paso en Home Content (wp-admin).
 * Llamado desde el plugin con function_exists() como guarda (ver
 * home-content-options.php) — el plugin nunca asume que el tema activo
 * define esto.
 *
 * @return array<string,string> clave => label legible.
 */
function es_process_icon_choices() {
	$labels = array(
		'compass'  => __( 'Compass (understand)', 'estavillo-child' ),
		'flow'     => __( 'Flow (map)', 'estavillo-child' ),
		'target'   => __( 'Target (identify)', 'estavillo-child' ),
		'layers'   => __( 'Layers (design)', 'estavillo-child' ),
		'check'    => __( 'Check (validate)', 'estavillo-child' ),
		'document' => __( 'Document (record)', 'estavillo-child' ),
		'bulb'     => __( 'Bulb (idea)', 'estavillo-child' ),
		'rocket'   => __( 'Rocket (scale)', 'estavillo-child' ),
	);
	return $labels;
}

/**
 * Markup SVG de un ícono por clave, o cadena vacía si la clave no existe en
 * la librería (nunca imprime HTML fuera de esta whitelist).
 *
 * @param string $key Clave dentro de es_process_icon_library().
 * @return string
 */
function es_process_icon_svg( $key ) {
	$library = es_process_icon_library();
	return isset( $library[ $key ] ) ? $library[ $key ] : '';
}

/**
 * Registra los strings del tema en Polylang (si está activo).
 */
function es_child_register_pll_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}
	foreach ( es_child_ui_strings() as $key => $text ) {
		pll_register_string( 'es_' . $key, $text, 'Estavillo Child' );
	}
}
add_action( 'init', 'es_child_register_pll_strings' );

/**
 * Devuelve un string de interfaz, traducido por Polylang si está disponible.
 *
 * @param string $key Clave dentro de es_child_ui_strings().
 * @return string
 */
function es__( $key ) {
	$strings = es_child_ui_strings();
	$text    = isset( $strings[ $key ] ) ? $strings[ $key ] : $key;
	if ( function_exists( 'pll__' ) ) {
		return pll__( $text );
	}
	return $text;
}
