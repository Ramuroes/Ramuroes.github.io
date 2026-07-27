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
 * Sprint 4C suma: un índice sticky opcional (campo "Case index" del meta
 * box — manual, "Label|#anchor" por línea, ver docs) y la librería de
 * clases .es-case-* (case-study.css) para estructurar contenido dentro del
 * editor nativo — ver README para el listado completo con ejemplos.
 *
 * Sprint 4D reordena el hero como grilla editorial de 2 columnas en
 * desktop (texto a la izquierda, imagen/placeholder a la derecha) —
 * mismos campos de siempre, solo cambia el markup/CSS del hero. En mobile
 * sigue apilado (imagen debajo del texto). No toca .es-case-* del cuerpo.
 *
 * Mega sprint (hero options + breadcrumbs): el hero ahora admite 4 layouts
 * seleccionables por caso (campo "Hero layout" del meta box — ver
 * es_case_hero_layout_choices() en el plugin), vía una clase modificadora
 * sobre .es-case__hero; el default 'split-right' es exactamente el markup/
 * CSS de Sprint 4D, sin clase extra, cero riesgo para casos existentes.
 * Suma también una tira de breadcrumbs (Home / Work / título del caso)
 * arriba del índice sticky.
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

			// Layout del hero: 'split-right' (default histórico, Sprint 4D) no
			// suma clase modificadora — cero riesgo para casos ya publicados.
			$es_hero_layout       = get_post_meta( $es_case_id, '_es_case_hero_layout', true );
			$es_hero_layout_class = ( $es_hero_layout && 'split-right' !== $es_hero_layout ) ? ' es-case__hero--' . sanitize_html_class( $es_hero_layout ) : '';

			// Breadcrumbs: Home / Work / título del caso (helper compartido,
			// ver functions.php → es_breadcrumb_trail() — también usado por
			// las 4 páginas fijas).
			$es_breadcrumb_trail = es_breadcrumb_trail( 'nav_work', get_the_title() );

			// Índice sticky: manual, "Label|#anchor" por línea (campo "Case
			// index" del meta box). Si está vacío, el índice no se imprime.
			$es_case_index_raw = get_post_meta( $es_case_id, '_es_case_index', true );
			$es_case_index     = array();
			if ( ! empty( $es_case_index_raw ) ) {
				foreach ( preg_split( '/\r\n|\r|\n/', $es_case_index_raw ) as $es_index_line ) {
					$es_index_line = trim( $es_index_line );
					if ( '' === $es_index_line || false === strpos( $es_index_line, '|' ) ) {
						continue;
					}
					list( $es_index_label, $es_index_href ) = array_map( 'trim', explode( '|', $es_index_line, 2 ) );
					if ( '' !== $es_index_label && '' !== $es_index_href ) {
						$es_case_index[] = array(
							'label' => $es_index_label,
							'href'  => $es_index_href,
						);
					}
				}
			}
			?>
			<?php get_template_part( 'template-parts/breadcrumbs', null, array( 'trail' => $es_breadcrumb_trail ) ); ?>
			<?php if ( ! empty( $es_case_index ) ) : ?>
				<nav class="es-case-index" aria-label="<?php echo esc_attr( es__( 'case_sections_aria' ) ); ?>">
					<div class="es-case-index__inner">
						<?php foreach ( $es_case_index as $es_index_item ) : ?>
							<a class="es-case-index__link" href="<?php echo esc_url( $es_index_item['href'] ); ?>"><?php echo esc_html( $es_index_item['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</nav>
			<?php endif; ?>
			<article class="es-section es-case">
				<div class="es-container es-case__hero<?php echo esc_attr( $es_hero_layout_class ); ?>" data-es-reveal>
					<div class="es-case__hero-content">
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

					<div class="es-case__hero-media">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="es-case__media">
								<?php the_post_thumbnail( 'large' ); ?>
							</div>
						<?php else : ?>
							<div class="es-placeholder es-case__media es-case__placeholder" role="img" aria-label="<?php echo esc_attr( es__( 'case_media_ph_aria' ) ); ?>">
								<span class="es-placeholder__tag">{asset: <?php echo esc_html( sanitize_title( get_the_title() ) ); ?>}</span>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<?php
				// es-container--case: el body del caso usa el container ancho
				// del sistema editorial (1320px, spec Grid System v1). El hero
				// de arriba y el chrome del sitio quedan en 1140 a propósito.
				?>
				<div class="es-container es-container--case">
					<?php
					// Sin data-es-reveal acá, a propósito: el body es UN solo
					// elemento de miles de px de alto, y un IntersectionObserver
					// con threshold > 0 puede no dispararse nunca para un
					// elemento más alto que el viewport (ratio máx = viewport /
					// alto total < threshold) — el contenido quedaba invisible
					// en producción. El contenido del caso nunca depende del
					// sistema de reveal; el hero (elemento chico) sí anima.
					?>
					<div class="es-case__body">
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
