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

**Status:** In progress.

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
- Header, Footer — next, same "Home Content" options page, same pattern
  (hook pre-existing filters, no new theme plumbing).
- Hero — deliberately deferred until the Hero block/layout ticket flagged
  in `BACKLOG.md` lands first.

See `EDITABILITY-PLAN.md` for usage and the architecture rationale.

**Flagged during this sprint, deliberately not built now** (logged to
`BACKLOG.md` instead of expanding this sprint's scope):
- A dedicated Hero block/layout ticket — the mobile secondary-CTA stacking
  still doesn't feel intentional even after the Sprint 1 CSS fix; needs its
  own focused pass, not another isolated patch.
- Accessibility/UX items: sticky header refinement, an optional "back to
  top" interaction, and a breadcrumbs/accessibility strategy for internal
  case-study pages (relevant once a single Case Study template exists).

---

## Sprint 4 — Real content

**Goal:** replace placeholder content with the real portfolio material.

- Add real case content for Presupuestador and Trazur.
- Add real images (or intentional, final placeholders where images aren't
  ready yet).
- Refine copy in both EN and ES.

---

## Sprint 5 — Hero variants

**Goal:** expand hero variety only once the above is stable — not before.

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
