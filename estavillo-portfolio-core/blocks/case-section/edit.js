/**
 * estavillo/case-section — editor.
 * Preview con el markup/clases reales del theme; anchor y layout en el
 * inspector; label/heading/lead inline (RichText); InnerBlocks libre.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;

	wp.blocks.registerBlockType('estavillo/case-section', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

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
						}),
						el(SelectControl, {
							label: __('Layout', 'estavillo-portfolio-core'),
							value: a.layout,
							options: [
								{ label: __('Wide — texto a medida de lectura, componentes a ancho completo', 'estavillo-portfolio-core'), value: 'wide' },
								{ label: __('Reading — toda la sección a medida de lectura', 'estavillo-portfolio-core'), value: 'reading' },
							],
							onChange: function (v) {
								set({ layout: v });
							},
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'es-case-section' + (a.layout === 'reading' ? ' es-case-section--reading' : '') },
						a.anchor
							? el('div', { className: 'es-caseb-mini' }, '#' + a.anchor)
							: null,
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
						el(InnerBlocks, null)
					)
				)
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
