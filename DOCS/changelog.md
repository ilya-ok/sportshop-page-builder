# История изменений

## [1.1.1] — 2026-06-23

### Добавлено
- Тип поля `wysiwyg` — полноценный TinyMCE-редактор в блоках конструктора
  - Поддержка шорткодов, HTML, вставка изображений через WP Media Uploader
  - Файлы: `assets/js/admin-builder.js` (функции `initWysiwygEditor`, `renderField`, `collectAndSync`)
  - Шаблон `templates/blocks/text.php`: `wpautop()` убран — TinyMCE сам оборачивает текст в `<p>` теги

### Исправлено
- **Критический баг: невалидный JSON после каждого сохранения**
  - Причина: WordPress Core в `update_metadata()` вызывает `wp_unslash()` (stripslashes) на значении перед записью в БД. Это стирало `\"` → `"` (ломало JSON-структуру) и `\n` → `n` (в тексте появлялась буква «n» вместо переноса строки)
  - Исправление: обёртка `wp_slash()` вокруг `wp_json_encode()` в `SPB_Admin::save()` и `spb_save_blocks()`. `wp_slash` удваивает backslash-ы; `wp_unslash` в WordPress убирает один уровень — в БД попадает корректный JSON
  - Файлы: `includes/class-spb-admin.php`, `sportshop-page-builder.php`
- **Шорткод с атрибутами в кавычках вызывал исчезновение всей строки конструктора**
  - Причина 1: TinyMCE-плагины `wpview`/`wptextpattern` преобразовывали шорткоды в визуальные объекты; `getContent()` возвращал пустую строку → строка сохранялась пустой
  - Причина 2: кавычки в `id="..."` атрибутах шорткода не экранировались в JSON → невалидный JSON → строка исчезала при следующей загрузке
  - Исправление: `wpview` и `wptextpattern` исключены из списка TinyMCE-плагинов; root-причина JSON-проблемы устранена `wp_slash`

---

## [1.1.0] — 2026-06-16

### Добавлено
- Блок `link_card` («Карточка ссылки») — карточка-ссылка: изображение сверху (16:9), надпись и стрелка в белом блоке снизу
  - Поля: `title`, `image` (медиатека), `image_url` (относительный путь, приоритет), `link_url`
  - Файлы: `includes/blocks/class-spb-block-link-card.php`, `templates/blocks/link-card.php`
  - CSS: `.spb-link-card` в `assets/css/frontend.css`
  - Hover: zoom изображения, оранжевый текст (без выхода за пределы карточки)

### Исправлено
- Анимация раскрытия блоков в конструкторе: заменена с jQuery `slideToggle` на CSS-переход `max-height`
  - Ранее: первый или второй клик вызывал двойное срабатывание из-за конфликта jQuery и CSS `display: none`
  - Теперь: класс `--open` — единственный источник состояния, CSS анимирует `max-height: 0 → 3000px`
  - Файлы: `assets/js/admin-builder.js`, `assets/css/admin-builder.css`

---

## [1.0.0] — 2026-05-14

### Добавлено
- Базовый Page Builder: строки → колонки → блоки
- Drag-and-drop (jQuery UI Sortable): строки по Y, колонки по X, блоки между колонками
- Блоки: `text`, `text_image`, `banner`, `calc_card`
- Синхронизация страниц между сайтами мультисайта (`MPS_Copy_Pages`)
- Шорткод `[spb_blocks]` и автовывод через `the_content`
