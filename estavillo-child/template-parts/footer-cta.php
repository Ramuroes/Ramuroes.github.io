<?php
/**
 * Connect — CTA de contacto "Let's talk." (sección 05).
 *
 * COPY PLACEHOLDER (Home v4). El email real se define vía el filtro
 * 'es_contact_email'; mientras esté vacío se muestra el tag {pending}.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_num       = isset( $args['num'] ) ? $args['num'] : '05';
$es_cta_title = apply_filters( 'es_home_cta_title', "Let's <em>talk.</em>" );
$es_cta_lead  = apply_filters(
	'es_home_cta_lead',
	"I'm open to Product Design, Design Systems and UX Research roles — anywhere the goal is making a real system work better, not just look better. If that's what you're building, I'd like to hear about it."
);
$es_email       = es_contact_email();
$es_connect_url = apply_filters( 'es_home_connect_url', '#connect' );
?>

<section class="es-section es-footer-cta" id="connect">
	<div class="es-container">
		<div class="es-section-head" data-es-reveal>
			<div class="es-section-head__title">
				<span class="es-section-head__num"><?php echo esc_html( $es_num ); ?></span>
				<h2 class="es-label"><?php echo esc_html( es__( 'cta_label' ) ); ?></h2>
			</div>
		</div>

		<p class="es-footer-cta__title" data-es-reveal>
			<?php echo wp_kses( $es_cta_title, array( 'em' => array() ) ); ?>
		</p>
		<p class="es-footer-cta__lead" data-es-reveal style="--es-reveal-delay: 90ms">
			<?php echo esc_html( $es_cta_lead ); ?>
		</p>

		<div class="es-footer-cta__actions" data-es-reveal style="--es-reveal-delay: 180ms">
			<?php if ( ! empty( $es_email ) ) : ?>
				<a class="es-footer-cta__email" href="mailto:<?php echo esc_attr( antispambot( $es_email ) ); ?>">
					<?php echo esc_html( antispambot( $es_email ) ); ?>
				</a>
			<?php else : ?>
				<span class="es-placeholder__tag">{pending: email &rarr; filtro es_contact_email}</span>
			<?php endif; ?>
			<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_connect_url ); ?>">
				<?php esc_html_e( 'All ways to connect', 'estavillo-child' ); ?>
				<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
			</a>
		</div>
	</div>
</section>
