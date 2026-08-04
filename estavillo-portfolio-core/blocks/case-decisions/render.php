<?php
/**
 * Render dinámico de estavillo/case-decisions.
 *
 * Cada card es un razonamiento en dos pasos, no una fila de tabla: un
 * <dl> para el hallazgo, un conector puramente decorativo, y un segundo
 * <dl> para la decisión de diseño — dos grupos dt/dd separados, no uno
 * solo con cuatro hijos. Eso es lo que permite diferenciarlos de verdad
 * (color de label, borde de acento en la decisión) en vez de que "queden
 * con el mismo peso visual" por compartir una sola fila de grilla.
 *
 * El orden en el DOM ya es hallazgo → decisión (dt antes que su dd en
 * cada <dl>), así que un lector de pantalla encuentra la etiqueta antes
 * que su párrafo sin necesitar aria-label extra.
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
		if ( ! is_array( $es_item ) || false === ( $es_item['visible'] ?? true ) ) {
			return false;
		}
		return '' !== trim( (string) ( $es_item['title'] ?? '' ) ) || '' !== trim( (string) ( $es_item['task'] ?? '' ) );
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
		<?php
		$es_has_task      = '' !== trim( (string) ( $es_item['task'] ?? '' ) );
		$es_has_guardrail = '' !== trim( (string) ( $es_item['guardrail'] ?? '' ) );
		?>
		<div class="es-case-decision" tabindex="0">
			<div class="es-case-decision__head">
				<?php if ( '' !== trim( (string) ( $es_item['num'] ?? '' ) ) ) : ?>
					<div class="es-case-decision__num"><?php echo wp_kses_post( (string) $es_item['num'] ); ?></div>
				<?php endif; ?>
				<?php if ( '' !== trim( (string) ( $es_item['title'] ?? '' ) ) ) : ?>
					<h3 class="es-case-decision__title"><?php echo wp_kses_post( (string) $es_item['title'] ); ?></h3>
				<?php endif; ?>
			</div>
			<?php if ( $es_has_task ) : ?>
				<dl class="es-case-decision__pair es-case-decision__pair--finding">
					<dt><?php echo esc_html( $es_task_label ); ?></dt>
					<dd><?php echo wp_kses_post( (string) $es_item['task'] ); ?></dd>
				</dl>
			<?php endif; ?>
			<?php if ( $es_has_task && $es_has_guardrail ) : ?>
				<div class="es-case-decision__connector" aria-hidden="true"></div>
			<?php endif; ?>
			<?php if ( $es_has_guardrail ) : ?>
				<dl class="es-case-decision__pair es-case-decision__pair--decision">
					<dt><?php echo esc_html( $es_guardrail_label ); ?></dt>
					<dd><?php echo wp_kses_post( (string) $es_item['guardrail'] ); ?></dd>
				</dl>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
