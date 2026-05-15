# Структура данных

## Формат JSON (`_spb_blocks`)

Данные хранятся в `postmeta` по ключу `_spb_blocks` как JSON-строка.
Структура: **массив строк → колонки → блоки**.

```json
[
  {
    "id": "row_abc",
    "columns": [
      {
        "id": "col_xyz",
        "width": "50",
        "blocks": [
          { "type": "text", "title": "Заголовок", "text": "<p>Текст</p>" }
        ]
      },
      {
        "id": "col_def",
        "width": "50",
        "blocks": [
          { "type": "banner", "title": "Акция", "bg_image": "banner-may", "link_url": "/catalog/" }
        ]
      }
    ]
  }
]
```

### Ширина колонки (`width`)
Допустимые значения: `"25"` | `"33"` | `"50"` | `"66"` | `"75"` | `"100"` (проценты).

### Изображения
Хранится **`post_name`** (slug) вложения, **не ID**.
Это позволяет синхронизировать страницы между сайтами мультисайта без перебиндинга ID.

## Хелперы

```php
// Получить строки страницы
spb_get_blocks(int $post_id): array

// Сохранить строки
spb_save_blocks(int $post_id, array $rows): void

// Получить URL изображения по slug вложения
spb_get_image_url(string $slug, string $size = 'full'): string
```

Пример `spb_get_image_url`:
```php
$url = spb_get_image_url('my-photo', 'large');
// → ищет attachment с post_name = 'my-photo', возвращает URL нужного размера
```

## Важные ограничения при сохранении

**Всегда** использовать `JSON_UNESCAPED_UNICODE` при `wp_json_encode()`:
```php
wp_json_encode($data, JSON_UNESCAPED_UNICODE)
```

**Почему:** без этого флага кириллица кодируется как `К...`.
Затем WordPress magic quotes добавляет слеш: `\\u041a`.
`wp_unslash` убирает лишний слеш: `К` → корректно.
Но в некоторых конфигурациях слеш обрезается лишний раз → на фронте появляется `u041a...` вместо текста.
С `JSON_UNESCAPED_UNICODE` кириллица хранится напрямую и проблема исключена.

`spb_get_blocks()` содержит fallback-декодирование через `stripslashes()` для совместимости со старыми данными.
