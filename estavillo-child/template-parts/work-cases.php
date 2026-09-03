<?php
/**
 * Work — jerarquía única para templates/page-work.php (ticket "Refine Work
 * archive hierarchy and featured logic").
 *
 *   01 — Featured Work   el caso marcado "Feature this case on Home"
 *   02 — Selected Work   el resto de los casos "Show in Home" (sin el featured)
 *   03 — More Work        Case Studies de archivo (CPT) + el contenido viejo
 *                          pegado en la página (SAMIC, French Bakery, webs
 *                          anteriores, industrial/3D/visual/motion)
 *
 * Product Design primero, el resto como archivo de trayectoria — nunca al
 * mismo nivel visual. Un caso aparece en UNA sola sección: el dato ya viene
 * así de es_work_page_source() (featured/selected/archive mutuamente
 * excluyentes, ver inc/work-page-fallback.php y, del lado del plugin,
 * es_portfolio_get_case_studies_for_work_page()).
 *
 * Numeración DINÁMICA, no fija: si no hay ningún caso featured hoy, "02 —
 * Selected Work" pasa a ser la primera sección visible y se numera "01" —
 * nunca hay un "02" sin ningún "01" arriba, que se lee como una sección
 * rota. Se calcula acá mismo, sección por sección, según lo que realmente
 * va a imprimirse.
 *
 * $args:
 *   'legacy' string  HTML ya renderizado (the_content() del propio Work
 *                     page, capturado en templates/page-work.php ANTES de
 *                     imprimir nada) — el contenido viejo del portfolio. Se
 *                     imprime tal cual, sin tocarlo: esta plantilla sólo
 *                     decide DÓNDE va, nunca reescribe qué hay adentro.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_data     = es_work_page_source();
$es_featured = $es_data['featured'];
$es_selected = $es_data['selected'];
$es_archive  = $es_data['archive'];
$es_legacy   = isset( $args['legacy'] ) ? trim( (string) $args['legacy'] ) : '';

$es_num = 0;

// Featured Media (ticket "Featured media del Case Study"): mismo criterio
// de whitelist defensiva que los templates de Home — ver
// template-parts/featured-case.php. Se resuelve acá arriba, una vez, en
// vez de adentro del if de abajo, sólo por prolijidad de la condición.
$es_featured_layout_class = '';
if ( ! empty( $es_featured ) ) {
	$es_featured_layout = isset( $es_featured['layout'] ) ? $es_featured['layout'] : 'standard';
	if ( 'wide' === $es_featured_layout ) {
		$es_featured_layout_class = ' es-card--featured-wide';
	} elseif ( 'full' === $es_featured_layout ) {
		$es_featured_layout_class = ' es-card--featured-full';
	}
}
?>

<?php if ( ! empty( $es_featured ) ) : ?>
	<?php $es_num++; ?>
	<section class="es-section es-work es-work--featured" id="featured-work">
		<div class="es-container">
			<div class="es-section-head" data-es-reveal>
				<div class="es-section-head__title">
					<span class="es-section-head__num"><?php echo esc_html( sprintf( '%02d', $es_num ) ); ?></span>
					<h2 class="es-label"><?php echo esc_html( es__( 'work_featured_label' ) ); ?></h2>
				</div>
			</div>

			<a class="es-card es-card--wide<?php echo esc_attr( $es_featured_layout_class ); ?>" href="<?php echo esc_url( $es_featured['url'] ); ?>" data-es-reveal>
				<div class="es-card__media">
					<?php if ( function_exists( 'es_featured_has_media' ) && es_featured_has_media( $es_featured ) ) : ?>
						<?php es_render_featured_media( $es_featured ); ?>
					<?php else : ?>
						<?php es_work_media( $es_featured ); ?>
					<?php endif; ?>
				</div>
				<div class="es-card__body">
					<div class="es-card__meta">
						<?php if ( ! empty( $es_featured['label'] ) ) : ?>
							<span class="es-card__label"><?php echo esc_html( $es_featured['label'] ); ?></span>
						<?php endif; ?>
						<span class="es-card__kicker"><?php echo esc_html( $es_featured['kicker'] ); ?></span>
					</div>
					<div class="es-card__title"><?php echo esc_html( $es_featured['title'] ); ?></div>
					<div class="es-card__excerpt"><?php echo esc_html( $es_featured['excerpt'] ); ?></div>
					<?php if ( ! empty( $es_featured['tags'] ) ) : ?>
						<div class="es-card__tags">
							<?php foreach ( $es_featured['tags'] as $es_tag ) : ?>
								<span class="es-pill"><?php echo esc_html( $es_tag ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<span class="es-card__cta"><?php echo esc_html( es__( 'work_view_case' ) ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
				</div>
			</a>
		</div>
	</section>
<?php endif; ?>

<?php if ( ! empty( $es_selected ) ) : ?>
	<?php $es_num++; ?>
	<section class="es-section es-work" id="work">
		<div class="es-container">
			<div class="es-section-head" data-es-reveal>
				<div class="es-section-head__title">
					<span class="es-section-head__num"><?php echo esc_html( sprintf( '%02d', $es_num ) ); ?></span>
					<h2 class="es-label"><?php echo esc_html( es__( 'work_label' ) ); ?></h2>
				</div>
			</div>

			<div class="es-grid es-grid--2">
				<?php foreach ( $es_selected as $es_i => $es_case ) : ?>
					<a class="es-card" href="<?php echo esc_url( $es_case['url'] ); ?>" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( $es_i * 90 ); ?>ms">
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
							<span class="es-card__cta"><?php echo esc_html( es__( 'work_view_case' ) ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( ! empty( $es_archive ) || '' !== $es_legacy ) : ?>
	<?php $es_num++; ?>
	<section class="es-section es-work-archive" id="archive">
		<div class="es-container">
			<div class="es-section-head" data-es-reveal>
				<div class="es-section-head__title">
					<span class="es-section-head__num"><?php echo esc_html( sprintf( '%02d', $es_num ) ); ?></span>
					<h2 class="es-label"><?php echo esc_html( es__( 'work_archive_label' ) ); ?></h2>
				</div>
			</div>
			<?php $es_archive_lead = es__( 'work_archive_lead' ); ?>
			<?php if ( '' !== trim( $es_archive_lead ) ) : ?>
				<p class="es-lead es-work-archive__lead"><?php echo esc_html( $es_archive_lead ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $es_archive ) ) : ?>
				<div class="es-grid es-grid--3">
					<?php foreach ( $es_archive as $es_i => $es_case ) : ?>
						<a class="es-card" href="<?php echo esc_url( $es_case['url'] ); ?>" data-es-reveal style="--es-reveal-delay: <?php echo esc_attr( $es_i * 90 ); ?>ms">
							<div class="es-card__media"><?php es_work_media( $es_case ); ?></div>
							<div class="es-card__body">
								<div class="es-card__meta">
									<?php if ( ! empty( $es_case['label'] ) ) : ?>
										<span class="es-card__label"><?php echo esc_html( $es_case['label'] ); ?></span>
									<?php endif; ?>
									<span class="es-card__kicker"><?php echo esc_html( $es_case['kicker'] ); ?></span>
								</div>
								<div class="es-card__title"><?php echo esc_html( $es_case['title'] ); ?></div>
								<?php if ( ! empty( $es_case['category'] ) ) : ?>
									<div class="es-card__tags">
										<span class="es-pill"><?php echo esc_html( $es_case['category'] ); ?></span>
									</div>
								<?php endif; ?>
								<span class="es-card__cta"><?php echo esc_html( es__( 'work_view_case' ) ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $es_legacy ) : ?>
				<?php
				/*
				 * Contenido viejo del portfolio, sin tocar (ver el comentario
				 * en templates/page-work.php sobre por qué se captura ahí y se
				 * imprime acá). .es-work-archive__legacy sólo contiene reglas de
				 * escala/espaciado en pages.css — nunca reescribe el markup ni
				 * borra nada de lo que el editor pegó.
				 */
				$es_legacy_class = 'es-work-archive__legacy';
				if ( ! empty( $es_archive ) ) {
					// Separador visual sólo cuando también hay cards de archivo
					// arriba — si el contenido viejo es lo único en la sección,
					// ya tiene el margin-top natural del section-head/lead.
					$es_legacy_class .= ' es-work-archive__legacy--after-grid';
				}
				?>
				<div class="<?php echo esc_attr( $es_legacy_class ); ?>">
					<?php echo $es_legacy; // phpcs:ignore WordPress.Security.EscapeOutput -- ya renderizado/sanitizado por the_content() en templates/page-work.php. ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>
