/**
 * estavillo/case-flow — editor.
 *
 * El preview inline es el flujo real (el theme puentea case-flow.css al
 * iframe del editor), y CADA cosa editable se edita donde se ve: número,
 * título y línea corta con RichText sobre el nodo; tipo de nodo, acento,
 * filas de detalle y ramas en el panel del propio nodo. Las etiquetas de
 * todo el bloque (inicio/fin, "Ver detalle", "Cerrar", densidad) viven una
 * sola vez en el inspector.
 *
 * Sin JSX y sin build step, igual que el resto de la librería: sólo
 * wp.element.createElement vía esCaseUI.el.
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
	var SelectControl = wp.components.SelectControl;

	var KINDS = [
		{ label: __('Step', 'estavillo-portfolio-core'), value: 'step' },
		{ label: __('Decision', 'estavillo-portfolio-core'), value: 'decision' },
		{ label: __('Start', 'estavillo-portfolio-core'), value: 'start' },
		{ label: __('Milestone', 'estavillo-portfolio-core'), value: 'milestone' },
		{ label: __('End', 'estavillo-portfolio-core'), value: 'end' },
	];

	var ACCENTS = [
		{ label: __('Default', 'estavillo-portfolio-core'), value: '' },
		{ label: __('Signal (green — system path)', 'estavillo-portfolio-core'), value: 'signal' },
		{ label: __('Decision (orange — judgment point)', 'estavillo-portfolio-core'), value: 'decision' },
		{ label: __('Muted', 'estavillo-portfolio-core'), value: 'muted' },
	];

	/**
	 * Sub-repetidor genérico (filas de detalle / ramas). Devuelve el array
	 * nuevo por callback — nunca muta el original.
	 */
	function subList(o) {
		var rows = o.rows || [];
		return el(
			'div',
			{ className: 'es-caseb-sub' },
			el('p', { className: 'es-caseb-sub__title' }, o.title),
			rows.map(function (row, ri) {
				return el(
					'div',
					{ className: 'es-caseb-sub__row', key: o.keyPrefix + ri },
					el(TextControl, {
						label: o.labelLabel,
						value: row.label || '',
						onChange: function (v) {
							o.onChange(ui.listUpdate(rows, ri, { label: v }));
						},
					}),
					el(TextControl, {
						label: o.textLabel,
						value: row.text || '',
						onChange: function (v) {
							o.onChange(ui.listUpdate(rows, ri, { text: v }));
						},
					}),
					ui.itemBar({
						index: ri,
						count: rows.length,
						onMove: function (from, to) {
							o.onChange(ui.listMove(rows, from, to));
						},
						onRemove: function (idx) {
							o.onChange(ui.listRemove(rows, idx));
						},
					})
				);
			}),
			ui.addButton(o.addLabel, function () {
				o.onChange(ui.listAdd(rows, { label: '', text: '' }));
			})
		);
	}

	wp.blocks.registerBlockType('estavillo/case-flow', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var nodes = a.nodes || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			function updateNode(i, patch) {
				set({ nodes: ui.listUpdate(nodes, i, patch) });
			}

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Flow labels', 'estavillo-portfolio-core'), initialOpen: true },
						el(TextControl, {
							label: __('Start marker', 'estavillo-portfolio-core'),
							help: __('Leave blank to hide.', 'estavillo-portfolio-core'),
							value: a.startLabel,
							onChange: function (v) {
								set({ startLabel: v });
							},
						}),
						el(TextControl, {
							label: __('End marker', 'estavillo-portfolio-core'),
							help: __('Leave blank to hide.', 'estavillo-portfolio-core'),
							value: a.endLabel,
							onChange: function (v) {
								set({ endLabel: v });
							},
						}),
						el(TextControl, {
							label: __('"Show detail" label', 'estavillo-portfolio-core'),
							value: a.detailLabel,
							onChange: function (v) {
								set({ detailLabel: v });
							},
						}),
						el(TextControl, {
							label: __('"Close" label', 'estavillo-portfolio-core'),
							value: a.closeLabel,
							onChange: function (v) {
								set({ closeLabel: v });
							},
						}),
						el(TextControl, {
							label: __('Screen-reader word before each number', 'estavillo-portfolio-core'),
							help: __('e.g. "Step". Read before "01" by screen readers only.', 'estavillo-portfolio-core'),
							value: a.stepLabel,
							onChange: function (v) {
								set({ stepLabel: v });
							},
						}),
						el(SelectControl, {
							label: __('Density', 'estavillo-portfolio-core'),
							value: a.density,
							options: [
								{ label: __('Comfortable', 'estavillo-portfolio-core'), value: 'comfortable' },
								{ label: __('Compact', 'estavillo-portfolio-core'), value: 'compact' },
							],
							onChange: function (v) {
								set({ density: v });
							},
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'es-flow es-flow--' + (a.density || 'comfortable') },
						a.startLabel
							? el('p', { className: 'es-flow__marker es-flow__marker--start' }, el('span', null, a.startLabel))
							: null,
						el(
							'ol',
							{ className: 'es-flow__track' },
							nodes.map(function (node, i) {
								var kind = node.kind || 'step';
								var accent = node.accent ? ' is-accent-' + node.accent : '';
								return el(
									'li',
									{
										className: 'es-flow__item es-flow__item--' + kind + accent,
										key: 'node-' + i,
									},
									el(
										'div',
										{ className: 'es-flow__node' },
										ui.itemBar({
											index: i,
											count: nodes.length,
											onMove: function (from, to) {
												set({ nodes: ui.listMove(nodes, from, to) });
											},
											onRemove: function (idx) {
												set({ nodes: ui.listRemove(nodes, idx) });
											},
										}),
										el(
											'div',
											{ className: 'es-caseb-inline2' },
											el(SelectControl, {
												label: __('Node type', 'estavillo-portfolio-core'),
												value: kind,
												options: KINDS,
												onChange: function (v) {
													updateNode(i, { kind: v });
												},
											}),
											el(SelectControl, {
												label: __('Accent', 'estavillo-portfolio-core'),
												value: node.accent || '',
												options: ACCENTS,
												onChange: function (v) {
													updateNode(i, { accent: v });
												},
											})
										),
										el('span', { className: 'es-flow__shape', 'aria-hidden': 'true' }),
										el(
											'span',
											{ className: 'es-flow__body' },
											el(RichText, {
												tagName: 'span',
												className: 'es-flow__num',
												placeholder: '01',
												value: node.num || '',
												allowedFormats: [],
												onChange: function (v) {
													updateNode(i, { num: v });
												},
											}),
											el(RichText, {
												tagName: 'span',
												className: 'es-flow__title',
												placeholder: __('Node title…', 'estavillo-portfolio-core'),
												value: node.title || '',
												allowedFormats: [],
												onChange: function (v) {
													updateNode(i, { title: v });
												},
											}),
											el(RichText, {
												tagName: 'span',
												className: 'es-flow__text',
												placeholder: __('One short line shown on the node…', 'estavillo-portfolio-core'),
												value: node.text || '',
												allowedFormats: ['core/italic'],
												onChange: function (v) {
													updateNode(i, { text: v });
												},
											})
										),
										subList({
											title: __('Detail rows (popover)', 'estavillo-portfolio-core'),
											keyPrefix: 'd-' + i + '-',
											rows: node.detail || [],
											labelLabel: __('Label', 'estavillo-portfolio-core'),
											textLabel: __('Text', 'estavillo-portfolio-core'),
											addLabel: __('Add detail row', 'estavillo-portfolio-core'),
											onChange: function (rows) {
												updateNode(i, { detail: rows });
											},
										}),
										subList({
											title: __('Branches (decision yes/no)', 'estavillo-portfolio-core'),
											keyPrefix: 'b-' + i + '-',
											rows: node.branches || [],
											labelLabel: __('Branch label', 'estavillo-portfolio-core'),
											textLabel: __('Outcome', 'estavillo-portfolio-core'),
											addLabel: __('Add branch', 'estavillo-portfolio-core'),
											onChange: function (rows) {
												updateNode(i, { branches: rows });
											},
										})
									)
								);
							})
						),
						ui.addButton(__('Add node', 'estavillo-portfolio-core'), function () {
							set({
								nodes: ui.listAdd(nodes, {
									kind: 'step',
									accent: '',
									num: '',
									title: '',
									text: '',
									detail: [],
									branches: [],
								}),
							});
						}),
						a.endLabel
							? el('p', { className: 'es-flow__marker es-flow__marker--end' }, el('span', null, a.endLabel))
							: null
					)
				)
			);
		},
	});
})(window.wp, window.esCaseUI);
