# Presupuestador — Case Study field sheet (ES + EN)

Exact recommended values for every native/meta field on the Case Study
post, for both language versions. Pair with:

- `presupuestador-case-study-es.html` (paste into the Spanish post's Custom HTML block)
- `presupuestador-case-study-en.html` (paste into the English post's Custom HTML block)
- `presupuestador-wordpress-publish.md` (step-by-step publishing order)

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
| **Eyebrow / category** | Product + System Design · En curso |
| **Excerpt** | Un sistema de apoyo a la decisión para presupuestar en un taller de fabricación metálica — vuelve explícito el criterio de precios de una sola persona y lo convierte en una herramienta consistente que puede usar todo el equipo. |
| **Tags (Case Tags)** | Product Design, Systems Design, Applied AI |
| **Status / label** | En curso |
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

```
Resumen|#overview
Contexto|#context
Problema|#problem
Descubrimiento|#discovery
Sistema|#system
MVP|#mvp
Testing|#testing
App Alpha|#app-alpha
IA|#ai
Resultados|#results
Limitaciones|#limitations
Aprendizajes|#learnings
Próximos pasos|#next
```

---

## English post

| Field | Value |
|---|---|
| **Post title** | Presupuestador |
| **Slug** | `presupuestador` (if the Spanish post already claimed this slug, WordPress/Polylang will suffix the English one automatically — e.g. `presupuestador-2` — that's expected and fine, the visible title stays "Presupuestador") |
| **Eyebrow / category** | Product + System Design · In progress |
| **Excerpt** | A decision-support system for quoting in a metal fabrication workshop — makes one person's pricing judgment explicit and turns it into a consistent tool the whole team can use. |
| **Tags (Case Tags)** | Product Design, Systems Design, Applied AI (same tags as the Spanish post — Case Tags are language-neutral by design in this theme, see `EDITABILITY-PLAN.md` → "Polylang") |
| **Status / label** | In progress |
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

```
Overview|#overview
Context|#context
Problem|#problem
Discovery|#discovery
System|#system
MVP|#mvp
Testing|#testing
App Alpha|#app-alpha
AI|#ai
Results|#results
Limitations|#limitations
Learnings|#learnings
Next steps|#next
```

---

## Featured image and hero layout recommendation

- **Which image to use as featured image:** none exists yet (see
  `presupuestador-assets-plan.md` for the full asset inventory). Once
  available, the best candidate is either **the App Alpha dashboard
  screenshot** (shows the product, reads well as a portrait/tall crop) or
  **the system architecture diagram** redrawn as a clean final asset — not
  a workshop photo, since the case is about the decision system, not the
  physical workshop.
- **Recommended hero layout:** **`split-right`** (the default —
  "Split — image right"). It's the safest choice for a portrait-oriented
  product screenshot (the App Alpha dashboard) and needs no extra
  configuration.
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
