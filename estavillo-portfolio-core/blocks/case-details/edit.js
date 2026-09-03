/**
 * estavillo/case-details — editor.
 * En el editor el acordeón se muestra siempre abierto (el click en el
 * summary se anula para poder editar el texto sin plegarlo).
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
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;

	wp.blocks.registerBlockType('estavillo/case-details', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			var inspector = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __('Accordion', 'estavillo-portfolio-core'), initialOpen: true },
					el(SelectControl, {
						label: __('Width', 'estavillo-portfolio-core'),
						help: __('Reading: a medida de lectura (default, el comportamiento de siempre). Content: ancho completo del contenedor — para tablas, diagramas o comparativas que a medida de lectura quedan apretadas. Mismos dos nombres que el Width de Case Section.', 'estavillo-portfolio-core'),
						value: a.width || 'reading',
						options: [
							{ label: __('Reading', 'estavillo-portfolio-core'), value: 'reading' },
							{ label: __('Content', 'estavillo-portfolio-core'), value: 'content' },
						],
						onChange: function (v) {
							set({ width: v });
						},
					}),
					el(TextControl, {
						label: __('Accessible label', 'estavillo-portfolio-core'),
						help: __(
							'Sólo hace falta cuando varios acordeones de la misma página comparten el mismo texto visible (p. ej. seis "Más sobre este paso"): un lector de pantalla los lista como seis controles idénticos. Poné acá el nombre completo — "Más sobre: Entender el sistema" — y el texto visible queda como está. Vacío = el nombre sale del texto visible, como siempre.',
							'estavillo-portfolio-core'
						),
						value: a.ariaLabel || '',
						onChange: function (v) {
							set({ ariaLabel: v });
						},
					})
				)
			);

			return el(
				Fragment,
				null,
				inspector,
				el(
					'div',
					blockProps,
					el(
						'details',
						{
							className: 'es-case-details' + ('content' === a.width ? ' es-case-details--content' : ''),
							open: true,
							onClick: function (e) {
								// nunca plegar en el editor
								if (e.target && e.target.closest && e.target.closest('summary')) {
									e.preventDefault();
								}
							},
						},
						el(
							'summary',
							null,
							el(RichText, {
								tagName: 'span',
								placeholder: __('Título del acordeón…', 'estavillo-portfolio-core'),
								value: a.summary,
								allowedFormats: [],
								onChange: function (v) {
									set({ summary: v });
								},
							})
						),
						el('div', { className: 'es-case-details__body' }, el(InnerBlocks, null))
					)
				)
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
