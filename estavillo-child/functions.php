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
