<?php
/**
 * Render dinámico de estavillo/about-teaser-text — DEPRECATED.
 *
 * Refinement ticket §5: la sincronización automática del About teaser con
 * la página About dejó de ser deseada. La Home About teaser ahora se edita
 * DIRECTO con bloques core (core/image + core/paragraph + CTA) — ver
 * docs/content/home-gutenberg-en.html. Este bloque queda registrado solo
 * por compatibilidad hacia atrás (contenido ya guardado que todavía lo
 * tenga): renderiza un párrafo estático por defecto, SIN leer la página
 * About ni ninguna opción/filtro. No usar en contenido nuevo.
 *
 * @package estavillo-portfolio-core
 * @var array $attributes Atributos del bloque (ninguno usado).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_text = function_exists( 'es_about_intro_default' ) ? (string) es_about_intro_default() : '';
if ( '' !== $es_text ) {
	$es_paras = preg_split( '/\r\n\r\n|\n\n+/', trim( $es_text ) );
	$es_text  = ! empty( $es_paras[0] ) ? trim( $es_paras[0] ) : trim( $es_text );
}

if ( '' === $es_text ) {
	return;
}
?>
<p <?php echo get_block_wrapper_attributes( array( 'class' => 'es-about__text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>><?php echo esc_html( $es_text ); ?></p>
