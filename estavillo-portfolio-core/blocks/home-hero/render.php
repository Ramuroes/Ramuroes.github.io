<?php
/**
 * Render dinámico de estavillo/home-hero.
 *
 * El copy llega YA renderizado en $content (InnerBlocks — eyebrow,
 * titular, párrafo, grupo de CTAs — editados inline en Gutenberg y
 * guardados en post_content). Este archivo solo emite el cascarón que NO
 * debe ser editable: la <section class="es-hero">, la capa del fondo
 * animado ([data-es-hero-map], la anima assets/js/hero-system-map.js del
 * child theme), los data-attributes de variante desktop/mobile y el
 * indicador de scroll.
 *
 * Variante del fondo animado — PRECEDENCIA (documentada):
 *   1. Atributo del bloque (desktopVariant / mobileVariant), validado
 *      contra las variantes registradas para su contexto
 *      (es_hero_variant_choices()). Vacío = sin elección a nivel bloque.
 *   2. Ajuste del Customizer (es_get_option(), a su vez whitelisteado con
 *      su propio default seguro) — sigue existiendo como fallback global.
 *   3. Default hardcodeado seguro (si ni el theme está activo).
 * El motor JS del hero (hero-system-map.js) ya lee el data-attribute del
 * DOM ANTES que su config localizada, así que setear estos attrs acá es
 * todo lo que hace falta para que la variante elegida en el bloque maneje
 * la animación — sin tocar el JS ni duplicar el registro de variantes.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    InnerBlocks ya renderizados (el copy del hero).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_desktop_choices = function_exists( 'es_hero_variant_choices' ) ? es_hero_variant_choices( 'desktop' ) : array();
$es_mobile_choices  = function_exists( 'es_hero_variant_choices' ) ? es_hero_variant_choices( 'mobile' ) : array();

$es_attr_desktop = isset( $attributes['desktopVariant'] ) ? (string) $attributes['desktopVariant'] : '';
$es_attr_mobile  = isset( $attributes['mobileVariant'] ) ? (string) $attributes['mobileVariant'] : '';

if ( '' !== $es_attr_desktop && isset( $es_desktop_choices[ $es_attr_desktop ] ) ) {
	$es_hero_desktop = $es_attr_desktop;
} elseif ( function_exists( 'es_get_option' ) ) {
	$es_hero_desktop = es_get_option( 'es_hero_variant_desktop' );
} else {
	$es_hero_desktop = 'network_constellation';
}

if ( '' !== $es_attr_mobile && isset( $es_mobile_choices[ $es_attr_mobile ] ) ) {
	$es_hero_mobile = $es_attr_mobile;
} elseif ( function_exists( 'es_get_option' ) ) {
	$es_hero_mobile = es_get_option( 'es_hero_variant_mobile' );
} else {
	$es_hero_mobile = 'network_constellation_subtle';
}

// Scroll behavior — 'standard' (default, la transición existente sin
// cambios) o 'parallax' (opt-in: la capa del fondo animado queda sticky un
// tramo acotado mientras el copy sube y se desvanece, luego el fondo se va).
// Se emite como clase modificadora; toda la coreografía parallax vive en
// hero.css detrás de esa clase + prefers-reduced-motion + un breakpoint de
// desktop, así el default y mobile no cambian.
$es_scroll_mode = isset( $attributes['scrollMode'] ) && 'parallax' === $attributes['scrollMode'] ? 'parallax' : 'standard';
$es_hero_class  = 'es-hero es-hero--scroll-' . $es_scroll_mode;
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => $es_hero_class ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?> data-hero-desktop="<?php echo esc_attr( $es_hero_desktop ); ?>" data-hero-mobile="<?php echo esc_attr( $es_hero_mobile ); ?>" data-hero-scroll="<?php echo esc_attr( $es_scroll_mode ); ?>">
	<div class="es-hero__bg">
		<div class="es-hero__visual" data-es-hero-map aria-hidden="true"></div>
	</div>

	<div class="es-container es-hero__inner">
		<div class="es-hero__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?></div>
	</div>

	<a class="es-hero__scroll" href="#process" aria-label="<?php echo esc_attr( es_portfolio_theme_string( 'scroll_aria', __( 'Scroll to How I work', 'estavillo-portfolio-core' ) ) ); ?>">
		<span class="es-hero__scroll-label"><?php echo esc_html( es_portfolio_theme_string( 'scroll_label', __( 'Scroll', 'estavillo-portfolio-core' ) ) ); ?></span>
		<span class="es-hero__scroll-line" aria-hidden="true"></span>
	</a>
</section>
