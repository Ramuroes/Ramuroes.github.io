/**
 * estavillo/case-section — editor.
 *
 * Sistema editorial v2: el editor elige PRESETS con nombre humano (layout /
 * spacing de capítulo / orden mobile), nunca columnas ni px. El preview usa
 * el MISMO wrapper .es-case-section__grid que el frontend (via
 * useInnerBlocksProps, así los bloques hijos son hijos directos de la
 * grilla) — misma composición en editor y frontend.
 *
 * Guardrails:
 * - splits: template bloqueado [contenido, media] — al elegir un split los
 *   bloques existentes se mueven a la región Contenido (reversible con
 *   deshacer); al salir del split las regiones se desenvuelven.
 * - wide de solo texto: aviso en el preview (Wide es para artefactos).
 * - chip de variante (solo editor, nunca en el frontend).
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var RichText = wp.blockEditor.RichText;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var Notice = wp.components.Notice;

	var SPLIT_CONTENT = 'estavillo/case-split-content';
	var SPLIT_MEDIA = 'estavillo/case-split-media';

	// Bloques que "ganan" el ancho completo de un layout Wide. Un Wide sin
	// ninguno de estos es solo texto estirado — para eso está Reading.
	var ARTIFACT_BLOCKS = [
		'estavillo/case-figure',
		'estavillo/case-stats',
		'estavillo/case-ladder',
		'estavillo/case-taxonomy',
		'estavillo/case-timeline',
		'estavillo/case-decisions',
		'estavillo/case-status',
		'core/image',
		'core/video',
		'core/embed',
		'core/gallery',
		'core/table',
	];

	var LAYOUT_LABELS = {
		'reading': __('Reading', 'estavillo-portfolio-core'),
		'split-left': __('Split — texto a la izquierda', 'estavillo-portfolio-core'),
		'split-right': __('Split — imagen a la izquierda', 'estavillo-portfolio-core'),
		'split-balanced': __('Split balanceado', 'estavillo-portfolio-core'),
		'wide': __('Wide artifact', 'estavillo-portfolio-core'),
	};

	var SPACING_LABELS = {
		'compact': __('Compact', 'estavillo-portfolio-core'),
		'standard': __('Standard', 'estavillo-portfolio-core'),
		'spacious': __('Spacious', 'estavillo-portfolio-core'),
	};

	function isSplitLayout(layout) {
		return layout.indexOf('split-') === 0;
	}

	function isRegionBlock(block) {
		return block.name === SPLIT_CONTENT || block.name === SPLIT_MEDIA;
	}

	function sectionClasses(a) {
		var c = 'es-case-section es-case-section--sp-' + (a.spacing || 'standard');
		if (a.layout === 'reading') {
			c += ' es-case-section--reading';
		} else if (isSplitLayout(a.layout)) {
			c += ' es-case-section--split es-case-section--' + a.layout;
			if (a.mobileOrder) {
				c += ' es-case-section--m-' + a.mobileOrder;
			}
		}
		return c;
	}

	wp.blocks.registerBlockType('estavillo/case-section', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var isSplit = isSplitLayout(a.layout);

			var innerBlocks = wp.data.useSelect(
				function (select) {
					return select('core/block-editor').getBlocks(props.clientId);
				},
				[props.clientId]
			);
			var replaceInnerBlocks = wp.data.useDispatch('core/block-editor').replaceInnerBlocks;

			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			// La grilla del editor = la grilla del frontend: useInnerBlocksProps
			// hace que los wrappers de los bloques hijos sean hijos DIRECTOS del
			// div .es-case-section__grid (en wide no hay grilla — div neutro).
			var innerProps = useInnerBlocksProps(
				{ className: a.layout === 'wide' ? 'es-caseb-flat' : 'es-case-section__grid' },
				isSplit
					? {
						template: [[SPLIT_CONTENT], [SPLIT_MEDIA]],
						templateLock: 'all',
					}
					: {}
			);

			// Cambio de layout con reestructuración segura (y deshacible):
			// hacia split → los hijos actuales se mueven a la región Contenido;
			// desde split → las regiones se desenvuelven en flujo plano.
			function onLayoutChange(v) {
				var toSplit = isSplitLayout(v);
				var fromSplit = isSplitLayout(a.layout);
				var alreadyRegions =
					innerBlocks.length === 2 &&
					innerBlocks[0].name === SPLIT_CONTENT &&
					innerBlocks[1].name === SPLIT_MEDIA;

				if (toSplit && !fromSplit && !alreadyRegions) {
					var moved = innerBlocks.map(function (b) {
						return wp.blocks.cloneBlock(b);
					});
					replaceInnerBlocks(
						props.clientId,
						[
							wp.blocks.createBlock(SPLIT_CONTENT, {}, moved),
							wp.blocks.createBlock(SPLIT_MEDIA, {}, []),
						],
						false
					);
				} else if (!toSplit && fromSplit) {
					var flat = [];
					innerBlocks.forEach(function (b) {
						if (isRegionBlock(b)) {
							b.innerBlocks.forEach(function (c) {
								flat.push(wp.blocks.cloneBlock(c));
							});
						} else {
							flat.push(wp.blocks.cloneBlock(b));
						}
					});
					replaceInnerBlocks(props.clientId, flat, false);
				}
				set({ layout: v });
			}

			var wideTextOnly =
				a.layout === 'wide' &&
				innerBlocks.length > 0 &&
				!innerBlocks.some(function (b) {
					return ARTIFACT_BLOCKS.indexOf(b.name) !== -1;
				});

			var header = [
				a.anchor ? el('div', { className: 'es-caseb-mini', key: 'anchor' }, '#' + a.anchor) : null,
				el(RichText, {
					key: 'label',
					tagName: 'div',
					className: 'es-case-label',
					placeholder: __('Fig. 00 — Etiqueta', 'estavillo-portfolio-core'),
					value: a.label,
					allowedFormats: [],
					onChange: function (v) {
						set({ label: v });
					},
				}),
				el(RichText, {
					key: 'heading',
					tagName: 'h2',
					className: 'es-case-heading',
					placeholder: __('Heading de la sección', 'estavillo-portfolio-core'),
					value: a.heading,
					allowedFormats: ['core/italic'],
					onChange: function (v) {
						set({ heading: v });
					},
				}),
				el(RichText, {
					key: 'lead',
					tagName: 'p',
					className: 'es-case-lead',
					placeholder: __('Lead opcional…', 'estavillo-portfolio-core'),
					value: a.lead,
					allowedFormats: ['core/bold', 'core/italic', 'core/link'],
					onChange: function (v) {
						set({ lead: v });
					},
				}),
			];

			var variantChip = el(
				'div',
				{ className: 'es-caseb-variant' },
				LAYOUT_LABELS[a.layout] + ' · ' + SPACING_LABELS[a.spacing || 'standard']
			);

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Section', 'estavillo-portfolio-core'), initialOpen: true },
						el(TextControl, {
							label: __('Anchor (id)', 'estavillo-portfolio-core'),
							help: __('Sin #. Tiene que coincidir con la línea correspondiente del campo "Case index" (Label|#anchor).', 'estavillo-portfolio-core'),
							value: a.anchor,
							onChange: function (v) {
								set({ anchor: v });
							},
						})
					),
					el(
						PanelBody,
						{ title: __('Composición', 'estavillo-portfolio-core'), initialOpen: true },
						el(SelectControl, {
							label: __('Layout', 'estavillo-portfolio-core'),
							value: a.layout,
							options: [
								{ label: __('Reading — narrativa larga, medida de lectura', 'estavillo-portfolio-core'), value: 'reading' },
								{ label: __('Split — texto a la izquierda, media a la derecha (5/7)', 'estavillo-portfolio-core'), value: 'split-left' },
								{ label: __('Split — imagen a la izquierda, texto a la derecha (7/5)', 'estavillo-portfolio-core'), value: 'split-right' },
								{ label: __('Split balanceado — antes/después, comparaciones (6/6)', 'estavillo-portfolio-core'), value: 'split-balanced' },
								{ label: __('Wide artifact — figura/stats/diagrama a ancho completo', 'estavillo-portfolio-core'), value: 'wide' },
							],
							onChange: onLayoutChange,
						}),
						isSplit
							? el(SelectControl, {
								label: __('Orden en mobile', 'estavillo-portfolio-core'),
								help: __('Cómo se apilan las dos regiones en pantallas chicas.', 'estavillo-portfolio-core'),
								value: a.mobileOrder,
								options: [
									{ label: __('Orden de desktop (default)', 'estavillo-portfolio-core'), value: '' },
									{ label: __('Contenido primero', 'estavillo-portfolio-core'), value: 'content-first' },
									{ label: __('Media primero', 'estavillo-portfolio-core'), value: 'media-first' },
								],
								onChange: function (v) {
									set({ mobileOrder: v });
								},
							})
							: null,
						el(SelectControl, {
							label: __('Espaciado del capítulo', 'estavillo-portfolio-core'),
							value: a.spacing || 'standard',
							options: [
								{ label: __('Compact', 'estavillo-portfolio-core'), value: 'compact' },
								{ label: __('Standard', 'estavillo-portfolio-core'), value: 'standard' },
								{ label: __('Spacious', 'estavillo-portfolio-core'), value: 'spacious' },
							],
							onChange: function (v) {
								set({ spacing: v });
							},
						})
					)
				),
				el(
					'div',
					blockProps,
					el(
						'div',
						{ className: sectionClasses(a) },
						variantChip,
						wideTextOnly
							? el(
								Notice,
								{ status: 'warning', isDismissible: false, className: 'es-caseb-notice' },
								__('Wide es para artefactos (Figure, Stats, Ladder, Taxonomy…). Para un capítulo de solo texto usá el layout Reading.', 'estavillo-portfolio-core')
							)
							: null,
						a.layout === 'reading'
							? el('div', { className: 'es-case-section__grid es-caseb-headgrid' }, header)
							: header,
						el('div', innerProps)
					)
				)
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})(window.wp, window.esCaseUI);
