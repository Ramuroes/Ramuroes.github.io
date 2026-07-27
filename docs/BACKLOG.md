# Backlog — Estavillo Portfolio (Kadence child theme)

Flat, prioritized list of known work. Each item should become its own small
ticket when picked up — do not bundle unrelated items into one prompt/PR.

Priority labels:

- **P0** — must fix before publishing
- **P1** — important polish
- **P2** — later improvement
- **P3** — future idea

Items are grouped by area for readability, but priority (not grouping)
determines pickup order. See `ROADMAP.md` for how these map onto sprints.

- **Done (Phase 1 — Portfolio Content integration)** — The approved About /
  How I Work / Hobbies / Connect copy (chat-drafted and iterated across
  several editorial rounds) is now live as the theme's own default content
  in `estavillo-child/functions.php` and `template-parts/{about-teaser,
  about-content,footer-cta,contact-content}.php` — no wp-admin access
  exists in this environment, so "populating the CMS" means the code-level
  defaults the `es_portfolio_home_content` option falls back to (same
  "Home never breaks" pattern used everywhere else), not a live database
  write. Also fixed a real dormant bug found during integration: the six
  How I Work step defaults never set `icon_key`, so the process icons
  never rendered anywhere they weren't manually re-entered in wp-admin —
  now every step ships with a real icon key from the curated library.
  Added `es_about_timeline_defaults()` and `es_about_education_defaults()`
  (same pattern as the existing hobbies/process-steps defaults) so
  Timeline and Education have real starting content too. **Deliberately
  incomplete, not invented:** the first pass left the Timeline/Education
  entries that couldn't be sourced blank rather than guessed. A follow-up
  correction pass then reused already-approved data found elsewhere in
  the repo instead of leaving it blank: the Timeline's first entry now
  reads **"Lead Product Designer — Guzmán Villalba", 2025–2026** (the
  exact Role/Period/Source-context already approved in
  `docs/content/workshop-quoting-system-fields-en.md` — this also
  answers "current role," since that Period is still open), and
  Education now credits **Universidad de la República (UdelaR)** and
  **Google · Coursera** (both confirmed by the project owner). **Ceibal**
  still has no approved role/dates anywhere in the repo and stays out of
  the Timeline rather than guessed. Same for Education's two Year
  fields, and the Connect page's Availability status line (a real-time
  claim only the site owner can set). Navigation labels and Footer
  social links were intentionally NOT touched — never part of an
  approved-content round. Validated with a real-load PHP harness
  (requires the actual `functions.php` + `inc/*.php` with a WP-core
  shim, renders the actual template-parts via output buffering — not a
  synthetic approximation) and Chromium screenshots of Home/About/How I
  Work at 1440px/390px in both dark and light (the real
  `[data-theme='light']` token override) — zero console errors, zero
  overflow, zero broken images in all 12 combinations. **Still open:**
  Ceibal's Timeline entry, Education's two Year fields, Connect
  Availability line, real LinkedIn/Behance URLs, and — once real pages
  exist in wp-admin — swapping the Nav/CTA anchor URLs (`#work`,
  `#about`, etc.) for real page slugs. That last item now has a
  recommended architecture (see "Nav/CTA links break when browsing
  About/How I Work/Work/Contact" below) but is not yet implemented.
- **Done (About page — full professional-history restructure)** — The
  About page's Career Timeline (2 sparse entries) is replaced with a full
  information architecture: **Selected Experience** (Guzmán Villalba,
  Trazur, Ceibal — always visible, prioritized) and **Previous
  Experience** (Verona Office & Home, Samic SA, Fupsi.org — a secondary,
  collapsed group). Every entry supports role/organization/location/
  period/summary plus a "Key contributions" bullet list inside its own
  native `<details>/<summary>` disclosure (reused the theme's existing
  disclosure pattern from `estavillo/case-details`, ported into
  `pages.css` as `.es-about-details` — no new JS, no new Gutenberg
  block). Education gained institution/faculty/school/description/final-
  project fields (still `title`/`org`/`year` at its core, extended, not
  replaced). Added two new sections: **Other Certifications** (8 items,
  collapsed group) and **Languages**. All content sourced from
  `docs/about-page-authoritative-source.md`, a document the project owner
  added specifically because two prior attempts to reconstruct this
  content from the rest of the repo were insufficient — nothing in this
  pass was invented or reconstructed from partial sources; every role,
  date, and responsibility traces to that file. Confirmed **no** "Master"
  or "FUMS" entries (both were in a superseded draft from an earlier,
  interrupted ticket; the authoritative source explicitly excludes them).
  New CMS admin UI in `home-content-options.php` for all of the above
  (Selected/Previous Experience, extended Education, Other
  Certifications, Languages), replacing the old Career Timeline UI —
  same `es_portfolio_home_content` option, same saved-value → theme-
  default → empty-fallback precedence, no database migration. Hobbies
  list also updated to the authoritative 9-item list (previously 7) —
  two new interests (Gaming, Photography) ship without icons, since the
  curated hobby-icon library has no artwork for them yet; they render
  label-only via the existing `--empty` icon state rather than get new
  SVGs drawn for this ticket. Validated with the same real-load PHP
  harness plus a dedicated Chromium accessibility pass (keyboard focus,
  Enter-to-toggle, visible focus outline, correct default-collapsed
  state, content actually hidden/revealed) across desktop/tablet/mobile
  × dark/light (18 combinations, zero console errors, zero overflow).
  Theme bumped to 0.2.6, plugin to 1.5.2.
- **Done (About page correction — editability, repetition, copy, disclosure,
  timeline, alignment)** — Follow-up correction pass on the restructure
  above, addressing 8 reported problems without starting the Gutenberg
  migration (see the new P1 item below for that). **(1) Editability root
  cause fixed:** the "Portfolio Content" admin form read
  `$data['about_experience_selected']` etc. directly with no fallback to
  the theme defaults — the option genuinely never had those keys, so the
  form looked empty even though the frontend rendered full content (via
  `apply_filters($hook, theme_default())`). Added
  `es_portfolio_maybe_seed_about_defaults()` (`home-content-options.php`),
  a versioned, admin_init-hooked, one-time seed: for each of the 7 About
  field groups (About text, Experience, Earlier Experience, Education,
  Other Certifications, Languages, Hobbies) that is genuinely absent from
  the saved option (`array_key_exists()`, not `empty()` — so a
  deliberately-emptied field is never re-filled), it writes the current
  theme default into the option as a real, editable value. Guarded by
  `ES_PORTFOLIO_ABOUT_DEFAULTS_VERSION` so it runs its per-group check
  once and never overwrites anything already saved (including a prior
  seed). **(2) "About" repetition** reduced: the About page's first
  content section is no longer labeled "About" (new dedicated string,
  `about_intro_label` = "My approach" — kept separate from `about_label`,
  which the Home teaser and the About page's own hero eyebrow still use
  unchanged); hero title changed from "About." to "About me." in
  `templates/page-about.php`. Breadcrumb and eyebrow left as-is per the
  ticket. **(3) Introduction copy replaced** with the approved
  direct/personal direction (`es_about_intro_default()` in
  `functions.php`) — 4 short paragraphs instead of one dense
  academic-sounding block; `es_about_intro_paragraphs()` splits on blank
  lines so the admin's `about_text` textarea (bumped to 10 rows) renders
  as real `<p>` tags instead of collapsing into one paragraph.
  **(4) Experience relabeled:** "Selected Experience" → "Experience",
  "Previous Experience" → "Earlier Experience" (frontend labels, admin
  section headings, and the disclosure summary text — id changed
  `#previous-experience` → `#earlier-experience`, nothing links to it).
  **(5) Timeline feeling preserved** with a minimal CSS-only treatment: a
  continuous 1px vertical rule + a small accent dot per entry on
  `.es-exp-list`/`.es-exp-item` (pure CSS pseudo-elements, no JS, no new
  component) — works the same on mobile. **(6) Disclosure lightened:**
  `.es-about-details` no longer has a bordered box + solid background;
  replaced with a plain `summary` row (label left, simple `+`/`−`
  indicator right) and a single hairline `border-bottom` under the
  summary, always visible open or closed — same color/type tokens, so
  dark/light both still work with zero extra rules. **(7) Education
  desktop alignment bug fixed:** `.es-cred:first-child` only reset the
  top border/padding on the *first* grid cell, so in the 2-column row the
  Google certificate (2nd cell) kept its top padding and started lower
  than the Degree title next to it. Fixed by resetting the first *row*
  (`:nth-child(-n+2)`, scoped to `.es-about-page__education`) on desktop,
  reverted at the ≤680px stacking breakpoint so mobile keeps a divider
  between the two stacked entries. **(8) Certification links audited:**
  searched the entire git history for any previously-used verification
  URL for the 8 Other Certifications or the 2 Education entries — none
  exists anywhere (only credential IDs, never a `coursera.org/verify`-
  style link or similar); confirmed nothing to recover, left `link`
  fields empty and editable, no broken links rendered, matches the
  authoritative doc's own "do not invent URLs" instruction. Skills
  section: not rendered (none exists to hide). Resume: byte-identical,
  untouched. Validated with the real-load PHP harness (all 7 seed groups,
  paragraph-splitting, no Master/FUMS) plus Chromium across desktop/
  tablet/mobile × dark/light. Theme bumped to 0.2.7, plugin to 1.5.3.

---

## Bugs / fixes

- **Done (Sprint 4C)** — Blue interaction states (was: mobile menu
  active/pressed, especially "Work"). Fixed globally, not just on the
  mobile menu: desktop nav brand/links, the menu button, footer nav/meta
  links, buttons, arrow links, cards, and the new case-study index/body
  links all now carry explicit rest/hover/focus-visible/active/visited
  color rules reinforced with `!important` (the reinforcement is scoped to
  defending against external/adversarial CSS — e.g. Kadence or a caching
  plugin re-injecting a blue `a:visited` — never used for the theme's own
  internal cascade). Verified with a fake-Kadence adversarial-CSS-injection
  Playwright test across desktop and mobile.
- **P0** — Selected Work / case CTA links should be white/neutral by
  default and turn green **on hover only**, not permanently green.
  (`.es-card__cta` currently sets a base `color: var(--es-accent)` — this
  contradicts the intended default state and needs correcting.)
- **Done (Sprint 4)** — Mobile hero secondary CTA ("See how I work") no
  longer reads as an accidental wrap below the primary button. Root cause:
  the primary button's internal padding (26px) meant its label started
  ~26px to the right of the secondary link's text, even though both
  shared the same container edge — a real, measurable misalignment, not
  just a vibe. Fixed by aligning the secondary link's text to the
  button's text (`padding-left: 26px` at the ≤680px stack breakpoint) —
  CSS-only, same component, same copy, same animation. Verified via
  computed-position diffing (0.0px gap) at 375/390/428px plus visual
  screenshots. Broader Hero block/layout work (mobile-specific
  constellation composition, editability) remains its own future ticket —
  this fix was scoped narrowly to the one reported alignment bug.
- **P1** — Mobile hero needs a better mobile-specific constellation layout
  (current mobile treatment is a scaled-down desktop layer, not a
  purpose-built mobile composition).
- **P1** — Footer line/rhythm feels unresolved despite the recent spacing
  refinement.
- **P1** — Hero-to-How-I-Work transition still feels slightly awkward even
  after the gradient softening fix (border removal + fade).
- **P1 — Nav/CTA links break when browsing About/How I Work/Work/Contact.**
  `es_nav_links()` defaults (and the About/Connect/How-I-Work CTA URL
  defaults) are same-page anchors (`#work`, `#about`, `#process`,
  `#connect`) meant for Home's own single-page scroll layout. From any of
  the 4 standalone pages there's no element with those ids, so the links
  silently do nothing. **Recommended fix (analysis only — not yet
  implemented):** reject a blanket switch to real page URLs, since Home's
  header/mobile-menu/footer nav is shared with Home's *own* inline
  sections (Featured/Selected Work/About/Connect all live on Home too,
  using those same anchors) — swapping every nav link to a page URL would
  break Home's in-page scroll. Instead: (1) give each nav-link row one
  new optional field, "Page URL", alongside the existing Label + anchor;
  (2) add a small helper, `es_nav_link_href( $link )`, used by
  site-header.php (desktop nav + mobile menu) and site-footer.php instead
  of printing `$link['url']` directly — on Home it always returns the
  anchor (preserving today's scroll behavior exactly); off Home it
  returns the Page URL if one's set, and otherwise falls back to
  `home_url( '/' ) . $anchor` (navigates to Home, then scrolls — works
  correctly today, with zero admin setup, unlike the current bare
  anchor). This is additive only (one new optional field, one small
  helper function used in 2 existing template-parts) — no new blocks, no
  template redesign, and zero behavior change for anyone who hasn't
  filled in a Page URL yet. Rejected alternatives: real page URLs
  everywhere (breaks Home's own sections, above); anchors everywhere
  unprefixed (today's broken state).

## Design polish

- ~~**P1** — How I Work needs a future icon/motion treatment per step.~~
  **Done** — six approved illustrations integrated (asset-integration
  ticket, see `docs/HOW-I-WORK-ILLUSTRATIONS.md`); the old `.es-process__icon`/
  `.es-process__icon--empty` slots no longer exist, replaced by
  `estavillo/how-i-work-illustration`. Richer motion beyond the MVP hover
  stays tracked below ("Future motion exploration beyond How I Work's MVP
  list").
- **P1** — Footer needs a more intentional "ending" — it should read as a
  deliberate close to the page, not just a rule + links.
- **P2** — Header alignment (logo / nav / language / toggle slot) is
  acceptable for now. Do not over-tune; revisit only if a specific problem
  is identified.
- **P3** — Theme toggle: the header slot (`.es-nav__toggle`) is a
  placeholder only. Do not wire up functionality until light mode itself is
  actually implemented as a design system (colors, tokens, contrast pass).
- **P3** — Global light-mode implementation and toggle: a real site-wide
  pass (not just the `[data-theme='light']` token overrides that already
  exist in `tokens.css` for future-compatibility) — a working toggle, a
  contrast audit across every page, and validation this project hasn't
  done yet (light mode is explicitly out of scope/unvalidated on How I
  Work's polish pass, for example — see `docs/HOW-I-WORK-ILLUSTRATIONS.md`
  and the asset-integration/polish commits). Do not start until requested
  as its own ticket.

## Content / editability

- **P0** — Home cannot remain a rigid PHP-only template forever; the goal
  is to convert Home sections progressively into editable blocks/sections
  (Header, Hero, Selected Work, Featured Case, How I Work, About,
  Connect/Footer) — not one huge PHP-only template forever. See
  `EDITABILITY-PLAN.md`. Do this as small, ticket-sized section
  conversions, one at a time — not a single big migration.
- **P1 — Migrate the About page body to Gutenberg (documented now, not
  started).** About is currently the most content-heavy page still driven
  entirely by PHP template + "Portfolio Content" option fields (Experience,
  Earlier Experience, Education, Other Certifications, Languages, Hobbies —
  each a fixed-shape repeater with a capped row count, same pattern as How
  I Work's 6 steps). The preferred future architecture, following the same
  approach already proven for Case Studies (`single-es_case_study.php` +
  `the_content()`): the About page body becomes normal Gutenberg-editable
  content, so the site owner can add, remove, and reorder sections — including
  a Skills section, images, callouts, or anything else — without a PHP
  edit for every change. Scope for that future ticket: (1) migrate the
  existing About sections (Experience, Earlier Experience, Education,
  Other Certifications, Languages, Hobbies) into block content or
  block/pattern equivalents with **zero data loss** — every entry
  currently in `es_portfolio_home_content` needs a real migration path,
  not a re-entry-from-scratch; (2) once migrated, "Portfolio Content"
  should retain only the fields that are genuinely shared/global across
  pages — navigation, footer, contact data, Resume URL(s), and any other
  cross-page setting — not page-specific content that Gutenberg now owns;
  (3) Skills has never been built or rendered on the About page — the
  authoritative source doc explicitly says not to add one "in this
  version," not never — this future Gutenberg migration is the natural
  place to add a Skills section, as a normal block, if/when the site
  owner defines what should be in it (no such content exists anywhere in
  the repo today; nothing to migrate for it, only to add fresh later). Not
  started. Do not begin this migration inside an unrelated content
  ticket — it is large enough to be its own ticket, planned and reviewed
  on its own.
- **P2** — About compact-layout refinements: a visual-polish pass over
  About (spacing/hierarchy/separators) analogous to the one just done for
  How I Work, once About's own open questions above are settled. Not
  started — do not begin inside an unrelated How I Work ticket.
- **Done (Sprint 3)** — **Selected Work** is now the first section
  converted to editable content: a "Case Study" custom post type, editable
  in wp-admin, with a hardcoded-placeholder fallback so Home never breaks
  if no case studies exist yet. See `EDITABILITY-PLAN.md` for exact usage.
- **P1** — Enter Presupuestador and Trazur as real, published Case Study
  posts. **Presupuestador's body content is ready two ways** since Sprint
  4J: (a) the preferred path — insert the Gutenberg pattern **"Estavillo —
  Presupuestador Case Structure (ES)"** (or "(EN)") from the inserter's
  Patterns tab into the Case Study post: the full 13-section case lands as
  editable blocks (plugin ≥ 1.1.0 + theme update required); or (b) the
  fallback path — paste the masters
  `docs/content/presupuestador-case-study-{es,en}.html` into a Custom HTML
  block, exactly as before. Native fields per `-fields.md`, publish order
  per `-wordpress-publish.md`, both unchanged. What's still needed: doing
  the wp-admin step itself (no wp-admin access from this environment), the
  native fields from the field sheet, real screenshots/photos replacing the
  `{asset: …}` placeholders — with the block path these are replaced from
  the Media Library via each Case Figure block, no HTML editing — and
  picking a "Hero layout" once a real featured image exists. Also delete
  the temporary Customizer "Additional CSS" visibility override after
  updating the theme (Sprint 4J fixed the root cause). **Trazur's Spanish
  content is written and integrated** —
  `docs/content/trazur-gutenberg-es.html` +
  `docs/content/trazur-fields-es.md`, validated (round-trip parser +
  render harness + Chromium desktop/mobile/dark/light, zero invalid
  blocks). **Trazur's English content is also written and integrated** —
  `docs/content/trazur-gutenberg-en.html` +
  `docs/content/trazur-fields-en.md`, an editorial adaptation (not a
  literal translation) of the approved Spanish case for an international
  Product Design audience, same 17 sections/anchors (renamed to English
  slugs) and same evidence, validated the same way (round-trip parser +
  render harness + Chromium desktop/mobile/dark/light, 144/144 blocks
  valid, zero invalid blocks, zero overflow). **Still pending for both
  languages:** the wp-admin paste-and-publish step itself (no wp-admin
  access from this environment), all real images/screenshots replacing
  the `{asset: …}` / `placeholderLabel` placeholders (shared between the
  two language versions — same underlying images) + the poster PDF,
  live-WordPress visual QA, and the claims flagged in
  `trazur-fields-es.md` / `trazur-fields-en.md` needing sign-off (the
  English sheet also flags its own terminology and wording choices for
  review). Do not mark either case published until those are done.
- **P1 (new, after Sprint 4M)** — Migrate the Presupuestador body to the
  corrected editorial composition system: Case Section's Content/Reading/
  Wide width control + native Gutenberg Columns for side-by-side text/
  figure compositions — deliberately NOT done in Sprint 4M ("do not
  migrate Presupuestador yet"). For new cases (Trazur, French Bakery,
  Samic) start from the **"Case Study — Canonical Starter"** pattern; use
  **"Case Study — Editorial System Demo"** as the reference lab page for
  what each width does and how to compose columns.
- **Done (Patterns Phase 0)** — Second-pass architecture review of
  recurring case artifacts (persona, comparison table, journey map, flow
  diagram, methodology, callouts, etc. — prompted by auditing the real
  Trazur case) concluded no new custom block was justified yet (one real
  case isn't "validated by multiple projects"). Added three reusable
  Patterns built only from native + existing Estavillo blocks —
  **"Case Study — Persona"**, **"Case Study — Comparison Table"**,
  **"Case Study — Callout Panel"** — plus a CSS-only **Checkmark List**
  block style. See `estavillo-child/README.md` → "Patterns Phase 0" and
  `EDITABILITY-PLAN.md` for the full breakdown. Journey Maps and Flow
  Diagrams stay images; image+text layouts stay plain native Columns —
  neither got a dedicated pattern. Available now for the Trazur/Samic/
  French Bakery content tickets below; no code changes expected when
  those are picked up unless real content surfaces a genuine gap.
- **Done (Sprint 3)** — **Featured Case** is editable: reuses the Case
  Study CPT with a "Feature this case on Home" checkbox instead of a
  separate mechanism. See `EDITABILITY-PLAN.md`.
- **Done (Sprint 3)** — **About** is editable: a plugin-owned "Home
  Content" options page (wp-admin) now hooks the `es_home_about_text` /
  `es_home_about_url` / `es_home_about_portrait` filters that already
  existed since Home v1 — no theme template changes were needed. See
  `EDITABILITY-PLAN.md`.
- **Done (Sprint 3)** — **How I Work** is editable from the same "Home
  Content" page: each of the 6 steps (title + text) can be edited
  individually — leaving a step blank keeps its current placeholder, so
  editors don't have to fill in all 6 at once. Icon slot stays reserved
  (out of scope, design/motion territory).
- **Done (Sprint 3)** — **Connect** is editable from the same "Home
  Content" page: title, lead text, contact email, and CTA URL. The
  contact email field is shared with the Footer (same `es_contact_email`
  filter both sections already used) — one field updates both.
- **Done (Sprint 3)** — **Header** nav links are editable from the same
  "Home Content" page (4 label+URL rows, edited individually). The same
  array feeds the desktop nav, the mobile menu, and the footer nav — one
  edit updates all three, since they already shared `es_nav_links()`.
- **Done (Sprint 3)** — **Footer** is editable from the same "Home
  Content" page: LinkedIn URL, Behance URL, and location. Nav links and
  contact email were already covered by the Header/Connect tickets above
  (same shared data), so Footer only needed its two genuinely footer-only
  fields.
- **Done (Sprint 4B)** — Case Study now has a real single page:
  `single-es_case_study.php`, reusing the ESTAVILLO chrome, rendering
  title, eyebrow, excerpt, tags, featured image (or placeholder), a
  Status/Role/Tools/Period meta row, and the standard WordPress editor
  body via `the_content()`. No case builder — the body is normal editor
  content, same as any WordPress post. Role/Tools/Period are new optional
  plain-text fields on the existing Case Study meta box.
- **Done (Sprint 4A)** — Polylang readiness: the Case Study CPT registers
  itself as translatable via `pll_get_post_types` (self-configuring, no
  manual settings step); `es_case_tag` deliberately stays language-neutral
  for V1 (see `EDITABILITY-PLAN.md`); the header and mobile-menu language
  indicators are now a real Polylang switcher (`pll_the_languages()`),
  guarded by `function_exists()` so the site is unaffected if Polylang is
  inactive.
- **Done (Sprint 4C)** — Case Study format system: an optional sticky
  in-page index (manual `Label|#anchor` field, new `_es_case_index` post
  meta) and a 14-class `.es-case-*` library (section, label, heading, lead,
  two-column, figure+caption, browser-chrome frame, stats grid, timeline,
  decision cards, pullquote, status grid, native accordion) usable inside
  the standard editor via `the_content()` — no case builder, no ACF. See
  `estavillo-child/README.md` for the full reference and example.
- **Done (Sprint 4G / mega sprint)** — 4 new fixed pages (Work, About, How
  I Work, Contact), all standalone page templates sharing the ESTAVILLO
  chrome. Work reuses the existing "Show on Home" flag to split Selected
  vs. Archive (no new field). About gained 4 new Phase-1 fields on Home
  Content (CV URL, career timeline, education/certificates, hobbies). How
  I Work and Contact reuse existing data with zero new fields. Case Study
  gained breadcrumbs and 3 new opt-in hero layout variants. See
  `estavillo-child/README.md` → "Páginas fijas", "Hero layout options",
  "Breadcrumbs".
- **P3** — Work's Selected/Archive split is binary (reuses the existing
  "Show on Home" checkbox — one flag serves two purposes now). This is
  fine while there's no real content, but if a case ever needs to be
  "hidden from the Home teaser but still current work, not archive" the
  one flag can't express that 3-way distinction. Revisit only if that
  scenario actually comes up — don't add a second flag speculatively.
- **P3** — About's career timeline and education/certificates are capped
  at 4 fixed rows each (same pattern as How I Work's 6 fixed steps). If
  a real timeline ever needs more than 4 entries, this becomes a proper
  repeater — not urgent, most careers fit in 4 rows for a portfolio.
- **Done (Sprint 4H)** — How I Work dedicated page rebuilt as a real
  editorial page (`template-parts/how-i-work-detail.php`), not a reused
  copy of the Home teaser: same 6 steps, plus 3 new optional per-step
  fields (Why it matters / Example / Tools) shown only there. Contact
  page's heading hierarchy fixed (two competing giant headings → one H1
  from page-head, one smaller supporting statement below) and gained 2
  optional fields (secondary note, availability/status line). About's
  hobbies/interests rebuilt from a single comma-separated field into 8
  structured rows (label, icon, optional text, show/hide) with a new
  7-icon curated library and CSS-only hover/focus micro-interactions.
  (Sprint 4K replaced that placeholder library with the 8 approved
  hand-drawn artworks in `estavillo-child/assets/icons/` — old saved
  keys `music`/`horse` resolve automatically to `guitar`/`horse-head`,
  nothing to reconfigure.)
  "Home Content" renamed to **"Portfolio Content"** in wp-admin (label
  only — option key/slug unchanged, no data lost). See
  `estavillo-child/README.md` → "Where to edit each part of the
  portfolio" for the full field reference.
- **P3** — Hobbies' "order" is row position (same convention as every
  other repeater in this codebase — Nav Links, Process Steps, Timeline,
  Education), not a separate numeric field. If that ever feels
  insufficient in practice, add one then — not speculatively now.
- **P2** — Convert **Hero** to editable content (copy/CTAs are already
  filterable; coordinate with the Hero block/layout ticket above so
  editability doesn't get built on top of a layout that's about to
  change).
- **P2** — Real images / final asset placeholders across Home sections.
- **P2** — Refine EN/ES copy for all Home sections.
- **P2** — Create the Home (ES) page (Polylang translation of the existing
  Home page, same template) once real content is ready — mechanical setup
  step in wp-admin, no code required (see `EDITABILITY-PLAN.md`).

- **P1 — How I Work strategy defined, not yet implemented.** Full content,
  interaction model, and Gutenberg architecture for both the Home teaser
  and the dedicated How I Work page are planned in
  `docs/HOW-I-WORK-STRATEGY.md` — six moves renamed for precision (closing
  two real gaps against the "Core Positioning" brief: exploring
  alternatives, and testing with users), Home teaser regrouped into three
  ideas (Understand/Explore/Improve) instead of reproducing the six-card
  grid, a new expandable-detail schema (adds `ai_help`/`human_judgment`,
  reuses the already-fixed `estavillo/case-details` disclosure), a large
  editorial illustration family (replacing the current small abstract
  icons), and an MVP/future motion split. No new custom Gutenberg block
  needed — reuses `core/image` (Media-Library-driven, same as About's
  portrait fix) + `core/heading`/`core/paragraph` + `estavillo/case-details`
  + a new Pattern, following the same block-vs-pattern reasoning already
  applied to About's Hobbies. Several open decisions (grouping split,
  three copy renames, illustration production, AI band wording) need
  explicit sign-off before implementation — see the strategy doc's §13.
  Not started.

## How I Work — Phase 2 (planned, not started)

Every item below is intentionally **not implemented** — captured here so
none of it gets lost, picked up only after the How I Work strategy above
is approved and built. See `docs/HOW-I-WORK-STRATEGY.md` for the current
process-page work; these are the adjacent/downstream items raised
alongside it.

- **P2** — Home teaser illustration sizing exploration: the three
  Understand/Explore/Improve icons are deliberately restrained (44px) for
  the MVP asset-integration pass — revisit only if the Home teaser itself
  gets a dedicated design pass, not as a follow-on to How I Work's own
  page work.
- **P2** — Optional AI-band visual/illustration exploration: the
  transversal AI band is text-only by design for MVP (no illustration, no
  logo wall) — a future pass could explore a small abstract supporting
  element IF it earns its place, per the same "no illustration merely to
  fill space" principle already applied in the polish pass.
- **P2** — Case-study architecture inspired by editorial senior
  portfolios (a stronger visual/structural reference point than the
  current case template, once more real cases exist to test it against).
- **P2** — Narrative project titles (move away from purely descriptive
  case names toward titles with a point of view), once enough cases exist
  to make the pattern worth establishing.
- **P2** — Annotated product screens within case studies (callouts on
  real UI, not just prose describing it).
- **P2** — Case-specific structures rather than one rigid case template —
  let each case's own shape drive its section order/composition instead
  of forcing every case through the same 13/17-section skeleton.
- **P2** — Document AI decisions, limitations, and validation *within*
  individual case studies (not just the site-wide How I Work AI band) —
  where AI was used on that specific project, what its limits were, how
  output was validated.
- **P2** — Explicit treatment of business outcomes in case studies —
  distinct from raw metrics below: narrating *why* an outcome mattered to
  the product/business/operational context it happened in, using the same
  sober-language rules as the How I Work strategy (`docs/HOW-I-WORK-STRATEGY.md`
  §2) — never inflated claims, never invented numbers.
- **P2** — Metrics and evidence, including operational metrics and KPIs:
  a consistent, honest way to present real numbers/outcomes per case once
  they exist (task success, errors, time, adoption, support load, or
  progress toward a stated product goal) — never invented placeholders,
  never a number without a real source.
- **P2** — Testimonials and proof of collaboration (quotes, references,
  or other third-party evidence of real work with real people/teams).
- **P2** — Future "Capabilities / What I Do" section (site-wide, not
  case-specific) — scope and placement not yet defined.
- **P2** — Future Tools placement — a real, honest list of tools used
  (distinct from the How I Work AI band, which is about the *role* AI
  plays, not a product list).
- **P3** — Client/company logo treatment — only once real, permissioned
  logos exist to show; do not build the component ahead of real assets.
- **P2** — Accessibility review across the portfolio — a full pass over
  every page (not just How I Work's own principle in
  `docs/HOW-I-WORK-STRATEGY.md` §11), checking real keyboard/focus/
  contrast/motion behavior site-wide once enough pages are stable to make
  a single audit worthwhile, rather than re-litigating it page by page.
- **P3** — Typographic motion exploration (site-wide direction, not
  scoped to any one page yet).
- **P3** — Future motion exploration beyond How I Work's MVP list (see
  `docs/HOW-I-WORK-STRATEGY.md` §8 "Future") — illustration video/
  Lottie-style sequences, scroll-scrubbed/pinned animation, cursor-follow
  or parallax effects; explicitly deferred past the MVP pass for both How
  I Work and any other page that might want it later.
- **P2** — How I Work video script and storyboard — a future richer
  medium for the same Core Positioning narrative; script/storyboard only
  for now, no video production.
- **P1** — Spanish How I Work version, once the English content and
  Gutenberg architecture in `docs/HOW-I-WORK-STRATEGY.md` are implemented
  and approved — same "no invented translation" rule already applied to
  About's Spanish content (still pending separately).
- **P2** — Relationships between process steps and related cases — the
  `related_case` field noted in the strategy doc's expandable-content
  schema is deferred until this exists; needs a real cross-linking model
  between How I Work moves and Case Study posts, not a hardcoded guess.
- **P2** — Future Home refinement, once How I Work (page + teaser) is
  approved and live — Home's own How I Work section may need a follow-up
  pass after the full page's final direction is settled, since the teaser
  is designed to point at it.

Editability priority order (for reference, full detail in
`EDITABILITY-PLAN.md`):

1. Selected Work — **done** (Case Study CPT)
2. Featured Case — **done** (Case Study CPT, "featured" flag)
3. About — **done** (Home Content options page)
4. How I Work — **done** (Home Content options page, per-step)
5. Connect — **done** (Home Content options page)
6. Header — **done** (Home Content options page, nav links)
7. Footer — **done** (Home Content options page, social links + location)
8. Hero — not started (deferred until the Hero block/layout ticket lands)

## Accessibility / UX

- **P2** — Case Study sticky index: currently a single horizontal strip
  under the header on all viewport sizes (deliberately kept simple in
  Sprint 4C). Future improvement once there's real multi-section case
  content to test against: a **desktop-only side index pinned to the
  left** of the case body (e.g. sticky column running alongside
  `.es-case__body`), while mobile keeps today's horizontal/compact strip
  unchanged. Not built now — do not overbuild the index ahead of real
  content.
- **Done (Sprint 4H)** — Sticky header was silently broken in real
  WordPress (visibly not sticking on scroll) despite `position: sticky`
  being present in the CSS — root cause was `.es-page { overflow-x: hidden }`
  (base.css) turning `.es-page` into its own scroll-clipping ancestor,
  which breaks `position: sticky` for any descendant per the CSS Overflow
  spec. Fixed by removing that rule (the one real horizontal-bleed source,
  the mobile hero SVG, was already self-contained by its own
  `.es-hero { overflow: hidden }`). No admin toggle — sticky is automatic
  on every ESTAVILLO template. Supersedes the "behavior/visual polish"
  framing this item used to have below — the bug was functional, not
  cosmetic.
- **P2** — Optional "back to top" interaction.
- **Done (Sprint 4G / mega sprint, then Sprint 4H)** — Breadcrumbs (Home /
  Work / case title) on the Case Study single page, accessible
  (`<nav aria-label="Breadcrumb">`, `aria-current="page"`), Polylang-aware
  automatically (native `home_url()`/permalink behavior, no extra code).
  Sprint 4H extended them to Work/About/How I Work/Contact (they render
  everywhere the ticket asked, never on Home) and fixed a second bug: the
  breadcrumb CSS lived only in `case-study.css` (Case-Study-only), so it
  would have rendered unstyled on the other 4 pages even once wired in —
  moved to `site.css` (shared chrome). See `template-parts/breadcrumbs.php`
  and `functions.php` → `es_breadcrumb_trail()`.

## Hero variants

- **P3** — Add 2–3 curated additional hero variants, only after Home
  content and editability are stable. `network_constellation` stays the
  default. Do not build a large hero gallery — variety is not the current
  bottleneck.

## Process

- **P0** — Adopt the ticket-based workflow documented in this `docs/`
  folder for all future work; avoid large token-heavy redesign prompts
  (see `DECISIONS.md`).

## About — future capabilities

- **P3 — Optional per-hobby description / tooltip.** Assessed during the
  About polish ticket (§4); **not implemented** by design (the hobbies
  section and its illustration system are approved and were left untouched).
  The architecture already supports it: `estavillo/hobby-list` stores each
  hobby as an open object in its `items` attribute (`{label, icon, …}` —
  `block.json` types it as a plain object), and the legacy PHP fallback
  (`template-parts/about-content.php`) already reads an optional
  `text`/description per hobby (`.es-hobby-item__text`). So a future
  expandable description or tooltip/popover can be added per hobby **without
  a new block and without a data migration** — only three touch points would
  change: the block `edit.js` (add a textarea control per item), the block
  `render.php` (emit the text, e.g. as `.es-hobby-item__text` or a
  `data-tooltip`), and a small CSS block for the reveal/popover. Keep it
  restrained so it never competes with the illustration grid.

## Header & Footer (Phase 5) — routing rules, ownership, backlog

**Where it's edited (one source of truth):** Case Studies → **Portfolio
Content** (plugin `es_portfolio_home_content` option) → "Header — site
identity", "Header (navigation links)", "Footer" sections. No second
settings system; the Customizer keeps only accent/hero/font. Header/Footer
are global template parts (`template-parts/site-header.php` /
`site-footer.php`), never page content — they update everywhere at once
(Home, Work, How I Work, About, Connect, Case Studies, EN + ES).

**Navigation routing rules** (centralized in `es_nav_resolve_url()`,
`inc/header-footer.php`):
- A nav URL that is a bare in-page anchor (`#work`) → on Home stays `#work`
  (in-page scroll); from any other page it becomes `HOME_URL + #work` so it
  jumps to Home and then the section — never a dead local anchor.
- A nav URL that is a real page URL/path → used as-is (already
  language-correct; with Polylang, `home_url()`/permalinks resolve to the
  current language natively — no guessed translated URLs).
- Empty URL → Home URL (never an empty `href`).
- The desktop header, mobile menu and footer nav all consume the same
  `es_nav_links_display()` set (label + resolved url + show flag).

**Active nav** is server-side (`es_nav_active_key()` / `es_nav_item_is_active()`):
Work + Case Study singles → Work; How I Work page → How I Work; About page →
About; Connect page → Connect; Home → neutral. Green label + a small
absolutely-positioned dot (no layout shift), `aria-current="page"`.

**Content ownership / Polylang:** the `es_portfolio_home_content` option is a
single site-wide option, so all Header/Footer fields are **language-neutral**
(phone, email, social URLs, location, logo, sticky, layout — one value shared
EN/ES, never duplicated). Translatable nav labels keep coming from the theme's
`es__()` strings, which are registered with Polylang (`pll_register_string`) —
translate them in **Languages → Strings translations** (leave the Portfolio
Content nav labels blank to keep the per-language versions). **Limitation:**
Portfolio Content text fields (a custom nav label typed there, the footer
note) are single-value; per-language variants for those specific fields are
not supported without a larger multilingual-options architecture — documented,
not built.

### Backlog (not implemented in Phase 5)
- **Complete light-mode visual system.** The header theme-toggle is still a
  non-functional placeholder; light mode is a separate phase. *Light mode is
  NOT complete.*
- Optional animated Connect statement (footer/Connect) — restrained only.
- Optional future header CTA (the header already leaves room for it).
- Richer footer composition (extra columns / newsletter) — only if justified.
- Optional social-icon expansion (line icons per network) — text-only for now.
- Advanced Home section-aware active nav (scroll-spy) — deliberately avoided
  (no heavy JS scroll tracking this phase; Home stays neutral).
- Footer newsletter / form — only if later justified.
- Per-language values for the custom nav label / footer note fields.

## Spanish-parity phase

Full content-ownership rules, EN/ES page matrix, recommended Polylang string
values and known limitations now live in `docs/MULTILINGUAL-PARITY.md` — read
that file before touching bilingual content. Summary: Home/About/How I
Work/Connect now have paired `*-gutenberg-es.html` source files
(`docs/content/`); Header/Footer/breadcrumb/shared-CTA strings were wired
through the existing `es__()`/Polylang mechanism (previously several used raw
`__()` with no `.mo` pipeline, so they never actually varied by language —
fixed as part of this phase). The Presupuestador/Workshop Quoting System case
still has no Spanish translation (documented, not fabricated — see
`docs/MULTILINGUAL-PARITY.md` §5). The Work page still has no Gutenberg
content file in either language (pre-existing gap, unrelated to this phase).
