# Case Flow — interactive process-flow component

`estavillo/case-flow` is a design-system component, not a Trazur one-off.
Same data model renders a Process Flow, a User Journey, a Service
Blueprint, a Decision Tree, an IA Workflow or a System Diagram — the shape
comes from the data, never from hand-written markup.

## 1. Why one block and not a Pattern

The ticket asked to prefer a Pattern. A Pattern cannot do this job, for
four concrete reasons:

1. **Connectors and positions must be computed.** A Pattern is frozen,
   serialized HTML. Adding a node, reordering one, or forking a decision
   would mean hand-editing SVG paths and grid coordinates — which the
   ticket explicitly forbids. The block derives every connector, row,
   column and branch lane from the node graph.
2. **The data is typed, not free-form.** Node kind, accent, branch labels,
   detail rows and order are structured fields. Core blocks give you
   paragraphs, not a schema.
3. **List View has to stay legible.** A Pattern of this shape explodes
   into dozens of nested Groups. One block = one clear List View entry
   ("Case Flow"), with everything inside edited in place.
4. **Repo precedent.** `case-decisions`, `case-timeline`, `case-stats` and
   `hobby-list` are already exactly this: one dynamic block with an
   `items` array. Following it adds zero new concepts.

Everything else follows the existing library conventions: dynamic render
(`render.php`, server-side, so evolving markup can never invalidate a
saved block), editor JS with **no build step**
(`wp.element.createElement`, no JSX), and presentation owned by the child
theme, not duplicated in the plugin.

## 2. Files

| File | Role |
|---|---|
| `estavillo-portfolio-core/blocks/case-flow/block.json` | Attributes + registration |
| `estavillo-portfolio-core/blocks/case-flow/render.php` | Server render (the only markup source) |
| `estavillo-portfolio-core/blocks/case-flow/edit.js` | Editor UI (repeaters, no JSX) |
| `estavillo-child/assets/css/case-flow.css` | All presentation (tokens only) |
| `estavillo-child/assets/js/case-flow.js` | Optional interaction layer (~4 KB) |

Loaded on the front end only where the block actually is
(`has_block( 'estavillo/case-flow' )` in `inc/enqueue.php`), and bridged
into the editor iframe by `includes/case-blocks.php` so the editor preview
matches the real site.

`docs/backups/case-flow-pre-graph-refactor/` holds the complete
pre-graph-model implementation (all five files above plus both content
files) and a `RESTORE.md` with the exact copy-back commands, in case the
linear-spine version ever needs to come back.

## 3. Data model

**The data is a directed graph.** Each node declares which nodes it goes
*to*; the layout — row, column, branch lane, connector geometry — is
computed from those edges. Nothing about position is authored.

```jsonc
{
  "sectionLabel": "Ideal user flow", // optional eyebrow above the flow
  "startLabel": "Course catalog",     // optional pill before the flow
  "endLabel":   "Certificate downloaded",
  "detailLabel":"Show detail",        // the "more" affordance on each node
  "closeLabel": "Close",
  "stepLabel":  "Step",               // screen-reader word before each number
  "density":    "comfortable",        // | "compact"
  "nodes": [
    {
      "id":     "login",              // stable handle other nodes point at
      "kind":   "decision",           // start | step | decision | milestone | end
      "accent": "decision",           // "" | signal (green) | decision (orange) | muted
      "num":    "03",
      "title":  "Signed in?",
      "text":   "One short line shown on the node.",
      "edges":  [                     // ← the graph. Order matters (see below).
        { "to": "checkout", "label": "Yes" },
        { "to": "register", "label": "No"  }
      ],
      "edgeLabel":"Main entry to the site",                  // → label on the incoming arrow
      "ai":     true,                                        // → (IA) badge on the shape
      "detail": [ { "label": "Pain point", "text": "…" } ]   // → popover rows
    }
  ]
}
```

### How edges become a layout

`es_flow_layout()` in `render.php` is the whole engine, in six passes:

1. **Ids.** Nodes without an explicit `id` get a stable generated one.
2. **Edges.** *If no node in the flow declares `edges` at all, the nodes
   auto-chain in document order.* That is the backward-compatibility
   contract: any flow authored before this model keeps rendering exactly
   as it did, with no migration.
3. **Main path.** Walk `edges[0]` from the first node. **The first edge of
   a node is its main line; every later edge is a branch.** That is the
   only convention an editor has to know.
4. **Serpentine.** The main path is placed left→right, then right→left,
   wrapping every 4 columns. Band parity drives `.is-rtl`.
5. **Branch lanes.** A branch target is placed in an extra grid row above
   its band, offset one column off the main line. A lane row is only
   allocated for bands that actually have branches, so a linear flow uses
   the exact same rows it used before.
6. **Connectors.** Each branch node gets an `in` and an `out` descriptor
   carrying the *row of the other endpoint*, and geometry is chosen from
   that row relationship (`is-from-above` / `is-to-below` …) — not from
   "is this a loop". This is what lets a branch that starts and ends in
   the same band work identically to one that spans a band break.

A **loop** needs no special authoring: it is simply a branch whose target
is a node that already appears earlier on the main path. The engine
detects it from the graph and routes the return path offset from the main
column so it reads as parallel rather than merged.

A dangling edge (pointing at an id that does not exist) is ignored; it can
never break the render.

### Shape vocabulary

`kind` maps to a real flowchart shape, matching the reference diagram:

| `kind` | Shape | Used for |
|---|---|---|
| `start` / `end` | **Pastilla** (stadium, filled) | Inicio / Fin |
| `step` | **Rectángulo** | A process step |
| `decision` | **Rombo** (true 45°-rotated square, text unrotated inside) | A yes/no question |
| `milestone` | Rectangle with a thick left edge | A checkpoint |

Boxes are connected by **L-shaped connectors built from two CSS borders
plus a fixed-size arrow SVG** — never a stretched SVG, because
`preserveAspectRatio="none"` distorts an arrowhead at arbitrary column
widths while orthogonal borders never distort. The description sits
*outside and below* each shape, and `edgeLabel` prints above the incoming
arrow — the same reading order as the reference PNG. Branch connectors
carry their edge label (Yes / No) as a small mono chip on the connector
itself, and are always routed into a card's **side or top**, never its
bottom, so they can't cross the card's own body text.
The `(IA)` badge marks assistant touchpoints and prints its legend once
at the foot of the flow (`aiLegend`), only if at least one node uses it.

**Why `detail` is a label/text array and not fixed fields.** The ticket
lists description / UX reasoning / pain point / opportunity / AI
intervention / business value / accessibility / evidence as *possible*
information. Hard-coding those eight as named attributes would lock the
component to one diagram type. An open label/text array covers all of
them today and covers Service Blueprint lanes or Journey emotions
tomorrow **without a schema change or a block deprecation**. That single
decision is what makes the roadmap in §8 reachable without a rebuild.

`kind` and `accent` are validated against a whitelist server-side; an
unknown value falls back to `step` / no accent, so old or hand-edited
data can never break the render.

## 4. Editing guide (what an editor actually does)

Insert **Case Flow** (Estavillo Case Study category). Then:

| To change | Where |
|---|---|
| Node number, title, one-line text | Type directly on the node in the canvas |
| Node type (step / decision / start / milestone / end) | "Node type" select on the node |
| Colour accent | "Accent" select on the node — Signal (green) / Decision (orange) / Muted |
| Node order | ↑ / ↓ buttons on the node |
| Add / remove a node | "Add node" at the end; trash icon on a node |
| **Where a node goes next** | **"Goes to" on the node — one row per outgoing edge: target node + optional label. The *first* row is the main line; the rest are branches.** |
| **A decision's Yes / No** | **Give the decision two "Goes to" rows and label them Yes and No. Each points at a real node.** |
| **A "No" that detours through its own screen** | **Point the No edge at that screen's node, and give *that* node a "Goes to" row pointing back at the step the flow rejoins.** |
| **A loop / return path** | **Point a "Goes to" row at any node that appears earlier in the flow. Nothing else to configure.** |
| **Node ID** | **"Node ID" on the node — only needed if you want a readable handle; one is generated otherwise** |
| Section eyebrow above the flow | Inspector → Flow labels → "Section label" (blank = hidden) |
| Label on the incoming arrow | "Label on the incoming arrow" on the node (blank = none) |
| Mark a node as an AI touchpoint | "AI intervention point (IA)" toggle on the node |
| The (IA) legend wording | Inspector → Flow labels → "(IA) legend" |
| Popover content | "Detail rows" on the node — each row is a Label + Text; add/reorder/remove freely |
| Start / end markers | Inspector → Flow labels (blank = hidden) |
| "Show detail" / "Close" wording | Inspector → Flow labels |
| Density | Inspector → Density |

No SVG is ever edited by hand, and **no position is ever authored**.
Connectors, branch lanes, diamonds, the vertical/horizontal switch and the
progress indicator are all derived from the node list and its edges.

> **Migrating an older flow.** Nothing to do. A flow with no `edges` on
> any node auto-chains in document order and renders exactly as before.
> Add edges only to the nodes that actually fork.

## 5. Responsive behaviour

**Mobile is the base layout, not a fallback** — the CSS has no media
query until 680px, so the phone experience is what the component is
written for first.

- **Mobile (base):** vertical editorial narrative in **narrative order** —
  the main path top to bottom, with each branch screen appearing directly
  after the decision that opens it, indented behind a dashed guide and
  keeping its Yes/No label. One shape per step at full width, a sticky
  `01/12` progress indicator that tracks the step in view, detail expands
  as an in-flow card (never a popover), 44px+ touch targets, no horizontal
  scroll. Branch geometry is dropped, but branch *logic* is not: the
  indent plus the label still say "this is the No path".
- **Tablet (≥680px):** same vertical narrative with more air.
- **Desktop (≥1280px):** the full graph. A serpentine grid whose columns
  come from `--es-flow-cols` and whose rows come from the layout engine,
  with an extra **branch lane** row above any band that has branches.
  Deliberately a wrapping grid rather than one scrolling row: hiding half
  a process behind a horizontal scroll gesture would defeat "desktop
  presents the process clearly". Where a branch lane interrupts the main
  line, an empty `.es-flow__pass` grid item carries the line vertically
  through the lane so the spine never appears broken. Detail becomes a
  popover.

Every node's placement is `grid-row: var(--r); grid-column: var(--c)` from
PHP-emitted custom properties — there is no `nth-child` positioning left
anywhere in the stylesheet, which is precisely what stops the CSS from
knowing anything about this particular diagram.

## 6. Interaction and accessibility

- Trigger is a real `<button>` — Enter/Space work for free. A node with
  no detail rows renders a plain `<div>` instead, so there is never an
  interactive control that does nothing.
- `aria-expanded` + `aria-controls` on the trigger, pointing at the panel.
- Desktop: hover **and** keyboard focus both open (parity). Leaving with
  the mouse does not close while focus is still inside.
- Touch: tap opens, second tap closes, outside tap closes.
- `Escape` closes and returns focus to the trigger.
- Popovers are clamped inside the viewport by measuring the real rect and
  writing `--es-flow-shift` (and flipping upward when there is no room
  below) — not by hoping the CSS is enough.
- Focus ring is the site-wide green outline from `base.css`.
- The flow is an `<ol>`; branches are a nested `<ul>`. The step number is
  prefixed with a screen-reader-only word ("Step") so "01" is announced
  meaningfully.
- Decision state is carried by shape **and** colour (diamond + orange),
  never colour alone.
- Verified contrast on the dark surface: title 13.9:1, body 5.7:1,
  popover text 9.7:1, step number 6.6:1.

## 7. Motion, progressive enhancement, performance

**Motion** — restrained, one pass, never looping: connector lines draw
themselves once on load (`stroke-dasharray`), arrowheads fade in once,
hover lifts a node 2px, the popover reveals in `--es-dur-fast`. Every one
of those is disabled under `prefers-reduced-motion: reduce`, with no
content lost.

**Progressive enhancement** — this is structural, not a nicety. The
server always emits the detail panels *visible*; the JS adds
`.is-enhanced` and only then does CSS collapse them into popovers /
accordions. With JS blocked or broken the whole flow — every node, every
detail row, every branch — reads top to bottom as a static document.
Verified in the harness with the script removed.

**Connector alignment** — the connector is a child of the *shape*, not of
the list item, so `top:50%` / `left:50%` centres it on whatever that shape
happens to be: a one-line rectangle, a two-line title, or a much taller
diamond. There is no height constant anywhere. An earlier version hung it
off the list item and needed a magic number that silently broke the moment
a title wrapped.

**Performance** — ~4 KB of vanilla JS, no framework, no dependency, no
build step. Connector *lines* are CSS borders (free); only the arrowheads
and branch tips are inline SVG, each a fixed-size `<polyline>` with
`non-scaling-stroke` and no `<defs>`/`<marker>`, so several flows on one
page can't collide on IDs. The layout is computed once in PHP and shipped
as custom properties — **the browser does no measurement and the JS does
no layout work at all**. Assets load only on pages that contain the block.
Two `IntersectionObserver`s: one for the progress indicator, one one-shot
observer for the entrance reveal.

## 8. Roadmap — reuse without rebuilding

Already supported by the current data model, no code change needed:

- **User Journey** — one node per stage; detail rows become
  Doing / Thinking / Feeling / Pain / Opportunity.
- **Process Flow / Product Workflow** — the Trazur case as shipped.
- **Decision Tree** — `kind: "decision"` with two or more edges; chain
  several. Branches that detour through their own screen and rejoin are
  first-class.
- **IA Workflow** — accents mark automated vs. human-judgment steps.
- **System Diagram** — `kind: "milestone"` for components, detail rows
  for inputs/outputs.
- **Loops and return paths** — any edge pointing at an earlier node.

Genuinely needs future work: **Service Blueprint** would want a lane
axis (frontstage / backstage / support). That is an additive
`lane` field on the node plus a grid row mapping — the existing nodes
would keep rendering unchanged. Deliberately not built now: no second
real use case exists yet to validate the shape (same "build the block
after several projects prove the structure repeats" rule already written
into `case-patterns.php`).

## 9. Known limitations (honest)

- **Parallel paths are accepted by the data model but have no distinct
  layout yet.** A node with two forward edges that both continue
  independently and never rejoin will render, but the second path is
  placed with the branch-lane treatment rather than as a genuine second
  spine. Forks that rejoin — which is what a decision is — are fully
  supported; true parallelism is not, and needs a second real use case
  before the layout is worth designing.
- **The engine picks the main path by convention, not by analysis:** it
  follows `edges[0]`. Authoring a flow whose first edge is the *exception*
  path will lay the diagram out around the exception. This is documented
  in the editor UI, but it is a convention an author can get wrong.
- **Branch depth is one level.** A branch node's own branches are not
  given their own lane.
- **Not verified inside a real Gutenberg editor.** No WordPress instance
  can run in this environment. What *was* verified: `block.json` is valid,
  the real WP block parser round-trips both content files with zero
  invalid blocks and identical EN/ES structure, `edit.js` passes a syntax
  check and uses only the same APIs as the eleven shipped blocks, and the
  attributes survive a full serialize→parse→render cycle through the real
  `render.php`. The editor canvas, save/reopen and List View still need a
  human pass on the live site before publishing.
- Desktop wraps to rows of 4 columns, so a long flow reads as several
  bands. This is verified to work at length (an 18-node fixture lays out
  as 5 correct serpentine bands with no CSS change), but a flow of that
  size is tall; a dedicated "long flow" treatment is not built.
