/**
 * estavillo/how-i-work-teaser — editor.
 *
 * Un solo bloque envoltorio con las tres ideas como InnerBlocks (cada una:
 * ilustración + título + texto, copy editable inline). Dos SelectControls
 * en el Inspector ("Teaser layout") — Desktop layout y Illustration size —
 * se aplican como clases modificadoras al wrapper de las InnerBlocks, así
 * el editor previsualiza stacked/side y small/medium/large en vivo (la CSS
 * del theme está puenteada al editor). save = InnerBlocks.Content; el
 * frontend lo envuelve render.php con las mismas clases.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;

	function concept(step, title, text) {
		return ['core/group', { className: 'es-process-teaser__group' }, [
			['estavillo/how-i-work-illustration', { step: step, context: 'home' }],
			['core/heading', { level: 4, className: 'es-process-teaser__group-title', content: title }],
			['core/paragraph', { className: 'es-process-teaser__group-text', content: text }],
		]];
	}

	var TEMPLATE = [
		concept(1, 'Understand', 'See how people, information and goals actually connect.'),
		concept(4, 'Explore', 'Test ideas and challenge assumptions before committing to one.'),
		concept(6, 'Improve', 'Build something that works &#8212; and keeps working.'),
	];

	function rowClass(a) {
		var layout = a.desktopLayout === 'side' ? 'side' : 'stacked';
		var size = ['small', 'medium', 'large'].indexOf(a.illustrationSize) !== -1 ? a.illustrationSize : 'small';
		return 'es-process-teaser__row es-process-teaser__row--' + layout + ' es-process-teaser__row--illus-' + size;
	}

	wp.blocks.registerBlockType('estavillo/how-i-work-teaser', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps();
			var innerProps = useInnerBlocksProps(
				{ className: rowClass(a) },
				{ template: TEMPLATE, templateLock: false }
			);

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Teaser layout', 'estavillo-portfolio-core' ), initialOpen: true },
						el(SelectControl, {
							label: __( 'Desktop layout', 'estavillo-portfolio-core' ),
							help: __( 'Mobile always stacks the illustration above the text.', 'estavillo-portfolio-core' ),
							value: a.desktopLayout || 'stacked',
							options: [
								{ label: __( 'Illustration above', 'estavillo-portfolio-core' ), value: 'stacked' },
								{ label: __( 'Illustration beside text', 'estavillo-portfolio-core' ), value: 'side' },
							],
							onChange: function (v) { set({ desktopLayout: v }); },
						}),
						el(SelectControl, {
							label: __( 'Illustration size', 'estavillo-portfolio-core' ),
							value: a.illustrationSize || 'small',
							options: [
								{ label: __( 'Small', 'estavillo-portfolio-core' ), value: 'small' },
								{ label: __( 'Medium', 'estavillo-portfolio-core' ), value: 'medium' },
								{ label: __( 'Large', 'estavillo-portfolio-core' ), value: 'large' },
							],
							onChange: function (v) { set({ illustrationSize: v }); },
						})
					)
				),
				el('div', blockProps, el('div', innerProps))
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
