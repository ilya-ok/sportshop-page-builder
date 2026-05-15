<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Абстрактный базовый класс блока.
 * Каждый тип блока расширяет этот класс.
 */
abstract class SPB_Block_Base {

	/**
	 * Уникальный идентификатор типа блока (snake_case).
	 */
	abstract public function get_type(): string;

	/**
	 * Название блока для отображения в админке.
	 */
	abstract public function get_label(): string;

	/**
	 * Схема полей блока.
	 * Используется JS для генерации формы в метабоксе.
	 *
	 * Типы полей: text, textarea, image, select, url, checkbox
	 *
	 * @return array[]
	 */
	abstract public function get_fields(): array;

	/**
	 * Имя PHP-шаблона без расширения (из папки templates/blocks/).
	 */
	public function get_template(): string {
		return str_replace( '_', '-', $this->get_type() );
	}

	/**
	 * Отрендерить блок.
	 *
	 * @param array $block Данные блока из JSON.
	 */
	public function render( array $block ): void {
		$template = SPB_PLUGIN_DIR . 'templates/blocks/' . $this->get_template() . '.php';
		if ( file_exists( $template ) ) {
			include $template;
		}
	}
}
