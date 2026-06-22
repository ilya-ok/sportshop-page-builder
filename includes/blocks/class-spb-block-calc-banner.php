<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class SPB_Block_Calc_Banner extends SPB_Block_Base {

	public function get_type(): string  { return 'calc_banner'; }
	public function get_label(): string { return 'Баннер калькулятора'; }

	public function get_fields(): array {
		return array(
			array( 'name' => 'title',        'type' => 'html',  'label' => 'Заголовок' ),
			array( 'name' => 'subtitle',     'type' => 'html',  'label' => 'Подзаголовок' ),
			array( 'name' => 'tag',          'type' => 'html',  'label' => 'Метка (напр. «Бесплатный расчёт»)' ),
			array( 'name' => 'bg_image',     'type' => 'image', 'label' => 'Фоновое изображение (из медиатеки)' ),
			array( 'name' => 'bg_image_url', 'type' => 'text',  'label' => 'Фоновое изображение — относительный путь (приоритет над медиатекой): /wp-content/...' ),
			array( 'name' => 'link_url',     'type' => 'text',  'label' => 'Ссылка (относительная: /kalkulyator/)' ),
		);
	}
}
