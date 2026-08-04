# How I Work — Content & Layout Specification (implementation-ready)

Status: **specification only — nothing in this document has been
implemented.** No Home, How I Work, plugin, theme, Gutenberg content, or
ZIP files were touched while writing it. This document converts the
approved `docs/HOW-I-WORK-STRATEGY.md` into final copy and a precise
build blueprint. Once the two remaining blocking decisions in §11 are
resolved, this is ready to hand to implementation directly — no further
strategy round should be needed first.

**Sourcing discipline**: every example, tool name, and fact below is
drawn from content already in this repository — the About page's
Experience section, `docs/content/workshop-quoting-system-fields-en.md`,
and `docs/content/trazur-fields-en.md`. Nothing is invented. Where a
real, sufficiently-supported example didn't exist for a specific slot,
that slot says so explicitly rather than filling the gap.

---

## 1. Final content

### A. Home — How I Work teaser

| Element | Copy |
|---|---|
| **Eyebrow** | How I work *(existing string, `process_label` — unchanged)* |
| **Headline** | I don't start with interfaces. I start by understanding the system. |
| **Supporting paragraph** | The result should make sense for the people using it, and for the system that has to carry it. |
| **Understand — title** | Understand |
| **Understand — description** | See how people, information and goals actually connect. |
| **Explore — title** | Explore |
| **Explore — description** | Test ideas and challenge assumptions before committing to one. |
| **Improve — title** | Improve |
| **Improve — description** | Build something that works — and keeps working. |
| **CTA** | See the full process → *(links to the How I Work page)* |

**Layout note**: desktop uses a horizontal row of the three ideas —
Understand → Explore → Improve, left to right, with a restrained visual
cue (a thin connecting line or a small chevron between each pair — not
numbered, not illustrated) reinforcing the left-to-right sequence without
turning it into a diagram. This is a **preview**, not a shrunk six-move
grid: no numbers, no icons, no expandable content, no per-move detail —
see §5 for full layout.

### B. Full How I Work page

| Element | Copy |
|---|---|
| **Eyebrow** | How I work *(same string as the teaser)* |
| **Main headline (H1)** | How I work. *(existing, unchanged — page identity)* |
| **Lead statement** | I don't start with interfaces. I start by understanding the system. |
| **Introductory paragraph** | Most of my work starts the same way: by learning how a system actually operates — not how it's supposed to, and not what the interface implies. That means talking to the people who use it, watching how the work actually gets done, and reading whatever documentation, spreadsheets or workflows already exist. Design Thinking, Lean UX, Service Design, Agile and Design Sprints all inform how I move through that — but none of them run the show. The project does. |
| **Balanced-model statement** | A solution only really works if it holds up on more than one axis at once: useful for the people who rely on it, realistic about how the organization actually runs, technically buildable, accessible, and worth the effort for the business behind it. |

The introductory paragraph is also where "Methods and references" (§3)
is satisfied — the five methodologies are named once, in passing, inside
real prose, never as a labeled grid or a section of their own.

---

### Final six moves

Each move ships **one short visible description** plus **four
consolidated disclosure sections** (not seven). The mapping from the
full strategy schema (`docs/HOW-I-WORK-STRATEGY.md` §6) to these four is:

| Visible section | Internal schema field(s) it carries |
|---|---|
| *(always visible, not a disclosure section)* Short description | `text` |
| **Why it matters** | `why` |
| **How I approach it** | `what_i_may_do` (methods/activities) |
| **AI and human judgment** | `ai_help` + `human_judgment`, merged |
| **In practice** | `example` + `success_signals` + `related_case`, combined flexibly — not every move needs all three |

---

#### 01 — Understand the system

**Short description**: I start by mapping how the system actually works
— the people, information, constraints, and product or business goals
involved — not just the interface. Understanding operational reality and
business context early reduces the risk of solving the wrong problem
later.

**Why it matters**: A screen is only the visible layer of a system.
Redesigning it without understanding what's underneath just makes the
same problem look better.

**How I approach it**: Contextual inquiry, stakeholder mapping, and
direct process observation — sitting with the people actually doing the
work, not just interviewing them about it afterward.

**AI and human judgment**: AI helps me process and organize a large
volume of interview notes, documents and observations faster — but
deciding what actually matters, and confirming it with the people
involved, stays a human judgment call throughout.

**In practice**: On the Workshop Quoting System, that meant sitting on
the shop floor at Guzmán Villalba to see how a quote actually got built —
paper, memory, a spreadsheet nobody fully trusted — before assuming the
fix was a better form. *[Related case: Workshop Quoting System — content
written, not yet published; see §11, decision 2]*

---

#### 02 — Find the real problem

**Short description**: From that map, I look for the specific point
where user pain, operational friction, and barriers to product or
business objectives actually meet — not a general list of issues. Naming
the real problem precisely is what keeps the rest of the work from
becoming decoration.

**Why it matters**: Teams often ask for a redesign when the real issue
actually sits one step earlier or later in the process. Solving the
wrong problem well is still solving the wrong problem.

**How I approach it**: Root-cause analysis, journey mapping, and
structured evaluation of where the friction actually shows up — not just
where it's reported.

**AI and human judgment**: AI can help surface patterns across a large
set of notes or tickets faster than reading them one by one — but naming
which pattern is the real problem, versus just the loudest one, is a
judgment call I don't hand off.

**In practice**: In Trazur, the interface had real usability issues, but
the deeper problem was trust — people didn't believe the platform
understood their situation, so they disengaged before the screen ever
became the issue. *[Related case: Trazur — content written, not yet
published; see §11, decision 2]*

---

#### 03 — Gather evidence

**Short description**: I build a case with whatever evidence is
available and relevant — interviews, observation, workflows, documents,
and product data such as errors, time, or adoption. Evidence turns a
debate about opinions into a decision grounded in what's actually
happening.

**Why it matters**: A strong opinion in the room isn't evidence, no
matter how senior it comes from. Evidence is what turns a debate about
taste into a decision about the system.

**How I approach it**: Interviews, field observation, and — where the
volume of material justifies it — AI-assisted synthesis to process more
evidence without skipping the review step.

**AI and human judgment**: AI can help process a larger volume of
interviews, documents or notes than would be practical by hand — but
every AI-synthesized finding still gets reviewed by a person before it
counts as evidence, not just accepted at face value.

**In practice**: For Trazur, that meant pairing traditional research
methods with AI-assisted analysis to work through a heavier volume of
material without skipping that review step. *[Related case: Trazur —
content written, not yet published; see §11, decision 2]*

---

#### 04 — Explore and challenge

**Short description**: Before committing to one direction, I generate
alternatives and challenge assumptions — comparing potential value,
effort, risk, and feasibility for each. This keeps a plan honest about
what it depends on and what it costs.

**Why it matters**: Every proposal quietly depends on something nobody's
confirmed yet. Finding that assumption — and testing the one that would
be expensive to get wrong — is cheaper than discovering it after launch.

**How I approach it**: Assumption mapping, walkthroughs with real users,
and comparing alternatives side by side instead of committing to the
first workable idea.

**AI and human judgment**: AI is useful for generating a wider set of
alternatives to compare quickly — but choosing which trade-off is
actually acceptable for this team, this budget, and this timeline isn't
something AI has the context to decide.

**In practice**: On the Workshop Quoting System, that meant checking
with the person who actually prices jobs that a proposed shortcut wasn't
quietly removing a judgment call they relied on. *[Related case: Workshop
Quoting System — content written, not yet published; see §11, decision
2]*

---

#### 05 — Design practical solutions

**Short description**: Not every problem needs a new interface —
sometimes the strongest move is fixing what's underneath it. When a
solution is the right call, I design it to hold up under real
conditions: usability and accessibility alongside business viability,
technical feasibility, maintainability, and the organization's actual
constraints.

**Why it matters**: A solution that only works under ideal conditions
doesn't survive a busy shop floor or a slow connection. Practical means
it still works on a bad day — and accessibility, clarity and
maintainability are considered from the beginning, not added as a final
compliance layer.

**How I approach it**: Service blueprints, wireframes, and
systems-level decisions about what actually needs to change versus what
just needs to be documented better.

**AI and human judgment**: AI can help explore layout or content
variations faster — but whether a solution is genuinely usable under
real conditions, for the people who'll actually rely on it, gets
confirmed with people, not inferred.

**In practice**: Trazur's proposed solution was built around
low-connectivity, low-fidelity conditions from the start, instead of
assuming a fast connection and a confident, tech-comfortable user.
*[Related case: Trazur — content written, not yet published; see §11,
decision 2]*

---

#### 06 — Test, learn and iterate

**Short description**: I treat a first version as a hypothesis,
evaluated against both user experience and relevant outcomes — task
success, errors, time, adoption, support load, or progress toward a
product goal. What "wrong" would look like is defined before shipping,
so the next version is actually better, not just different.

**Why it matters**: Iteration without a defined target just produces
motion, not improvement — deciding in advance what would prove a version
wrong is what makes revisiting it worthwhile.

**How I approach it**: Usage review, structured feedback loops, and
versioned documentation, so a decision made in an early version isn't
lost by the time a later one needs to build on it.

**AI and human judgment**: AI can help track and summarize feedback or
usage patterns over a longer period than I could review manually — but
deciding whether a result counts as success, and what to do next, is a
human call.

**In practice**: What success might look like here is fairly concrete:
fewer manual re-checks of a quote, less dependency on one person's
memory, or a shorter gap between a request and a usable estimate. Across
projects — from the Workshop Quoting System to institutional work at
Ceibal — the versions that held up were the ones designed to be
revisited on purpose, not treated as finished at delivery. *[Related
case: Workshop Quoting System — content written, not yet published; see
§11, decision 2]*

---

## 2. Tone — compliance check

The copy above was written and re-read against the ticket's tone list:
senior, practical, thoughtful, product-oriented, commercially aware,
humble, confident, clear, approachable — and against the two "avoid"
lists (academic/corporate/market-driven/buzzword language, and inflated
claims). No instance of "user-centered," "seamless," "innovative,"
"world-class," "game-changing," "10x," or "guaranteed ROI" appears
anywhere above. Every business/operational reference (friction, adoption,
support load, dependency on one person, calibration) is either drawn
directly from `docs/content/workshop-quoting-system-fields-en.md` /
`trazur-fields-en.md`, or phrased as a possibility ("what success might
look like"), never a delivered result.

## 3. Methods and references

Design Thinking, Lean UX, Service Design, Agile, and Design Sprints are
named exactly once, inside the introductory paragraph (§1B), as informing
influences — never as section headers, never as a labeled framework grid.
No author is quoted or named (no Don Norman, no Jake Knapp, no others) —
the six moves and their copy are written to demonstrate Ramiro's own
reasoning, not to borrow credibility from a citation.

## 4. Design references — principles extracted, not imitated

These informed structural/layout decisions below, not visual identity:

- **Nate Bauer** (editorial storytelling, case hierarchy) → each move is
  built as its own "story section" (§5) rather than a repeated card —
  directly why the layout moved from a uniform vertical list to
  alternating editorial compositions this round.
- **Emily Backes** (practical product narratives, AI integration) → the
  "AI and human judgment" consolidation (§1) and the transversal AI band
  placement (§9) — AI framed as part of the working narrative, not a
  separate features list.
- **Mizko** (positioning clarity) → the anchor line + balanced-model
  statement doing the positioning work up front, before any of the six
  moves start.
- **Aaron James** (restrained motion, typography) → the MVP motion list
  (§8) stays deliberately small; no motion effect exists that isn't tied
  to an existing, already-proven mechanism on this site.
- **Mikael Andersson** (multidisciplinary profile presentation) →
  reflected already in the introductory paragraph's plain acknowledgment
  of multiple informing methodologies without over-claiming expertise in
  any one of them.

No color, type, iconography, or page-chrome choice was copied from any of
the five — only the structural principle named next to each.

---

## 5. Visual layout blueprint

### Desktop

1. **Page head** (existing shared component) — eyebrow, H1, lead
   statement.
2. **Introduction band** — introductory paragraph + balanced-model
   statement, single column, measured width (~65-70ch), generous top/
   bottom spacing to breathe before the first move.
3. **Six moves, each its own full-width editorial section**:
   - A slim header row spans the full content width: a small mono
     number (`01`–`06`, same treatment as `.es-section-head__num`
     elsewhere on the site) + the move's title as an `h3`.
   - Below that, a **two-column composition**: illustration on one
     side (~40-42% width), title's short description + the "More on
     this move" disclosure on the other (~58-60% width) — same
     proportions already proven for About's Portrait/Intro section.
   - **Alternates sides by move**: odd moves (01, 03, 05) place the
     illustration on the left; even moves (02, 04, 06) place it on the
     right. Achieved by literally reordering the two columns in the
     markup (§6) — not a CSS `order` trick — so DOM order, visual order,
     and keyboard focus order all agree, satisfying the accessibility
     rule from the strategy doc (§11 there).
   - Illustration is vertically centered against the text column;
     generous whitespace above/below each section (not touching hairline
     rules the way the old uniform list did) so each move genuinely
     reads as its own section, not a repeating row — this is the direct
     fix for "avoid a long single-column document feeling."
   - Disclosure panel opens **inside the text column**, not full-width —
     it never crosses into the illustration's half.
4. **Transversal AI band** — full-width, distinct background tint (not
   the page's base background), no number, no illustration slot shaped
   like the six moves' — reads as a horizontal interlude between the
   sequence and the close. See §9 for the placement reasoning.
5. **Closing CTA** — short line + one button, same visual weight as
   other page-end CTAs already on the site (About's Resume CTA, Home's
   footer CTA) — not a second hero.
6. Standard footer.

### Mobile

- Illustration always stacks **above** its move's text (never below,
  regardless of the desktop left/right alternation) — matches the
  already-proven About Portrait/Intro mobile behavior (portrait first,
  full width, natural stack).
- Number + title stack as one block above the illustration.
- Disclosure panel spans full width once stacked.
- Sections keep the same generous vertical spacing as desktop, scaled
  down — still reads as six distinct sections, not a dense list.
- AI band and closing CTA both go full-width, same relative position in
  the sequence as desktop (after move 06, before the footer).

### Active-step / progress cue (supersedes the strategy doc's "connecting
rail" concept)

The previous strategy round proposed a persistent vertical rail
connecting all six moves — that assumed a single-column vertical list.
It doesn't fit this round's alternating editorial layout (there's no
single edge for a rail to run along). **Resolved**: replace it with a
small, fixed-position step counter (e.g. "02 — Find the real problem")
in a corner of the viewport, updated via the same IntersectionObserver
approach, visible only while scrolling through the six-move sequence
(hidden before move 01 and after move 06). Purely supplementary — same
accessibility treatment as before (nothing depends on it; every move's
number/title is static, readable content regardless of scroll position).

---

## 6. Gutenberg blueprint

Same architectural reasoning as the strategy doc (§10 there): no new
custom block. `core/image`, `core/heading`, `core/paragraph`,
`core/columns`, and the existing `estavillo/case-details` cover
everything below.

### Home teaser

```html
<!-- wp:group {"tagName":"section","className":"es-section es-process-teaser","anchor":"process"} -->
<section class="wp-block-group es-section es-process-teaser" id="process">
  <!-- wp:group {"className":"es-container"} -->
  <div class="wp-block-group es-container">

    <!-- wp:group {"className":"es-section-head"} -->
      <!-- num "02" + heading "How I work", existing .es-section-head pattern -->
    <!-- /wp:group -->

    <!-- wp:heading {"level":3,"className":"es-process-teaser__headline"} -->
    <h3>I don't start with interfaces. I start by understanding the system.</h3>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"className":"es-process-teaser__lead"} -->
    <p>The result should make sense for the people using it, and for the system that has to carry it.</p>
    <!-- /wp:paragraph -->

    <!-- wp:columns {"className":"es-process-teaser__row"} -->
    <div class="wp-block-columns es-process-teaser__row">

      <!-- wp:column {"width":"33.33%"} -->
      <div class="wp-block-column" style="flex-basis:33.33%">
        <!-- wp:heading {"level":4} --><h4>Understand</h4><!-- /wp:heading -->
        <!-- wp:paragraph --><p>See how people, information and goals actually connect.</p><!-- /wp:paragraph -->
      </div>
      <!-- /wp:column -->

      <!-- wp:column {"width":"33.33%"} -->
      <div class="wp-block-column" style="flex-basis:33.33%">
        <!-- wp:heading {"level":4} --><h4>Explore</h4><!-- /wp:heading -->
        <!-- wp:paragraph --><p>Test ideas and challenge assumptions before committing to one.</p><!-- /wp:paragraph -->
      </div>
      <!-- /wp:column -->

      <!-- wp:column {"width":"33.33%"} -->
      <div class="wp-block-column" style="flex-basis:33.33%">
        <!-- wp:heading {"level":4} --><h4>Improve</h4><!-- /wp:heading -->
        <!-- wp:paragraph --><p>Build something that works — and keeps working.</p><!-- /wp:paragraph -->
      </div>
      <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

    <!-- wp:buttons -->
      <!-- "See the full process →", same .es-link-arrow-style pattern as other Home CTAs -->
    <!-- /wp:buttons -->

  </div>
  <!-- /wp:group -->
</section>
<!-- /wp:group -->
```

No className on any `core/column` here (three-way split, width only) —
same proven-safe rule as About's Columns fixes.

### Page introduction

```html
<!-- wp:paragraph {"className":"es-how-page__intro"} -->
<p>Most of my work starts the same way…</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"es-how-page__balance"} -->
<p>A solution only really works if it holds up on more than one axis at once…</p>
<!-- /wp:paragraph -->
```

Page head itself (eyebrow/H1/lead) stays the existing shared PHP
`template-parts/page-head`, unchanged — same pattern already used on
About/Work/Contact.

### One process move (shown for move 01 — illustration-left; move 02
would simply write the two `core/column`s in the opposite order)

```html
<!-- wp:group {"tagName":"article","className":"es-process-move","anchor":"understand-the-system"} -->
<article class="wp-block-group es-process-move" id="understand-the-system">

  <!-- wp:group {"className":"es-process-move__head"} -->
  <div class="wp-block-group es-process-move__head">
    <!-- wp:paragraph {"className":"es-process-move__num"} -->
    <p class="es-process-move__num">01</p>
    <!-- /wp:paragraph -->

    <!-- wp:heading {"level":3,"className":"es-process-move__title"} -->
    <h3>Understand the system</h3>
    <!-- /wp:heading -->
  </div>
  <!-- /wp:group -->

  <!-- wp:columns {"className":"es-process-move__row"} -->
  <div class="wp-block-columns es-process-move__row">

    <!-- wp:column {"width":"42%"} -->
    <div class="wp-block-column" style="flex-basis:42%">
      <!-- wp:image {"sizeSlug":"large","linkDestination":"none"} /-->
    </div>
    <!-- /wp:column -->

    <!-- wp:column {"width":"58%"} -->
    <div class="wp-block-column" style="flex-basis:58%">

      <!-- wp:paragraph {"className":"es-process-move__text"} -->
      <p>I start by mapping how the system actually works…</p>
      <!-- /wp:paragraph -->

      <!-- wp:estavillo/case-details {"summary":"More on this move","className":"is-style-light"} -->

        <!-- wp:heading {"level":4} --><h4>Why it matters</h4><!-- /wp:heading -->
        <!-- wp:paragraph --><p>A screen is only the visible layer of a system…</p><!-- /wp:paragraph -->

        <!-- wp:heading {"level":4} --><h4>How I approach it</h4><!-- /wp:heading -->
        <!-- wp:paragraph --><p>Contextual inquiry, stakeholder mapping, and direct process observation…</p><!-- /wp:paragraph -->

        <!-- wp:heading {"level":4} --><h4>AI and human judgment</h4><!-- /wp:heading -->
        <!-- wp:paragraph --><p>AI helps me process and organize a large volume of interview notes…</p><!-- /wp:paragraph -->

        <!-- wp:heading {"level":4} --><h4>In practice</h4><!-- /wp:heading -->
        <!-- wp:paragraph --><p>On the Workshop Quoting System, that meant sitting on the shop floor…</p><!-- /wp:paragraph -->

      <!-- /wp:estavillo/case-details -->

    </div>
    <!-- /wp:column -->

  </div>
  <!-- /wp:columns -->

</article>
<!-- /wp:group -->
```

The whole move is one outer `core/group` — one collapsible node in List
View per move, exactly the requirement. No className combined with a
width attribute on any `core/column` — width only, styling scoped
structurally (`.es-process-move__row > .wp-block-column:first-child`,
etc.), same rule proven on About.

**A registered Pattern**, `"How I Work — Move"`, should pre-compose this
whole structure (illustration-left variant) plus a second variant for
illustration-right, so building all six is "insert pattern six times,
alternate which variant, fill in text" — not manual block assembly each
time.

### Transversal AI section

```html
<!-- wp:group {"tagName":"section","className":"es-section es-process-ai-band","anchor":"ai-in-the-work"} -->
<section class="wp-block-group es-section es-process-ai-band" id="ai-in-the-work">
  <!-- wp:group {"className":"es-container"} -->
  <div class="wp-block-group es-container">

    <!-- wp:heading {"level":2,"className":"es-process-ai-band__statement"} -->
    <h2>AI accelerates the work. Human judgment defines the direction.</h2>
    <!-- /wp:heading -->

    <!-- wp:paragraph --><p>…2-3 sentences, §1's Core Positioning language…</p><!-- /wp:paragraph -->

  </div>
  <!-- /wp:group -->
</section>
<!-- /wp:group -->
```

### Final CTA

```html
<!-- wp:group {"tagName":"section","className":"es-section es-process-closing"} -->
<section class="wp-block-group es-section es-process-closing">
  <!-- wp:group {"className":"es-container"} -->
  <div class="wp-block-group es-container">

    <!-- wp:paragraph --><p>See how this plays out on a real project.</p><!-- /wp:paragraph -->

    <!-- wp:buttons -->
      <!-- link to Work / Selected Work -->
    <!-- /wp:buttons -->

  </div>
  <!-- /wp:group -->
</section>
<!-- /wp:group -->
```

None of this has been tested against the real block parser yet — that
validation happens during implementation, following the exact same
harness-based process already used for About.

---

## 7. Illustration brief

**Shared art direction** (unchanged from the strategy doc, restated for
completeness): thin linework (1.5-2px), one recurring simplified human
figure interacting with a system/network motif across all six, one
restrained green accent (`var(--es-accent)`) per piece, transparent
background, consistent canvas/perspective, meaningful rather than
decorative, drawn as clean separable shapes so a future motion pass can
animate one part without redrawing. Editorial illustration, not a UI
icon — no generic iconography, no SaaS feature-icon style, no stock
illustration look.

**Six individual concepts** (unchanged from the strategy doc):

1. **Understand the system** — figure studying a tangled network of
   nodes.
2. **Find the real problem** — figure pointing at one highlighted node
   among many unhighlighted ones.
3. **Gather evidence** — figure collecting/stacking documents or data
   fragments.
4. **Explore and challenge** — figure at a fork, facing branching paths.
5. **Design practical solutions** — figure assembling a clear structure
   out of loose parts.
6. **Test, learn and iterate** — figure with a loop/cycle arrow,
   reviewing a result.

Not generated yet, per instruction. Production approach (who draws them,
on what timeline) is a blocking decision — §11, decision 1.

---

## 8. MVP motion specification

- **Progressive reveal** — reuse `[data-es-reveal]` as-is, one move at a
  time as it scrolls into view (not staggered sub-elements within a
  move — the move is the unit now, not a card in a grid).
- **Active step accent** — the fixed step-counter cue from §5 (supersedes
  the earlier "connecting rail" concept), IntersectionObserver-driven,
  purely supplementary.
- **Subtle illustration movement** — small idle/hover shift only (2-4px
  drift or the accent element shifting on hover/focus-within), same
  restrained precedent as the hobby icons.
- **Smooth disclosure animation + plus-to-minus transition** — direct
  reuse of `.es-case-details.is-style-light`'s already-fixed
  transform-based indicator, unchanged.
- **`prefers-reduced-motion: reduce`** — disables all of the above;
  state still changes, just without animating.

Everything else (illustration video, scroll-scrubbed animation,
cursor-follow/parallax, richer interactions) stays in the backlog —
already captured in `docs/BACKLOG.md`, no changes needed there for
motion.

---

## 9. AI placement — resolved

The transversal AI statement sits **after the six moves, before the
closing CTA** (§5, §6) — a distinct full-width band with its own
background treatment, no number, no illustration in the six-move style.
This placement was reconsidered against this round's new alternating
editorial layout and still holds: it reads as a closing lens over
everything just described (each move's own "AI and human judgment"
section already showed AI at work throughout), not a seventh scene in
the sequence and not competing with any single move for attention. It
sits below the narrative, not inside or above it.

---

## 10. Differentiation

Places where generic UX phrasing was deliberately replaced with language
specific to how Ramiro actually works:

| Generic version (avoided) | Used instead |
|---|---|
| "I follow a user-centered design process." | "I don't start with interfaces. I start by understanding the system." |
| "I conduct research to inform design decisions." | "I build a case with whatever evidence is available and relevant… evidence turns a debate about opinions into a decision grounded in what's actually happening." |
| "I create seamless, intuitive experiences." | "Practical means it still works on a bad day." |
| "I leverage AI to enhance the design process." | Per-move "AI and human judgment," always paired with what stays human and how output gets checked — never AI capability alone. |
| "I iterate based on user feedback." | "What success might look like here is fairly concrete: fewer manual re-checks of a quote, less dependency on one person's memory…" |

The throughline across all six moves is the thing a generic senior UX
narrative usually doesn't say directly: turning **tacit, undocumented
knowledge** (a price that lives in one person's memory, a workaround
nobody wrote down) into something **explicit, evidenced and shared** —
named directly in move 01's framing and echoed in move 06's example. That
is the actual answer to "why does Ramiro work differently," not a claim
about being different asserted on its own.

---

## 11. Remaining blocking decisions

Every decision from the strategy doc's §13 that this specification
resolves is treated as settled by the copy/blueprint above (grouping
split, labels, the three renames, `why` placement, AI band copy, closing
CTA, tone calibration, generic-vs-case-specific success language) — not
repeated here. Two genuinely still block implementation:

1. **Illustration production.**
   - *Question*: who draws the six illustrations, and on what timeline?
   - *Recommendation*: AI-assisted drafting + human review/refinement,
     reusing the hobby-icon precedent (draft, approve, normalize into
     `assets/`).
   - *Alternative*: commission hand-drawn illustration from scratch.
   - *Trade-off*: AI-assisted is faster/cheaper but needs careful human
     art-direction to hold consistent perspective/proportions across all
     six; commissioned work is slower/costlier but may need fewer
     consistency passes.
   - *Blocks implementation*: **yes, but only for the illustrations
     themselves** — the Gutenberg structure, copy, and layout can all be
     implemented now with empty `core/image` placeholders (same proven
     About-portrait pattern), illustrations added after.

2. **Related-case links** (Workshop Quoting System, Trazur — both
   referenced in "In practice" above).
   - *Question*: link to the real Case Study permalinks now, or keep
     text-only until those cases are actually published?
   - *Recommendation*: text-only for now (as written above, with the
     "content written, not yet published" note) — do not link to a post
     that isn't live. Convert to a real permalink once each case is
     published (tracked separately in `docs/BACKLOG.md`, "Enter
     Presupuestador and Trazur as real, published Case Study posts").
   - *Alternative*: publish placeholder/draft-status links now anyway.
   - *Trade-off*: text-only is fully honest today but less useful until
     the cases go live; linking now risks a dead or draft-only link on a
     public page.
   - *Blocks implementation*: **no** — text-only mentions are ready to
     ship as-is; this only affects when the links themselves get added.

---

## Deliverable summary

Files changed by this ticket: **`docs/HOW-I-WORK-CONTENT-SPEC.md`**
(new). `docs/BACKLOG.md` was reviewed and needs **no changes** — every
open item this document touches (illustration production, related-case
linking, Presupuestador/Trazur publication) is already tracked there
from prior rounds. No Home, How I Work, plugin, theme, Gutenberg content,
or ZIP file was modified. Stopping here per the instruction.
