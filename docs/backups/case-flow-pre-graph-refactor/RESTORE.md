# Case Flow — backup before the graph refactor

Snapshot of the **complete** Case Flow implementation as it shipped in
commit `9b88d39` (theme `0.2.38`, plugin `1.5.26`), taken immediately
before restructuring the component from a linear node list into a real
flow graph (decisions with real branches, rejoins and loops).

This is the version with: serpentine desktop grid, entrance animation,
section eyebrow, Start/End context badges, and branch "satellite" cards.

## What is in here

| File in this folder | Restores to |
| --- | --- |
| `case-flow.css` | `estavillo-child/assets/css/case-flow.css` |
| `case-flow.js` | `estavillo-child/assets/js/case-flow.js` |
| `render.php` | `estavillo-portfolio-core/blocks/case-flow/render.php` |
| `edit.js` | `estavillo-portfolio-core/blocks/case-flow/edit.js` |
| `block.json` | `estavillo-portfolio-core/blocks/case-flow/block.json` |
| `trazur-gutenberg-es.html` | `docs/content/trazur-gutenberg-es.html` |
| `trazur-gutenberg-en.html` | `docs/content/trazur-gutenberg-en.html` |

That is every file the component owns. Nothing else in the theme or the
plugin participates in Case Flow.

## How to restore

From the repository root:

```sh
B=docs/backups/case-flow-pre-graph-refactor
cp $B/case-flow.css  estavillo-child/assets/css/case-flow.css
cp $B/case-flow.js   estavillo-child/assets/js/case-flow.js
cp $B/render.php     estavillo-portfolio-core/blocks/case-flow/render.php
cp $B/edit.js        estavillo-portfolio-core/blocks/case-flow/edit.js
cp $B/block.json     estavillo-portfolio-core/blocks/case-flow/block.json
cp $B/trazur-gutenberg-es.html docs/content/trazur-gutenberg-es.html
cp $B/trazur-gutenberg-en.html docs/content/trazur-gutenberg-en.html
```

Then rebuild both ZIPs and re-import the two Trazur contents in
WordPress (the content files carry the block attributes, so restoring
the code without the content — or the other way round — leaves the two
halves out of sync).

## Equivalent git restore

The same state is commit `9b88d39` on
`claude/estavillo-child-theme-foundation-wipcr9`:

```sh
git checkout 9b88d39 -- estavillo-child/assets/css/case-flow.css \
  estavillo-child/assets/js/case-flow.js \
  estavillo-portfolio-core/blocks/case-flow/ \
  docs/content/trazur-gutenberg-es.html \
  docs/content/trazur-gutenberg-en.html
```
