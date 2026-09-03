<?php
/**
 * Resultados de búsqueda — chrome ESTAVILLO en vez del template de Kadence.
 *
 * Ver template-parts/generic-document.php para el porqué. El H1 lleva el
 * término buscado entre comillas; el patrón sale de es__() para que sea
 * traducible como cualquier otra cadena de interfaz (%s = término).
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$es_search_term = get_search_query();

get_template_part(
	'template-parts/generic-document',
	null,
	array(
		'body_class' => 'es-search-page',
		'eyebrow'    => es__( 'search_eyebrow' ),
		'title'      => sprintf( es__( 'search_title' ), esc_html( $es_search_term ) ),
		'mode'       => 'list',
		'empty'      => es__( 'search_empty' ),
	)
);
