<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Метабокс Page Builder в редакторе WordPress.
 *
 * Структура данных (JSON в postmeta _spb_blocks):
 * [
 *   {
 *     "id": "row_abc",
 *     "columns": [
 *       {
 *         "id": "col_xyz",
 *         "width": "50",          // процент: 25|33|50|66|75|100
 *         "blocks": [
 *           { "type": "text", "title": "...", "text": "..." }
 *         ]
 *       }
 *     ]
 *   }
 * ]
 */
class SPB_Admin {

	private static ?SPB_Admin $instance = null;

	/** Допустимые значения ширины колонки (проценты). */
	private const VALID_WIDTHS = array( '25', '33', '50', '66', '75', '100' );

	private function __construct() {
		add_action( 'add_meta_boxes',        array( $this, 'register_metabox' ) );
		add_action( 'save_post',             array( $this, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function register_metabox(): void {
		$post_types = apply_filters( 'spb_post_types', array( 'page' ) );
		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'spb_blocks',
				'Page Builder',
				array( $this, 'render_metabox' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	public function enqueue_assets( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$post_types = apply_filters( 'spb_post_types', array( 'page' ) );
		if ( ! in_array( get_post_type(), $post_types, true ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_editor();

		wp_enqueue_style(
			'spb-admin',
			SPB_PLUGIN_URL . 'assets/css/admin-builder.css',
			array(),
			SPB_VERSION
		);

		wp_enqueue_script(
			'spb-admin',
			SPB_PLUGIN_URL . 'assets/js/admin-builder.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			SPB_VERSION,
			true
		);

		wp_localize_script( 'spb-admin', 'spbConfig', array(
			'blocks'  => SPB_Block_Registry::get_instance()->get_js_schema(),
			'widths'  => array(
				'25'  => '25%',
				'33'  => '33%',
				'50'  => '50%',
				'66'  => '66%',
				'75'  => '75%',
				'100' => '100%',
			),
			'layouts' => array(
				array( 'label' => '100',       'cols' => array( '100' ) ),
				array( 'label' => '50 / 50',   'cols' => array( '50', '50' ) ),
				array( 'label' => '33 / 66',   'cols' => array( '33', '66' ) ),
				array( 'label' => '66 / 33',   'cols' => array( '66', '33' ) ),
				array( 'label' => '33 / 33 / 33', 'cols' => array( '33', '33', '33' ) ),
				array( 'label' => '25 / 75',   'cols' => array( '25', '75' ) ),
				array( 'label' => '75 / 25',   'cols' => array( '75', '25' ) ),
				array( 'label' => '25 / 25 / 25 / 25', 'cols' => array( '25', '25', '25', '25' ) ),
			),
			'strings' => array(
				'addRow'        => '+ Добавить строку',
				'addColumn'     => '+ Колонка',
				'addBlock'      => '+ Добавить блок',
				'layout'        => 'Макет',
				'chooseLayout'  => 'Выбрать макет',
				'deleteRow'     => 'Удалить строку',
				'deleteColumn'  => 'Удалить колонку',
				'deleteBlock'   => 'Удалить блок',
				'confirmRow'    => 'Удалить строку со всем содержимым?',
				'confirmColumn' => 'Удалить колонку со всем содержимым?',
				'confirmBlock'  => 'Удалить этот блок?',
				'chooseImage'   => 'Выбрать',
				'selectImage'   => 'Выбрать изображение',
				'noImage'       => 'Не выбрано',
				'width'         => 'Ширина',
				'row'           => 'Строка',
				'column'        => 'Колонка',
			),
		) );
	}

	public function render_metabox( WP_Post $post ): void {
		$json = get_post_meta( $post->ID, SPB_META_KEY, true ) ?: '[]';
		?>
		<div class="spb-builder" id="spb-builder">
			<div class="spb-rows" id="spb-rows">
				<!-- Строки рендерит JS -->
			</div>
			<div class="spb-builder__footer">
				<button type="button" class="button button-primary spb-add-row">
					+ Добавить строку
				</button>
			</div>
			<input type="hidden" id="spb-json" name="spb_blocks"
				value="<?php echo esc_attr( $json ); ?>">
			<?php wp_nonce_field( 'spb_save', 'spb_nonce' ); ?>
		</div>
		<?php
	}

	public function save( int $post_id, WP_Post $post ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['spb_nonce'] ) || ! wp_verify_nonce( $_POST['spb_nonce'], 'spb_save' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$post_types = apply_filters( 'spb_post_types', array( 'page' ) );
		if ( ! in_array( $post->post_type, $post_types, true ) ) {
			return;
		}
		if ( ! isset( $_POST['spb_blocks'] ) ) {
			return;
		}

		$raw  = wp_unslash( $_POST['spb_blocks'] );
		$rows = json_decode( $raw, true );

		if ( ! is_array( $rows ) ) {
			return;
		}

		update_post_meta( $post_id, SPB_META_KEY, wp_slash( wp_json_encode( $this->sanitize_rows( $rows ), JSON_UNESCAPED_UNICODE ) ) );
	}

	private function sanitize_rows( array $rows ): array {
		$registry = SPB_Block_Registry::get_instance();
		$clean    = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$clean_row = array(
				'id'      => sanitize_key( $row['id'] ?? '' ),
				'columns' => array(),
			);

			foreach ( (array) ( $row['columns'] ?? array() ) as $col ) {
				if ( ! is_array( $col ) ) {
					continue;
				}
				$width     = in_array( (string) ( $col['width'] ?? '' ), self::VALID_WIDTHS, true )
					? (string) $col['width']
					: '100';
				$clean_col = array(
					'id'     => sanitize_key( $col['id'] ?? '' ),
					'width'  => $width,
					'blocks' => array(),
				);

				foreach ( (array) ( $col['blocks'] ?? array() ) as $block ) {
					$type = sanitize_key( $block['type'] ?? '' );
					if ( ! $registry->get( $type ) ) {
						continue;
					}
					$clean_block = array( 'type' => $type );
					foreach ( $block as $key => $value ) {
						if ( 'type' === $key ) {
							continue;
						}
						$clean_block[ sanitize_key( $key ) ] = is_string( $value )
							? wp_kses_post( $value )
							: $value;
					}
					$clean_col['blocks'][] = $clean_block;
				}

				$clean_row['columns'][] = $clean_col;
			}

			$clean[] = $clean_row;
		}

		return $clean;
	}
}
