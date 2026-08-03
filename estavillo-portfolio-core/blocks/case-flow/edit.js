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
	var ToggleControl = wp.components.ToggleControl;

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
					o.extraFields ? o.extraFields(row, function (patch) {
						o.onChange(ui.listUpdate(rows, ri, patch));
					}) : null,
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
							label: __('Section title (eyebrow)', 'estavillo-portfolio-core'),
							help: __('Optional small caption above the flow, e.g. "IDEAL USER FLOW". Leave blank to hide.', 'estavillo-portfolio-core'),
							value: a.sectionLabel,
							onChange: function (v) {
								set({ sectionLabel: v });
							},
						}),
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
						el(TextControl, {
							label: __('(IA) legend', 'estavillo-portfolio-core'),
							help: __('Shown once under the flow, only if a node is marked as an AI point.', 'estavillo-portfolio-core'),
							value: a.aiLegend,
							onChange: function (v) {
								set({ aiLegend: v });
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
						a.sectionLabel
							? el('p', { className: 'es-eyebrow es-flow__eyebrow' }, a.sectionLabel)
							: null,
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
										el(TextControl, {
											label: __('Label on the incoming arrow', 'estavillo-portfolio-core'),
											help: __('e.g. "Main entry to the site". Blank = no label.', 'estavillo-portfolio-core'),
											value: node.edgeLabel || '',
											onChange: function (v) {
												updateNode(i, { edgeLabel: v });
											},
										}),
										el(ToggleControl, {
											label: __('AI intervention point (IA)', 'estavillo-portfolio-core'),
											checked: !!node.ai,
											onChange: function (v) {
												updateNode(i, { ai: v });
											},
										}),
										el(
											'span',
											{ className: 'es-flow__shape' },
											el(
												'span',
												{ className: 'es-flow__shape-inner' },
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
												})
											),
											node.ai ? el('span', { className: 'es-flow__ai' }, el('span', { 'aria-hidden': 'true' }, 'IA')) : null
										),
										el(
											'span',
											{ className: 'es-flow__body' },
											el(RichText, {
												tagName: 'span',
												className: 'es-flow__text',
												placeholder: __('Short description, shown under the shape…', 'estavillo-portfolio-core'),
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
											// Rama con pantalla propia: si esta salida NO es
											// "sigue directo al próximo nodo" sino que pasa por
											// una pantalla intermedia (p. ej. "No" -> Crear
											// cuenta), esa pantalla se vuelve visible en el
											// diagrama (nunca sólo texto adentro del panel).
											// Vacío = comportamiento de siempre, sin tarjeta.
											extraFields: function (row, onChangeRow) {
												return el(
													'div',
													{ className: 'es-caseb-sub__row' },
													el(TextControl, {
														label: __('Detour screen (optional)', 'estavillo-portfolio-core'),
														help: __('If this branch leads to its own screen (not straight to the next node), name it here — it becomes a visible node.', 'estavillo-portfolio-core'),
														value: row.detourTitle || '',
														onChange: function (v) {
															onChangeRow({ detourTitle: v });
														},
													}),
													row.detourTitle
														? el(TextControl, {
															label: __('Detour description', 'estavillo-portfolio-core'),
															value: row.detourText || '',
															onChange: function (v) {
																onChangeRow({ detourText: v });
															},
														})
														: null,
													row.detourTitle
														? el(TextControl, {
															label: __('Reconnect caption', 'estavillo-portfolio-core'),
															help: __('e.g. "Continues to Checkout" — where this detour rejoins the flow.', 'estavillo-portfolio-core'),
															value: row.detourNext || '',
															onChange: function (v) {
																onChangeRow({ detourNext: v });
															},
														})
														: null
												);
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
									edgeLabel: '',
									ai: false,
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
