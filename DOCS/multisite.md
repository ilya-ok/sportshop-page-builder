# Синхронизация мультисайта

## Принцип

Весь контент страницы хранится в одном `postmeta` поле `_spb_blocks` как JSON.
Изображения идентифицируются по `post_name` (slug), а не по ID — ID одного и того же файла разный на каждом сайте мультисайта, slug — одинаковый.

Чтобы скопировать страницу на другой сайт, достаточно:
1. Скопировать сам пост (`wp_insert_post` / `wp_update_post`)
2. Скопировать postmeta `_spb_blocks` — JSON переносится без изменений
3. Убедиться, что медиафайлы загружены на целевом сайте с теми же slugs

## Пример копирования страницы

```php
function spb_copy_page_to_site(int $source_post_id, int $target_site_id): void {
    $source     = get_post($source_post_id);
    $blocks_json = get_post_meta($source_post_id, '_spb_blocks', true);

    switch_to_blog($target_site_id);

    $existing = get_page_by_path($source->post_name);
    $data = [
        'post_title'  => $source->post_title,
        'post_name'   => $source->post_name,
        'post_status' => 'publish',
        'post_type'   => 'page',
    ];

    $target_id = $existing
        ? wp_update_post(array_merge(['ID' => $existing->ID], $data))
        : wp_insert_post($data);

    update_post_meta($target_id, '_spb_blocks', $blocks_json);

    restore_current_blog();
}
```

## Медиафайлы

Если на целевом сайте нет файла с нужным slug — `spb_get_image_url()` вернёт пустую строку.

Варианты решения:
- Загрузить файлы вручную на каждый сайт с теми же именами
- Использовать плагин **Network Media Library** (общая медиатека для всего мультисайта)
- При копировании страницы автоматически делать `media_sideload_image()` — аналогично тому, как это сделано в `multisite-sync/includes/class-mps-copy.php`

## Интеграция с multisite-sync

Класс `MPS_Copy_Pages` реализован в плагине `multisite-sync` (`includes/class-mps-copy-pages.php`).

**Расположение:** Network Admin → Синхронизация Multisite → Копирование страниц

**Как работает:**
1. Загружает все страницы главного сайта, показывает статус на каждом дочернем сайте (✓ / —)
2. Кнопка «Копировать» → `wp_insert_post` / `wp_update_post` + `update_post_meta('_spb_blocks', ...)`
3. JSON `_spb_blocks` переносится без изменений — slug изображений разрешаются на каждом сайте независимо через `spb_get_image_url()`
