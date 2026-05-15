<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class SPB_Block_Text_Image extends SPB_Block_Base {

	public function get_type(): string {
		return 'text_image';
	}

	public function get_label(): string {
		return 'Текст + изображение';
	}

	public function get_fields(): array {
		return array(
			array( 'name' => 'title',          'type' => 'text',     'label' => 'Заголовок' ),
			array( 'name' => 'text',           'type' => 'textarea', 'label' => 'Текст' ),
			array( 'name' => 'image',          'type' => 'image',    'label' => 'Изображение' ),
			array(
				'name'    => 'image_position',
				'type'    => 'select',
				'label'   => 'Позиция изображения',
				'options' => array(
					'left'  => 'Слева',
					'right' => 'Справа',
				),
			),
			array( 'name' => 'link_url',   'type' => 'url',  'label' => 'URL ссылки' ),
			array( 'name' => 'link_label', 'type' => 'text', 'label' => 'Текст ссылки' ),
		);
	}
}
