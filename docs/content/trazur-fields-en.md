# Trazur Cursos (AI + UX Research) — English case field sheet

Companion to `trazur-gutenberg-en.html`. That file is the body content
(paste into the block editor's Code editor). This file is every native/
meta field on the Case Study post, plus the publishing order, image
checklist, and — most importantly — the claims and wording choices that
still need the project owner's sign-off before this goes live.

This is an **editorial adaptation** of the approved Spanish case
(`trazur-gutenberg-es.html` / `trazur-fields-es.md`), not a literal
translation — same information, same structure, same 17 sections, same
evidence, rewritten in natural English for an international Product
Design audience. If a Spanish equivalent is ever linked as a Polylang
translation of this post, the two post titles are allowed to differ by
language (Polylang links by translation relationship, not by matching
titles).

---

## Native fields

| Field | Value |
|---|---|
| **Post title** | Trazur Cursos *(recommended, matching the Spanish version's title choice — see that field sheet's "On the title" note)* |
| **Slug** | `trazur-cursos-en` (or however this site's Polylang URL convention distinguishes translations — confirm against how the Spanish `trazur-cursos` slug is set up) |
| **Eyebrow / category** | UX Research · Applied AI |
| **Excerpt** | Redesigning an e-learning platform for livestock traceability training in rural Uruguay, combining traditional UX research with AI-assisted analysis under human review. |
| **Case Tags** | UX Research, Product Design, Applied AI |
| **Status / label** | Redesign proposal · Validation pending |
| **Role** | UX Researcher & UX/UI Designer (AI focus) |
| **Tools** | Balsamiq, Relume, WordPress, Google Analytics, Microsoft Clarity, ChatGPT, Gemini, Synthetic Users, LLaMA |
| **Period** | 2025 (6 months) |
| **Source / context line** (used only if this case is ever marked "Feature this case on Home") | A thesis project built with Renzo Morandi, under the guidance of Alejandra Capocasale. |
| **Placeholder label** (shown only if no featured image is set) | trazur-cursos |
| **Case link URL** | Leave empty — no confirmed live URL exists for this project. |
| **Show on Home → Selected Work** | Leave unchecked until a real featured image exists and the flagged claims below are reviewed. |
| **Feature this case on Home** | Leave unchecked — same reason, and this is the most visible slot on the site. |
| **Order** (native "Order" field) | 0, or the lowest available value. |
| **Case index** | See exact block below. |

---

## Case Index — paste into the "Case index" field

```
Overview|#overview
Context|#context
The Problem|#problem
Goals & Expected KPIs|#goals
Method|#method
AI-Assisted Analysis|#ai-analysis
Key Insights|#insights
Persona|#persona
Journey Map|#journey
From Insights to Decisions|#decisions
Prototyping & Fidelity|#prototyping
Proposed Solution|#proposal
Expected Impact|#expected-impact
Limitations|#limitations
Next Steps|#next-steps
Key Learnings|#learnings
Project Poster|#poster
```

These 17 lines match, one to one and in the same order, the 17 `anchor`
attributes of the `estavillo/case-section` blocks in
`trazur-gutenberg-en.html` — confirmed by automated validation (see the
validation report).

Note that the anchors themselves are English slugs, not transliterations
of the Spanish ones (e.g. `#insights` rather than `#hallazgos`/
`#findings`) — see "Terminology decisions" below for why a couple of
section labels were deliberately not literal translations.

---

## Hero layout recommendation

Same situation as the Spanish version — no real image exists yet for
this case.

- **Best featured-image candidate once one exists:** the high-fidelity
  completion mockup (`{asset: trazur-mockup-alta-fidelidad-confirmacion}`,
  section `#prototyping`) — the same recommendation as the Spanish
  field sheet, since it's the same underlying image.
- **Recommended hero layout:** `split-right` (the theme default).
- **If the featured image ends up wide instead of portrait:** switch to
  `stacked`. See `estavillo-child/README.md` → "Hero layout options".

---

## Image checklist

**Deliberately identical placeholder labels to the Spanish version** —
these are the same underlying images, shared across both language
posts once produced, not duplicated per language. Nothing new is
pending here beyond what `trazur-fields-es.md` already lists.

| # | Placeholder (`placeholderLabel`) | Section | Status | Confidentiality note |
|---|---|---|---|---|
| 1 | `trazur-contexto-rural-ia` | `#context` | Pending | None — AI-generated art, no real data to redact. |
| 2 | `trazur-estrategia-metodologica` | `#method` | Pending | None. |
| 3 | `trazur-persona-juan-pablo` | `#persona` | Pending | Use a generic/stock photo representing the archetype — not a real, identifiable person's photo without consent. "Juan Pablo" is an archetype persona, not a named real producer. |
| 4 | `trazur-journey-map` | `#journey` | Pending | None, unless the original diagram includes a real name or figure — redact if so. |
| 5 | `trazur-wireframe-baja-fidelidad-inscripcion` | `#prototyping` | Pending | None — wireframe, no real data. |
| 6 | `trazur-wireframe-media-fidelidad-cursado` | `#prototyping` | Pending | None. |
| 7 | `trazur-mockup-alta-fidelidad-confirmacion` | `#prototyping` | Pending | Use seeded/example data (name, progress %), not a real learner's data. Same image proposed for the hero — see note below. |
| ~~8~~ | ~~`trazur-flujo-ideal`~~ | `#proposal` | **No image needed anymore** — the ideal flow is now the interactive `estavillo/case-flow` block, editable in Gutenberg (see `docs/CASE-FLOW.md`). | — |
| 9 | *(native featured image, not in the body)* | Hero | Pending | Same as #7 — same source photo reused for the hero, as in the Spanish version. |
| — | Poster (`trazur-poster-tdg-2025.pdf`) | `#poster` (button) | Pending | Review for sensitive internal data before uploading. If the poster itself is only in Spanish, decide whether an English case should link to it as-is or need its own version — flagged below. |

---

## Terminology decisions (deliberately not literal translations)

Called out explicitly per your request — these are the wording choices
where a more natural Product Design term was used instead of a direct
translation from the Spanish:

- **"Hallazgos clave" → "Key Insights"**, not "Key Findings." "Findings"
  is the more literal match, but "Insights" reads more natural in an
  international portfolio and matches the Research → Insights → Design
  Decisions → Proposed Solution → Expected Impact arc from your brief.
  Applied consistently: the section anchor is `#insights`, the
  `case-decisions` block's task label reads "Insight" (was "Hallazgo"/
  "Finding"), and every cross-reference ("see Key Insights") follows
  the same word.
- **"De los hallazgos a las decisiones" → "From Insights to Decisions"**
  — same reasoning, kept consistent with the point above.
- **"Propuesta de experiencia" → "Proposed Solution"**, not "Proposed
  Experience." Matches the exact term used in your brief's quality-check
  narrative arc, and reads more standard for a portfolio case.
- **"Trabajo de Grado" → "thesis project."** The most internationally
  recognizable equivalent for a final undergraduate design project —
  used consistently in the persona caption, journey map caption, flow
  diagram caption, and the poster section, rather than "Degree Work" or
  "Final Project."
- **"Metas del rediseño" → "Redesign targets"** (in the Goals & KPIs
  stats) vs. **"Objetivos" → "Goals"** (the qualitative objectives
  list) — two different English words for two different Spanish
  concepts that both loosely translate to "objectives," kept distinct
  on purpose so a reader doesn't conflate the qualitative goals with
  the quantitative targets.
- **Persona quote translated, not left in Spanish.** "Me gustaría que
  me llevara de la mano, porque en estas cosas me pierdo fácil" became
  "I wish someone would walk me through it — this kind of thing, I get
  lost easily." This is a research-synthesis quote representing the
  archetype (the persona itself is an archetype, not a literal
  transcribed interview — see the Spanish field sheet's own caveat on
  this), so it was translated like the rest of the persona content
  rather than kept in the original language.
- **`browserLabel` values translated** (e.g. "trazur.uy — inscripción"
  → "trazur.uy — signup") since they're in-image display text a reader
  would expect to read in the case's own language. **`placeholderLabel`
  values were NOT translated** — those are internal asset identifiers,
  kept identical to the Spanish version on purpose (see the image
  checklist above).

---

## Sentences that may need your review

A few places allowed more than one reasonable phrasing — flagging them
so you can confirm the choice made, or swap in your own wording:

1. **Persona quote** (see "Terminology decisions" above) — confirm the
   English phrasing captures the tone you intended, since this is the
   one place where translation involves the most interpretive judgment
   in the whole document.
2. **"Redesign proposal · Validation pending"** (Status/label field) —
   my own phrasing for the native Status field; not present verbatim in
   either the PDF or the approved Spanish body content, since the
   Spanish integration didn't fix an exact Status field value either.
   Confirm or adjust.
3. **Slug suggestion `trazur-cursos-en`** — a placeholder guess at your
   Polylang URL convention. If your site's existing Polylang setup uses
   a different pattern for language variants, use that instead.
4. **Poster PDF language** (see image checklist, item "Poster") — the
   English case currently points at the same `trazur-poster-tdg-2025.pdf`
   filename as the Spanish version. Confirm whether the English case
   should link to the same (Spanish-language) poster, or whether an
   English poster should be produced separately.

All other claims (KPI framing, the AI-limitations synthesis in
AI-Assisted Analysis, the missing native "Team" field, the empty Case
link URL, the duplicated hero/mockup image, the recommended title/slug)
carry over unchanged from `trazur-fields-es.md` — review that list too
if you haven't already signed off on it.

---

## Publishing instructions

1. In wp-admin: **Case Studies → Add New** (or open the existing draft
   for this case if one exists — don't create a duplicate). If this is
   meant to be the Polylang translation of the Spanish post, use that
   post's **"+ Add translation"** flow instead of creating an unrelated
   new post.
2. Fill every field from the "Native fields" table above, in the "Case
   details" meta box and the sidebar (Excerpt, Case Tags).
3. Open the block editor → **Options (⋮) → Code editor**. Paste
   everything below the instructional comment in
   `trazur-gutenberg-en.html`. Switch back to **Visual editor** and
   confirm all 17 sections appear as real blocks (Case Section,
   Columns, Case Figure, Case Stats, Case Decisions, Case Quote, Case
   Details, Case Timeline, Case Ladder, Table, Group, Buttons), with no
   "unexpected or invalid content" warning.
4. Paste the **Case Index** block above into the "Case index" field.
5. Leave the featured image empty until a real one exists (see the
   image checklist).
6. Leave **Show on Home → Selected Work** and **Feature this case on
   Home** unchecked, per the native fields table.
7. Publish (or update), then check the front end:
   - The sticky Case Index shows all 17 entries in order, and each link
     scrolls to the matching section.
   - `#context` and `#persona` show a genuine two-column layout on
     desktop and stack cleanly on mobile.
   - The Case Decisions cards (`#decisions`), the Case Stats grid
     (`#goals`), the timeline (`#prototyping`), and the Case Details
     accordions (`#next-steps`) all render correctly; the accordions
     open and close.
   - The tables in `#ai-analysis` and `#insights` stay fully readable
     on mobile (horizontal scroll inside the table, no crushed text, no
     page-level overflow).
   - If linked as a Polylang translation of the Spanish post, confirm
     the language switcher moves between the two correctly.
8. Once you've reviewed "Terminology decisions," "Sentences that may
   need your review," and the carried-over claims list above, come back
   to the Home/Selected Work fields once a real featured image exists.

This ticket covers only the English integration of the Trazur case.
