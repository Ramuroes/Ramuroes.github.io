<?php
/**
 * Header / Footer — global helpers (Phase 5).
 *
 * One source of truth for the global chrome. Every value here is read through
 * a filter the plugin (Portfolio Content) bridges to its admin fields, and
 * every filter has a theme default, so the header/footer never break if the
 * plugin is inactive or a field is left blank — same "empty never breaks a
 * page" principle as the rest of the theme.
 *
 * Nothing here is language-specific storage: phone/email/social/layout are
 * language-neutral (one global value); the translatable strings (nav labels,
 * a11y labels) keep coming from es__() / Polylang string translation.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Site identity (wordmark)
 * ---------------------------------------------------------------------- */

/**
 * Wordmark data for the header/footer brand.
 *
 * - text  : the text wordmark (default "ESTAVILLO");
 * - url   : where the brand links (default Home);
 * - image : optional logo image URL — when set, renders instead of the text
 *           (future-proofing; the text is still used as the accessible name).
 *
 * @return array{text:string,url:string,image:string}
 */
function es_wordmark() {
	return array(
		'text'  => (string) apply_filters( 'es_wordmark_text', 'ESTAVILLO' ),
		'url'   => (string) apply_filters( 'es_wordmark_url', apply_filters( 'es_home_url', home_url( '/' ) ) ),
		'image' => (string) apply_filters( 'es_wordmark_image', '' ),
	);
}

/* -------------------------------------------------------------------------
 * Header behaviour
 * ---------------------------------------------------------------------- */

/**
 * Whether the header is sticky. Default true (the current approved behaviour).
 * Filter 'es_sticky_header' receives a boolean.
 *
 * @return bool
 */
function es_sticky_header_enabled() {
	return (bool) apply_filters( 'es_sticky_header', true );
}

/**
 * Body classes so CSS can react to the sticky state (drives the anchor
 * scroll-offset, gated so a static header adds no gap).
 *
 * @param string[] $classes Current body classes.
 * @return string[]
 */
function es_hf_body_classes( $classes ) {
	$classes[] = es_sticky_header_enabled() ? 'es-header-sticky' : 'es-header-static';
	return $classes;
}
add_filter( 'body_class', 'es_hf_body_classes' );

/* -------------------------------------------------------------------------
 * Navigation routing (Home anchor vs. internal page) — §4
 * ---------------------------------------------------------------------- */

/**
 * URL de la página fija que usa un template del theme, en el IDIOMA de esta
 * request. Es la pieza que permite que el menú apunte a páginas reales
 * (Cómo trabajo / Sobre mí / Contacto) en vez de a anchors de la Home, sin
 * hardcodear un slug ni una ruta por idioma.
 *
 * Por qué por TEMPLATE y no por slug/ID: el template
 * ('templates/page-how-i-work.php') es el contrato estable del theme. El
 * slug lo puede cambiar el editor, el ID es distinto en cada instalación y
 * en cada idioma, y una ruta hardcodeada ('/es/como-trabajo/') se rompe en
 * silencio si Polylang cambia el prefijo o si se renombra la página. El
 * template, en cambio, es lo que el propio theme define.
 *
 * Polylang: NO hay que hacer nada especial más que dejar que filtre la
 * query. Cada traducción es su propio post con su propio template, así que
 * pedir "la página con este template" dentro del idioma activo devuelve
 * naturalmente la versión correcta. De ahí el suppress_filters => false:
 * get_posts() lo pone en true por defecto, y con true Polylang NO puede
 * filtrar por idioma — devolvería la primera página que encuentre, en
 * cualquier idioma. Es el detalle que rompería ES sin dar ningún error.
 *
 * Cacheado por (template + idioma) durante la request: el menú, el menú
 * mobile, el footer y el breadcrumb piden lo mismo varias veces.
 *
 * @param string $template Ruta del template relativa al theme.
 * @return string URL absoluta, o '' si no existe una página con ese template.
 */
function es_page_url_by_template( $template ) {
	static $es_cache = array();

	$es_lang = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '';
	$es_key  = (string) $template . '|' . $es_lang;
	if ( isset( $es_cache[ $es_key ] ) ) {
		return $es_cache[ $es_key ];
	}

	$es_ids = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'numberposts'            => 1,
			'fields'                 => 'ids',
			'meta_key'               => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- una sola vez por template/idioma y cacheado en memoria.
			'meta_value'             => (string) $template,  // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- idem.
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'suppress_filters'       => false, // imprescindible: habilita el filtro de idioma de Polylang.
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$es_cache[ $es_key ] = ! empty( $es_ids ) ? (string) get_permalink( $es_ids[0] ) : '';
	return $es_cache[ $es_key ];
}

/**
 * La URL de una página fija si existe; si no, el anchor de la Home de
 * siempre. Degradación limpia: mientras la página no esté creada (o esté
 * en borrador, o falte la traducción), el ítem sigue navegando a la
 * sección equivalente de la Home exactamente como hasta ahora.
 *
 * @param string $template Template de la página destino.
 * @param string $anchor   Anchor de fallback (p. ej. '#process').
 * @return string
 */
function es_nav_page_or_anchor( $template, $anchor ) {
	$es_url = es_page_url_by_template( $template );
	return '' !== $es_url ? $es_url : $anchor;
}

/**
 * Resolve one nav URL for the CURRENT request. The single, centralized rule
 * (no conditional URL logic scattered through templates):
 *
 *   - empty                     → Home URL (never an empty href);
 *   - bare in-page anchor (#x)  → on Home, keep "#x" (smooth in-page scroll);
 *                                 off Home, prefix the Home URL so it jumps
 *                                 to Home and then the section ("home + anchor")
 *                                 instead of a dead local anchor;
 *   - anything else (real page  → used as-is. A real WP page URL is already
 *     URL, or path+anchor)         language-correct as saved, and with Polylang
 *                                   home_url()/permalinks resolve to the current
 *                                   language natively — no guessed translated URLs.
 *
 * @param string $url Raw nav URL as configured (e.g. "#work" or a page URL).
 * @return string Resolved, always-valid href for this request.
 */
function es_nav_resolve_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return home_url( '/' );
	}
	if ( '#' === $url[0] ) {
		return is_front_page() ? $url : home_url( '/' ) . $url;
	}
	return $url;
}

/**
 * Nav items ready to render: label + resolved url + show flag. Reused by the
 * desktop header, the mobile menu and the footer nav (one dataset, §3).
 *
 * @return array<int,array{label:string,url:string,raw:string,show:bool}>
 */
function es_nav_links_display() {
	$out = array();
	foreach ( es_nav_links() as $es_item ) {
		$es_show = ! array_key_exists( 'show', $es_item ) || ! empty( $es_item['show'] );
		if ( ! $es_show ) {
			continue;
		}
		$es_raw = isset( $es_item['url'] ) ? (string) $es_item['url'] : '';
		$out[]  = array(
			'label' => isset( $es_item['label'] ) ? (string) $es_item['label'] : '',
			'url'   => es_nav_resolve_url( $es_raw ),
			'raw'   => $es_raw,
			'show'  => true,
		);
	}
	return $out;
}

/* -------------------------------------------------------------------------
 * Active navigation state (server-side) — §5
 * ---------------------------------------------------------------------- */

/**
 * The section key the CURRENT request maps to, for active-nav highlighting.
 * Server-side, page/context aware — no scroll tracking. Home stays neutral
 * (returns '') on purpose (§5).
 *
 * Un Case Study TAMBIÉN queda neutro, a propósito: adentro de un caso el
 * "dónde estoy" ya lo dicen el breadcrumb (Home / Work / Título, con el
 * aria-current="page" en el crumb que de verdad ES la página actual) y el
 * índice interno del caso, que es el que manda en esa pantalla. Marcar
 * además "Work" en el menú era un tercer indicador redundante — y encima
 * semánticamente incorrecto: Work es la sección padre, no la página en la
 * que está el usuario, así que su aria-current="page" mentía.
 *
 * @return string '' | 'work' | 'process' | 'about' | 'connect'
 */
function es_nav_active_key() {
	if ( is_page_template( 'templates/page-work.php' ) ) {
		return 'work';
	}
	if ( is_page_template( 'templates/page-how-i-work.php' ) ) {
		return 'process';
	}
	if ( is_page_template( 'templates/page-about.php' ) ) {
		return 'about';
	}
	if ( is_page_template( 'templates/page-contact.php' ) ) {
		return 'connect';
	}
	return '';
}

/**
 * Whether a given nav item is the active one for this request. Matches on the
 * item's in-page anchor fragment (the default config: #work/#process/#about/
 * #connect) against es_nav_active_key(); also matches a real-page target by
 * comparing its path to the current singular page's path, so active state
 * still works if an item is repointed to a real WP page.
 *
 * @param array $item A nav item (raw url in 'raw', or 'url').
 * @return bool
 */
function es_nav_item_is_active( $item ) {
	$active = es_nav_active_key();
	$raw    = isset( $item['raw'] ) ? (string) $item['raw'] : ( isset( $item['url'] ) ? (string) $item['url'] : '' );

	// Fragment convention (#work → 'work').
	$hash = strpos( $raw, '#' );
	if ( false !== $hash ) {
		$frag = sanitize_key( substr( $raw, $hash + 1 ) );
		if ( '' !== $active && $frag === $active ) {
			return true;
		}
	}

	// Real-page target: compare paths on singular pages.
	if ( '' !== $raw && '#' !== $raw[0] && is_singular() ) {
		$item_path = trim( (string) wp_parse_url( $raw, PHP_URL_PATH ), '/' );
		$curr_path = trim( (string) wp_parse_url( (string) get_permalink(), PHP_URL_PATH ), '/' );
		if ( '' !== $item_path && $item_path === $curr_path ) {
			return true;
		}
	}

	return false;
}

/* -------------------------------------------------------------------------
 * Footer content + layout
 * ---------------------------------------------------------------------- */

/**
 * Optional short footer note. Empty by default (nothing hardcoded / approved).
 *
 * @return string
 */
function es_footer_note() {
	return (string) apply_filters( 'es_footer_note', '' );
}

/**
 * Copyright name. The year is generated automatically at render time, so the
 * editor never has to update it; only the name is stored.
 *
 * @return string
 */
function es_footer_copyright_name() {
	return (string) apply_filters( 'es_footer_copyright_name', 'Ramiro Estavillo' );
}

/**
 * Footer layout preset: 'three' (default) | 'two' | 'compact'.
 *
 * @return string
 */
function es_footer_layout() {
	$layout = (string) apply_filters( 'es_footer_layout', 'three' );
	return in_array( $layout, array( 'three', 'two', 'compact' ), true ) ? $layout : 'three';
}

/**
 * Footer container width: 'standard' (default, site container) | 'wide'.
 *
 * @return string
 */
function es_footer_width() {
	$width = (string) apply_filters( 'es_footer_width', 'standard' );
	return in_array( $width, array( 'standard', 'wide' ), true ) ? $width : 'standard';
}

/**
 * Per-section visibility toggle. All sections visible by default; a section is
 * hidden only when its key is explicitly set false in the 'es_footer_visibility'
 * filter map. (Items with no data of their own — e.g. an empty social URL —
 * hide themselves regardless.)
 *
 * @param string $key nav|email|phone|whatsapp|linkedin|instagram|behance|location|note
 * @return bool
 */
function es_footer_visible( $key ) {
	$map = (array) apply_filters( 'es_footer_visibility', array() );
	return ! array_key_exists( $key, $map ) || ! empty( $map[ $key ] );
}

/* -------------------------------------------------------------------------
 * Two restrained line icons (phone + WhatsApp) — §12
 * Local, currentColor, 16-grid, 1.3 stroke — same language as the How I Work
 * / theme-toggle icons. No emoji, no external icon library. Theme-authored
 * (trusted) so echoed directly.
 * ---------------------------------------------------------------------- */

/**
 * @param string $name 'phone' | 'whatsapp'
 * @return string SVG markup (empty string for an unknown name).
 */
function es_footer_icon( $name ) {
	$open  = '<svg class="es-footer-contact__icon" width="15" height="15" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">';
	$icons = array(
		// handset outline
		'phone'    => '<path d="M5.4 2.6 3.1 3.3c-.5.1-.8.6-.7 1.1a11 11 0 0 0 9.2 9.2c.5.1 1-.2 1.1-.7l.7-2.3-2.7-1.3-1 1.1a8.2 8.2 0 0 1-3.4-3.4l1.1-1z"/>',
		// speech bubble outline (message → WhatsApp), restrained, not the brand glyph
		'whatsapp' => '<path d="M13.2 8.4a5.2 5.2 0 0 1-7.4 4.7L2.8 13.5l.8-2.8A5.2 5.2 0 1 1 13.2 8.4Z"/><path d="M6.2 6.6c0 2.3 1.9 4.2 4.2 4.2"/>',
	);
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return $open . $icons[ $name ] . '</svg>';
}
