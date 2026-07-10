# Roadmap — Estavillo Portfolio (Kadence child theme)

Status: the child theme foundation and Home v1 are built and visually strong.
We are moving from **"build the system"** to **"iterate the product."**

The priority from here forward is to avoid large, token-heavy rewrites and
instead work in small, scoped tickets — one section or one concern at a
time. See `DECISIONS.md` for the standing rules behind this shift and
`BACKLOG.md` for the itemized, prioritized task list that feeds each sprint.

This roadmap is organized into short sprints. Sprints are not calendar-bound;
they're just an ordering of scope. Do not start a sprint's work until the
previous one is considered done (or explicitly deprioritized).

---

## Sprint 0 — Documentation / control layer

**Goal:** establish the ticket-based workflow itself. No code changes.

- Create `docs/ROADMAP.md`, `docs/BACKLOG.md`, `docs/DECISIONS.md`,
  `docs/QA-CHECKLIST.md`, `docs/EDITABILITY-PLAN.md`.
- No CSS/JS/PHP changes.
- No visual redesign.
- No ZIP rebuild required (docs-only).

**Status:** in progress (this document is part of it).

---

## Sprint 1 — Critical UI fixes

**Goal:** close out the remaining known bugs before anything else ships.

- Remove remaining blue interaction states (mobile menu active/pressed,
  especially the "Work" link).
- Fix mobile menu active state so it never shows blue anywhere.
- Fix mobile CTA wrapping ("See how I work" wrapping below the main CTA on
  some real phones) — define the responsive behavior intentionally instead
  of leaving it to accident.
- Normalize case CTA hover states (Selected Work / case links should be
  white/neutral by default, green only on hover — not permanently green).

See `BACKLOG.md` P0 items for the exact list this sprint should clear.

---

## Sprint 2 — Home polish

**Goal:** refine what already exists. Not a redesign.

- Improve the hero-to-How-I-Work transition (still feels slightly awkward
  even after the gradient softening fix).
- Refine footer rhythm (currently feels unresolved).
- Improve How I Work's visual treatment lightly (within the existing
  reserved icon/motion slots — no layout change).
- Do not redesign color, type, spacing, or layout beyond these specific
  refinements.

---

## Sprint 3 — Editability foundation

**Goal:** prove the editability path on one section before touching the rest.

- Make **Selected Work** editable first (see `EDITABILITY-PLAN.md` priority
  order).
- Decide the mechanism: filters, theme options, block patterns, or CPT —
  see `EDITABILITY-PLAN.md` for the comparison and phased recommendation.
- Build one editable section end-to-end (including the WordPress-admin
  editing experience, not just the PHP data structure) before migrating any
  other section.

**Status:** Done, except Hero (deliberately deferred — see below).

- Selected Work — **done**: "Case Study" custom post type (native fields +
  one small meta box, no ACF), with the current placeholder cases kept as
  an automatic fallback when no Case Studies exist yet.
- Featured Case — **done**: reuses the same Case Study CPT (a "feature
  this case" flag) rather than a separate mechanism.
- About — **done**: a plugin-owned "Home Content" options page hooks the
  `es_home_about_*` filters that already existed in the theme since Home
  v1 — no theme template changes needed.
- How I Work — **done**: same options page, per-step merge (editing one
  step doesn't require filling in all 6).
- Connect — **done**: title, lead, contact email, CTA URL. Contact email
  is shared with Footer (same underlying filter both sections use).
- Header — **done**: 4 nav links, edited individually. Same array feeds
  desktop nav, mobile menu, and footer nav (already shared before this
  ticket) — one edit updates all three.
- Footer — **done**: LinkedIn URL, Behance URL, location. Nav links and
  contact email were already covered by Header/Connect above (same shared
  data), so this ticket only needed its two genuinely footer-only fields.
- Hero — deliberately deferred until the Hero block/layout ticket flagged
  in `BACKLOG.md` lands first — editability shouldn't be built on top of a
  layout that's about to change.

Every section above (except Selected Work/Featured Case, which needed a
CPT because they're repeatable) turned out to already have
`apply_filters()` extension points from Home v1 — Sprint 3's real work for
them was giving those filters a wp-admin UI (one shared "Home Content"
options page in the plugin), not building new plumbing. No theme template
was changed for About, How I Work, Connect, Header, or Footer.

See `EDITABILITY-PLAN.md` for usage and the architecture rationale.

**Flagged during this sprint, deliberately not built now** (logged to
`BACKLOG.md` instead of expanding this sprint's scope):
- A dedicated Hero block/layout ticket — broader mobile-specific
  constellation composition and Hero editability, still open. (The
  specific mobile secondary-CTA alignment bug mentioned here was fixed as
  a scoped, approved exception in Sprint 4 — see below — without opening
  the full Hero ticket.)
- Accessibility/UX items: sticky header refinement, an optional "back to
  top" interaction, and a breadcrumbs/accessibility strategy for internal
  case-study pages (the single Case Study template now exists as of
  Sprint 4B, so breadcrumbs are unblocked whenever picked up).

---

## Sprint 4 — Polylang, Case Study template, real content

**Goal:** a fast, publishable V1 — real EN/ES portfolio switching and a
real single Case Study page, then real content on top of both.

**Sprint 4A — Polylang readiness — done.** The Case Study CPT registers
itself as translatable via Polylang's `pll_get_post_types` filter (plugin,
self-configuring — no manual Polylang settings step required).
`es_case_tag` deliberately stays language-neutral for V1 (approved human
decision — see `EDITABILITY-PLAN.md`). The header and mobile-menu language
indicators are now a real switcher via `pll_the_languages()`, guarded by
`function_exists()` — falls back to the original static "EN / ES" markup,
byte-for-byte, if Polylang isn't installed. Home Content options stay
single/global for V1 (approved human decision, not per-language).

**Sprint 4B — Single Case Study template — done.** New
`estavillo-child/single-es_case_study.php`, resolved automatically by
WordPress's own template hierarchy (no plugin registration needed),
reusing the ESTAVILLO header/footer chrome. Renders title, eyebrow,
excerpt, tags, featured image (or placeholder), a Status/Role/Tools/Period
meta row, and the post body via the standard `the_content()` — no case
builder, no custom body fields. Role/Tools/Period are new optional
plain-text fields on the existing Case Study meta box. New
`assets/css/case-study.css`, enqueued only on this template; `site.css`
(chrome) and `motion.js`/`nav.js` are now shared between Home and this
template instead of being Home-only.

**Also in this sprint (approved, out-of-cycle P0):** the mobile hero CTA
alignment bug — see `BACKLOG.md` "Bugs / fixes" for the root cause and
fix. Scoped narrowly to that one bug; the broader Hero block/layout ticket
flagged in Sprint 3 is still open and unstarted.

**Sprint 4C — Case Study format system + global blue-state fix — done.**
Built the reusable Case Study formatting layer on top of the Sprint 4B
template: an optional sticky in-page index (manual `Label|#anchor` textarea
field, new `_es_case_index` post meta — empty by default, renders nothing
when unset) and a 14-class `.es-case-*` library
(section/label/heading/lead/two-column/figure+caption/browser-chrome
frame/stats grid/timeline/decision cards/pullquote/status grid/native
`<details>` accordion) usable directly inside the standard editor via
`the_content()` — no case builder, no ACF, no new repeaters. Visually
informed by a reference case-study mockup (studied for layout/composition
only — reimplemented from scratch with existing `--es-*` tokens, zero new
colors/type, and its JS runtime was never shipped). Also closed out the
long-flagged **global blue interaction-state bug**: every interactive
element under the ESTAVILLO scope (desktop nav brand/links, mobile menu
button, footer nav/meta links, buttons, arrow links, cards, and the two new
case-study elements) now has explicit rest/hover/focus-visible/active/
visited rules reinforced with `!important`, verified via an adversarial
fake-Kadence-CSS-injection test to survive a worst-case external override —
supersedes the narrower mobile-menu-only fix from Sprint 1's backlog item.
See `estavillo-child/README.md` ("Sistema de formato de Case Study") for
the full class reference and a copy-paste editor example.

**Sprint 4D — Case Study hero polish — done.** The hero area of
`single-es_case_study.php` felt too centered/narrow for a "premium
editorial" feel. Reworked into a 2-column grid on desktop
(`.es-case__hero-content` left: eyebrow/title/excerpt/tags/status-role-
tools-period; `.es-case__hero-media` right: featured image or placeholder,
4:5 frame), single stacked column on mobile (<1000px, text then image,
unchanged from before). Text is explicitly left-aligned, never centered.
No new fields, no change to the `.es-case-*` content-class library, no
Home/typography/color changes. The sticky index is unaffected (separate
element above the hero). Logged a future improvement in `BACKLOG.md`
(desktop-only side index) rather than building it now.

**Sprint 4E — EN/ES Home strategy — next.** Create the Home (ES) page as
a Polylang translation of the existing Home page (same template, no new
code) once real Spanish copy exists. Verify hero copy / UI strings
resolve correctly per language through the existing `es__()` /
`pll_register_string()` wiring.

**Sprint 4F — Real content loading — next.** Enter real Presupuestador and
Trazur case studies as Case Study posts (now that the single template
exists to display them properly, including the new `.es-case-*` formatting
library and the editorial hero layout), in both languages via their
Polylang translations. Add real images. Refine EN/ES copy for all Home
sections (within the single-language-global constraint of Home Content
options — see Sprint 3 notes above).

**Sprint 4G — V1 publishable structure (mega sprint) — done.** Backup
branch/tag `backup-before-mega-sprint-case-pages` created first, from a
confirmed-clean working tree. Built the rest of what a publishable V1
needs beyond the single Case Study page:

- **Presupuestador case content** — a complete, paste-ready `.es-case-*`
  HTML deliverable (`docs/content/presupuestador-case-study.html`)
  covering context/problem/discovery/architecture/MVP/App Alpha/AI
  role/limitations/reflection/next steps, ready to paste into a Custom
  HTML block. Entering it as an actual published Case Study post is a
  manual wp-admin step (see that file's own instructions) — not done by
  this ticket, since it requires wp-admin access this environment doesn't
  have.
- **Case Study hero layout options** — 3 new opt-in variants
  (`split-left`, `compact`, `stacked`) alongside the Sprint 4D default,
  selectable per case from a "Hero layout" field on the meta box. Mobile
  is identical across all 4 (text-first, stacked) — enforced structurally
  via `@media (min-width: 1000px)` after a real bug was caught in testing
  (a naive `@media (max-width: 999px)` override lost a CSS specificity
  fight against the `split-left` variant, breaking mobile stacking for
  that one variant — see `estavillo-child/assets/css/case-study.css`).
  Also fixed the hero's vertical alignment (`start` → `center`) so a long
  excerpt no longer strands the image with a lopsided empty gap.
- **Breadcrumbs** — Home / Work / case title, on the Case Study page only,
  reusing `es_nav_links()` for the "Work" link (repoint it once a real
  Work page exists and every breadcrumb follows).
- **4 new fixed pages** — Work, About, How I Work, Contact
  (`templates/page-*.php`), all standalone and sharing the ESTAVILLO
  chrome (sticky header + footer) with Home and Case Study. Work splits
  Case Studies into "Selected" and "Archive" by reusing the existing
  "Show on Home" flag (no new field). About gained 4 new Phase-1 fields
  (CV URL, timeline, education, hobbies) on the Home Content options
  page. How I Work and Contact add zero new fields — they just give
  existing Home data (process steps, Connect/Footer fields) their own
  dedicated pages.
- **How I Work icons** — a curated, whitelisted library of 8 inline SVG
  icons (`es_process_icon_choices()` / `es_process_icon_svg()` in
  `functions.php`), selectable per step from Home Content. Never
  arbitrary HTML — the admin only ever stores a whitelisted key.
- **Polylang page strategy** — documented, not built: `home_url()` and
  WordPress permalinks already resolve per-language natively once
  Polylang is active, so breadcrumbs and nav links need no extra code.
  Exact wp-admin steps for translating all 5 fixed pages are in
  `estavillo-child/README.md` → "Polylang".
- **V1/V2 framing** — added to `README.md`, `EDITABILITY-PLAN.md`: V1 is
  this fast, options-page-and-filters system; V2 (unplanned in detail) is
  a future migration of the same sections to Gutenberg blocks/patterns,
  reading the same underlying data/filters.

No Home sections, global typography, or global colors were touched.
`dist/estavillo-child.zip` and `dist/estavillo-portfolio-core.zip` both
rebuilt (the plugin changed this sprint — new hero-layout field, Work
query, About fields, icon choices in `home-content-options.php`).

**Sprint 4H — Infra/polish: sticky header + breadcrumbs fixes, richer How
I Work / Contact pages, hobbies system — done.** Backup branch confirmed
intact, working tree clean before starting.

- **Sticky header, root cause found and fixed.** `.es-page` had
  `overflow-x: hidden` (base.css) — an ancestor of `.es-site-header` and
  `.es-case-index` on every single template. Per the CSS Overflow spec,
  setting only `overflow-x` (not `overflow-y`) makes the browser compute
  `overflow-y: auto` too, turning `.es-page` into its own scroll-clipping
  container — which breaks `position: sticky` for any descendant (it
  sticks relative to `.es-page`, not the viewport). Verified with a real
  scroll test (not just checking the computed `position` value, which is
  what let this ship undetected — the header's `getBoundingClientRect().top`
  went to -387px after scrolling with the old CSS, confirmed pinned at 0
  with the fix). The one real horizontal-bleed source (the mobile hero
  SVG, `right:-10%`/`width:120%`) was already self-contained by
  `.es-hero { overflow: hidden }`, so removing the redundant rule was
  safe. No new admin control — sticky is automatic, always was meant to
  be.
- **Breadcrumbs, root cause found and fixed.** Two separate bugs: (1)
  `template-parts/breadcrumbs.php` was only ever called from
  `single-es_case_study.php` — never wired into Work/About/How I
  Work/Contact. (2) Its CSS lived in `case-study.css`, which only loads on
  the Case Study template — so even wiring it in elsewhere would have
  rendered unstyled text. Fixed both: moved the CSS to `site.css` (shared
  chrome, loads everywhere the header does) and added a reusable
  `es_breadcrumb_trail()` helper (`functions.php`), now called from all 5
  interior pages. Home deliberately has none.
- **How I Work dedicated page rebuilt.** New
  `template-parts/how-i-work-detail.php` — an editorial vertical sequence
  (numbered marker column + connecting line, not a SaaS icon-circle
  timeline), showing 3 new optional per-step fields ("Why it matters",
  "Example", "Tools" — rendered as pills) that the compact Home teaser
  never shows. Extracted the shared step-defaults and icon-rendering logic
  into `functions.php` (`es_home_process_steps()`,
  `es_process_step_icon_markup()`) so the teaser and detail page can't
  drift out of sync. Icon library grew from 8 to 10 (added `map` and
  `tool`, the two the ticket named explicitly that weren't covered yet).
- **Contact page hierarchy fixed.** Root cause: `page-head.php` printed
  "Contact." at H1 scale, then `contact-content.php` immediately printed
  "Let's talk." at `.es-footer-cta__title` scale — bigger than the H1
  above it. Now "Let's talk." renders at a deliberately smaller,
  supporting scale (`.es-contact-page__statement`, between H2 and lead).
  Added 2 new optional fields: a secondary note and an availability/status
  line (rendered as the same status-pill component Featured Case already
  uses).
- **About hobbies/interests, rebuilt as a real structured system.**
  Replaced the single comma-separated text field with up to 8 rows (label,
  icon, optional short text, Show/hide checkbox) — ships with the 7
  suggested interests pre-filled as real default content (not empty
  scaffolding). New curated icon library (taekwondo, music, coffee, horse,
  drawing, travel, cinema), each with a single CSS hover/focus
  micro-interaction (transform/opacity only, triggered by
  `:hover`/`:focus-visible`, never a loop) — covered automatically by the
  sitewide `prefers-reduced-motion` rule. Keyboard-focusable
  (`tabindex="0"`) for interaction parity; the label is always visible so
  touch users never depend on hover to understand an item.
- **"Home Content" renamed to "Portfolio Content"** (visible label only —
  menu slug, option key, and every function/filter name unchanged, so no
  saved data or bookmarked URL breaks).
- New README section, "Where to edit each part of the portfolio" — maps
  every field above to its exact wp-admin location, plus what's not
  editable yet and what V2 will change.

---

## Sprint 5 — Hero variants

**Goal:** expand hero variety only once the above is stable — not before.
This is the **Home animated hero** (the `network_constellation` /
`blueprint_flow` motion engines in `hero-system-map.js`) — a different
system from the Case Study hero *layout* variants added in Sprint 4G
above (those are static content arrangement, no motion/canvas involved).

- Add only 2–3 curated additional hero variants.
- `network_constellation` remains the default; this does not change.
- Avoid building a large hero gallery until content and editability are
  stable — variety is not the current bottleneck.

---

## Working principles across all sprints

- One ticket = one section or one concern. Keep diffs small and reviewable.
- Each sprint should be broken into individual tickets in `BACKLOG.md`
  before work starts, not decided ad hoc mid-sprint.
- No sprint should silently expand into a redesign. If a fix reveals a
  larger structural problem, log it as a new backlog item and finish the
  original scope first.
