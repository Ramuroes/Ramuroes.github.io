<?php
/**
 * Pattern content — Case Study — Persona.
 *
 * Phase 0 of the second-pass architecture review (Trazur artifact audit):
 * a persona is a composition of native Gutenberg blocks + one existing
 * Estavillo Case Study block (case-figure for the replaceable photo,
 * case-quote for the pull-quote) — NOT a new custom block. See
 * estavillo-child/README.md ("Patterns Phase 0") for why: a single real
 * example (Trazur) doesn't meet
 * the "validated by multiple projects" bar for a new block, and this exact
 * skeleton (photo, demographics, bio, goals, frustrations, quote) is fully
 * expressible with blocks that already exist.
 *
 * Root is a Group (no background/className — purely an editor-convenience
 * wrapper so the whole persona can be selected/moved/duplicated as one
 * unit in List View) containing a 35/65 Columns split. Ratio, order and
 * every block inside are ordinary Gutenberg content once inserted —
 * editors can freely ungroup, reorder or delete any part of it.
 *
 * 100% fictional placeholder copy in {braces} — no real person, no case
 * content. Translate/replace inline like any other block content; no
 * separate ES/EN pattern needed since none of this text is final content.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:group -->
<div class="wp-block-group"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"35%"} -->
<div class="wp-block-column" style="flex-basis:35%"><!-- wp:estavillo/case-figure {"variant":"standard","placeholderLabel":"persona-photo","alt":"Placeholder photo — replace with a real or clearly fictional portrait"} /-->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">{Persona name}</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{Role or one-line profile — job, context, primary device.}</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>{Age}</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>{Location}</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>{Primary device}</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>{Digital literacy / context}</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"65%"} -->
<div class="wp-block-column" style="flex-basis:65%"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Biography</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{Two or three sentences of background: who they are, their context, and why they matter to this case.}</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Goals</h4>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>{Goal placeholder one.}</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>{Goal placeholder two.}</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Frustrations</h4>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>{Frustration placeholder one.}</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>{Frustration placeholder two.}</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:estavillo/case-quote {"quote":"{A short first-person line that captures this persona's core need or frustration.}","cite":"{Persona name}, {role}"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
CONTENT;
