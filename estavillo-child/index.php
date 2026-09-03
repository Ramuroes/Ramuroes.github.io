<?php
/**
 * Fallback final de la jerarquía de templates de WordPress.
 *
 * WordPress exige que un theme tenga index.php, y es lo que sirve cuando
 * ningún template más específico coincide. Hasta la iteración de cierre este
 * child no tenía uno, así que ese caso caía al index.php de Kadence, con el
 * menú viejo y sin el sistema visual (ver template-parts/generic-document.php).
 *
 * Ojo: tener index.php acá NO desvía single.php ni page.php del padre — la
 * jerarquía de WP prueba primero el template específico en el child Y en el
 * padre, y sólo después baja a index.php. Este archivo cubre exactamente lo
 * que quedaba suelto, ni un caso más.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part(
	'template-parts/generic-document',
	null,
	array(
		'body_class' => 'es-index-page',
		'eyebrow'    => es__( 'archive_eyebrow' ),
		'title'      => wp_strip_all_tags( get_the_archive_title() ),
		'mode'       => 'list',
		'empty'      => es__( 'archive_empty' ),
	)
);
