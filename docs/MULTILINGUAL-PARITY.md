# Multilingual (EN/ES) parity — content ownership, page matrix, string inventory

One concise reference for the Spanish-parity phase. Read this before editing
any bilingual content or Polylang settings.

## 1. Content ownership rules

| Kind of string | Where it lives | How it's translated |
|---|---|---|
| Page-specific editorial content (Home hero, About narrative, How I Work moves, Connect intro, page CTAs, section headings) | The Gutenberg **page content** itself (`post_content`), one WP Page per language | Write/paste the paired `*-es.html` file into the Spanish Page. No PHP involved. |
| Global shared UI strings (nav labels, footer note default, shared CTA labels, a11y labels like "Menu"/"Open menu") | `estavillo-child/functions.php` → `es_child_ui_strings()`, read through `es__()` | **Polylang → Strings translation** admin screen. `es__()` calls `pll__()` when Polylang is active; the English value in `es_child_ui_strings()` is only the fallback/registration default. See §3 below for the recommended Spanish value of every key. |
| Global language-neutral values (phone, email, WhatsApp, social URLs, wordmark, sticky-header on/off, footer layout) | The single `es_portfolio_home_content` option (Portfolio Content admin page) | **Not translated, not duplicated.** One value, shared by both languages, by design (see `estavillo-child/inc/header-footer.php` docblock). |
| Backend/admin UI (Portfolio Content field labels, block Inspector labels, pattern titles) | Plugin PHP/JS, wrapped in `__()`/`esc_html_e()`/`@wordpress/i18n` with the `estavillo-portfolio-core` text domain | Already correct as shipped — WP core i18n mechanism, no separate EN/ES admin screens. No `.mo` file exists or is required for this ticket (the admin UI itself stays English; only the *mechanism* had to be i18n-clean, which it already was). |
| Technical identifiers (CSS classes, HTML IDs, PHP/JS function names, option keys, block names, anchors, SVG class names) | Code | **Never translated.** All anchors (`#work`, `understand-the-system`, etc.) are identical between the EN and ES Gutenberg files. |

## 2. EN/ES page matrix

| WP Page | Slug (EN) | Slug (ES, recommended) | Template | EN Gutenberg source | ES Gutenberg source | Status |
|---|---|---|---|---|---|---|
| Home | `/` | `/` (Spanish site root, per Polylang language home) | `templates/page-home-estavillo.php` | `docs/content/home-gutenberg-en.html` | `docs/content/home-gutenberg-es.html` | ES file created this phase |
| About | `/about/` | `/sobre-mi/` | `templates/page-about.php` | `docs/content/about-gutenberg-en.html` | `docs/content/about-gutenberg-es.html` | ES file created this phase |
| How I Work | `/how-i-work/` | `/como-trabajo/` | `templates/page-how-i-work.php` | `docs/content/how-i-work-gutenberg-en.html` | `docs/content/how-i-work-gutenberg-es.html` | ES file created this phase |
| Connect | `/contact/` | `/contacto/` | `templates/page-contact.php` | `docs/content/connect-gutenberg-en.html` | `docs/content/connect-gutenberg-es.html` | ES file created this phase |
| Work | `/my-work/` (canonical, pre-existing) | `/es/trabajos/` (canonical, pre-existing) | `templates/page-work.php` | *(PHP fallback only — no Gutenberg content file exists yet for either language)* | — | Naming/navigation unified (see "Work/Proyectos naming unification" ticket); page body still not migrated to Gutenberg |
| Case Study — Trazur | `/work/trazur/` (recommended slug) | `/proyectos/trazur/` | `single-es_case_study.php` | `docs/content/trazur-gutenberg-en.html` | `docs/content/trazur-gutenberg-es.html` | **Already complete** (earlier phase) |
| Case Study — Presupuestador / Workshop Quoting System | `/work/workshop-quoting-system/` (recommended slug) | `/proyectos/presupuestador/` | `single-es_case_study.php` | `docs/content/workshop-quoting-system-gutenberg-en.html` (current, supersedes the older `presupuestador-case-study-en.html`) | **missing — see §5** | Not published in either language yet (marked "content written, not yet published" in its own source file) |

## 3. Shared-string inventory — recommended Spanish values

These are **not stored in the repository** (Polylang strings are a runtime WP
setting). Enter them in **Polylang → Strings translation** once Polylang is
active with English + Spanish configured. Key = the array key in
`es_child_ui_strings()` (`estavillo-child/functions.php`).

| Key | English (registered default) | Recommended Spanish |
|---|---|---|
| `nav_work` | Work | Proyectos |
| `nav_how` | How I Work | Cómo trabajo |
| `nav_about` | About | Sobre mí |
| `nav_connect` | Connect | Contacto |
| `hero_eyebrow` | Product Designer · Systems & Operations | Product Designer · Sistemas y Operaciones |
| `hero_cta_primary` | View featured case | Ver caso destacado |
| `hero_cta_secondary` | See how I work | Ver cómo trabajo |
| `featured_label` | Featured case | Caso destacado |
| `featured_cta` | Read the case study | Leer el caso |
| `process_label` | How I work | Cómo trabajo |
| `process_cta` | See the full process | Ver el proceso completo |
| `work_label` | Selected work | Proyectos seleccionados |
| `work_view_all` | All work | Ver todos los proyectos |
| `work_view_case` | View case study | Ver caso |
| `about_label` | About | Sobre mí |
| `about_intro_label` | My approach | Mi enfoque |
| `about_cta` | More about me | Más sobre mí |
| `cta_label` | Connect | Contacto |
| `cta_button` | Write me | Escribime |
| `lang_switch_label` | Language switch | Cambiar idioma |
| `case_meta_status` | Status | Estado |
| `case_meta_role` | Role | Rol |
| `case_meta_tools` | Tools | Herramientas |
| `case_meta_period` | Period | Período |
| `breadcrumb_home` | Home | Inicio |
| `nav_aria_main` | Main | Principal |
| `nav_aria_footer` | Footer | Pie de página |
| `nav_aria_mobile` | Mobile | Móvil |
| `menu_label` | Menu | Menú |
| `menu_open` | Open menu | Abrir menú |
| `menu_close` | Close menu | Cerrar menú |
| `theme_toggle_title` | Light / dark — coming soon | Claro / oscuro — próximamente |
| `scroll_label` | Scroll | Desplazate |
| `scroll_aria` | Scroll to How I work | Desplazate a Cómo trabajo |
| `whatsapp_label` | WhatsApp | WhatsApp *(unchanged — brand name)* |
| `footer_call_generic` | Call by phone | Llamar por teléfono |
| `footer_call_named` | Call %s | Llamar a %s |
| `footer_wa_generic` | Contact on WhatsApp | Contactar por WhatsApp |
| `footer_wa_named` | Contact %s on WhatsApp | Contactar a %s por WhatsApp |
| `case_sections_aria` | Case sections | Secciones del caso |
| `case_media_ph_aria` | Placeholder for the case visual | Marcador para la imagen del caso |
| `connect_cta_all` | All ways to connect | Todas las formas de contacto |
| `about_eyebrow` | Product Designer · Systems & Operations | Product Designer · Sistemas y Operaciones |
| `about_title` | About me. | Sobre mí. |
| `how_title` | How I work. | Cómo trabajo. |
| `how_lead` | I don't start with interfaces. I start by understanding the system. | No empiezo por las interfaces. Empiezo por entender el sistema. |
| `work_title` | Work. | Proyectos. |
| `work_lead` | A selection of product and systems design work, from live decision tools to earlier academic and legacy projects. | Una selección de proyectos de Product Design y sistemas, más trabajo anterior en diseño digital, industrial y visual. *(actualizado — ticket "Work/Proyectos naming unification"; reemplaza la redacción anterior de esta fila)* |
| `work_archive_label` | Archive / older work | Archivo / trabajo anterior |
| `connect_eyebrow` | Get in touch | Hablemos |
| `connect_title` | Start a conversation. | Empecemos una conversación. |
| `connect_lead` | I'm open to Product Design, Design Systems and UX Research roles — anywhere the goal is making a real system work better, not just look better. | Estoy abierto a roles de Product Design, Design Systems y UX Research — donde el objetivo sea lograr que un sistema real funcione mejor, no solo que se vea mejor. |

**Reused terminology note:** `nav_how`/`process_label` and `nav_about`/`about_label`/`about_eyebrow` are intentionally identical in both languages — this mirrors the *pre-existing* English pattern (the How I Work eyebrow already reuses `process_label`; the About eyebrow already avoids the word "About" a third time). Not a new repetition introduced by Spanish parity.

## 4. §9 breadcrumb / eyebrow / H1 — model applied, and one flagged conflict

The ticket's own example table suggests specific EN wording ("Background" as
the About eyebrow, "Let's connect." as the Connect H1) that **does not match
the currently approved English copy** (the About eyebrow is
"Product Designer · Systems & Operations" per the About final-polish ticket;
the Connect H1 is "Start a conversation." per the Connect revision ticket).
Per the ticket's own override clause ("do not force this wording... report
any conflict before changing approved copy"), **the approved English copy
was left unchanged**, and the Spanish translations above are faithful
translations of the *actual* approved English strings, not the ticket's
illustrative examples. Using the ticket's suggested "Trayectoria" for the
About eyebrow would also have collided with "Trayectoria" already used as
the About page's own "Background" section heading two sections later.

Applied model (all via the `es__()`/Polylang keys above):

| Page | Breadcrumb | Eyebrow | H1 |
|---|---|---|---|
| How I Work | Inicio / Cómo trabajo | Cómo trabajo | Cómo trabajo. |
| About | Inicio / Sobre mí | Product Designer · Sistemas y Operaciones | Sobre mí. |
| Connect | Inicio / Contacto | Hablemos | Empecemos una conversación. |
| Work | Inicio / Proyectos | Proyectos | Proyectos. |

## 5. Missing Case Study translations (documented, not fabricated)

- **Trazur** — EN and ES Gutenberg content both exist and are complete
  (`trazur-gutenberg-en.html` / `trazur-gutenberg-es.html`, an earlier
  phase). Nothing to do.
- **Presupuestador / Workshop Quoting System** — only English content
  exists for the *current* structure (`workshop-quoting-system-gutenberg-en.html`),
  and its own header comment says it is not yet published. An older
  `presupuestador-case-study-es.html` exists in the repo but uses a
  superseded structure (plain Custom-HTML block instead of the current
  Case Section/Columns block system) and does **not** match the current
  English case — pasting it in as-is would not be a real translation of
  the live content. Per §14 ("do not fabricate Spanish cases"), **no new
  Spanish case content was written this phase.** When this case is ready
  to be translated, it should follow the same paired-file convention
  (`workshop-quoting-system-gutenberg-es.html`) and start from the
  *current* English file, not the superseded one.
  **Manual step if the English case is published before its Spanish
  translation exists:** do not create a Polylang translation link to the
  old `presupuestador-case-study-es.html` draft — leave the case
  untranslated in Polylang (no ES post linked) rather than link to
  stale content.

## 6. Intentional English terms (not bugs)

- "Product Designer" — kept in English everywhere it names the person's
  core professional identity (Home hero eyebrow, About bio opening
  sentence, About eyebrow), per explicit ticket instruction. "Industrial
  Designer" is kept alongside it only in that same fixed opening-bio
  phrase, for the same reason.
- Design methodology names used as-is in both languages: Design Thinking,
  Lean UX, Service Design, Agile, Design Sprints, Scrum, Kanban — standard
  international terms, not translated.
- Official certificate/course titles (e.g. "Google UX Design Professional
  Certificate", "User Experience: The Beginner's Guide") — left in English
  exactly as issued; translating a real, named credential would
  misrepresent it.
- "UX", "UX Research", "IA" (Spanish for AI) — industry-standard acronyms,
  used as-is in professional Spanish UX writing.
- Company/brand/proper names: Guzmán Villalba, Trazur, Ceibal, Verona
  Office & Home, Samic SA, Fupsi.org, WhatsApp, WordPress, WooCommerce,
  Figma, Mailchimp, Coursera, edX, etc. — never translated.

## 7. Manual WordPress import steps (per page)

For each of the four pages in §2 marked "ES file created this phase":

1. In wp-admin, either open the existing Spanish translation of the page
   (if Polylang already created one) or create a new Page and use
   Polylang's "+ Add translation" from the English page so the two stay
   linked.
2. Set the recommended Spanish title/slug from the matrix in §2.
3. Open the block editor → Options (⋮) → Code editor, paste the full
   contents of the matching `*-gutenberg-es.html` file below its own
   instructional header comment, then switch back to Visual editor.
4. Complete the "ACTION REQUIRED BEFORE PUBLISHING" items listed at the
   top of that specific file (portrait upload, CV link, LinkedIn/Instagram
   URLs, closing CTA link) — same real assets as the English page, not a
   second set.
5. Do **not** publish until the ES page is reviewed against this document
   and the English page it mirrors.
6. Enter the Polylang string translations from §3 (Languages → Strings
   translation) — this is what makes the header, footer, breadcrumbs and
   shared CTAs actually render in Spanish; the page content alone is not
   enough.
7. Verify Polylang's language switcher links the EN and ES page pairs
   correctly (visit each language, confirm the switcher lands on its real
   counterpart, not the Spanish homepage as a fallback).

This ticket does **not** publish or delete any WordPress page from
repository code — all of the above is a manual wp-admin action.

## 8. Known limitations

- The Portfolio Content option (`es_portfolio_home_content`) is a single
  global value. Any field an editor fills in there (e.g. a custom nav
  label override, or the footer note) applies to **both** languages —
  there is no per-language override for that admin screen. This is a
  pre-existing architectural limitation (documented earlier in
  `docs/BACKLOG.md`, Phase 5), not something introduced or fixed by this
  phase.
- Legacy PHP fallback templates (`template-parts/about-content.php`,
  `template-parts/how-i-work-detail.php`, `template-parts/contact-content.php`,
  `template-parts/hero-home.php`'s non-Gutenberg branch, etc.) still use
  WP core `esc_html_e()`/`_e()` with the theme text domain rather than
  `es__()`. This is pre-existing, correctly-structured WP i18n code, but
  there is no `.mo` compilation pipeline in this repo, so these strings
  would only ever render in English. This is low-risk: these templates
  are **legacy fallbacks** that render only when a page has no real
  Gutenberg content yet — since Home/About/How I Work/Connect all have
  real Gutenberg content (English today, and Spanish once the files in
  this phase are imported per §7), this fallback branch does not fire on
  either language's live page. Left unchanged per "do not clean unrelated
  backend code."
- Two small pre-existing hardcoded-language defaults were found and are
  documented, not changed (out of scope / low-risk / could not be fixed
  without a product decision): `estavillo/case-details`'s default
  `<summary>` text is the Spanish word "Detalles" (only used if a case
  omits the `summary` attribute — never happens in any current content,
  including the new ES files, which always set it explicitly); the Case
  Figure block's placeholder alt text defaults to Spanish
  ("Placeholder de imagen pendiente") for the same missing-image edge
  case, regardless of page language.
- Editor-only "List View" block names (`metadata.name`, e.g. "Background",
  "Key contributions — Trazur") were left in English inside the new
  `*-es.html` files. These are not visible on the frontend — only inside
  the block editor's List View — so they don't affect Spanish-language
  parity for site visitors. Translating them is a small future polish
  item, not attempted this phase to keep the diff focused on user-facing
  content.
- The Work page (`templates/page-work.php`) has no Gutenberg content file
  in either language yet — it still renders from `es_work_page_source()`
  (Case Study CPT query), which is already language-neutral in its
  structure (labels via `es__()`); this is unrelated to this phase.
- The Bachelor's degree title and the About final-project title are
  translated renderings of the current English authoritative page, not
  independently verified against an official transcript or thesis
  document — flagged for the project owner to confirm exact wording.
