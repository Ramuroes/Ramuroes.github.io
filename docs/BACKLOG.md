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

- **P0** — Mobile menu active/pressed state still turns blue on some links,
  especially "Work". Must never show blue anywhere.
- **P0** — Selected Work / case CTA links should be white/neutral by
  default and turn green **on hover only**, not permanently green.
  (`.es-card__cta` currently sets a base `color: var(--es-accent)` — this
  contradicts the intended default state and needs correcting.)
- **P0/P1** — On mobile, the secondary hero link "See how I work" still
  drops below the primary CTA in a way that doesn't feel intentional, even
  after the Sprint 1 stacking fix (explicit column layout at ≤680px). Do
  not patch this again with an isolated CSS tweak — it needs its own
  focused Hero block/layout ticket (see `ROADMAP.md`), not a quick fix
  bundled into unrelated work.
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
  the Case Study CPT exists (Sprint 4) — enter them as Case Study posts,
  not template edits.
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
- **P2** — Convert **Hero** to editable content (copy/CTAs are already
  filterable; coordinate with the Hero block/layout ticket above so
  editability doesn't get built on top of a layout that's about to
  change).
- **P2** — Real images / final asset placeholders across Home sections.
- **P2** — Refine EN/ES copy for all Home sections.

Editability priority order (for reference, full detail in
`EDITABILITY-PLAN.md`):

1. Selected Work — **done** (Case Study CPT)
2. Featured Case — **done** (Case Study CPT, "featured" flag)
3. About — **done** (Home Content options page)
4. How I Work — **done** (Home Content options page, per-step)
5. Connect — **done** (Home Content options page)
6. Header — **done** (Home Content options page, nav links)
7. Hero

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
