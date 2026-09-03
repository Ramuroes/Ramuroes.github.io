<?php
/**
 * Render dinámico de estavillo/featured-case.
 *
 * Sin atributos configurables — la selección real de qué Case Study se
 * muestra vive 100% en la casilla "Feature this case on Home" del CPT
 * (estavillo-portfolio-core/includes/case-study-cpt.php). Este bloque
 * delega en es_home_featured_source() (child theme,
 * inc/featured-case-fallback.php) — la MISMA función que ya usaba
 * template-parts/featured-case.php — así ni el fallback placeholder ni el
 * markup/clases (.es-featured__*, ya estilado por
 * estavillo-child/assets/css/pages-home.css) se duplican. Llamar una
 * función del theme desde un bloque del plugin, guardado con
 * function_exists(), es el mismo criterio ya usado por
 * estavillo/how-i-work-illustration — el bloque solo puede insertarse si
 * este plugin está activo, así que el único riesgo real es que el CHILD
 * THEME esté inactivo, cubierto acá.
 *
 * El encabezado de sección (número + título + CTA "View all") NO vive en
 * este bloque — es contenido Gutenberg plano (Heading/Paragraph) al lado,
 * editable como cualquier otro texto. Este bloque renderiza solo la
 * grilla texto+media.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque (ninguno usado).
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'es_home_featured_source' ) ) {
	return;
}

$es_featured = apply_filters( 'es_home_featured', es_home_featured_source() );

if ( empty( $es_featured ) ) {
	return;
}

$es_featured_tag = ! empty( $es_featured['placeholder_label'] ) ? $es_featured['placeholder_label'] : 'featured';

// Featured Media (ticket "Featured media del Case Study"): mismo criterio
// que template-parts/featured-case.php — ver ese archivo para el porqué
// de la whitelist defensiva acá también.
$es_featured_layout = isset( $es_featured['layout'] ) ? $es_featured['layout'] : 'standard';
if ( ! in_array( $es_featured_layout, array( 'standard', 'wide', 'full' ), true ) ) {
	$es_featured_layout = 'standard';
}
$es_featured_grid_class = 'es-featured__grid es-featured__grid--' . $es_featured_layout;
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => $es_featured_grid_class ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<div class="es-featured__text">
		<?php if ( ! empty( $es_featured['kicker'] ) ) : ?>
			<div class="es-eyebrow es-featured__kicker"><?php echo esc_html( $es_featured['kicker'] ); ?></div>
		<?php endif; ?>
		<h3 class="es-featured__title">
			<?php echo wp_kses( $es_featured['title'], array( 'em' => array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- wp_kses() ya sanitiza. ?>
		</h3>
		<p class="es-featured__body"><?php echo esc_html( $es_featured['body'] ); ?></p>
		<?php if ( ! empty( $es_featured['source'] ) ) : ?>
			<p class="es-featured__source"><?php echo esc_html( $es_featured['source'] ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $es_featured['status'] ) ) : ?>
			<div class="es-featured__status">
				<span class="es-status-pill"><span class="es-live-dot" aria-hidden="true"></span><?php echo esc_html( $es_featured['status'] ); ?></span>
			</div>
		<?php endif; ?>
		<a class="es-link-arrow es-featured__cta" href="<?php echo esc_url( $es_featured['url'] ); ?>">
			<?php echo esc_html( es_portfolio_theme_string( 'featured_cta', __( 'Read the case study', 'estavillo-portfolio-core' ) ) ); ?>
		</a>
	</div>

	<div class="es-featured__media">
		<?php if ( function_exists( 'es_featured_has_media' ) && es_featured_has_media( $es_featured ) ) : ?>
			<?php es_render_featured_media( $es_featured ); ?>
		<?php elseif ( ! function_exists( 'es_featured_has_media' ) && ! empty( $es_featured['image'] ) ) : ?>
			<?php // Child theme sin inc/featured-media.php (versión vieja/inactiva): mismo fallback de siempre, sólo imagen. ?>
			<img src="<?php echo esc_url( $es_featured['image'] ); ?>" alt="" loading="lazy" />
		<?php else : ?>
			<div class="es-placeholder es-featured__placeholder" role="img" aria-label="<?php esc_attr_e( 'Placeholder for the featured case visual', 'estavillo-portfolio-core' ); ?>">
				<span class="es-placeholder__tag">{asset: <?php echo esc_html( $es_featured_tag ); ?>}</span>
			</div>
		<?php endif; ?>
	</div>
</div>
