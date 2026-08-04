<?php
/**
 * Plugin Name: Estavillo Portfolio Core
 * Description: Editable content for the Estavillo portfolio — the Case Study CPT (Selected Work, Featured Case), a Home Content options page (About, How I Work, Connect, Header, Footer), and the "Estavillo Case Study" Gutenberg block library + Presupuestador patterns for editing cases visually. Decoupled from the theme via WordPress filters, so Home always falls back to its placeholder content if this plugin is deactivated or a field is left empty.
 * Version: 1.5.27
 * Author: Ramiro Estavillo
 * Text Domain: estavillo-portfolio-core
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ES_PORTFOLIO_CORE_VERSION', '1.5.27' );
define( 'ES_PORTFOLIO_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ES_PORTFOLIO_CORE_URI', plugin_dir_url( __FILE__ ) );

require ES_PORTFOLIO_CORE_DIR . 'includes/case-study-cpt.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/home-content-options.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/polylang-compat.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/case-blocks.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/case-patterns.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/about-blocks.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/how-i-work-patterns.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/how-i-work-blocks.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/home-blocks.php';
require ES_PORTFOLIO_CORE_DIR . 'includes/home-patterns.php';

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
