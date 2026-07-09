<?php
/**
 * Single Case Study — template standalone que reusa el chrome ESTAVILLO.
 *
 * Sprint 4B. WordPress resuelve este archivo automáticamente por la
 * jerarquía de templates (single-{post_type}.php) para cualquier post del
 * CPT es_case_study — no requiere registro ni un filtro template_include.
 * Si el plugin "Estavillo Portfolio Core" está inactivo, el CPT no existe
 * y esta URL no se resuelve en absoluto (nada "cae" a este archivo sin el
 * CPT registrado), así que no hace falta ningún fallback acá.
 *
 * Standalone igual que templates/page-home-estavillo.php: imprime su
 * propio wp_head()/wp_footer() pero NO usa el header/footer de Kadence —
 * reusa el chrome propio de ESTAVILLO (site-header + site-footer).
 *
 * Deliberadamente simple (Sprint 4B): título, eyebrow/kicker, extracto,
 * tags, imagen destacada, status/role/tools/period (todos opcionales) y
 * el contenido del editor estándar de WordPress vía the_content() — nada
 * de campos custom para el cuerpo. No hay case builder acá.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'es-page es-case-study' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>

<div id="es-page" class="es-page">

	<?php get_template_part( 'template-parts/site-header' ); ?>

	<main id="top" class="es-main">
		<?php
		while ( have_posts() ) :
			the_post();

			$es_case_id      = get_the_ID();
			$es_case_kicker  = get_post_meta( $es_case_id, '_es_case_kicker', true );
			$es_case_excerpt = get_the_excerpt();
			$es_case_tags    = get_the_terms( $es_case_id, 'es_case_tag' );
			if ( ! is_array( $es_case_tags ) ) {
				$es_case_tags = array();
			}

			$es_case_meta = array_filter(
				array(
					'status' => get_post_meta( $es_case_id, '_es_case_label', true ),
					'role'   => get_post_meta( $es_case_id, '_es_case_role', true ),
					'tools'  => get_post_meta( $es_case_id, '_es_case_tools', true ),
					'period' => get_post_meta( $es_case_id, '_es_case_period', true ),
				)
			);
			?>
			<article class="es-section es-case">
				<div class="es-container es-case__head" data-es-reveal>
					<?php if ( ! empty( $es_case_kicker ) ) : ?>
						<p class="es-eyebrow es-case__kicker"><?php echo esc_html( $es_case_kicker ); ?></p>
					<?php endif; ?>

					<h1 class="es-h1 es-case__title"><?php the_title(); ?></h1>

					<?php if ( ! empty( $es_case_excerpt ) ) : ?>
						<p class="es-lead es-case__excerpt"><?php echo esc_html( $es_case_excerpt ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $es_case_tags ) ) : ?>
						<div class="es-case__tags">
							<?php foreach ( $es_case_tags as $es_tag ) : ?>
								<span class="es-pill"><?php echo esc_html( $es_tag->name ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $es_case_meta ) ) : ?>
						<dl class="es-case__meta">
							<?php foreach ( $es_case_meta as $es_meta_key => $es_meta_value ) : ?>
								<div class="es-case__meta-item">
									<dt><?php echo esc_html( es__( 'case_meta_' . $es_meta_key ) ); ?></dt>
									<dd><?php echo esc_html( $es_meta_value ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
				</div>

				<div class="es-container">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="es-case__media" data-es-reveal>
							<?php the_post_thumbnail( 'large' ); ?>
						</div>
					<?php else : ?>
						<div class="es-placeholder es-case__media es-case__placeholder" role="img" aria-label="<?php esc_attr_e( 'Placeholder for the case visual', 'estavillo-child' ); ?>" data-es-reveal>
							<span class="es-placeholder__tag">{asset: <?php echo esc_html( sanitize_title( get_the_title() ) ); ?>}</span>
						</div>
					<?php endif; ?>

					<div class="es-case__body" data-es-reveal>
						<?php the_content(); ?>
					</div>
				</div>
			</article>
		<?php endwhile; ?>
	</main>

	<?php get_template_part( 'template-parts/site-footer' ); ?>

</div>

<?php wp_footer(); ?>
</body>
</html>
