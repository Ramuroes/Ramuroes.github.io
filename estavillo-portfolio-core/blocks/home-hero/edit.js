/**
 * estavillo/home-hero — editor.
 *
 * Mismo modelo que case-section: el copy es InnerBlocks (editable inline,
 * guardado en post_content vía save = InnerBlocks.Content) y el cascarón
 * (sección, fondo animado, variantes) lo emite render.php en el frontend.
 * El template de abajo solo precarga el copy aprobado al insertar el
 * bloque fresco — no hay templateLock, todo sigue siendo editable y
 * reordenable como cualquier contenido Gutenberg.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;

	var TEMPLATE = [
		['core/paragraph', {
			className: 'es-eyebrow es-hero__eyebrow',
			content: 'Product Designer · Systems & Operations',
		}],
		['core/heading', {
			level: 1,
			className: 'es-h1 es-hero__title',
			content: 'I design systems that turn <em>complexity</em> into <em class="es-accent-word">clarity</em>.',
		}],
		['core/paragraph', {
			className: 'es-lead es-hero__lead',
			content: 'AI-assisted tools and workflows for real operations. From estimating to execution.',
		}],
		['core/group', { className: 'es-hero__actions' }, [
			['core/paragraph', { content: '<a class="es-btn" href="#work">View featured case</a>' }],
			['core/paragraph', { content: '<a class="es-link-arrow es-link-arrow--quiet" href="#process">See how I work</a>' }],
		]],
	];

	wp.blocks.registerBlockType('estavillo/home-hero', {
		edit: function () {
			var blockProps = useBlockProps({ className: ui.scopeClass('') });
			var innerProps = useInnerBlocksProps(
				{ className: 'es-hero__content' },
				{ template: TEMPLATE }
			);

			return el(
				'div',
				blockProps,
				el(
					'p',
					{ style: { padding: '4px 0', opacity: 0.6, fontSize: '12px' } },
					__( 'Hero copy — fully editable here. The animated background and its desktop/mobile variants render on the real site (Customize → ESTAVILLO Theme Options).', 'estavillo-portfolio-core' )
				),
				el('div', innerProps)
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
