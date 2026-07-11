# Backlog — Estavillo Portfolio (Kadence child theme)

Flat, prioritized list of known work. Each item should become its own small
ticket when picked up — do not bundle unrelated items into one prompt/PR.

Priority labels:

- **P0** — must fix before publishing
- **P1** — important polish
- **P2** — later improvement
- **P3** — future idea

Items are grouped by area for readability, but priority (not grouping)
determines pickup order. See `ROADMAP.md` for how these map onto sprints.

---

## Bugs / fixes

- **Done (Sprint 4C)** — Blue interaction states (was: mobile menu
  active/pressed, especially "Work"). Fixed globally, not just on the
  mobile menu: desktop nav brand/links, the menu button, footer nav/meta
  links, buttons, arrow links, cards, and the new case-study index/body
  links all now carry explicit rest/hover/focus-visible/active/visited
  color rules reinforced with `!important` (the reinforcement is scoped to
  defending against external/adversarial CSS — e.g. Kadence or a caching
  plugin re-injecting a blue `a:visited` — never used for the theme's own
  internal cascade). Verified with a fake-Kadence adversarial-CSS-injection
  Playwright test across desktop and mobile.
- **P0** — Selected Work / case CTA links should be white/neutral by
  default and turn green **on hover only**, not permanently green.
  (`.es-card__cta` currently sets a base `color: var(--es-accent)` — this
  contradicts the intended default state and needs correcting.)
- **Done (Sprint 4)** — Mobile hero secondary CTA ("See how I work") no
  longer reads as an accidental wrap below the primary button. Root cause:
  the primary button's internal padding (26px) meant its label started
  ~26px to the right of the secondary link's text, even though both
  shared the same container edge — a real, measurable misalignment, not
  just a vibe. Fixed by aligning the secondary link's text to the
  button's text (`padding-left: 26px` at the ≤680px stack breakpoint) —
  CSS-only, same component, same copy, same animation. Verified via
  computed-position diffing (0.0px gap) at 375/390/428px plus visual
  screenshots. Broader Hero block/layout work (mobile-specific
  constellation composition, editability) remains its own future ticket —
  this fix was scoped narrowly to the one reported alignment bug.
- **P1** — Mobile hero needs a better mobile-specific constellation layout
  (current mobile treatment is a scaled-down desktop layer, not a
  purpose-built mobile composition).
- **P1** — Footer line/rhythm feels unresolved despite the recent spacing
  refinement.
- **P1** — Hero-to-How-I-Work transition still feels slightly awkward even
  after the gradient softening fix (border removal + fade).

## Design polish

- **P1** — How I Work needs a future icon/motion treatment per step. Slots
  are reserved (`.es-process__icon`, `.es-process__icon--empty`) — this
  item is about designing and filling that slot, not changing layout.
- **P1** — Footer needs a more intentional "ending" — it should read as a
  deliberate close to the page, not just a rule + links.
- **P2** — Header alignment (logo / nav / language / toggle slot) is
  acceptable for now. Do not over-tune; revisit only if a specific problem
  is identified.
- **P3** — Theme toggle: the header slot (`.es-nav__toggle`) is a
  placeholder only. Do not wire up functionality until light mode itself is
  actually implemented as a design system (colors, tokens, contrast pass).

## Content / editability

- **P0** — Home cannot remain a rigid PHP-only template forever; the goal
  is to convert Home sections progressively into editable blocks/sections
  (Header, Hero, Selected Work, Featured Case, How I Work, About,
  Connect/Footer) — not one huge PHP-only template forever. See
  `EDITABILITY-PLAN.md`. Do this as small, ticket-sized section
  conversions, one at a time — not a single big migration.
- **Done (Sprint 3)** — **Selected Work** is now the first section
  converted to editable content: a "Case Study" custom post type, editable
  in wp-admin, with a hardcoded-placeholder fallback so Home never breaks
  if no case studies exist yet. See `EDITABILITY-PLAN.md` for exact usage.
- **P1** — Enter Presupuestador and Trazur as real, published Case Study
  posts. **Presupuestador's body content is ready**, now as two paste-ready
  masters — `docs/content/presupuestador-case-study-es.html` and `-en.html`
  (13 sections each, `#overview` → `#next`), with `-fields.md` (exact native
  field values per language) and `-wordpress-publish.md` (step-by-step
  publish order incl. Polylang linking). Sprint 4I (see `ROADMAP.md`) added
  two real diagrams to both language masters using the new `.es-case-ladder`
  and `.es-case-taxonomy` components (Overview's stage sequence, System's
  pricing-variable map) — content-only, no new anchors, no invented
  numbers. What's still needed: pasting either file into a Custom HTML
  block on a real Case Study post (wp-admin step, not done by this
  ticket — no wp-admin access from this environment), the native fields
  from the field sheet, real screenshots/photos replacing the
  `.es-placeholder` figures (see `presupuestador-assets-plan.md`), and
  picking a "Hero layout" once a real featured image exists. **Trazur's
  content still needs to be written** (no HTML deliverable yet — this
  ticket only covered Presupuestador).
- **Done (Sprint 3)** — **Featured Case** is editable: reuses the Case
  Study CPT with a "Feature this case on Home" checkbox instead of a
  separate mechanism. See `EDITABILITY-PLAN.md`.
- **Done (Sprint 3)** — **About** is editable: a plugin-owned "Home
  Content" options page (wp-admin) now hooks the `es_home_about_text` /
  `es_home_about_url` / `es_home_about_portrait` filters that already
  existed since Home v1 — no theme template changes were needed. See
  `EDITABILITY-PLAN.md`.
- **Done (Sprint 3)** — **How I Work** is editable from the same "Home
  Content" page: each of the 6 steps (title + text) can be edited
  individually — leaving a step blank keeps its current placeholder, so
  editors don't have to fill in all 6 at once. Icon slot stays reserved
  (out of scope, design/motion territory).
- **Done (Sprint 3)** — **Connect** is editable from the same "Home
  Content" page: title, lead text, contact email, and CTA URL. The
  contact email field is shared with the Footer (same `es_contact_email`
  filter both sections already used) — one field updates both.
- **Done (Sprint 3)** — **Header** nav links are editable from the same
  "Home Content" page (4 label+URL rows, edited individually). The same
  array feeds the desktop nav, the mobile menu, and the footer nav — one
  edit updates all three, since they already shared `es_nav_links()`.
- **Done (Sprint 3)** — **Footer** is editable from the same "Home
  Content" page: LinkedIn URL, Behance URL, and location. Nav links and
  contact email were already covered by the Header/Connect tickets above
  (same shared data), so Footer only needed its two genuinely footer-only
  fields.
- **Done (Sprint 4B)** — Case Study now has a real single page:
  `single-es_case_study.php`, reusing the ESTAVILLO chrome, rendering
  title, eyebrow, excerpt, tags, featured image (or placeholder), a
  Status/Role/Tools/Period meta row, and the standard WordPress editor
  body via `the_content()`. No case builder — the body is normal editor
  content, same as any WordPress post. Role/Tools/Period are new optional
  plain-text fields on the existing Case Study meta box.
- **Done (Sprint 4A)** — Polylang readiness: the Case Study CPT registers
  itself as translatable via `pll_get_post_types` (self-configuring, no
  manual settings step); `es_case_tag` deliberately stays language-neutral
  for V1 (see `EDITABILITY-PLAN.md`); the header and mobile-menu language
  indicators are now a real Polylang switcher (`pll_the_languages()`),
  guarded by `function_exists()` so the site is unaffected if Polylang is
  inactive.
- **Done (Sprint 4C)** — Case Study format system: an optional sticky
  in-page index (manual `Label|#anchor` field, new `_es_case_index` post
  meta) and a 14-class `.es-case-*` library (section, label, heading, lead,
  two-column, figure+caption, browser-chrome frame, stats grid, timeline,
  decision cards, pullquote, status grid, native accordion) usable inside
  the standard editor via `the_content()` — no case builder, no ACF. See
  `estavillo-child/README.md` for the full reference and example.
- **Done (Sprint 4G / mega sprint)** — 4 new fixed pages (Work, About, How
  I Work, Contact), all standalone page templates sharing the ESTAVILLO
  chrome. Work reuses the existing "Show on Home" flag to split Selected
  vs. Archive (no new field). About gained 4 new Phase-1 fields on Home
  Content (CV URL, career timeline, education/certificates, hobbies). How
  I Work and Contact reuse existing data with zero new fields. Case Study
  gained breadcrumbs and 3 new opt-in hero layout variants. See
  `estavillo-child/README.md` → "Páginas fijas", "Hero layout options",
  "Breadcrumbs".
- **P3** — Work's Selected/Archive split is binary (reuses the existing
  "Show on Home" checkbox — one flag serves two purposes now). This is
  fine while there's no real content, but if a case ever needs to be
  "hidden from the Home teaser but still current work, not archive" the
  one flag can't express that 3-way distinction. Revisit only if that
  scenario actually comes up — don't add a second flag speculatively.
- **P3** — About's career timeline and education/certificates are capped
  at 4 fixed rows each (same pattern as How I Work's 6 fixed steps). If
  a real timeline ever needs more than 4 entries, this becomes a proper
  repeater — not urgent, most careers fit in 4 rows for a portfolio.
- **Done (Sprint 4H)** — How I Work dedicated page rebuilt as a real
  editorial page (`template-parts/how-i-work-detail.php`), not a reused
  copy of the Home teaser: same 6 steps, plus 3 new optional per-step
  fields (Why it matters / Example / Tools) shown only there. Contact
  page's heading hierarchy fixed (two competing giant headings → one H1
  from page-head, one smaller supporting statement below) and gained 2
  optional fields (secondary note, availability/status line). About's
  hobbies/interests rebuilt from a single comma-separated field into 8
  structured rows (label, icon, optional text, show/hide) with a new
  7-icon curated library and CSS-only hover/focus micro-interactions.
  "Home Content" renamed to **"Portfolio Content"** in wp-admin (label
  only — option key/slug unchanged, no data lost). See
  `estavillo-child/README.md` → "Where to edit each part of the
  portfolio" for the full field reference.
- **P3** — Hobbies' "order" is row position (same convention as every
  other repeater in this codebase — Nav Links, Process Steps, Timeline,
  Education), not a separate numeric field. If that ever feels
  insufficient in practice, add one then — not speculatively now.
- **P2** — Convert **Hero** to editable content (copy/CTAs are already
  filterable; coordinate with the Hero block/layout ticket above so
  editability doesn't get built on top of a layout that's about to
  change).
- **P2** — Real images / final asset placeholders across Home sections.
- **P2** — Refine EN/ES copy for all Home sections.
- **P2** — Create the Home (ES) page (Polylang translation of the existing
  Home page, same template) once real content is ready — mechanical setup
  step in wp-admin, no code required (see `EDITABILITY-PLAN.md`).

Editability priority order (for reference, full detail in
`EDITABILITY-PLAN.md`):

1. Selected Work — **done** (Case Study CPT)
2. Featured Case — **done** (Case Study CPT, "featured" flag)
3. About — **done** (Home Content options page)
4. How I Work — **done** (Home Content options page, per-step)
5. Connect — **done** (Home Content options page)
6. Header — **done** (Home Content options page, nav links)
7. Footer — **done** (Home Content options page, social links + location)
8. Hero — not started (deferred until the Hero block/layout ticket lands)

## Accessibility / UX

- **P2** — Case Study sticky index: currently a single horizontal strip
  under the header on all viewport sizes (deliberately kept simple in
  Sprint 4C). Future improvement once there's real multi-section case
  content to test against: a **desktop-only side index pinned to the
  left** of the case body (e.g. sticky column running alongside
  `.es-case__body`), while mobile keeps today's horizontal/compact strip
  unchanged. Not built now — do not overbuild the index ahead of real
  content.
- **Done (Sprint 4H)** — Sticky header was silently broken in real
  WordPress (visibly not sticking on scroll) despite `position: sticky`
  being present in the CSS — root cause was `.es-page { overflow-x: hidden }`
  (base.css) turning `.es-page` into its own scroll-clipping ancestor,
  which breaks `position: sticky` for any descendant per the CSS Overflow
  spec. Fixed by removing that rule (the one real horizontal-bleed source,
  the mobile hero SVG, was already self-contained by its own
  `.es-hero { overflow: hidden }`). No admin toggle — sticky is automatic
  on every ESTAVILLO template. Supersedes the "behavior/visual polish"
  framing this item used to have below — the bug was functional, not
  cosmetic.
- **P2** — Optional "back to top" interaction.
- **Done (Sprint 4G / mega sprint, then Sprint 4H)** — Breadcrumbs (Home /
  Work / case title) on the Case Study single page, accessible
  (`<nav aria-label="Breadcrumb">`, `aria-current="page"`), Polylang-aware
  automatically (native `home_url()`/permalink behavior, no extra code).
  Sprint 4H extended them to Work/About/How I Work/Contact (they render
  everywhere the ticket asked, never on Home) and fixed a second bug: the
  breadcrumb CSS lived only in `case-study.css` (Case-Study-only), so it
  would have rendered unstyled on the other 4 pages even once wired in —
  moved to `site.css` (shared chrome). See `template-parts/breadcrumbs.php`
  and `functions.php` → `es_breadcrumb_trail()`.

## Hero variants

- **P3** — Add 2–3 curated additional hero variants, only after Home
  content and editability are stable. `network_constellation` stays the
  default. Do not build a large hero gallery — variety is not the current
  bottleneck.

## Process

- **P0** — Adopt the ticket-based workflow documented in this `docs/`
  folder for all future work; avoid large token-heavy redesign prompts
  (see `DECISIONS.md`).
