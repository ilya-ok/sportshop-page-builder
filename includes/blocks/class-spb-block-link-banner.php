<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class SPB_Block_Link_Banner extends SPB_Block_Base {

	public function get_type(): string  { return 'link_banner'; }
	public function get_label(): string { return 'Баннер-ссылка'; }

	public function get_fields(): array {
		return array(
			array( 'name' => 'title',     'type' => 'html',  'label' => 'Заголовок' ),
			array( 'name' => 'subtitle',  'type' => 'html',  'label' => 'Подзаголовок' ),
			array( 'name' => 'tag',       'type' => 'html',  'label' => 'Метка (напр. «Подробнее»)' ),
			array( 'name' => 'image',     'type' => 'image', 'label' => 'Картинка (из медиатеки)' ),
			array( 'name' => 'image_url', 'type' => 'text',  'label' => 'Картинка — относительный путь (приоритет над медиатекой): /wp-content/...' ),
			array( 'name' => 'link_url',  'type' => 'text',  'label' => 'Ссылка на страницу (относительная: /catalog/)' ),
		);
	}
}
