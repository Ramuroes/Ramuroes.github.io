<?php
/**
 * Render dinámico de estavillo/how-i-work-teaser.
 *
 * Solo envuelve las InnerBlocks (las tres ideas ya renderizadas) en un div
 * con las clases modificadoras que la CSS del theme (pages-home.css) usa
 * para el layout desktop y el tamaño de ilustración:
 *
 *   .es-process-teaser__row                     -> grilla 3-up (desktop) / 1-up (mobile)
 *   .es-process-teaser__row--stacked | --side   -> ilustración arriba | al costado
 *   .es-process-teaser__row--illus-{small|medium|large}  -> tamaño (var CSS)
 *
 * Bloque envoltorio ÚNICO (no tres bloques duplicados): las tres ideas son
 * InnerBlocks (ilustración + título + texto, copy editable), y estos dos
 * atributos se aplican por igual a las tres. Mobile siempre apila (la CSS
 * ignora --side bajo el breakpoint). Usar un div propio (no un core/group)
 * evita que el motor de layout de core inyecte is-layout-* y pise la
 * grilla — mismo criterio por el que Home dejó de usar un wp:group con la
 * clase de grilla.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    InnerBlocks ya renderizados (las 3 ideas).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_layout = isset( $attributes['desktopLayout'] ) ? (string) $attributes['desktopLayout'] : 'stacked';
if ( ! in_array( $es_layout, array( 'stacked', 'side' ), true ) ) {
	$es_layout = 'stacked';
}

$es_size = isset( $attributes['illustrationSize'] ) ? (string) $attributes['illustrationSize'] : 'small';
if ( ! in_array( $es_size, array( 'small', 'medium', 'large' ), true ) ) {
	$es_size = 'small';
}

$es_classes = 'es-process-teaser__row es-process-teaser__row--' . $es_layout . ' es-process-teaser__row--illus-' . $es_size;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $es_classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
</div>
