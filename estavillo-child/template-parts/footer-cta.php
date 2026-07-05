<?php
/**
 * Footer CTA — "Let's talk" + banda de intersección.
 *
 * COPY PLACEHOLDER. El email real se define vía el filtro
 * 'es_contact_email' (Code Snippets) — mientras esté vacío se muestra
 * el tag pendiente, siguiendo la convención {pending} de los mockups.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_cta_title = apply_filters( 'es_home_cta_title', "Let's <em>talk.</em>" );
$es_cta_lead  = apply_filters(
	'es_home_cta_lead',
	"I'm selectively open to product design opportunities with operational depth — especially systems, workflows and AI-assisted products."
);
$es_email     = apply_filters( 'es_contact_email', '' );

$es_intersection = apply_filters(
	'es_home_intersection',
	array( 'Systems Thinking', 'Product Design', 'AI-Assisted Workflows', 'Operations' )
);
?>

<section class="es-section es-footer-cta" id="connect">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num">02</span>
				<h2 class="es-label"><?php echo esc_html( es__( 'cta_label' ) ); ?></h2>
			</div>
		</div>

		<p class="es-footer-cta__title" data-es-reveal>
			<?php echo wp_kses( $es_cta_title, array( 'em' => array() ) ); ?>
		</p>
		<p class="es-footer-cta__lead" data-es-reveal style="--es-reveal-delay: 90ms">
			<?php echo esc_html( $es_cta_lead ); ?>
		</p>

		<div data-es-reveal style="--es-reveal-delay: 180ms">
			<?php if ( ! empty( $es_email ) ) : ?>
				<a class="es-footer-cta__email" href="mailto:<?php echo esc_attr( antispambot( $es_email ) ); ?>">
					<?php echo esc_html( antispambot( $es_email ) ); ?>
				</a>
			<?php else : ?>
				<span class="es-placeholder__tag">{pending: email &rarr; filtro es_contact_email}</span>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="es-intersection">
	<div class="es-container">
		<div class="es-intersection__row">
			<span class="es-label"><?php esc_html_e( 'I work at the intersection of', 'estavillo-child' ); ?></span>
			<?php foreach ( $es_intersection as $es_item ) : ?>
				<span class="es-intersection__item">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
						<circle cx="7" cy="7" r="6" stroke="currentColor" stroke-width="1.1" />
						<circle cx="7" cy="7" r="2" fill="currentColor" />
					</svg>
					<?php echo esc_html( $es_item ); ?>
				</span>
			<?php endforeach; ?>
		</div>
	</div>
</section>
