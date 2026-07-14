<?php
/**
 * Pattern content — Presupuestador Case Structure (EN).
 *
 * Block transcription of the master
 * docs/content/presupuestador-case-study-en.html: same 13 sections, same
 * anchors, same honest copy and placeholders. See case-patterns.php for
 * the decisions (cols → sequential flow, one pattern per language).
 *
 * @package estavillo-portfolio-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return <<<'CONTENT'
<!-- wp:estavillo/case-section {"anchor":"overview","label":"Fig. 00 — Overview","heading":"A quote that depended on one person's head, turned into criteria that can be written down, questioned, and improved.","lead":"Presupuestador is a decision-support system for a metal fabrication workshop in Montevideo. The goal was never a generic quoting app — it was making explicit a pricing judgment that had only ever lived in one person's head, and putting it within reach of the rest of the team without losing the expert judgment behind it."} -->
<!-- wp:estavillo/case-ladder {"steps":[{"label":"Operational diagnosis","state":"done"},{"label":"Documented criteria","state":"done"},{"label":"Google Sheets MVP","state":"done"},{"label":"Calibration against the estimator","state":"done"},{"label":"App Alpha","state":"active"},{"label":"Team-wide adoption","state":"future"}]} /-->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">Where the project actually stands: App Alpha is built and answering real quotes today; the step after it — the whole team quoting independently — is the goal, not yet a measured result (more in Results and Limitations below).</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"context","label":"Fig. 01 — Context","heading":"A workshop that quotes well — just slowly, and from one person's head.","lead":"Guzmán Villalba is a metal fabrication workshop in Montevideo. Every job — from a custom staircase railing to a full production run of parts — starts with a quote. For years, that quote came from one estimator's judgment: material, labor, machine time, and margin, priced from memory and experience, not from a written system."} -->
<!-- wp:paragraph -->
<p>That worked while volume stayed manageable. Then jobs started queuing up behind one person. Someone new on the team couldn't estimate without shadowing that process for months. And because the judgment lived only in his head, it couldn't be questioned, versioned, or improved — only repeated.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"problem","label":"Fig. 02 — The operational problem","heading":"A slow quote can lose the job before any metal gets cut.","lead":"A client calling for a rough number needs a fast, defensible answer. If the only path is a complete formal quote — and that quote depends on one specific person having time that week — the answer arrives late, or doesn't arrive at all."} -->
<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Pricing judgment wasn't written down anywhere — it lived entirely in one person's experience.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>There was no fast way to give an orientative range without building the full quote.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Adding people to the estimating side of the business meant, in practice, that one person had to walk each of them through every case.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>[DATA PENDING VALIDATION — if a real average response time exists (e.g. typical days of wait for a formal quote), replace this paragraph with that concrete figure. No number is included here because there's no verifiable source for one in this repository.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"discovery","label":"Fig. 03 — Understanding the real workflow","heading":"Watching quotes get built, not just asking how they get built."} -->
<!-- wp:paragraph -->
<p>Interviews alone undersold how much judgment was involved — the estimator himself struggled to put his own rules into words. Shadowing several real quotes end to end surfaced the actual variables in play: material type and thickness, cut complexity, finishing steps, machine availability that week, and a margin that flexed with client relationship and urgency.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The clearest finding: the judgment wasn't arbitrary, it was <em>consistent but undocumented</em> — which meant it could be captured as an explicit model, rather than replaced by a generic pricing formula that would have ignored the workshop's real constraints.</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-figure {"variant":"standard","tag":"01","caption":"[SCREENSHOT PENDING — photo from the shadowing sessions or synthesized research notes.]","placeholderLabel":"discovery-notes","alt":"Placeholder for discovery research photo or notes"} /-->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"system","label":"Fig. 04 — Design hypothesis and architecture","heading":"One pricing model, several surfaces.","lead":"The starting hypothesis: if the estimator's judgment could be made explicit, it didn't need replacing — it needed a shape that someone else could use, question, and improve. Rather than building one monolithic app up front, the system separates the <em>pricing logic</em> (versioned, inspectable, owned by the workshop) from the <em>surfaces</em> that read it — so the estimator could keep working from day one, and the interface could evolve without touching the logic underneath."} -->
<!-- wp:estavillo/case-figure {"variant":"browser","browserLabel":"system-diagram","placeholderLabel":"architecture-diagram","alt":"Placeholder for system architecture diagram"} /-->

<!-- wp:paragraph -->
<p>Inputs (material, dimensions, finish, urgency) feed a single pricing model. That model returns both a fast orientative range and, once confirmed, a full itemized quote. The same model is queried from the Sheets MVP and, later, from the Alpha app — switching surfaces never means re-deriving the logic.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>[DIAGRAM PENDING — replace the placeholder with the real input → pricing model → output flow once it exists as a final asset.]</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-taxonomy {"root":"Pricing model — the inputs it reads","items":[{"title":"Material &amp; thickness","meta":"cost driver"},{"title":"Cut complexity","meta":"labor driver"},{"title":"Finishing steps","meta":"labor driver"},{"title":"Machine availability that week","meta":"capacity, not price"}],"modsLabel":"Adjusted<br>by…","mods":["Client relationship","Urgency"]} /-->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">The same variables surfaced during discovery — material and thickness, cut complexity, finishing, and machine time set the cost and labor pull; client relationship and urgency adjust it from there.</p>
<!-- /wp:paragraph -->

<!-- wp:estavillo/case-stats {"items":[{"num":"1","label":"single, versioned pricing model"},{"num":"2","label":"surfaces reading it (Sheets and App Alpha)"},{"num":"2","label":"output types per query: fast range and itemized quote"}]} /-->

<!-- wp:paragraph {"className":"es-case-caption"} -->
<p class="es-case-caption">These three numbers describe system design decisions (how many models, how many surfaces, how many output types) — not business metrics about the workshop.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"mvp","label":"Fig. 05 — MVP in Google Sheets + Apps Script","heading":"Ship the logic before the interface.","lead":"The first version of the pricing model lived entirely in Google Sheets with Apps Script — deliberately, so the estimator could correct and extend the logic himself without waiting on a development cycle, and so the system could be validated against real quotes before any interface was built."} -->
<!-- wp:estavillo/case-figure {"variant":"browser","browserLabel":"presupuestador.xlsx — pricing model v1","placeholderLabel":"mvp-sheets-screenshot","alt":"Placeholder for MVP spreadsheet screenshot"} /-->

<!-- wp:paragraph -->
<p>Every quote that ran through the sheet was logged. That growing base of quotes — not a business number, but the system's own working material — is what later made it possible to compare the model against the estimator's own judgment and adjust its weighting before moving to a dedicated interface.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"testing","label":"Fig. 06 — Testing and iteration","heading":"The estimator as the reference, not as a test subject.","lead":"Validation wasn't a round of usability testing with outside users — it was comparing, quote by quote, what the model proposed against what the estimator himself would say. When they agreed, that confirmed a rule. When they didn't, the gap was the signal to revisit the model, not to dismiss the disagreement."} -->
<!-- wp:estavillo/case-timeline {"items":[{"title":"Run a real quote through the model","text":"Same case, two judgments side by side: the model's and the estimator's."},{"title":"Flag matches and gaps","text":"Gaps weren't averaged away or ignored — each one was reviewed to find the missing or mis-weighted variable."},{"title":"Adjust the model, not the judgment","text":"The goal was for the model to learn to explain the estimator's judgment — not the other way around."}]} /-->

<!-- wp:paragraph -->
<p>[DATA PENDING VALIDATION — if a real accuracy or coverage figure exists for how often the model matched the estimator's judgment within an acceptable range, add it here with its source. Until then, this process is described qualitatively because no confirmed figure exists.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"app-alpha","label":"Fig. 07 — App Alpha","heading":"From a spreadsheet only one person could drive to a tool the team can use."} -->
<!-- wp:estavillo/case-figure {"variant":"browser","browserLabel":"app.presupuestador — alpha","placeholderLabel":"app-alpha-screenshot","alt":"Placeholder for App Alpha screenshot"} /-->

<!-- wp:estavillo/case-timeline {"items":[{"title":"Same model, new front door","text":"The Alpha app reads the same pricing model validated in Sheets — no logic was rewritten, only re-surfaced."},{"title":"Guided input instead of a blank spreadsheet","text":"A short structured form replaces free-form cells, so anyone on the team — not just the original estimator — can produce a consistent quote."},{"title":"Fast range first, precise quote second","text":"The app answers a client-facing \"roughly how much\" in seconds, then supports building the full itemized quote afterward."}]} /-->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"ai","label":"Fig. 08 — The role of AI, and where human judgment stays essential","heading":"AI assists the quote. It never sets the price on its own.","lead":"AI is used in three narrow, inspectable places in the flow — each one assistive, each one reviewable by the person actually building the quote."} -->
<!-- wp:estavillo/case-decisions {"taskLabel":"Task","guardrailLabel":"Guardrail","items":[{"num":"01","title":"Reading raw input","task":"Extract dimensions or material notes from a photo or a client's free-text description.","guardrail":"Always shown back to the estimator as editable fields before the model runs."},{"num":"02","title":"Suggesting a range","task":"Propose an orientative range from past quotes with similar inputs.","guardrail":"Labeled explicitly as a suggestion — the pricing model's own logic sets the final number, not the AI."},{"num":"03","title":"Flagging anomalies","task":"Flag a quote that falls far outside the historical pattern, so someone reviews it before it goes out.","guardrail":"Advisory only — it never blocks sending a quote, it only asks for a second look."}]} /-->

<!-- wp:paragraph -->
<p>The system doesn't replace the workshop's estimator — it structures his judgment and puts it into operation. Ranges are for answering fast and doing an early viability check; an unusual case, with conditions the model doesn't cover well yet, still needs a person's judgment before it's confirmed.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"results","label":"Fig. 09 — Current state","heading":"Where the system stands today.","lead":"Presupuestador is a system in real use, not a finished product. Here's what can be said with confidence today, and what still needs more evidence before it can be claimed as a result."} -->
<!-- wp:estavillo/case-status {"columns":[{"heading":"True today","style":"done","items":["Pricing judgment is written down, versioned, and no longer lives only in one person's head.","A single model answers from both Sheets and the Alpha app.","Every quote that runs through the system gets logged — it doesn't disappear into a conversation or a piece of paper."]},{"heading":"Needs more evidence","style":"attention","items":["[DATA PENDING VALIDATION — real impact on client response time, once there's enough usage data to measure it with confidence.]","[DATA PENDING VALIDATION — how many people on the team, beyond the original estimator, already quote independently using the system.]","Unusual cases still rely on the estimator's direct judgment — the system doesn't have enough history yet to cover them with confidence."]}]} /-->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"limitations","label":"Fig. 10 — Limitations","heading":"What the system doesn't do yet."} -->
<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Unusual or fully custom jobs still get resolved with manual judgment — the model covers the common pattern well, not the exceptional case.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>There's no flow yet designed mobile-first for the shop floor.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>The AI-suggested range needs more quote history to stay accurate as material costs shift.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>The system depends on the estimator continuing to review and correct the model — the judgment doesn't maintain itself.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"learnings","label":"Fig. 11 — What I learned","heading":"The hard part was never the screen."} -->
<!-- wp:paragraph -->
<p>The real design work was making explicit a judgment that one person had never had to put into words — clear enough to write down, argue with, and correct. The app was, by comparison, the easiest step once that was done.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>I also learned to distrust the temptation to replace expert judgment with a tidy formula: a model that ignores the workshop's real constraints — the client relationship, the urgency, machine availability that week — would have been simpler to build, and worse. The system had to learn from the estimator, not correct him from the outside.</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-section -->

<!-- wp:estavillo/case-section {"anchor":"next","label":"Fig. 12 — Next steps","heading":"Where this goes from here."} -->
<!-- wp:estavillo/case-details {"summary":"Extend the model to unusual and fully custom jobs"} -->
<!-- wp:paragraph -->
<p>[DATA PENDING VALIDATION — describe the concrete plan for covering the non-standard jobs that still rely on manual judgment today.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-details -->

<!-- wp:estavillo/case-details {"summary":"Build a flow designed for the shop floor"} -->
<!-- wp:paragraph -->
<p>[DATA PENDING VALIDATION — describe the concrete plan for a lightweight quoting flow usable directly from a phone on the workshop floor.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-details -->

<!-- wp:estavillo/case-details {"summary":"Formalize the AI-assisted range with more history"} -->
<!-- wp:paragraph -->
<p>[DATA PENDING VALIDATION — describe how the historical quote base will keep growing to improve the suggested range's accuracy over time.]</p>
<!-- /wp:paragraph -->
<!-- /wp:estavillo/case-details -->
<!-- /wp:estavillo/case-section -->
CONTENT;
