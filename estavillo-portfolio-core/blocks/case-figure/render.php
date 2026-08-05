<?php
/**
 * Render dinámico de estavillo/case-figure.
 *
 * Tres variantes, mismas clases que la librería del theme:
 * - standard: <figure class="es-case-figure es-case-figure--standard">
 * - wide:     <figure class="es-case-figure"> (ancho completo del container)
 * - browser:  igual que wide pero la imagen va dentro del marco
 *             .es-case-browser (barra con 3 puntos + label).
 * Sin imagen elegida se imprime el placeholder .es-placeholder con el tag
 * {asset: …} — el mismo contrato del HTML manual y del plan de assets.
 *
 * ZOOM (opcional, `enableZoom`): envuelve la figura entera (imagen o marco
 * browser, según la variante) en un <button> que abre un visor compartido
 * por página (estavillo-child/assets/js/case-figure-lightbox.js) — un solo
 * <dialog>, no uno por figura. El botón sólo se emite si HAY una imagen
 * real (con el placeholder no hay nada que ampliar) y lleva la URL de
 * máxima calidad disponible (tamaño 'full', no la 'large' que ya se ve en
 * la página) para que el visor pueda mostrar detalle que en el layout
 * normal no entra. El caption NO se duplica: el visor lo toma directo del
 * <figcaption> ya renderizado, al abrirse.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lupa con "+": trazo lineal, sin librería de íconos ni emoji — mismo
 * lenguaje visual que el resto del portfolio (case-flow, hobbies). El
 * mismo dibujo (mismo viewBox, mismos puntos) se repite en edit.js para
 * que el editor y el frontend se vean iguales.
 */
if ( ! function_exists( 'es_case_figure_zoom_badge_svg' ) ) {
	function es_case_figure_zoom_badge_svg() {
		return '<svg viewBox="0 0 20 20" aria-hidden="true" focusable="false">'
			. '<circle cx="8" cy="8" r="5.5" />'
			. '<line x1="12.2" y1="12.2" x2="17" y2="17" />'
			. '<line x1="5.2" y1="8" x2="10.8" y2="8" />'
			. '<line x1="8" y1="5.2" x2="8" y2="10.8" />'
			. '</svg>';
	}
}

$es_media_id    = isset( $attributes['mediaId'] ) ? (int) $attributes['mediaId'] : 0;
$es_url         = isset( $attributes['url'] ) ? (string) $attributes['url'] : '';
$es_alt         = isset( $attributes['alt'] ) ? (string) $attributes['alt'] : '';
$es_caption     = isset( $attributes['caption'] ) ? trim( (string) $attributes['caption'] ) : '';
$es_tag         = isset( $attributes['tag'] ) ? trim( (string) $attributes['tag'] ) : '';
$es_variant     = isset( $attributes['variant'] ) ? (string) $attributes['variant'] : 'standard';
$es_ph_label    = isset( $attributes['placeholderLabel'] ) ? trim( (string) $attributes['placeholderLabel'] ) : '';
$es_brow_label  = isset( $attributes['browserLabel'] ) ? trim( (string) $attributes['browserLabel'] ) : '';
$es_enable_zoom = ! empty( $attributes['enableZoom'] );
$es_zoom_label  = trim( (string) ( $attributes['zoomLabel'] ?? '' ) );
$es_zoom_close  = trim( (string) ( $attributes['zoomCloseLabel'] ?? '' ) );
$es_zoom_in_lb  = trim( (string) ( $attributes['zoomInLabel'] ?? '' ) );
$es_zoom_out_lb = trim( (string) ( $attributes['zoomOutLabel'] ?? '' ) );
$es_zoom_rst_lb = trim( (string) ( $attributes['zoomResetLabel'] ?? '' ) );

// ¿Hay imagen real (no placeholder)? Se calcula ANTES de armar el HTML: el
// zoom nunca se ofrece sobre un {asset: …} pendiente, no hay nada que ampliar.
$es_has_image = $es_media_id > 0 || '' !== $es_url;

// La imagen: attachment de la biblioteca si hay ID (srcset incluido), URL
// directa como fallback, y si no hay nada, el placeholder {asset: …}.
$es_img_html = '';
if ( $es_media_id ) {
	$es_img_html = wp_get_attachment_image( $es_media_id, 'large', false, array( 'alt' => $es_alt, 'loading' => 'lazy' ) );
}
if ( '' === $es_img_html && '' !== $es_url ) {
	$es_img_html = '<img src="' . esc_url( $es_url ) . '" alt="' . esc_attr( $es_alt ) . '" loading="lazy" />';
}
if ( '' === $es_img_html ) {
	$es_ph_aria  = '' !== $es_alt ? $es_alt : __( 'Placeholder de imagen pendiente', 'estavillo-portfolio-core' );
	$es_img_html = '<div class="es-placeholder" role="img" aria-label="' . esc_attr( $es_ph_aria ) . '">'
		. '<span class="es-placeholder__tag">{asset: ' . esc_html( '' !== $es_ph_label ? $es_ph_label : 'pending' ) . '}</span>'
		. '</div>';
}

// Marco browser (variante "browser"): barra con 3 puntos + label.
if ( 'browser' === $es_variant ) {
	$es_img_html = '<div class="es-case-browser">'
		. '<div class="es-case-browser__bar">'
		. '<span class="es-case-browser__dot"></span>'
		. '<span class="es-case-browser__dot"></span>'
		. '<span class="es-case-browser__dot"></span>'
		. ( '' !== $es_brow_label ? '<span class="es-case-browser__label">' . esc_html( $es_brow_label ) . '</span>' : '' )
		. '</div>'
		. $es_img_html
		. '</div>';
}

// Zoom: envuelve TODO lo anterior (imagen o marco browser completo) en un
// botón real — Enter/Espacio funcionan gratis, sin reinventar manejo de
// teclado. La URL de máxima calidad ('full') es la que usa el visor; la
// que ya se ve en la página sigue siendo 'large', sin cambios ahí.
$es_zoom_active = $es_enable_zoom && $es_has_image;
if ( $es_zoom_active ) {
	$es_zoom_src = '';
	$es_zoom_w   = 0;
	$es_zoom_h   = 0;
	if ( $es_media_id ) {
		$es_full = wp_get_attachment_image_src( $es_media_id, 'full' );
		if ( $es_full ) {
			$es_zoom_src = (string) $es_full[0];
			$es_zoom_w   = (int) $es_full[1];
			$es_zoom_h   = (int) $es_full[2];
		}
	}
	if ( '' === $es_zoom_src ) {
		$es_zoom_src = $es_url;
	}

	if ( '' !== $es_zoom_src ) {
		$es_zoom_aria = '' !== $es_zoom_label ? $es_zoom_label : 'Zoom image';
		if ( '' !== $es_alt ) {
			$es_zoom_aria .= ': ' . $es_alt;
		}
		$es_img_html = '<button type="button" class="es-case-figure__zoom-trigger" data-es-zoom-trigger'
			. ' data-es-zoom-src="' . esc_url( $es_zoom_src ) . '"'
			. ( $es_zoom_w ? ' data-es-zoom-w="' . esc_attr( (string) $es_zoom_w ) . '"' : '' )
			. ( $es_zoom_h ? ' data-es-zoom-h="' . esc_attr( (string) $es_zoom_h ) . '"' : '' )
			. ' data-es-zoom-close-label="' . esc_attr( '' !== $es_zoom_close ? $es_zoom_close : 'Close image' ) . '"'
			. ' data-es-zoom-in-label="' . esc_attr( '' !== $es_zoom_in_lb ? $es_zoom_in_lb : 'Zoom in' ) . '"'
			. ' data-es-zoom-out-label="' . esc_attr( '' !== $es_zoom_out_lb ? $es_zoom_out_lb : 'Zoom out' ) . '"'
			. ' data-es-zoom-reset-label="' . esc_attr( '' !== $es_zoom_rst_lb ? $es_zoom_rst_lb : 'Reset zoom' ) . '"'
			. ' aria-label="' . esc_attr( $es_zoom_aria ) . '"'
			. '>'
			. $es_img_html
			. '<span class="es-case-figure__zoom-badge" aria-hidden="true">' . es_case_figure_zoom_badge_svg() . '</span>'
			. '</button>';
	} else {
		$es_zoom_active = false;
	}
}

$es_figure_class = 'es-case-figure';
if ( 'standard' === $es_variant ) {
	$es_figure_class .= ' es-case-figure--standard';
} elseif ( 'wide' === $es_variant ) {
	// Gancho de estilo para el sistema editorial v2 (sangrado al borde en
	// mobile). Ningún HTML legacy usa esta clase — solo el bloque.
	$es_figure_class .= ' es-case-figure--wide';
}
if ( $es_zoom_active ) {
	$es_figure_class .= ' es-case-figure--zoomable';
}

$es_caption_html = '';
if ( '' !== $es_tag || '' !== $es_caption ) {
	$es_caption_html = '<figcaption class="es-case-caption">'
		. ( '' !== $es_tag ? '<span class="es-case-caption__tag">' . wp_kses_post( $es_tag ) . '</span>' : '' )
		. ( '' !== $es_caption ? '<span>' . wp_kses_post( $es_caption ) . '</span>' : '' )
		. '</figcaption>';
}
?>
<figure <?php echo get_block_wrapper_attributes( array( 'class' => $es_figure_class ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php echo $es_img_html; // phpcs:ignore WordPress.Security.EscapeOutput -- construido arriba con esc_*(). ?>
	<?php echo $es_caption_html; // phpcs:ignore WordPress.Security.EscapeOutput -- construido arriba con esc_*(). ?>
</figure>
