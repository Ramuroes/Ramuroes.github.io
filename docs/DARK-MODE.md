# Portfolio dark mode — architecture

One child theme, one dark system, applied everywhere. No second theme, no
duplicated stylesheets, no per-page colors, and — since the closing
iteration — no switch either: dark is the portfolio, not a setting.

> **Changed in the closing iteration.** This started as an opt-in global
> toggle so the dark system could be reviewed before going live. It is now
> the permanent baseline. Sections 3 and 4 below describe what replaced the
> toggle and why; the mechanism they used still exists in code, inert, as
> the plumbing for a future light mode.

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
estavillo-child/assets/css/tokens.css       ← :root is dark; [data-theme='light'] exists but nothing emits it
estavillo-child/assets/css/base.css         ← html/body surface (theme), .es-page (layout only)
estavillo-child/assets/css/theme-dark.css   ← every selector prefixed body.es-theme-dark
estavillo-child/inc/theme-dark-mode.php     ← mode resolution + body_class (+ inert preview plumbing)
estavillo-child/inc/enqueue.php             ← theme-dark.css enqueued site-wide; es_uses_estavillo_chrome()
```

Two layers do two different jobs, and it matters which is which:

- **`html, body` in `base.css`** paints the surface (`--es-paper` /
  `--es-ink`). This is what makes *any* view dark, including one this theme
  has no template for. Before the closing iteration this lived on `.es-page`,
  so a view without that wrapper fell back to Kadence's light.
- **`theme-dark.css`** dresses the *Kadence-native chrome* — header, nav,
  footer, entry content, forms, archives. It needs `body.es-theme-dark`, and
  that class is now always emitted.

- **`.es-page`'s existing dark styling is untouched.** This phase adds
  coverage, it doesn't rebuild anything already working.
- **`theme-dark.css` is 100% class-gated, and the gate is deliberately
  narrow.** Every rule starts with `body.es-theme-dark`, and
  `es_theme_dark_body_class()` emits that class **only on views this theme
  does not render itself** (`! es_uses_estavillo_chrome()`).

  This is not a detail. Those selectors were written for Kadence's markup
  and carry Kadence-beating specificity (`body.es-theme-dark a` is 0-2-1),
  so inside `.es-page` they win against design-system rules that are 0-1-0.
  Emitting the class everywhere was measured against three views and
  overrode 5 to 15 elements per view: the eyebrow lost its accent green and
  came out flat ink, a muted excerpt jumped to full ink, `<strong>` ended up
  *dimmer* than the paragraph containing it, and every keyboard-focused link
  dropped to `opacity: .85`. None of that was a design decision. With the
  narrow gate, the portfolio pages are governed by their own system and
  `theme-dark.css` does exactly what its header says it does.

  `body.es-theme--dark` (double dash) is the separate, always-emitted
  *theme* class — it states which mode is active and is the stable hook for
  a future light mode.
  It reuses the same tokens `.es-page` already uses (`--es-paper`,
  `--es-ink`, `--es-line`, `--es-accent`, etc.) and the same treatment
  patterns already approved inside `.es-page` (the border+radius image
  frame from `case-study.css`, the input styling from the Connect form in
  `pages.css`) — no new colors, no new component language.
- **One body-class decision, one place.** `es_theme_dark_mode_active()` in
  `inc/theme-dark-mode.php` is the single source of truth for "does THIS
  request render dark," consumed by one `body_class` filter. It's purely
  additive — it never removes or reorders any existing class.

## 3. There is no switch (and why the old one was removed)

`es_theme_dark_mode_enabled()` returns `true`. It still routes through
`apply_filters( 'es_theme_dark_mode', true )`, so the extension point
survives — but nothing in the plugin bridges that filter anymore, and there
is no field for it in wp-admin.

The **"Appearance — Portfolio dark mode"** checkbox on the Portfolio Content
screen is gone, replaced by a one-line note saying dark is the theme. Three
reasons, in order of how much they mattered:

1. **Its "off" state was a bug, not an alternative.** Light tokens exist in
   `tokens.css` under `[data-theme='light']`, but nothing emits that
   attribute and `theme-dark.css` has no light counterpart for the
   Kadence-native chrome. Now that `html, body` carries the dark surface
   unconditionally, unchecking the box produced a half-migrated site: dark
   background, undressed Kadence header and footer. A control whose off
   position is broken is a trap, not a setting.
2. **A stored `'0'` would have silently won.** The old bridge only deferred
   to the theme default when the key was *absent* (`array_key_exists`), and
   the save handler wrote `theme_dark_mode = '0'` on every submit of that
   section. The screen's own help text told the admin to leave it unchecked
   "until the content migration is finished" — so on a real install the
   stored value is almost certainly `'0'`. Flipping only the theme default
   would have shipped a no-op.
3. **Nothing to decide.** One visual system means no choice to expose.

**Nothing was deleted from storage.** The `theme_dark_mode` key already
saved in `es_portfolio_home_content` stays where it is, untouched and
unread. If a real light mode ever arrives, the filter is the switch and this
section becomes a two-way choice again.

## 4. Admin-only preview — present, inert, kept on purpose

The preview mechanism in `inc/theme-dark-mode.php` (nonce + `manage_options`
+ short-lived `httponly`/`SameSite=Lax` cookie + `DONOTCACHEPAGE`) still
exists and is intentionally not removed, but **it cannot fire today**:
`es_theme_dark_mode_active()` returns `true` at its first line, the admin-bar
node returns early, and the Portfolio Content row that offered it is gone
along with the rest of that section.

It is kept because it is exactly the mechanism a future light mode needs:
review a whole visual system on the real public site, signed and
capability-gated, without exposing it to visitors, and without a page cache
leaking the preview HTML to anyone else.

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
  recommended. This is now the *live* look, not a preview — so if a specific
  Kadence element resists these rules it is visible to visitors. Raise that
  one selector's specificity in `theme-dark.css` first; reach for
  `!important` only as a last resort. **This is the main open risk of making
  dark the default.** The views to check first are the ones this theme has
  no template for: search results, category/tag archives, a single blog
  post, and any Page saved without one of the six ESTAVILLO templates.
- **The mode is global and language-independent by construction** — it reads
  no post content and no Polylang state, so EN and ES can never diverge.
  There is no and will never be a per-language value.
- **Light mode is unfinished, not merely disabled.** `[data-theme='light']`
  in `tokens.css` covers the token layer only; there is no light counterpart
  for `theme-dark.css`'s Kadence-native rules, and nothing emits the
  attribute. Treat those tokens as a starting point, not a working mode.
- **Most Kadence-native views are no longer Kadence-native.** The closing
  iteration added `404.php`, `search.php`, `archive.php`, `index.php`,
  `page.php` and `single.php` to the child theme, all rendering through
  `template-parts/generic-document.php` with the ESTAVILLO chrome. Those
  views no longer depend on `theme-dark.css` at all — they are dark for the
  same reason Home is. `theme-dark.css` remains the safety net for whatever
  Kadence still serves (embedded widgets, plugin-owned templates such as a
  Fluent Forms confirmation page, `wp-login`-adjacent screens), which is why
  it is still enqueued site-wide and still worth QA'ing against the live
  install.
