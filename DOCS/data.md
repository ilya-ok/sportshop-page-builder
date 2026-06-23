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

**Всегда** оборачивать JSON в `wp_slash()` перед `update_post_meta()`:
```php
update_post_meta($post_id, SPB_META_KEY, wp_slash(wp_json_encode($data, JSON_UNESCAPED_UNICODE)));
```

**Почему `wp_slash` обязателен:** WordPress Core в `update_metadata()` вызывает `wp_unslash()` (stripslashes) на значении перед сохранением. Это стирает JSON-экранирование backslash-ами:
- `\"` (JSON-escape кавычки) → `"` — голая кавычка ломает JSON-структуру → `json_decode` фейлится → страница билдера пустая
- `\n` (JSON-escape переноса строки) → `n` — в тексте появляется буква «n» вместо переноса строки

`wp_slash()` (addslashes) перед передачей удваивает backslash-ы. `wp_unslash()` в WordPress убирает один уровень — в итоге корректный JSON попадает в `$wpdb` и хранится в БД.

`get_post_meta()` возвращает значение из БД **без** `wp_unslash` — JSON читается как есть, `json_decode()` работает корректно.

**`JSON_UNESCAPED_UNICODE` обязателен:** без него кириллица кодируется как `\uXXXX`. `wp_unslash()` в цепочке стирает backslash → `u041a` вместо буквы на фронте. С флагом кириллица хранится напрямую и проблема исключена.
