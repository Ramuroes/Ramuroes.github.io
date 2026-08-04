# Decisions log — Estavillo Portfolio (Kadence child theme)

Standing decisions for the project. These are settled unless explicitly
revisited and re-logged here — future tickets should not silently
re-litigate them.

---

## Design system

- **Default hero variant is `network_constellation`.** It has been the
  default through multiple concept explorations
  (`system_map_nodes` → `blueprint_flow` → `network_constellation`) and
  stays the default going forward. Do not swap the default without an
  explicit, separate decision.
- **Green (`--es-accent` / `--es-signal`) is the primary system/accent
  color.** It signals "system," "resolved," "interactive," "on."
- **Orange (`--es-decision`) is reserved for decision/focus moments only.**
  Use sparingly — it is not a general accent and should not compete with
  green for attention.
- **No blue interaction states, anywhere, ever.** Any blue appearing in
  hover/focus/active/visited states is a bug, not a style choice — it
  originates from Kadence/browser defaults leaking through, not from
  authored theme CSS. See `QA-CHECKLIST.md` and `BACKLOG.md` for the
  current known instances.
- **Dark-first.** The design system is built dark-first; light mode is not
  yet implemented as a first-class system (see the theme-toggle note in
  `BACKLOG.md`).
- **Typography is settled:** Newsreader (serif) / Instrument Sans (sans) /
  Spline Sans Mono (mono), with a font-preset toggle
  (`design_system` default / `classic_mockup` optional) via
  `body.es-font--*`. Do not change the type system as a side effect of
  unrelated tickets.

## Architecture

- **Home is currently a custom PHP page template**, chosen for build speed
  while the design system and hero were being established. This is a
  known, intentional trade-off — not the end state. It must evolve toward
  editable sections (see `EDITABILITY-PLAN.md`).
- **`es_home_sections()` (in `functions.php`) is the stable contract** for
  Home's section order and composition — a filterable, ordered map from
  section key to template part. Future editability work (block patterns,
  CPT, etc.) should build on top of this contract rather than replacing
  the page template loop from scratch.
- Content, links, nav items, and copy strings should go through filters
  (`apply_filters`) rather than being hardcoded, wherever that pattern is
  already established — this keeps the door open for editability work
  without requiring a rewrite.

## Process

- **Work in small, scoped tickets — not mega prompts.** Large,
  multi-concern redesign requests are expensive (token-heavy) and hard to
  verify safely. Prefer one section or one concern per ticket.
- **Each future Claude Code request should modify as few files as
  possible.** If a request seems to require touching many unrelated files,
  that's a signal it should be split into multiple tickets.
- **`docs/BACKLOG.md` is the source of truth for "what's next"** — pull
  the next ticket from there rather than inventing new scope mid-session.
- Never merge to `main` or open a PR unless explicitly asked.
- Always verify changes (PHP lint, JS syntax, visual/interaction check)
  before committing, and rebuild `dist/estavillo-child.zip` after any
  change that affects the theme's shipped files.
