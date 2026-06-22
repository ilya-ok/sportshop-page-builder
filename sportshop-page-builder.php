<?php
/**
 * Plugin Name: Sportshop Page Builder
 * Description: Простой page builder для создания страниц из блоков. Поддерживает синхронизацию между сайтами мультисайта.
 * Version: 1.0.0
 * Author: SportShop
 * Text Domain: spb
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

define( 'SPB_VERSION', '1.0.0' );
define( 'SPB_PLUGIN_FILE', __FILE__ );
define( 'SPB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SPB_META_KEY', '_spb_blocks' );

require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-base.php';
require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-text.php';
require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-text-image.php';
require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-banner.php';
require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-calc-card.php';
require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-link-card.php';
require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-link-banner.php';
require_once SPB_PLUGIN_DIR . 'includes/blocks/class-spb-block-calc-banner.php';
require_once SPB_PLUGIN_DIR . 'includes/class-spb-block-registry.php';
require_once SPB_PLUGIN_DIR . 'includes/class-spb-admin.php';
require_once SPB_PLUGIN_DIR . 'includes/class-spb-renderer.php';

function spb_init() {
	SPB_Block_Registry::get_instance();
	SPB_Renderer::get_instance();

	if ( is_admin() ) {
		SPB_Admin::get_instance();
	}
}
add_action( 'plugins_loaded', 'spb_init' );

/**
 * Получить блоки страницы.
 *
 * @param int $post_id
 * @return array
 */
function spb_get_blocks( int $post_id ): array {
	$raw = get_post_meta( $post_id, SPB_META_KEY, true );
	if ( ! $raw ) {
		return array();
	}
	// json_decode корректно обрабатывает \uXXXX-последовательности,
	// но если данные были сохранены с двойным слешем (\\uXXXX),
	// дополнительно пробуем stripslashes перед декодированием.
	$blocks = json_decode( $raw, true );
	if ( ! is_array( $blocks ) ) {
		$blocks = json_decode( stripslashes( $raw ), true );
	}
	return is_array( $blocks ) ? $blocks : array();
}

/**
 * Сохранить блоки страницы.
 *
 * @param int   $post_id
 * @param array $blocks
 */
function spb_save_blocks( int $post_id, array $blocks ): void {
	update_post_meta( $post_id, SPB_META_KEY, wp_json_encode( $blocks, JSON_UNESCAPED_UNICODE ) );
}

/**
 * Получить URL изображения по slug вложения.
 * Хранение по slug (post_name) позволяет синхронизировать между сайтами.
 *
 * @param string $slug
 * @param string $size
 * @return string
 */
function spb_get_image_url( string $slug, string $size = 'full' ): string {
	if ( ! $slug ) {
		return '';
	}
	$posts = get_posts( array(
		'post_type'      => 'attachment',
		'name'           => $slug,
		'posts_per_page' => 1,
		'post_status'    => 'inherit',
	) );
	if ( empty( $posts ) ) {
		return '';
	}
	return wp_get_attachment_image_url( $posts[0]->ID, $size ) ?: '';
}
