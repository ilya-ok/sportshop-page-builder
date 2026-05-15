# Sportshop Page Builder — индекс документации

**Версия:** 1.0.0 | **Обновлено:** 2026-05-14

WordPress page builder без зависимостей. Структура: **строки → колонки → блоки**.

## Документация (читай по задаче)

| Файл | Когда читать |
|------|-------------|
| [DOCS/architecture.md](DOCS/architecture.md) | Структура файлов, константы, post types |
| [DOCS/data.md](DOCS/data.md) | Формат JSON, хелперы, хранение кириллицы |
| [DOCS/admin.md](DOCS/admin.md) | Метабокс, JS-архитектура, drag-and-drop, пресеты макетов |
| [DOCS/frontend.md](DOCS/frontend.md) | Рендер, CSS, шорткод, автовывод через the_content |
| [DOCS/blocks.md](DOCS/blocks.md) | Типы блоков, типы полей, добавление нового блока |
| [DOCS/multisite.md](DOCS/multisite.md) | Синхронизация страниц между сайтами мультисайта |

## Ключевые факты (всегда актуально)

- **Meta key** — `_spb_blocks`. Содержит JSON: массив строк → колонки → блоки.
- **Изображения** — хранится `post_name` (slug) вложения, **не ID**.
- **JSON** — сохранять через `wp_json_encode($data, JSON_UNESCAPED_UNICODE)`, иначе кириллица ломается.
- **Автовывод** — хук `the_content` в `SPB_Renderer`, никаких правок темы не нужно.
- **Новый блок** — класс + шаблон + одна строка в `register_defaults()`, JS подхватывает автоматически.

## Файлы, которые меняются чаще всего

```
includes/class-spb-admin.php            ← метабокс, сохранение, санитизация
includes/class-spb-renderer.php         ← фронтенд рендер
assets/js/admin-builder.js              ← весь UI builder
assets/css/admin-builder.css            ← стили админки
assets/css/frontend.css                ← стили фронтенда (сетка + блоки)
includes/blocks/class-spb-block-*.php  ← добавление нового блока
templates/blocks/*.php                  ← вёрстка блока
```
