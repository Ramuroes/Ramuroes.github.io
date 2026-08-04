# Presupuestador — visual asset plan

**Status check first, so this doesn't get misread:** this repository
contains **zero real images for this case** — no screenshots, photos, or
diagrams. The only image anywhere in the repo is
`estavillo-child/screenshot.png`, the generic WordPress theme thumbnail,
unrelated to this project. Every asset below is genuinely pending — none
of them are being claimed as "already exists" anywhere else in this
sprint's other deliverables. This file is an inventory of what to shoot/
design next, not a status report on assets that exist.

For each asset: purpose, where it's used, the exact aspect ratio the
current CSS expects (so a cropped image doesn't fight the layout),
mobile/desktop crop notes, current status, confidentiality treatment, and
a suggested caption (matching the `.es-case-caption` pattern already used
in both HTML files).

---

## 1. Hero / featured image

- **Purpose:** the single image that represents the whole case — used as
  the native WordPress "Featured Image," shown in the case's hero (right
  or below the title, depending on the "Hero layout" field) and as the
  card thumbnail on Work/Home.
- **Section:** hero (outside `the_content()` — set via the native
  Featured Image field, not pasted into the HTML).
- **Aspect ratio:** depends on the chosen hero layout —
  `split-right`/`split-left` want **4:5** (portrait) on desktop, **4:3**
  on mobile; `compact` wants **16:10**; `stacked` wants **21:9** (wide,
  desktop) and falls back to the same 4:3 crop on mobile. See
  `presupuestador-case-study-fields.md` → "Featured image and hero layout
  recommendation" for which layout to pick based on what this image
  actually turns out to be.
- **Desktop/mobile crop:** whichever image is chosen, keep the visual
  focus centered — the theme crops to the ratios above via `object-fit:
  cover`, so anything near the edges of a portrait crop can get trimmed
  on mobile's shorter 4:3 frame.
- **Status:** **pending.** No candidate image exists yet.
- **Confidentiality:** if this ends up being a real product screenshot
  (App Alpha), redact or blur anything showing real customer names, real
  prices, or real job details before use — screenshot from a seeded/demo
  data state if possible, not a live customer's actual quote.
- **Suggested caption:** not applicable — the hero image doesn't use
  `.es-case-caption` (that's for in-body figures only).

## 2. Original manual estimating material

- **Purpose:** grounds the "before" state — shows the tacit, undocumented
  process this case set out to replace (a notebook, a scrap of paper with
  a handwritten calculation, a photo of the estimator at work — whatever
  actually documents the pre-system state).
- **Section:** `#context` or `#discovery` — currently the HTML's
  `#discovery` section has one `.es-case-figure` placeholder
  (`{asset: discovery-notes}`) that this asset is the natural fit for.
- **Aspect ratio:** **16:9** (matches
  `.es-case__body .es-case-figure .es-placeholder`'s current placeholder
  ratio).
- **Desktop/mobile crop:** `.es-case-cols` stacks to a single column
  under 767px, so the image goes full-width on mobile — avoid a crop
  that only makes sense as half-width.
- **Status:** **pending.** Not confirmed to exist — if no such artifact
  was ever kept, this placeholder may need to stay a placeholder
  permanently, or be replaced with a posed/recreated photo instead of
  claiming it's an original artifact.
- **Confidentiality:** if a real handwritten estimate exists, redact any
  real client name, phone number, or exact price before photographing/
  scanning it.
- **Suggested caption:** "Fig. 01 — Cómo se armaba un presupuesto antes
  del sistema." / "Fig. 01 — How a quote used to get built before the
  system."

## 3. Current-state workflow diagram

- **Purpose:** a simple diagram of the *old* process (client call → verbal
  estimate → informal follow-up → maybe a written quote days later) —
  makes the bottleneck visible at a glance instead of only described in
  prose.
- **Section:** `#discovery` or `#problem` (not currently placeheld in
  either HTML file — this is an optional enhancement, not a gap in the
  current draft; the section reads fine as text-only today).
- **Aspect ratio:** **16:9** if added as a standalone `.es-case-figure`,
  or **16:10** if framed inside a `.es-case-browser` chrome (treat it like
  a diagram "screenshot").
- **Desktop/mobile crop:** keep text inside the diagram large enough to
  stay legible at mobile width — avoid a diagram with more than ~5-6
  labeled steps if it needs to survive a phone-width crop.
- **Status:** **pending / optional.** Not required to publish — the case
  already explains this in prose.
- **Confidentiality:** none — this would be an original diagram, not a
  photo of real operational data.
- **Suggested caption:** "El flujo antes del sistema." / "The workflow
  before the system."

## 4. Target workflow diagram

- **Purpose:** the "after" counterpart to #3 — shows how a quote moves
  through the new system (input → pricing model → range/quote output).
- **Section:** `#system` — this is exactly what the existing
  `{asset: architecture-diagram}` placeholder in both HTML files is for.
- **Aspect ratio:** **16:10** (matches
  `.es-case__body .es-case-browser .es-placeholder`).
- **Desktop/mobile crop:** same legibility concern as #3 — this is the
  single most important diagram in the case (it's the one both language
  versions explicitly flag as pending with `[DIAGRAM PENDING]`/
  `[DIAGRAMA PENDIENTE]`), worth getting right before anything else on
  this list.
- **Status:** **pending.** Already flagged as pending directly in the
  case copy itself.
- **Confidentiality:** none — original diagram.
- **Suggested caption:** already has one in the HTML body copy (no
  separate `.es-case-caption` — the explanation is inline prose next to
  the figure).

## 5. Knowledge-extraction / framework artifact

- **Purpose:** evidence of the actual discovery method — e.g. a photo of
  a whiteboard session, a synthesized list of the pricing variables that
  came out of shadowing sessions, or research notes turned into a clean
  one-page framework.
- **Section:** `#discovery` (can share the same placeholder as #2, or if
  a second, distinct artifact exists, added as a second figure in that
  section — optional, not required).
- **Aspect ratio:** **16:9** as a `.es-case-figure`.
- **Desktop/mobile crop:** same as #2.
- **Status:** **pending.**
- **Confidentiality:** if this shows real notes from the actual
  estimator, confirm he's comfortable with his handwriting/notes being
  shown, even redacted.
- **Suggested caption:** "Síntesis del criterio de precios, a partir de
  varias sesiones de acompañamiento." / "Pricing criteria synthesized
  from several shadowing sessions."

## 6. Google Sheets MVP — inputs

- **Purpose:** shows the actual input structure of the Sheets model (the
  columns/fields an estimator fills in) — grounds the "MVP" section in a
  real interface, however simple.
- **Section:** `#mvp` — the existing `{asset: mvp-sheets-screenshot}`
  placeholder.
- **Aspect ratio:** **16:10** (`.es-case-browser` frame).
- **Desktop/mobile crop:** spreadsheets are wide by nature — pick a zoom
  level where column headers stay readable at mobile width, even if that
  means showing fewer columns than the full sheet.
- **Status:** **pending.**
- **Confidentiality:** **high priority to redact.** This is the one asset
  most likely to expose real pricing logic if screenshotted directly —
  blank out or replace real formulas, real material cost figures, and any
  real client names in the row data before using this as a public case
  study image. Consider using seeded/example row data instead of a live
  screenshot.
- **Suggested caption:** "presupuestador.xlsx — modelo de precios v1." /
  "presupuestador.xlsx — pricing model v1" (already used as the browser-
  chrome label in both HTML files; a caption below the image can add
  "vista de entrada" / "input view" to distinguish from asset #7).

## 7. Google Sheets MVP — results / output

- **Purpose:** the output side of the same spreadsheet — the calculated
  range/quote, and ideally the comparison column against the estimator's
  own number (this is the evidence for the `#testing` section).
- **Section:** `#testing` (not currently placeheld — the testing section
  is text/timeline-only today; this would be a natural addition once the
  asset exists, but isn't required to publish).
- **Aspect ratio:** **16:10** if framed as a `.es-case-browser` figure.
- **Desktop/mobile crop:** same column-legibility concern as #6.
- **Status:** **pending.**
- **Confidentiality:** same redaction priority as #6 — this is the
  asset most likely to contain real historical quote values. Redact
  actual dollar amounts or replace with representative example data.
- **Suggested caption:** "Comparación entre el modelo y el criterio del
  presupuestador, caso por caso." / "Model output compared against the
  estimator's own judgment, case by case."

## 8. Testing evidence

- **Purpose:** whatever documents the validation process described in
  `#testing` — could be the same asset as #7, or a separate log/summary
  view if one exists.
- **Section:** `#testing`.
- **Aspect ratio:** **16:10** if a screenshot, or **16:9** as a general
  figure.
- **Status:** **pending.** The `#testing` section in both HTML files
  currently has an explicit `[DATA PENDING VALIDATION]` note for any
  quantified accuracy/coverage figure — an asset alone doesn't resolve
  that, a real number or a clear qualitative summary would be needed too.
- **Confidentiality:** same as #6/#7 if it includes real quote data.
- **Suggested caption:** "Evidencia de testing: coincidencias y desvíos
  entre modelo y criterio experto." / "Testing evidence: matches and gaps
  between the model and expert judgment."

## 9. App Alpha dashboard

- **Purpose:** the clearest "product" shot in the whole case — shows the
  guided-input interface described in `#app-alpha`.
- **Section:** `#app-alpha` — the existing `{asset: app-alpha-screenshot}`
  placeholder. Also the top candidate for the **featured/hero image** (see
  item 1) if it reads well as a portrait crop.
- **Aspect ratio:** **16:10** in-body (`.es-case-browser`); **4:5** if
  also reused as the hero image under the default `split-right`/
  `split-left` layout.
- **Desktop/mobile crop:** if this doubles as the hero image, test both
  the 4:5 (desktop) and 4:3 (mobile) crops specifically — a dashboard
  screenshot with important UI near the top/bottom edges may lose content
  in the taller portrait crop.
- **Status:** **pending.**
- **Confidentiality:** screenshot from demo/seeded data, not a real
  client's live quote in progress.
- **Suggested caption:** "app.presupuestador — alpha." (already used as
  the browser-chrome label in both HTML files).

## 10. AI vs. human decision diagram

- **Purpose:** a visual companion to the `#ai` section's 3 decision cards
  (reading input / suggesting a range / flagging anomalies) — could show
  where each AI touchpoint sits in the flow and where it hands back to a
  person.
- **Section:** `#ai` (not currently placeheld — the section reads
  completely on its own via the 3 `.es-case-decisions` text cards; this
  is a nice-to-have enhancement, not a gap).
- **Aspect ratio:** **16:9** as a `.es-case-figure`, or **16:10** if
  framed as a browser-chrome diagram.
- **Status:** **pending / optional.**
- **Confidentiality:** none — original diagram.
- **Suggested caption:** "Dónde asiste la IA, dónde decide una persona."
  / "Where AI assists, where a person decides."

## 11. Architecture diagram

- **Purpose:** duplicate entry pointing to the same asset as #4 (the
  ticket's own list separates "target workflow" and "architecture
  diagram" as two line items, but in this case's structure they're the
  same figure — the one-model/two-surfaces diagram in `#system`). Not
  duplicating the work, just noting the mapping explicitly so nothing
  gets built twice.
- **Section:** `#system`.
- **Status:** **pending** — see #4.

## 12. Current status / roadmap visual

- **Purpose:** an optional visual summary of the `#results` and `#next`
  sections — e.g. a simple "today / next" two-column graphic echoing the
  `.es-case-status` grid already used in both HTML files.
- **Section:** `#results` or `#next` (not currently placeheld — both
  sections already communicate this via text/`.es-case-status`/
  `.es-case-details`; a visual is a nice-to-have, not required).
- **Aspect ratio:** **16:9** if added.
- **Status:** **pending / optional.**
- **Confidentiality:** none.
- **Suggested caption:** "Dónde está el sistema hoy, y hacia dónde va." /
  "Where the system stands today, and where it's headed."

---

## Priority order if assets are produced incrementally

1. **#4 / #11 — architecture diagram** (`#system`) — explicitly flagged
   pending in the case copy itself; most load-bearing for explaining what
   was actually built.
2. **#9 — App Alpha dashboard** (`#app-alpha`) — best featured-image
   candidate, and the most "product-y" proof of the work.
3. **#6 / #7 — Sheets MVP screenshots** (`#mvp` / `#testing`) — needs the
   most careful redaction, so budget extra time for that, not just the
   screenshot itself.
4. Everything else (#2, #3, #5, #8, #10, #12) — genuinely optional
   polish; the case reads completely without them.
