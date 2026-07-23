<?php
/**
 * Render dinámico de estavillo/about-teaser-text.
 *
 * Sin atributos configurables — el texto real vive en wp-admin →
 * Portfolio Content → About (campo about_text, puenteado al filtro
 * es_home_about_text por el propio plugin, includes/home-content-options.php).
 * Delega en es_about_intro_default()/es_about_intro_paragraphs() (child
 * theme, functions.php) — las MISMAS funciones que ya usa
 * template-parts/about-content.php (la página About real) y
 * template-parts/about-teaser.php (el fallback PHP de este mismo teaser)
 * — solo el PRIMER párrafo, como excerpt. Nunca copia el texto: lo lee en
 * cada render, así este bloque siempre queda sincronizado con la página
 * About sin mantenimiento manual.
 *
 * @package estavillo-portfolio-core
 * @var array $attributes Atributos del bloque (ninguno usado).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'es_about_intro_default' ) || ! function_exists( 'es_about_intro_paragraphs' ) ) {
	return;
}

$es_about_text_full = apply_filters( 'es_home_about_text', es_about_intro_default() );
$es_paragraphs       = es_about_intro_paragraphs( $es_about_text_full );
$es_teaser_text      = ! empty( $es_paragraphs[0] ) ? $es_paragraphs[0] : $es_about_text_full;

if ( '' === trim( (string) $es_teaser_text ) ) {
	return;
}
?>
<p <?php echo get_block_wrapper_attributes( array( 'class' => 'es-about__text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>><?php echo esc_html( $es_teaser_text ); ?></p>
