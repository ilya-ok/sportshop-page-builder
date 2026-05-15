<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class SPB_Block_Banner extends SPB_Block_Base {

	public function get_type(): string {
		return 'banner';
	}

	public function get_label(): string {
		return 'Баннер';
	}

	public function get_fields(): array {
		return array(
			array( 'name' => 'title',     'type' => 'text',  'label' => 'Заголовок' ),
			array( 'name' => 'subtitle',  'type' => 'text',  'label' => 'Подзаголовок' ),
			array( 'name' => 'bg_image',  'type' => 'image', 'label' => 'Фоновое изображение' ),
			array( 'name' => 'link_url',  'type' => 'url',   'label' => 'URL ссылки' ),
			array( 'name' => 'link_label','type' => 'text',  'label' => 'Текст кнопки' ),
		);
	}
}
