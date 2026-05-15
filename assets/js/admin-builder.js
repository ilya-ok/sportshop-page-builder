/* global spbConfig, wp */
(function ($) {
	'use strict';

	// ================================================================
	// Состояние
	// ================================================================

	// rows: Array<{ id, columns: Array<{ id, width, blocks: Array<{type,...}> }> }>
	var rows = [];

	var $builder, $rowsList, $jsonInput;
	var blockRegistry = {};   // type -> { label, fields }
	var uid = 0;

	function newId(prefix) {
		return prefix + '_' + (++uid) + '_' + Math.random().toString(36).slice(2, 6);
	}

	// ================================================================
	// Инициализация
	// ================================================================

	$(function () {
		if (typeof spbConfig === 'undefined') return;

		spbConfig.blocks.forEach(function (b) {
			blockRegistry[b.type] = b;
		});

		$builder   = $('#spb-builder');
		$rowsList  = $('#spb-rows');
		$jsonInput = $('#spb-json');

		try { rows = JSON.parse($jsonInput.val()) || []; }
		catch (e) { rows = []; }

		renderAll();
		bindEvents();
	});

	// ================================================================
	// Рендер
	// ================================================================

	function renderAll() {
		$rowsList.empty();
		rows.forEach(function (row, ri) {
			$rowsList.append(renderRow(row, ri));
		});
		initSortableRows();
		initSortableCols();
		initSortableBlocks();
	}

	// ---------- Строка ----------

	function renderRow(row, ri) {
		var $row = $('<div class="spb-row-wrap">')
			.attr('data-row-id', row.id);

		// Шапка строки
		var $head = $('<div class="spb-row-head">');
		$head.append('<span class="spb-drag spb-row-drag dashicons dashicons-move" title="Перетащить строку"></span>');
		$head.append('<span class="spb-row-head__label">Строка</span>');

		// Кнопка "Макет"
		var $layoutBtn = $('<button type="button" class="button spb-row-layout-btn">Макет</button>');
		$head.append($layoutBtn);
		$head.append(renderLayoutPicker());

		// Кнопка "+ Колонка"
		$head.append('<button type="button" class="button spb-add-col-btn">+ Колонка</button>');

		// Кнопка удаления
		$head.append('<button type="button" class="spb-row-del button-link-delete" title="Удалить строку"><span class="dashicons dashicons-trash"></span></button>');

		$row.append($head);

		// Колонки
		var $cols = $('<div class="spb-cols">');
		(row.columns || []).forEach(function (col) {
			$cols.append(renderColumn(col));
		});
		$row.append($cols);

		return $row;
	}

	function renderLayoutPicker() {
		var $picker = $('<div class="spb-layout-picker">');
		(spbConfig.layouts || []).forEach(function (layout) {
			var $btn = $('<button type="button" class="spb-layout-preset">')
				.attr('data-cols', JSON.stringify(layout.cols));

			// Иконка макета
			var $icon = $('<span class="spb-layout-icon">');
			layout.cols.forEach(function (w) {
				$icon.append($('<span class="spb-layout-icon__col">').css('flex', parseFloat(w)));
			});
			$btn.append($icon);
			$btn.append($('<span>').text(layout.label));
			$picker.append($btn);
		});
		return $picker;
	}

	// ---------- Колонка ----------

	function renderColumn(col) {
		var $col = $('<div class="spb-col-wrap">')
			.attr('data-col-id', col.id)
			.attr('data-width', col.width);

		// Шапка колонки
		var $head = $('<div class="spb-col-head">');
		$head.append('<span class="spb-drag spb-col-drag dashicons dashicons-leftright" title="Перетащить колонку"></span>');
		$head.append('<span class="spb-col-head__label">Колонка</span>');

		// Выбор ширины
		var $sel = $('<select class="spb-col-width">');
		Object.keys(spbConfig.widths).forEach(function (w) {
			var $opt = $('<option>').val(w).text(spbConfig.widths[w]);
			if (w === String(col.width)) $opt.prop('selected', true);
			$sel.append($opt);
		});
		$head.append($sel);

		$head.append('<button type="button" class="spb-col-del button-link-delete" title="Удалить колонку"><span class="dashicons dashicons-trash"></span></button>');
		$col.append($head);

		// Блоки
		var $blocks = $('<div class="spb-col-blocks">');
		(col.blocks || []).forEach(function (block) {
			$blocks.append(renderBlock(block));
		});
		$col.append($blocks);

		// Добавить блок
		var $addWrap = $('<div class="spb-col-footer">');
		var $addBtn  = $('<button type="button" class="button spb-add-block-btn">+ Добавить блок</button>');
		var $picker  = $('<div class="spb-block-picker">');
		Object.keys(blockRegistry).forEach(function (type) {
			$picker.append(
				$('<button type="button" class="spb-pick-block">').text(blockRegistry[type].label).attr('data-type', type)
			);
		});
		$addWrap.append($addBtn).append($picker);
		$col.append($addWrap);

		return $col;
	}

	// ---------- Блок ----------

	function renderBlock(block) {
		var schema = blockRegistry[block.type];
		if (!schema) return $();

		var $item = $('<div class="spb-block-item">')
			.attr('data-type', block.type);

		// Шапка блока
		var $head = $('<div class="spb-block-head">');
		$head.append('<span class="spb-drag spb-block-drag dashicons dashicons-move" title="Перетащить блок"></span>');
		$head.append('<span class="spb-block-head__label">' + escHtml(schema.label) + '</span>');
		$head.append('<span class="spb-block-toggle dashicons dashicons-arrow-down-alt2"></span>');
		$head.append('<button type="button" class="spb-block-del button-link-delete"><span class="dashicons dashicons-trash"></span></button>');
		$item.append($head);

		// Поля
		var $body = $('<div class="spb-block-body">');
		schema.fields.forEach(function (field) {
			$body.append(renderField(field, block[field.name] || ''));
		});
		$item.append($body);

		return $item;
	}

	function renderField(field, value) {
		var $row  = $('<div class="spb-field">');
		$row.append('<label class="spb-field__label">' + escHtml(field.label) + '</label>');

		var $ctrl;
		switch (field.type) {
			case 'textarea':
				$ctrl = $('<textarea class="spb-field__input large-text" rows="4">')
					.attr('data-field', field.name).val(value);
				break;

			case 'select':
				$ctrl = $('<select class="spb-field__input">').attr('data-field', field.name);
				Object.keys(field.options || {}).forEach(function (k) {
					$('<option>').val(k).text(field.options[k])
						.prop('selected', k === value)
						.appendTo($ctrl);
				});
				break;

			case 'image': {
				var slug = value || '';
				$ctrl = $('<div class="spb-img-wrap">');
				$ctrl.append($('<input type="hidden" class="spb-field__input">').attr('data-field', field.name).val(slug));
				$ctrl.append(
					$('<span class="spb-img-slug">').text(slug || spbConfig.strings.noImage)
						.toggleClass('spb-img-slug--empty', !slug)
				);
				$ctrl.append($('<button type="button" class="button spb-img-choose">').text(spbConfig.strings.chooseImage));
				if (slug) {
					$ctrl.append($('<button type="button" class="button spb-img-clear">').text('✕'));
				}
				break;
			}

			case 'url':
				$ctrl = $('<input type="url" class="spb-field__input large-text">')
					.attr('data-field', field.name).val(value);
				break;

			default:
				$ctrl = $('<input type="text" class="spb-field__input large-text">')
					.attr('data-field', field.name).val(value);
		}

		$row.append($ctrl);
		return $row;
	}

	// ================================================================
	// Drag-and-drop (jQuery UI Sortable)
	// ================================================================

	function initSortableRows() {
		$rowsList.sortable({
			handle:      '.spb-row-drag',
			axis:        'y',
			placeholder: 'spb-placeholder spb-placeholder--row',
			forcePlaceholderSize: true,
			stop: collectAndSync,
		});
	}

	function initSortableCols() {
		$rowsList.find('.spb-cols').each(function () {
			$(this).sortable({
				handle:       '.spb-col-drag',
				axis:         'x',
				placeholder:  'spb-placeholder spb-placeholder--col',
				forcePlaceholderSize: true,
				stop: collectAndSync,
			});
		});
	}

	function initSortableBlocks() {
		// connectWith позволяет перетаскивать блоки между колонками
		$rowsList.find('.spb-col-blocks').each(function () {
			$(this).sortable({
				handle:      '.spb-block-drag',
				placeholder: 'spb-placeholder spb-placeholder--block',
				forcePlaceholderSize: true,
				connectWith: '.spb-col-blocks',
				stop: collectAndSync,
			});
		});
	}

	// ================================================================
	// Сбор состояния из DOM
	// ================================================================

	function collectAndSync() {
		rows = [];
		$rowsList.find('.spb-row-wrap').each(function () {
			var row = {
				id:      $(this).attr('data-row-id') || newId('row'),
				columns: [],
			};
			$(this).find('> .spb-cols > .spb-col-wrap').each(function () {
				var col = {
					id:     $(this).attr('data-col-id') || newId('col'),
					width:  $(this).find('.spb-col-width').val() || '100',
					blocks: [],
				};
				$(this).find('> .spb-col-blocks > .spb-block-item').each(function () {
					var block = { type: $(this).attr('data-type') || '' };
					$(this).find('[data-field]').each(function () {
						block[$(this).data('field')] = $(this).val();
					});
					col.blocks.push(block);
				});
				row.columns.push(col);
			});
			rows.push(row);
		});
		syncJson();
	}

	function syncJson() {
		$jsonInput.val(JSON.stringify(rows));
	}

	// ================================================================
	// События
	// ================================================================

	function bindEvents() {

		// ---------- Строки ----------

		// Добавить строку
		$builder.on('click', '.spb-add-row', function () {
			rows.push(makeRow([ makeCol('100') ]));
			renderAll();
		});

		// Удалить строку
		$builder.on('click', '.spb-row-del', function (e) {
			e.stopPropagation();
			if (!confirm(spbConfig.strings.confirmRow)) return;
			var $row = $(this).closest('.spb-row-wrap');
			var id   = $row.attr('data-row-id');
			rows = rows.filter(function (r) { return r.id !== id; });
			renderAll();
		});

		// Показать/скрыть выбор макета
		$builder.on('click', '.spb-row-layout-btn', function (e) {
			e.stopPropagation();
			$(this).closest('.spb-row-head').find('.spb-layout-picker').toggleClass('is-open');
		});

		// Применить макет
		$builder.on('click', '.spb-layout-preset', function () {
			var widths  = JSON.parse($(this).attr('data-cols'));
			var $rowWrap = $(this).closest('.spb-row-wrap');
			var rowId   = $rowWrap.attr('data-row-id');
			var row     = findRow(rowId);
			if (!row) return;

			// Сохраняем блоки из существующих колонок
			var existingBlocks = [];
			(row.columns || []).forEach(function (c) {
				existingBlocks = existingBlocks.concat(c.blocks || []);
			});

			row.columns = widths.map(function (w, i) {
				return makeCol(w, existingBlocks.splice(0, i === widths.length - 1 ? existingBlocks.length : 0));
			});

			renderAll();
			$(this).closest('.spb-layout-picker').removeClass('is-open');
		});

		// Добавить колонку
		$builder.on('click', '.spb-add-col-btn', function () {
			var rowId = $(this).closest('.spb-row-wrap').attr('data-row-id');
			var row   = findRow(rowId);
			if (row) {
				row.columns.push(makeCol('50'));
				renderAll();
			}
		});

		// Удалить колонку
		$builder.on('click', '.spb-col-del', function (e) {
			e.stopPropagation();
			if (!confirm(spbConfig.strings.confirmColumn)) return;
			var $col  = $(this).closest('.spb-col-wrap');
			var colId = $col.attr('data-col-id');
			var row   = findRow($col.closest('.spb-row-wrap').attr('data-row-id'));
			if (!row) return;
			row.columns = row.columns.filter(function (c) { return c.id !== colId; });
			renderAll();
		});

		// Смена ширины колонки
		$builder.on('change', '.spb-col-width', function () {
			collectAndSync();
		});

		// ---------- Блоки ----------

		// Показать/скрыть пикер блоков
		$builder.on('click', '.spb-add-block-btn', function (e) {
			e.stopPropagation();
			$(this).closest('.spb-col-footer').find('.spb-block-picker').toggleClass('is-open');
		});

		// Добавить блок в колонку
		$builder.on('click', '.spb-pick-block', function () {
			var type  = $(this).attr('data-type');
			var $col  = $(this).closest('.spb-col-wrap');
			var colId = $col.attr('data-col-id');
			var row   = findRow($col.closest('.spb-row-wrap').attr('data-row-id'));
			if (!row) return;
			var col = row.columns.find(function (c) { return c.id === colId; });
			if (!col) return;

			var block = makeBlock(type);
			col.blocks.push(block);

			// Добавляем блок в DOM без полного ре-рендера (сохраняем состояние DnD)
			var $blockEl = renderBlock(block);
			$col.find('.spb-col-blocks').append($blockEl);
			$blockEl.find('.spb-block-body').hide();
			$blockEl.addClass('spb-block-item--open');
			$blockEl.find('.spb-block-body').slideDown(150);

			// Переподключаем sortable для новых элементов
			$col.find('.spb-col-blocks').sortable('refresh');
			syncJson();

			$(this).closest('.spb-block-picker').removeClass('is-open');
		});

		// Раскрыть/свернуть блок
		$builder.on('click', '.spb-block-head', function (e) {
			if ($(e.target).closest('.spb-block-del, .spb-block-drag').length) return;
			var $item = $(this).closest('.spb-block-item');
			$item.toggleClass('spb-block-item--open');
			$item.find('.spb-block-body').slideToggle(150);
		});

		// Удалить блок
		$builder.on('click', '.spb-block-del', function (e) {
			e.stopPropagation();
			if (!confirm(spbConfig.strings.confirmBlock)) return;
			$(this).closest('.spb-block-item').remove();
			collectAndSync();
		});

		// Изменение полей блока
		$builder.on('change input', '.spb-field__input', function () {
			collectAndSync();
		});

		// ---------- Медиафайлы ----------

		$builder.on('click', '.spb-img-choose', function () {
			var $wrap = $(this).closest('.spb-img-wrap');
			var frame = wp.media({
				title:    spbConfig.strings.selectImage,
				button:   { text: spbConfig.strings.chooseImage },
				multiple: false,
				library:  { type: 'image' },
			});
			frame.on('select', function () {
				var att  = frame.state().get('selection').first().toJSON();
				var slug = att.post_name || att.name || att.filename.replace(/\.[^.]+$/, '');

				$wrap.find('.spb-field__input').val(slug);
				$wrap.find('.spb-img-slug').text(slug).removeClass('spb-img-slug--empty');
				if (!$wrap.find('.spb-img-clear').length) {
					$wrap.append($('<button type="button" class="button spb-img-clear">').text('✕'));
				}
				collectAndSync();
			});
			frame.open();
		});

		$builder.on('click', '.spb-img-clear', function () {
			var $wrap = $(this).closest('.spb-img-wrap');
			$wrap.find('.spb-field__input').val('');
			$wrap.find('.spb-img-slug').text(spbConfig.strings.noImage).addClass('spb-img-slug--empty');
			$(this).remove();
			collectAndSync();
		});

		// ---------- Закрыть дропдауны при клике вне ----------

		$(document).on('click', function () {
			$('.spb-layout-picker.is-open').removeClass('is-open');
			$('.spb-block-picker.is-open').removeClass('is-open');
		});

		$builder.on('click', '.spb-layout-picker, .spb-block-picker', function (e) {
			e.stopPropagation();
		});
	}

	// ================================================================
	// Фабрики объектов
	// ================================================================

	function makeRow(columns) {
		return { id: newId('row'), columns: columns || [] };
	}

	function makeCol(width, blocks) {
		return { id: newId('col'), width: width || '100', blocks: blocks || [] };
	}

	function makeBlock(type) {
		var schema = blockRegistry[type];
		var block  = { type: type };
		if (schema) {
			schema.fields.forEach(function (f) {
				block[f.name] = f.type === 'select'
					? Object.keys(f.options || {})[0] || ''
					: '';
			});
		}
		return block;
	}

	// ================================================================
	// Поиск в состоянии
	// ================================================================

	function findRow(id) {
		return rows.find(function (r) { return r.id === id; }) || null;
	}

	// ================================================================
	// Утилиты
	// ================================================================

	function escHtml(str) {
		return $('<div>').text(String(str)).html();
	}

}(jQuery));
