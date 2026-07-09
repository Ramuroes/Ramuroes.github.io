<?php
/**
 * Plugin Name: Estavillo Portfolio Core
 * Description: Case Study content type for the Estavillo portfolio (Selected Work) — CPT, tags taxonomy, and admin fields. Decoupled from the theme via the `es_portfolio_case_studies_for_home` filter, so the Home page falls back to placeholder content automatically if this plugin is deactivated or has no published cases.
 * Version: 1.0.0
 * Author: Ramiro Estavillo
 * Text Domain: estavillo-portfolio-core
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ES_PORTFOLIO_CORE_VERSION', '1.0.0' );
define( 'ES_PORTFOLIO_CORE_DIR', plugin_dir_path( __FILE__ ) );

require ES_PORTFOLIO_CORE_DIR . 'includes/case-study-cpt.php';

/**
 * Al activar: registra el CPT/taxonomía antes de flushear, así las reglas
 * de reescritura (permalinks de un caso individual) quedan listas sin
 * pasos manuales en Ajustes → Enlaces permanentes.
 */
function es_portfolio_core_activate() {
	es_register_case_study_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'es_portfolio_core_activate' );

/**
 * Al desactivar: limpia las reglas de reescritura que agregamos.
 */
function es_portfolio_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'es_portfolio_core_deactivate' );
