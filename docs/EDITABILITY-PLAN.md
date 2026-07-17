# Editability plan — Estavillo Portfolio (Kadence child theme)

The Home page is currently a custom PHP page template
(`templates/page-home-estavillo.php`), looping a filterable, ordered
section map (`es_home_sections()` in `functions.php`). This was the right
call for build speed while the design system and hero were being
established, but it can't stay a PHP-only surface forever — the user needs
to edit copy, links, images, and selected cases in WordPress without
requiring a Claude Code session for every change.

This document compares the options and recommends a phased path.

---

## Options

### A. Keep PHP template + filters

Content lives in PHP arrays inside each template part; values are run
through `apply_filters()` so they *can* be overridden externally (e.g. from
`functions.php`, a small custom plugin, or a `theme_mod`-backed filter)
without editing the template part itself.

- **Pros:** Zero new infrastructure. Already partially in place
  (`es_home_sections()`, `es_contact_email()`, nav links, UI strings).
  Fastest to extend. Keeps full control over markup/motion/hover behavior,
  which matters a lot for this design system.
- **Cons:** Not editable *in the WordPress admin UI* by a non-technical
  editor — someone still has to write a filter (i.e. write code) to change
  content. Doesn't scale to "the user edits this themselves in wp-admin."

### B. WordPress block patterns

Convert sections into registered block patterns built from core blocks
(or a small set of custom blocks), inserted into a real page in the block
editor. Content becomes editable via the standard block editor UI.

- **Pros:** Real in-admin editability with no custom UI to build. Content
  editors (including the user) can reorder, edit text/images, and even
  duplicate patterns using tools they already know. Patterns can still
  carry the theme's custom classes/CSS for the exact visual treatment.
- **Cons:** More setup than (A): need to register patterns, decide how
  much of the current bespoke markup/animation (e.g. Selected Work's
  arrow-slide CTA, the hero) survives being expressed as block markup vs.
  needing a custom block. Some hand-built interaction details may need to
  become custom blocks (see C) rather than pure core-block patterns.

### C. Custom blocks / ACF blocks

Build dedicated blocks (native block API or ACF Blocks) per section type
(e.g. a "Selected Work item" block, a "Featured Case" block), each with a
defined field schema and full control over rendering.

- **Pros:** Best of both worlds for this project — structured, validated
  fields (so editors can't break the layout) plus full control over markup
  and motion, same as (A). Scales cleanly to repeatable content (e.g.
  multiple Selected Work items as repeated blocks).
- **Cons:** Most setup cost of the three. Requires either PHP block
  registration + `render_callback` (native) or ACF (extra dependency) —
  needs a decision on tooling before starting. Worth it specifically for
  sections with repeatable/structured items (Selected Work) more than for
  singular sections (About, Connect).

### D. CPT "Case Study" + template rendering

A dedicated Custom Post Type for portfolio case studies, each with its own
fields (title, summary, tags/stack, status, link, images), rendered through
a template (archive/loop for Selected Work, single template for a future
case-study detail page).

- **Pros:** The right long-term model for case content specifically —
  cases are naturally repeatable structured records, and a CPT lets the
  user add/edit/reorder/publish cases entirely in wp-admin, including
  eventually building out full case-study detail pages. Also the natural
  home for Presupuestador/Trazur content (Sprint 4).
- **Cons:** Overkill for singular sections like About or Connect. Requires
  its own admin UI thought (custom fields via native meta boxes or ACF),
  and a template for both the Home teaser/list view and any future single
  case-study page.

---

## Recommendation: phased approach

Don't pick one option for everything — different sections have different
shapes (singular vs. repeatable), so the right tool differs.

### Phase 1 — Filters/options for quick control (extends A)

Already underway. Keep using `apply_filters()` for content that's simple,
singular, and low-churn (nav links, contact email, UI strings, section
order via `es_home_sections()`). This is the cheapest lever and should stay
in place even after later phases land — it's not being replaced, just not
sufficient on its own for high-churn or repeatable content.

### Phase 2 — Block patterns for editable sections (B, with C where needed)

For Home sections that are singular but benefit from real in-admin editing
(Featured Case, About, Connect, How I Work), convert to block patterns.
Where a section's interaction detail can't be expressed as plain block
markup (e.g. bespoke hover/motion behavior), wrap that piece as a small
custom block (C) rather than forcing it into core blocks.

### Phase 3 — CPT for case studies (D) — **done for Selected Work and Featured Case**

For **Selected Work** specifically — the highest-priority section per the
order below, and the most naturally repeatable/structured content — build
a Case Study CPT. This supersedes treating Selected Work as a block pattern
list; a CPT is the correct model for "add a new case without touching a
template."

**Implemented (Sprint 3):** `es_case_study` CPT. Deliberately minimal — no
ACF, native WordPress fields only:

| Field | Mechanism |
|---|---|
| Title | native post title |
| Short description | native excerpt |
| Featured image | native post thumbnail (placeholder frame if none) |
| Tags | native taxonomy `es_case_tag` (non-hierarchical, tag UI) |
| Home order | native `page-attributes` support ("Order" field) |
| Eyebrow/category, case URL, label/status, placeholder text, show-on-Home flag | one small custom meta box (`Case details`) |

**Extracted into a companion plugin (Sprint 3, part 2):** the CPT
registration, taxonomy, meta box, and Case Study query all moved out of
the child theme into a standalone plugin, **Estavillo Portfolio Core**
(`estavillo-portfolio-core/` at the repo root, `dist/estavillo-portfolio-core.zip`).
This is the correct architectural boundary going forward: **content types
are plugin territory, presentation is theme territory** — a theme switch
(or a future headless/decoupled frontend) shouldn't take portfolio content
down with it, and reinstalling/updating the visual theme shouldn't risk
Case Study data or registration.

The theme and plugin communicate through exactly one WordPress filter,
`es_portfolio_case_studies_for_home` — no direct function calls cross the
boundary in either direction:

```php
// Theme side (inc/selected-work-fallback.php) — always safe to call,
// plugin present or not:
function es_home_selected_work_source() {
    $cases = apply_filters( 'es_portfolio_case_studies_for_home', array() );
    return $cases ? $cases : es_home_selected_work_fallback_cases();
}

// Plugin side (includes/case-study-cpt.php) — only runs if the plugin is
// active, and only overrides the theme's default when it actually has data:
add_filter( 'es_portfolio_case_studies_for_home', function ( $default_cases ) {
    $real_cases = es_portfolio_get_case_studies_for_home();
    return $real_cases ? $real_cases : $default_cases;
} );
```

Because the theme never calls a plugin function by name, there is no
`function_exists()`/`is_plugin_active()` guard to maintain and no fatal
error is possible if the plugin is deactivated — `apply_filters()` with no
registered callback just returns the theme's own default, which is the
same three hardcoded placeholder cases the template always used. Home can
never render empty and never breaks, whether the plugin is active with
Case Studies, active with none yet, or not installed at all. The existing
`es_home_selected_work` filter still runs on top of whichever source is
used, unchanged.

The post type slug (`es_case_study`) and taxonomy slug (`es_case_tag`) did
not change in the move, and neither did any meta key — existing Case Study
posts created in wp-admin before this extraction continue to work exactly
as before.

**Featured Case, same pattern (Sprint 3, next ticket):** Featured Case now
draws from the same `es_case_study` CPT instead of its own hardcoded array
— no second content type. A Case Study gets one new boolean field, "Feature
this case on Home," and reuses existing fields for the rest (`kicker` →
eyebrow/category meta, `body` → native excerpt, `status` → the existing
label/status meta, `url`/`image`/`placeholder_label` → the same fields
Selected Work already uses). One new field was genuinely needed with no
native or existing equivalent: `_es_case_source` (the small attribution
line under the body paragraph, Featured Case-only).

Selection rule: if more than one Case Study is flagged featured, the one
with the lowest **Order** (the same native `page-attributes` field that
orders Selected Work) wins — deterministic, no separate priority field.

Same filter-bridge pattern, just a second, independent filter —
`es_portfolio_featured_case_for_home` — so Selected Work and Featured Case
can each be wired up, populated, or left on fallback independently of one
another:

```php
// Theme side (inc/featured-case-fallback.php):
function es_home_featured_source() {
    $case = apply_filters( 'es_portfolio_featured_case_for_home', array() );
    return $case ? $case : es_home_featured_fallback_case();
}

// Plugin side (includes/case-study-cpt.php) — WP_Query orders by
// menu_order ASC and takes the first match, i.e. lowest Order wins:
add_filter( 'es_portfolio_featured_case_for_home', function ( $default_case ) {
    $real_case = es_portfolio_get_featured_case_for_home();
    return $real_case ? $real_case : $default_case;
} );
```

Same guarantee as Selected Work: no direct function call crosses the
theme/plugin boundary, so deactivating the plugin cannot break Featured
Case — it just falls back to the same hardcoded placeholder (the Guzmán
Villalba case) that was already there, with byte-identical markup and CSS.
A Case Study can independently be shown in Selected Work, set as the
Featured Case, both, or neither — the two flags and their queries don't
interact.

**Single Case Study template — done (Sprint 4B).** `estavillo-child/single-es_case_study.php`
is resolved automatically by WordPress's own template hierarchy for any
`es_case_study` post — `single-{post_type}.php` in the active theme, no
`template_include` filter and no plugin registration needed, since this is
100% presentation and belongs to the theme like everything else chrome-
related. It reuses `template-parts/site-header`/`site-footer` (same
pattern as `templates/page-home-estavillo.php`) so a visitor never leaves
the ESTAVILLO system. Deliberately minimal, no case builder: title,
eyebrow (`_es_case_kicker`), excerpt, tags, featured image (or the same
placeholder pattern as Selected Work), an optional Status/Role/Tools/
Period meta row, and the post body via plain `the_content()` — Role,
Tools, and Period are three new optional plain-text fields on the same
"Case details" meta box, nothing more elaborate. Breadcrumbs/accessibility
strategy (flagged in `BACKLOG.md`) is now unblocked whenever it's picked
up, since a real single template exists to attach it to.

See `estavillo-child/README.md` → "Selected Work — editable vía Case
Studies", "Featured Case — editable vía Case Studies", and "Single Case
Study page" for exact wp-admin usage instructions.

**Case Study format system — done (Sprint 4C).** Two additions on top of
the Sprint 4B template, both deliberately minimal:

1. **Sticky in-page index** — one new plain-text field, `_es_case_index`
   (a textarea, `Label|#anchor` per line), on the same "Case details" meta
   box. Explicitly **not** a repeater and **not** ACF — approved V1
   decision, same reasoning as the rest of this plan (native fields only,
   simplest mechanism that works). Anchors are matched manually against
   `id="…"` attributes the author adds inside the editor content; nothing
   is auto-generated from headings. Empty field = index doesn't render, so
   existing/older Case Studies are unaffected.
2. **`.es-case-*` CSS class library** (`case-study.css`) — a fixed set of
   ~14 classes (section, label, heading, lead, two-column, figure+caption,
   browser-chrome frame, stats grid, timeline, decision cards, pullquote,
   status grid, native `<details>` accordion) applied directly inside
   `the_content()`. This is styling only, not a new content model — the
   post body is still one `the_content()` field, edited in the standard
   editor; the classes just give the author a documented vocabulary for
   structuring that HTML. No new post type, no new meta fields, no block
   registration, no page-builder abstraction.

See `estavillo-child/README.md` → "Sistema de formato de Case Study" for
the full class reference, the index field format, and a copy-paste
example.

### Polylang — done (Sprint 4A)

Approved V1 decisions (see the architecture decision conversation this
sprint implements):

1. **Case Study CPT is translatable.** `estavillo-portfolio-core/includes/polylang-compat.php`
   hooks Polylang's `pll_get_post_types` filter to add `es_case_study` —
   self-configuring, no manual "enable translation" checkbox needed in
   Polylang's settings. Since every Case Study field (meta box fields,
   featured image, excerpt, tags) is post meta / native post data, and
   Polylang's translation mechanism creates a full new linked post per
   language, every field already works per-language automatically — no
   extra code was needed for the fields themselves.
2. **`es_case_tag` stays language-neutral** (deliberately not added to
   `pll_get_taxonomies`) — tags are shared across EN/ES posts rather than
   requiring a parallel translated tag vocabulary. Simpler for V1; revisit
   only if bilingual tag precision becomes a real complaint.
3. **The language switcher is real.** `template-parts/site-header.php`
   now calls `pll_the_languages( array( 'raw' => 1 ) )` (guarded by
   `function_exists()`) in both the desktop header and the mobile menu
   foot, replacing what was a hardcoded, non-functional "EN / ES" string.
   Same markup/classes (`.es-nav__lang`, `.es-nav__lang-on`) either way —
   with Polylang active it renders real links to the translated URLs and
   marks the current language; without Polylang (or with no languages
   configured yet) it falls back to the exact original static text,
   byte-for-byte.
4. **Home Content options stay single/global for V1** (approved human
   decision, not per-language) — see the "Phase 1, given a wp-admin UI"
   section below for why: this data is explicitly a stopgap ahead of the
   V2 block-pattern migration, and Gutenberg blocks on a Polylang-
   translated Page get correct per-language content for free, which
   bilingual-izing the options page now would just duplicate and then
   discard.

**What's still a wp-admin step, not code:** creating the actual Home (ES)
page (a second WP Page using the same template, linked as a Polylang
translation of the existing Home page) — see Sprint 4C in `ROADMAP.md`.
Nothing in the code blocks this; it's simply not been created yet because
real Spanish copy isn't ready.

### Phase 1, given a wp-admin UI — About, How I Work, Connect, Header, Footer

These sections are singular, not repeatable — a CPT is the wrong tool
(that's what Phase 3 is for). Rather than jump to Phase 2 (block patterns)
for them, Sprint 3 revisited Phase 1 with one observation: **these
sections already had `apply_filters()` extension points since Home v1**
(`es_home_about_text`, `es_home_process_steps`, `es_contact_email`,
`es_nav_links`, `es_social_links`, `es_footer_location`, etc. — all
documented in `estavillo-child/README.md` from the start as the
"Code Snippets" editing mechanism). The only thing missing was a wp-admin
UI over those filters.

So the plugin gained one options page, **Case Studies → Home Content**
(`estavillo-portfolio-core/includes/home-content-options.php`), storing a
single option (`es_portfolio_home_content`, an associative array) and
hooking each existing filter: if a field is set, the filter returns it; if
empty, the filter returns the theme's own default untouched. No new
filter names, no theme template changes, same "empty field → fallback,
never breaks" guarantee as the CPT — just applied per-field instead of
per-post.

This is deliberately **not** Phase 2 (block patterns) — it's Phase 1 with
better ergonomics. Block patterns remain the right call only if/when these
sections need real in-admin rich-text/layout editing beyond flat fields;
until then this is far cheaper and lower-risk, and matches "prefer native
WordPress functionality, avoid new dependencies."

See `estavillo-child/README.md` → "Home Content — About, How I Work,
Connect, Header, Footer" for exact wp-admin usage instructions.

### Fixed pages — Work / About / How I Work / Contact (mega sprint, V1)

Same Phase 1 approach, extended to 4 new standalone page templates instead
of Home-only sections:

- **Work** reuses the exact same data as Selected Work (the CPT's "Show on
  Home" flag) — no new field, just a second, fuller presentation of it
  (plus an "Archive" group for cases explicitly opted out of Home).
- **About** gained 4 new Phase-1 fields on the existing Home Content
  options page (CV URL, career timeline, education/certificates, hobbies)
  — same per-row "empty title = skip that row" merge pattern already
  proven by How I Work's steps and Header's nav links.
- **How I Work** and **Contact** pages add zero new fields — they reuse
  the How I Work steps and the Connect/Footer fields, just rendered as
  their own dedicated pages instead of Home sections.

This is explicitly **V1**: fast, safe, built on the same
options-page-plus-filters mechanism as the rest of Phase 1, not a new
mechanism. See the README's "V1 / V2" note for what a Gutenberg-based V2
would look like for these same sections, and "Páginas fijas" in
`estavillo-child/README.md` for the full field reference.

**Infra/polish follow-up (Sprint 4H):** the fixed pages above shipped
with two real bugs (sticky header not sticking, breadcrumbs missing/
unstyled on 4 of 5 pages that needed them) — both root-caused and fixed,
see `ROADMAP.md` → Sprint 4H for the diagnosis. Also in that sprint: How
I Work's dedicated page became a genuinely richer editorial page (not a
reused Home teaser) with 3 new optional per-step fields; Contact's
heading hierarchy was fixed and gained 2 optional fields; About's hobbies
field was rebuilt from one text field into a structured, iconed,
show/hide-able list of up to 8 items. The options page itself was
renamed **"Portfolio Content"** in wp-admin (cosmetic only — same option
key, same menu slug, same filters). This closes out the "infrastructure"
phase of V1 — from here, work shifts to loading real content (Sprint 4F)
rather than building more structure.

---

### Case Study bodies — Gutenberg block library (Sprint 4J) — **done**

This is Phase 2 + C landing for the Case Study **body** (until now the one
part of a case that still required raw HTML): the plugin registers 10
dynamic blocks under the inserter category **"Estavillo Case Study"** —
case-section, case-figure, case-stats, case-ladder, case-taxonomy,
case-timeline, case-decisions, case-status, case-quote, case-details — one
folder per block under `estavillo-portfolio-core/blocks/`, registered via
`block.json`, rendered server-side (`render.php`) with the theme's existing
`.es-case-*` classes, edited with no-build plain-JS editor scripts
(`edit.js` + shared `assets/js/case-blocks-ui.js`). No ACF, no remote
libraries, no frontend JS. Repeatable items (stats, ladder steps, taxonomy
variables/tags, timeline steps, decision cards, status bullets) get
add/remove/reorder controls; Case Figure uses MediaUpload/MediaUploadCheck
so images are picked/replaced from the Media Library — no HTML editing.

Two patterns — **"Estavillo — Presupuestador Case Structure (ES)"/"(EN)"**
(`patterns/presupuestador-{es,en}.php`) — insert the full 13-section case
with the real (honest) copy as starter content. Editing a case now means:
insert pattern → edit copy inline → replace figure placeholders from the
Media Library → reorder sections by moving blocks.

Deliberately additive: the Custom-HTML workflow and the class library stay
as the fallback/import format; existing posts render unchanged; no
migration is performed on any existing post (to move a post to blocks,
insert the pattern alongside the old Custom HTML block, then delete the
HTML block once the block version matches). No CPT/meta-key/Polylang
changes.

### Case Study editorial composition system (Sprint 4L→4N, corrected) — **done**

The Case Section block is the composition chassis for case bodies. The
case body container is **1320px** (`es-container--case` — body only;
header/footer/nav/hero stay at 1140). Case Section is a genuinely
**flexible chapter container**: label/heading/lead (dedicated RichText,
always full width) + **unrestricted InnerBlocks** — no allowedBlocks, no
template, no templateLock. Insert and reorder anything (Heading,
Paragraph, List, Image, Gallery, Group, Row, Stack, native Gutenberg
Columns/Column, the existing Estavillo Case Study blocks) with normal
block controls. Case Section itself controls only three chapter-level
things, human-labeled, never columns or px:

- **Width**: Content (default — full width of the immediate parent, no
  measure cap on direct-child text) · Reading (whole chapter ~72ch,
  never wider than the parent — the only width that constrains prose).
  Wide was consolidated into Content (Sprint 4N — see below) and no
  longer appears as a choice.
- **Chapter spacing**: Compact 96 / Standard 120 (default) / Spacious
  144 — total space between chapters, hairline in the middle.
- **Chapter divider**: on/off toggle for the hairline itself (spacing
  unaffected; the page's first chapter never shows it regardless).

Text-left/image-right (or the reverse) compositions are built with
**native Gutenberg Columns/Column** inside a chapter — any of core's
ratios (33/66, 40/60, 50/50, 60/40, 66/33), moving blocks between
columns, reordering, duplicating: all standard Gutenberg, untouched by
this block. Responsive stacking on tablet/mobile is core's own. A Case
Section can go *in* a column too — put one Case Section in each column
for two chapters side by side, or a Case Section next to a Case Figure/
Case Decisions/plain image. "Content" width always means the full width
of whichever element the section actually lives in — the page body, or
its column — never the viewport or the 1320px case container regardless
of nesting depth (Sprint 4N, see below).

*(Correction, Sprint 4M: an earlier version of this system locked
Case Section into fixed Content/Media regions over a custom 12-column CSS
grid with Split-left/right/balanced presets and a "mobile order" control.
Real WordPress testing showed it too rigid — content forced into narrow
predefined areas, sometimes leaving a large empty area on the right,
caused by a legacy CSS rule that capped any direct-child paragraph to
~820px regardless of the chosen width. That architecture was removed
entirely and replaced with the flexible model described above. The two
region block types (`case-split-content`/`case-split-media`) stay
registered, parent-locked and hidden from the inserter, only so a post
already saved with them keeps rendering — as a flat, unstyled sequence,
not the old grid. Recreate such a post from the corrected pattern rather
than editing it in place.)*

*(Correction, Sprint 4N: Case Section worked correctly at the page level
but did not respect a native Gutenberg Column's width when nested inside
one — it behaved like a page-level container regardless of its parent,
which could force a sibling column to wrap or let the section escape its
assigned width. Root cause: the block had no explicit sizing rules of its
own (relying entirely on default block flow) and Reading's `max-width:
72ch` had no upper bound tied to the parent. Fixed with a base rule —
`width: 100%; max-width: 100%; min-width: 0` on every Case Section — and
`max-width: min(72ch, 100%)` on Reading, so "full width" is always
relative to the immediate parent, at any nesting depth, and Reading never
exceeds a narrow column. Confirmed core's own Columns CSS was never being
overridden. Content and Wide always looked identical, so Wide was dropped
from the Inspector; a block already saved with `"layout":"wide"` keeps
loading and renders exactly like Content — no migration, no invalidation.)*

How to use each width: insert **"Case Study — Editorial System Demo"**
(Content and Reading in sequence, with Columns 40/60, 60/40 and 50/50,
fictional copy — a lab page to explore) or **"Case Study — Canonical
Starter"** (Content → Content with Columns 40/60 → Content with a wide
artifact → Reading close, scaffolding copy in {braces}) from the pattern
inserter, or set the Width select on any Case Section. Backward
compatible: sections with no saved width attribute (all pre-4M content,
including the Presupuestador patterns) fall back to Content — full
width, no cap, functionally the same or better than before; legacy
Custom-HTML bodies render unchanged; no automatic migration of any post.

### Patterns Phase 0 (second-pass architecture review) — **done**

Before writing any content for Trazur, a second architecture pass asked a
narrower question than the first: for each recurring artifact type
(persona, comparison table, journey map, flow diagram, methodology, KPI
section, timeline, callout, etc.), what is the smallest structure that
solves it — an image, plain native blocks, native blocks saved as a
Pattern, a new custom block, or an existing Estavillo/Kadence block? The
explicit bias: avoid repeating the original Case Section mistake (a
custom grid where Gutenberg already had an answer), and build new custom
blocks only after multiple real cases validate the same shape — one real
example (Trazur) isn't enough evidence yet.

Result: three reusable **Patterns** (compositions of existing blocks,
registered exactly like the Presupuestador/Canonical Starter patterns
above) plus one CSS-only block style — no new `block.json`/`render.php`/
`edit.js` anywhere:

- **"Case Study — Persona"** — Columns 35/65 (no wrapping Group — see the
  bug-fix entry below) > photo
  (`estavillo/case-figure`, replaceable) + name/role/demographics, and
  Biography + Goals/Frustrations (nested Columns 50/50) + a pull-quote
  (`estavillo/case-quote`). Fictional placeholder copy.
- **"Case Study — Comparison Table"** — `estavillo/case-section`
  (eyebrow/heading/lead) + a native **Table** block (4 generic columns,
  3 placeholder rows — add/remove with the Table block's own controls) +
  a caption. Generic enough for tool comparisons, before/after,
  competitor comparisons, or research findings.
- **"Case Study — Callout Panel"** — a native Group with an opt-in class
  (`.es-case-callout`, existing `--es-*` tokens only — deliberately not
  the Group block's own Color panel, since this theme has no `theme.json`
  palette exposing those tokens as pickable colors) + eyebrow/heading/
  paragraph/optional list.
- **Checkmark List** — a `register_block_style()` variation on
  `core/list` (`estavillo-child/inc/block-styles.php`): opt-in per list,
  the default bullet list is untouched.

All three patterns are ordinary, fully editable Gutenberg content the
moment they're inserted — ungroup, move, duplicate, delete any part, same
as hand-built content. Two things the review deliberately left as images/
native-only rather than turning into new work: Journey Maps and Flow
Diagrams stay images (a flowchart's arrows/decision nodes are a
diagramming problem, not a content-editing one — building a block for
that would be the highest-effort, lowest-editing-value item on the list);
image+text layouts stay plain native Columns (no dedicated pattern — the
right ratio depends on the narrative, and forcing a preset would
reintroduce the rigidity Case Section's grid was removed for).

---

## Editability priority order

As given by the project owner, highest priority first:

1. **Selected Work** — **done.** Repeatable, highest-churn, built as the
   `es_case_study` CPT (Phase 3) described above.
2. **Featured Case** — **done.** Pulls from the same CPT as Selected Work
   (a case flagged "featured"), not a separate block pattern — see above.
3. **About** — **done.** Home Content options page (Phase 1 + wp-admin UI).
4. **How I Work** — **done.** Same options page, per-step.
5. **Connect** — **done.** Same options page.
6. **Header** — **done.** Same options page, nav links.
7. **Footer** — **done.** Same options page (social links + location only
   — nav links and email are shared with Header/Connect).
8. **Hero** — the only section left. Copy/CTAs are already filterable, but
   this is deliberately sequenced **after** the dedicated Hero
   block/layout ticket flagged in `BACKLOG.md`, so editability isn't built
   on top of a layout that's about to change.

## Sequencing note

Per `ROADMAP.md` Sprint 3: build **one** editable section end-to-end
(Selected Work) — including the actual wp-admin editing experience, not
just a data structure — before migrating any other section. Validated in
practice, then Featured Case reused the same CPT and filter-bridge pattern
rather than introducing a new mechanism. The remaining singular sections
(About, How I Work, Connect, Header, Hero) are still Phase 1/2 candidates
(theme options / block patterns), not CPT-backed — they aren't naturally
repeatable content the way Case Studies are.
