<?php
/**
 * Contact — contenido completo de templates/page-contact.php.
 *
 * Mismos datos "Connect" de siempre (es_home_cta_title / es_home_cta_lead /
 * es_contact_email — Sprint 3, editables en Home Content) más los mismos
 * datos de Footer (es_social_links / es_footer_location — también Sprint
 * 3), más 2 campos opcionales nuevos de esta página (es_connect_note,
 * es_connect_status — sprint de infra/polish). Ningún filtro de los de
 * siempre cambió de nombre/shape.
 *
 * Jerarquía deliberada (fix del sprint de infra/polish — antes "Contact."
 * (page-head, escala H1) y "Let's talk." (escala footer-cta, más grande
 * TODAVÍA que H1) competían una arriba de la otra por el mismo peso
 * visual). Ahora: page-head da el único título de página-escala-H1
 * ("Contact."); acá abajo "Let's talk." es una frase de acompañamiento a
 * escala menor (.es-contact-page__statement, entre H2 y lead) — apoya al
 * título, no compite con él.
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
$es_note   = apply_filters( 'es_connect_note', '' );
$es_status = apply_filters( 'es_connect_status', '' );
$es_email  = es_contact_email();
$es_social = apply_filters(
	'es_social_links',
	array(
		'LinkedIn' => '',
		'Behance'  => '',
	)
);
$es_place = apply_filters( 'es_footer_location', 'Montevideo, Uruguay' );
?>

<section class="es-section es-contact-page" id="connect">
	<div class="es-container">
		<p class="es-contact-page__statement" data-es-reveal>
			<?php echo wp_kses( $es_cta_title, array( 'em' => array() ) ); ?>
		</p>
		<p class="es-lead es-contact-page__lead" data-es-reveal style="--es-reveal-delay: 90ms">
			<?php echo esc_html( $es_cta_lead ); ?>
		</p>

		<?php if ( ! empty( $es_status ) || ! empty( $es_note ) ) : ?>
			<div class="es-contact-page__meta" data-es-reveal style="--es-reveal-delay: 120ms">
				<?php if ( ! empty( $es_status ) ) : ?>
					<span class="es-status-pill"><span class="es-live-dot" aria-hidden="true"></span><?php echo esc_html( $es_status ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $es_note ) ) : ?>
					<p class="es-contact-page__note"><?php echo esc_html( $es_note ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="es-contact-page__grid" data-es-reveal style="--es-reveal-delay: 150ms">
			<div class="es-contact-page__block">
				<span class="es-label"><?php esc_html_e( 'Email', 'estavillo-child' ); ?></span>
				<?php if ( ! empty( $es_email ) ) : ?>
					<a class="es-footer-cta__email" href="mailto:<?php echo esc_attr( antispambot( $es_email ) ); ?>">
						<?php echo esc_html( antispambot( $es_email ) ); ?>
					</a>
				<?php else : ?>
					<span class="es-placeholder__tag">{pending: email}</span>
				<?php endif; ?>
			</div>

			<div class="es-contact-page__block">
				<span class="es-label"><?php esc_html_e( 'Location', 'estavillo-child' ); ?></span>
				<p class="es-body"><?php echo esc_html( $es_place ); ?></p>
			</div>

			<div class="es-contact-page__block">
				<span class="es-label"><?php esc_html_e( 'Elsewhere', 'estavillo-child' ); ?></span>
				<div class="es-contact-page__social">
					<?php foreach ( $es_social as $es_name => $es_url ) : ?>
						<?php if ( ! empty( $es_url ) ) : ?>
							<a class="es-link-arrow" href="<?php echo esc_url( $es_url ); ?>" target="_blank" rel="noopener">
								<?php echo esc_html( $es_name ); ?>
								<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
							</a>
						<?php else : ?>
							<span class="es-muted" title="<?php esc_attr_e( 'URL pending', 'estavillo-child' ); ?>"><?php echo esc_html( $es_name ); ?></span>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
