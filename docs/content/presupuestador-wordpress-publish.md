# Presupuestador — WordPress publishing instructions

Step-by-step order for taking the Spanish and English case content from
this `docs/content/` folder into a real, published pair of Case Study
posts. Follow this order — the Spanish post goes first, then the English
post is created *as its Polylang translation*, not as an unrelated post.

Companion files:
- `presupuestador-case-study-es.html` — paste into the Spanish post
- `presupuestador-case-study-en.html` — paste into the English post
- `presupuestador-case-study-fields.md` — exact value for every field
  below, for both languages
- `presupuestador-assets-plan.md` — what images are still needed (none
  exist yet — every image placeholder below stays a placeholder until
  those are produced)

---

## 1. Create or edit the Spanish Case Study

In wp-admin: **Case Studies → Add New**. Set the title to **Presupuestador**
(see the field sheet for the exact excerpt/eyebrow/etc.). If a draft post
for this case already exists, open that one instead of creating a
duplicate.

## 2. Fill every Case Study meta field (Spanish)

In the "Case details" meta box below the editor, fill in — using the
exact values from `presupuestador-case-study-fields.md` → "Spanish post":

- Eyebrow / category
- Label / status
- Role
- Tools
- Period
- Hero layout (start with `split-right`, the default — see the field
  sheet for when to switch)
- Leave **Show on Home → Selected Work** unchecked for now, and **Feature
  this case on Home** unchecked — both should wait until the featured
  image is real (see step 4 and the field sheet's reasoning)

Also set the native **Excerpt** (sidebar panel) and **Case Tags**
(sidebar panel) to the values in the field sheet.

## 3. Paste the Spanish HTML into a Custom HTML block

In the block editor body, add a **Custom HTML** block. Open
`presupuestador-case-study-es.html`, and paste **everything below the
closing `-->` of its instructional comment** — not the comment block
itself. Do not split it across multiple Custom HTML blocks; one block
keeps the source easy to diff/update later.

## 4. Upload / set featured image

No real featured image exists yet (see the asset plan). Leave the
Featured Image panel empty for now — the theme's placeholder frame
(`{asset: presupuestador}`) renders cleanly with no image set, so the
page is safe to preview and even publish before this is ready. Come back
to this step once an image from the asset plan's priority list exists.

## 5. Fill the Case index

In the "Case details" meta box, paste the Spanish Case Index block from
`presupuestador-case-study-fields.md` → "Case index (Spanish)" into the
**Case index** textarea. Double-check each `#anchor` matches an `id=` in
the pasted HTML exactly (they do, as shipped — this is only worth
re-checking if you edit the HTML's section IDs later).

## 6. Publish and verify anchors

Publish (or update) the post, then open it on the front end:

- Confirm the sticky in-page index appears below the header and lists
  all 13 entries in order.
- Click through several index links and confirm each one scrolls to the
  matching section (not to the top of the page, not to the wrong
  section).
- Confirm no section is missing and no section repeats.

## 7. Create the English translation through Polylang

With the Spanish post open, use Polylang's language box (top of the
sidebar, or wherever your Polylang version places it) and click **"+ Add
translation"** for English. This creates a new post pre-linked as the
Spanish post's translation — don't create an unrelated new Case Study
post for the English version, or the two won't be connected as
translations of each other.

## 8. Fill the English fields independently

The new English post starts empty (Polylang links translations but
doesn't copy content). Repeat step 2 using
`presupuestador-case-study-fields.md` → "English post" values. Tags are
intentionally the **same** tag terms as the Spanish post (Case Tags are
language-neutral in this theme — see `EDITABILITY-PLAN.md` → "Polylang")
so don't create new English-only tag terms.

## 9. Paste the English HTML

Same as step 3, but with `presupuestador-case-study-en.html` into the
English post's own Custom HTML block.

## 10. Link both translations

If step 7 was followed, the two posts are already linked. Confirm this:
open either post, check the Polylang language box shows the other
language as a linked translation (not "+ Add translation" again, which
would mean it created a second, disconnected post by mistake).

## 11. Check Home Featured Case and Selected Work

With both posts published but **Show on Home** and **Feature on Home**
left unchecked (per step 2), Home should look exactly as it did before —
confirm this. Once a real featured image exists and you're ready to
surface the case:

- Check **Show this case in Home → Selected Work** to have it appear in
  the Work page and Home's Selected Work teaser.
- Only check **Feature this case on Home** once you're fully satisfied
  with the copy and the image — this is the single most visible slot on
  Home.

## 12. Test desktop, mobile, sticky header, breadcrumbs, and language switcher

For **both** language versions of the post:

- **Desktop and mobile:** scroll the full page, confirm all 13 sections
  render cleanly, the `.es-case-decisions` cards (AI section) and
  `.es-case-status` columns (Results section) stack correctly on mobile,
  and the `.es-case-details` accordions (Next steps) open/close.
- **Sticky header:** scroll down and confirm the site header stays
  pinned to the top of the viewport (this was a real bug, now fixed —
  see `ROADMAP.md` → Sprint 4H — worth double-checking on a real case
  page, not just the templates it was tested against).
- **Breadcrumbs:** confirm "Home / Work / Presupuestador" (or "Home /
  Trabajo / Presupuestador" in Spanish, depending on how the "Work" nav
  link is labeled) appears above the sticky index and that "Work" links
  to wherever the Work nav link currently points.
- **Language switcher:** with Polylang active, confirm the header's EN/ES
  switcher on the Spanish post links to the English post and vice versa
  — not to Home or to a 404.
