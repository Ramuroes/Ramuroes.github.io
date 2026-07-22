# How I Work — strategy document (Phase 2 planning)

Status: **planning only — nothing in this document has been implemented.**
No Home, How I Work, plugin, theme, Gutenberg content, or ZIP files were
touched while writing it. This is the reference to work from once the open
decisions in §13 are resolved and the ticket is picked up as its own
implementation pass (matching the phased approach already used for About).

---

## 1. Audit of the current Home teaser and How I Work page

**Data model** (`functions.php` → `es_home_process_steps_defaults()`,
filtered through `es_home_process_steps()`, shared by both surfaces) is in
better shape than the visual treatment suggests. Each of the 6 steps
already carries `title`, `text` (short description), `icon_key`, `why`,
`example`, `tools` — three of the final schema's seven fields (§6) already
exist under different names (`why` → Why it matters, `tools` → What I may
do, `example` → Example from my work); the four genuinely new fields are
**How AI can help**, **What requires human judgment**, **What success
might look like**, and **Related case**.

**Home teaser** (`template-parts/how-i-work.php` → `.es-process__grid`,
`pages-home.css`): a 3-column, 6-card grid — number + small icon slot +
title + text, no lead-in copy, no grouping. This is precisely the "full
six-card page" the ticket says Home must not reproduce; today it already
does, just with 3 of the 6 fields hidden. Section position is good — it's
`02` in `es_home_sections()`, right after Hero and before Featured
Case/Selected Work/About/Connect, so it already lands early, while
attention is highest.

**How I Work page** (`template-parts/how-i-work-detail.php` →
`.es-process-detail__*`, `pages.css`): a vertical numbered sequence with a
fixed marker column (number + icon + connecting rail line) and body at
right. This structure is already the right shape — an "editorial index,"
not the icons-in-circles-and-arrows SaaS timeline cliché the ticket warns
against — and needs enhancement, not replacement. Two real gaps: (1)
`why`/`example`/`tools` are **always visible**, not expandable — there is
no `<details>` anywhere on this page, unlike About's disclosures; (2) the
icons are the same small abstract set as the teaser (`compass`, `target`,
`map`, `document`, `check`, `layers`, `tool`, `bulb`, `rocket`) — already
flagged in `docs/BACKLOG.md` under Design polish as needing "a future
icon/motion treatment," which is exactly this ticket.

**Motion**: both surfaces already use the site's one existing scroll
mechanism, `[data-es-reveal]` / `.es-reveal` (`motion.js` +
`components.css`), staggered per item via `--es-reveal-delay` — a real,
reusable MVP baseline, not something to invent from scratch.

**Disclosure precedent**: `estavillo/case-details` (dynamic Gutenberg
block) with the `is-style-light` style — hairline summary, native
`<details>/<summary>`, and (as of the most recent About fix) a smooth
transform-based +/− micro-animation, keyboard-accessible,
`prefers-reduced-motion`-aware. This is a proven, already-fixed component
and the natural mechanism for How I Work's "expandable detail."

**Step naming**: closer to the proposed six moves than expected — see §3.

**Grouping into 3 ideas** (Understand / Explore / Improve): does not exist
anywhere today. New work, not a modification of something existing.

---

## 2. Proposed final narrative (revised)

Anchor line, used verbatim (not paraphrased) in at least two places:

> "I don't start with interfaces. I start by understanding the system."

- **Home teaser**: this line (or a slightly shorter version) becomes the
  section's lead-in copy, sitting between the eyebrow/heading and the
  3-group summary. Today's teaser has *no* narrative copy at all — it
  jumps straight to the grid. This is the single biggest missing piece on
  Home, more than the icons.
- **How I Work page**: the same line opens the page (as the page-head
  `lead`, replacing the current more generic "The same process, every
  time — understand the system before proposing how it could work
  better."), then a short supporting paragraph before the 6-move sequence
  states the Core Positioning bullets in prose — real system, real
  constraints, tacit knowledge made legible, alternatives explored before
  polish, AI accelerating without replacing judgment. Design Thinking/Lean
  UX/Service Design/Agile/Sprints are named at most once, in passing, as
  informing influences — never as a labeled methodology grid.

**The missing dimension, now integrated**: the page must communicate that
this process connects three things, not one — user needs, operational
reality, and business goals. A short explicit statement makes this
concrete instead of implicit:

> "This isn't only about how something looks or feels — it's about
> whether it works for the person using it, the system carrying it, and
> the goals it's meant to support."

This line (or an edited version of it) sits directly under the anchor
line, before the 6 moves begin. It sets up the **balanced model** that
should be legible throughout the page, not stated once and forgotten:

- **user value** — does it work for the person actually using it;
- **business viability** — does it serve a real product or business
  objective;
- **operational reality** — does it hold up under how the organization
  actually runs, not an idealized version of it;
- **technical feasibility** — can it actually be built and maintained
  with the constraints that exist;
- **responsible and accessible design** — is it usable, honest, and
  accountable for who it affects.

**Language discipline** (applies everywhere business/product language
appears — the 6 moves, the expandable schema, the AI band): sober,
specific, falsifiable. Preferred vocabulary: *business goals, product
objectives, measurable outcomes, operational impact, reduce friction,
reduce errors, improve adoption, reduce unnecessary dependency,
accelerate decision-making, avoid unnecessary complexity, improve
efficiency, sustainable improvement.* Never: *massive growth, guaranteed
ROI, exponential results, game-changing, 10x, world-class* — or any
unfalsifiable claim of revenue growth or guaranteed return. The
difference in practice: "this can reduce how often a quote gets
re-checked by hand" is allowed; "this will boost revenue" is not — the
first is a plausible, observable operational effect; the second is a
promise no design process can honestly make on its own.

---

## 3. Recommended number and names of process moves

**Keep six.** The current defaults already carry almost the exact meaning
requested — this is a renaming pass, not a rewrite:

| # | Current | Recommended | Change |
|---|---|---|---|
| 01 | Understand the system | **Understand the system** | none |
| 02 | Find the real bottleneck | **Find the real problem** | rename — "problem" is broader than "bottleneck" (a real issue is sometimes a misalignment or risk, not a literal bottleneck) |
| 03 | Gather evidence | **Gather evidence** | none |
| 04 | Challenge assumptions | **Explore and challenge** | rename + scope widened — closes a real content gap: Core Positioning explicitly lists "exploring alternatives before committing to polished solutions," which the current step 4 copy doesn't mention at all, only assumption-testing |
| 05 | Design practical solutions | **Design practical solutions** | none |
| 06 | Iterate with purpose | **Test, learn and iterate** | rename — closes another real gap: Core Positioning lists "testing with users and stakeholders," which current step 6 copy never mentions, only iteration |

Three renames, all preserving underlying meaning, and two of the three
directly close gaps between the current copy and the Core Positioning
brief (explicitly naming exploration-of-alternatives and
testing-with-users). This is the recommendation; final copy sign-off is
listed in §13.

**Final six moves — draft short descriptions**, each now carrying the
business/product dimension throughout (not as a bolted-on sentence, and
never as a 7th step). These are drafts for the always-visible `text`
field (§6), written to the same sober-language rules as §2:

**01 — Understand the system.** I start by mapping how the system
actually works — the people, information, constraints, and product or
business goals involved — not just the interface. Understanding
operational reality and business context early reduces the risk of
solving the wrong problem later.

**02 — Find the real problem.** From that map, I look for the specific
point where user pain, operational friction, and barriers to product or
business objectives actually meet — not a general list of issues. Naming
the real problem precisely is what keeps the rest of the work from
becoming decoration.

**03 — Gather evidence.** I build a case with whatever evidence is
available and relevant — interviews, observation, workflows, documents,
and product data such as errors, time, or adoption. Evidence turns a
debate about opinions into a decision grounded in what's actually
happening.

**04 — Explore and challenge.** Before committing to one direction, I
generate alternatives and challenge assumptions — comparing potential
value, effort, risk, and feasibility for each. This keeps a plan honest
about what it depends on and what it costs.

**05 — Design practical solutions.** Not every problem needs a new
interface — sometimes the strongest move is fixing what's underneath it.
When a solution is the right call, I design it to hold up under real
conditions: usability and accessibility alongside business viability,
technical feasibility, maintainability, and the organization's actual
constraints.

**06 — Test, learn and iterate.** I treat a first version as a
hypothesis, evaluated against both user experience and relevant
outcomes — task success, errors, time, adoption, support load, or
progress toward a product goal. What "wrong" would look like is defined
before shipping, so the next version is actually better, not just
different.

Each draft stays within the 1-2 sentence budget already set for the
always-visible surface (§6) — the fuller KPI/methods/risk detail belongs
inside the expandable panel, not here. The process is still described as
adaptable and non-linear: no language above implies a fixed linear
handoff between moves, and the page's supporting paragraph (§5, item 2)
states this explicitly before the sequence begins.

---

## 4. Home content structure

Replace `.es-process__grid` (6 cards) with a **shorter, three-part**
component:

1. Eyebrow + heading (existing `process_label` string, unchanged).
2. One-sentence lead statement — the anchor line from §2, editorial serif
   treatment (reuse the `.es-about-page__text`-style large-intro
   treatment already established, not a new type scale).
3. **One bridging line**, immediately under the anchor line, connecting
   people/systems/outcomes without naming business or KPIs — draft:
   *"The result should make sense for the people using it, and for the
   system that has to carry it."* This is the one place Home gestures at
   "outcomes" at all — in plain language, not the balanced-model
   vocabulary from §2 (which stays on the full page).
4. **Three grouped ideas** (not six cards): Understand / Explore /
   Improve, each rendered as a compact block — short label + one
   supporting line, **no illustration, no icon, no expandable detail, no
   number-per-move, no business language, no KPI mentions**. Deliberately
   lighter than anything else on the page. Draft supporting lines (revised
   to reflect people+systems, not just UX activity):
   - **Understand** — "See how people, information and goals actually
     connect."
   - **Explore** — "Test ideas and challenge assumptions before
     committing to one."
   - **Improve** — "Build something that works — and keeps working."
5. One CTA, reusing the existing `.es-link-arrow` component: "See the
   full process →", linking to the How I Work page.

**Recommended grouping** (2-2-2, each pair sharing one real relationship):

- **Understand** → 01 Understand the system + 02 Find the real problem
  ("see clearly before acting")
- **Explore** → 03 Gather evidence + 04 Explore and challenge
  ("test assumptions, don't guess")
- **Improve** → 05 Design practical solutions + 06 Test, learn and
  iterate ("ship something that works, then make it better")

An alternative 3-1-2 split (Understand = 01+02+03, Explore = 04 alone,
Improve = 05+06) was considered — it maps more literally onto "research
phase / divergent phase / build phase" but leaves "Explore" as a single
move standing in for a whole group, which reads thinner on Home. 2-2-2 is
the recommendation; both are listed as an open decision in §13.

**Explicitly not on Home**: six cards, per-move illustrations, per-move
numbers, why/methods/AI/judgment/example content, expandable panels. If it
needs a `<details>`, it doesn't belong on Home.

---

## 5. Complete How I Work page content structure

1. Page head (existing `page-head` template-part) — eyebrow, H1 "How I
   work.", lead = the anchor line from §2.
2. Short supporting paragraph(s) stating the Core Positioning in prose
   (new content, 2-4 sentences, sets up the 6 moves without listing them
   as a checklist).
3. The 6-move vertical editorial sequence (existing
   `.es-process-detail__*` layout, enhanced per §6-8) — number, large
   illustration, title, short description (always visible), expandable
   detail (collapsed by default).
4. **AI band** — a distinct section after the 6 moves, not a 7th step.
   See §9.
5. Optional closing CTA — "See how this plays out on a real project" →
   Work/Selected Work or a specific Case Study, reusing the existing
   `.es-link-arrow`/footer-CTA components. Not required for MVP; flagged
   as an easy add once a case is confirmed as the reference (ties into the
   backlog item "relationships between process steps and related cases,"
   §12).
6. Standard footer.

---

## 6. Expandable content schema (final)

Per move, always-visible surface stays minimal (number, illustration,
title, short description) — everything else moves inside one collapsed
`<details>` panel, reusing `estavillo/case-details` (`is-style-light`).
Final field set and order, replacing the previous draft:

| Field | Status | Notes |
|---|---|---|
| `title` | exists | unchanged |
| `text` (short description) | exists | stays 1-2 sentences, always visible — draft copy per move is in §3 |
| `illustration` | **replaces** `icon_key` | see §7 — no longer a small abstract glyph |
| **Why it matters** | exists (`why`) | **moves inside** the expandable panel (currently always-visible; see §13, decision 4) |
| **What I may do** | **renames** `tools`/`methods` | broader than a tool list — the deliberate/methods/activities that may apply, phrased as *may*, not a mandatory checklist, matching "adaptable, non-linear" (§3); stays a short list (comma list or real `core/list`, see §10) |
| **How AI can help** | **new** (`ai_help`) | 1-2 sentences, specific to this move — must also name a **limit or how the output gets validated** (e.g. "…every AI-synthesized finding is still reviewed by a person before it counts as evidence"), not just what AI does. Ties directly into §9. |
| **What requires human judgment** | **new** (`human_judgment`) | 1-2 sentences: what stays human-only at this specific move |
| **Example from my work** | **renames** `example` | same data, same free-text field, renamed for tone |
| **What success might look like** | **new** (`success_signals`) | describes **possible observable improvements, not guarantees** — see the sober-language rule in §2. Example vocabulary to draw from: clearer decision-making, fewer errors, reduced operational friction, improved accessibility, faster task completion, better adoption, less dependency on one person, progress toward a relevant product goal. Never a number/percentage unless it comes from a real, evidenced case (§13, decision 11). |
| **Related case, when available** | **new, optional** (`related_case`) | a link to a real Case Study post; text-only for now (no case exists yet that's tagged per-move) — see backlog item "relationships between process steps and related cases" |

Every field below `text` is optional per move (an empty field simply
doesn't render, same convention used everywhere else in this codebase) —
no move is required to fill all seven.

---

## 7. Visual illustration direction

Six large custom illustrations, one per move, one consistent family:

- **Thin linework** (1.5-2px stroke), matching the hairline aesthetic
  already established via `--es-line`/`--es-line-strong`.
- **One recurring human figure** interacting with a system/network motif
  — the *same* simplified figure across all six, so the set reads as one
  family and not six different styles. Concrete per-move briefs (starting
  point for whoever draws them):
  1. *Understand the system* — figure studying a tangled network of nodes.
  2. *Find the real problem* — figure pointing at one highlighted node
     among many unhighlighted ones.
  3. *Gather evidence* — figure collecting/stacking documents or data
     fragments.
  4. *Explore and challenge* — figure at a fork, facing branching paths.
  5. *Design practical solutions* — figure assembling a clear structure
     out of loose parts.
  6. *Test, learn and iterate* — figure with a loop/cycle arrow, reviewing
     a result.
- **One restrained green accent** (`var(--es-accent)`) per illustration —
  a single highlighted node, line, or figure detail, never a fill across
  the whole piece.
- **Transparent background** — SVG, sits directly on `--es-paper`/
  `--es-paper-2` in both themes.
- **Consistent canvas/viewBox proportions and perspective** across all
  six, so they read as a matched set at a glance.
- **Meaningful, not decorative** — each illustration should be
  identifiable from the brief alone, without needing the title next to
  it to explain what's happening.
- **Suitable for subtle animation** — drawn as clean, separable shapes
  (figure vs. accent vs. system motif as distinguishable groups/paths),
  so a future motion pass can animate one part without needing to
  redraw the artwork (see §8).

**Production approach**: the existing hobby-icon precedent (draw
candidates, get explicit approval, normalize into `assets/icons/` as
metadata-only SVGs) is the right process to reuse — AI-assisted drafting,
human review and refinement before anything ships. Flagged as an open
decision in §13 (who draws them and on what timeline is outside this
document's scope).

**Sizing**: large enough to carry real presence on the full page
(approx. 120-160px) — Home gets **no illustration at all** per §4, which
keeps the size/perspective/consistency requirements scoped to one surface
only.

---

## 8. Motion strategy — MVP vs. future

**MVP (this phase, once approved):**

- **Scroll-based progressive reveal** — reuse `[data-es-reveal]` as-is,
  staggered per move via `--es-reveal-delay` (same mechanism already
  proven across About/Home).
- **Active-step / connecting indicator** — extend the existing
  `.es-process-detail__marker::after` rail line with a small
  IntersectionObserver-driven "in view" state (vanilla JS, no framework,
  consistent with `motion.js`'s existing no-dependency approach):
  the move currently in view gets a highlighted rail segment/dot. Purely
  supplementary — see §11.
- **Subtle illustration motion** — small idle/hover shift only (2-4px
  drift, or the accent element shifting on `:hover`/`:focus-within`), not
  autoplay/loop — same "V1 deliberately contained" precedent already used
  for hobby icons (`translateY` + accent color, hover/focus only).
- **Smooth disclosure expansion + animated +/-** — direct reuse of the
  `.es-case-details.is-style-light` component and the transform-based
  rotating-bar indicator just fixed on About, applied unchanged (or with
  a minor size/spacing variant if the larger illustration context needs
  it).
- **Accessible keyboard interaction** — native `<details>/<summary>`,
  no custom ARIA required, visible `:focus-visible` outline (same proven
  pattern).
- **`prefers-reduced-motion: reduce`** — disables the scroll-spy
  transition, illustration micro-motion, and disclosure transition
  (state still changes, just without animating), matching the guard just
  added to About's disclosure and the site's existing global rule in
  `base.css`.

**Future (explicitly out of scope for this phase):**

- Illustration video / Lottie-style animated sequences per move.
- Scroll-scrubbed or pinned illustration animation (illustration "draws
  itself" as you scroll).
- Cursor-follow or parallax effects.
- Audio/voiceover walkthrough.
- Alternative/horizontal page-layout experiments.

---

## 9. AI integration model (revised placement + per-move reinforcement)

A **dedicated short band** after the 6 moves, not a 7th step and not a
logo list. Rationale: scattering AI mentions silently through the 6 moves
would under-communicate it (the ticket asks for a clear, quotable
statement), while adding it as step 7 would misrepresent it as one more
sequential phase rather than something that runs through all of them.
**Placement detail, to keep it from reading as a 7th step visually**:
the band should NOT reuse the numbered-move layout (no `07`, no
marker-column, no illustration in the same slot/size as the six
illustrations) — it gets its own distinct treatment (e.g. a full-width
statement band with a different background tint, or a pull-quote
treatment already used elsewhere on the site), so it's visually legible
as a *layer underneath* the six moves, not a seventh item in the same
list. This is a layout instruction for whoever builds it, not yet a CSS
decision.

Content:

1. The line itself, as a headline/pull-quote: *"AI accelerates the work.
   Human judgment defines the direction."*
2. 2-3 sentences naming where AI concretely shows up, matching Core
   Positioning: research synthesis, exploration, prototyping,
   documentation.
3. One explicit counter-statement on what stays human: judgment,
   validation, ethics, accessibility.

No logos, no tool grid here — that's a separate, already-flagged future
item ("future Tools placement," §12). This band is about the *role* AI
plays, not which products are used.

**Per-move reinforcement (this is where "referenced inside individual
moves… with clear limits and validation" actually happens)**: the band
states the general principle once; the per-move **How AI can help**
field (§6) is where it becomes concrete and falsifiable — each instance
should name where AI helped *and* how the result was checked (a limit,
a review step, a human sign-off), not just a capability claim. A field
that only says what AI can do, with no limit or validation named, doesn't
meet this bar and should be rewritten before publishing. This keeps the
band's promise ("human judgment defines the direction") demonstrated at
the move level, not just asserted once at the top.

---

## 10. Recommended Gutenberg architecture

Applying the same block-vs-pattern-vs-plain-blocks reasoning already used
for About's Hobbies decision: **a dedicated block is justified only when
content is genuinely data-shaped and depends on a registry a block can
centralize (icon/illustration resolution, reorderable items). Rich,
varied prose content is better served by real, individually composed
Gutenberg blocks or a Pattern, the same way About's Experience entries
were built** (not crammed into one block's array attribute).

How I Work's 6 moves are prose-heavy across 7 optional fields (§6) —
closer to About's Experience entries than to Hobbies' short label+icon
pairs. Recommended architecture, **requiring no new custom block**:

- **Illustration slot**: real `core/image`, empty by default, replaced via
  Media Library — same precedent as the About portrait fix (no invented
  placeholder, no server-side icon-registry needed, since these are large
  illustrative assets, not small `currentColor`-tinted UI glyphs the way
  hobby icons are).
- **Title / short description**: `core/heading` + `core/paragraph`,
  same convention as every other section on this site.
- **Expandable detail**: `estavillo/case-details` with `is-style-light`
  — the same already-built, already-fixed block, reused as-is (or with a
  small opt-in style variant if the larger illustration context calls for
  different spacing — a CSS-only addition, not a new block).
- **Numbering** (`01`-`06`): plain `core/paragraph` with a className,
  matching `.es-section-head__num` used everywhere else.
- **A new Pattern**, `"How I Work — Step"` (added to the plugin's
  pattern-registration file, `postTypes:['page']` scoped, same convention
  as `case-persona.php`), pre-composing one move's full structure so
  inserting it 6 times is copy-a-pattern-and-fill-in-text, not manual
  block-by-block assembly.
- **AI band** (§9): either plain composed blocks (it's short — one
  heading, two paragraphs) or a second small Pattern,
  `"How I Work — AI Band"`, for convenience. Low stakes either way.
- **Home's 3 grouped ideas** (§4): real `core/columns` (3 columns), same
  proven native-flex-basis approach used for About's Portrait/Education
  fix — no className+width combination on the `core/column`s themselves,
  styled structurally. No new block or pattern strictly required, though
  a small convenience Pattern wouldn't hurt.

### Exact nested block structure for one process move

The gap in the previous draft: leaving illustration/title/description/
disclosure as loose sibling blocks would list every one of them as a
separate top-level row in List View — six moves × four-plus rows each,
with nothing showing they belong together. Fixed by wrapping each move in
**one outer `core/group`**, so List View shows one collapsible node per
move (`Group (Move 01)`) containing everything else as its children —
this is the actual requirement ("clearly grouped Gutenberg unit… not an
unstructured collection of loose blocks"), not just a visual nicety.

Proposed structure (shown for move 01; identical shape for 02-06, this is
exactly what the Pattern above pre-composes):

```html
<!-- wp:group {"tagName":"article","className":"es-process-move","anchor":"understand-the-system"} -->
<article class="wp-block-group es-process-move" id="understand-the-system">

  <!-- wp:group {"className":"es-process-move__marker"} -->
  <div class="wp-block-group es-process-move__marker">

    <!-- wp:paragraph {"className":"es-process-move__num"} -->
    <p class="es-process-move__num">01</p>
    <!-- /wp:paragraph -->

    <!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"es-process-move__illustration"} /-->

  <!-- /wp:group -->

  <!-- wp:group {"className":"es-process-move__body"} -->
  <div class="wp-block-group es-process-move__body">

    <!-- wp:heading {"level":3,"className":"es-process-move__title"} -->
    <h3>Understand the system</h3>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"className":"es-process-move__text"} -->
    <p>I start by mapping how the system actually works…</p>
    <!-- /wp:paragraph -->

    <!-- wp:estavillo/case-details {"summary":"More on this move","className":"is-style-light"} -->

      <!-- wp:heading {"level":4} --><h4>Why it matters</h4><!-- /wp:heading -->
      <!-- wp:paragraph --><p>…</p><!-- /wp:paragraph -->

      <!-- wp:heading {"level":4} --><h4>What I may do</h4><!-- /wp:heading -->
      <!-- wp:list --><ul><li>…</li></ul><!-- /wp:list -->

      <!-- wp:heading {"level":4} --><h4>How AI can help</h4><!-- /wp:heading -->
      <!-- wp:paragraph --><p>…(including the limit/validation, §9)</p><!-- /wp:paragraph -->

      <!-- wp:heading {"level":4} --><h4>What requires human judgment</h4><!-- /wp:heading -->
      <!-- wp:paragraph --><p>…</p><!-- /wp:paragraph -->

      <!-- wp:heading {"level":4} --><h4>Example from my work</h4><!-- /wp:heading -->
      <!-- wp:paragraph --><p>…</p><!-- /wp:paragraph -->

      <!-- wp:heading {"level":4} --><h4>What success might look like</h4><!-- /wp:heading -->
      <!-- wp:paragraph --><p>…(observable, not a guarantee — §6)</p><!-- /wp:paragraph -->

      <!-- wp:heading {"level":4} --><h4>Related case</h4><!-- /wp:heading -->
      <!-- wp:paragraph --><p>…(when available)</p><!-- /wp:paragraph -->

    <!-- /wp:estavillo/case-details -->

  <!-- /wp:group -->

<!-- /wp:group -->
```

Why `core/heading` (h4) per field rather than a bold inline label (the
`.es-process-detail__note-label` span the *current* PHP page uses):
each field becomes its own visible, semantic row in List View — both the
editor benefit this ticket asks for, and a real accessibility win
(screen-reader users get actual heading navigation inside the panel, not
a bolded run of text). `summary="More on this move"` is a placeholder
string — low-stakes wording, not listed as a blocking open decision.

Every element inside the `core/group`/`core/columns` family here follows
the already-proven rule from the About round-3 fix: **no className
combined with a width/other risky attribute on the same block** — widths
(if the marker/body ever need explicit ones instead of flex-basis
defaults) go on a `core/columns`+`core/column` pair with no className on
the column itself, exactly as done for About's Portrait/Intro and
Education sections. This structure has not been tested against the real
parser yet (no code was touched for this ticket) — that validation is
part of the eventual implementation pass, not this document.

**Migration path**: How I Work's data currently lives in PHP
(`es_home_process_steps_defaults()` + the Portfolio Content admin
fields). Migrating to Gutenberg should follow the exact explicit-fallback
model already proven on About: `templates/page-how-i-work.php` gains the
same `if ( real content ) the_content(); else legacy template-part;`
branch, and the legacy PHP path stays untouched until both languages are
live and approved — matching the already-agreed phase order (About →
How I Work → Connect → Home → legacy cleanup). Not started; this is
architecture only.

---

## 11. Accessibility considerations

**Stated principle** (appears once on the page — as a line inside the
Design practical solutions move, §3/§6, not as a separate badge or
section, and not repeated elsewhere):

> "Accessibility, clarity, and maintainability are considered from the
> beginning — not added as a final compliance layer."

This is a description of *when in the process* accessibility is
considered, not a credential. **Do not** describe Ramiro as an
accessibility specialist, and do not claim formal accessibility expertise,
certification, or audit experience anywhere on the page — none has been
established in this repository, and the difference between "I consider
accessibility from the start" (true, honest, supportable) and "I am an
accessibility expert" (an unestablished claim) matters and must not blur.

- Native `<details>/<summary>` for every expandable panel — keyboard
  operable with zero custom ARIA, exactly as already proven on About.
- `:focus-visible` outlines preserved on every interactive element
  (disclosure summaries, the Home CTA link).
- `prefers-reduced-motion: reduce` guards on: scroll-spy transitions,
  illustration micro-motion, disclosure open/close transition (state
  still changes, just without animating).
- Illustrations treated as **decorative** (`alt=""`, `aria-hidden="true"`)
  — title and short description are written to be self-sufficient, so a
  redundant SVG description would add screen-reader noise rather than
  information. Revisit only if a specific illustration ever needs to
  convey something the text doesn't.
- The active-step scroll-spy indicator is **purely supplementary** — every
  move's number/title/description is static, always-readable content
  regardless of scroll position or JS execution; nothing depends on the
  enhancement to be understood.
- State changes are never motion-only: open/closed already differs
  structurally (`[open]` attribute, not just a CSS transition) and
  visually (bar shape, not just color) — same pattern already validated
  on About.
- Focus order must match visual/reading order (moves 1→6, top to bottom)
  — no CSS `order` tricks that would desync keyboard tab order from the
  visual sequence, on either Home's 3-group teaser or the full page's
  6-move list.
- Color: reuse `var(--es-accent)` only — already contrast-tested across
  dark/light on this site; no new colors to re-validate.

---

## 12. Backlog created or updated

Added to `docs/BACKLOG.md` under a new "How I Work — Phase 2 (planned,
not started)" group, referencing this document. Also includes every item
explicitly requested for the backlog in this ticket:

- Case-study architecture inspired by editorial senior portfolios
- Narrative project titles
- Annotated product screens
- Case-specific structures rather than one rigid template
- AI decisions, limitations and validation documented within cases
- Metrics and evidence
- Testimonials and proof of collaboration
- Future Capabilities / What I Do section
- Future Tools placement
- Client/company logo treatment
- Typographic motion exploration
- How I Work video script and storyboard
- Spanish How I Work version
- Relationships between process steps and related cases
- Future Home refinement after How I Work is approved

This round adds four more, requested explicitly for the business/product
dimension:

- Explicit treatment of business outcomes in case studies
- Operational metrics and KPIs (folded into the existing "Metrics and
  evidence" item, reworded to name this explicitly)
- Accessibility review across the portfolio (broader than this one page)
- Future motion exploration (broader than the "Typographic motion
  exploration" item already present)

(Full entries with context are in `docs/BACKLOG.md`; not duplicated here
to avoid the two files drifting out of sync.)

---

## 13. Open decisions requiring approval (revised format)

Each decision below states the question, the recommendation, the
alternative actually considered, the real trade-off, and whether it
blocks starting implementation.

1. **Home grouping split.**
   - *Question*: group the six moves 2-2-2 or 3-1-2 under
     Understand/Explore/Improve?
   - *Recommendation*: 2-2-2 (§4) — every group carries two real moves,
     none reads as an afterthought.
   - *Alternative*: 3-1-2 (Understand=01-03, Explore=04 alone,
     Improve=05-06) — maps more literally onto research/diverge/build.
   - *Trade-off*: 3-1-2 is a cleaner phase-mapping; 2-2-2 is more visually
     balanced and gives "Explore" real substance on Home instead of
     standing in for one move.
   - *Blocks implementation*: **yes** — determines the Home teaser's
     actual content grouping.

2. **Grouping labels.**
   - *Question*: keep "Understand / Explore / Improve," or use different
     words?
   - *Recommendation*: keep as given — short, plain, no jargon, already
     matches the ticket's own wording.
   - *Alternative*: none proposed; ticket explicitly invited review but
     no stronger alternative surfaced during this pass.
   - *Trade-off*: none identified.
   - *Blocks implementation*: **no** — low-risk default, can ship as-is.

3. **Three move-name renames** ("Find the real problem," "Explore and
   challenge," "Test, learn and iterate").
   - *Question*: adopt these over the current defaults ("Find the real
     bottleneck," "Challenge assumptions," "Iterate with purpose")?
   - *Status*: **resolved this round** — this ticket's own §2 restated
     these exact three names as the working set, which is taken as
     confirmation. No longer open.
   - *Blocks implementation*: **no** — settled.

4. **`why` (Why it matters) field placement.**
   - *Question*: move fully inside the expandable panel (this document's
     recommendation, §6), or keep it as a separate always-visible line
     above the expandable detail (its current behavior on the live PHP
     page)?
   - *Recommendation*: move inside — keeps the always-visible surface to
     number/illustration/title/description only, consistent with "not a
     services grid."
   - *Alternative*: keep visible — "why it matters" is arguably the most
     persuasive single line per move and some visitors won't expand
     anything.
   - *Trade-off*: visible-by-default is more persuasive but busier;
     inside-the-panel is cleaner but requires a click to reach the
     strongest line.
   - *Blocks implementation*: **yes** — changes the always-visible layout.

5. **Illustration production.**
   - *Question*: who draws the six illustrations, and on what timeline?
   - *Recommendation*: AI-assisted drafting + human review/refinement,
     reusing the exact hobby-icon precedent (draft, approve, normalize
     into `assets/`).
   - *Alternative*: commission hand-drawn illustration from scratch.
   - *Trade-off*: AI-assisted is faster and cheaper but needs careful
     human art-direction to hit "coherent perspective and proportions"
     across all six; commissioned work is slower/costlier but may need
     less iteration to reach visual consistency.
   - *Blocks implementation*: **yes** — the full page cannot ship with
     the current small icons per the ticket's own critique, and nothing
     else in this phase depends on resolving it first, so it blocks only
     the illustration work specifically, not the rest of the page.

6. **`related_case` field.**
   - *Question*: implement now as a plain optional text/URL field, or
     defer until real per-move case relationships exist?
   - *Recommendation*: defer — no case is meaningfully tagged per-move
     yet (see backlog: "relationships between process steps and related
     cases").
   - *Alternative*: ship the empty field now so it's ready whenever a
     case exists.
   - *Trade-off*: shipping empty costs nothing but adds an unused field;
     deferring keeps the schema exactly matched to real content.
   - *Blocks implementation*: **no** — either choice is compatible with
     shipping the rest of the page.

7. **AI band copy.**
   - *Question*: is the drafted structure (§9) and anchor line final?
   - *Recommendation*: structure and anchor line are solid; the 2-3
     supporting sentences are a starting draft, not final copy.
   - *Alternative*: n/a — this is a wording pass, not a structural fork.
   - *Trade-off*: n/a.
   - *Blocks implementation*: **yes** — final sentence-level wording
     needed before the band can be built.

8. **Active-step scroll-spy.**
   - *Question*: is the IntersectionObserver-driven rail highlight in
     scope for MVP?
   - *Recommendation*: include it — small, vanilla, no new dependency,
     purely supplementary (§11), consistent with the "restrained
     enhancements" framing.
   - *Alternative*: drop it from MVP, ship the static rail only, revisit
     later.
   - *Trade-off*: it's the one genuinely new interaction pattern in this
     phase (everything else in §8's MVP list reuses an existing
     mechanism) — small added build cost for a real "coherent sequence"
     signal while scrolling.
   - *Blocks implementation*: **no** — page functions correctly without
     it; can be added or deferred independently.

9. **Closing CTA to a Case Study.**
   - *Question*: wire the closing CTA now (generic link to Work/Selected
     Work) or hold until a specific case is the intended reference?
   - *Recommendation*: wire it generically now, repoint later — matches
     how other CTAs on this site already degrade gracefully.
   - *Alternative*: omit the CTA entirely until a specific case is ready.
   - *Trade-off*: a generic link risks feeling like a placeholder;
     omitting it entirely loses a natural exit point from the page.
   - *Blocks implementation*: **no** — page reads fine either way.

10. **Tone calibration for business/product language** (new this round).
    - *Question*: how much business/product framing is enough to satisfy
      "connects user needs, operational reality and business goals"
      without drifting toward "aggressive marketing copy" (explicitly
      ruled out)?
    - *Recommendation*: err conservative — prefer qualitative, falsifiable
      language (§2's vocabulary list) over any number/KPI mention unless
      grounded in a real, evidenced case; when in doubt, cut the business
      language rather than add more.
    - *Alternative*: lean more explicitly commercial (name specific KPI
      categories more assertively per move) to make the business
      relevance unmissable.
    - *Trade-off*: conservative framing risks under-communicating the
      business dimension to a hiring manager skimming; more assertive
      framing risks the exact "inflated claims" problem the ticket warns
      against.
    - *Blocks implementation*: **yes** — affects final copy tone across
      §3, §4, §6, and §9 simultaneously; should be resolved once, not
      re-litigated per section.

11. **"What success might look like" — generic or case-specific.**
    - *Question*: ship the generic example vocabulary drafted in §6 as
      final copy, or hold each move's version until it can be grounded
      in a real project outcome?
    - *Recommendation*: ship the generic drafts now (they're honest,
      observable-improvement language, not invented numbers); replace
      per-move with something case-specific only once a real, evidenced
      example exists — never invent a number or outcome to fill the
      field.
    - *Alternative*: leave the field blank per move until real outcomes
      exist for all six, shipping it later as a batch.
    - *Trade-off*: shipping generic language now is more complete at
      launch but slightly less concrete; leaving it blank is more
      conservative but leaves a visibly empty field in 6 places at once.
    - *Blocks implementation*: **no** — generic drafted language is
      ready to ship as-is if approved.

No further action until these are resolved. Stopping here per the
instruction — Home, How I Work, the plugin, the theme, Gutenberg content,
and all ZIPs remain untouched.
