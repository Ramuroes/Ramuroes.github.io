<?php
/**
 * Render dinámico de estavillo/home-hero.
 *
 * El copy llega YA renderizado en $content (InnerBlocks — eyebrow,
 * titular, párrafo, CTAs — editados inline en Gutenberg y guardados en
 * post_content). Este archivo solo emite el cascarón que NO debe ser
 * editable: la <section class="es-hero">, la capa del fondo animado
 * ([data-es-hero-map], la anima assets/js/hero-system-map.js del child
 * theme) y los data-attributes de variante desktop/mobile, que siguen
 * saliendo del Customizer vía es_get_option() (inc/theme-options.php del
 * child theme) — el sistema de variantes existente, sin duplicarlo ni
 * moverlo a Gutenberg. Si el child theme no está activo, caen a los
 * defaults históricos y la sección renderiza igual (sin animación, porque
 * el JS del hero es del theme — degradación limpia).
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    InnerBlocks ya renderizados (el copy del hero).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_hero_desktop = function_exists( 'es_get_option' ) ? es_get_option( 'es_hero_variant_desktop' ) : 'network_constellation';
$es_hero_mobile  = function_exists( 'es_get_option' ) ? es_get_option( 'es_hero_variant_mobile' ) : 'network_constellation_subtle';
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'es-hero' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?> data-hero-desktop="<?php echo esc_attr( $es_hero_desktop ); ?>" data-hero-mobile="<?php echo esc_attr( $es_hero_mobile ); ?>">
	<div class="es-hero__visual" data-es-hero-map aria-hidden="true"></div>

	<div class="es-container es-hero__inner">
		<div class="es-hero__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?></div>
	</div>
</section>
