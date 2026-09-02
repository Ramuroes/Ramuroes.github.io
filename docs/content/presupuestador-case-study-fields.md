# Presupuestador — Case Study field sheet (ES + EN)

Exact recommended values for every native/meta field on the Case Study
post, for both language versions. Pair with:

- `presupuestador-case-study-es.html` (paste into the Spanish post's block
  editor **Code editor** — no longer a Custom HTML block, see that file's
  own instructional header, note 7)
- `presupuestador-case-study-en.html` (same, English post)
- `presupuestador-wordpress-publish.md` (step-by-step publishing order —
  its "paste into Custom HTML block" step is now superseded by the Code
  editor method described in the two content files themselves)
- `presupuestador-case-study-update-notes.md` (what changed in the
  2026-09-02 update — new narrative, 4 Case Flow diagrams, new anchors)

**Editorial note carried over from the case content itself:** the numbers
that used to appear in an earlier draft of this case (a specific quote
turnaround range, a specific count of people unable to estimate
independently) had no verifiable source anywhere in this repository, so
they are **not** reintroduced here either. Where a field below would
normally want a precise figure and none is confirmed, this sheet says so
explicitly rather than inventing one.

---

## Spanish post

| Field | Value |
|---|---|
| **Post title** | Presupuestador |
| **Slug** | `presupuestador` |
| **Eyebrow / category** | Product + System Design · En desarrollo activo |
| **Excerpt** | Un sistema de apoyo a la decisión para presupuestar en un taller de fabricación metálica — de una planilla artesanal a un estimador rápido implementado y un sistema de presupuesto y producción en desarrollo activo. |
| **Tags (Case Tags)** | Product Design, Systems Design, Applied AI |
| **Status / label** | V1 implementado · V2 en desarrollo |
| **Role** | Product Designer |
| **Tools** | Google Sheets, Apps Script, Figma, GPT-4 |
| **Period** | 2025– |
| **Source / context line** (used only by Featured Case, if this case is ever marked "Feature this case on Home") | Desarrollado e implementado en Guzmán Villalba — taller de fabricación metálica, Montevideo. |
| **Placeholder label** (shown only if no featured image is set) | presupuestador |
| **Hero layout** | Ver "Hero layout recommendation" más abajo — aplica igual para ambos idiomas. |
| **Show on Home → Selected Work** | Sí (checkbox activado) — recomendado una vez que el featured image real esté cargado; hasta entonces, dejarlo destildado para que Home siga mostrando el fallback de siempre en vez de una card con placeholder. |
| **Feature this case on Home** | No, todavía. Activar recién cuando exista al menos el featured image real — el Featured Case de Home es la sección más visible y no debería estrenar el caso con un placeholder de imagen. |
| **Order** (campo nativo "Order" de WordPress, controla la posición en Selected Work) | 0 (o el valor más bajo disponible, para que aparezca primero/como card ancha una vez activado) |
| **Case index** | Ver bloque completo más abajo. |

### Case index (Spanish) — paste into "Case index" field

Reemplaza el índice anterior (13 anchors) por completo — la actualización
2026-09-02 reordenó y renombró varias secciones; ver
`presupuestador-case-study-update-notes.md` para el detalle exacto.

```
Resumen|#overview
Contexto|#context
Problema|#problem
Cómo funcionaba antes|#as-is
Hipótesis y objetivo|#hypothesis
MVP|#mvp
Restimator rápido V1|#v1-product
Aprendizajes de V1|#learnings-v1
Evolución hacia V2|#evolution-to-v2
Diseño del producto V2|#v2-design
Sistema y arquitectura|#system
Evolución del producto|#evolution
Producción y operación|#production
Validación y estado actual|#status
Próximos pasos|#next
```

---

## English post

| Field | Value |
|---|---|
| **Post title** | Presupuestador |
| **Slug** | `presupuestador` (if the Spanish post already claimed this slug, WordPress/Polylang will suffix the English one automatically — e.g. `presupuestador-2` — that's expected and fine, the visible title stays "Presupuestador") |
| **Eyebrow / category** | Product + System Design · Active development |
| **Excerpt** | A decision-support system for quoting in a metal fabrication workshop — from an artisanal spreadsheet to an implemented rapid estimator and a quotation-and-production system in active development. |
| **Tags (Case Tags)** | Product Design, Systems Design, Applied AI (same tags as the Spanish post — Case Tags are language-neutral by design in this theme, see `EDITABILITY-PLAN.md` → "Polylang") |
| **Status / label** | V1 implemented · V2 in development |
| **Role** | Product Designer |
| **Tools** | Google Sheets, Apps Script, Figma, GPT-4 |
| **Period** | 2025– |
| **Source / context line** | Developed and implemented at Guzmán Villalba — metal fabrication workshop, Montevideo. |
| **Placeholder label** | presupuestador |
| **Hero layout** | Same recommendation as the Spanish post — see below. |
| **Show on Home → Selected Work** | Same logic as the Spanish post: only once the real featured image is set. Home Content today is single-language/global (see `EDITABILITY-PLAN.md`), so Home itself doesn't show two language variants at once — this flag only affects what this specific post's own single page and Work-page listing show. |
| **Feature this case on Home** | Same as the Spanish post: not yet. |
| **Order** | 0 (match the Spanish post's value) |
| **Case index** | Ver bloque completo más abajo. |

### Case index (English) — paste into "Case index" field

Replaces the earlier index (13 anchors) entirely — the 2026-09-02 update
reordered and renamed several sections; see
`presupuestador-case-study-update-notes.md` for the exact detail.

```
Overview|#overview
Context|#context
Problem|#problem
How it worked before|#as-is
Hypothesis and objective|#hypothesis
MVP|#mvp
Restimator rapid V1|#v1-product
Learnings from V1|#learnings-v1
Evolution toward V2|#evolution-to-v2
V2 product design|#v2-design
System and architecture|#system
Product evolution|#evolution
Production and operations|#production
Validation and current status|#status
Next steps|#next
```

---

## Featured image and hero layout recommendation

- **Which image to use as featured image:** none exists yet as a
  publishable asset (see `presupuestador-assets-plan.md` for the general
  asset inventory, and `presupuestador-case-study-update-notes.md` for
  the full mapping against the 2026-09-02 image curation). Once
  anonymized, the best candidate is **the Restimator V1 dashboard**
  (curation id `V1-0003` — shows the implemented product, reads well as a
  portrait/tall crop) — not the App Alpha concept referenced by earlier
  drafts of this sheet, which this update's content no longer uses.
- **Recommended hero layout:** **`split-right`** (the default —
  "Split — image right"). It's the safest choice for a portrait-oriented
  product screenshot (the V1 dashboard) and needs no extra configuration.
- **Alternative if the featured image changes:** if the eventual featured
  image is a **wide screenshot** (e.g. the Sheets MVP, which reads better
  landscape than portrait) instead of a portrait dashboard shot, switch
  the "Hero layout" field to **`stacked`** ("Stacked — text first, image
  below, full width") — that gives a wide image its own full-width frame
  instead of squeezing it into the narrower portrait column that
  `split-right`/`split-left` use. If the excerpt above ever runs
  noticeably longer than what's shown here, `compact` is the other safe
  fallback (shorter image frame, avoids an awkward gap next to a lot of
  text) — see `estavillo-child/README.md` → "Hero layout options" for the
  full comparison of all 4 variants.
