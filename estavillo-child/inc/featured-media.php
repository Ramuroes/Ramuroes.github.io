<?php
/**
 * Featured Media — render compartido (ticket "Featured media del Case
 * Study").
 *
 * CONCEPTO DISTINTO de estavillo/case-figure: esto es la representación
 * del CASE STUDY ENTERO en Home (Featured Case) y Work (Featured Work),
 * configurada a nivel de post meta del CPT — nunca dentro del contenido
 * Gutenberg del caso. No se toca ni se reusa el bloque case-figure.
 *
 * Sí se reusa, deliberadamente, el MISMO contrato de datos que ya usa
 * case-figure para video: data-es-src (en vez de src) + data-es-video-
 * autoplay, resueltos por assets/js/case-figure-media.js (selector
 * generalizado a cualquier <video data-es-src>, no sólo los de case-figure
 * — ver ese archivo) — mismo lazy-load con IntersectionObserver y mismo
 * autoplay condicionado a prefers-reduced-motion, sin un segundo módulo JS
 * que mantener. <noscript><source></noscript> como fallback reproducible
 * sin JS, igual que case-figure.
 *
 * El video de Featured Media SIEMPRE es decorativo (loop, muted, sin
 * controles, aria-hidden) — es un visual cinematográfico junto a texto
 * editorial real que ya describe el caso, no un video con su propia
 * narración; por eso no hay toggles de autoplay/loop/muted/controls en el
 * admin (ver el panel "Featured Media" en case-study-cpt.php) — a
 * diferencia de case-figure, acá esas cinco variables no tienen que ser
 * configurables para que el caso de uso funcione.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ¿Este $case tiene media real (no placeholder)? Mismo criterio en los
 * tres call sites (Home template-part, Home block, Work featured card) —
 * una sola función, cero riesgo de que alguno se desincronice y muestre
 * un <video> vacío o un placeholder de más.
 *
 * @param array $case Shape de es_case_get_featured_media() + 'image'.
 * @return bool
 */
function es_featured_has_media( array $case ) {
	if ( 'video' === ( $case['media_type'] ?? 'image' ) ) {
		return ! empty( $case['video_url'] );
	}
	return ! empty( $case['image'] );
}

/**
 * Imprime el <img>/<video> del Featured Media. NO imprime el placeholder
 * (eso sigue viviendo en cada template, con su propio marco visual — ver
 * es_featured_has_media() arriba para decidir cuál rama usar) ni el div
 * contenedor (.es-featured__media / .es-card__media, con su propio alto
 * fijo por layout — ver pages-home.css/components.css): sólo la etiqueta
 * de media en sí, para que object-fit/focal-position/tipo sean
 * consistentes sin importar qué página la está mostrando.
 *
 * @param array $case Shape de es_case_get_featured_media() + 'image'/'title'.
 */
function es_render_featured_media( array $case ) {
	$media_type = isset( $case['media_type'] ) ? $case['media_type'] : 'image';
	$object_fit = isset( $case['object_fit'] ) ? $case['object_fit'] : 'cover';
	$focal      = isset( $case['focal'] ) ? $case['focal'] : 'center';

	$es_focal_css = array(
		'left'   => 'left center',
		'right'  => 'right center',
		'center' => 'center',
	);
	$es_style = 'object-fit:' . ( 'contain' === $object_fit ? 'contain' : 'cover' ) . ';'
		. 'object-position:' . ( isset( $es_focal_css[ $focal ] ) ? $es_focal_css[ $focal ] : 'center' ) . ';';

	if ( 'video' === $media_type && ! empty( $case['video_url'] ) ) {
		$es_poster_attr = ! empty( $case['video_poster'] ) ? ' poster="' . esc_url( $case['video_poster'] ) . '"' : '';
		echo '<video'
			. ' style="' . esc_attr( $es_style ) . '"'
			. ' playsinline preload="none" loop muted aria-hidden="true" tabindex="-1"'
			. ' data-es-video-autoplay'
			. $es_poster_attr // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado (esc_url) arriba.
			. ' data-es-src="' . esc_url( $case['video_url'] ) . '">'
			. '<noscript><source src="' . esc_url( $case['video_url'] ) . '" /></noscript>'
			. '</video>';
		return;
	}

	if ( ! empty( $case['image'] ) ) {
		// alt="" a propósito, mismo criterio que ya usaban featured-case.php
		// y es_work_media(): el visual es decorativo/ilustrativo junto a
		// texto editorial real que ya describe el caso.
		echo '<img src="' . esc_url( $case['image'] ) . '" alt="" loading="lazy" style="' . esc_attr( $es_style ) . '" />';
	}
}
