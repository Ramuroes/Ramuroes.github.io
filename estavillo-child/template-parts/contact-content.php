<?php
/**
 * Connect — contenido de fallback de templates/page-contact.php.
 *
 * Revisión (ticket Connect — revision): reemplaza el diseño de filas tipo
 * botón (icono+label+valor, cada fila un core/button) por la composición
 * aprobada — introducción editorial "Let's talk." + lista de contacto a la
 * izquierda, formulario a la derecha, en un core/columns real de 2
 * columnas (42/58). Deliberadamente usa las MISMAS clases que
 * docs/content/connect-gutenberg-en.html (.wp-block-columns/.wp-block-
 * column, .es-contact-intro, .es-contact-list, .es-contact-item,
 * .es-contact-form) — la primera migración usaba <ul>/<li>/<a> acá y
 * core/buttons en Gutenberg bajo los mismos nombres de clase, lo que causó
 * una regresión real (icono de Location duplicado, ver historial de git).
 * Un solo shape de markup simple para los dos elimina esa categoría de
 * riesgo.
 *
 * Solo 5 métodos de contacto (antes 6): Email, WhatsApp, LinkedIn,
 * Instagram, Location. Phone ya NO se muestra como fila separada — mostrar
 * el mismo número dos veces (Phone y WhatsApp) fue uno de los problemas
 * reportados; es_contact_phone() se mantiene en functions.php por si una
 * página futura lo necesita, pero esta página ya no lo lee.
 *
 * Eyebrow/título/intro NO viven acá — los sigue imprimiendo page-head.php
 * siempre ("Preserve the approved page-head system", pedido explícito del
 * ticket de revisión), tanto si esta página cae al fallback como si ya
 * tiene contenido Gutenberg real (mismo criterio que About/How I Work: el
 * eyebrow+H1+lead de cabecera nunca es parte del cuerpo migrable).
 *
 * Fuente de verdad (ticket de revisión, sección 9): esta plantilla SOLO se
 * renderiza cuando la Page todavía no tiene contenido Gutenberg real — una
 * vez que lo tiene, el contenido estático de connect-gutenberg-en.html es
 * la fuente de verdad visible y editar estos campos de Portfolio Content
 * no cambia lo que ve el visitante. Ver la nota en el admin (home-content-
 * options.php, sección "Connect page (dedicated)").
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_email      = es_contact_email();
$es_whatsapp   = es_contact_whatsapp();
$es_country    = apply_filters( 'es_connect_country', 'Uruguay' );
$es_status     = apply_filters( 'es_connect_status', 'Based in Uruguay — open to remote and international roles.' );
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
?>

<section class="es-section es-contact-page" id="connect">
	<div class="es-container">
		<div class="wp-block-columns" data-es-reveal>
			<div class="wp-block-column" style="flex-basis:42%;flex-grow:0">
				<div class="es-contact-intro">
					<h2 class="es-h2 es-contact-intro__heading"><?php esc_html_e( "Let's", 'estavillo-child' ); ?> <em class="es-accent-word"><?php esc_html_e( 'talk.', 'estavillo-child' ); ?></em></h2>
					<p class="es-contact-intro__lead"><?php esc_html_e( "If that's what you're building, I'd like to hear about it.", 'estavillo-child' ); ?></p>
					<?php if ( ! empty( $es_status ) ) : ?>
						<p class="es-contact-intro__availability"><?php echo esc_html( $es_status ); ?></p>
					<?php endif; ?>
				</div>

				<div class="es-contact-list">
					<div class="es-contact-item es-contact-item--email">
						<p class="es-contact-item__label"><?php esc_html_e( 'Email', 'estavillo-child' ); ?></p>
						<p class="es-contact-item__value"><a href="mailto:<?php echo esc_attr( antispambot( $es_email ) ); ?>"><?php echo esc_html( antispambot( $es_email ) ); ?></a></p>
					</div>
					<div class="es-contact-item es-contact-item--whatsapp">
						<p class="es-contact-item__label"><?php esc_html_e( 'WhatsApp', 'estavillo-child' ); ?></p>
						<p class="es-contact-item__value"><a href="https://wa.me/<?php echo esc_attr( es_phone_digits( $es_whatsapp ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $es_whatsapp ); ?></a></p>
					</div>
					<div class="es-contact-item es-contact-item--linkedin">
						<p class="es-contact-item__label"><?php esc_html_e( 'LinkedIn', 'estavillo-child' ); ?></p>
						<?php if ( ! empty( $es_linkedin_url ) ) : ?>
							<p class="es-contact-item__value"><a href="<?php echo esc_url( $es_linkedin_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View profile', 'estavillo-child' ); ?></a></p>
						<?php else : ?>
							<p class="es-contact-item__value es-placeholder__tag"><?php esc_html_e( '{pending: URL}', 'estavillo-child' ); ?></p>
						<?php endif; ?>
					</div>
					<div class="es-contact-item es-contact-item--instagram">
						<p class="es-contact-item__label"><?php esc_html_e( 'Instagram', 'estavillo-child' ); ?></p>
						<?php if ( ! empty( $es_instagram_url ) ) : ?>
							<p class="es-contact-item__value"><a href="<?php echo esc_url( $es_instagram_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View profile', 'estavillo-child' ); ?></a></p>
						<?php else : ?>
							<p class="es-contact-item__value es-placeholder__tag"><?php esc_html_e( '{pending: URL}', 'estavillo-child' ); ?></p>
						<?php endif; ?>
					</div>
					<div class="es-contact-item es-contact-item--location">
						<p class="es-contact-item__label"><?php esc_html_e( 'Location', 'estavillo-child' ); ?></p>
						<p class="es-contact-item__value"><?php echo esc_html( $es_country ); ?></p>
					</div>
				</div>
			</div>

			<div class="wp-block-column" style="flex-basis:58%;flex-grow:0">
				<div class="es-contact-form">
					<h2 class="es-h2 es-contact-form__heading"><?php esc_html_e( 'Send a', 'estavillo-child' ); ?> <em class="es-accent-word"><?php esc_html_e( 'message.', 'estavillo-child' ); ?></em></h2>
					<p class="es-contact-form__lead"><?php esc_html_e( 'I usually reply within a few working days.', 'estavillo-child' ); ?></p>
					<?php
					/**
					 * FORM PLACEHOLDER — target is Fluent Forms Free (see
					 * docs/FLUENT-FORMS.md for setup steps). Once a form
					 * exists, replace the markup below with either the
					 * Fluent Forms Gutenberg block's rendered output or
					 * echo do_shortcode( '[fluentform id="1"]' ). No
					 * plugin is installed yet — this is a clearly-marked
					 * placeholder only.
					 */
					?>
					<div class="es-contact-form-placeholder">
						<p class="es-label"><?php esc_html_e( 'Form placeholder', 'estavillo-child' ); ?></p>
						<p class="es-contact-form-placeholder__note"><?php esc_html_e( 'Name, Email, Message, Submit — this will be replaced by a Fluent Forms block once the plugin is installed and a form is created.', 'estavillo-child' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
