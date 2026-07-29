<?php
/**
 * Portfolio dark mode — global toggle + admin-only safe preview.
 *
 * Three pieces, all in this one file:
 *  1. es_theme_dark_mode_enabled() — the GLOBAL switch (Portfolio Content →
 *     "Portfolio dark mode", bridged via the 'es_theme_dark_mode' filter,
 *     same theme-default/plugin-bridge pattern as es_sticky_header_enabled()
 *     in inc/header-footer.php). Default Disabled.
 *  2. Admin-only preview: a signed (nonce), capability-gated, cookie-based
 *     toggle so an administrator can see the dark system live on the real
 *     public site WITHOUT flipping it on for anyone else. Re-checks the
 *     capability on every read, not just when the cookie was set, so a
 *     stray/shared cookie can never activate it for a non-admin.
 *  3. es_theme_dark_body_class() — the single place that decides whether
 *     THIS request renders dark: adds body.es-theme-dark, the one class
 *     every rule in assets/css/theme-dark.css is gated behind.
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
 * The GLOBAL "Portfolio dark mode" switch. Disabled until an administrator
 * explicitly turns it on in Portfolio Content — never on by default.
 *
 * @return bool
 */
function es_theme_dark_mode_enabled() {
	return (bool) apply_filters( 'es_theme_dark_mode', false );
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
	if ( es_theme_dark_mode_active() ) {
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
