<?php
/**
 * Connect — contenido de fallback de templates/page-contact.php.
 *
 * Redesign (ticket Connect): la página se migra al mismo patrón explícito
 * de fallback que About/How I Work (ver page-contact.php) — este
 * template-part SOLO se renderiza cuando la Page de WordPress todavía no
 * tiene contenido Gutenberg real. Reemplaza la grilla anterior de 3
 * columnas (Email / Location / Elsewhere) por la estructura pedida: una
 * lista de métodos de contacto (ícono + label + valor, fila completa
 * clickeable donde corresponde) a la izquierda y un placeholder de
 * formulario a la derecha, en desktop.
 *
 * Eyebrow/título/intro NO viven acá — los sigue imprimiendo page-head.php
 * siempre, tanto si esta página cae al fallback como si ya tiene
 * contenido Gutenberg real (mismo criterio que About/How I Work: el
 * eyebrow+H1+lead de cabecera nunca es parte del cuerpo migrable).
 *
 * Campos nuevos de este ticket: es_contact_phone, es_contact_whatsapp
 * (functions.php), es_connect_country, es_social_links ahora también con
 * clave 'Instagram'. es_contact_email y es_connect_status (repurpuesto
 * como "Availability text") ya existían.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_email      = es_contact_email();
$es_phone      = es_contact_phone();
$es_whatsapp   = es_contact_whatsapp();
$es_country    = apply_filters( 'es_connect_country', 'Uruguay' );
$es_status     = apply_filters( 'es_connect_status', '' );
$es_social     = apply_filters(
	'es_social_links',
	array(
		'LinkedIn'  => '',
		'Behance'   => '',
		'Instagram' => '',
	)
);
$es_linkedin_url  = $es_social['LinkedIn'] ?? '';
$es_instagram_url = $es_social['Instagram'] ?? '';
// Cualquier otra red (hoy: Behance) se muestra aparte, como enlace
// secundario — no es uno de los 6 métodos primarios de este ticket.
$es_secondary_social = array_diff_key( $es_social, array_flip( array( 'LinkedIn', 'Instagram' ) ) );
?>

<section class="es-section es-contact-page" id="connect">
	<div class="es-container">
		<?php if ( ! empty( $es_status ) ) : ?>
			<div class="es-contact-page__status" data-es-reveal>
				<span class="es-status-pill"><span class="es-live-dot" aria-hidden="true"></span><?php echo esc_html( $es_status ); ?></span>
			</div>
		<?php endif; ?>

		<div class="es-contact-page__layout" data-es-reveal style="--es-reveal-delay: 90ms">
			<div class="es-contact-page__methods">
				<ul class="es-contact-methods">
					<li class="es-contact-methods__item">
						<a class="es-contact-method" href="mailto:<?php echo esc_attr( antispambot( $es_email ) ); ?>">
							<span class="es-contact-method__icon" aria-hidden="true"><?php echo wp_kses( es_connect_icon_svg( 'email' ), es_icon_svg_kses_rules() ); ?></span>
							<span class="es-contact-method__text">
								<span class="es-contact-method__label"><?php esc_html_e( 'Email', 'estavillo-child' ); ?></span>
								<span class="es-contact-method__value"><?php echo esc_html( antispambot( $es_email ) ); ?></span>
							</span>
						</a>
					</li>
					<li class="es-contact-methods__item">
						<a class="es-contact-method" href="tel:+<?php echo esc_attr( es_phone_digits( $es_phone ) ); ?>">
							<span class="es-contact-method__icon" aria-hidden="true"><?php echo wp_kses( es_connect_icon_svg( 'phone' ), es_icon_svg_kses_rules() ); ?></span>
							<span class="es-contact-method__text">
								<span class="es-contact-method__label"><?php esc_html_e( 'Phone', 'estavillo-child' ); ?></span>
								<span class="es-contact-method__value"><?php echo esc_html( $es_phone ); ?></span>
							</span>
						</a>
					</li>
					<li class="es-contact-methods__item">
						<a class="es-contact-method" href="https://wa.me/<?php echo esc_attr( es_phone_digits( $es_whatsapp ) ); ?>" target="_blank" rel="noopener">
							<span class="es-contact-method__icon" aria-hidden="true"><?php echo wp_kses( es_connect_icon_svg( 'whatsapp' ), es_icon_svg_kses_rules() ); ?></span>
							<span class="es-contact-method__text">
								<span class="es-contact-method__label"><?php esc_html_e( 'WhatsApp', 'estavillo-child' ); ?></span>
								<span class="es-contact-method__value"><?php echo esc_html( $es_whatsapp ); ?></span>
							</span>
						</a>
					</li>
					<li class="es-contact-methods__item">
						<?php if ( ! empty( $es_linkedin_url ) ) : ?>
							<a class="es-contact-method" href="<?php echo esc_url( $es_linkedin_url ); ?>" target="_blank" rel="noopener">
								<span class="es-contact-method__icon" aria-hidden="true"><?php echo wp_kses( es_connect_icon_svg( 'linkedin' ), es_icon_svg_kses_rules() ); ?></span>
								<span class="es-contact-method__text">
									<span class="es-contact-method__label"><?php esc_html_e( 'LinkedIn', 'estavillo-child' ); ?></span>
									<span class="es-contact-method__value"><?php esc_html_e( 'View profile', 'estavillo-child' ); ?></span>
								</span>
							</a>
						<?php else : ?>
							<span class="es-contact-method es-contact-method--pending">
								<span class="es-contact-method__icon" aria-hidden="true"><?php echo wp_kses( es_connect_icon_svg( 'linkedin' ), es_icon_svg_kses_rules() ); ?></span>
								<span class="es-contact-method__text">
									<span class="es-contact-method__label"><?php esc_html_e( 'LinkedIn', 'estavillo-child' ); ?></span>
									<span class="es-contact-method__value es-placeholder__tag"><?php esc_html_e( '{pending: URL}', 'estavillo-child' ); ?></span>
								</span>
							</span>
						<?php endif; ?>
					</li>
					<li class="es-contact-methods__item">
						<?php if ( ! empty( $es_instagram_url ) ) : ?>
							<a class="es-contact-method" href="<?php echo esc_url( $es_instagram_url ); ?>" target="_blank" rel="noopener">
								<span class="es-contact-method__icon" aria-hidden="true"><?php echo wp_kses( es_connect_icon_svg( 'instagram' ), es_icon_svg_kses_rules() ); ?></span>
								<span class="es-contact-method__text">
									<span class="es-contact-method__label"><?php esc_html_e( 'Instagram', 'estavillo-child' ); ?></span>
									<span class="es-contact-method__value"><?php esc_html_e( 'View profile', 'estavillo-child' ); ?></span>
								</span>
							</a>
						<?php else : ?>
							<span class="es-contact-method es-contact-method--pending">
								<span class="es-contact-method__icon" aria-hidden="true"><?php echo wp_kses( es_connect_icon_svg( 'instagram' ), es_icon_svg_kses_rules() ); ?></span>
								<span class="es-contact-method__text">
									<span class="es-contact-method__label"><?php esc_html_e( 'Instagram', 'estavillo-child' ); ?></span>
									<span class="es-contact-method__value es-placeholder__tag"><?php esc_html_e( '{pending: URL}', 'estavillo-child' ); ?></span>
								</span>
							</span>
						<?php endif; ?>
					</li>
					<li class="es-contact-methods__item">
						<span class="es-contact-method es-contact-method--static">
							<span class="es-contact-method__icon" aria-hidden="true"><?php echo wp_kses( es_connect_icon_svg( 'location' ), es_icon_svg_kses_rules() ); ?></span>
							<span class="es-contact-method__text">
								<span class="es-contact-method__label"><?php esc_html_e( 'Location', 'estavillo-child' ); ?></span>
								<span class="es-contact-method__value"><?php echo esc_html( $es_country ); ?></span>
							</span>
						</span>
					</li>
				</ul>

				<?php if ( ! empty( array_filter( $es_secondary_social ) ) ) : ?>
					<div class="es-contact-page__secondary">
						<?php foreach ( $es_secondary_social as $es_name => $es_url ) : ?>
							<?php if ( ! empty( $es_url ) ) : ?>
								<a class="es-link-arrow" href="<?php echo esc_url( $es_url ); ?>" target="_blank" rel="noopener">
									<?php echo esc_html( $es_name ); ?>
									<span class="es-link-arrow__icon" aria-hidden="true">&rarr;</span>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="es-contact-page__form">
				<?php
				/**
				 * FORM PLACEHOLDER — replace the markup below with your
				 * form plugin's shortcode or block once one is chosen
				 * (e.g. echo do_shortcode( '[contact-form-7 id="..."]' ),
				 * or paste a real Gutenberg form block if this page has
				 * real content). No plugin is installed yet — this is a
				 * clearly-marked placeholder only.
				 */
				?>
				<div class="es-contact-form-placeholder">
					<p class="es-label"><?php esc_html_e( 'Send a message', 'estavillo-child' ); ?></p>
					<p class="es-contact-form-placeholder__note"><?php esc_html_e( 'Contact form — Name, Email, Message, Submit. Insert your form plugin’s shortcode or block here.', 'estavillo-child' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>
