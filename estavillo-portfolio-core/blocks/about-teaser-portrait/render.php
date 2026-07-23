<?php
/**
 * Render dinámico de estavillo/about-teaser-portrait — DEPRECATED.
 *
 * Refinement ticket §5: la Home About teaser ahora usa un core/image
 * editable directo (ver docs/content/home-gutenberg-en.html). Este bloque
 * queda registrado solo por compatibilidad hacia atrás: renderiza un marco
 * reservado neutro, SIN leer ninguna opción de Portfolio Content. No usar
 * en contenido nuevo.
 *
 * @package estavillo-portfolio-core
 * @var array $attributes Atributos del bloque (ninguno usado).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-about__portrait' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<div class="es-placeholder es-about__placeholder" role="img" aria-label="<?php esc_attr_e( 'Reserved space for the editorial portrait', 'estavillo-portfolio-core' ); ?>"></div>
</div>
