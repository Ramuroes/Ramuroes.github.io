/**
 * estavillo/featured-case — editor.
 *
 * Sin atributos: no hay nada que configurar en este bloque (qué caso se
 * muestra se decide en el propio Case Study — casilla "Feature this case
 * on Home"). El preview usa ServerSideRender para mostrar el caso real tal
 * cual queda en el frontend, mismo patrón que estavillo/how-i-work-illustration.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType('estavillo/featured-case', {
		edit: function () {
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(ServerSideRender, {
					block: 'estavillo/featured-case',
					attributes: {},
					EmptyResponsePlaceholder: function () {
						return el(
							'p',
							{ style: { padding: '16px', opacity: 0.7 } },
							__(
								'No case is marked "Feature this case on Home" yet — mark one from Case Studies → (edit a case) → Case details, or this stays empty on the front end.',
								'estavillo-portfolio-core'
							)
						);
					},
				})
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
