<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Forbidden' );
}

class SPB_Block_Text extends SPB_Block_Base {

	public function get_type(): string {
		return 'text';
	}

	public function get_label(): string {
		return 'Текст';
	}

	public function get_fields(): array {
		return array(
			array( 'name' => 'title', 'type' => 'text',     'label' => 'Заголовок' ),
			array( 'name' => 'text',  'type' => 'textarea', 'label' => 'Текст' ),
		);
	}
}
