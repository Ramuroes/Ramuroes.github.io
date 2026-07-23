/**
 * estavillo/about-teaser-portrait — editor.
 *
 * Sin atributos: la imagen se define en wp-admin → Portfolio Content →
 * About → "Portrait image URL" (compartida con la página About).
 * ServerSideRender muestra el estado real, mismo patrón que el resto de
 * los bloques de Home.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType('estavillo/about-teaser-portrait', {
		edit: function () {
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(
					'p',
					{ style: { padding: '4px 0', opacity: 0.6, fontSize: '12px' } },
					__( 'Reads Portfolio Content → About → Portrait image URL (shared with the About page). Set it there once — it updates both pages.', 'estavillo-portfolio-core' )
				),
				el(ServerSideRender, {
					block: 'estavillo/about-teaser-portrait',
					attributes: {},
				})
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
