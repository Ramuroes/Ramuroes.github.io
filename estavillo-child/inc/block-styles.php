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

	/**
	 * About-page Gutenberg migration: opt-in lightweight style for the
	 * existing estavillo/case-details block (accessible native
	 * <details>/<summary>, already used by Case Studies). Case Studies
	 * keep their current bordered/filled look — this is a NEW, separate
	 * style choice in the block's own Styles panel, not a change to the
	 * block's default appearance. CSS lives in assets/css/pages.css
	 * (`.es-case-details.is-style-light`), the same stylesheet already
	 * scoped to the 4 fixed pages — not in case-study.css, so About never
	 * needs to load that stylesheet just for this one block style.
	 * register_block_style() only records the style association (it
	 * doesn't require the block type to already be registered), so this
	 * is harmlessly inert if the "Estavillo Portfolio Core" plugin is
	 * ever deactivated — same "no fatal if the plugin is off" contract
	 * as the rest of the theme/plugin boundary.
	 */
	register_block_style(
		'estavillo/case-details',
		array(
			'name'  => 'light',
			'label' => __( 'Light (no box, hairline only)', 'estavillo-child' ),
		)
	);
}
add_action( 'init', 'es_child_register_block_styles' );
