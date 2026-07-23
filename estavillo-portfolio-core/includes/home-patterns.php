<?php
/**
 * Patterns de página — Home migration ticket.
 *
 * Un solo pattern: el teaser de How I Work en Home. Reusa la categoría
 * "Estavillo — Pages" ya registrada por how-i-work-patterns.php (mismo
 * slug 'estavillo-pages' — register_block_pattern_category() no falla si
 * se llama dos veces con el mismo slug, WP dedupe). No se registra un
 * "pattern de Home completo": igual que About/How I Work/Connect antes,
 * la fuente de verdad del contenido real es
 * docs/content/home-gutenberg-en.html, pegado directo en la página — un
 * pattern de página completa solo duplicaría eso sin necesidad.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra el pattern del teaser de How I Work en Home.
 */
function es_home_patterns_register() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'estavillo-pages',
		array( 'label' => __( 'Estavillo — Pages', 'estavillo-portfolio-core' ) )
	);

	register_block_pattern(
		'estavillo/home-how-i-work-teaser',
		array(
			'title'       => __( 'Home — How I Work teaser', 'estavillo-portfolio-core' ),
			'description' => __( 'Compact 3-up preview (Understand/Explore/Improve) with the approved illustrations — real Gutenberg equivalent of template-parts/how-i-work.php. Same copy, same classes, no CSS changes needed.', 'estavillo-portfolio-core' ),
			'categories'  => array( 'estavillo-pages' ),
			'postTypes'   => array( 'page' ),
			'content'     => require ES_PORTFOLIO_CORE_DIR . 'patterns/home-how-i-work-teaser.php',
		)
	);
}
add_action( 'init', 'es_home_patterns_register', 20 );
