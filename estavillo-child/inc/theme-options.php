<?php
/**
 * Opciones del tema en el Customizer (Apariencia → Personalizar → Estavillo).
 *
 * Controles:
 *  - es_accent_color         : green | orange       (acento de todo el sitio)
 *  - es_hero_variant_desktop : system_map | static_fallback
 *  - es_hero_variant_mobile  : system_map_subtle | static_fallback
 *
 * @package estavillo-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valores permitidos por control (fuente única de verdad para sanitizar).
 *
 * @return array
 */
function es_theme_option_choices() {
	return array(
		'es_accent_color'         => array(
			'green'  => __( 'Green (default)', 'estavillo-child' ),
			'orange' => __( 'Orange', 'estavillo-child' ),
		),
		'es_hero_variant_desktop' => array(
			'system_map_nodes' => __( 'System map · nodes (default)', 'estavillo-child' ),
			'blueprint_flow'   => __( 'Blueprint flow · inputs → decide → resolve', 'estavillo-child' ),
			'static_fallback'  => __( 'Static fallback', 'estavillo-child' ),
		),
		'es_hero_variant_mobile'  => array(
			'system_map_subtle' => __( 'System map · subtle (default)', 'estavillo-child' ),
			'blueprint_flow'    => __( 'Blueprint flow · simplified', 'estavillo-child' ),
			'static_fallback'   => __( 'Static fallback', 'estavillo-child' ),
		),
	);
}

/**
 * Defaults de cada opción.
 *
 * @return array
 */
function es_theme_option_defaults() {
	return array(
		'es_accent_color'         => 'green',
		'es_hero_variant_desktop' => 'system_map_nodes',
		'es_hero_variant_mobile'  => 'system_map_subtle',
	);
}

/**
 * Lee una opción del tema con default y whitelist.
 *
 * @param string $key Nombre de la opción.
 * @return string
 */
function es_get_option( $key ) {
	$defaults = es_theme_option_defaults();
	$choices  = es_theme_option_choices();
	$value    = get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
	if ( isset( $choices[ $key ] ) && ! array_key_exists( $value, $choices[ $key ] ) ) {
		return $defaults[ $key ];
	}
	return $value;
}

/**
 * Sanitiza contra la whitelist del control.
 *
 * @param string               $value   Valor entrante.
 * @param WP_Customize_Setting $setting Setting del Customizer.
 * @return string
 */
function es_sanitize_choice( $value, $setting ) {
	$choices  = es_theme_option_choices();
	$defaults = es_theme_option_defaults();
	$key      = $setting->id;
	if ( isset( $choices[ $key ] ) && array_key_exists( $value, $choices[ $key ] ) ) {
		return $value;
	}
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Registra sección y controles en el Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function es_customize_register( $wp_customize ) {
	$defaults = es_theme_option_defaults();
	$choices  = es_theme_option_choices();

	$wp_customize->add_section(
		'es_theme_options',
		array(
			'title'       => __( 'Estavillo', 'estavillo-child' ),
			'description' => __( 'Opciones del portfolio: acento de color y variantes del hero animado.', 'estavillo-child' ),
			'priority'    => 30,
		)
	);

	$controls = array(
		'es_accent_color'         => __( 'Accent color', 'estavillo-child' ),
		'es_hero_variant_desktop' => __( 'Desktop hero variant', 'estavillo-child' ),
		'es_hero_variant_mobile'  => __( 'Mobile hero variant', 'estavillo-child' ),
	);

	foreach ( $controls as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $defaults[ $key ],
				'type'              => 'theme_mod',
				'sanitize_callback' => 'es_sanitize_choice',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => 'es_theme_options',
				'type'    => 'select',
				'choices' => $choices[ $key ],
			)
		);
	}
}
add_action( 'customize_register', 'es_customize_register' );

/**
 * Clases en <body> según opciones: acento y variantes de hero.
 * El CSS resuelve el resto vía custom properties (--es-accent).
 *
 * @param string[] $classes Clases actuales.
 * @return string[]
 */
function es_body_classes( $classes ) {
	$classes[] = 'es-accent--' . es_get_option( 'es_accent_color' );
	return $classes;
}
add_filter( 'body_class', 'es_body_classes' );
