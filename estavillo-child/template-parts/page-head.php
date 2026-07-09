<?php
/**
 * Page head — cabecera simple compartida por las páginas fijas nuevas
 * (Work / About / How I Work / Contact): eyebrow + h1 + lead opcional.
 *
 * No es un hero nuevo: reusa las mismas voces tipográficas de siempre
 * (.es-eyebrow, .es-h1, .es-lead — base.css, sin cambios), así que no hay
 * tipografía nueva para estas páginas. Recibe 'eyebrow', 'title' (admite
 * <em>) y 'lead' vía $args.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_ph_eyebrow = isset( $args['eyebrow'] ) ? $args['eyebrow'] : '';
$es_ph_title   = isset( $args['title'] ) ? $args['title'] : '';
$es_ph_lead    = isset( $args['lead'] ) ? $args['lead'] : '';
?>
<header class="es-section es-page-head">
	<div class="es-container" data-es-reveal>
		<?php if ( ! empty( $es_ph_eyebrow ) ) : ?>
			<p class="es-eyebrow es-page-head__eyebrow"><?php echo esc_html( $es_ph_eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( ! empty( $es_ph_title ) ) : ?>
			<h1 class="es-h1 es-page-head__title"><?php echo wp_kses( $es_ph_title, array( 'em' => array() ) ); ?></h1>
		<?php endif; ?>
		<?php if ( ! empty( $es_ph_lead ) ) : ?>
			<p class="es-lead es-page-head__lead"><?php echo esc_html( $es_ph_lead ); ?></p>
		<?php endif; ?>
	</div>
</header>
