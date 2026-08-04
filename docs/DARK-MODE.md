# Portfolio dark mode — architecture, toggle, safe preview

One child theme, one dark system, one global switch. No second theme, no
duplicated stylesheets, no per-page colors.

## 1. Why this exists (audit findings)

`tokens.css`'s `:root` is already dark by design, and `base.css`/`layout.css`/
`components.css` already apply it — but every rule in those three files is
scoped under `.es-page`, a wrapper `<div>` that only exists inside the six
"standalone" ESTAVILLO templates (Home, Work, About, How I Work, Connect,
single Case Study). Those templates print their own `<head>`/`<body>` and
never call `get_header()`/`get_footer()`, so they've been dark since Home v1
— no toggle involved, nothing to activate.

Every other context — a generic Gutenberg Page without one of those
templates, a single blog post, an archive, search results, 404 — has no
override in this child theme and renders through Kadence's own native
templates and chrome, which never picked up the `--es-*` tokens at all.
`tokens.css`/`base.css` are enqueued site-wide but are **inert** outside
`.es-page` (confirmed in `inc/enqueue.php`'s own comments) — that's the
actual root cause of the "still inherits light Kadence styles somewhere"
symptom, not a bug in the existing dark system.

## 2. Architecture

```
estavillo-child/assets/css/theme-dark.css   ← every selector prefixed body.es-theme-dark
estavillo-child/inc/theme-dark-mode.php     ← global switch + admin-only preview + body_class
estavillo-child/inc/enqueue.php             ← theme-dark.css enqueued site-wide, unconditionally
estavillo-portfolio-core/includes/home-content-options.php
                                             ← "Portfolio dark mode" admin field (Appearance section)
```

- **`.es-page`'s existing dark styling is untouched.** This phase adds
  coverage, it doesn't rebuild anything already working.
- **`theme-dark.css` is 100% class-gated.** Every single rule starts with
  `body.es-theme-dark` — with the class absent, the file has zero effect
  (verified: identical computed styles whether the stylesheet is loaded or
  not — see `dark-mode-validate.mjs`, "Estado A").
  It reuses the same tokens `.es-page` already uses (`--es-paper`,
  `--es-ink`, `--es-line`, `--es-accent`, etc.) and the same treatment
  patterns already approved inside `.es-page` (the border+radius image
  frame from `case-study.css`, the input styling from the Connect form in
  `pages.css`) — no new colors, no new component language.
- **One body-class decision, one place.** `es_theme_dark_mode_active()` in
  `inc/theme-dark-mode.php` is the single source of truth for "does THIS
  request render dark," consumed by one `body_class` filter. It's purely
  additive — it never removes or reorders any existing class.

## 3. The switch

**Location:** wp-admin → Case Studies → Portfolio Content →
**"Appearance — Portfolio dark mode"** (last section on the page, right
above Save). One checkbox: *"Enabled — apply dark mode site-wide for every
visitor."*

- **Default: Disabled.** Stored in the same `es_portfolio_home_content`
  option as everything else on that screen (key `theme_dark_mode`), bridged
  through the `es_theme_dark_mode` filter with the same
  "only override once this section was saved at least once" guard already
  used for `sticky_header` — so an environment where this was never touched
  behaves exactly as if it were explicitly Disabled.
- **Disabled:** the public site is unchanged. `body.es-theme-dark` is never
  added for a regular visitor; `theme-dark.css` stays inert.
- **Enabled:** every template gets `body.es-theme-dark` for every visitor,
  in both languages — global and language-independent by construction (the
  filter reads no post content, no Polylang state).

## 4. Admin-only preview (in and out)

Two ways in, both nonce-protected (`es_theme_preview_toggle` action) and
capability-gated (`current_user_can( 'manage_options' )`, re-checked on
every page load — not just when the preview was turned on):

1. **Portfolio Content screen** → "Preview (administrators only)" row →
   **"Preview dark mode →"** button, opens the live site in a new tab.
2. **Admin toolbar**, on any front-end page → **"Preview dark mode"** node
   (top right, next to the user menu) — reachable without going back to
   wp-admin first.

Clicking either sets a short-lived (`24h`), `httponly`, `SameSite=Lax`
cookie (`es_theme_preview=dark`) scoped to your browser only, then redirects
to the clean URL (no query string ever gets bookmarked or cached). Every
subsequent page load re-checks `manage_options` before honoring that
cookie — an anonymous visitor can never trigger or inherit preview, even if
they had the cookie value.

**Exit preview** — two equally-visible ways, whichever is closer:
- Admin toolbar → **"Exit dark preview"** (replaces the "Preview" node while
  active).
- Portfolio Content screen → **"Exit dark preview"** button (same section).

**Cache safety:** while previewing (global switch still Disabled),
`DONOTCACHEPAGE` + `nocache_headers()` fire so a page-cache plugin never
stores the dark-preview HTML and serves it to an anonymous visitor later.
This only fires on the preview branch — the real global "Enabled" state
needs no special cache handling, every visitor is meant to see it.

## 5. Coverage

See `assets/css/theme-dark.css` for the full rule set, organized exactly
like the ticket's own categories: Base (html/body/selection/focus),
Estructura (Kadence-native header/nav/footer/sidebar/breadcrumbs),
Contenido (headings/text/links/quotes/tables/figures/code), Formularios
(inputs/textarea/select/focus/disabled/autofill), Gutenberg (group/columns/
details/pullquote/cover/embed not already covered), Templates
(archive/search/404/pagination/comments).

**Images — hard rule, no exceptions:** every rule touching an `<img>` sets
only `background`/`border`/`border-radius` (the same integrated-frame
treatment already used for light Trazur screenshots inside `.es-page`).
Nothing in this phase sets `filter`, inverts, or darkens a single pixel of
any image, and nothing touches lightbox/zoom markup at all — verified by
the harness (`imgFilter === 'none'`).

## 6. Known limitations

- **Not verified against a live Kadence install.** Kadence itself isn't
  vendored in this repository (it's installed separately on the live
  site), so the "Estructura" selectors in `theme-dark.css`
  (`.site-header`, `.entry-content`, `.widget-area`, etc.) are common WP/
  Kadence conventions, not confirmed against Kadence's actual compiled CSS
  or exact specificity. The validation harness in this phase simulates a
  representative generic-page skeleton with those class names — real
  visual QA once this is deployed to the live Kadence install is still
  recommended before flipping the global switch on. If a specific Kadence
  element resists these rules, raise that one selector's specificity in
  `theme-dark.css` first; reach for `!important` only as a last resort.
- **Portfolio Content is a single global option** (documented repeatedly
  elsewhere in this repo, e.g. `docs/BACKLOG.md`) — the dark-mode switch is
  necessarily the same for every visitor/language; there is no and will
  never be a per-language dark-mode value, by design (the ticket asks for
  exactly this).
- Light mode as a public, user-facing feature is explicitly out of scope
  for this phase (`[data-theme='light']` already exists in `tokens.css`
  from earlier work, unrelated to and untouched by this phase's global
  toggle).
