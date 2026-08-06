/**
 * estavillo/tools — editor.
 *
 * Grilla real .es-tools (mismas clases que el frontend) con RichText
 * inline por título/herramienta + los helpers compartidos de
 * case-blocks-ui.js (listUpdate/listMove/listRemove/listAdd/itemBar) —
 * mismo patrón que ya usan estavillo/case-stats y estavillo/hobby-list.
 *
 * Categorías: subir/bajar/duplicar/borrar (itemBar con onDuplicate, la
 * única instancia en la librería que lo usa) + "Add category".
 * Herramientas: subir/bajar/borrar dentro de su categoría + "Add tool".
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var RichText = wp.blockEditor.RichText;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;

	// Misma librería lineal de es_process_icon_library() (theme) — sólo la
	// clave + un label legible; el SVG real lo resuelve siempre render.php,
	// nunca este archivo (sin duplicar artwork en el bundle del editor).
	var ICONS = [
		{ label: __('None', 'estavillo-portfolio-core'), value: '' },
		{ label: 'Compass', value: 'compass' },
		{ label: 'Flow', value: 'flow' },
		{ label: 'Map', value: 'map' },
		{ label: 'Target', value: 'target' },
		{ label: 'Layers', value: 'layers' },
		{ label: 'Tool', value: 'tool' },
		{ label: 'Check', value: 'check' },
		{ label: 'Document', value: 'document' },
		{ label: 'Bulb', value: 'bulb' },
		{ label: 'Rocket', value: 'rocket' },
		{ label: 'Cube', value: 'cube' },
	];

	// Ícono de "duplicar" para la barra de categoría: dos rectángulos
	// superpuestos, mismo trazo 1.3 que el resto del sistema — evita
	// depender de qué dashicon exista en esta versión de WP.
	function duplicateIcon() {
		return el(
			'svg',
			{ width: 16, height: 16, viewBox: '0 0 16 16', fill: 'none', stroke: 'currentColor', strokeWidth: '1.3', 'aria-hidden': 'true', focusable: 'false' },
			el('rect', { x: 2.5, y: 4.5, width: 8, height: 8, rx: 1 }),
			el('path', { d: 'M6 4.6V3a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1h-1.6' })
		);
	}

	wp.blocks.registerBlockType('estavillo/tools', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var groups = a.groups || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			function patchGroup(i, patch) {
				set({ groups: ui.listUpdate(groups, i, patch) });
			}

			var inspector = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __('Tools', 'estavillo-portfolio-core'), initialOpen: true },
					el(SelectControl, {
						label: __('Layout', 'estavillo-portfolio-core'),
						help: __('Editorial: herramientas en lista vertical, una por línea (default). Inline: una sola línea por categoría, separadas por «·» — pensado para espacios más chicos (Home).', 'estavillo-portfolio-core'),
						value: a.layout,
						options: [
							{ label: __('Editorial', 'estavillo-portfolio-core'), value: 'editorial' },
							{ label: __('Inline', 'estavillo-portfolio-core'), value: 'inline' },
						],
						onChange: function (v) {
							set({ layout: v });
						},
					}),
					el(ToggleControl, {
						label: __('Show category icons', 'estavillo-portfolio-core'),
						checked: !! a.showIcons,
						onChange: function (v) {
							set({ showIcons: v });
						},
					})
				)
			);

			var groupEls = groups.map(function (group, gi) {
				var items = group.items || [];

				function patchItem(ii, value) {
					patchGroup(gi, { items: ui.listUpdate(items, ii, value) });
				}

				return el(
					'div',
					{ className: 'es-tools__group', key: 'tools-group-' + gi },
					el(
						'div',
						{ className: 'es-tools__head' },
						a.showIcons
							? el(SelectControl, {
									label: __('Icon', 'estavillo-portfolio-core'),
									hideLabelFromVision: true,
									value: group.icon || '',
									options: ICONS,
									onChange: function (v) {
										patchGroup(gi, { icon: v });
									},
							  })
							: null,
						el(RichText, {
							tagName: 'h3',
							className: 'es-tools__title',
							placeholder: __('Category…', 'estavillo-portfolio-core'),
							value: group.title || '',
							allowedFormats: [],
							onChange: function (v) {
								patchGroup(gi, { title: v });
							},
						})
					),
					el(
						'ul',
						{ className: 'es-tools__list' },
						items.map(function (item, ii) {
							return el(
								'li',
								{ key: 'tool-' + gi + '-' + ii },
								el(RichText, {
									tagName: 'span',
									placeholder: __('Tool…', 'estavillo-portfolio-core'),
									value: item || '',
									allowedFormats: [],
									onChange: function (v) {
										patchItem(ii, v);
									},
								}),
								ui.itemBar({
									index: ii,
									count: items.length,
									onMove: function (from, to) {
										patchGroup(gi, { items: ui.listMove(items, from, to) });
									},
									onRemove: function (idx) {
										patchGroup(gi, { items: ui.listRemove(items, idx) });
									},
								})
							);
						})
					),
					ui.addButton(__('Add tool', 'estavillo-portfolio-core'), function () {
						patchGroup(gi, { items: ui.listAdd(items, '') });
					}),
					ui.itemBar({
						index: gi,
						count: groups.length,
						duplicateIcon: duplicateIcon(),
						onMove: function (from, to) {
							set({ groups: ui.listMove(groups, from, to) });
						},
						onRemove: function (idx) {
							set({ groups: ui.listRemove(groups, idx) });
						},
						onDuplicate: function (idx) {
							var copy = Object.assign({}, groups[idx]);
							var next = groups.slice();
							next.splice(idx + 1, 0, copy);
							set({ groups: next });
						},
					})
				);
			});

			return el(
				Fragment,
				null,
				inspector,
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'es-tools es-tools--' + a.layout + (a.showIcons ? '' : ' es-tools--no-icons') },
						el('div', { className: 'es-tools__grid' }, groupEls)
					),
					ui.addButton(__('Add category', 'estavillo-portfolio-core'), function () {
						set({ groups: ui.listAdd(groups, { title: '', icon: '', items: [], categoryDescription: '' }) });
					})
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
