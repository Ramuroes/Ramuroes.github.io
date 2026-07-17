<?php
/**
 * Pattern content — Case Study — Comparison Table.
 *
 * Phase 0 of the second-pass architecture review (Trazur artifact audit):
 * a comparison/findings table is native core/table, not a new custom
 * block — WordPress's own Table block already gives header rows, an
 * editable column/row count and a real <table> in the markup, which is
 * strictly better for accessibility and editing than a bespoke repeater
 * UI would be. See estavillo-child/README.md ("Patterns Phase 0").
 *
 * Deliberately generic: "Option / Strengths / Weaknesses / Notes" reads
 * for a tool comparison, a heuristic evaluation, a before/after, or a
 * competitor comparison alike — editors add/remove rows and columns with
 * core/table's own controls, so the 4-column/3-row starting shape here is
 * a starting point, not an assumption baked into any code.
 *
 * Uses the existing estavillo/case-section block for the eyebrow/heading/
 * lead (case-section already has those three attributes — no need for
 * separate core/heading + core/paragraph just to duplicate them) and the
 * existing .es-case-caption class for the caption below the table, same
 * convention already used by the other case patterns in this plugin.
 *
 * 100% fictional placeholder copy in {braces} — no real Trazur (or any
 * other case) content.
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:estavillo/case-section {"anchor":"comparison","label":"Comparison","heading":"{What this comparison shows, in one sentence.}","lead":"{One or two sentences of context: what is being compared, and why it matters for this decision.}"} -->
<!-- wp:table -->
<figure class="wp-block-table"><table><thead><tr><th>Option</th><th>Strengths</th><th>Weaknesses</th><th>Notes</th></tr></thead><tbody><tr><td>{Option A}</td><td>{What worked well.}</td><td>{Where it fell short.}</td><td>{Anything else worth flagging.}</td></tr><tr><td>{Option B}</td><td>{What worked well.}</td><td>{Where it fell short.}</td><td>{Anything else worth flagging.}</td></tr><tr><td>{Option C}</td><td>{What worked well.}</td><td>{Where it fell short.}</td><td>{Anything else worth flagging.}</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">{Caption: one line on what was compared and how — e.g. the method used, the sample, or the source of each option's data.}</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->
CONTENT;
