/**
 * Estavillo Case Study blocks — helpers compartidos del editor.
 *
 * Sin build step: JS plano, wp.* globals. Cada edit.js de blocks/* declara
 * este handle como dependencia y usa window.esCaseUI.
 *
 * Qué expone:
 * - el:            alias de wp.element.createElement
 * - listUpdate / listMove / listRemove / listAdd: operaciones inmutables
 *                  sobre atributos array (nunca mutar el array del atributo:
 *                  siempre devolver copia nueva — así undo/redo funciona)
 * - itemBar():     toolbar de ítem repetible (subir / bajar / borrar)
 * - addButton():   botón "agregar ítem"
 * - scopeProps():  className del wrapper del preview — incluye es-case__body
 *                  para que la librería .es-case-* del theme (puenteada al
 *                  editor por case-blocks.php) matchee tal cual el frontend.
 */
(function (wp) {
	'use strict';

	var el = wp.element.createElement;
	var Button = wp.components.Button;
	var __ = wp.i18n.__;

	function listUpdate(list, index, patch) {
		return (list || []).map(function (item, i) {
			if (i !== index) {
				return item;
			}
			if (typeof item === 'object' && item !== null) {
				return Object.assign({}, item, patch);
			}
			return patch;
		});
	}

	function listMove(list, from, to) {
		var next = (list || []).slice();
		if (to < 0 || to >= next.length) {
			return next;
		}
		var moved = next.splice(from, 1)[0];
		next.splice(to, 0, moved);
		return next;
	}

	function listRemove(list, index) {
		return (list || []).filter(function (_, i) {
			return i !== index;
		});
	}

	function listAdd(list, item) {
		return (list || []).concat([item]);
	}

	/**
	 * Toolbar de un ítem repetible: subir / bajar / (duplicar) / borrar.
	 *
	 * Duplicar es opcional (o.onDuplicate) — ningún bloque existente lo pasa,
	 * así que para los 16 bloques de siempre esto renderiza exactamente igual
	 * que antes. o.duplicateIcon acepta un ícono custom (string de dashicon o
	 * elemento SVG); sin él cae a un dashicon genérico.
	 *
	 * @param {Object} o { index, count, onMove(from,to), onRemove(index), onDuplicate?(index), duplicateIcon? }
	 */
	function itemBar(o) {
		var buttons = [
			el(Button, {
				key: 'up',
				icon: 'arrow-up-alt2',
				label: __('Move up', 'estavillo-portfolio-core'),
				size: 'small',
				disabled: o.index === 0,
				onClick: function () {
					o.onMove(o.index, o.index - 1);
				},
			}),
			el(Button, {
				key: 'down',
				icon: 'arrow-down-alt2',
				label: __('Move down', 'estavillo-portfolio-core'),
				size: 'small',
				disabled: o.index === o.count - 1,
				onClick: function () {
					o.onMove(o.index, o.index + 1);
				},
			}),
		];
		if (o.onDuplicate) {
			buttons.push(
				el(Button, {
					key: 'duplicate',
					icon: o.duplicateIcon || 'admin-page',
					label: __('Duplicate', 'estavillo-portfolio-core'),
					size: 'small',
					onClick: function () {
						o.onDuplicate(o.index);
					},
				})
			);
		}
		buttons.push(
			el(Button, {
				key: 'remove',
				icon: 'trash',
				label: __('Remove', 'estavillo-portfolio-core'),
				size: 'small',
				isDestructive: true,
				onClick: function () {
					o.onRemove(o.index);
				},
			})
		);
		return el('div', { className: 'es-caseb-itembar' }, buttons);
	}

	function addButton(label, onClick) {
		return el(
			Button,
			{
				className: 'es-caseb-add',
				variant: 'secondary',
				size: 'small',
				icon: 'plus',
				onClick: onClick,
			},
			label
		);
	}

	/**
	 * Props del wrapper del preview del bloque en el editor.
	 * es-case__body habilita los selectores del theme; es-caseb-scope los
	 * ajustes editor-only de case-blocks-editor.css (fondo de papel oscuro,
	 * padding, margin reset).
	 *
	 * @param {string} extra Clases extra opcionales.
	 */
	function scopeClass(extra) {
		return 'es-caseb-scope es-case__body' + (extra ? ' ' + extra : '');
	}

	window.esCaseUI = {
		el: el,
		listUpdate: listUpdate,
		listMove: listMove,
		listRemove: listRemove,
		listAdd: listAdd,
		itemBar: itemBar,
		addButton: addButton,
		scopeClass: scopeClass,
	};
})(window.wp);
