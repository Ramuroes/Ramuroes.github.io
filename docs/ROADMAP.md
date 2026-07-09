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

**Sprint 4C — EN/ES Home strategy — next.** Create the Home (ES) page as
a Polylang translation of the existing Home page (same template, no new
code) once real Spanish copy exists. Verify hero copy / UI strings
resolve correctly per language through the existing `es__()` /
`pll_register_string()` wiring.

**Sprint 4D — Real content loading — next.** Enter real Presupuestador and
Trazur case studies as Case Study posts (now that the single template
exists to display them properly), in both languages via their Polylang
translations. Add real images. Refine EN/ES copy for all Home sections
(within the single-language-global constraint of Home Content options —
see Sprint 3 notes above).

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
