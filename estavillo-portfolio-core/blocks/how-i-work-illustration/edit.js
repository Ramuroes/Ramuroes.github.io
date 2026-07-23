/**
 * estavillo/how-i-work-illustration — editor.
 *
 * El preview usa ServerSideRender (paquete núcleo de Gutenberg, pensado
 * exactamente para este caso: un bloque dinámico cuyo render depende de
 * lógica PHP existente) en vez de reimplementar la selección/inlineado de
 * SVG en JS — así el toggle de acentos en el editor es el mismo render.php
 * real que corre en el frontend, sin duplicar los seis SVG en el bundle
 * del editor (mismo motivo por el que estavillo/hobby-list tampoco los
 * duplica, solo que acá en vez de una caja vacía + <select> se ve el
 * dibujo real).
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var ServerSideRender = wp.serverSideRender;

	var STEPS = [
		{ label: '01 — ' + __( 'Understand the system', 'estavillo-portfolio-core' ), value: 1 },
		{ label: '02 — ' + __( 'Find the real problem', 'estavillo-portfolio-core' ), value: 2 },
		{ label: '03 — ' + __( 'Gather evidence', 'estavillo-portfolio-core' ), value: 3 },
		{ label: '04 — ' + __( 'Explore and challenge', 'estavillo-portfolio-core' ), value: 4 },
		{ label: '05 — ' + __( 'Design practical solutions', 'estavillo-portfolio-core' ), value: 5 },
		{ label: '06 — ' + __( 'Test, learn and iterate', 'estavillo-portfolio-core' ), value: 6 },
	];

	var CONTEXTS = [
		{ label: __( 'Full page', 'estavillo-portfolio-core' ), value: 'page' },
		{ label: __( 'Home teaser', 'estavillo-portfolio-core' ), value: 'home' },
	];

	wp.blocks.registerBlockType('estavillo/how-i-work-illustration', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Illustration', 'estavillo-portfolio-core' ), initialOpen: true },
						el(SelectControl, {
							label: __( 'Step', 'estavillo-portfolio-core' ),
							value: a.step,
							options: STEPS,
							onChange: function (v) {
								set({ step: parseInt(v, 10) });
							},
						}),
						el(SelectControl, {
							label: __( 'Context', 'estavillo-portfolio-core' ),
							help: __( 'Solo afecta las clases de presentación (tamaño/posición vía CSS) — la ilustración es la misma.', 'estavillo-portfolio-core' ),
							value: a.context,
							options: CONTEXTS,
							onChange: function (v) {
								set({ context: v });
							},
						})
					),
					el(
						PanelBody,
						{ title: __( 'Appearance', 'estavillo-portfolio-core' ), initialOpen: true },
						el(ToggleControl, {
							label: __( 'Show green accents', 'estavillo-portfolio-core' ),
							checked: a.showAccents !== false,
							onChange: function (v) {
								set({ showAccents: v });
							},
						})
					),
					el(
						PanelBody,
						{ title: __( 'Accessibility', 'estavillo-portfolio-core' ), initialOpen: true },
						el(ToggleControl, {
							label: __( 'Decorative illustration', 'estavillo-portfolio-core' ),
							help: __( 'Activado (default): oculta la ilustración de lectores de pantalla — usalo cuando el título/texto adyacente ya explica qué representa. Desactivado: agrega una etiqueta accesible corta basada en el paso elegido.', 'estavillo-portfolio-core' ),
							checked: a.decorative !== false,
							onChange: function (v) {
								set({ decorative: v });
							},
						})
					)
				),
				el(
					'div',
					blockProps,
					el(ServerSideRender, {
						block: 'estavillo/how-i-work-illustration',
						attributes: a,
					})
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
