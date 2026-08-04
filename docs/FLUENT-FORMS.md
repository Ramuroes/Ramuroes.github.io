# Connect page — Fluent Forms setup

The Connect page's "Send a message." column is prepared for **Fluent
Forms Free** (target chosen in the Connect revision ticket). The plugin is
**not installed from the repository** — no plugin is ever installed
automatically here, this is a WordPress admin action for the site owner.

Until a form exists, the column shows a clearly-marked placeholder (dashed
border, reduced opacity — the same "reserved, not yet real" visual
language used for other empty states on this site). See the "ACTION
REQUIRED" comment at the top of `docs/content/connect-gutenberg-en.html`
and the `es-contact-form-placeholder` Group inside the "Contact form"
column.

## Setup steps

1. In wp-admin, go to **Plugins → Add New**, search "Fluent Forms", install
   and activate **Fluent Forms** (the free version — no paid plan
   required).
2. Open **Fluent Forms → All Forms → + Add New Form** and create a form
   with exactly these fields, in this order:
   - **Name** (text)
   - **Email** (email)
   - **Message** (textarea)
   - **Submit** button
   Do not add phone, company, job title, budget, project type, newsletter
   signup, file upload, or a visible CAPTCHA — the approved direction for
   this page is intentionally minimal.
3. In the form's **Settings → Confirmation**, set the message shown after
   a successful submission (e.g. "Thanks — I'll get back to you within a
   few working days.").
4. In **Settings → Email Notifications**, confirm the recipient email is
   set to the address that should receive submissions (defaults to the
   site admin email; change it if that's not the right inbox).
5. Back in the Connect page (Pages → Connect → Edit), delete the
   **"Form placeholder" Group** inside the "Contact form" column and
   insert the **Fluent Forms** Gutenberg block in its place (search
   "Fluent Forms" in the block inserter).
6. In the block's settings, select the form created in step 2.
7. Update the page, then open it on the front end (not just the editor
   preview) and submit a real test entry with a real name/email/message.
8. Confirm the submission arrives at the configured recipient inbox
   (check spam/junk too — first-time sends from a new WordPress install
   sometimes land there) and that the on-page confirmation message from
   step 3 appears after submitting.

## Styling

`estavillo-child/assets/css/pages.css` already includes scoped CSS for
Fluent Forms' typical markup (`.fluentform`, `.ff-el-input--label`,
`.ff-el-form-control`, `.ff-btn-submit`) plus generic `input`/`textarea`/
`label`/`button[type=submit]` selectors as a fallback, so the form should
already look correct once inserted: compact uppercase mono labels, dark
fields matching the portfolio's tokens, a comfortable textarea, and a
green-accent outline submit button matching the rest of the site's
buttons. All of it is scoped under `.es-contact-form` (the Connect page's
own wrapper) — **it never applies to a Fluent Forms instance used
elsewhere on the site.**

If Fluent Forms' real markup uses different class names than assumed here
once it's actually installed, the generic element selectors
(`input`/`textarea`/`label`/`button[type=submit]`) still apply as a
baseline — check the rendered form and add the plugin's actual classes to
`.es-contact-form`'s rules in `pages.css` if extra polish is needed.
