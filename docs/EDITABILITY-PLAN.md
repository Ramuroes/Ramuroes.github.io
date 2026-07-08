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

### Phase 3 — CPT for case studies (D)

For **Selected Work** specifically — the highest-priority section per the
order below, and the most naturally repeatable/structured content — build
a Case Study CPT. This supersedes treating Selected Work as a block pattern
list; a CPT is the correct model for "add a new case without touching a
template."

---

## Editability priority order

As given by the project owner, highest priority first:

1. **Selected Work** — repeatable, highest-churn, best fit for a CPT
   (Phase 3), but can start as a block-pattern/loop (Phase 2) if the CPT
   isn't ready yet.
2. **Featured Case** — singular, good fit for a block pattern (Phase 2);
   may later pull from the same CPT as Selected Work (a case marked
   "featured").
3. **About** — singular, block pattern (Phase 2).
4. **How I Work** — singular, structured (steps), block pattern (Phase 2),
   possibly a small custom block if the reserved icon/motion slot needs
   structured fields per step.
5. **Connect** — singular, block pattern (Phase 2).

## Sequencing note

Per `ROADMAP.md` Sprint 3: build **one** editable section end-to-end
(Selected Work) — including the actual wp-admin editing experience, not
just a data structure — before migrating any other section. Validate the
approach works in practice before committing to it for the rest of Home.
