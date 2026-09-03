<?php
/**
 * Portfolio dark mode — el modo del sitio y su clase de body.
 *
 * Tres piezas en este archivo:
 *  1. es_theme_dark_mode_enabled() — resuelve el modo de ESTA request. Hoy
 *     devuelve true siempre: el sistema oscuro dejó de ser una opción y pasó
 *     a ser el único sistema visual del portfolio. Sigue pasando por
 *     apply_filters( 'es_theme_dark_mode', … ) porque ese filtro es el punto
 *     de extensión para un modo claro real, si algún día existe.
 *  2. Preview admin-only: toggle con nonce, capability y cookie, para ver el
 *     modo en el sitio público sin activarlo para nadie más. HOY ESTÁ
 *     INERTE — es_theme_dark_mode_active() corta antes de llegar, la fila de
 *     la pantalla de admin se auto-oculta y el nodo de la admin bar no se
 *     registra. Se conserva a propósito: es exactamente el mecanismo que
 *     hace falta para probar un futuro modo claro antes de publicarlo.
 *  3. es_theme_dark_body_class() — el único lugar que decide qué clases de
 *     tema lleva el body: 'es-theme-dark' (gate histórico de
 *     assets/css/theme-dark.css) y 'es-theme--dark' (contrato nuevo).
 *
 * Language-independent by construction: reads no post/page content, no
 * Polylang state — one global decision, same in EN and ES.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ES_THEME_DARK_PREVIEW_COOKIE', 'es_theme_preview' );
define( 'ES_THEME_DARK_PREVIEW_NONCE_ACTION', 'es_theme_preview_toggle' );

/**
 * El modo visual de ESTA request. Hoy, siempre oscuro.
 *
 * @return bool
 */
function es_theme_dark_mode_enabled() {
	/*
	 * Dark es el DEFAULT del child theme (iteración de cierre). Antes el
	 * default era false y el look oscuro dependía de que la vista imprimiera
	 * el wrapper .es-page, que sólo existe en los seis templates propios: el
	 * 404, la búsqueda, los archivos y cualquier página sin template del
	 * theme caían al claro de Kadence, con un header y un menú que ya no
	 * existen en el resto del sitio.
	 *
	 * Sigue pasando por el filtro, así que la opción "Portfolio dark mode"
	 * del plugin lo puede apagar; lo único que cambió es de qué lado arranca.
	 * El día que exista light mode, este mismo filtro es el interruptor.
	 */
	return (bool) apply_filters( 'es_theme_dark_mode', true );
}

/**
 * Whether an admin-only preview cookie is present for the CURRENT request.
 * Does not by itself decide anything — es_theme_dark_mode_active() below
 * still requires the capability check to actually honor it.
 *
 * @return bool
 */
function es_theme_dark_preview_cookie_present() {
	return isset( $_COOKIE[ ES_THEME_DARK_PREVIEW_COOKIE ] ) && 'dark' === $_COOKIE[ ES_THEME_DARK_PREVIEW_COOKIE ];
}

/**
 * Whether THIS request should render dark: the global option, OR an
 * admin-only preview for a user who can currently manage options. Never
 * true for wp-admin screens themselves (the toggle only affects the public
 * theme output) and never true for an anonymous/low-privilege visitor,
 * regardless of what cookies their browser happens to carry.
 *
 * @return bool
 */
function es_theme_dark_mode_active() {
	if ( es_theme_dark_mode_enabled() ) {
		return true;
	}
	if ( is_admin() ) {
		return false;
	}
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	return es_theme_dark_preview_cookie_present();
}

/**
 * body_class: purely additive — never removes/reorders any existing class
 * (es-page, es-header-sticky, Kadence's own classes, etc.).
 *
 * @param string[] $classes
 * @return string[]
 */
function es_theme_dark_body_class( $classes ) {
	if ( ! es_theme_dark_mode_active() ) {
		return $classes;
	}

	/*
	 * Clase de TEMA. Va SIEMPRE: dice qué modo visual está activo, nada más.
	 * Es el contrato estable — cuando exista un modo claro, el theme emite
	 * 'es-theme--light' y ningún selector cambia de nombre.
	 */
	$classes[] = 'es-theme--dark';

	/*
	 * Clase de IMPLEMENTACIÓN, y sólo donde hace falta: es el gate de
	 * assets/css/theme-dark.css, que existe para vestir el chrome NATIVO de
	 * Kadence. Sus selectores nunca se escribieron para convivir con el
	 * design system (son del tipo "body.es-theme-dark a", 0-2-1), así que
	 * dentro de .es-page le ganan a reglas del sistema que son 0-1-0.
	 *
	 * Medido: emitiéndola en todas las vistas, dentro de .es-page el eyebrow
	 * perdía su verde de acento y salía en tinta plana, el texto apagado de
	 * un extracto subía a tinta llena, <strong> quedaba MÁS apagado que el
	 * párrafo que lo contiene, y TODO link enfocado por teclado bajaba a
	 * opacity .85 — 87 diferencias sobre tres vistas. Ninguna era una
	 * decisión de diseño: era una hoja pensada para otro contexto pisando al
	 * sistema.
	 *
	 * Con el gate limitado a las vistas que NO imprimen el chrome ESTAVILLO,
	 * theme-dark.css vuelve a hacer exactamente lo que su propia cabecera
	 * dice que hace, y las páginas del portfolio quedan gobernadas sólo por
	 * su design system.
	 */
	if ( ! function_exists( 'es_uses_estavillo_chrome' ) || ! es_uses_estavillo_chrome() ) {
		$classes[] = 'es-theme-dark';
	}

	return $classes;
}
add_filter( 'body_class', 'es_theme_dark_body_class' );

/**
 * Build a nonce-protected preview-toggle URL for the given target page and
 * on/off state. Shared by the admin bar and the Portfolio Content screen so
 * both links are generated the exact same way.
 *
 * @param string $state 'on' | 'off'.
 * @param string $target_url Page the link should return to after toggling.
 * @return string
 */
function es_theme_dark_preview_url( $state, $target_url ) {
	$url = add_query_arg( 'es_theme_preview', $state, $target_url );
	return wp_nonce_url( $url, ES_THEME_DARK_PREVIEW_NONCE_ACTION );
}

/**
 * Handle ?es_theme_preview=on|off&_wpnonce=... on the front end. Verifies
 * capability + nonce, sets/clears a short-lived admin-scoped cookie, then
 * redirects to the same URL with the toggle params stripped — so the
 * cleaned-up URL is what ends up bookmarked/shared/cached, and reloading
 * the page never re-submits the (time-limited) nonce.
 */
function es_theme_dark_preview_handle_request() {
	if ( is_admin() || ! isset( $_GET['es_theme_preview'] ) ) {
		return;
	}
	// Capability first, before even looking at the nonce — an anonymous or
	// low-privilege visitor is turned away regardless of what they pass.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, ES_THEME_DARK_PREVIEW_NONCE_ACTION ) ) {
		return;
	}

	$state  = sanitize_key( wp_unslash( $_GET['es_theme_preview'] ) );
	$secure = is_ssl();
	$cookie_args = array(
		'path'     => '/',
		'secure'   => $secure,
		'httponly' => true,
		'samesite' => 'Lax',
	);

	if ( 'on' === $state ) {
		setcookie( ES_THEME_DARK_PREVIEW_COOKIE, 'dark', array_merge( $cookie_args, array( 'expires' => time() + DAY_IN_SECONDS ) ) );
	} elseif ( 'off' === $state ) {
		setcookie( ES_THEME_DARK_PREVIEW_COOKIE, '', array_merge( $cookie_args, array( 'expires' => time() - HOUR_IN_SECONDS ) ) );
	} else {
		return; // Unrecognized value: do nothing, no redirect.
	}

	$clean_url = remove_query_arg( array( 'es_theme_preview', '_wpnonce' ) );
	wp_safe_redirect( $clean_url );
	exit;
}
add_action( 'template_redirect', 'es_theme_dark_preview_handle_request' );

/**
 * Cache safety: a page rendered under admin preview (global switch still
 * OFF) must never be stored by a page-cache plugin and later served to an
 * anonymous visitor. Only fires on that specific branch — the global
 * "Enabled" state is an intentional site-wide look and doesn't need this,
 * every visitor is meant to see it.
 */
function es_theme_dark_preview_nocache() {
	if ( is_admin() || es_theme_dark_mode_enabled() ) {
		return;
	}
	if ( es_theme_dark_mode_active() ) {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		nocache_headers();
	}
}
add_action( 'template_redirect', 'es_theme_dark_preview_nocache', 20 );

/**
 * Admin bar: an always-reachable, one-click way in and out of preview,
 * from any front-end page (not just Portfolio Content). Never registered
 * for anyone without manage_options, and hidden entirely once the global
 * switch is Enabled (nothing left to preview/exit at that point).
 *
 * @param WP_Admin_Bar $bar
 */
function es_theme_dark_admin_bar( $bar ) {
	if ( is_admin() || ! current_user_can( 'manage_options' ) || es_theme_dark_mode_enabled() ) {
		return;
	}

	$current_url = home_url( add_query_arg( null, null ) );

	if ( es_theme_dark_preview_cookie_present() ) {
		$bar->add_node(
			array(
				'id'    => 'es-theme-dark-exit',
				'title' => esc_html__( 'Exit dark preview', 'estavillo-child' ),
				'href'  => esc_url( es_theme_dark_preview_url( 'off', $current_url ) ),
			)
		);
	} else {
		$bar->add_node(
			array(
				'id'    => 'es-theme-dark-preview',
				'title' => esc_html__( 'Preview dark mode', 'estavillo-child' ),
				'href'  => esc_url( es_theme_dark_preview_url( 'on', $current_url ) ),
			)
		);
	}
}
add_action( 'admin_bar_menu', 'es_theme_dark_admin_bar', 100 );
