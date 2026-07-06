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
 * Registro de variantes de hero (extensible por filtro).
 *
 * Fuente única de verdad para el Customizer. Cada variante declara en qué
 * contextos aplica ('desktop' y/o 'mobile'). Para sumar una variante nueva
 * SIN editar el tema: registrar el motor JS en window.EstavilloHero y
 * agregar la clave acá vía el filtro 'es_hero_variants' (p. ej. desde Code
 * Snippets):
 *
 *   add_filter( 'es_hero_variants', function ( $v ) {
 *       $v['mi_variante'] = array(
 *           'label'    => 'Mi variante',
 *           'contexts' => array( 'desktop', 'mobile' ),
 *       );
 *       return $v;
 *   } );
 *
 * @return array<string,array{label:string,contexts:string[]}>
 */
function es_hero_variants() {
	$variants = array(
		'system_map_nodes'  => array(
			'label'    => __( 'System map · nodes (default)', 'estavillo-child' ),
			'contexts' => array( 'desktop' ),
		),
		'system_map_subtle' => array(
			'label'    => __( 'System map · subtle (default)', 'estavillo-child' ),
			'contexts' => array( 'mobile' ),
		),
		'blueprint_flow'    => array(
			'label'    => __( 'Blueprint flow · inputs → decide → resolve', 'estavillo-child' ),
			'contexts' => array( 'desktop', 'mobile' ),
		),
		'static_fallback'   => array(
			'label'    => __( 'Static fallback', 'estavillo-child' ),
			'contexts' => array( 'desktop', 'mobile' ),
		),
	);

	return apply_filters( 'es_hero_variants', $variants );
}

/**
 * Choices de hero para un contexto ('desktop' | 'mobile').
 *
 * @param string $context Contexto.
 * @return array<string,string> clave => label
 */
function es_hero_variant_choices( $context ) {
	$out = array();
	foreach ( es_hero_variants() as $key => $meta ) {
		$contexts = isset( $meta['contexts'] ) ? (array) $meta['contexts'] : array( 'desktop', 'mobile' );
		if ( in_array( $context, $contexts, true ) ) {
			$out[ $key ] = isset( $meta['label'] ) ? $meta['label'] : $key;
		}
	}
	return $out;
}

/**
 * Valores permitidos por control (fuente única de verdad para sanitizar).
 * Las variantes de hero salen del registro filtrable es_hero_variants().
 *
 * @return array
 */
function es_theme_option_choices() {
	return array(
		'es_accent_color'         => array(
			'green'  => __( 'Green (default)', 'estavillo-child' ),
			'orange' => __( 'Orange', 'estavillo-child' ),
		),
		'es_hero_variant_desktop' => es_hero_variant_choices( 'desktop' ),
		'es_hero_variant_mobile'  => es_hero_variant_choices( 'mobile' ),
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
