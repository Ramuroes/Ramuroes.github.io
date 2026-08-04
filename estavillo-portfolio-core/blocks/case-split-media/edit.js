/**
 * estavillo/case-split-media — editor.
 * Región de media de un split: wrapper único (useInnerBlocksProps sobre el
 * propio wrapper del bloque — hija directa de la grilla del section, igual
 * que en el frontend). Solo bloques orientados a media.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;

	wp.blocks.registerBlockType('estavillo/case-split-media', {
		edit: function () {
			var blockProps = useBlockProps({ className: 'es-case-split__media es-caseb-region' });
			var innerProps = useInnerBlocksProps(blockProps, {
				allowedBlocks: [
					'estavillo/case-figure',
					'core/image',
					'core/video',
					'core/embed',
				],
				template: [['estavillo/case-figure']],
			});
			return el('div', innerProps);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
