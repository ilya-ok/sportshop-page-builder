<?php
/**
 * Шаблон блока: Текст
 * Доступные переменные: $block (array)
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );
?>
<div class="spb-text">
	<?php if ( ! empty( $block['title'] ) ) : ?>
		<h2 class="spb-text__title"><?php echo esc_html( $block['title'] ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $block['text'] ) ) : ?>
		<div class="spb-text__body">
			<?php echo wp_kses_post( $block['text'] ); ?>
		</div>
	<?php endif; ?>
</div>
