# QA checklist — Estavillo Portfolio (Kadence child theme)

Manual checks to run in a real WordPress install after any theme change,
before considering a ticket done. Headless/automated checks (Playwright,
PHP lint, JS syntax) catch a lot, but these need a human eye in an actual
browser against the live WP install.

Not every ticket touches every area — use judgment on which subset applies,
but always re-check the "no blue" and cache items, since those have
regressed more than once.

---

## Desktop

- [ ] Hover states across header (logo, nav links), Featured Case CTA,
      Selected Work cards/CTAs, About, Connect/footer — all read as
      green/ink, never blue.
- [ ] Keyboard-only focus (Tab) shows the green focus ring where expected,
      and only on actual keyboard focus (`:focus-visible`), not on mouse
      click.
- [ ] Secondary text links (e.g. "See how I work") stay plain text on
      hover/focus — no border, background, or button box appears.
- [ ] Selected Work CTA hover: confirm the link/arrow is neutral/white at
      rest and turns green only on hover (see BACKLOG P0 item — currently
      failing).
- [ ] Footer: visual check of spacing/rhythm — does it read as a
      deliberate close to the page, not an afterthought?
- [ ] Hero variant actually changes when a different variant is selected
      in the Customizer (not stuck on a cached/previous variant).
- [ ] Typography preset changes only when explicitly selected in the
      Customizer — no unrelated type shifts.

## Mobile (real device or accurate emulation, not just resize)

- [ ] Mobile menu opens and closes cleanly (tap menu button, tap X,
      Escape, tap a link, tap outside the panel).
- [ ] Mobile menu active/pressed states never show blue on any link,
      especially "Work" (see BACKLOG P0 item — currently failing).
- [ ] Mobile menu scroll lock engages while open (background doesn't
      scroll behind the panel).
- [ ] iPhone-width hero: primary CTA and "See how I work" secondary link
      lay out correctly — confirm no unintended wrap of the secondary link
      below the primary CTA on real phone widths (see BACKLOG P0 item).
- [ ] Hero constellation is legible and not overwhelming on small screens.

## Caching / infra

- [ ] Hard refresh / cache purge after deploying a theme update — confirm
      the new JS/CSS is actually being served (check asset `?ver=` query
      string reflects the current file, not a stale cached version).
- [ ] If a CDN or object cache is in front of the site, purge it as part
      of verifying the change, not just the browser cache.

## Sign-off

- [ ] No blue anywhere (final pass, after all above).
- [ ] Nothing in this checklist was skipped without a documented reason.
