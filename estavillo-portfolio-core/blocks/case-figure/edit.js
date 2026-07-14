/**
 * estavillo/case-figure — editor.
 * MediaUpload/MediaUploadCheck para la imagen; variante, alt, tag y
 * placeholder en el inspector; caption inline debajo de la imagen.
 */
(function (wp, ui) {
	'use strict';

	var el = ui.el;
	var __ = wp.i18n.__;
	var Fragment = wp.element.Fragment;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var SelectControl = wp.components.SelectControl;
	var Button = wp.components.Button;

	function dots() {
		return [
			el('span', { className: 'es-case-browser__dot', key: 'd1' }),
			el('span', { className: 'es-case-browser__dot', key: 'd2' }),
			el('span', { className: 'es-case-browser__dot', key: 'd3' }),
		];
	}

	wp.blocks.registerBlockType('estavillo/case-figure', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });

			var media = a.url
				? el('img', { src: a.url, alt: a.alt || '' })
				: el(
						'div',
						{ className: 'es-placeholder', role: 'img', 'aria-label': a.alt || __('Placeholder de imagen pendiente', 'estavillo-portfolio-core') },
						el('span', { className: 'es-placeholder__tag' }, '{asset: ' + (a.placeholderLabel || 'pending') + '}')
				  );

			if (a.variant === 'browser') {
				media = el(
					'div',
					{ className: 'es-case-browser' },
					el(
						'div',
						{ className: 'es-case-browser__bar' },
						dots(),
						a.browserLabel ? el('span', { className: 'es-case-browser__label' }, a.browserLabel) : null
					),
					media
				);
			}

			var pickButton = el(MediaUploadCheck, null,
				el(MediaUpload, {
					allowedTypes: ['image'],
					value: a.mediaId,
					onSelect: function (m) {
						set({ mediaId: m.id, url: m.url, alt: m.alt || a.alt });
					},
					render: function (o) {
						return el(
							'div',
							{ className: 'es-caseb-itembar', style: { justifyContent: 'flex-start' } },
							el(Button, { variant: 'secondary', size: 'small', onClick: o.open },
								a.url ? __('Reemplazar imagen', 'estavillo-portfolio-core') : __('Elegir imagen', 'estavillo-portfolio-core')),
							a.url
								? el(Button, {
										variant: 'tertiary',
										size: 'small',
										isDestructive: true,
										onClick: function () {
											set({ mediaId: 0, url: '', alt: '' });
										},
								  }, __('Quitar', 'estavillo-portfolio-core'))
								: null
						);
					},
				})
			);

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Figure', 'estavillo-portfolio-core'), initialOpen: true },
						el(SelectControl, {
							label: __('Variante', 'estavillo-portfolio-core'),
							value: a.variant,
							options: [
								{ label: __('Standard — a medida de lectura', 'estavillo-portfolio-core'), value: 'standard' },
								{ label: __('Browser — marco tipo navegador', 'estavillo-portfolio-core'), value: 'browser' },
								{ label: __('Wide — ancho completo', 'estavillo-portfolio-core'), value: 'wide' },
							],
							onChange: function (v) {
								set({ variant: v });
							},
						}),
						el(TextareaControl, {
							label: __('Texto alternativo (alt)', 'estavillo-portfolio-core'),
							value: a.alt,
							onChange: function (v) {
								set({ alt: v });
							},
						}),
						el(TextControl, {
							label: __('Tag de la caption (p. ej. "01")', 'estavillo-portfolio-core'),
							value: a.tag,
							onChange: function (v) {
								set({ tag: v });
							},
						}),
						el(TextControl, {
							label: __('Label del placeholder (sin imagen)', 'estavillo-portfolio-core'),
							help: __('Se imprime como {asset: label} — mismo contrato que el plan de assets.', 'estavillo-portfolio-core'),
							value: a.placeholderLabel,
							onChange: function (v) {
								set({ placeholderLabel: v });
							},
						}),
						a.variant === 'browser'
							? el(TextControl, {
									label: __('Label de la barra del browser', 'estavillo-portfolio-core'),
									value: a.browserLabel,
									onChange: function (v) {
										set({ browserLabel: v });
									},
							  })
							: null
					)
				),
				el(
					'div',
					blockProps,
					el(
						'figure',
						{ className: 'es-case-figure' + (a.variant === 'standard' ? ' es-case-figure--standard' : '') },
						media,
						el(
							'figcaption',
							{ className: 'es-case-caption' },
							a.tag ? el('span', { className: 'es-case-caption__tag' }, a.tag) : null,
							el(RichText, {
								tagName: 'span',
								placeholder: __('Caption…', 'estavillo-portfolio-core'),
								value: a.caption,
								allowedFormats: ['core/italic'],
								onChange: function (v) {
									set({ caption: v });
								},
							})
						)
					),
					pickButton
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp, window.esCaseUI);
