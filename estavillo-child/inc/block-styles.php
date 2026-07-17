<?php
/**
 * Variaciones de estilo de bloques nativos (CSS-only, sin bloques nuevos).
 *
 * Fase 0 de la segunda revisión de arquitectura (auditoría de artefactos
 * Trazur, ver README.md "Patterns Phase 0"): "Checkmark List" es una style variation
 * registrada sobre core/list — agrega la clase `is-style-checkmark` al
 * bloque cuando el editor la elige desde el panel de Estilos, nada más.
 * No cambia el bullet por defecto de ninguna lista existente (opt-in, no
 * global) y no agrega ningún bloque nuevo. El CSS real vive en
 * assets/css/case-study.css (`.es-case__body ul.is-style-checkmark`) —
 * solo se aplica dentro de un Case Study, igual que el resto de la
 * librería .es-case-*.
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra las variaciones de estilo de bloques nativos del child theme.
 */
function es_child_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}

	register_block_style(
		'core/list',
		array(
			'name'  => 'checkmark',
			'label' => __( 'Checkmark List', 'estavillo-child' ),
		)
	);
}
add_action( 'init', 'es_child_register_block_styles' );
