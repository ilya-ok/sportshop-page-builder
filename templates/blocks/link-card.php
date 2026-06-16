<?php
/**
 * Шаблон блока: Карточка ссылки
 * Картинка сверху, надпись в белом блоке снизу.
 * Переменные: $block (array)
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

$image_url_raw = trim( $block['image_url'] ?? '' );
$image_url     = $image_url_raw ? home_url( $image_url_raw ) : spb_get_image_url( $block['image'] ?? '', 'large' );
$title         = $block['title']    ?? '';
$link_url      = $block['link_url'] ?? '';

if ( $link_url && ! preg_match( '#^https?://#', $link_url ) ) {
	$link_url = home_url( $link_url );
}
?>
<a class="spb-link-card" href="<?php echo esc_attr( $link_url ); ?>">

	<div class="spb-link-card__img-wrap">
		<?php if ( $image_url ) : ?>
			<img class="spb-link-card__img"
			     src="<?php echo esc_url( $image_url ); ?>"
			     alt="<?php echo esc_attr( $title ); ?>"
			     loading="lazy">
		<?php else : ?>
			<div class="spb-link-card__img-placeholder"></div>
		<?php endif; ?>
	</div>

	<?php if ( $title ) : ?>
		<div class="spb-link-card__body">
			<span class="spb-link-card__title"><?php echo esc_html( $title ); ?></span>
			<span class="spb-link-card__arrow" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<path d="M5 12h14M12 5l7 7-7 7"/>
				</svg>
			</span>
		</div>
	<?php endif; ?>

</a>
