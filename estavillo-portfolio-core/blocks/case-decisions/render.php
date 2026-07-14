<?php
/**
 * Render dinámico de estavillo/case-decisions.
 *
 * Misma estructura que el HTML manual: un solo <dl class="es-case-decision__row">
 * por card con los dos pares dt/dd (tarea y resguardo).
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$es_items = array_filter(
	$es_items,
	function ( $es_item ) {
		return is_array( $es_item ) && ( '' !== trim( (string) ( $es_item['title'] ?? '' ) ) || '' !== trim( (string) ( $es_item['task'] ?? '' ) ) );
	}
);

if ( empty( $es_items ) ) {
	return;
}

$es_task_label      = trim( (string) ( $attributes['taskLabel'] ?? 'Task' ) );
$es_guardrail_label = trim( (string) ( $attributes['guardrailLabel'] ?? 'Guardrail' ) );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-case-decisions' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php foreach ( $es_items as $es_item ) : ?>
		<div class="es-case-decision">
			<?php if ( '' !== trim( (string) ( $es_item['num'] ?? '' ) ) ) : ?>
				<div class="es-case-decision__num"><?php echo wp_kses_post( (string) $es_item['num'] ); ?></div>
			<?php endif; ?>
			<?php if ( '' !== trim( (string) ( $es_item['title'] ?? '' ) ) ) : ?>
				<h3 class="es-case-decision__title"><?php echo wp_kses_post( (string) $es_item['title'] ); ?></h3>
			<?php endif; ?>
			<dl class="es-case-decision__row">
				<?php if ( '' !== trim( (string) ( $es_item['task'] ?? '' ) ) ) : ?>
					<dt><?php echo esc_html( $es_task_label ); ?></dt>
					<dd><?php echo wp_kses_post( (string) $es_item['task'] ); ?></dd>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) ( $es_item['guardrail'] ?? '' ) ) ) : ?>
					<dt><?php echo esc_html( $es_guardrail_label ); ?></dt>
					<dd><?php echo wp_kses_post( (string) $es_item['guardrail'] ); ?></dd>
				<?php endif; ?>
			</dl>
		</div>
	<?php endforeach; ?>
</div>
