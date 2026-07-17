<?php
/**
 * Pattern content — Case Study — Callout Panel.
 *
 * Phase 0 of the second-pass architecture review (Trazur artifact audit):
 * a tinted callout box is a plain core/group with an opt-in CSS class
 * (.es-case-callout, defined in the child theme's case-study.css using
 * only existing --es-* tokens) — not a new block, and not the block
 * editor's own Color panel (which has no --es-* palette registered, so
 * its picker would either show generic default colors or an arbitrary
 * custom hex — exactly what this ticket rules out). See
 * estavillo-child/README.md ("Patterns Phase 0").
 *
 * Reuses .es-case-label (already defined for Case Section's own eyebrow)
 * for the optional eyebrow line, so the callout's eyebrow matches the
 * rest of the case visually with zero new CSS beyond the box itself.
 *
 * Generic enough for context notes, key learnings, warnings, design
 * principles, or limitations — same box, different heading/body each
 * time. The list at the end is explicitly optional; delete it if the
 * callout doesn't need one.
 *
 * 100% fictional/generic placeholder copy in {braces} — no case-specific
 * content.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:group {"className":"es-case-callout"} -->
<div class="wp-block-group es-case-callout"><!-- wp:paragraph {"className":"es-case-label"} -->
<p class="es-case-label">{Eyebrow — e.g. Key learning, Limitation, Design principle}</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">{Callout heading.}</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{Short paragraph — two or three sentences making the point. Keep callouts brief; if it needs more space, it's probably not a callout.}</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>{Optional supporting point — delete this list if the callout doesn't need one.}</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->
CONTENT;
