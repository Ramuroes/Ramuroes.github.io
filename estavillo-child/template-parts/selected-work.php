<?php
/**
 * Selected work — grilla de casos destacados.
 *
 * CONTENIDO PLACEHOLDER: los tres casos por defecto salen de los mockups.
 * Editable sin tocar markup vía el filtro 'es_home_selected_work'
 * (Code Snippets), o más adelante conectando un CPT/páginas de caso.
 * Si 'image' es null se muestra el marco placeholder del design system.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_cases = apply_filters(
	'es_home_selected_work',
	array(
		array(
			'num'     => '01',
			'kicker'  => 'Product + System Design · In progress',
			'title'   => 'Decision System for Estimating in Metal Workshops',
			'excerpt' => 'An AI-assisted system that reduces quoting time, improves accuracy and brings consistency to the entire team.',
			'url'     => '#',
			'image'   => null,
		),
		array(
			'num'     => '02',
			'kicker'  => 'UX Research · Service Design',
			'title'   => 'Trazur Platform Redesign',
			'excerpt' => 'UX research and service design for an e-learning platform about livestock traceability.',
			'url'     => '#',
			'image'   => null,
		),
		array(
			'num'     => '03',
			'kicker'  => 'Academic UX Case · Google UX Certificate',
			'title'   => 'French Bakery (Academic Case)',
			'excerpt' => 'UX case project developed during the Google UX Design Certificate.',
			'url'     => '#',
			'image'   => null,
		),
	)
);

$es_view_all_url = apply_filters( 'es_home_view_all_url', '#' );
?>

<section class="es-section es-work" id="work">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">01</span>
				<h2 class="es-label"><?php echo esc_html( es__( 'work_label' ) ); ?></h2>
			</div>
			<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_view_all_url ); ?>">
				<?php echo esc_html( es__( 'work_view_all' ) ); ?>
				<span aria-hidden="true">&rarr;</span>
			</a>
		</div>

		<div class="es-grid es-grid--3">
			<?php foreach ( $es_cases as $es_i => $es_case ) : ?>
				<a
					class="es-card"
					href="<?php echo esc_url( $es_case['url'] ); ?>"
					data-es-reveal
					style="--es-reveal-delay: <?php echo esc_attr( $es_i * 90 ); ?>ms"
				>
					<div class="es-card__media">
						<?php if ( ! empty( $es_case['image'] ) ) : ?>
							<img src="<?php echo esc_url( $es_case['image'] ); ?>" alt="" loading="lazy" />
						<?php else : ?>
							<span class="es-placeholder__tag">{asset: <?php echo esc_html( sanitize_title( $es_case['title'] ) ); ?>}</span>
						<?php endif; ?>
					</div>
					<div class="es-card__body">
						<div class="es-card__num"><?php echo esc_html( $es_case['num'] ); ?></div>
						<div class="es-card__kicker"><?php echo esc_html( $es_case['kicker'] ); ?></div>
						<div class="es-card__title"><?php echo esc_html( $es_case['title'] ); ?></div>
						<div class="es-card__excerpt"><?php echo esc_html( $es_case['excerpt'] ); ?></div>
						<span class="es-card__cta">
							<?php echo esc_html( es__( 'work_view_case' ) ); ?>
							<span aria-hidden="true">&nearr;</span>
						</span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
