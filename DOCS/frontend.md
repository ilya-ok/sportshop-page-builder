# Фронтенд: рендер, CSS, шорткод

## Автовывод через the_content

Класс: `SPB_Renderer` (`includes/class-spb-renderer.php`)

Плагин хукается в `the_content`. Блоки добавляются **перед** стандартным контентом страницы автоматически — никаких правок темы не нужно.

Условия срабатывания:
- `is_singular()` — одиночная страница
- `in_the_loop()` — внутри основного цикла
- `is_main_query()` — главный запрос (не виджет, не сайдбар)
- В контенте нет шорткода `[spb_blocks]` (не дублирует вывод)
- Есть сохранённые блоки

## Шорткод

```
[spb_blocks]           ← блоки текущей страницы
[spb_blocks id="42"]   ← блоки указанного поста
```

## Прямой вызов из PHP

```php
SPB_Renderer::get_instance()->render(get_the_ID());

// или через хелпер (только данные, без рендера):
$rows = spb_get_blocks(get_the_ID());
```

## HTML-структура вывода

```html
<div class="spb-layout">
  <div class="spb-row">
    <div class="spb-col spb-col--50">
      <div class="spb-block spb-block--text">
        <!-- template: templates/blocks/text.php -->
      </div>
    </div>
    <div class="spb-col spb-col--50">
      <div class="spb-block spb-block--banner">
        <!-- template: templates/blocks/banner.php -->
      </div>
    </div>
  </div>
</div>
```

## CSS

Фронтенд-стили в `assets/css/frontend.css`, подключаются через `wp_enqueue_scripts` в `SPB_Renderer`.
Тема может переопределять классы в своём CSS.

Сетка колонок:
```css
.spb-row { display: flex; flex-wrap: wrap; margin: 0 -15px; }
.spb-col { box-sizing: border-box; padding: 0 15px; }
/* При ≤768px все колонки → 100% */
```

## Шаблоны блоков

Файлы в `templates/blocks/`. Переменная `$block` содержит данные блока (array).

| Тип | Шаблон | Переменные |
|-----|--------|------------|
| `text` | `text.php` | `title`, `text` |
| `text_image` | `text-image.php` | `title`, `text`, `image`, `image_position`, `link_url`, `link_label` |
| `banner` | `banner.php` | `title`, `subtitle`, `bg_image`, `link_url`, `link_label` |
| `calc_card` | `calc-card.php` | `title`, `subtitle`, `tag`, `bg_image`, `bg_image_url`, `link_url` |

Изображение в шаблоне:
```php
$url = spb_get_image_url($block['image'] ?? '');
// Ищет attachment по post_name, возвращает URL
```
