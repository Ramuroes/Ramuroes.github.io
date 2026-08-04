/**
 * estavillo/case-timeline — editor.
 * Riel real .es-case-timeline con título/texto inline por ítem + toolbar.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;

	wp.blocks.registerBlockType('estavillo/case-timeline', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var items = a.items || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(
					'ol',
					{ className: 'es-case-timeline' },
					items.map(function (item, i) {
						return el(
							'li',
							{ className: 'es-case-timeline__item', key: 'tl-' + i },
							el(RichText, {
								tagName: 'div',
								className: 'es-case-timeline__title',
								placeholder: __('Título del paso…', 'estavillo-portfolio-core'),
								value: item.title || '',
								allowedFormats: [],
								onChange: function (v) {
									set({ items: ui.listUpdate(items, i, { title: v }) });
								},
							}),
							el(RichText, {
								tagName: 'div',
								className: 'es-case-timeline__text',
								placeholder: __('Texto del paso…', 'estavillo-portfolio-core'),
								value: item.text || '',
								allowedFormats: ['core/bold', 'core/italic'],
								onChange: function (v) {
									set({ items: ui.listUpdate(items, i, { text: v }) });
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
				ui.addButton(__('Agregar paso', 'estavillo-portfolio-core'), function () {
					set({ items: ui.listAdd(items, { title: '', text: '' }) });
				})
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
