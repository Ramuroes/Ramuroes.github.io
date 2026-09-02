# Presupuestador / Restimator — case study update notes (2026-09-02)

What changed in `presupuestador-case-study-es.html` and
`presupuestador-case-study-en.html`, why, and what's still pending. Read
this before publishing either file — it has the image mapping, the new
Case Index, and the technical decision that changed the file format.

No case content was deployed to WordPress as part of this update — this
is a repo-only implementation for review, on `main`, per the request.

## 1. What was kept

Almost every sentence of the previous draft survives somewhere in the new
structure — nothing about the taller, the original problem, the MVP, or
the closing reflections was factually wrong, so none of it was rewritten
from scratch:

- **Context** (`#context`) and **Problem** (`#problem`) — reused near
  verbatim; still accurate.
- **MVP** (`#mvp`) — reused, with the old `#testing` section's
  compare-and-adjust loop folded into it (same content, no longer a
  separate anchor — it's part of the MVP's own validation story, not a
  distinct stage).
- **Learnings** (`#learnings-v1`) — the two original paragraphs are
  unchanged, with one new paragraph added to bridge into why V2 exists.
- **Next steps** (`#next`) — the original three items are still there,
  plus a fourth about closing the approval/production loop.
- All `[DATO PENDIENTE DE VALIDACIÓN — …]` / `[DATA PENDING VALIDATION —
  …]` placeholders for unverifiable numbers are preserved exactly — none
  were filled in with invented figures, and two new ones were added
  (client-response-time impact, and how many people quote independently)
  where the earlier "Results" section made the same honest admission.

## 2. What was added

- **Four Case Flow diagrams**, exactly as requested — see §4.
- **A distinct V1 vs. V2 narrative.** The previous draft only had one
  product stage ("App Alpha") between the Sheets MVP and "Results." This
  update splits that into a real V1 (a small, fast estimator — implemented,
  in use) and a real V2 (a fuller quotation-and-production system — partly
  implemented, partly still being built), with an explicit bridge section
  (`#evolution-to-v2`) explaining why V1 wasn't enough.
- **`#v2-design`** — a new section on V2's product-design decisions
  (distributing complexity, the reusable product-template library, the
  conceptual blueprint reader), each explicitly labeled prototype/designed,
  not shipped.
- **`#production`** — a new section on the taller/production bridge,
  separating what's implemented (the production view) from what's only
  designed (the production order, purchasing).
- **A rewritten `#status`** (was `#results`) that states V1 and V2's real
  status side by side, folds in the old `#limitations` list, and includes
  the QA/UX/accessibility review evidence.
- **An honest correction on AI usage.** The earlier draft's `#ai` section
  described three *in-product* AI features (reading a photo, suggesting a
  range, flagging anomalies) as if they were shipped. That claim was never
  backed by verifiable evidence in this repository — the same problem an
  earlier ticket already found and corrected for the English "Workshop
  Quoting System" draft (see §6). This update removes that framing
  entirely rather than repeating it, and replaces it with one honest
  paragraph inside `#status`: AI was used for development/documentation
  support (structuring criteria, coding assistance, organizing docs), not
  as an in-product feature. No `#ai` section on its own anymore.

## 3. What was reordered

New anchor order (old → new mapping):

| Old anchor | New anchor | Change |
|---|---|---|
| `#overview` | `#overview` | kept, ladder updated to the new stage model |
| `#context` | `#context` | unchanged |
| `#problem` | `#problem` | unchanged |
| `#discovery` | folded into `#as-is` | the shadowing/research insight now frames "how it worked before," instead of sitting as its own step |
| — | `#as-is` (new) | **Flow 1** |
| `#system` (architecture half) | split: intro → `#hypothesis`, rest → `#system` (V2) | the MVP-era "one model, several surfaces" idea now opens as the hypothesis, and reappears at V2 scale later |
| `#mvp` | `#mvp` | unchanged, absorbs old `#testing` |
| `#testing` | folded into `#mvp` | see above |
| `#app-alpha` | `#v1-product` | reframed as the *rapid estimator*, not the full app — **Flow 2** |
| `#learnings` | `#learnings-v1` | unchanged content, new anchor |
| — | `#evolution-to-v2` (new) | bridge |
| — | `#v2-design` (new) | |
| `#system` (rest) | `#system` | now V2's fuller architecture — **Flow 3 (v2)** |
| — | `#evolution` (new) | **Flow 4** |
| — | `#production` (new) | |
| `#results` | `#status` | rewritten, absorbs `#limitations` and the AI note |
| `#limitations` | folded into `#status` | |
| `#ai` | removed as a section; corrected paragraph moved into `#status` | see §2 |
| `#next` | `#next` | unchanged content, one item added |

**`presupuestador-case-study-fields.md` was updated to match** — both
language sections' **Case index** blocks now list the 15 new anchors
(replacing the old 13 entirely), and the Status/label and hero-image
recommendation were updated to stop referencing "App Alpha." This wasn't
optional: publishing the new content with the old Case Index would have
left the in-page navigation pointing at anchors that no longer exist.
`presupuestador-wordpress-publish.md` steps 3 and 9 were updated for the
same reason (see §5).

## 4. Where the new flows are

All four use the real `estavillo/case-flow` (v1) or `estavillo/case-flow-v2`
Gutenberg block — the same component built for Trazur (`docs/CASE-FLOW.md`).
No new component, no hand-drawn SVG, no content copied from Trazur — only
the block's data model was reused, with entirely new nodes/edges.

| # | Section | Block | Why v1 vs v2 |
|---|---|---|---|
| 1 | `#as-is` — "Antes de Restimator" / "Before Restimator" | `case-flow` (v1) | mostly linear; one loop (corrections → re-validate) shows the rework problem without needing vertex-anchored geometry |
| 2 | `#v1-product` — "Primera hipótesis: estimar antes de presupuestar" | `case-flow` (v1) | one decision, two short outcomes — v1's side/top connector entry is enough |
| 3 | `#system` — "De estimador a sistema operativo" | `case-flow-v2` | two real decision forks (internal approval, client acceptance) plus a loop (declined → re-quote) — this is what v2's vertex-anchored geometry and `columns:5` (same column count Trazur uses) is for |
| 4 | `#evolution` — "Una solución que fue creciendo con el problema" | `case-flow` (v1), `density:"compact"` | deliberately simple/editorial per the brief — no decisions, milestone shapes only |

**Status badges.** The block has no native per-node status-badge
attribute (checked `block.json` for both blocks — only `sectionLabel`,
`nodes`, `startLabel`, `endLabel`, `detailLabel`, `closeLabel`,
`stepLabel`, `aiLegend`, `density`, and v2's `columns`). Rather than
inventing new component code for this ticket, status is carried two ways,
both already supported by the existing schema:

1. A **"Estado" / "Status" detail row** — the first `detail` entry on
   every node in Flow 3 (and the relevant nodes in Flow 2) — states one of
   Implementado / En desarrollo / Diseñado-prototipo / Futuro exactly, with
   its evidence.
2. A **3-tier accent mapping** for a quick visual scan: `signal` (green) =
   implemented, no accent = in development, and the case is written so a
   glance plus "Ver detalle" is never required to get the real state.

A one-line legend paragraph sits directly above Flow 3 explaining this
mapping — this is the "resolverlo con una leyenda aparte" fallback the
brief allowed for, since no badge attribute exists to add cleanly.

## 5. Technical decision: file format changed

**This was necessary, not a stylistic choice.** Both files were previously
a single "Custom HTML" block using the `.es-case-*` CSS classes directly —
that's how every earlier Presupuestador draft worked. A Custom HTML block
renders its contents as opaque, unparsed HTML; a `<!-- wp:estavillo/case-flow
{...} /-->` block comment pasted inside one would render as an invisible
HTML comment, not an actual interactive block. There's no way to embed a
real Case Flow diagram inside a Custom HTML block.

So both files are now a real sequence of Gutenberg blocks — the same
format already used for the Trazur case and for
`workshop-quoting-system-gutenberg-en.html` — pasted via the block
editor's **Code editor** (Options menu → Code editor), not a Custom HTML
block. The visual output for every non-flow section is identical: the
blocks (`estavillo/case-section`, `case-ladder`, `case-figure`,
`case-stats`, `case-status`, `case-details`, plus core paragraph/list)
render the exact same `.es-case-*` markup the hand-written HTML used to
produce directly — verified by parsing both files with the real WordPress
parser (`@wordpress/block-serialization-default-parser`, the same package
Gutenberg itself uses), which round-trips both files with **zero invalid
or stray-HTML blocks**. Both content files' own instructional headers
(note 7) explain this to whoever publishes them next.

Two blocks used in the old pattern-file transcription of this case
(`estavillo/case-taxonomy`, for the pricing-model variable grid, and
`estavillo/case-decisions`, for the old AI-guardrails framing) are no
longer used — not because they broke, but because the sections that used
them were substantively rewritten (the taxonomy's content is now a short
stats block under `#hypothesis`; the AI section was corrected and folded
into `#status`, see §2).

## 6. Relationship to the other Presupuestador files

- **`presupuestador-case-study.html`** and **`presupuestador-en.php`**
  (the Gutenberg pattern) were already legacy before this update — see
  `docs/handoff/PRESUPUESTADOR-CONTENT-MAP.md`. Not touched.
- **`estavillo-portfolio-core/patterns/presupuestador-es.php`** — this
  pattern was a block-for-block transcription of the *previous* version of
  `presupuestador-case-study-es.html`. It's now out of sync with the new
  content (still has the old 13-anchor "App Alpha" narrative) and was
  **not** regenerated as part of this ticket — the brief's deliverables
  were the two content files, the CSS (none was needed, see §7) and this
  notes file. If ES publishing should go through the Pattern inserter
  again (as `docs/handoff/PRESUPUESTADOR-CONTENT-MAP.md` recommends), that
  pattern file needs a follow-up regeneration from the new
  `presupuestador-case-study-es.html`.
- **`workshop-quoting-system-fields-en.md` /
  `-gutenberg-en.html`** — a separate, earlier ticket's EN-only rewrite
  ("V1 deployed, V2 is future exploration only"). This update's `#status`
  and Flow 3 go further than that file did, because the 2026-09-02 image
  curation (see §8) shows V2 already has real implemented pieces
  (dashboard, quotations, production) that didn't exist yet when that file
  was written. The two files now describe different snapshots in time of
  the same evolving project; this ticket did not reconcile or edit
  `workshop-quoting-system-*`, since it targeted
  `presupuestador-case-study-{es,en}.html` specifically, per the brief.
- **`docs/handoff/PRESUPUESTADOR-CONTENT-MAP.md`** (written in an earlier
  session) now has a stale "current state" description for these two
  files — its narrative summary still says "App Alpha, en curso." Not
  updated in this ticket (out of its stated scope); flagging it here so
  it isn't mistaken for still-accurate.
- **`presupuestador-assets-plan.md`** — still broadly valid as a
  general aspect-ratio/crop/caption reference, but its asset list predates
  the 2026-09-02 image curation (§8), which is now the more complete and
  more current source for what actually exists. Not edited.

## 7. CSS / components

**No new CSS was written.** Every block used already ships its
presentation via `estavillo-child/assets/css/case-study.css`,
`case-flow.css` and `case-flow-v2.css` — reusing the existing Trazur/
Case Study visual system exactly as instructed. Nothing in
`estavillo-child` or `estavillo-portfolio-core` was modified for this
ticket.

## 8. Image placeholders — what's pending and why

Every image slot in both files is an `estavillo/case-figure` block with a
`{asset: …}` placeholder — **no real image was embedded**, even where a
safe (non-`ANON_REQUIRED`) candidate exists in the attached
`PORTFOLIO_FINAL_SELECTION` curation. Two reasons, both procedural rather
than about any single image:

1. **`ANON_REQUIRED` pieces cannot be treated as publishable yet** — 20 of
   the 42 curated images carry that flag, and the brief was explicit that
   this pass must not anonymize them. Embedding an image regardless of its
   anonymization status would blur that line.
2. **No content file in this repo embeds a real final image URL before
   publish**, `ANON_REQUIRED` or not — every image (Trazur included) stays
   a placeholder until a human uploads the real file to the WordPress
   Media Library and points the block at it from the editor. There's no
   pre-upload URL to set from a static repo file. Following that same
   convention here keeps this update consistent with how every other case
   in this repo currently works.

What the curation *did* inform: which placeholder goes in which section,
and the caption text, which now names the real candidate image and its
anonymization status — so publishing is a matter of anonymizing/uploading
and pointing the block at the file, not re-deciding where each image goes.

| Placeholder | Section | Real candidate | Anonymization |
|---|---|---|---|
| `as-is-context` | `#context` | `HIST-0030` — diagnóstico histórico | Not required — pending authorization to publish aggregate metrics |
| `manual-cost-sheet` | `#problem` | `HIST-0041` — planilla de costos manual | **Required** |
| `google-sheets-mvp` | `#mvp` | `V1-0001` — MVP en planilla | **Required** |
| `v1-interface` | `#v1-product` | `V1-0003` — dashboard V1 implementado | **Required** |
| `v1-mobile` | `#v1-product` | `V1-0007` — V1 mobile implementada | **Required** |
| `mid-fi` | `#v2-design` | `V2CD-0001` — editor V2 diseñado | **Required** |
| `design-system` | `#v2-design` | `HIST-0395`, `HIST-0400`, `HIST-0451` | Not required |
| `production-flow` | `#production` | `V2VI-0016` — producción V2 implementada | **Required** |
| `results-evidence` | `#status` | `HIST-0323`, `HIST-0456`, `HIST-0459` | Not required |

Not used from the brief's original placeholder list: `low-fi-wireframes`
and `v2-prototype`/`v2-implemented` as separate named slots — their
closest curated equivalents (`V1-0014`/`V1-0016` wireframes, `V2CD-0021`
tablero, `V2CD-0012` lector de planos, `V2CD-0017` compras) are real, but
didn't have a section in the final 15-anchor structure narrow enough to
place them without crowding it — see `FINAL_SELECTION_REPORT.md`'s own
"Visuales importantes que faltan producir" for the currently-missing
critical shots (a same-task V1→V2 comparison, a full implemented
end-to-end flow capture, and post-implementation results), none of which
exist yet regardless of this update.

Featured image and hero layout recommendation in
`presupuestador-case-study-fields.md` were updated to point at `V1-0003`
instead of the old "App Alpha dashboard" reference.

## 9. Validation performed

- **Real WordPress block parser round-trip** (`@wordpress/block-serialization-default-parser`)
  on both files: 66 real blocks each, **0 stray/invalid HTML** in either
  file.
- **JSON attribute validity**: every block's JSON attributes blob parses
  as valid JSON (35 JSON-bearing blocks per file).
- **Block balance**: `case-section` (15/15), `case-details` (4/4),
  `paragraph` (22/22), `list` (2/2), `list-item` (7/7) — opens match
  closes in both files.
- **Anchor integrity**: 15 unique anchors, identical set in ES and EN,
  matching the new Case Index in `presupuestador-case-study-fields.md`
  exactly.
- **Flow graph integrity**: all 4 flows in both languages checked for
  dangling edges (edges pointing at a non-existent node id) and duplicate
  node ids — **none found** in any of the 8 flow instances (4 flows × 2
  languages).
- **No literal `-->` inside any block's JSON attributes** (would
  prematurely terminate the HTML comment and corrupt the rest of the
  file) — checked, none found.
- Not verified: rendering inside a real WordPress editor/frontend (no
  WordPress instance runs in this environment — same limitation
  `docs/CASE-FLOW.md` §9 already documents for the original Trazur build).
  A human pass in the real editor is still needed before publishing,
  exactly as that doc recommends.

## 10. Honesty checklist against the brief

- No invented users, metrics, time savings, percentages, cost reduction,
  or adoption numbers anywhere in the new content.
- No prototype or concept presented as implemented — every V2 flow node
  and every new section states its real status, sourced from the
  2026-09-02 `FINAL_SELECTION_REPORT.md` curation (REAL / IMPLEMENTED /
  IMPLEMENTED — ACTIVE DEVELOPMENT / PROTOTYPE / CONCEPT), not from
  assumption.
- The one factual correction made *to existing content* (removing the
  in-product AI claims, §2) is disclosed explicitly in `#status` and in
  this document, not silently dropped.
- All `[DATO PENDIENTE DE VALIDACIÓN]` / `[DATA PENDING VALIDATION]`
  placeholders from the previous draft are preserved; two more were added
  where the new content makes a claim that would otherwise need one.
