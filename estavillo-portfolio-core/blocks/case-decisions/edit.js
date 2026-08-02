/**
 * estavillo/case-decisions — editor.
 * Cards reales con num/título/hallazgo/decisión inline + toolbar por
 * card; las etiquetas dt (Hallazgo/Decisión) se editan una vez en el
 * inspector. El preview refleja la MISMA estructura de dos <dl>
 * separados + conector que usa render.php — no una aproximación.
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
	var ToggleControl = wp.components.ToggleControl;

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
							label: __('Etiqueta de hallazgo (dt)', 'estavillo-portfolio-core'),
							value: a.taskLabel,
							onChange: function (v) {
								set({ taskLabel: v });
							},
						}),
						el(TextControl, {
							label: __('Etiqueta de decisión (dt)', 'estavillo-portfolio-core'),
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
							var rowClass = 'es-case-decision' + (item.visible === false ? ' es-caseb-row--hidden' : '');
							return el(
								'div',
								{ className: rowClass, key: 'dec-' + i },
								el(
									'div',
									{ className: 'es-case-decision__head' },
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
										placeholder: __('Título del hallazgo…', 'estavillo-portfolio-core'),
										value: item.title || '',
										allowedFormats: [],
										onChange: function (v) {
											set({ items: ui.listUpdate(items, i, { title: v }) });
										},
									})
								),
								el(
									'dl',
									{ className: 'es-case-decision__pair es-case-decision__pair--finding' },
									el('dt', null, a.taskLabel || 'Finding'),
									el(
										'dd',
										null,
										el(RichText, {
											tagName: 'span',
											placeholder: __('Qué mostró la evidencia…', 'estavillo-portfolio-core'),
											value: item.task || '',
											allowedFormats: ['core/italic'],
											onChange: function (v) {
												set({ items: ui.listUpdate(items, i, { task: v }) });
											},
										})
									)
								),
								el('div', { className: 'es-case-decision__connector', 'aria-hidden': 'true' }),
								el(
									'dl',
									{ className: 'es-case-decision__pair es-case-decision__pair--decision' },
									el('dt', null, a.guardrailLabel || 'Design decision'),
									el(
										'dd',
										null,
										el(RichText, {
											tagName: 'span',
											placeholder: __('Qué se decidió hacer…', 'estavillo-portfolio-core'),
											value: item.guardrail || '',
											allowedFormats: ['core/italic'],
											onChange: function (v) {
												set({ items: ui.listUpdate(items, i, { guardrail: v }) });
											},
										})
									)
								),
								el(ToggleControl, {
									label: __('Visible', 'estavillo-portfolio-core'),
									checked: item.visible !== false,
									onChange: function (v) {
										set({ items: ui.listUpdate(items, i, { visible: v }) });
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
					ui.addButton(__('Agregar decisión', 'estavillo-portfolio-core'), function () {
						set({ items: ui.listAdd(items, { num: '', title: '', task: '', guardrail: '', visible: true }) });
					})
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
