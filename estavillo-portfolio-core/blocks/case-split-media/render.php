<?php
/**
 * Render dinámico de estavillo/case-split-media: la región de MEDIA de un
 * Case Section en layout Split. Solo un wrapper con la clase de grilla —
 * la columna exacta (6–12, 1–7 o 7–12 según el split) la decide el CSS del
 * theme a partir de la clase del section padre.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    InnerBlocks ya renderizados.
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-split__media' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
</div>
