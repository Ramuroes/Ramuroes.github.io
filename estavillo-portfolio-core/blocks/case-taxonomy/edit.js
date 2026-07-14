/**
 * estavillo/case-taxonomy — editor.
 * Raíz, cards de la grilla y tags de modificadores editables inline con
 * toolbars de ítem; todo con las clases reales del theme.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;

	wp.blocks.registerBlockType('estavillo/case-taxonomy', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var items = a.items || [];
			var mods = a.mods || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'es-case-taxonomy' },
					el(RichText, {
						tagName: 'div',
						className: 'es-case-taxonomy__root',
						placeholder: __('Raíz — qué agrupa este diagrama…', 'estavillo-portfolio-core'),
						value: a.root,
						allowedFormats: [],
						onChange: function (v) {
							set({ root: v });
						},
					}),
					el(
						'div',
						{ className: 'es-case-taxonomy__grid' },
						items.map(function (item, i) {
							return el(
								'div',
								{ className: 'es-case-taxonomy__item', key: 'it-' + i },
								el(RichText, {
									tagName: 'div',
									className: 'es-case-taxonomy__item-title',
									placeholder: __('Variable…', 'estavillo-portfolio-core'),
									value: item.title || '',
									allowedFormats: [],
									onChange: function (v) {
										set({ items: ui.listUpdate(items, i, { title: v }) });
									},
								}),
								el(RichText, {
									tagName: 'div',
									className: 'es-case-taxonomy__item-meta',
									placeholder: __('meta…', 'estavillo-portfolio-core'),
									value: item.meta || '',
									allowedFormats: [],
									onChange: function (v) {
										set({ items: ui.listUpdate(items, i, { meta: v }) });
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
					ui.addButton(__('Agregar variable', 'estavillo-portfolio-core'), function () {
						set({ items: ui.listAdd(items, { title: '', meta: '' }) });
					}),
					el(
						'div',
						{ className: 'es-case-taxonomy__mods' },
						el(RichText, {
							tagName: 'div',
							className: 'es-case-taxonomy__mods-label',
							placeholder: __('Se ajusta según…', 'estavillo-portfolio-core'),
							value: a.modsLabel,
							allowedFormats: [],
							onChange: function (v) {
								set({ modsLabel: v });
							},
						}),
						el(
							'div',
							{ className: 'es-case-taxonomy__mods-tags' },
							mods.map(function (mod, i) {
								return el(
									'span',
									{ key: 'mod-' + i, style: { display: 'inline-flex', alignItems: 'center', gap: '4px' } },
									el(
										'span',
										{ className: 'es-case-taxonomy__tag' },
										el(RichText, {
											tagName: 'span',
											placeholder: __('Modificador…', 'estavillo-portfolio-core'),
											value: mod,
											allowedFormats: [],
											onChange: function (v) {
												set({ mods: ui.listUpdate(mods, i, v) });
											},
										})
									),
									ui.itemBar({
										index: i,
										count: mods.length,
										onMove: function (from, to) {
											set({ mods: ui.listMove(mods, from, to) });
										},
										onRemove: function (idx) {
											set({ mods: ui.listRemove(mods, idx) });
										},
									})
								);
							}),
							ui.addButton(__('Agregar modificador', 'estavillo-portfolio-core'), function () {
								set({ mods: ui.listAdd(mods, '') });
							})
						)
					)
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
