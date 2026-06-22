<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Реестр типов блоков.
 * Хранит все зарегистрированные блоки, отдаёт схему полей в JS.
 */
class SPB_Block_Registry {

	private static ?SPB_Block_Registry $instance = null;

	/** @var SPB_Block_Base[] */
	private array $blocks = array();

	private function __construct() {
		$this->register_defaults();
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Зарегистрировать тип блока.
	 */
	public function register( SPB_Block_Base $block ): void {
		$this->blocks[ $block->get_type() ] = $block;
	}

	/**
	 * Получить блок по типу.
	 */
	public function get( string $type ): ?SPB_Block_Base {
		return $this->blocks[ $type ] ?? null;
	}

	/**
	 * Все зарегистрированные блоки.
	 *
	 * @return SPB_Block_Base[]
	 */
	public function all(): array {
		return $this->blocks;
	}

	/**
	 * Схема всех блоков для передачи в JS.
	 */
	public function get_js_schema(): array {
		$schema = array();
		foreach ( $this->blocks as $block ) {
			$schema[] = array(
				'type'   => $block->get_type(),
				'label'  => $block->get_label(),
				'fields' => $block->get_fields(),
			);
		}
		return $schema;
	}

	private function register_defaults(): void {
		$this->register( new SPB_Block_Text() );
		$this->register( new SPB_Block_Text_Image() );
		$this->register( new SPB_Block_Banner() );
		$this->register( new SPB_Block_Calc_Card() );
		$this->register( new SPB_Block_Link_Card() );
		$this->register( new SPB_Block_Link_Banner() );
		$this->register( new SPB_Block_Calc_Banner() );
	}
}
