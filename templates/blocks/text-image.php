<?php
/**
 * Шаблон блока: Текст + изображение
 * Доступные переменные: $block (array)
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

$position  = $block['image_position'] ?? 'left';
$image_url = spb_get_image_url( $block['image'] ?? '' );
?>
<div class="spb-text-image spb-text-image--<?php echo esc_attr( $position ); ?>">
	<?php if ( $image_url ) : ?>
		<div class="spb-text-image__media">
			<img src="<?php echo esc_url( $image_url ); ?>"
				alt="<?php echo esc_attr( $block['title'] ?? '' ); ?>">
		</div>
	<?php endif; ?>

	<div class="spb-text-image__content">
		<?php if ( ! empty( $block['title'] ) ) : ?>
			<h2 class="spb-text-image__title"><?php echo esc_html( $block['title'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $block['text'] ) ) : ?>
			<div class="spb-text-image__body">
				<?php echo wp_kses_post( $block['text'] ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $block['link_url'] ) ) : ?>
			<a class="spb-text-image__link"
				href="<?php echo esc_url( $block['link_url'] ); ?>">
				<?php echo esc_html( $block['link_label'] ?? $block['link_url'] ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
