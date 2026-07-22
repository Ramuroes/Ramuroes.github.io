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
`example`, `tools` — four of the schema fields this ticket asks for already
exist; only `ai_help`, `human_judgment`, and a structured `related_case`
link are genuinely missing (see §6).

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

## 2. Proposed final narrative

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

---

## 4. Home content structure

Replace `.es-process__grid` (6 cards) with a **shorter, three-part**
component:

1. Eyebrow + heading (existing `process_label` string, unchanged).
2. One-sentence lead statement — the anchor line from §2, editorial serif
   treatment (reuse the `.es-about-page__text`-style large-intro
   treatment already established, not a new type scale).
3. **Three grouped ideas** (not six cards): Understand / Explore /
   Improve, each rendered as a compact block — short label + one
   supporting line, **no illustration, no icon, no expandable detail, no
   number-per-move**. Deliberately lighter than anything else on the
   page.
4. One CTA, reusing the existing `.es-link-arrow` component: "See the
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

## 6. Expandable content schema

Per move, always-visible surface stays minimal (number, illustration,
title, short description) — everything else moves inside one collapsed
`<details>` panel, reusing `estavillo/case-details` (`is-style-light`):

| Field | Status | Notes |
|---|---|---|
| `title` | exists | unchanged |
| `text` (short description) | exists | stays 1-2 sentences, always visible |
| `illustration` | **replaces** `icon_key` | see §7 — no longer a small abstract glyph |
| `why` | exists | **moves inside** the expandable panel (currently always-visible; see §13 for the alternative of keeping it visible) |
| `methods` | **renames** `tools` | same data, broader framing — "methods or activities," not just tool names; stays a short comma list or becomes a real `core/list` if migrated to Gutenberg (see §10) |
| `ai_help` | **new** | 1-2 sentences: how AI concretely helps at this specific move (not generic) |
| `human_judgment` | **new** | 1-2 sentences: what stays human-only at this specific move |
| `example` | exists | stays free text for now |
| `related_case` | **new, optional** | a link to a real Case Study post; text-only for now (no case exists yet that's tagged per-move) — see backlog item "relationships between process steps and related cases" |

`why`/`methods`/`ai_help`/`human_judgment`/`example` are all optional per
move (empty fields simply don't render their block, same convention
already used everywhere else in this codebase) — no move is required to
fill all five.

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

## 9. AI integration model

A **dedicated short band** after the 6 moves, not a 7th step and not a
logo list. Rationale: scattering AI mentions silently through the 6 moves
would under-communicate it (the ticket asks for a clear, quotable
statement), while adding it as step 7 would misrepresent it as one more
sequential phase rather than something that runs through all of them.

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

The per-move `ai_help`/`human_judgment` fields (§6) reinforce this same
message at the granular level, inside each move's expandable detail —
consistent with "transversal," never contradicting the band's framing.

---

## 10. Recommended Gutenberg architecture

Applying the same block-vs-pattern-vs-plain-blocks reasoning already used
for About's Hobbies decision: **a dedicated block is justified only when
content is genuinely data-shaped and depends on a registry a block can
centralize (icon/illustration resolution, reorderable items). Rich,
varied prose content is better served by real, individually composed
Gutenberg blocks or a Pattern, the same way About's Experience entries
were built** (not crammed into one block's array attribute).

How I Work's 6 moves are prose-heavy across 5 optional fields
(why/methods/ai_help/human_judgment/example) — closer to About's
Experience entries than to Hobbies' short label+icon pairs. Recommended
architecture, **requiring no new custom block**:

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

(Full entries with context are in `docs/BACKLOG.md`; not duplicated here
to avoid the two files drifting out of sync.)

---

## 13. Open decisions requiring approval

1. **Home grouping split**: 2-2-2 (recommended, §4) vs. 3-1-2 — confirm
   before implementation.
2. **Grouping labels**: "Understand / Explore / Improve" as given, or a
   different set of three words — ticket says "explore," treating this
   document's choice as a proposal, not final.
3. **Three copy renames** (§3): "Find the real problem," "Explore and
   challenge," "Test, learn and iterate" — need explicit sign-off before
   the data model changes.
4. **`why` field placement**: moved fully inside the expandable panel
   (recommended, §6) vs. kept as a separate always-visible line above the
   expandable detail (its current behavior). Affects visual density on
   the always-visible surface.
5. **Illustration production**: who draws the six illustrations and on
   what timeline — AI-assisted draft + human refinement (matching the
   hobby-icon precedent) is recommended as the *process*, but scheduling
   is outside this document.
6. **`related_case` field**: implemented now as a plain optional text/URL
   field, or deferred entirely until the backlog item "relationships
   between process steps and related cases" is picked up. Recommendation:
   defer — no case exists yet that's meaningfully tagged per-move.
7. **AI band copy**: this document drafts the structure and the anchor
   line; final sentence-level wording is an editorial decision, not
   assumed.
8. **Active-step scroll-spy**: confirm it's in scope for the MVP pass —
   it is the one genuinely new interaction pattern (small vanilla JS,
   IntersectionObserver) rather than a reuse of something that already
   exists; everything else in §8's MVP list reuses proven mechanisms.
9. **Closing CTA to a Case Study** (§5, item 5): confirm whether to wire
   this now (linking generically to Work/Selected Work) or hold until a
   specific case is the intended reference.

No further action until these are resolved. Stopping here per the
instruction — Home, How I Work, the plugin, the theme, Gutenberg content,
and all ZIPs remain untouched.
