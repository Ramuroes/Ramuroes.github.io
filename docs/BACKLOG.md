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
- **P1** — Add real case content for Presupuestador and Trazur, now that
  the Case Study CPT **and its single template** exist (Sprint 4B) — enter
  them as Case Study posts (title, excerpt, tags, featured image, Role/
  Tools/Period, and full body via the standard editor), not template
  edits.
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

- **P2** — Sticky header refinement (behavior/visual polish beyond the
  current acceptable state — see `BACKLOG.md` header alignment note
  below).
- **P2** — Optional "back to top" interaction.
- **P2** — Breadcrumbs / accessibility strategy for internal case-study
  pages — relevant once single Case Study pages are actually built (see
  the Case Study CPT above; no single-case template exists yet).

## Hero variants

- **P3** — Add 2–3 curated additional hero variants, only after Home
  content and editability are stable. `network_constellation` stays the
  default. Do not build a large hero gallery — variety is not the current
  bottleneck.

## Process

- **P0** — Adopt the ticket-based workflow documented in this `docs/`
  folder for all future work; avoid large token-heavy redesign prompts
  (see `DECISIONS.md`).
