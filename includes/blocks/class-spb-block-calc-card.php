<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

/**
 * Блок «Карточка калькулятора» — весь блок-ссылка с фоном и белым текстом.
 * Ссылка относительная (начинается с /), работает на всех сайтах мультисети.
 */
class SPB_Block_Calc_Card extends SPB_Block_Base {

	public function get_type(): string  { return 'calc_card'; }
	public function get_label(): string { return 'Карточка калькулятора'; }

	public function get_fields(): array {
		return array(
			array( 'name' => 'title',    'type' => 'html',     'label' => 'Заголовок' ),
			array( 'name' => 'subtitle', 'type' => 'html',     'label' => 'Подзаголовок' ),
			array( 'name' => 'tag',      'type' => 'html',     'label' => 'Метка (напр. «Бесплатный расчёт»)' ),
			array( 'name' => 'bg_image',     'type' => 'image', 'label' => 'Фоновое изображение (из медиатеки)' ),
			array( 'name' => 'bg_image_url', 'type' => 'text',  'label' => 'Фоновое изображение — относительный путь (приоритет над медиатекой): /wp-content/...' ),
			array( 'name' => 'link_url',     'type' => 'text',  'label' => 'Ссылка (относительная: /kalkulyator/)' ),
		);
	}
}
