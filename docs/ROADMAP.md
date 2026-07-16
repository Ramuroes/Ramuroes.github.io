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

**Sprint 4I — Presupuestador × REstimator design-reference adaptation —
done.** A Claude Design handoff bundle (a separate design-exploration
repo, "REstimator (Dark)" case-study mockup) was reviewed as visual,
interaction, and narrative *reference only* — not copied, not used as a
content source, and its runtime (`support.js`) was never touched. Three
small tickets:

- **CSS (additive only).** Two new opt-in `.es-case-*` components in
  `case-study.css`: `.es-case-ladder` (horizontal stage/phase chip
  sequence, `--active` marks the current stage using the existing
  `--es-accent` green — never `--es-decision` orange, which stays reserved
  for a single decision/focus moment) and `.es-case-taxonomy` (root label +
  variable/category grid + an optional "adjusted by…" modifier row). Same
  rules as every existing class here: only `--es-*` tokens, no new
  colors/fonts, no JS, invisible to every case that doesn't use them.
- **Content (Presupuestador masters only).** Enriched
  `presupuestador-case-study-{es,en}.html` in place: a `.es-case-ladder` in
  `#overview` showing the real evolution (diagnosis → documented criteria →
  Sheets MVP → calibration → App Alpha *(marked active — true today per
  `#results`)* → team-wide adoption *(explicitly the unproven goal, not a
  claim)*), and a `.es-case-taxonomy` in `#system` visualizing the pricing
  variables already named in `#discovery`'s prose (material/thickness, cut
  complexity, finishing, machine availability, adjusted by client
  relationship and urgency). All 13 anchors, their order, and the Case
  Index untouched. No numbers were introduced — the reference mockup's
  illustrative stats (file counts, record counts, override ratios) were
  deliberately **not** reintroduced; that content-integrity call was
  already made once (see the editorial note at the top of both HTML
  files) and stands.
- **Docs.** `estavillo-child/README.md`'s class table and copy-paste
  example gained the two new classes; the example's `es-case-stat` values
  (previously `~650` / `1,600+` — traceable to the same reference mockup)
  were replaced with an obviously generic `N` placeholder plus an explicit
  "don't invent this number" note, so the illustrative figures no longer
  live anywhere in this repo as something that reads like real data.

No template, CPT, or JS changes; no new fields; no dark/light theme
toggle added (theme stays dark-first per `DECISIONS.md`). Publishing
Presupuestador to wp-admin is still the open step described in Sprint 4G's
entry above and `BACKLOG.md`.

---

## Sprint 4J — Gutenberg migration for Case Studies (visibility fix + width system + block library) — done

**Goal:** make Case Studies editable from Gutenberg without raw HTML, and
fix the two production problems found when publishing Presupuestador (body
invisible; body too narrow vs. the hero).

- **Visibility root cause (confirmed empirically in Chromium).** The single
  template put `data-es-reveal` on the entire `.es-case__body` (one element
  thousands of px tall) and `motion.js` observed it with an
  IntersectionObserver `threshold: 0.12`. An element taller than ~8× the
  viewport can never reach a 12% intersection ratio, so `.es-in` never
  arrived and the body stayed at `opacity: 0` forever. Fix in three layers:
  the body no longer participates in the reveal system; `motion.js` now
  observes with `threshold: 0` and adds an `es-motion` gate class on
  `<html>` right before observing; the hidden state in `components.css`
  only applies under `html.es-motion`, so with JS blocked/broken/delayed
  nothing is ever hidden, and `prefers-reduced-motion` shows everything
  without animating. The temporary Customizer "Additional CSS" override is
  obsolete — delete it after updating the theme.
- **Editorial width system.** `.es-case__body` lost its old
  `max-width: 720px` and now spans the full `.es-container` (same edges as
  the hero). Reading content (paragraphs, lists, headings, captions,
  quotes, details) caps at `--es-case-measure` (820px); wide components
  (figure, browser, stats, ladder, taxonomy, decisions, status, timeline)
  may use the full container. Grid fixes folded in: `.es-case-status` is
  now a true 2-column pair (the old fixed `repeat(4,1fr)` left half the
  frame as a dead panel), stats/decisions use `auto-fit` so any item count
  fills the row. Opt-ins: `es-case-section--reading`,
  `es-case-figure--standard`, `es-case-ladder__step--done`.
- **Block library (plugin, v1.1.0).** 10 dynamic blocks under the new
  "Estavillo Case Study" inserter category, one folder per block with
  `block.json` + server `render.php` + no-build plain-JS editor:
  case-section, case-figure (MediaUpload + placeholder + browser frame),
  case-stats, case-ladder, case-taxonomy, case-timeline, case-decisions,
  case-status, case-quote, case-details. Frontend markup reuses the theme's
  `.es-case-*` classes 1:1 (no duplicated CSS, no frontend JS, no ACF, no
  remote libraries); the plugin bridges the theme's tokens + case CSS into
  the editor iframe so previews look like the real page.
- **Patterns.** "Estavillo — Presupuestador Case Structure (ES)" and
  "(EN)": the full 13-section case built from the new blocks, same anchors,
  honest copy and [DATA PENDING VALIDATION] / `{asset: …}` placeholders as
  the masters. Two patterns (one per language) instead of auto-detection —
  Polylang defines the post's language, not the pattern inserter's. The
  masters' two-column `.es-case-cols` groups became sequential text →
  figure flow (more editable, identical on mobile).
- **Backward compatible.** The existing Custom-HTML workflow and the
  `.es-case-*` library are untouched; existing posts keep rendering; no
  CPT/meta/Polylang changes.

---

## Sprint 4K — Approved hobby-icons artwork (About) — done

**Goal:** replace the placeholder 20×20 inline hobby icons with the
final APPROVED hand-drawn artwork (estavillo-hobby-icons.zip) and give
the About "Hobbies & interests" section a premium compact layout —
without redrawing the artwork, redesigning the page, or touching the
editability model.

- **Assets.** 8 approved SVGs installed as `estavillo-child/assets/icons/`
  (`taekwondo`, `guitar`, `coffee`, `horse-head`, `horse-run`, `drawing`,
  `travel`, `cinema`). Metadata-only normalization (square centered
  viewBox, `fill="black"` → `currentColor`, `aria-hidden`) — path data
  byte-identical to the upload, asserted by the integration script.
- **Registry.** `es_hobby_icon_library()` now reads the files (static
  per-request cache) instead of holding inline strings; the wp-admin
  select offers the 8 approved choices; legacy saved keys keep working
  via `es_hobby_icon_resolve_key()` (`music` → `guitar`, `horse` →
  `horse-head`) on both the frontend and the admin select.
- **Layout.** Pill chips → compact editorial grid with `--es-line`
  hairlines; 28px icons (34px box for the landscape horse-run), neutral
  ink at rest, accent green + 2px lift on hover/:focus-visible (V1 only —
  per-icon animations reserved for a V2 via `.es-hobby-icon--{key}`
  wrappers). Motion respects `prefers-reduced-motion`; reveal reuses the
  existing fail-safe system.
- **Validated** with the mock-WP PHP harness (real functions.php + real
  about-content.php render, kses whitelist proof, alias proof) and
  Chromium (desktop/mobile/hover/focus/light/reduced-motion/JS-off, no
  overflow, no blue states).

---

## Sprint 4L — Case Study editorial composition system (spec "Grid System v1") — done

**Goal:** implement the approved editorial grid inside the existing
Gutenberg Case Study blocks — native to Gutenberg, safe for
non-technical editing, reusable by Presupuestador, Trazur, French
Bakery and Samic. No redesign of anything else; no page builder.

- **Container.** Case body only: `.es-container--case` at 1320px.
  Header/footer/nav and the case hero stay at 1140 untouched.
- **Grid.** 12 col / 32px gutter desktop (≥1024), 6 col / 24px tablet
  (768–1023, splits stack), 1 col mobile (<768). The same
  `.es-case-section__grid` wrapper exists in frontend (render.php) and
  editor (useInnerBlocksProps) — identical composition both sides.
- **Case Section presets (locked).** `layout`:
  reading (cols 3–10 + 72ch hard cap) / split-left 5-7 / split-right 7-5
  (media first visually via order, DOM unchanged) / split-balanced 6-6 /
  wide (cols 1–12, flat legacy markup — zero regression). `mobileOrder`:
  desktop order default / content-first / media-first (stacked only).
  `spacing`: compact 96 / standard 120 / spacious 144 (total chapter
  gap, hairline in the middle). No px, no column numbers, no arbitrary
  margins in the UI.
- **Split regions.** Two new internal blocks (case-split-content /
  case-split-media), parent-locked + inserter-hidden, template locked;
  switching layouts restructures children automatically and undoably;
  text-only Wide shows an editor notice; editor-only variant chip.
- **Patterns.** "Case Study — Editorial System Demo" (canonical order:
  Reading → Split 5/7 → Wide figure → Wide Stats+Ladder → Split 7/5 →
  Balanced → Reading close with Quote/Details; 100% fictional copy) and
  "Case Study — Canonical Starter" (Reading → Split → Wide → Reading
  close, scaffolding copy). Presupuestador content untouched — migration
  is a later ticket by design.
- **Validated**: render harness through the real render.php files
  (incl. Presupuestador ES/EN regression), mock-wp editor harness
  (conversions, guardrails, template locks), Chromium at
  320/375/390/768/1024/1440 (container 1320, 12/32 + 6/24 grids, exact
  5/7-7/5-6/6 ratios, 72ch reading, 96/120/144 spacing, 4 mobile-order
  combinations, dark/light, JS-off, reduced-motion, zero overflow).

---

## Sprint 4M — Case Study editorial system: architecture correction — done

**Goal:** correct the Case Section composition model based on real
WordPress testing of Sprint 4L. Not a redesign — a narrowly scoped fix
to a rigid architecture that produced a bad editing experience.

**Problem confirmed in real WordPress:** Sprint 4L's locked Split
architecture (fixed Content/Media regions on a custom 12-column CSS
grid) forced content into narrow predefined areas and could leave a
large, unintended empty area on the right. Root cause: a CSS rule
inherited from the original width system (Sprint B) applied
`max-width: 820px` to any paragraph that was a direct child of a Case
Section, regardless of the section's chosen width — so a "Content"
chapter's full-width paragraph was silently capped, leaving unused
space beside it.

**Fix:**
- Case Section is now a genuinely flexible chapter container: label/
  heading/lead (unchanged) + **unrestricted InnerBlocks** — no
  allowedBlocks, no template, no templateLock. Editors insert and
  reorder anything (Heading, Paragraph, List, Image, Gallery, Group,
  Row, Stack, native Columns/Column, existing Estavillo blocks) with
  normal Gutenberg controls.
- Three chapter-level attributes only: `layout` — **Content** (default,
  full 1320px container, no measure cap), **Reading** (whole chapter
  ~72ch, the only width that constrains prose), **Wide** (same width as
  Content; distinction is documentation-only); `spacing` (compact 96 /
  standard 120 / spacious 144, unchanged); new `divider` boolean
  (chapter hairline on/off).
- Removed entirely: split-left/split-right/split-balanced, mobileOrder,
  the locked Content/Media region blocks' use in new content, the
  custom 12-column CSS grid, and all automatic block-wrapping/
  unwrapping on layout change.
- The actual bug fix: `.es-case-section--content`/`--wide` now
  explicitly cancel the inherited measure cap on direct-child
  paragraphs/lists/headings; only `--reading` constrains width, and it
  does so on the whole section (not per-child).
- Column-left/image-right compositions are built with **native
  Gutenberg Columns/Column** (33/66, 40/60, 50/50, 60/40, 66/33) inside
  a Content chapter — Case Section adds a consistent gap token and
  otherwise does not touch this composition or its responsive stacking
  (core's own, not reimplemented).
- Both patterns rewritten: "Case Study — Editorial System Demo" (7
  chapters: Content w/ full-width paragraph, Columns 40/60, Columns
  60/40, Wide figure+Stats+Ladder, Reading, Columns 50/50, Reading close
  w/ Quote+Details) and "Case Study — Canonical Starter" (Content →
  Content w/ Columns 40/60 → Wide → Reading close).
- Backward compatible: `case-split-content`/`case-split-media` block
  types stay registered (parent-locked, inserter-hidden) purely so any
  already-saved post using the old architecture keeps rendering (now as
  a flat, unstyled sequence rather than a styled split — recommended to
  recreate from the corrected pattern rather than edit in place).
  Presupuestador patterns, Custom-HTML bodies, Polylang, hero, header,
  footer, breadcrumbs: untouched.
- Validated: render harness (both new patterns + Presupuestador ES/EN
  regression + a synthetic old-architecture post proving no fatal error
  and no content loss), mock-wp editor harness (free InnerBlocks, no
  auto-restructuring, simplified Inspector), Chromium at
  320/375/390/768/1024/1440 (exact 40/60/60/40/50/50 ratios via native
  Columns, 72ch Reading, full-width Content paragraph confirmed
  uncapped, native core stacking on tablet/mobile, dark/light, JS-off,
  reduced-motion, zero overflow).

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
