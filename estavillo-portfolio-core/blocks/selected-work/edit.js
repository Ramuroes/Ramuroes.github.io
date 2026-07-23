/**
 * estavillo/selected-work — editor.
 *
 * Sin atributos: qué casos aparecen y en qué orden se decide en cada Case
 * Study ("Show this case in Home → Selected Work" + el campo nativo
 * "Order"), no en este bloque. ServerSideRender muestra la grilla real,
 * mismo patrón que estavillo/featured-case y estavillo/how-i-work-illustration.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType('estavillo/selected-work', {
		edit: function () {
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				'div',
				blockProps,
				el(ServerSideRender, {
					block: 'estavillo/selected-work',
					attributes: {},
					EmptyResponsePlaceholder: function () {
						return el(
							'p',
							{ style: { padding: '16px', opacity: 0.7 } },
							__(
								'No published Case Study is marked "Show in Home → Selected Work" yet — check that box on a Case Study, or this stays empty on the front end.',
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
