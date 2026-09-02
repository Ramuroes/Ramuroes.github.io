/**
 * estavillo/case-figure — editor.
 * MediaUpload/MediaUploadCheck para la imagen; tipo de media, variante
 * (tamaño), alt, tag y placeholder en el inspector; caption inline debajo
 * de la imagen.
 *
 * Tamaño (`variant`) y tipo de media (`mediaType`) — ver el docblock de
 * render.php para el porqué de nombres/valores: 'standard' sigue siendo el
 * default de siempre; 'large' es el nombre nuevo del ancho completo
 * (comparte CSS con el legado 'wide' — misma clase, cero duplicación);
 * 'full' es el sangrado real al viewport (visual break / hero), sin
 * equivalente legado.
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
	var ToggleControl = wp.components.ToggleControl;
	var Button = wp.components.Button;

	function dots() {
		return [
			el('span', { className: 'es-case-browser__dot', key: 'd1' }),
			el('span', { className: 'es-case-browser__dot', key: 'd2' }),
			el('span', { className: 'es-case-browser__dot', key: 'd3' }),
		];
	}

	// Lupa con "+": mismo trazo lineal que el resto de los íconos del
	// portfolio (case-flow, hobbies) — nada de librerías ni emoji. Sólo un
	// indicador en el editor de que el zoom está activo; el real (con el
	// mismo dibujo) lo emite render.php en el frontend.
	function zoomBadge() {
		return el(
			'svg',
			{ className: 'es-case-figure__zoom-badge', viewBox: '0 0 20 20', 'aria-hidden': 'true', focusable: 'false' },
			el('circle', { cx: '8', cy: '8', r: '5.5' }),
			el('line', { x1: '12.2', y1: '12.2', x2: '17', y2: '17' }),
			el('line', { x1: '5.2', y1: '8', x2: '10.8', y2: '8' }),
			el('line', { x1: '8', y1: '5.2', x2: '8', y2: '10.8' })
		);
	}

	wp.blocks.registerBlockType('estavillo/case-figure', {
		edit: function (props) {
			var a = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps({ className: ui.scopeClass('') });
			var isVideo = a.mediaType === 'video';

			// Preview WYSIWYG del maxHeight/objectFit (sólo tiene efecto visual
			// con maxHeight seteado — mismo criterio que render.php): sin esto
			// el editor no reflejaría un ajuste que el frontend sí aplica.
			var mediaStyle = a.mediaMaxHeight
				? { maxHeight: a.mediaMaxHeight, objectFit: 'contain' === a.mediaObjectFit ? 'contain' : 'cover', width: '100%' }
				: undefined;

			var media;
			if (a.url) {
				media = isVideo
					? el('video', {
							className: 'es-case-figure__video',
							src: a.url,
							poster: a.videoPosterUrl || undefined,
							style: mediaStyle,
							muted: !!a.videoMuted,
							loop: !!a.videoLoop,
							playsInline: true,
							// Siempre con controles en el editor, aunque
							// "Mostrar controles" (videoControls) esté OFF: sin
							// esto no hay forma de scrubbear/pausar el preview
							// para revisar el poster. El frontend sí respeta
							// videoControls tal cual — esto es sólo
							// conveniencia de edición, no cambia lo publicado.
							controls: true,
					  })
					: el('img', { src: a.url, alt: a.alt || '', style: mediaStyle });
			} else {
				var phLabel = isVideo
					? __('Placeholder de video pendiente', 'estavillo-portfolio-core')
					: __('Placeholder de imagen pendiente', 'estavillo-portfolio-core');
				media = el(
					'div',
					{ className: 'es-placeholder', role: 'img', 'aria-label': a.alt || phLabel },
					el('span', { className: 'es-placeholder__tag' }, '{asset: ' + (a.placeholderLabel || 'pending') + '}')
				);
			}

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
					allowedTypes: isVideo ? ['video'] : ['image'],
					value: a.mediaId,
					onSelect: function (m) {
						set({ mediaId: m.id, url: m.url, alt: m.alt || a.alt });
					},
					render: function (o) {
						return el(
							'div',
							{ className: 'es-caseb-itembar', style: { justifyContent: 'flex-start' } },
							el(Button, { variant: 'secondary', size: 'small', onClick: o.open },
								a.url
									? (isVideo ? __('Reemplazar video', 'estavillo-portfolio-core') : __('Reemplazar imagen', 'estavillo-portfolio-core'))
									: (isVideo ? __('Elegir video', 'estavillo-portfolio-core') : __('Elegir imagen', 'estavillo-portfolio-core'))),
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

			// Opciones de tamaño: 'wide' (legado) sólo se ofrece si el bloque
			// YA lo tiene guardado — así el <select> muestra el valor real de
			// contenido existente sin forzar una migración, pero ningún
			// bloque nuevo puede volver a elegirlo (usa 'large' en su lugar,
			// mismo resultado visual/CSS).
			var variantOptions = [
				{ label: __('Small — a medida de lectura (default)', 'estavillo-portfolio-core'), value: 'standard' },
				{ label: __('Large — ancho protagonista', 'estavillo-portfolio-core'), value: 'large' },
				{ label: __('Full width — visual break / hero', 'estavillo-portfolio-core'), value: 'full' },
				{ label: __('Browser — marco tipo navegador', 'estavillo-portfolio-core'), value: 'browser' },
			];
			if ('wide' === a.variant) {
				variantOptions.splice(2, 0, { label: __('Wide (legado — igual que Large)', 'estavillo-portfolio-core'), value: 'wide' });
			}

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
							label: __('Tipo de media', 'estavillo-portfolio-core'),
							value: a.mediaType,
							options: [
								{ label: __('Imagen estática (JPG/PNG/WEBP)', 'estavillo-portfolio-core'), value: 'image' },
								{ label: __('GIF animado', 'estavillo-portfolio-core'), value: 'gif' },
								{ label: __('Video (MP4/WebM)', 'estavillo-portfolio-core'), value: 'video' },
							],
							onChange: function (v) {
								// Cambiar de tipo limpia la selección: un mediaId de
								// imagen quedaría apuntando al archivo equivocado si
								// se interpreta como video (o viceversa). alt/caption/
								// tag NO se tocan — siguen siendo válidos para el
								// media nuevo que se elija.
								set({ mediaType: v, mediaId: 0, url: '' });
							},
						}),
						el(SelectControl, {
							label: __('Tamaño', 'estavillo-portfolio-core'),
							value: a.variant,
							options: variantOptions,
							onChange: function (v) {
								set({ variant: v });
							},
						}),
						el(TextareaControl, {
							label: __('Texto alternativo (alt)', 'estavillo-portfolio-core'),
							help: isVideo
								? __('Si el video NO es decorativo, se usa como su nombre accesible (no hay atributo alt nativo en <video>).', 'estavillo-portfolio-core')
								: undefined,
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
							: null,
						isVideo
							? el(
									Fragment,
									null,
									el(MediaUploadCheck, null,
										el(MediaUpload, {
											allowedTypes: ['image'],
											value: a.videoPosterId,
											onSelect: function (m) {
												set({ videoPosterId: m.id, videoPosterUrl: m.url });
											},
											render: function (o) {
												return el(
													'div',
													{ className: 'es-caseb-itembar', style: { justifyContent: 'flex-start' } },
													el(Button, { variant: 'secondary', size: 'small', onClick: o.open },
														a.videoPosterUrl ? __('Reemplazar poster', 'estavillo-portfolio-core') : __('Elegir poster', 'estavillo-portfolio-core')),
													a.videoPosterUrl
														? el(Button, {
																variant: 'tertiary',
																size: 'small',
																isDestructive: true,
																onClick: function () {
																	set({ videoPosterId: 0, videoPosterUrl: '' });
																},
														  }, __('Quitar poster', 'estavillo-portfolio-core'))
														: null
												);
											},
										})
									),
									el(ToggleControl, {
										label: __('Autoplay', 'estavillo-portfolio-core'),
										help: __('Arranca solo, sin interacción. Respeta prefers-reduced-motion del visitante — si tiene reducción de movimiento activada, el video no arranca solo y se ve el poster.', 'estavillo-portfolio-core'),
										checked: !! a.videoAutoplay,
										onChange: function (v) {
											set({ videoAutoplay: v });
										},
									}),
									el(ToggleControl, {
										label: __('Loop', 'estavillo-portfolio-core'),
										checked: !! a.videoLoop,
										onChange: function (v) {
											set({ videoLoop: v });
										},
									}),
									el(ToggleControl, {
										label: __('Silenciado (muted)', 'estavillo-portfolio-core'),
										help: __('La mayoría de los navegadores bloquea el autoplay si el video no está silenciado.', 'estavillo-portfolio-core'),
										checked: !! a.videoMuted,
										onChange: function (v) {
											set({ videoMuted: v });
										},
									}),
									el(ToggleControl, {
										label: __('Mostrar controles', 'estavillo-portfolio-core'),
										help: __('Para video convencional (con audio/narración) en vez de animación tipo GIF/hero.', 'estavillo-portfolio-core'),
										checked: !! a.videoControls,
										onChange: function (v) {
											set({ videoControls: v });
										},
									}),
									el(ToggleControl, {
										label: __('Video decorativo', 'estavillo-portfolio-core'),
										help: __('Sin narración ni información que no esté también en el texto del caso. Si lo desmarcás, el alt de arriba se usa como nombre accesible.', 'estavillo-portfolio-core'),
										checked: !! a.videoDecorative,
										onChange: function (v) {
											set({ videoDecorative: v });
										},
									})
							  )
							: null,
						a.variant === 'full'
							? el(
									Fragment,
									null,
									el(SelectControl, {
										label: __('Ajuste del contenido (object-fit)', 'estavillo-portfolio-core'),
										help: __('Sólo tiene efecto si definís un alto máximo abajo.', 'estavillo-portfolio-core'),
										value: a.mediaObjectFit,
										options: [
											{ label: __('Cover — llena el marco (puede recortar bordes)', 'estavillo-portfolio-core'), value: 'cover' },
											{ label: __('Contain — se ve completo (puede agregar espacio)', 'estavillo-portfolio-core'), value: 'contain' },
										],
										onChange: function (v) {
											set({ mediaObjectFit: v });
										},
									}),
									el(TextControl, {
										label: __('Alto máximo (CSS, opcional)', 'estavillo-portfolio-core'),
										help: __('p. ej. "70vh" — para media muy vertical en Full width. Vacío = sin límite, se conserva el aspect ratio completo.', 'estavillo-portfolio-core'),
										value: a.mediaMaxHeight,
										onChange: function (v) {
											set({ mediaMaxHeight: v });
										},
									})
							  )
							: null,
						! isVideo
							? el(ToggleControl, {
									label: __('Permitir ampliar imagen', 'estavillo-portfolio-core'),
									help: __(
										'Suma un visor a pantalla completa (click o tap) con zoom y desplazamiento. Recomendado para Journey Maps, tablas y diagramas — el contenido interno queda chico en mobile sin esto.',
										'estavillo-portfolio-core'
									),
									checked: !! a.enableZoom,
									onChange: function (v) {
										set({ enableZoom: v });
									},
							  })
							: null,
						! isVideo && a.enableZoom
							? el(
									Fragment,
									null,
									el(TextControl, {
										label: __('Texto accesible del disparador', 'estavillo-portfolio-core'),
										help: __('Nombre accesible del botón que abre el visor (lectores de pantalla).', 'estavillo-portfolio-core'),
										value: a.zoomLabel,
										onChange: function (v) {
											set({ zoomLabel: v });
										},
									}),
									el(TextControl, {
										label: __('Texto accesible de "Cerrar"', 'estavillo-portfolio-core'),
										value: a.zoomCloseLabel,
										onChange: function (v) {
											set({ zoomCloseLabel: v });
										},
									}),
									el(TextControl, {
										label: __('Texto accesible de "Acercar"', 'estavillo-portfolio-core'),
										value: a.zoomInLabel,
										onChange: function (v) {
											set({ zoomInLabel: v });
										},
									}),
									el(TextControl, {
										label: __('Texto accesible de "Alejar"', 'estavillo-portfolio-core'),
										value: a.zoomOutLabel,
										onChange: function (v) {
											set({ zoomOutLabel: v });
										},
									}),
									el(TextControl, {
										label: __('Texto accesible de "Restablecer"', 'estavillo-portfolio-core'),
										value: a.zoomResetLabel,
										onChange: function (v) {
											set({ zoomResetLabel: v });
										},
									})
							  )
							: null
					)
				),
				el(
					'div',
					blockProps,
					el(
						'figure',
						{
							className:
								'es-case-figure' +
								(a.variant === 'standard' ? ' es-case-figure--standard' : '') +
								(a.variant === 'wide' || a.variant === 'large' ? ' es-case-figure--wide' : '') +
								(a.variant === 'full' ? ' es-case-figure--full' : '') +
								(!isVideo && a.enableZoom ? ' es-case-figure--zoomable' : ''),
						},
						!isVideo && a.enableZoom ? zoomBadge() : null,
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
