/**
 * estavillo/case-stats — editor.
 * Grilla real .es-case-stats con RichText inline por stat + toolbar de ítem
 * (subir/bajar/borrar) y botón de agregar.
 *
 * Sólo dos controles además de eso: un <select> de ícono por stat (misma
 * librería lineal curada del theme que usa Tools — se guarda la CLAVE, nunca
 * markup) y un toggle "Animate numbers" a nivel bloque, en el Inspector.
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

	// Espejo de es_process_icon_library() (theme): sólo clave + label. El SVG
	// real lo resuelve siempre render.php, nunca este archivo — así el artwork
	// vive en un único lugar y el bundle del editor no lo duplica.
	var ICONS = [
		{ label: __('None', 'estavillo-portfolio-core'), value: '' },
		{ label: 'Chart', value: 'chart' },
		{ label: 'Trend', value: 'trend' },
		{ label: 'Clock', value: 'clock' },
		{ label: 'User', value: 'user' },
		{ label: 'Search', value: 'search' },
		{ label: 'Check', value: 'check' },
		{ label: 'Target', value: 'target' },
		{ label: 'Compass', value: 'compass' },
		{ label: 'Flow', value: 'flow' },
		{ label: 'Map', value: 'map' },
		{ label: 'Layers', value: 'layers' },
		{ label: 'Tool', value: 'tool' },
		{ label: 'Document', value: 'document' },
		{ label: 'Bulb', value: 'bulb' },
		{ label: 'Rocket', value: 'rocket' },
		{ label: 'Cube', value: 'cube' },
	];

	wp.blocks.registerBlockType('estavillo/case-stats', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var items = a.items || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			function patch(i, p) {
				set({ items: ui.listUpdate(items, i, p) });
			}

			var inspector = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __('Case Stats', 'estavillo-portfolio-core'), initialOpen: true },
					el(ToggleControl, {
						label: __('Animate numbers', 'estavillo-portfolio-core'),
						help: __(
							'Los números cuentan desde 0 una sola vez, cuando el bloque entra en pantalla. El valor final siempre se imprime en el HTML: sin JavaScript, o con «reducir movimiento» activado, se ve directamente el número.',
							'estavillo-portfolio-core'
						),
						checked: !! a.animate,
						onChange: function (v) {
							set({ animate: v });
						},
					})
				)
			);

			return el(
				Fragment,
				null,
				inspector,
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'es-case-stats' },
						items.map(function (item, i) {
							return el(
								'div',
								{ className: 'es-case-stat', key: 'stat-' + i },
								el(SelectControl, {
									label: __('Icon', 'estavillo-portfolio-core'),
									hideLabelFromVision: true,
									value: item.icon || '',
									options: ICONS,
									onChange: function (v) {
										patch(i, { icon: v });
									},
								}),
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
						set({ items: ui.listAdd(items, { num: '', label: '', icon: '' }) });
					})
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
