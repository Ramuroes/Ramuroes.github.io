<?php
/**
 * Work — listado completo de Case Studies para templates/page-work.php.
 *
 * Dos grupos, claramente separados:
 *  - "Selected work": los mismos casos marcados "Show this case in Home →
 *    Selected Work" (sin campo nuevo, mismo flag que ya existía) — primer
 *    caso como card ancha, el resto en grilla de 2, igual lenguaje visual
 *    que el teaser de Home (selected-work.php).
 *  - "Archive": casos marcados explícitamente "no mostrar en Home" —
 *    grilla simple de cards más chicas, con su propio encabezado de
 *    sección, debajo de un separador claro. No se imprime en absoluto si
 *    está vacío (no hay archivo todavía).
 *
 * Fuente de datos: es_work_page_source() (inc/work-page-fallback.php).
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_data     = es_work_page_source();
$es_selected = $es_data['selected'];
$es_archive  = $es_data['archive'];
$es_wide     = array_shift( $es_selected );
?>

<?php if ( $es_wide || ! empty( $es_selected ) ) : ?>
<section class="es-section es-work" id="work">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">01</span>
				<h2 class="es-label"><?php echo esc_html( es__( 'work_label' ) ); ?></h2>
			</div>
		</div>

		<div class="es-work__stack">
			<?php if ( $es_wide ) : ?>
				<a class="es-card es-card--wide" href="<?php echo esc_url( $es_wide['url'] ); ?>" data-es-reveal>
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
						<span class="es-card__cta"><?php echo esc_html( es__( 'work_view_case' ) ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
					</div>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $es_selected ) ) : ?>
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
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( ! empty( $es_archive ) ) : ?>
<section class="es-section es-work-archive" id="archive">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">02</span>
				<h2 class="es-label"><?php echo esc_html( es__( 'work_archive_label' ) ); ?></h2>
			</div>
		</div>

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
						<span class="es-card__cta"><?php echo esc_html( es__( 'work_view_case' ) ); ?> <span class="es-card__arrow" aria-hidden="true">&rarr;</span></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>
