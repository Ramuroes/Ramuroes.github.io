<?php
/**
 * Archivos (categoría, etiqueta, fecha, autor, archivo de un CPT) — chrome
 * ESTAVILLO en vez del template de Kadence. Ver
 * template-parts/generic-document.php para el porqué.
 *
 * El título sale de get_the_archive_title() de WP, que ya devuelve el nombre
 * real del término/mes/autor. Se le quita el prefijo ("Categoría:", "Mes:")
 * porque el eyebrow ya dice de qué tipo de archivo se trata y repetirlo en el
 * H1 sonaba a panel de admin, no a portfolio.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'get_the_archive_title_prefix',
	function () {
		return '';
	}
);

get_template_part(
	'template-parts/generic-document',
	null,
	array(
		'body_class' => 'es-archive-page',
		'eyebrow'    => es__( 'archive_eyebrow' ),
		'title'      => wp_strip_all_tags( get_the_archive_title() ),
		'lead'       => wp_strip_all_tags( get_the_archive_description() ),
		'mode'       => 'list',
		'empty'      => es__( 'archive_empty' ),
	)
);
