<?php
/**
 * Render dinámico de estavillo/case-figure.
 *
 * `variant` (tamaño), mismas clases que la librería del theme:
 * - standard: <figure class="es-case-figure es-case-figure--standard">
 *             default de siempre — a medida de lectura (--es-case-measure).
 * - wide:     <figure class="es-case-figure es-case-figure--wide"> — legado,
 *             ancho completo del container.
 * - large:    nombre canónico nuevo del mismo ancho que "wide" — comparten
 *             la MISMA clase CSS a propósito (cero duplicación). "wide"
 *             sigue existiendo sólo por compatibilidad con contenido ya
 *             guardado; ningún bloque nuevo debería volver a elegirlo.
 * - full:     <figure class="es-case-figure es-case-figure--full"> —
 *             sangra al viewport (visual break/hero), sin equivalente
 *             legado. CSS en case-study.css.
 * - browser:  suma el marco .es-case-browser (barra con 3 puntos + label)
 *             sobre cualquiera de los tamaños de arriba.
 *
 * `mediaType` (contenido): 'image' (default, compat con todo el contenido
 * guardado antes de que este atributo existiera), 'gif' (alias de 'image'
 * para el render — un GIF es un <img> normal, nunca CSS background) o
 * 'video' (<video>, ver más abajo). Sin imagen/video elegido se imprime el
 * placeholder .es-placeholder con el tag {asset: …} — el mismo contrato
 * del HTML manual y del plan de assets.
 *
 * VIDEO: nunca se imprime `src` ni `autoplay` crudo — `data-es-src` +
 * `data-es-video-autoplay` quedan para que
 * estavillo-child/assets/js/case-figure-media.js decida cuándo cargar el
 * archivo pesado (IntersectionObserver) y si corresponde autoplay
 * (prefers-reduced-motion, que PHP no puede saber en el servidor). Un
 * <noscript><source></noscript> sirve de fallback reproducible a mano sin
 * JS. `videoDecorative` (default true) saca el <video> del árbol de
 * accesibilidad; si es false, `alt` hace de nombre accesible vía
 * aria-label. El zoom (abajo) nunca se ofrece sobre video — sólo
 * imagen/GIF.
 *
 * ZOOM (opcional, `enableZoom`, sólo imagen/GIF): envuelve la figura
 * entera (imagen o marco browser, según la variante) en un <button> que
 * abre un visor compartido por página
 * (estavillo-child/assets/js/case-figure-lightbox.js) — un solo <dialog>,
 * no uno por figura. El botón sólo se emite si HAY una imagen real (con
 * el placeholder no hay nada que ampliar) y lleva la URL de máxima
 * calidad disponible (tamaño 'full', no la 'large' que ya se ve en la
 * página) para que el visor pueda mostrar detalle que en el layout normal
 * no entra. El caption NO se duplica: el visor lo toma directo del
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

$es_media_id      = isset( $attributes['mediaId'] ) ? (int) $attributes['mediaId'] : 0;
$es_url           = isset( $attributes['url'] ) ? (string) $attributes['url'] : '';
$es_alt           = isset( $attributes['alt'] ) ? (string) $attributes['alt'] : '';
$es_caption       = isset( $attributes['caption'] ) ? trim( (string) $attributes['caption'] ) : '';
$es_tag           = isset( $attributes['tag'] ) ? trim( (string) $attributes['tag'] ) : '';
$es_variant       = isset( $attributes['variant'] ) ? (string) $attributes['variant'] : 'standard';
$es_ph_label      = isset( $attributes['placeholderLabel'] ) ? trim( (string) $attributes['placeholderLabel'] ) : '';
$es_brow_label    = isset( $attributes['browserLabel'] ) ? trim( (string) $attributes['browserLabel'] ) : '';
$es_enable_zoom   = ! empty( $attributes['enableZoom'] );
$es_zoom_label    = trim( (string) ( $attributes['zoomLabel'] ?? '' ) );
$es_zoom_close    = trim( (string) ( $attributes['zoomCloseLabel'] ?? '' ) );
$es_zoom_in_lb    = trim( (string) ( $attributes['zoomInLabel'] ?? '' ) );
$es_zoom_out_lb   = trim( (string) ( $attributes['zoomOutLabel'] ?? '' ) );
$es_zoom_rst_lb   = trim( (string) ( $attributes['zoomResetLabel'] ?? '' ) );

// Media (ticket "ampliar sistema multimedia"): 'image' es el default de
// siempre — cualquier bloque guardado antes de que este atributo existiera
// cae acá y toma EXACTAMENTE la misma rama de abajo que ya usaba. 'gif' es
// deliberadamente un alias de 'image' para el render (un GIF es un <img>
// normal — nunca CSS background, ver docblock más abajo); sólo cambia qué
// selector de Biblioteca de medios ofrece el editor.
$es_media_type  = isset( $attributes['mediaType'] ) ? (string) $attributes['mediaType'] : 'image';
$es_is_video    = 'video' === $es_media_type;

$es_video_poster_id  = isset( $attributes['videoPosterId'] ) ? (int) $attributes['videoPosterId'] : 0;
$es_video_poster_url = isset( $attributes['videoPosterUrl'] ) ? (string) $attributes['videoPosterUrl'] : '';
$es_video_autoplay   = ! isset( $attributes['videoAutoplay'] ) || ! empty( $attributes['videoAutoplay'] );
$es_video_loop       = ! isset( $attributes['videoLoop'] ) || ! empty( $attributes['videoLoop'] );
$es_video_muted      = ! isset( $attributes['videoMuted'] ) || ! empty( $attributes['videoMuted'] );
$es_video_controls   = ! empty( $attributes['videoControls'] );
$es_video_decorative = ! isset( $attributes['videoDecorative'] ) || ! empty( $attributes['videoDecorative'] );

$es_object_fit  = isset( $attributes['mediaObjectFit'] ) ? (string) $attributes['mediaObjectFit'] : 'cover';
$es_max_height  = isset( $attributes['mediaMaxHeight'] ) ? trim( (string) $attributes['mediaMaxHeight'] ) : '';
// Sólo tiene efecto visual si hay un max-height que constreñir — sin eso,
// width:100%/height:auto ya conserva el aspect ratio sin recortar nada, que
// es el default "sin crops destructivos" pedido por el ticket.
$es_media_style = '';
if ( '' !== $es_max_height ) {
	$es_media_style = 'max-height:' . esc_attr( $es_max_height ) . ';object-fit:' . ( 'contain' === $es_object_fit ? 'contain' : 'cover' ) . ';width:100%;';
}

// ¿Hay imagen real (no placeholder)? Se calcula ANTES de armar el HTML: el
// zoom nunca se ofrece sobre un {asset: …} pendiente, no hay nada que ampliar.
// Sólo aplica a la rama imagen/GIF — el zoom con pan no está pensado para
// video (ver case-figure-lightbox.js).
$es_has_image = ! $es_is_video && ( $es_media_id > 0 || '' !== $es_url );
$es_has_video = $es_is_video && ( $es_media_id > 0 || '' !== $es_url );

if ( $es_is_video ) {
	// Poster: attachment de biblioteca si hay ID, si no URL directa. Sin
	// poster el navegador muestra el primer frame (o negro hasta cargar) —
	// no es un error, sólo un fallback menos prolijo.
	$es_poster_src = '';
	if ( $es_video_poster_id ) {
		$es_poster_src = (string) wp_get_attachment_image_url( $es_video_poster_id, 'large' );
	}
	if ( '' === $es_poster_src && '' !== $es_video_poster_url ) {
		$es_poster_src = $es_video_poster_url;
	}

	$es_video_src = '';
	$es_video_w   = 0;
	$es_video_h   = 0;
	if ( $es_media_id ) {
		$es_video_src = (string) wp_get_attachment_url( $es_media_id );
		$es_video_meta = wp_get_attachment_metadata( $es_media_id );
		if ( ! empty( $es_video_meta['width'] ) && ! empty( $es_video_meta['height'] ) ) {
			$es_video_w = (int) $es_video_meta['width'];
			$es_video_h = (int) $es_video_meta['height'];
		}
	}
	if ( '' === $es_video_src && '' !== $es_url ) {
		$es_video_src = $es_url;
	}

	if ( '' === $es_video_src ) {
		$es_ph_aria  = '' !== $es_alt ? $es_alt : __( 'Placeholder de video pendiente', 'estavillo-portfolio-core' );
		$es_img_html = '<div class="es-placeholder" role="img" aria-label="' . esc_attr( $es_ph_aria ) . '">'
			. '<span class="es-placeholder__tag">{asset: ' . esc_html( '' !== $es_ph_label ? $es_ph_label : 'pending' ) . '}</span>'
			. '</div>';
	} else {
		// data-es-src en vez de src: case-figure-media.js decide cuándo
		// cargar el archivo pesado (IntersectionObserver) y si autoplay
		// corresponde (prefers-reduced-motion). Sin JS (o navegador viejo)
		// <noscript> sirve el <source> real — el video sigue siendo
		// reproducible a mano, sólo no autoarranca ni se difiere la carga.
		$es_video_attrs = 'class="es-case-figure__video" playsinline preload="none"'
			. ( '' !== $es_media_style ? ' style="' . $es_media_style . '"' : '' )
			. ( '' !== $es_poster_src ? ' poster="' . esc_url( $es_poster_src ) . '"' : '' )
			. ( $es_video_w ? ' width="' . esc_attr( (string) $es_video_w ) . '"' : '' )
			. ( $es_video_h ? ' height="' . esc_attr( (string) $es_video_h ) . '"' : '' )
			. ( $es_video_loop ? ' loop' : '' )
			. ( $es_video_muted ? ' muted' : '' )
			. ( $es_video_controls ? ' controls' : '' )
			. ( $es_video_autoplay ? ' data-es-video-autoplay' : '' )
			// Decorativo (default: sí — pensado para uso tipo GIF/hero, sin
			// audio con contenido): fuera del árbol de accesibilidad. Si NO es
			// decorativo, el alt hace de nombre accesible del <video> — no hay
			// atributo "alt" nativo en <video>, aria-label es el equivalente.
			. ( $es_video_decorative ? ' aria-hidden="true" tabindex="-1"' : ( '' !== $es_alt ? ' aria-label="' . esc_attr( $es_alt ) . '"' : '' ) );

		$es_img_html = '<video ' . $es_video_attrs . ' data-es-src="' . esc_url( $es_video_src ) . '">'
			. '<noscript><source src="' . esc_url( $es_video_src ) . '" /></noscript>'
			. '</video>';
	}
} else {
	// La imagen: attachment de la biblioteca si hay ID (srcset incluido), URL
	// directa como fallback, y si no hay nada, el placeholder {asset: …}.
	// Misma rama exacta que antes de este ticket — 'image' y 'gif' comparten
	// esta única ruta de render, un GIF animado es un <img> como cualquier
	// otro (nunca background-image: eso rompe alt real y el crop responsive).
	$es_img_html = '';
	if ( $es_media_id ) {
		// OJO: NO array_filter() acá — 'alt' => '' es un valor legítimo
		// (imagen deliberadamente decorativa) y array_filter() con el callback
		// default lo borraría del array por ser "falsy", haciendo que
		// wp_get_attachment_image() caiga al alt guardado en la Biblioteca en
		// vez de respetar el alt (vacío a propósito) del bloque.
		$es_img_attrs = array(
			'alt'     => $es_alt,
			'loading' => 'lazy',
		);
		if ( '' !== $es_media_style ) {
			$es_img_attrs['style'] = $es_media_style;
		}
		$es_img_html = wp_get_attachment_image( $es_media_id, 'large', false, $es_img_attrs );
	}
	if ( '' === $es_img_html && '' !== $es_url ) {
		$es_img_html = '<img src="' . esc_url( $es_url ) . '" alt="' . esc_attr( $es_alt ) . '" loading="lazy"'
			. ( '' !== $es_media_style ? ' style="' . $es_media_style . '"' : '' ) . ' />';
	}
	if ( '' === $es_img_html ) {
		$es_ph_aria  = '' !== $es_alt ? $es_alt : __( 'Placeholder de imagen pendiente', 'estavillo-portfolio-core' );
		$es_img_html = '<div class="es-placeholder" role="img" aria-label="' . esc_attr( $es_ph_aria ) . '">'
			. '<span class="es-placeholder__tag">{asset: ' . esc_html( '' !== $es_ph_label ? $es_ph_label : 'pending' ) . '}</span>'
			. '</div>';
	}
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
} elseif ( 'wide' === $es_variant || 'large' === $es_variant ) {
	// 'large' es el nombre canónico nuevo del ticket "ampliar sistema
	// multimedia"; 'wide' es el legado (sistema editorial v2, sangrado al
	// borde en mobile). Comparten la MISMA clase a propósito — mismo ancho,
	// mismo comportamiento responsive, cero CSS duplicado.
	$es_figure_class .= ' es-case-figure--wide';
} elseif ( 'full' === $es_variant ) {
	// Full width / hero: sangra al viewport (visual break). Sin equivalente
	// legado — clase nueva, CSS nuevo en case-study.css.
	$es_figure_class .= ' es-case-figure--full';
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
