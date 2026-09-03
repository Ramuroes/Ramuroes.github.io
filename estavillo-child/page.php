<?php
/**
 * Página sin template del theme — chrome ESTAVILLO en vez del de Kadence.
 *
 * Alcance: SÓLO las páginas guardadas sin ninguno de los templates propios.
 * Las seis páginas del portfolio (Home / Work / About / How I Work / Connect,
 * más el single de Case Study) tienen su template asignado y la jerarquía de
 * WordPress lo elige antes que este archivo, así que ninguna de ellas pasa
 * por acá. Este template existe para la página nueva que se crea mañana:
 * antes salía con el chrome viejo de Kadence, ahora nace dentro del sistema.
 *
 * La cabecera es la misma caja "Page header" del resto del sitio
 * (es_page_hero_args, inc/page-hero-meta.php): eyebrow, título y bajada
 * editables por página y por idioma, con el título de la página como default.
 * El cuerpo es the_content() dentro de .es-page, que es exactamente como ya
 * se renderiza el cuerpo de About / Connect / How I Work.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_template_part(
	'template-parts/generic-document',
	null,
	array_merge(
		array( 'body_class' => 'es-generic-page', 'mode' => 'content' ),
		es_page_hero_args()
	)
);
