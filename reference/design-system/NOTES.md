# Estavillo Visual Design System v1.0 — extracted rules

**Source material, not production code.** This folder holds Claude Design output
as reference artifacts:

- `Estavillo-Visual-Design-System.dc.html` — the full visual system (rules below).
- `Estavillo-Home-v4.dc.html` — motion source for the **`network_constellation`**
  default hero (the living node-network / constellation). Its behavior was
  re-implemented in vanilla JS in `assets/js/hero-system-map.js`; the DC file is
  reference only.
- `support.js` — the Claude Design canvas runtime. It must **never** be enqueued
  or executed on the WordPress site (it lives outside the theme, so WP never loads
  it). The `.dc.html` files reference it, but they are reference-only too.

The rules below are the useful parts, distilled so the theme can implement them
without shipping any of this HTML/JS.

## Tokens (dark-first)

| Role | Design system | Theme token |
|---|---|---|
| Paper / surfaces | `#0A0B0A → #1C201D` | `--es-paper*`, `--es-surface*` |
| Ink | `#EAEAE4 / #A4A59D / #6E6F68 / #45463F` | `--es-ink → --es-ink-4` |
| Hairlines | `rgba(255,255,255,.09 / .15 / .24)` | `--es-line`, `--es-line-strong` |
| **Signal (green)** | `#46D07F` (dim `.14`, `-2 #2FA968`) | `--es-signal*` |
| **Ember (orange)** | `#E8763C` (dim `.15`, `-2 #C25E2B`) | `--es-decision*` |

> The theme keeps its existing warmer green `#58B183` / `#2D6A4F` as the brand
> chrome accent (`--es-accent`), and adopts the design-system semantic pair
> `--es-signal` (green) + `--es-decision` (orange) for diagram meaning. See §05.

Type: **Spectral** (serif, editorial) · **IBM Plex Sans** (UI) · **IBM Plex Mono**
(technical labels). The theme's Newsreader / Instrument Sans / Spline Sans Mono
fill the same three roles (serif essay / sans interface / mono system) and are
kept — the *roles* are what matter, not the exact faces.

Radii: `999px` actions · `3px` panels · `2px` chips · `0` diagrams.
Grid: 12-col, 24px gutter, 8px spacing rhythm, content max **1180px**, reading
measure 62–66ch, page margin `clamp(20px,5vw,72px)`.

## §05 — Accent strategy (the core rule)

> **Green is the system. Orange is the moment of decision.**

Not two moods for two page types — two *meanings* on the same screen.

- **Neutral** = the field / structure / everything not doing something now.
- **Green (`--es-signal`)** = working & resolved path, live signals, success,
  primary action, active nav, brand primary.
- **Orange (`--es-decision`)** = the one point of human judgement or attention.
  **At most once per view.**

Do: one accent leads per view, the other only points; keep orange to a single
element. Don't: run both at full strength in the same block, tint whole
surfaces, or switch the meaning by page (the split this fixes).

## §07–10 — Graphic grammar

Every diagram is built from four primitives: **nodes · connections · regions ·
annotations**. If a graphic isn't describable as those, it isn't on brand.

- **Nodes** — shape = type: circle = entity/input/person, square = process/step,
  **diamond = decision**, double-ring = outcome. State = status: outline (idle),
  fill+halo (active, green), diamond+dot (focus, orange), solid+check (resolved),
  dashed (muted/out of scope).
- **Connections** — orthogonal on the grid, 90° turns with a soft 4px corner.
  Weights: 1px guide · 1.4px standard · 1.7–1.8px active (green). Dotted =
  inferred/axis. **Diagonals are reserved for one thing only: a decision branch.**
  Lines stop at a node's edge, never cross it; junctions get a small dot.
- **Regions** — dashed outline, barely-there fill; a scope/phase/system edge.
- **Annotations** — always mono, uppercase, quiet: IDs, dimensions, corner marks.

Three canonical layouts: **A · Flow** (L→R pipeline), **B · Decision tree**
(branch point), **C · System map** (interconnected field). Density: hero 6–10
nodes, case inline 4–7, never > ~12. Exactly one orange focus per diagram.

## §12 / §17 — Hero + motion

Hero = editorial statement left, live system map right, both on the blueprint
grid. The map tells one three-beat story: **inputs → decide → resolve**, and
assembles on load in reading order:

1. **Structure** — nodes fade + scale in from 55%, staggered ~60ms.
2. **Path draws** — green line strokes L→R, ~0.9s.
3. **Decision lights** — the orange diamond pulses once to ~114% and settles.
4. **Outcome resolves** — target node fills; mono labels fade up last.

Timing: micro 150–250ms · reveal 400–650ms · assembly 1.2–1.6s total. Easing:
enter `(.2,.7,.3,1)`, draw `(.3,.6,.2,1)`, no overshoot > 1.15×. **Once on load,
never on loop. No particles, no bounce, no cursor-chasing.** Reduced motion → final
state shown instantly. Mobile → simplified 4-node flow, assembles once, then holds.

## §11 — Background

Dot lattice (global, ~5%), blueprint grid (hero/feature, ~5% lines, 34px), column
rules (editorial). Keep at 4–7% opacity, fixed to viewport, fade out under dense
text. Never stack two, never tint, never animate/parallax.
