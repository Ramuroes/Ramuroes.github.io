<?php
/**
 * Pattern content — Home / How I Work teaser.
 *
 * Real Gutenberg equivalent of template-parts/how-i-work.php (the PHP
 * fallback, unchanged) — same classes, same copy
 * (es_home_process_teaser_defaults(), functions.php), same 3 illustrations
 * (steps 1/4/6, context "home") via the existing estavillo/how-i-work-
 * illustration block, so pages-home.css styles it identically with zero
 * new CSS.
 *
 * One structural change from the PHP version, deliberate: the 3-up row was
 * an <ol>/<li> in PHP, but core/list does not support nested blocks (an
 * illustration + heading + paragraph) inside a list item — and a plain
 * Group carrying the .es-process-teaser__row grid class does NOT survive
 * the real-WP cascade (same documented failure as Connect's original
 * two-column layout: custom CSS grid on a wp:group gets defeated by the
 * theme/layout-engine cascade and stacks vertically — confirmed on the
 * real site during the Home revision ticket). The proven fix is the same
 * one Connect used: REAL core/columns (native flex layout, native mobile
 * stacking), with the per-concept styling hung off a className on each
 * wp:column and the gap scoped via an ancestor selector in pages-home.css
 * — never a layout class on the columns block itself.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:group {"tagName":"section","anchor":"process","className":"es-section es-process-teaser","metadata":{"name":"How I Work teaser"}} -->
<section class="wp-block-group es-section es-process-teaser" id="process"><!-- wp:group {"className":"es-container"} -->
<div class="wp-block-group es-container"><!-- wp:group {"className":"es-section-head"} -->
<div class="wp-block-group es-section-head"><!-- wp:group {"className":"es-section-head__title"} -->
<div class="wp-block-group es-section-head__title"><!-- wp:paragraph {"className":"es-section-head__num"} -->
<p class="es-section-head__num">01</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"es-label"} -->
<h2 class="wp-block-heading es-label">How I work</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p><a class="es-link-arrow es-link-arrow--quiet" href="#process">See the full process &rarr;</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:heading {"level":3,"className":"es-process-teaser__headline"} -->
<h3 class="wp-block-heading es-process-teaser__headline">I don&#8217;t start with interfaces. I start by understanding the system.</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"es-process-teaser__lead"} -->
<p class="es-process-teaser__lead">The result should make sense for the people using it, and for the system that has to carry it.</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/how-i-work-teaser {"desktopLayout":"stacked","illustrationSize":"medium","metadata":{"name":"Concepts"}} -->
<!-- wp:group {"className":"es-process-teaser__group","metadata":{"name":"Understand"}} -->
<div class="wp-block-group es-process-teaser__group"><!-- wp:estavillo/how-i-work-illustration {"step":1,"context":"home"} /-->

<!-- wp:heading {"level":4,"className":"es-process-teaser__group-title"} -->
<h4 class="wp-block-heading es-process-teaser__group-title">Understand</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"es-process-teaser__group-text"} -->
<p class="es-process-teaser__group-text">See how people, information and goals actually connect.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"es-process-teaser__group","metadata":{"name":"Explore"}} -->
<div class="wp-block-group es-process-teaser__group"><!-- wp:estavillo/how-i-work-illustration {"step":4,"context":"home"} /-->

<!-- wp:heading {"level":4,"className":"es-process-teaser__group-title"} -->
<h4 class="wp-block-heading es-process-teaser__group-title">Explore</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"es-process-teaser__group-text"} -->
<p class="es-process-teaser__group-text">Test ideas and challenge assumptions before committing to one.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"es-process-teaser__group","metadata":{"name":"Improve"}} -->
<div class="wp-block-group es-process-teaser__group"><!-- wp:estavillo/how-i-work-illustration {"step":6,"context":"home"} /-->

<!-- wp:heading {"level":4,"className":"es-process-teaser__group-title"} -->
<h4 class="wp-block-heading es-process-teaser__group-title">Improve</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"es-process-teaser__group-text"} -->
<p class="es-process-teaser__group-text">Build something that works &#8212; and keeps working.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
<!-- /wp:estavillo/how-i-work-teaser --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
CONTENT;
