<?php
/**
 * Connect — CTA de contacto "Let's talk." (sección 05).
 *
 * El email real se define vía el filtro 'es_contact_email'; mientras esté
 * vacío se muestra el tag {pending}. Home migration ticket: sumó un
 * segundo enlace opcional a WhatsApp (es_contact_whatsapp(), el mismo
 * campo/número que ya usa la página Connect dedicada — no un "Phone"
 * nuevo ni independiente) junto al email, solo si el campo tiene valor.
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
$es_whatsapp    = es_contact_whatsapp();
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
			<div class="es-footer-cta__contacts">
				<?php if ( ! empty( $es_email ) ) : ?>
					<a class="es-footer-cta__email" href="mailto:<?php echo esc_attr( antispambot( $es_email ) ); ?>">
						<?php echo esc_html( antispambot( $es_email ) ); ?>
					</a>
				<?php else : ?>
					<span class="es-placeholder__tag">{pending: email &rarr; filtro es_contact_email}</span>
				<?php endif; ?>
				<?php if ( ! empty( $es_whatsapp ) ) : ?>
					<?php // Correction ticket §9: se muestra el número internacional visible (link WhatsApp), no la etiqueta "WhatsApp". ?>
					<a class="es-footer-cta__phone" href="https://wa.me/<?php echo esc_attr( es_phone_digits( $es_whatsapp ) ); ?>" target="_blank" rel="noopener">
						<?php echo esc_html( $es_whatsapp ); ?>
					</a>
				<?php endif; ?>
			</div>
			<a class="es-link-arrow es-link-arrow--quiet" href="<?php echo esc_url( $es_connect_url ); ?>">
				<?php esc_html_e( 'All ways to connect', 'estavillo-child' ); ?>
			</a>
		</div>
	</div>
</section>
