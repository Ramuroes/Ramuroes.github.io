/**
 * estavillo/case-findings-list — editor.
 *
 * Título/subtítulo del bloque se editan inline (RichText) igual que
 * case-section; cada fila es ícono (select) + herramienta (RichText) +
 * hallazgo (RichText) + visibilidad (toggle), con toolbar de reordenar/
 * borrar. Los paths de los íconos son una copia 1:1 de
 * es_findings_icon_svg() en render.php — sólo para que el preview del
 * editor se vea parecido al frontend real; el marcado que se guarda es
 * siempre la clave elegida, nunca el SVG.
 *
 * Sin JSX y sin build step, igual que el resto de la librería.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;

	var ICON_PATHS = {
		heuristics: 'M6,9 H26 V11.4 H6 Z M6,15.3 H22 V17.7 H6 Z M6,21.6 H26 V24 H6 Z',
		clarity: 'M7,5 L7,27 L12.5,21.8 L16.3,29 L20,27.1 L16.2,20 L24,19.6 Z',
		analytics: 'M6,20 H11 V26 H6 Z M13.5,14 H18.5 V26 H13.5 Z M21,8 H26 V26 H21 Z',
		'synthetic-users': 'M11,10 A5,5 0 1,0 21,10 A5,5 0 1,0 11,10 Z M6,27 C6,20 10,17 16,17 C22,17 26,20 26,27 Z',
		benchmarking: 'M15,6 H17 V24 H15 Z M6,10 H26 V12 H6 Z M9,26 H23 V28.4 H9 Z M4.8,16 A3.2,3.2 0 1,0 11.2,16 A3.2,3.2 0 1,0 4.8,16 Z M20.8,16 A3.2,3.2 0 1,0 27.2,16 A3.2,3.2 0 1,0 20.8,16 Z',
		compare: 'M2.5,16 A7.5,7.5 0 1,0 17.5,16 A7.5,7.5 0 1,0 2.5,16 Z M4.2,16 A5.8,5.8 0 1,0 15.8,16 A5.8,5.8 0 1,0 4.2,16 Z M14.5,16 A7.5,7.5 0 1,0 29.5,16 A7.5,7.5 0 1,0 14.5,16 Z M16.2,16 A5.8,5.8 0 1,0 27.8,16 A5.8,5.8 0 1,0 16.2,16 Z',
	};

	var ICON_OPTIONS = [
		{ label: __('None', 'estavillo-portfolio-core'), value: '' },
		{ label: __('Heuristics (checklist)', 'estavillo-portfolio-core'), value: 'heuristics' },
		{ label: __('Clarity (cursor)', 'estavillo-portfolio-core'), value: 'clarity' },
		{ label: __('Analytics (bars)', 'estavillo-portfolio-core'), value: 'analytics' },
		{ label: __('Synthetic Users (person)', 'estavillo-portfolio-core'), value: 'synthetic-users' },
		{ label: __('Benchmarking (scale)', 'estavillo-portfolio-core'), value: 'benchmarking' },
		{ label: __('AI vs. manual (compare)', 'estavillo-portfolio-core'), value: 'compare' },
	];

	function iconPreview(key) {
		var d = ICON_PATHS[key];
		if (!d) {
			return null;
		}
		return el(
			'svg',
			{ viewBox: '0 0 32 32', width: '20', height: '20', 'aria-hidden': 'true', focusable: 'false' },
			el('path', { fillRule: 'evenodd', clipRule: 'evenodd', d: d, fill: 'currentColor' })
		);
	}

	wp.blocks.registerBlockType('estavillo/case-findings-list', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var items = a.items || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			function updateItem(i, patch) {
				set({ items: ui.listUpdate(items, i, patch) });
			}

			return el(
				Fragment,
				null,
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'es-findings' },
						el(RichText, {
							tagName: 'h3',
							className: 'es-findings__title',
							placeholder: __('Section title…', 'estavillo-portfolio-core'),
							value: a.title || '',
							allowedFormats: [],
							onChange: function (v) {
								set({ title: v });
							},
						}),
						el(RichText, {
							tagName: 'p',
							className: 'es-findings__subtitle',
							placeholder: __('Optional subtitle / lead…', 'estavillo-portfolio-core'),
							value: a.subtitle || '',
							allowedFormats: ['core/italic'],
							onChange: function (v) {
								set({ subtitle: v });
							},
						}),
						el(
							'ul',
							{ className: 'es-findings__list' },
							items.map(function (item, i) {
								var rowClass = 'es-findings__row' + (item.visible === false ? ' es-caseb-row--hidden' : '');
								return el(
									'li',
									{ className: rowClass, key: 'find-' + i },
									el(
										'div',
										{ className: 'es-caseb-inline2' },
										el(SelectControl, {
											label: __('Icon', 'estavillo-portfolio-core'),
											value: item.icon || '',
											options: ICON_OPTIONS,
											onChange: function (v) {
												updateItem(i, { icon: v });
											},
										}),
										el(ToggleControl, {
											label: __('Visible', 'estavillo-portfolio-core'),
											checked: item.visible !== false,
											onChange: function (v) {
												updateItem(i, { visible: v });
											},
										})
									),
									item.icon ? el('span', { className: 'es-findings__icon' }, iconPreview(item.icon)) : null,
									el(RichText, {
										tagName: 'span',
										className: 'es-findings__tool',
										placeholder: __('Tool / technique…', 'estavillo-portfolio-core'),
										value: item.title || '',
										allowedFormats: [],
										onChange: function (v) {
											updateItem(i, { title: v });
										},
									}),
									el(RichText, {
										tagName: 'p',
										className: 'es-findings__finding',
										placeholder: __('Key finding…', 'estavillo-portfolio-core'),
										value: item.finding || '',
										allowedFormats: ['core/italic', 'core/bold'],
										onChange: function (v) {
											updateItem(i, { finding: v });
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
						ui.addButton(__('Add row', 'estavillo-portfolio-core'), function () {
							set({
								items: ui.listAdd(items, {
									icon: '',
									title: '',
									finding: '',
									visible: true,
								}),
							});
						})
					)
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
