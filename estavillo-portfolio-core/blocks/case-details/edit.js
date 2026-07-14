/**
 * estavillo/case-details — editor.
 * En el editor el acordeón se muestra siempre abierto (el click en el
 * summary se anula para poder editar el texto sin plegarlo).
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InnerBlocks = wp.blockEditor.InnerBlocks;

	wp.blocks.registerBlockType('estavillo/case-details', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(
					'details',
					{
						className: 'es-case-details',
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
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
