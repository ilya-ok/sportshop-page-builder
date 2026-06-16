# Блоки: типы, поля, добавление нового

## Существующие блоки

| Тип | Класс | Шаблон | Поля |
|-----|-------|--------|------|
| `text` | `SPB_Block_Text` | `templates/blocks/text.php` | title, text |
| `text_image` | `SPB_Block_Text_Image` | `templates/blocks/text-image.php` | title, text, image, image_position, link_url, link_label |
| `banner` | `SPB_Block_Banner` | `templates/blocks/banner.php` | title, subtitle, bg_image, link_url, link_label |
| `calc_card` | `SPB_Block_Calc_Card` | `templates/blocks/calc-card.php` | title, subtitle, tag, bg_image, bg_image_url, link_url |
| `link_card` | `SPB_Block_Link_Card` | `templates/blocks/link-card.php` | title, image, image_url, link_url |

**Поля блока `link_card`:**
- `title` — надпись внизу карточки
- `image` — изображение из медиатеки (хранит slug вложения)
- `image_url` — относительный путь к файлу (приоритет над `image`): `/wp-content/...`
- `link_url` — ссылка на страницу (относительная или полная)

Рендер: карточка-ссылка целиком, изображение сверху (16:9, zoom при hover), надпись и стрелка в белом блоке снизу. CSS-переход `max-height` в admin-builder.css.

---

**Поля блока `calc_card` — фоновое изображение:**
- `bg_image` — изображение из медиатеки (хранит slug вложения)
- `bg_image_url` — относительный путь к файлу (приоритет над `bg_image`): `/wp-content/...`. Удобно для мультисайта — путь одинаков на всех сайтах, JSON копируется без изменений.

**Рендер `bg_image_url` в шаблоне:** применяется `home_url($bg_image_url)` — необходимо когда WordPress установлен в подпапке (например `/sportshop/`). Без этого браузер резолвит путь от корня домена.

**Рендер `link_url` в шаблоне:** хранится как относительный путь (`/slug/`). При рендере применяется `home_url($link_url)` — по той же причине. Полные URL (`https://...`) остаются без изменений.

## Типы полей (get_fields)

| type | Элемент в админке | Примечания |
|------|-------------------|------------|
| `text` | `<input type="text">` | |
| `textarea` | `<textarea>` | |
| `url` | `<input type="url">` | |
| `select` | `<select>` | требует ключ `options: { value: label }` |
| `image` | кнопка Media Uploader | хранит `post_name` (slug) файла |

## Добавить новый блок — чеклист

**1. Создать класс** `includes/blocks/class-spb-block-{type}.php`:
```php
class SPB_Block_Cards extends SPB_Block_Base {

    public function get_type(): string  { return 'cards'; }
    public function get_label(): string { return 'Карточки'; }

    public function get_fields(): array {
        return [
            ['name' => 'title',  'type' => 'text',     'label' => 'Заголовок'],
            ['name' => 'image',  'type' => 'image',    'label' => 'Изображение'],
            ['name' => 'layout', 'type' => 'select',   'label' => 'Макет',
             'options' => ['2col' => '2 колонки', '3col' => '3 колонки']],
        ];
    }
}
```

**2. Подключить** в `sportshop-page-builder.php`:
```php
require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-cards.php';
```

**3. Зарегистрировать** в `SPB_Block_Registry::register_defaults()`:
```php
$this->register(new SPB_Block_Cards());
```

**4. Создать шаблон** `templates/blocks/cards.php`:
```php
<?php if (!defined('ABSPATH')) die('Forbidden'); ?>
<div class="spb-cards spb-cards--<?= esc_attr($block['layout'] ?? '3col') ?>">
    <h2><?= esc_html($block['title'] ?? '') ?></h2>
</div>
```

Имя шаблона = `str_replace('_', '-', $type) . '.php'`.
Для `text_image` → `text-image.php`.

## Реестр блоков

`SPB_Block_Registry` (Singleton) хранит все зарегистрированные блоки и отдаёт схему в JS:

```php
$registry = SPB_Block_Registry::get_instance();
$block_obj = $registry->get('text_image'); // → SPB_Block_Text_Image
$schema    = $registry->get_js_schema();   // → array для wp_localize_script
```

JS строит форму редактирования блока динамически из схемы `spbConfig.blocks` — никаких дополнительных JS-правок при добавлении нового блока не нужно.
