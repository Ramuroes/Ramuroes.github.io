# Workshop Quoting System (Presupuestador) — English case field sheet

Companion to `workshop-quoting-system-gutenberg-en.html`. That file is the
body content (paste into the block editor's Code editor). This file is
every native/meta field on the Case Study post, plus the publishing
order, image checklist, and — most importantly — the claims that still
need the project owner's sign-off before this goes live.

This is a **content-production update**, not a new case: the theme,
plugin, and block infrastructure are unchanged. It supersedes the "App
Alpha" narrative in the older `presupuestador-case-study-en.html` /
`presupuestador-en.php` pattern draft. It does not touch the Spanish
version — that stays a separate, later task.

---

## Native fields

| Field | Value |
|---|---|
| **Post title** | Workshop Quoting System |
| **Slug** | `workshop-quoting-system` (recommended — see "On the slug/title change" below) |
| **Eyebrow / category** | Product Design · Systems Design · Applied AI |
| **Excerpt** | A production-ready quoting system that transforms tacit workshop pricing knowledge into an explicit, reusable decision framework — supporting faster initial estimates while preserving expert judgment for complex work. |
| **Case Tags** | Product Design, Systems Design, Applied AI |
| **Status / label** | V1 deployed · Calibration in progress |
| **Role** | Lead Product Designer |
| **Tools** | Google Sheets, Apps Script, Claude Code, ChatGPT, Figma, Vercel, GitHub, WordPress |
| **Period** | 2025–2026 |
| **Source / context line** (used only if this case is ever marked "Feature this case on Home") | Designed and implemented at Guzmán Villalba, a custom metal fabrication workshop in Montevideo, Uruguay. |
| **Placeholder label** (shown only if no featured image is set) | workshop-quoting-system |
| **Case link URL** | Leave empty — the Case Study's own permalink is used. **Do not** paste the live Vercel URL yet (see "Claims requiring confirmation" below). |
| **Show on Home → Selected Work** | Leave unchecked until a real, sanitized featured image has been added. Turning this on with no image (or a placeholder) puts an unfinished-looking card in the most visible part of the site. |
| **Feature this case on Home** | Leave unchecked until both the English copy above is approved *and* a real featured image exists — this is the single most visible slot on Home. |
| **Order** (native WordPress "Order" field) | 0, or the lowest available value, so it appears first/as the wide card once "Show on Home" is turned on. |
| **Case index** | See exact block below. |

### On the slug/title change

The existing Spanish case (still titled "Presupuestador" internally) may
already own the `presupuestador` slug from earlier drafts. This English
post is titled **Workshop Quoting System** per this ticket's positioning
— if/when a Spanish equivalent is produced and linked as a Polylang
translation, the two post titles are allowed to differ by language (that's
normal — Polylang links by translation relationship, not by matching
titles). Nothing about the slug affects the body content or blocks.

---

## Case Index — paste into the "Case index" field

```
Overview|#overview
Context|#context
Problem|#problem
Discovery|#discovery
System|#system
MVP|#mvp
V1 Product|#v1-product
AI & Judgment|#ai
Current Status|#status
Limitations|#limitations
Learnings|#learnings
Next Steps|#next
```

Every anchor above matches exactly one `estavillo/case-section` block's
`anchor` attribute in `workshop-quoting-system-gutenberg-en.html` — verified
during validation (see the commit message / completion report for the
check that confirmed this 1:1).

---

## Hero layout recommendation

No real featured image exists yet — same situation as documented in
`presupuestador-assets-plan.md`, which still applies (nothing in it is
outdated by this content update; the images it lists as pending are
still pending).

- **Best featured-image candidate once one exists:** the **V1 product
  screenshot** (`{asset: v1-product-screenshot}` in the new content,
  section `#v1-product`) — it shows the actual shipped product, and
  reads well as a portrait or tall crop. The pricing-model diagram
  (`{asset: architecture-diagram}`, section `#system`) is the second-best
  candidate if the product screenshot isn't ready first.
- **Recommended hero layout:** `split-right` (the theme default —
  "Split — image right"). Safe for a portrait product screenshot, no
  extra configuration needed.
- **If the featured image ends up wide instead of portrait** (e.g. the
  Sheets MVP screenshot, which reads better landscape): switch "Hero
  layout" to `stacked` ("Stacked — text first, image below, full
  width"). If the excerpt above runs longer once edited, `compact` is
  the other safe fallback. See `estavillo-child/README.md` → "Hero
  layout options" for the full comparison.

---

## Image checklist

Same underlying inventory as `presupuestador-assets-plan.md` (nothing in
that file needs to change — the aspect ratios, crop notes, and
confidentiality/redaction guidance there are still accurate), retargeted
to the new section anchors and placeholder labels actually used in
`workshop-quoting-system-gutenberg-en.html`:

| Placeholder (`placeholderLabel` / `tag`) | Section | Status | Confidentiality note |
|---|---|---|---|
| `discovery-notes` | `#discovery` | Pending | If a real photo of shadowing-session notes, redact any real client name/price. |
| `architecture-diagram` | `#system` | Pending | None — original diagram, no redaction needed. |
| `mvp-sheets-screenshot` | `#mvp` | Pending | **High priority to redact** — blank out real formulas, real cost figures, real client names before use. Prefer seeded/example data. |
| `v1-product-screenshot` | `#v1-product` | Pending | Screenshot from seeded/demo data, not a live client's in-progress quote. Also the top featured-image candidate — see above. |

No image exists in the repository for this case today — every entry
above is genuinely pending, not "already produced but not linked."

---

## Claims requiring your confirmation before publishing

These are called out explicitly, in addition to being marked inline in
the HTML content itself. Please review each one:

1. **The "more than ten quotations pending" range** (`#problem`). Written
   as an observed operational range, not a measured KPI, per your
   instructions — confirm this framing is accurate, or adjust the
   wording/number.
2. **The ~70% non-acceptance estimate** (`#problem`). Included only
   because you explicitly authorized it as a clearly labeled internal
   working estimate — it is wrapped in an inline `[Internal working
   estimate, not yet measured — confirm before publishing: …]` note
   directly in the HTML. This is the single most sensitive figure in the
   whole document. Please either confirm it's an accurate reflection of
   the team's informal sense, replace it with a better number, or delete
   the paragraph outright before this goes live — don't publish it
   without a decision either way.
3. **The three "current AI use" descriptions** (`#ai`): structuring
   documented criteria, development support for the V1's code, and
   drafting/organizing documentation. These were inferred from the Tools
   field ("Claude Code, ChatGPT") and the general shape of the project —
   **not verified against a specific record of how AI was actually
   used**. Please confirm these three descriptions are accurate, or
   correct them. This section was rewritten most heavily from the
   earlier draft (which described in-product AI features — reading
   photos, suggesting ranges, flagging anomalies — that this update
   deliberately removed as unverified; see "What changed" below).
4. **Case link URL** — deliberately left empty per your instruction not
   to use the live Vercel URL yet. Confirm when/if it should be added.
5. **"Tested and confirmed working on desktop, tablet, and mobile"**
   (`#v1-product`) — stated directly per your brief ("It works on
   desktop, tablet and mobile... Functional testing has been
   completed"). Confirm this is safe to state as-is.
6. **Period: 2025–2026** — carried forward from the recommended value in
   this ticket; confirm it matches the real project timeline.

## What changed from the earlier "App Alpha" draft

- Every "App Alpha" / "Alpha app" reference replaced with the real V1
  deployed on Vercel; a dedicated `#v1-product` section replaces the old
  `#app-alpha` section.
- The old `#testing` section was folded into `#mvp` (the compare-and-
  adjust loop is now framed as part of the MVP's iteration story, and
  explicitly continues into V1 calibration) — the Case Index has one
  fewer entry than the old 13-anchor version (12 now) as a result.
- `#results` renamed to `#status` ("Current Status") and rewritten to
  state only what's true today (V1 deployed, responsive, core flow
  functional) versus what's still in progress (calibration,
  documentation, minor fixes) — no adoption or response-time numbers are
  claimed.
- `#ai` rewritten from three in-product AI features (photo reading,
  range suggestion, anomaly flagging as shipped functionality) to three
  actual development/production uses of AI tools, with anomaly
  flagging/smarter suggestions moved to an explicitly labeled "future
  exploration, not built" paragraph. **This is the most significant
  editorial judgment call in this update — see item 3 above.**
- `#limitations` no longer claims "no mobile-first flow exists" (now
  false, since V1 is confirmed responsive) — replaced with calibration-
  and-documentation-focused limitations plus the explicit "no validated
  business-impact metrics yet" line your brief required.
- `#learnings` keeps the original two paragraphs (still accurate, no
  metrics) and adds a new paragraph on using AI as a reviewable
  production/reasoning partner and on balancing speed against
  operational reliability, per your brief.
- `#next` rewritten from three items to four: finish V1 calibration,
  improve documentation/measurement, track estimated-vs-actual results,
  and explore V2 — explicitly labeled as investigation only, not built.
- No `estavillo/case-quote` block is used — same editorial decision as
  the original draft (no verified, attributable quote exists anywhere in
  this repository).
- Two `core/columns` compositions added (`#discovery` at 60/40,
  `#mvp` at 50/50) where a text/image pairing genuinely helped the
  narrative, per your layout rules — no decorative alternating layout
  was added elsewhere.

---

## Publishing instructions

1. In wp-admin: **Case Studies → Add New** (or open the existing draft
   for this case if one exists — don't create a duplicate).
2. Fill every field from the "Native fields" table above, in the "Case
   details" meta box and the sidebar (Excerpt, Case Tags).
3. Open the block editor's **Options (⋮) menu → Code editor**. Paste
   everything below the instructional comment in
   `workshop-quoting-system-gutenberg-en.html`. Switch back to **Visual
   editor** and confirm all 12 sections appear as real blocks (Case
   Section, Columns, Case Figure, Case Taxonomy, Case Timeline, Case
   Decisions, Case Status, Case Details) with no "unexpected or invalid
   content" warning.
4. Paste the **Case Index** block above into the "Case index" field.
5. Leave the Featured Image empty until a real image exists (see the
   image checklist) — the theme's placeholder frame renders cleanly with
   nothing set.
6. Leave **Show on Home → Selected Work** and **Feature this case on
   Home** unchecked, per the native fields table.
7. Publish (or update), then check the front end:
   - The sticky Case Index shows all 12 entries in order, and each link
     scrolls to the matching section.
   - `#discovery` and `#mvp` show a genuine two-column layout on desktop
     and stack cleanly on mobile.
   - The Case Decisions cards (`#ai`), Case Status columns (`#status`),
     and Case Details accordions (`#next`) all render and, for the
     accordions, open/close correctly.
8. Once you've reviewed the "Claims requiring your confirmation" section
   above and are satisfied with the copy, come back to the Home/Selected
   Work fields once a real featured image is ready.

This ticket does not cover the Spanish translation, or Trazur, Samic, or
French Bakery — those remain separate, later tasks.
