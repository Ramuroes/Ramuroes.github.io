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
- **P0** — On some real phones, "See how I work" wraps below the main CTA
  in the hero actions row. Define the responsive behavior intentionally
  (explicit stacking/wrap rule) instead of leaving it to accidental
  flex-wrap behavior.
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

- **P0** — Home cannot remain a rigid PHP-only template forever; needs a
  path toward WordPress-editable content. See `EDITABILITY-PLAN.md`.
- **P1** — Make **Selected Work** the first section converted to editable
  content (highest priority per the editability order below).
- **P1** — Add real case content for Presupuestador and Trazur (Sprint 4).
- **P2** — Convert **Featured Case** to editable content.
- **P2** — Convert **About** to editable content.
- **P2** — Convert **How I Work** to editable content.
- **P2** — Convert **Connect** to editable content.
- **P2** — Real images / final asset placeholders across Home sections.
- **P2** — Refine EN/ES copy for all Home sections.
- **P3** — Structured Case Study content model (CPT or equivalent) so
  portfolio cases can be authored without editing PHP or asking for a
  Claude Code session per case.

Editability priority order (for reference, full detail in
`EDITABILITY-PLAN.md`):

1. Selected Work
2. Featured Case
3. About
4. How I Work
5. Connect

## Hero variants

- **P3** — Add 2–3 curated additional hero variants, only after Home
  content and editability are stable. `network_constellation` stays the
  default. Do not build a large hero gallery — variety is not the current
  bottleneck.

## Process

- **P0** — Adopt the ticket-based workflow documented in this `docs/`
  folder for all future work; avoid large token-heavy redesign prompts
  (see `DECISIONS.md`).
