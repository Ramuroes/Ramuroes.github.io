/**
 * estavillo/case-stats — editor.
 * Grilla real .es-case-stats con RichText inline por stat + toolbar de ítem
 * (subir/bajar/borrar) y botón de agregar.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;

	wp.blocks.registerBlockType('estavillo/case-stats', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var items = a.items || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			function patch(i, p) {
				set({ items: ui.listUpdate(items, i, p) });
			}

			return el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'es-case-stats' },
					items.map(function (item, i) {
						return el(
							'div',
							{ className: 'es-case-stat', key: 'stat-' + i },
							el(RichText, {
								tagName: 'div',
								className: 'es-case-stat__num',
								placeholder: __('N', 'estavillo-portfolio-core'),
								value: item.num || '',
								allowedFormats: [],
								onChange: function (v) {
									patch(i, { num: v });
								},
							}),
							el(RichText, {
								tagName: 'div',
								className: 'es-case-stat__label',
								placeholder: __('etiqueta del número…', 'estavillo-portfolio-core'),
								value: item.label || '',
								allowedFormats: ['core/italic'],
								onChange: function (v) {
									patch(i, { label: v });
								},
							}),
							ui.itemBar({
								index: i,
								count: items.length,
								onMove: function (from, to) {
									set({ items: ui.listMove(items, from, to) });
								},
								onRemove: function (idx) {
									set({ items: ui.listRemove(items, idx) });
								},
							})
						);
					})
				),
				ui.addButton(__('Agregar stat', 'estavillo-portfolio-core'), function () {
					set({ items: ui.listAdd(items, { num: '', label: '' }) });
				})
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
