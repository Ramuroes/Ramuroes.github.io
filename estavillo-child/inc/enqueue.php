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
 * Versión de cache-bust por archivo: usa filemtime() para que CADA cambio de
 * un asset genere un ?ver= nuevo y el navegador/CDN vuelva a bajarlo.
 *
 * Motivo: con una versión estática (p. ej. ES_CHILD_VERSION fija) al actualizar
 * el tema el HTML del servidor cambia pero el JS/CSS cacheado NO — el hero
 * cliente-side quedaba viejo (sin el motor nuevo) aunque el Customizer emitiera
 * la variante correcta. filemtime evita eso de raíz.
 *
 * @param string $rel Ruta relativa al tema (p. ej. 'assets/js/hero-system-map.js').
 * @return string Versión (mtime como entero, o ES_CHILD_VERSION de fallback).
 */
function es_asset_ver( $rel ) {
	$path = ES_CHILD_DIR . '/' . ltrim( $rel, '/' );
	$mtime = file_exists( $path ) ? filemtime( $path ) : false;
	return $mtime ? (string) $mtime : ES_CHILD_VERSION;
}

/**
 * Registra y encola estilos y scripts.
 */
function es_child_enqueue_assets() {
	$v = ES_CHILD_VERSION;

	// Hoja del parent (Kadence encola la suya propia; esto cubre el caso estándar de child theme).
	wp_enqueue_style( 'kadence-parent', get_template_directory_uri() . '/style.css', array(), $v );
	wp_enqueue_style( 'estavillo-child', get_stylesheet_uri(), array( 'kadence-parent' ), es_asset_ver( 'style.css' ) );

	// Tipografías: solo se cargan las Google Fonts si el preset es
	// 'design_system'. Con 'classic_mockup' se usa el stack de sistema y no
	// se pide ninguna web font (más liviano). Igual queda el filtro para
	// forzar el comportamiento si hiciera falta.
	$load_fonts = 'design_system' === es_get_option( 'es_font_preset' );
	if ( apply_filters( 'es_child_load_google_fonts', $load_fonts ) ) {
		wp_enqueue_style(
			'es-fonts',
			'https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,300..700;1,6..72,300..700&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Spline+Sans+Mono:wght@400;500;600&display=swap',
			array(),
			null
		);
	}

	// Capa global del design system (tokens + base + layout + componentes).
	wp_enqueue_style( 'es-tokens', ES_CHILD_URI . '/assets/css/tokens.css', array( 'estavillo-child' ), es_asset_ver( 'assets/css/tokens.css' ) );
	wp_enqueue_style( 'es-base', ES_CHILD_URI . '/assets/css/base.css', array( 'es-tokens' ), es_asset_ver( 'assets/css/base.css' ) );
	wp_enqueue_style( 'es-layout', ES_CHILD_URI . '/assets/css/layout.css', array( 'es-base' ), es_asset_ver( 'assets/css/layout.css' ) );
	wp_enqueue_style( 'es-components', ES_CHILD_URI . '/assets/css/components.css', array( 'es-layout' ), es_asset_ver( 'assets/css/components.css' ) );

	// Capa específica de la home (hero animado + secciones).
	if ( es_is_home_template() ) {
		wp_enqueue_style( 'es-hero', ES_CHILD_URI . '/assets/css/hero.css', array( 'es-components' ), es_asset_ver( 'assets/css/hero.css' ) );
		wp_enqueue_style( 'es-pages-home', ES_CHILD_URI . '/assets/css/pages-home.css', array( 'es-hero' ), es_asset_ver( 'assets/css/pages-home.css' ) );

		wp_enqueue_script(
			'es-motion',
			ES_CHILD_URI . '/assets/js/motion.js',
			array(),
			es_asset_ver( 'assets/js/motion.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
		wp_enqueue_script(
			'es-hero-system-map',
			ES_CHILD_URI . '/assets/js/hero-system-map.js',
			array(),
			es_asset_ver( 'assets/js/hero-system-map.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		// Config del hero expuesta a JS (fuente confiable además del
		// data-attribute del template): la variante seleccionada llega al
		// motor aunque un plugin de caché/optimización reescriba el HTML.
		wp_localize_script(
			'es-hero-system-map',
			'EstavilloHeroConfig',
			array(
				'desktop'    => es_get_option( 'es_hero_variant_desktop' ),
				'mobile'     => es_get_option( 'es_hero_variant_mobile' ),
				'fontPreset' => es_get_option( 'es_font_preset' ),
				'version'    => ES_CHILD_VERSION,
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'es_child_enqueue_assets' );
