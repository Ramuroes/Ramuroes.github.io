<?php
/**
 * Entrada individual (post de blog, o cualquier CPT sin template propio) —
 * chrome ESTAVILLO en vez del template de Kadence.
 *
 * Alcance: NO afecta a los Case Studies. Ese CPT tiene su propio
 * single-es_case_study.php y la jerarquía de WordPress lo elige antes que
 * este archivo. Es el único CPT que registra el plugin (ver
 * estavillo-portfolio-core/includes/case-study-cpt.php), así que en la
 * práctica esto cubre las entradas del blog nativo.
 *
 * El cuerpo es the_content() dentro de .es-page, exactamente como ya se
 * renderiza el cuerpo de un Case Study: el contenido Gutenberg de la entrada
 * hereda el mismo sistema tipográfico y de color que el resto del sitio, sin
 * ninguna hoja nueva.
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
		'body_class' => 'es-single-page',
		'title'      => get_the_title(),
		'mode'       => 'content',
	)
);
