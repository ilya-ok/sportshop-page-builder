# История изменений

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
