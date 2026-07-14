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
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_media_id    = isset( $attributes['mediaId'] ) ? (int) $attributes['mediaId'] : 0;
$es_url         = isset( $attributes['url'] ) ? (string) $attributes['url'] : '';
$es_alt         = isset( $attributes['alt'] ) ? (string) $attributes['alt'] : '';
$es_caption     = isset( $attributes['caption'] ) ? trim( (string) $attributes['caption'] ) : '';
$es_tag         = isset( $attributes['tag'] ) ? trim( (string) $attributes['tag'] ) : '';
$es_variant     = isset( $attributes['variant'] ) ? (string) $attributes['variant'] : 'standard';
$es_ph_label    = isset( $attributes['placeholderLabel'] ) ? trim( (string) $attributes['placeholderLabel'] ) : '';
$es_brow_label  = isset( $attributes['browserLabel'] ) ? trim( (string) $attributes['browserLabel'] ) : '';

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

$es_figure_class = 'es-case-figure';
if ( 'standard' === $es_variant ) {
	$es_figure_class .= ' es-case-figure--standard';
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
