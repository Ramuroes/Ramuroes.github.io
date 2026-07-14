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
?>
<details <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-details' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<summary><?php echo '' !== $es_summary ? wp_kses_post( $es_summary ) : esc_html__( 'Detalles', 'estavillo-portfolio-core' ); ?></summary>
	<div class="es-case-details__body">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
	</div>
</details>
