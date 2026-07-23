/**
 * estavillo/home-hero — editor.
 *
 * Mismo modelo que case-section: el copy es InnerBlocks (editable inline,
 * guardado en post_content vía save = InnerBlocks.Content) y el cascarón
 * (sección, fondo animado, variantes, indicador de scroll) lo emite
 * render.php en el frontend.
 *
 * El grupo de CTAs usa layout FLEX nativo de Gutenberg (no una clase CSS
 * suelta): así core emite is-layout-flex y las dos CTAs previsualizan
 * horizontales EN EL EDITOR y quedan lado a lado en el front, sin depender
 * de que una regla .es-hero__actions{display:flex} sobreviva la cascada.
 *
 * Inspector: "Animated background" con dos dropdowns (desktop/mobile)
 * poblados desde window.EstavilloHeroVariants (localizado desde el mismo
 * registro es_hero_variant_choices() del theme — sin segundo registro). El
 * valor vacío = "Use theme default (Customizer)".
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

	var VARIANTS = window.EstavilloHeroVariants || { desktop: {}, mobile: {} };

	function toOptions(map) {
		var opts = [ { label: __( 'Use theme default (Customizer)', 'estavillo-portfolio-core' ), value: '' } ];
		Object.keys(map || {}).forEach(function (key) {
			opts.push({ label: map[key], value: key });
		});
		return opts;
	}

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
		['core/group', {
			className: 'es-hero__actions',
			layout: { type: 'flex', flexWrap: 'wrap', verticalAlignment: 'center', justifyContent: 'left' },
		}, [
			['core/paragraph', { content: '<a class="es-btn" href="#work">View featured case</a>' }],
			['core/paragraph', { content: '<a class="es-link-arrow es-link-arrow--quiet" href="#process">See how I work</a>' }],
		]],
	];

	wp.blocks.registerBlockType('estavillo/home-hero', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });
			var innerProps = useInnerBlocksProps(
				{ className: 'es-hero__content' },
				{ template: TEMPLATE }
			);

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Animated background', 'estavillo-portfolio-core' ), initialOpen: true },
						el('p', { style: { fontSize: '12px', opacity: 0.7, marginTop: 0 } },
							__( 'Choose the animated background for this Home. Leave on "theme default" to follow the site-wide Customizer setting.', 'estavillo-portfolio-core' )
						),
						el(SelectControl, {
							label: __( 'Background style — Desktop', 'estavillo-portfolio-core' ),
							value: a.desktopVariant || '',
							options: toOptions(VARIANTS.desktop),
							onChange: function (v) { set({ desktopVariant: v }); },
						}),
						el(SelectControl, {
							label: __( 'Background style — Mobile', 'estavillo-portfolio-core' ),
							value: a.mobileVariant || '',
							options: toOptions(VARIANTS.mobile),
							onChange: function (v) { set({ mobileVariant: v }); },
						})
					)
				),
				el(
					'div',
					blockProps,
					el('p', { style: { padding: '4px 0', opacity: 0.6, fontSize: '12px' } },
						__( 'Hero copy — fully editable here. The animated background (set under "Animated background" in the sidebar) and the scroll indicator render on the real site.', 'estavillo-portfolio-core' )
					),
					el('div', innerProps)
				)
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
