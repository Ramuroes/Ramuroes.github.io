/**
 * estavillo/about-teaser-text — editor.
 *
 * Sin atributos: el texto real vive en wp-admin → Portfolio Content →
 * About (mismo campo que alimenta la página About). ServerSideRender
 * muestra el párrafo real, mismo patrón que el resto de los bloques de
 * Home.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType('estavillo/about-teaser-text', {
		edit: function () {
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(
					'p',
					{ style: { padding: '4px 0', opacity: 0.6, fontSize: '12px' } },
					__( 'Reads from wp-admin → Portfolio Content → About. Edit the text there, not here.', 'estavillo-portfolio-core' )
				),
				el(ServerSideRender, {
					block: 'estavillo/about-teaser-text',
					attributes: {},
				})
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
