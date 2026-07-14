<?php
/**
 * Render dinámico de estavillo/case-ladder.
 *
 * Estados → clases: 'active' → --active, 'done' → --done, 'future' → chip
 * base (sin modificador). Mismo mapa que el editor.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_steps = isset( $attributes['steps'] ) && is_array( $attributes['steps'] ) ? $attributes['steps'] : array();
$es_steps = array_filter(
	$es_steps,
	function ( $es_step ) {
		return is_array( $es_step ) && '' !== trim( (string) ( $es_step['label'] ?? '' ) );
	}
);

if ( empty( $es_steps ) ) {
	return;
}
?>
<ol <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-ladder' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php
	foreach ( $es_steps as $es_step ) :
		$es_state = (string) ( $es_step['state'] ?? 'future' );
		$es_class = 'es-case-ladder__step';
		if ( 'active' === $es_state ) {
			$es_class .= ' es-case-ladder__step--active';
		} elseif ( 'done' === $es_state ) {
			$es_class .= ' es-case-ladder__step--done';
		}
		?>
		<li class="<?php echo esc_attr( $es_class ); ?>"><?php echo wp_kses_post( (string) ( $es_step['label'] ?? '' ) ); ?></li>
	<?php endforeach; ?>
</ol>
