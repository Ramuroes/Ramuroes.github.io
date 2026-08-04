/**
 * estavillo/case-section — editor.
 *
 * Contenedor de capítulo FLEXIBLE: label/heading/lead (RichText dedicados)
 * + InnerBlocks totalmente libre — sin allowedBlocks, sin template, sin
 * templateLock. El editor inserta y reordena lo que necesite (Heading,
 * Paragraph, List, Image, Gallery, Group, Row, Stack, Columns/Column
 * nativos de Gutenberg, y los bloques Estavillo Case Study existentes)
 * con los controles normales de bloques — este archivo no interviene en
 * esa composición interna en absoluto.
 *
 * Corrección (sprint de corrección arquitectónica): la versión anterior
 * imponía regiones fijas Contenido/Media con templateLock y reestructuraba
 * los hijos automáticamente al cambiar de layout. Eso resultó demasiado
 * rígido en uso real y — combinado con un bug de CSS heredado — dejaba un
 * hueco vacío a la derecha. Esta versión solo controla tres cosas a nivel
 * capítulo (Width / Chapter spacing / Chapter divider), con nombres
 * humanos, nunca columnas ni píxeles; la composición interna es 100%
 * Gutenberg nativo.
 *
 * Corrección de compatibilidad (sprint de anidamiento): el inspector solo
 * ofrece Content/Reading — Wide se consolidó en Content (mismo resultado
 * visual siempre) y se sacó de la UI. Un bloque YA GUARDADO con
 * "layout":"wide" sigue abriendo sin error: WIDTH_LABELS ya no tiene la
 * clave "wide", así que sectionClasses()/el chip de variante caen solos
 * al fallback "content" (mismo mecanismo ya probado con los valores
 * legacy split-* de la corrección anterior). Case Section además ahora
 * respeta el ancho de su padre inmediato cuando está anidado adentro de
 * un Column/Group/Row/Stack nativo (fix en case-study.css) — este archivo
 * no necesita saber si está anidado o no.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var RichText = wp.blockEditor.RichText;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;

	// Sin "wide": consolidado en "content" (mismo resultado visual). Un
	// bloque guardado con layout:"wide" no tiene match acá — sectionClasses()
	// y el chip de variante caen solos al fallback 'content' más abajo.
	var WIDTH_LABELS = {
		content: __('Content', 'estavillo-portfolio-core'),
		reading: __('Reading', 'estavillo-portfolio-core'),
	};
	var SPACING_LABELS = {
		compact: __('Compact', 'estavillo-portfolio-core'),
		standard: __('Standard', 'estavillo-portfolio-core'),
		spacious: __('Spacious', 'estavillo-portfolio-core'),
	};

	function sectionClasses(a) {
		var layout = WIDTH_LABELS[a.layout] ? a.layout : 'content';
		var c = 'es-case-section es-case-section--' + layout + ' es-case-section--sp-' + (a.spacing || 'standard');
		if (false === a.divider) {
			c += ' es-case-section--no-divider';
		}
		return c;
	}

	wp.blocks.registerBlockType('estavillo/case-section', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });
			// Sin allowedBlocks/template/templateLock: cualquier bloque puede
			// insertarse acá, incluidos Columns/Column nativos.
			var innerProps = useInnerBlocksProps({ className: 'es-caseb-flat' });

			var variantChip = el(
				'div',
				{ className: 'es-caseb-variant' },
				(WIDTH_LABELS[a.layout] || WIDTH_LABELS.content) + ' · ' + (SPACING_LABELS[a.spacing] || SPACING_LABELS.standard)
			);

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Section', 'estavillo-portfolio-core'), initialOpen: true },
						el(TextControl, {
							label: __('Anchor (id)', 'estavillo-portfolio-core'),
							help: __('Sin #. Tiene que coincidir con la línea correspondiente del campo "Case index" (Label|#anchor).', 'estavillo-portfolio-core'),
							value: a.anchor,
							onChange: function (v) {
								set({ anchor: v });
							},
						})
					),
					el(
						PanelBody,
						{ title: __('Chapter', 'estavillo-portfolio-core'), initialOpen: true },
						el(SelectControl, {
							label: __('Width', 'estavillo-portfolio-core'),
							help: __('Content: ancho completo del padre inmediato (el body del caso, o su columna si está anidado en Columns/Group/Row/Stack) — flexible, default. Reading: todo el capítulo a medida de lectura (~68–72ch), nunca más ancho que el padre. Para un artefacto ancho (captura, diagrama), usá Content — directo en el body o adentro de un Column — no hace falta una opción aparte.', 'estavillo-portfolio-core'),
							value: a.layout,
							options: [
								{ label: WIDTH_LABELS.content, value: 'content' },
								{ label: WIDTH_LABELS.reading, value: 'reading' },
							],
							onChange: function (v) {
								set({ layout: v });
							},
						}),
						el(SelectControl, {
							label: __('Chapter spacing', 'estavillo-portfolio-core'),
							value: a.spacing || 'standard',
							options: [
								{ label: SPACING_LABELS.compact, value: 'compact' },
								{ label: SPACING_LABELS.standard, value: 'standard' },
								{ label: SPACING_LABELS.spacious, value: 'spacious' },
							],
							onChange: function (v) {
								set({ spacing: v });
							},
						}),
						el(ToggleControl, {
							label: __('Chapter divider', 'estavillo-portfolio-core'),
							help: __('Línea divisoria arriba del capítulo (nunca aparece en el primero de la página).', 'estavillo-portfolio-core'),
							checked: a.divider !== false,
							onChange: function (v) {
								set({ divider: v });
							},
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: sectionClasses(a) },
						variantChip,
						el(RichText, {
							tagName: 'div',
							className: 'es-case-label',
							placeholder: __('Fig. 00 — Etiqueta', 'estavillo-portfolio-core'),
							value: a.label,
							allowedFormats: [],
							onChange: function (v) {
								set({ label: v });
							},
						}),
						el(RichText, {
							tagName: 'h2',
							className: 'es-case-heading',
							placeholder: __('Heading de la sección', 'estavillo-portfolio-core'),
							value: a.heading,
							allowedFormats: ['core/italic'],
							onChange: function (v) {
								set({ heading: v });
							},
						}),
						el(RichText, {
							tagName: 'p',
							className: 'es-case-lead',
							placeholder: __('Lead opcional…', 'estavillo-portfolio-core'),
							value: a.lead,
							allowedFormats: ['core/bold', 'core/italic', 'core/link'],
							onChange: function (v) {
								set({ lead: v });
							},
						}),
						el('div', innerProps)
					)
				)
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
