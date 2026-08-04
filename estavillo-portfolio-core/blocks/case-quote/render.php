<?php
/**
 * Render dinámico de estavillo/case-quote.
 *
 * div (no blockquote) a propósito: .es-case__body blockquote tiene su propio
 * estilo de cita simple (borde + itálica) que pisaría el diseño del
 * pullquote — mismo criterio que los ejemplos del README del theme.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_quote = isset( $attributes['quote'] ) ? trim( (string) $attributes['quote'] ) : '';
$es_cite  = isset( $attributes['cite'] ) ? trim( (string) $attributes['cite'] ) : '';

if ( '' === $es_quote ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-quote' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<p><?php echo wp_kses_post( $es_quote ); ?></p>
	<?php if ( '' !== $es_cite ) : ?>
		<cite><?php echo wp_kses_post( $es_cite ); ?></cite>
	<?php endif; ?>
</div>
