<?php
/**
 * How I Work — shared SVG illustration renderer.
 *
 * Six approved vector illustrations (Fig. 01-06, staged from
 * estavillo-how-i-work-staging.zip) live as trusted, versioned files at
 * assets/images/how-i-work/{file}.svg — the geometry is final art, never
 * redrawn or re-optimized here. This file owns exactly one job: read those
 * six files safely, once per request, and hand back markup that is
 * consistent whether it's used by the Home teaser (three small, secondary
 * icons) or the How I Work page's estavillo/how-i-work-illustration block
 * (one large illustration per move).
 *
 * Each source file already carries its own inline <style> mapping
 * .how-work-svg__line/__accent-path/__accent-node/__accent-fill/
 * __accent-soft to the CSS custom properties --how-work-line/-accent/
 * -accent-soft (see assets/css/pages-home.css for the real token mapping
 * and the accent-hide rules driven by .how-work-illustration--no-accents).
 *
 * Not a generic SVG framework: no arbitrary file paths in, no icon-registry
 * abstraction — six known steps, one known folder, on purpose.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical step map: step number (1-6) => file slug + accessible label.
 * Single source of truth for both the file lookup and the label used when
 * an illustration is rendered as meaningful (decorative = false).
 *
 * @return array<int,array{file:string,label:string}>
 */
function es_how_work_illustration_steps() {
	return array(
		1 => array(
			'file'  => '01-understand-system',
			'label' => __( 'Understand the system', 'estavillo-child' ),
		),
		2 => array(
			'file'  => '02-find-real-problem',
			'label' => __( 'Find the real problem', 'estavillo-child' ),
		),
		3 => array(
			'file'  => '03-gather-evidence',
			'label' => __( 'Gather evidence', 'estavillo-child' ),
		),
		4 => array(
			'file'  => '04-explore-challenge',
			'label' => __( 'Explore and challenge', 'estavillo-child' ),
		),
		5 => array(
			'file'  => '05-design-solutions',
			'label' => __( 'Design practical solutions', 'estavillo-child' ),
		),
		6 => array(
			'file'  => '06-test-learn-iterate',
			'label' => __( 'Test, learn and iterate', 'estavillo-child' ),
		),
	);
}

/**
 * Reject anything that isn't the trusted, validated shape we expect from
 * this specific curated set (no <script>, no event-handler attributes, no
 * <image>, no href/xlink:href of any kind, no javascript: URIs). These are
 * versioned repo files, not user input — this is a defensive check against
 * a future file accidentally being swapped for something unsafe, not an
 * attempt to sanitize/strip a hostile payload.
 *
 * @param string $svg Raw SVG markup.
 * @return bool
 */
function es_how_work_illustration_is_safe( $svg ) {
	if ( '' === $svg || false === stripos( $svg, '<svg' ) ) {
		return false;
	}
	$forbidden = array(
		'<script',
		'<image',
		'javascript:',
		'<?php',
		'<!doctype',
		'<!entity',
	);
	$lower = strtolower( $svg );
	foreach ( $forbidden as $needle ) {
		if ( false !== strpos( $lower, $needle ) ) {
			return false;
		}
	}
	if ( preg_match( '/\son[a-z]+\s*=/i', $svg ) ) {
		return false;
	}
	// href/xlink:href are only allowed as same-document anchors (#id) —
	// anything else (an external URL, a data: URI, a bare protocol-relative
	// reference) is rejected. A same-document href is safe and is exactly
	// what es_how_work_illustration_namespace_ids() exists to rewrite.
	if ( preg_match_all( '/(?:xlink:)?href\s*=\s*"([^"]*)"/i', $svg, $hrefs ) ) {
		foreach ( $hrefs[1] as $href ) {
			if ( '' === $href || '#' !== $href[0] ) {
				return false;
			}
		}
	}
	return true;
}

/**
 * Raw, validated SVG markup per step, read once per request and cached.
 * A missing, unreadable, or unsafe-looking file simply isn't in the
 * returned array — callers treat that the same as "no illustration yet."
 *
 * @return array<int,string> step => raw SVG markup (XML declaration
 *                            stripped, otherwise byte-identical to the file).
 */
function es_how_work_illustration_library() {
	static $library = null;
	if ( null !== $library ) {
		return $library;
	}
	$library = array();
	$dir     = get_stylesheet_directory() . '/assets/images/how-i-work/';

	foreach ( es_how_work_illustration_steps() as $step => $meta ) {
		$file = $dir . $meta['file'] . '.svg';
		if ( ! is_readable( $file ) ) {
			continue;
		}
		$svg = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local del propio tema, no remoto.
		if ( ! is_string( $svg ) || '' === trim( $svg ) ) {
			continue;
		}
		$svg = trim( preg_replace( '/^\s*<\?xml[^>]*\?>\s*/i', '', $svg ) );
		if ( '' === $svg || false === stripos( $svg, '<svg' ) || ! es_how_work_illustration_is_safe( $svg ) ) {
			continue;
		}
		$library[ $step ] = $svg;
	}
	return $library;
}

/**
 * Give every id inside an SVG string a request-unique prefix, and rewrite
 * same-document references (url(#id), href="#id") to match — defends
 * against duplicate-id collisions if the same illustration (or, later, a
 * file that does carry ids) is rendered more than once on one page. The
 * six current files have no ids at all, so this is a no-op today.
 *
 * @param string $svg      Raw SVG markup.
 * @param string $instance Unique-enough instance key for this render call.
 * @return string
 */
function es_how_work_illustration_namespace_ids( $svg, $instance ) {
	if ( false === strpos( $svg, ' id="' ) ) {
		return $svg;
	}
	$prefix = 'eshw-' . preg_replace( '/[^a-z0-9]/i', '', $instance ) . '-';
	$svg    = preg_replace_callback(
		'/\sid="([^"]+)"/',
		function ( $m ) use ( $prefix ) {
			return ' id="' . $prefix . $m[1] . '"';
		},
		$svg
	);
	$svg    = preg_replace_callback(
		'/url\(#([^)"\']+)\)/',
		function ( $m ) use ( $prefix ) {
			return 'url(#' . $prefix . $m[1] . ')';
		},
		$svg
	);
	$svg    = preg_replace_callback(
		'/href="#([^"]+)"/',
		function ( $m ) use ( $prefix ) {
			return 'href="#' . $prefix . $m[1] . '"';
		},
		$svg
	);
	return $svg;
}

/**
 * The shared renderer. Reusable by the Gutenberg illustration block
 * (blocks/how-i-work-illustration/render.php in estavillo-portfolio-core)
 * and the Home How I Work teaser (template-parts/how-i-work.php) — neither
 * one duplicates SVG markup, both call this.
 *
 * @param int   $step Step 1-6.
 * @param array $args {
 *     @type string $context      'page' or 'home'. Default 'page'.
 *     @type bool   $show_accents Show the green accent classes. Default true.
 *     @type bool   $decorative   Hide from assistive tech (true) or expose
 *                                an accessible label built from the step
 *                                (false). Default true.
 *     @type string $class        Extra wrapper class(es), optional.
 * }
 * @return string Safe HTML, or '' if the step/file is invalid or missing
 *                (fails gracefully — callers just render nothing).
 */
function es_how_work_illustration_svg( $step, $args = array() ) {
	$step = (int) $step;
	if ( $step < 1 || $step > 6 ) {
		return '';
	}

	$steps = es_how_work_illustration_steps();
	if ( ! isset( $steps[ $step ] ) ) {
		return '';
	}

	$library = es_how_work_illustration_library();
	if ( empty( $library[ $step ] ) ) {
		return '';
	}

	$defaults = array(
		'context'      => 'page',
		'show_accents' => true,
		'decorative'   => true,
		'class'        => '',
	);
	$args    = wp_parse_args( $args, $defaults );
	$context = 'home' === $args['context'] ? 'home' : 'page';

	static $instance = 0;
	++$instance;
	$svg = es_how_work_illustration_namespace_ids( $library[ $step ], 'move' . $step . '-' . $instance );

	// Decorative vs meaningful: aria-hidden + no label, or role="img" +
	// aria-label, never both a duplicate <title> and aria-label.
	if ( $args['decorative'] ) {
		$svg = preg_replace( '/aria-hidden="[^"]*"/', 'aria-hidden="true"', $svg, 1 );
	} else {
		$label = esc_attr( $steps[ $step ]['label'] );
		$svg   = preg_replace( '/aria-hidden="[^"]*"/', 'aria-label="' . $label . '"', $svg, 1 );
	}

	$wrapper_classes = array( 'how-work-illustration', 'how-work-illustration--' . $context );
	if ( ! $args['show_accents'] ) {
		$wrapper_classes[] = 'how-work-illustration--no-accents';
	}
	if ( '' !== $args['class'] ) {
		$wrapper_classes[] = (string) $args['class'];
	}

	return sprintf(
		'<div class="%1$s">%2$s</div>',
		esc_attr( implode( ' ', $wrapper_classes ) ),
		$svg // phpcs:ignore WordPress.Security.EscapeOutput -- trusted local file, validated by es_how_work_illustration_is_safe() at read time; see doc block above.
	);
}
