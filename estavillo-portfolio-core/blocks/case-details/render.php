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

/*
 * Nombre accesible del <summary>, independiente del texto visible.
 *
 * Por qué existe: en "Cómo trabajo" hay SEIS acordeones y los seis dicen lo
 * mismo ("Más sobre este paso"). Visualmente funciona, porque cada uno está
 * debajo del título de su paso y el ojo ve esa relación. Un lector de
 * pantalla no: al listar los controles de la página devuelve seis entradas
 * idénticas, sin forma de saber cuál abre qué. Es el caso de manual de
 * WCAG 2.4.6 (Headings and Labels) y 2.4.9 (Link Purpose, Link Only).
 *
 * La solución NO es alargar el texto visible —repetir el título del paso en
 * cada acordeón ensucia la lectura de la página— sino darle a cada control un
 * nombre accesible propio. aria-label va en el <summary>, que es el elemento
 * que el usuario enfoca y activa, no en el <details> contenedor.
 *
 * Vacío = comportamiento de siempre: sin atributo, el nombre accesible sale
 * del texto visible. Ningún acordeón ya guardado cambia de HTML.
 */
$es_aria = isset( $attributes['ariaLabel'] ) ? trim( wp_strip_all_tags( (string) $attributes['ariaLabel'] ) ) : '';
?>
<details <?php echo get_block_wrapper_attributes( array( 'class' => $es_classes ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<summary<?php echo '' !== $es_aria ? ' aria-label="' . esc_attr( $es_aria ) . '"' : ''; ?>><?php echo '' !== $es_summary ? wp_kses_post( $es_summary ) : esc_html__( 'Detalles', 'estavillo-portfolio-core' ); ?></summary>
	<div class="es-case-details__body">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- InnerBlocks ya renderizados/sanitizados por core. ?>
	</div>
</details>
