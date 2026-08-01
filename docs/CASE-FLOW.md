# Case Flow — interactive process-flow component

`estavillo/case-flow` is a design-system component, not a Trazur one-off.
Same data model renders a Process Flow, a User Journey, a Service
Blueprint, a Decision Tree, an IA Workflow or a System Diagram — the shape
comes from the data, never from hand-written markup.

## 1. Why one block and not a Pattern

The ticket asked to prefer a Pattern. A Pattern cannot do this job, for
four concrete reasons:

1. **Connectors must be computed.** A Pattern is frozen, serialized HTML.
   Adding or reordering a node would mean hand-editing SVG paths — which
   the ticket explicitly forbids. The block derives every connector from
   the node list.
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

## 3. Data model

```jsonc
{
  "startLabel": "Course catalog",     // optional pill before the flow
  "endLabel":   "Certificate downloaded",
  "detailLabel":"Show detail",        // the "more" affordance on each node
  "closeLabel": "Close",
  "stepLabel":  "Step",               // screen-reader word before each number
  "density":    "comfortable",        // | "compact"
  "nodes": [
    {
      "kind":   "step",               // start | step | decision | milestone | end
      "accent": "signal",             // "" | signal (green) | decision (orange) | muted
      "num":    "01",
      "title":  "Homepage",
      "text":   "One short line shown on the node.",
      "detail":  [ { "label": "Pain point", "text": "…" } ],   // → popover rows
      "branches":[ { "label": "Yes", "text": "…" } ]           // → decision yes/no
    }
  ]
}
```

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
| Popover content | "Detail rows" on the node — each row is a Label + Text; add/reorder/remove freely |
| Decision yes/no | "Branches" on the node — Branch label + Outcome |
| Start / end markers | Inspector → Flow labels (blank = hidden) |
| "Show detail" / "Close" wording | Inspector → Flow labels |
| Density | Inspector → Density |

No SVG is ever edited by hand. Connectors, diamonds, the vertical/
horizontal switch and the progress indicator are all derived.

## 5. Responsive behaviour

**Mobile is the base layout, not a fallback** — the CSS has no media
query until 680px, so the phone experience is what the component is
written for first.

- **Mobile (base):** vertical editorial narrative. One card per step,
  marker column on the left with the vertical connector, sticky `01/08`
  progress indicator that tracks the step in view, detail expands as an
  in-flow card (never a popover), 44px+ touch targets, no horizontal
  scroll. Same two-column marker/body idea already used by
  `.es-process-detail` on How I Work.
- **Tablet (≥680px):** same vertical narrative with more air; branches
  move side by side to use the width.
- **Desktop (≥1024px):** horizontal reading, laid out as a 3-column grid
  (4 at ≥1280px) so a 6–9 step flow is visible **all at once**. Deliberately
  a wrapping grid rather than one scrolling row: hiding half a process
  behind a horizontal scroll gesture would defeat "desktop presents the
  process clearly". The connector is hidden on the first node of each row
  so the wrap reads cleanly. Detail becomes a popover.

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

**Performance** — ~4 KB of vanilla JS, no framework, no dependency, no
build step. Inline SVG (two paths per connector, `non-scaling-stroke`,
no `<defs>`/`<marker>` so multiple flows on a page can't collide on IDs).
Assets load only on pages that contain the block. One
`IntersectionObserver` drives the progress indicator.

## 8. Roadmap — reuse without rebuilding

Already supported by the current data model, no code change needed:

- **User Journey** — one node per stage; detail rows become
  Doing / Thinking / Feeling / Pain / Opportunity.
- **Process Flow / Product Workflow** — the Trazur case as shipped.
- **Decision Tree** — `kind: "decision"` with branches; chain several.
- **IA Workflow** — accents mark automated vs. human-judgment steps.
- **System Diagram** — `kind: "milestone"` for components, detail rows
  for inputs/outputs.

Genuinely needs future work: **Service Blueprint** would want a lane
axis (frontstage / backstage / support). That is an additive
`lane` field on the node plus a grid row mapping — the existing nodes
would keep rendering unchanged. Deliberately not built now: no second
real use case exists yet to validate the shape (same "build the block
after several projects prove the structure repeats" rule already written
into `case-patterns.php`).

## 9. Known limitations (honest)

- **The flow is a linear spine with side branches, not an arbitrary
  graph.** Branches are labelled outcomes attached to a decision node;
  they do not re-merge into a different downstream node, and there is no
  edge routing between arbitrary pairs. Every diagram listed in §8 fits
  this shape. A true graph would need a layout solver and would make the
  editing UI far heavier — explicitly not worth it for a portfolio
  component.
- **Not verified inside a real Gutenberg editor.** No WordPress instance
  can run in this environment. What *was* verified: `block.json` is valid,
  the real WP block parser round-trips both content files with zero
  invalid blocks and identical EN/ES structure, `edit.js` passes a syntax
  check and uses only the same APIs as the eleven shipped blocks, and the
  attributes survive a full serialize→parse→render cycle through the real
  `render.php`. The editor canvas, save/reopen and List View still need a
  human pass on the live site before publishing.
- Desktop wraps to rows at 3/4 columns; a flow longer than ~12 nodes will
  read as several rows. Acceptable for case-study use; a dedicated
  "long flow" treatment is not built.
