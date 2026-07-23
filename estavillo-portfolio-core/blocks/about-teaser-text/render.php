<?php
/**
 * Render dinámico de estavillo/about-teaser-text.
 *
 * Sin atributos configurables — el texto real se lee EN VIVO de la página
 * About migrada a Gutenberg (su primer párrafo de intro, en el idioma
 * actual), vía es_home_about_intro() (child theme,
 * inc/about-intro-source.php). Esa función resuelve la página About del
 * idioma activo, parsea sus bloques y devuelve el primer párrafo real,
 * cayendo al sistema legacy (es_home_about_text) solo si la página no
 * tiene intro Gutenberg utilizable. Nunca copia el texto: lo lee en cada
 * render, así el teaser queda siempre sincronizado con la página About sin
 * mantenimiento manual ni copy propio de Home.
 *
 * @package estavillo-portfolio-core
 * @var array $attributes Atributos del bloque (ninguno usado).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'es_home_about_intro' ) ) {
	return;
}

$es_teaser_text = es_home_about_intro();

if ( '' === trim( (string) $es_teaser_text ) ) {
	return;
}
?>
<p <?php echo get_block_wrapper_attributes( array( 'class' => 'es-about__text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>><?php echo wp_kses_post( $es_teaser_text ); // es_home_about_intro() ya devuelve inline HTML seguro. ?></p>
