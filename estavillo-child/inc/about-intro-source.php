<?php
/**
 * Home About teaser — single source of truth for the intro paragraph.
 *
 * The About page has been migrated to Gutenberg (templates/page-about.php +
 * real block content). Its introduction now lives in that page's saved
 * post_content, NOT in the legacy `about_text` Portfolio Content option.
 * es_home_about_intro() resolves the About page for the CURRENT language
 * (Polylang-aware — English Home reads the English About, Spanish Home the
 * Spanish About), reads its saved Gutenberg content, and returns the first
 * genuine introductory paragraph.
 *
 * It reads the PARSED block tree only (parse_blocks) — never render_block,
 * never a full page render — so there is no recursion into the page's own
 * rendering and no risk of pulling the entire About page into the Home. It
 * skips breadcrumbs, eyebrows, section labels/numbers, headings, images,
 * empty and decorative blocks, and returns safe inline HTML.
 *
 * Only when the About page has no usable Gutenberg intro (e.g. it is still
 * on its own legacy about-content.php fallback, so its post_content is
 * empty) does this fall back to the legacy es_home_about_text filter /
 * es_about_intro_default() — the same source the About page itself would
 * render in that state, so the two never diverge. One source of truth, no
 * Home-specific duplicate copy, no manual synchronization.
 *
 * Caching: a per-request static memo keyed by language. The extraction is
 * one indexed meta query + one parse_blocks of a single page — cheap enough
 * that a persistent cache is not worth the invalidation risk, and a
 * per-request memo is trivially always-fresh (edit the About page, reload,
 * the teaser is current). No transients, nothing to invalidate on save.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ID de la página About en el idioma actual (Polylang filtra WP_Query de
 * páginas al idioma activo en el front automáticamente; sin Polylang hay
 * una sola página con este template). 0 si no existe ninguna.
 *
 * @return int
 */
function es_home_about_page_id() {
	$query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- página única, consulta barata + memoizada.
			'meta_value'     => 'templates/page-about.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return ! empty( $query->posts ) ? (int) $query->posts[0] : 0;
}

/**
 * Longitud de texto tolerante a mbstring ausente.
 *
 * @param string $text Texto ya sin tags.
 * @return int
 */
function es_home_text_length( $text ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $text ) : strlen( $text );
}

/**
 * Recorre el árbol de bloques parseado buscando el primer párrafo de intro
 * REAL. Recursa en contenedores estructurales (group/columns/column) sin
 * renderizarlos. Corta en el primer match.
 *
 * @param array $blocks Bloques parseados (parse_blocks()).
 * @param int   $depth  Profundidad actual (tope defensivo).
 * @return string Inline HTML seguro, o '' si no hay ninguno.
 */
function es_home_find_intro_paragraph( $blocks, $depth = 0 ) {
	if ( $depth > 6 || ! is_array( $blocks ) ) {
		return '';
	}

	foreach ( $blocks as $block ) {
		$name = isset( $block['blockName'] ) ? $block['blockName'] : '';

		// Contenedores: bajar un nivel, nunca renderizar.
		if ( in_array( $name, array( 'core/group', 'core/columns', 'core/column' ), true ) ) {
			$found = es_home_find_intro_paragraph( isset( $block['innerBlocks'] ) ? $block['innerBlocks'] : array(), $depth + 1 );
			if ( '' !== $found ) {
				return $found;
			}
			continue;
		}

		// Solo párrafos son candidatos (headings, imágenes, spacers,
		// breadcrumbs, eyebrows como heading, etc. se ignoran).
		if ( 'core/paragraph' !== $name ) {
			continue;
		}

		$html  = isset( $block['innerHTML'] ) ? trim( $block['innerHTML'] ) : '';
		$attrs = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
		$class = isset( $attrs['className'] ) ? (string) $attrs['className'] : '';

		// Descartar párrafos decorativos/estructurales por clase.
		if ( '' !== $class && preg_match( '/eyebrow|section-head|__num|\blabel\b|kicker|breadcrumb|placeholder|__tag/i', $class ) ) {
			continue;
		}

		// Debe ser prosa real, no una etiqueta corta tipo "01".
		$text = trim( wp_strip_all_tags( $html ) );
		if ( es_home_text_length( $text ) < 40 ) {
			continue;
		}

		// Devolver inline HTML seguro (mantener énfasis liviano, sin <p>).
		$inner = preg_replace( '#^\s*<p\b[^>]*>(.*)</p>\s*$#is', '$1', $html );
		return wp_kses(
			$inner,
			array(
				'em'     => array(),
				'strong' => array(),
				'i'      => array(),
				'b'      => array(),
				'br'     => array(),
				'a'      => array(
					'href'   => array(),
					'title'  => array(),
					'rel'    => array(),
					'target' => array(),
				),
			)
		);
	}

	return '';
}

/**
 * Primer párrafo de intro de la página About en el idioma actual, con
 * fallback al sistema legacy (opción/filtro es_home_about_text) solo si la
 * página no tiene contenido Gutenberg utilizable. Memoizado por idioma.
 *
 * @return string Inline HTML seguro ('' solo si ni Gutenberg ni el legacy
 *                tienen texto — caso que el caller ya maneja no imprimiendo
 *                nada).
 */
function es_home_about_intro() {
	static $cache = array();

	$lang = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : 'default';
	if ( isset( $cache[ $lang ] ) ) {
		return $cache[ $lang ];
	}

	$intro    = '';
	$about_id = es_home_about_page_id();
	if ( $about_id && function_exists( 'parse_blocks' ) ) {
		$content = (string) get_post_field( 'post_content', $about_id );
		if ( '' !== trim( $content ) ) {
			$intro = es_home_find_intro_paragraph( parse_blocks( $content ) );
		}
	}

	// Fallback legacy: la página About no tiene intro Gutenberg utilizable
	// todavía. Misma fuente que renderizaría la propia página About en ese
	// estado (about-content.php -> es_home_about_text), así nunca divergen.
	if ( '' === $intro ) {
		$full  = apply_filters( 'es_home_about_text', function_exists( 'es_about_intro_default' ) ? es_about_intro_default() : '' );
		$paras = function_exists( 'es_about_intro_paragraphs' ) ? es_about_intro_paragraphs( $full ) : array();
		$first = ! empty( $paras[0] ) ? $paras[0] : $full;
		$intro = esc_html( (string) $first );
	}

	$cache[ $lang ] = $intro;
	return $intro;
}
