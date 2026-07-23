# How I Work — illustrations (asset integration)

Concise reference for the six approved illustrations integrated from
`estavillo-how-i-work-staging.zip`. For the approved content/copy see
`docs/HOW-I-WORK-CONTENT-SPEC.md`; for the process/strategy behind the page
see `docs/HOW-I-WORK-STRATEGY.md`.

## The six files

`estavillo-child/assets/images/how-i-work/`

| Step | File | Move |
|---|---|---|
| 1 | `01-understand-system.svg` | Understand the system |
| 2 | `02-find-real-problem.svg` | Find the real problem |
| 3 | `03-gather-evidence.svg` | Gather evidence |
| 4 | `04-explore-challenge.svg` | Explore and challenge |
| 5 | `05-design-solutions.svg` | Design practical solutions |
| 6 | `06-test-learn-iterate.svg` | Test, learn and iterate |

Final art — never redrawn or re-optimized on integration. Each file: vector
only, transparent background, `viewBox="0 0 440 300"`, no scripts/event
handlers/external references, inline `<style>` mapping its own BEM classes
(`.how-work-svg__line`, `__accent-path`, `__accent-node`, `__accent-fill`,
`__accent-soft`) to CSS custom properties.

## Shared renderer

`estavillo-child/inc/how-i-work-illustrations.php` — the single place that
reads these files. `es_how_work_illustration_svg( $step, $args )` is the
public entry point, used by both:

- the Home teaser (`template-parts/how-i-work.php`, steps 1/4/6 only —
  Understand/Explore/Improve), and
- `estavillo/how-i-work-illustration` (server-rendered Gutenberg block,
  `estavillo-portfolio-core/blocks/how-i-work-illustration/render.php`).

`$args`: `context` (`page`/`home`, presentation only), `show_accents`
(bool), `decorative` (bool), `class` (extra wrapper class). Reads are
cached per request (`es_how_work_illustration_library()`), validated once
(`es_how_work_illustration_is_safe()` — rejects `<script>`, event-handler
attributes, `<image>`, any non-same-document href, on load), and namespaced
against duplicate-id collisions if a file is rendered more than once on a
page. A missing/invalid file returns `''` — callers render nothing, the
layout never breaks (see the `:empty` fallback rule in `pages.css`).

## The block

`estavillo/how-i-work-illustration` — leaf, server-rendered, `save()`
returns `null`. Attributes: `step` (1–6), `context` (`page`/`home`),
`showAccents` (bool), `decorative` (bool). Editor preview uses
`ServerSideRender` (core Gutenberg package) instead of duplicating the six
SVGs into the editor JS bundle — the preview is the real render.php output,
so the accent toggle looks exactly like the frontend.

## Accent on/off

`showAccents: false` hides only the four accent classes
(`.how-work-illustration--no-accents .how-work-svg__accent-{path,node,fill,soft}
{ display: none; }`, in `pages-home.css`) — the base line drawing
(`.how-work-svg__line`) is never touched. Same six files serve both states;
no monochrome duplicates.

## CSS variables

Defined once, scoped to `.how-work-illustration` (`pages-home.css`, loads
on Home + all 4 static pages):

```css
--how-work-line: var(--es-ink);
--how-work-accent: var(--es-signal);      /* diagram-semantic green, fixed
                                              regardless of the orange/green
                                              brand-accent toggle — same
                                              family the hero system map
                                              already uses */
--how-work-accent-soft-opacity: 0.12;     /* tune this alone to fade the
                                              soft circles, no SVG edits */
--how-work-accent-soft: rgba(88, 177, 131, var(--how-work-accent-soft-opacity));
```

`[data-theme='light']` overrides only the soft-accent RGB triple (line and
accent already flip via `--es-ink`/`--es-signal`).

## Replacing an illustration later

Drop a new file into `assets/images/how-i-work/` under the same name (or
update the filename in `es_how_work_illustration_steps()`,
`inc/how-i-work-illustrations.php`) — same `viewBox`, same five BEM
classes, same three CSS variables. No PHP, block, or content change needed
elsewhere; every call site re-resolves by step number.

## Motion (this pass)

MVP only: existing disclosure +/− transition (unchanged), a ≤2px
`translateY` hover on the illustration itself (pointer-fine + hover-capable
+ `prefers-reduced-motion: no-preference` only), and existing site-level
transitions. No IntersectionObserver, scroll reveal, path-drawing,
active-step tracking, sticky counter, staggered animation, loop, or
typewriter effect.

## Next motion phase (backlog)

Already tracked in `docs/BACKLOG.md` (P3): "Future motion exploration
beyond How I Work's MVP list" — illustration video/Lottie-style sequences,
scroll-scrubbed/pinned animation, cursor-follow or parallax effects,
explicitly deferred past this MVP pass. No new backlog item needed for
this ticket.
