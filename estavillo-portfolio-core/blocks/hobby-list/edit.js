/**
 * estavillo/hobby-list — editor.
 * Preview real de la grilla .es-hobbies__list (label editable inline) +
 * un <select> de ícono por ítem debajo (misma librería aprobada que Home
 * Content) — el SVG real solo se resuelve server-side (render.php), así
 * que acá el ícono se muestra como caja vacía + el nombre elegido en el
 * <select>, no como preview gráfico (sin build step / sin duplicar los
 * SVG en el bundle del editor).
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var SelectControl = wp.components.SelectControl;

	var ICONS = [
		{ label: __( 'None', 'estavillo-portfolio-core' ), value: '' },
		{ label: 'Taekwondo', value: 'taekwondo' },
		{ label: 'Guitar / Rock Music', value: 'guitar' },
		{ label: 'Coffee', value: 'coffee' },
		{ label: 'Horse Head', value: 'horse-head' },
		{ label: 'Horse Running', value: 'horse-run' },
		{ label: 'Drawing', value: 'drawing' },
		{ label: 'Travel', value: 'travel' },
		{ label: 'Cinema', value: 'cinema' },
	];

	wp.blocks.registerBlockType('estavillo/hobby-list', {
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
					'ul',
					{ className: 'es-hobbies__list' },
					items.map(function (item, i) {
						var iconClass = 'es-hobby-item__icon es-hobby-icon' + (item.icon ? ' es-hobby-icon--' + item.icon : ' es-hobby-item__icon--empty');
						return el(
							'li',
							{ className: 'es-hobby-item', key: 'hobby-' + i },
							el('span', { className: iconClass, 'aria-hidden': 'true' }),
							el(RichText, {
								tagName: 'span',
								className: 'es-hobby-item__label',
								placeholder: __( 'Hobby…', 'estavillo-portfolio-core' ),
								value: item.label || '',
								allowedFormats: [],
								onChange: function (v) {
									patch(i, { label: v });
								},
							}),
							el(
								'div',
								{ className: 'es-caseb-item' },
								el(SelectControl, {
									label: __( 'Icon', 'estavillo-portfolio-core' ),
									hideLabelFromVision: true,
									value: item.icon || '',
									options: ICONS,
									onChange: function (v) {
										patch(i, { icon: v });
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
							)
						);
					})
				),
				ui.addButton(__( 'Add hobby', 'estavillo-portfolio-core' ), function () {
					set({ items: ui.listAdd(items, { label: '', icon: '' }) });
				})
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
