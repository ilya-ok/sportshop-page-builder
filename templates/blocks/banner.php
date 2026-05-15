<?php
/**
 * Шаблон блока: Баннер
 * Доступные переменные: $block (array)
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

$bg_url = spb_get_image_url( $block['bg_image'] ?? '' );
$style  = $bg_url ? ' style="background-image:url(' . esc_url( $bg_url ) . ')"' : '';
?>
<div class="spb-banner"<?php echo $style; // already escaped above ?>>
	<div class="spb-banner__inner">
		<?php if ( ! empty( $block['title'] ) ) : ?>
			<h2 class="spb-banner__title"><?php echo esc_html( $block['title'] ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $block['subtitle'] ) ) : ?>
			<p class="spb-banner__subtitle"><?php echo esc_html( $block['subtitle'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $block['link_url'] ) ) : ?>
			<a class="spb-banner__btn"
				href="<?php echo esc_url( $block['link_url'] ); ?>">
				<?php echo esc_html( $block['link_label'] ?? 'Подробнее' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
