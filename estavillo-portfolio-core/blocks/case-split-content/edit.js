/**
 * estavillo/case-split-content — editor.
 * Región de texto de un split: wrapper único (useInnerBlocksProps sobre el
 * propio wrapper del bloque, así la región es hija directa de la grilla del
 * section — igual que en el frontend). Solo bloques de texto + figura
 * (la figura acá habilita los splits balanceados antes/después).
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;

	wp.blocks.registerBlockType('estavillo/case-split-content', {
		edit: function () {
			var blockProps = useBlockProps({ className: 'es-case-split__content es-caseb-region' });
			var innerProps = useInnerBlocksProps(blockProps, {
				allowedBlocks: [
					'core/paragraph',
					'core/heading',
					'core/list',
					'core/list-item',
					'estavillo/case-quote',
					'estavillo/case-figure',
					'core/image',
				],
				template: [['core/paragraph']],
			});
			return el('div', innerProps);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
