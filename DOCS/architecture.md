# Архитектура плагина

## Структура файлов

```
sportshop-page-builder/
├── sportshop-page-builder.php          # Константы, хелперы, подключение файлов
├── includes/
│   ├── class-spb-block-registry.php   # Реестр типов блоков, схема для JS
│   ├── class-spb-admin.php            # Метабокс, sanitize_rows(), enqueue_assets()
│   ├── class-spb-renderer.php         # Рендер строк → колонок → блоков, шорткод
│   └── blocks/
│       ├── class-spb-block-base.php   # Абстрактный базовый класс
│       ├── class-spb-block-text.php
│       ├── class-spb-block-text-image.php
│       └── class-spb-block-banner.php
├── templates/blocks/
│   ├── text.php
│   ├── text-image.php
│   └── banner.php
├── assets/
│   ├── js/admin-builder.js            # Весь UI builder (jQuery + jQuery UI Sortable)
│   └── css/admin-builder.css          # Стили admin + базовые frontend стили
└── DOCS/
    ├── architecture.md                # Этот файл
    ├── data.md                        # Структура данных, хелперы
    ├── admin.md                       # Метабокс, JS-архитектура, drag-and-drop
    ├── frontend.md                    # Рендер, CSS, шорткод, автовывод
    ├── blocks.md                      # Как добавить новый блок, типы полей
    └── multisite.md                   # Синхронизация между сайтами
```

## Константы

| Константа | Значение |
|-----------|----------|
| `SPB_VERSION` | `1.0.0` |
| `SPB_PLUGIN_FILE` | абсолютный путь к главному файлу |
| `SPB_PLUGIN_DIR` | папка плагина |
| `SPB_PLUGIN_URL` | URL папки плагина |
| `SPB_META_KEY` | `_spb_blocks` |

## Post types

По умолчанию метабокс показывается только для `page`.
Расширить через фильтр:
```php
add_filter('spb_post_types', fn($types) => array_merge($types, ['landing']));
```
