# Админка: метабокс и JS-архитектура

## Метабокс

Класс: `SPB_Admin` (`includes/class-spb-admin.php`)

Метабокс «Page Builder» появляется при редактировании `page` (и других post type через фильтр `spb_post_types`).

**HTML-структура метабокса:**
```
#spb-builder
├── #spb-rows          ← список строк (рендерит JS)
├── .spb-builder__footer
│   └── .spb-add-row   ← кнопка «+ Добавить строку»
└── #spb-json          ← скрытый input с JSON (name="spb_blocks")
```

**Сохранение:** `SPB_Admin::save()` → `sanitize_rows()` → `update_post_meta()`.
Вложенная санитизация: строки → колонки → блоки. Каждое поле блока проходит через `wp_kses_post()`.

## JS-архитектура (admin-builder.js)

### Состояние
```js
var rows = [];
// rows: Array<{ id, columns: Array<{ id, width, blocks: Array<{type,...}> }> }>
```
Состояние живёт в памяти JS и синхронизируется в скрытое поле `#spb-json` через `syncJson()`.

### Рендер
- **Полный ре-рендер** (`renderAll()`) — при добавлении/удалении строк и колонок
- **Без ре-рендера** (`collectAndSync()`) — после drag-and-drop, смены ширины, изменения поля

`collectAndSync()` обходит DOM → пересобирает `rows[]` → вызывает `syncJson()`.

### Drag-and-drop (jQuery UI Sortable)

| Уровень | Контейнер | Handle |
|---------|-----------|--------|
| Строки | `#spb-rows` | `.spb-row-drag` |
| Колонки | `.spb-cols` | `.spb-col-drag`, axis: `x` |
| Блоки | `.spb-col-blocks` | `.spb-block-drag`, `connectWith: '.spb-col-blocks'` |

`connectWith` позволяет перетаскивать блоки **между колонками**.

### Пресеты макетов строки

Кнопка «Макет» в шапке строки открывает визуальный пикер с 8 вариантами:
`100` / `50/50` / `33/66` / `66/33` / `33/33/33` / `25/75` / `75/25` / `25/25/25/25`.

При применении пресета блоки из существующих колонок перемещаются в первую колонку нового макета.

### Медиафайлы

Поле `image` открывает стандартный WordPress Media Uploader (`wp.media()`).
Сохраняется `attachment.post_name` (slug файла), не ID.

## Конфигурация (передаётся в JS через wp_localize_script)

```js
spbConfig.blocks   // схема всех блоков: [{ type, label, fields }]
spbConfig.widths   // { "25": "25%", "33": "33%", ... }
spbConfig.layouts  // пресеты макетов: [{ label, cols: ["50","50"] }, ...]
spbConfig.strings  // строки интерфейса
```
