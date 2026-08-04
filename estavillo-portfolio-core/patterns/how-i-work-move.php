<?php
/**
 * Pattern content — How I Work — Move.
 *
 * One reusable scaffold for a single "process move" section on the How I
 * Work page (docs/HOW-I-WORK-CONTENT-SPEC.md §6) — number + title, an
 * estavillo/how-i-work-illustration block (defaults to step 1 — pick the
 * real step for a new move from its Inspector "Step" control once one is
 * added), short description, and the four consolidated disclosure sections
 * inside the existing estavillo/case-details block (Why it matters / How I
 * approach it / AI and human judgment / In practice).
 *
 * Only ONE pattern variant exists on purpose: illustration/text placement
 * alternates left-right on desktop purely via CSS
 * (.es-how-page .es-process-move:nth-of-type(even) — see pages.css), not
 * by hand-authoring a mirrored second pattern. The image column is always
 * first in the markup regardless of which side it renders on — this is
 * also what makes mobile "illustration first" automatic (DOM order
 * already matches the required mobile reading order), and it's why no
 * CSS `order` property is used anywhere in this composition: reordering
 * is scoped to a single non-interactive, decorative image column, so it
 * never desyncs keyboard focus order from visual order.
 *
 * 100% placeholder copy in {braces} — no real content, no invented case
 * facts. Real per-move copy for all six moves already exists, written
 * directly into docs/content/how-i-work-gutenberg-en.html; this pattern
 * is a starter for future edits (a 7th move, a restructure), not the
 * primary delivery mechanism for the approved content.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:group {"tagName":"article","className":"es-process-move","anchor":"move-placeholder"} -->
<article class="wp-block-group es-process-move" id="move-placeholder"><!-- wp:group {"className":"es-container"} -->
<div class="wp-block-group es-container"><!-- wp:group {"className":"es-process-move__head"} -->
<div class="wp-block-group es-process-move__head"><!-- wp:paragraph {"className":"es-process-move__num"} -->
<p class="es-process-move__num">{01}</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"es-process-move__title"} -->
<h3 class="wp-block-heading es-process-move__title">{Move title}</h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"className":"es-process-move__row"} -->
<div class="wp-block-columns es-process-move__row"><!-- wp:column {"width":"42%"} -->
<div class="wp-block-column" style="flex-basis:42%"><!-- wp:estavillo/how-i-work-illustration {"step":1} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"58%"} -->
<div class="wp-block-column" style="flex-basis:58%"><!-- wp:paragraph {"className":"es-process-move__text"} -->
<p class="es-process-move__text">{One to two sentences — what this move is, in plain language.}</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-details {"summary":"More on this move","className":"is-style-light"} -->
<!-- wp:heading {"level":4,"className":"es-process-field-label"} -->
<h4 class="wp-block-heading es-process-field-label">Why it matters</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{Why this move matters, in one or two sentences.}</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":4,"className":"es-process-field-label"} -->
<h4 class="wp-block-heading es-process-field-label">How I approach it</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{Methods or activities that may apply — phrased as "may," not a mandatory checklist.}</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":4,"className":"es-process-field-label"} -->
<h4 class="wp-block-heading es-process-field-label">AI and human judgment</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{How AI can help at this specific move, including a limit or how the output gets validated — not a capability claim alone.}</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":4,"className":"es-process-field-label"} -->
<h4 class="wp-block-heading es-process-field-label">In practice</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{A real example from actual work, and/or what success might look like — observable improvements only, never a guarantee. Add "Related case: {case name} — pending publication" only once a real, documented case exists.}</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-details --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group --></article>
<!-- /wp:group -->
CONTENT;
