/**
 * estavillo/case-status — editor.
 * Dos columnas editables (heading + bullets inline con toolbar por bullet);
 * el estilo de cada columna (done/attention/neutro) se elige en el inspector.
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
	var SelectControl = wp.components.SelectControl;

	var STYLES = [
		{ label: 'Done (verde)', value: 'done' },
		{ label: 'Attention (naranja)', value: 'attention' },
		{ label: 'Neutro', value: 'plain' },
	];

	function colClass(style) {
		var c = 'es-case-status__col';
		if (style === 'done') {
			c += ' es-case-status__col--done';
		} else if (style === 'attention') {
			c += ' es-case-status__col--attention';
		}
		return c;
	}

	wp.blocks.registerBlockType('estavillo/case-status', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var columns = a.columns || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			function patchCol(ci, p) {
				set({ columns: ui.listUpdate(columns, ci, p) });
			}

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Estilo de columnas', 'estavillo-portfolio-core'), initialOpen: true },
						columns.map(function (col, ci) {
							return el(SelectControl, {
								key: 'style-' + ci,
								label: __('Columna', 'estavillo-portfolio-core') + ' ' + (ci + 1),
								value: col.style || 'plain',
								options: STYLES,
								onChange: function (v) {
									patchCol(ci, { style: v });
								},
							});
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: 'es-case-status' },
						columns.map(function (col, ci) {
							var bullets = col.items || [];
							return el(
								'div',
								{ className: colClass(col.style), key: 'col-' + ci },
								el(RichText, {
									tagName: 'div',
									className: 'es-case-status__head',
									placeholder: __('Heading de la columna…', 'estavillo-portfolio-core'),
									value: col.heading || '',
									allowedFormats: [],
									onChange: function (v) {
										patchCol(ci, { heading: v });
									},
								}),
								el(
									'ul',
									{ className: 'es-case-status__list' },
									bullets.map(function (bullet, bi) {
										return el(
											'li',
											{ key: 'b-' + ci + '-' + bi },
											el(RichText, {
												tagName: 'span',
												placeholder: __('Bullet…', 'estavillo-portfolio-core'),
												value: bullet,
												allowedFormats: ['core/bold', 'core/italic'],
												onChange: function (v) {
													patchCol(ci, { items: ui.listUpdate(bullets, bi, v) });
												},
											}),
											ui.itemBar({
												index: bi,
												count: bullets.length,
												onMove: function (from, to) {
													patchCol(ci, { items: ui.listMove(bullets, from, to) });
												},
												onRemove: function (idx) {
													patchCol(ci, { items: ui.listRemove(bullets, idx) });
												},
											})
										);
									})
								),
								el(
									'div',
									{ style: { padding: '0 18px 16px' } },
									ui.addButton(__('Agregar bullet', 'estavillo-portfolio-core'), function () {
										patchCol(ci, { items: ui.listAdd(bullets, '') });
									})
								)
							);
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
