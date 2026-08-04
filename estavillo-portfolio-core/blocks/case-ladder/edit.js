/**
 * estavillo/case-ladder — editor.
 * Preview real de la escalera (chips editables inline) + una fila de
 * controles por etapa (estado + subir/bajar/borrar) debajo.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var SelectControl = wp.components.SelectControl;

	var STATES = [
		{ label: 'Future', value: 'future' },
		{ label: 'Active', value: 'active' },
		{ label: 'Completed', value: 'done' },
	];

	function stepClass(state) {
		var c = 'es-case-ladder__step';
		if (state === 'active') {
			c += ' es-case-ladder__step--active';
		} else if (state === 'done') {
			c += ' es-case-ladder__step--done';
		}
		return c;
	}

	wp.blocks.registerBlockType('estavillo/case-ladder', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var steps = a.steps || [];
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(
					'ol',
					{ className: 'es-case-ladder' },
					steps.map(function (step, i) {
						return el(
							'li',
							{ className: stepClass(step.state), key: 'step-' + i },
							el(RichText, {
								tagName: 'span',
								placeholder: __('Etapa…', 'estavillo-portfolio-core'),
								value: step.label || '',
								allowedFormats: [],
								onChange: function (v) {
									set({ steps: ui.listUpdate(steps, i, { label: v }) });
								},
							})
						);
					})
				),
				steps.map(function (step, i) {
					return el(
						'div',
						{ className: 'es-caseb-item', key: 'ctl-' + i },
						el(
							'div',
							{ className: 'es-caseb-row' },
							el('span', { className: 'es-caseb-mini' }, (step.label || '(' + __('sin etiqueta', 'estavillo-portfolio-core') + ')').replace(/<[^>]*>/g, '')),
							el(SelectControl, {
								label: __('Estado', 'estavillo-portfolio-core'),
								hideLabelFromVision: true,
								value: step.state || 'future',
								options: STATES,
								onChange: function (v) {
									set({ steps: ui.listUpdate(steps, i, { state: v }) });
								},
							})
						),
						ui.itemBar({
							index: i,
							count: steps.length,
							onMove: function (from, to) {
								set({ steps: ui.listMove(steps, from, to) });
							},
							onRemove: function (idx) {
								set({ steps: ui.listRemove(steps, idx) });
							},
						})
					);
				}),
				ui.addButton(__('Agregar etapa', 'estavillo-portfolio-core'), function () {
					set({ steps: ui.listAdd(steps, { label: '', state: 'future' }) });
				})
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
