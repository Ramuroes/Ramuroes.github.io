<?php
/**
 * Render dinámico de estavillo/selected-work.
 *
 * Sin atributos configurables — qué casos aparecen y en qué orden se
 * deciden en cada Case Study (casilla "Show in Home → Selected Work" +
 * el campo nativo "Order"). Delega en es_home_selected_work_source() y
 * es_work_media() (child theme, inc/selected-work-fallback.php) — las
 * MISMAS funciones que ya usaba template-parts/selected-work.php — así ni
 * el fallback placeholder ni el markup/clases (.es-work__*, .es-card*,
 * .es-grid--2, ya estilados por estavillo-child/assets/css/pages-home.css)
 * se duplican. Mismo criterio de "plugin llama función del theme, guardado
 * con function_exists()" que estavillo/featured-case.
 *
 * El encabezado de sección (número + título + CTA "All work") NO vive en
 * este bloque — es contenido Gutenberg plano al lado, editable como
 * cualquier otro texto. Este bloque renderiza solo la card ancha + grilla.
 *
 * @package estavillo-portfolio-core
 * @var array    $attributes Atributos del bloque (ninguno usado).
 * @var string   $content    Sin uso (bloque hoja).
 * @var WP_Block $block      Instancia del bloque.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'es_home_selected_work_source' ) || ! function_exists( 'es_work_media' ) ) {
	return;
}

$es_cases = apply_filters( 'es_home_selected_work', es_home_selected_work_source() );

if ( empty( $es_cases ) ) {
	return;
}

$es_wide = array_shift( $es_cases );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'es-work__stack' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- ya escapado por core. ?>>
	<?php if ( $es_wide ) : ?>
		<a class="es-card es-card--wide" href="<?php echo esc_url( $es_wide['url'] ); ?>">
			<div class="es-card__media"><?php es_work_media( $es_wide ); ?></div>
			<div class="es-card__body">
				<div class="es-card__meta">
					<?php if ( ! empty( $es_wide['label'] ) ) : ?>
						<span class="es-card__label"><?php echo esc_html( $es_wide['label'] ); ?></span>
					<?php endif; ?>
					<span class="es-card__kicker"><?php echo esc_html( $es_wide['kicker'] ); ?></span>
				</div>
				<div class="es-card__title"><?php echo esc_html( $es_wide['title'] ); ?></div>
				<div class="es-card__excerpt"><?php echo esc_html( $es_wide['excerpt'] ); ?></div>
				<?php if ( ! empty( $es_wide['tags'] ) ) : ?>
					<div class="es-card__tags">
						<?php foreach ( $es_wide['tags'] as $es_tag ) : ?>
							<span class="es-pill"><?php echo esc_html( $es_tag ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<span class="es-card__cta"><?php esc_html_e( 'View case study', 'estavillo-portfolio-core' ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
			</div>
		</a>
	<?php endif; ?>

	<?php if ( ! empty( $es_cases ) ) : ?>
		<div class="es-grid es-grid--2">
			<?php foreach ( $es_cases as $es_case ) : ?>
				<a class="es-card" href="<?php echo esc_url( $es_case['url'] ); ?>">
					<div class="es-card__media"><?php es_work_media( $es_case ); ?></div>
					<div class="es-card__body">
						<div class="es-card__meta">
							<?php if ( ! empty( $es_case['label'] ) ) : ?>
								<span class="es-card__label"><?php echo esc_html( $es_case['label'] ); ?></span>
							<?php endif; ?>
							<span class="es-card__kicker"><?php echo esc_html( $es_case['kicker'] ); ?></span>
						</div>
						<div class="es-card__title"><?php echo esc_html( $es_case['title'] ); ?></div>
						<div class="es-card__excerpt"><?php echo esc_html( $es_case['excerpt'] ); ?></div>
						<span class="es-card__cta"><?php esc_html_e( 'View case study', 'estavillo-portfolio-core' ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
