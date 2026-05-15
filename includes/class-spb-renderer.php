<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Рендер строк → колонок → блоков на фронтенде.
 */
class SPB_Renderer {

	private static ?SPB_Renderer $instance = null;

	private function __construct() {
		add_shortcode( 'spb_blocks', array( $this, 'shortcode' ) );
		add_filter( 'the_content', array( $this, 'append_to_content' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets(): void {
		wp_enqueue_style(
			'spb-frontend',
			SPB_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			SPB_VERSION
		);
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Автоматически добавляет блоки после стандартного контента поста.
	 * Работает только на страницах post type из фильтра spb_post_types.
	 * Если шорткод [spb_blocks] уже есть в контенте — не дублирует вывод.
	 */
	public function append_to_content( string $content ): string {
		if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$post_types = apply_filters( 'spb_post_types', array( 'page' ) );
		if ( ! in_array( get_post_type(), $post_types, true ) ) {
			return $content;
		}

		// Не дублировать, если шорткод уже используется вручную
		if ( has_shortcode( get_post()->post_content, 'spb_blocks' ) ) {
			return $content;
		}

		$rows = spb_get_blocks( get_the_ID() );
		if ( empty( $rows ) ) {
			return $content;
		}

		ob_start();
		$this->render( get_the_ID() );
		return $content . ob_get_clean();
	}

	/**
	 * Шорткод [spb_blocks] или [spb_blocks id="42"]
	 */
	public function shortcode( array $atts ): string {
		$atts    = shortcode_atts( array( 'id' => get_the_ID() ), $atts );
		ob_start();
		$this->render( absint( $atts['id'] ) );
		return ob_get_clean();
	}

	/**
	 * Вывести page builder для указанного поста.
	 */
	public function render( int $post_id ): void {
		$rows     = spb_get_blocks( $post_id );
		$registry = SPB_Block_Registry::get_instance();

		if ( empty( $rows ) ) {
			return;
		}

		echo '<div class="spb-layout">';
		foreach ( $rows as $row ) {
			$this->render_row( $row, $registry );
		}
		echo '</div>';
	}

	private function render_row( array $row, SPB_Block_Registry $registry ): void {
		$columns = $row['columns'] ?? array();
		if ( empty( $columns ) ) {
			return;
		}
		echo '<div class="spb-row">';
		foreach ( $columns as $col ) {
			$this->render_column( $col, $registry );
		}
		echo '</div>';
	}

	private function render_column( array $col, SPB_Block_Registry $registry ): void {
		$width  = $col['width'] ?? '100';
		$blocks = $col['blocks'] ?? array();

		echo '<div class="spb-col spb-col--' . esc_attr( $width ) . '">';
		foreach ( $blocks as $block ) {
			$type      = $block['type'] ?? '';
			$block_obj = $registry->get( $type );
			if ( ! $block_obj ) {
				continue;
			}
			echo '<div class="spb-block spb-block--' . esc_attr( str_replace( '_', '-', $type ) ) . '">';
			$block_obj->render( $block );
			echo '</div>';
		}
		echo '</div>';
	}
}
