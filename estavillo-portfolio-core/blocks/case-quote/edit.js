/**
 * estavillo/case-quote — editor.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;

	wp.blocks.registerBlockType('estavillo/case-quote', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'es-case-quote' },
					el(RichText, {
						tagName: 'p',
						placeholder: __('Cita…', 'estavillo-portfolio-core'),
						value: a.quote,
						allowedFormats: ['core/italic'],
						onChange: function (v) {
							set({ quote: v });
						},
					}),
					el(RichText, {
						tagName: 'cite',
						placeholder: __('Atribución opcional — solo si es verificada…', 'estavillo-portfolio-core'),
						value: a.cite,
						allowedFormats: [],
						onChange: function (v) {
							set({ cite: v });
						},
					})
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
