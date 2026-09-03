/**
 * Featured Media — admin del Case Study (panel lateral, NO Gutenberg).
 *
 * Clásico wp.media: dos pickers (video + poster) y un toggle que muestra/
 * oculta los campos de video según el select de tipo de media. Vanilla JS,
 * sin build step — mismo criterio que el resto del plugin, sólo que esta
 * pantalla no es el editor de bloques, así que no hay wp.element/wp.i18n
 * garantizados; se usa el DOM plano de siempre.
 *
 * Encolado sólo en la pantalla de edición del Case Study — ver
 * es_case_featured_media_enqueue_admin_assets() en includes/case-study-cpt.php.
 *
 * @package estavillo-portfolio-core
 */
(function () {
	'use strict';

	function initPickers() {
		var openButtons = document.querySelectorAll('[data-es-picker-open]');
		Array.prototype.forEach.call(openButtons, function (button) {
			var fieldId = button.getAttribute('data-es-picker-open');
			var library = button.getAttribute('data-es-picker-library');
			var input = document.getElementById(fieldId);
			var wrapper = button.closest('[data-es-picker-field]');
			var preview = wrapper ? wrapper.querySelector('.es-picker-preview') : null;
			var frame = null;

			button.addEventListener('click', function (e) {
				e.preventDefault();

				if (frame) {
					frame.open();
					return;
				}

				frame = wp.media({
					title: button.textContent || 'Select',
					library: library ? { type: library } : {},
					multiple: false,
				});

				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					if (input) {
						input.value = attachment.id;
					}
					if (preview) {
						preview.textContent = attachment.title || attachment.filename || ('#' + attachment.id);
					}
				});

				frame.open();
			});
		});

		var clearButtons = document.querySelectorAll('[data-es-picker-clear]');
		Array.prototype.forEach.call(clearButtons, function (button) {
			var fieldId = button.getAttribute('data-es-picker-clear');
			var input = document.getElementById(fieldId);
			var wrapper = button.closest('[data-es-picker-field]');
			var preview = wrapper ? wrapper.querySelector('.es-picker-preview') : null;

			button.addEventListener('click', function (e) {
				e.preventDefault();
				if (input) {
					input.value = '0';
				}
				if (preview) {
					preview.textContent = 'No file selected';
				}
			});
		});
	}

	function initMediaTypeToggle() {
		var select = document.querySelector('[data-es-featured-media-type]');
		var videoFields = document.querySelector('[data-es-featured-video-fields]');
		if (!select || !videoFields) {
			return;
		}
		select.addEventListener('change', function () {
			videoFields.style.display = 'video' === select.value ? '' : 'none';
		});
	}

	function init() {
		if (!window.wp || !wp.media) {
			return;
		}
		initPickers();
		initMediaTypeToggle();
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
