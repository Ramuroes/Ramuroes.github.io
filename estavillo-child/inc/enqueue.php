<?php
/**
 * Carga de assets del child theme.
 *
 * Estrategia:
 *  - Los tokens y la base se cargan en todo el sitio (son inertes: solo
 *    definen variables --es-* y estilos bajo .es-page, no pisan Kadence).
 *  - hero.css, pages-home.css y los JS solo se cargan en el template
 *    "Estavillo — Home", para no sumar peso al resto del sitio.
 *  - Google Fonts (Newsreader / Instrument Sans / Spline Sans Mono) se
 *    puede desactivar con el filtro 'es_child_load_google_fonts' si
 *    Kadence ya sirve estas fuentes localmente.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ¿Estamos en el template de la home nueva?
 *
 * @return bool
 */
function es_is_home_template() {
	return is_page_template( 'templates/page-home-estavillo.php' );
}

/**
 * Registra y encola estilos y scripts.
 */
function es_child_enqueue_assets() {
	$v = ES_CHILD_VERSION;

	// Hoja del parent (Kadence encola la suya propia; esto cubre el caso estándar de child theme).
	wp_enqueue_style( 'kadence-parent', get_template_directory_uri() . '/style.css', array(), $v );
	wp_enqueue_style( 'estavillo-child', get_stylesheet_uri(), array( 'kadence-parent' ), $v );

	// Tipografías (desactivable via filtro).
	if ( apply_filters( 'es_child_load_google_fonts', true ) ) {
		wp_enqueue_style(
			'es-fonts',
			'https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,300..700;1,6..72,300..700&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Spline+Sans+Mono:wght@400;500;600&display=swap',
			array(),
			null
		);
	}

	// Capa global del design system (tokens + base + layout + componentes).
	wp_enqueue_style( 'es-tokens', ES_CHILD_URI . '/assets/css/tokens.css', array( 'estavillo-child' ), $v );
	wp_enqueue_style( 'es-base', ES_CHILD_URI . '/assets/css/base.css', array( 'es-tokens' ), $v );
	wp_enqueue_style( 'es-layout', ES_CHILD_URI . '/assets/css/layout.css', array( 'es-base' ), $v );
	wp_enqueue_style( 'es-components', ES_CHILD_URI . '/assets/css/components.css', array( 'es-layout' ), $v );

	// Capa específica de la home (hero animado + secciones).
	if ( es_is_home_template() ) {
		wp_enqueue_style( 'es-hero', ES_CHILD_URI . '/assets/css/hero.css', array( 'es-components' ), $v );
		wp_enqueue_style( 'es-pages-home', ES_CHILD_URI . '/assets/css/pages-home.css', array( 'es-hero' ), $v );

		wp_enqueue_script(
			'es-motion',
			ES_CHILD_URI . '/assets/js/motion.js',
			array(),
			$v,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
		wp_enqueue_script(
			'es-hero-system-map',
			ES_CHILD_URI . '/assets/js/hero-system-map.js',
			array(),
			$v,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'es_child_enqueue_assets' );
