<?php
/**
 * Render dinámico de estavillo/case-details.
 *
 * <details>/<summary> nativos — accesibles y sin JS, igual que el HTML
 * manual de la librería.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    InnerBlocks ya renderizados.
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_summary = isset( $attributes['summary'] ) ? trim( (string) $attributes['summary'] ) : '';

/*
 * Ancho: mismo vocabulario que Case Section — "reading" (a medida de lectura,
 * el comportamiento de siempre y default) y "content" (ancho completo del
 * contenedor). Sólo se emite clase para "content": un acordeón ya guardado no
 * tiene el atributo, cae al default y sale con el MISMO HTML que antes.
 */
$es_width   = isset( $attributes['width'] ) ? (string) $attributes['width'] : 'reading';
$es_classes = 'es-case-details' . ( 'content' === $es_width ? ' es-case-details--content' : '' );
?>
<details <?php echo get_block_wrapper_attributes( array( 'class' => $es_classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<summary><?php echo '' !== $es_summary ? wp_kses_post( $es_summary ) : esc_html__( 'Detalles', 'estavillo-portfolio-core' ); ?></summary>
	<div class="es-case-details__body">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
	</div>
</details>
