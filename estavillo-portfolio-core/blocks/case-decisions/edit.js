/**
 * estavillo/case-decisions — editor.
 * Cards reales con num/título/tarea/resguardo inline + toolbar por card;
 * las etiquetas dt (Task/Guardrail) se editan una vez en el inspector.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;

	wp.blocks.registerBlockType('estavillo/case-decisions', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var items = a.items || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Etiquetas de las cards', 'estavillo-portfolio-core'), initialOpen: true },
						el(TextControl, {
							label: __('Etiqueta de tarea (dt)', 'estavillo-portfolio-core'),
							value: a.taskLabel,
							onChange: function (v) {
								set({ taskLabel: v });
							},
						}),
						el(TextControl, {
							label: __('Etiqueta de resguardo (dt)', 'estavillo-portfolio-core'),
							value: a.guardrailLabel,
							onChange: function (v) {
								set({ guardrailLabel: v });
							},
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'es-case-decisions' },
						items.map(function (item, i) {
							return el(
								'div',
								{ className: 'es-case-decision', key: 'dec-' + i },
								el(RichText, {
									tagName: 'div',
									className: 'es-case-decision__num',
									placeholder: '01',
									value: item.num || '',
									allowedFormats: [],
									onChange: function (v) {
										set({ items: ui.listUpdate(items, i, { num: v }) });
									},
								}),
								el(RichText, {
									tagName: 'h3',
									className: 'es-case-decision__title',
									placeholder: __('Título de la decisión…', 'estavillo-portfolio-core'),
									value: item.title || '',
									allowedFormats: [],
									onChange: function (v) {
										set({ items: ui.listUpdate(items, i, { title: v }) });
									},
								}),
								el(
									'dl',
									{ className: 'es-case-decision__row' },
									el('dt', null, a.taskLabel || 'Task'),
									el(
										'dd',
										null,
										el(RichText, {
											tagName: 'span',
											placeholder: __('Qué hace…', 'estavillo-portfolio-core'),
											value: item.task || '',
											allowedFormats: ['core/italic'],
											onChange: function (v) {
												set({ items: ui.listUpdate(items, i, { task: v }) });
											},
										})
									),
									el('dt', null, a.guardrailLabel || 'Guardrail'),
									el(
										'dd',
										null,
										el(RichText, {
											tagName: 'span',
											placeholder: __('Qué lo limita…', 'estavillo-portfolio-core'),
											value: item.guardrail || '',
											allowedFormats: ['core/italic'],
											onChange: function (v) {
												set({ items: ui.listUpdate(items, i, { guardrail: v }) });
											},
										})
									)
								),
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
					ui.addButton(__('Agregar decisión', 'estavillo-portfolio-core'), function () {
						set({ items: ui.listAdd(items, { num: '', title: '', task: '', guardrail: '' }) });
					})
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
