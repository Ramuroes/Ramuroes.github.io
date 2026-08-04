<?php
/**
 * Render dinámico de estavillo/how-i-work-illustration.
 *
 * Delega 100% al renderer compartido del child theme
 * (es_how_work_illustration_svg(), inc/how-i-work-illustrations.php) — el
 * bloque nunca guarda ni duplica markup SVG, solo el paso + tres flags de
 * presentación. Si el child theme no está activo (esa función no existe) o
 * el paso/archivo no resuelve a nada, el bloque no imprime nada — mismo
 * criterio de degradación que estavillo/hobby-list.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'es_how_work_illustration_svg' ) ) {
	return;
}

$es_step = isset( $attributes['step'] ) ? (int) $attributes['step'] : 1;
if ( $es_step < 1 || $es_step > 6 ) {
	$es_step = 1;
}

$es_context      = isset( $attributes['context'] ) && 'home' === $attributes['context'] ? 'home' : 'page';
$es_show_accents = ! isset( $attributes['showAccents'] ) || (bool) $attributes['showAccents'];
$es_decorative   = ! isset( $attributes['decorative'] ) || (bool) $attributes['decorative'];

$es_markup = es_how_work_illustration_svg(
	$es_step,
	array(
		'context'      => $es_context,
		'show_accents' => $es_show_accents,
		'decorative'   => $es_decorative,
	)
);

if ( '' === $es_markup ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php echo $es_markup; // phpcs:ignore WordPress.Security.EscapeOutput -- generado por es_how_work_illustration_svg(), ya sanitizado ahí (ver inc/how-i-work-illustrations.php). ?>
</div>
