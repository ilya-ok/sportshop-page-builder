<?php
/**
 * Шаблон блока: Баннер-ссылка
 * Горизонтальный баннер на всю ширину, высота 100px.
 * Переменные: $block (array)
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

$image_url_raw = trim( $block['image_url'] ?? '' );
$image_url     = $image_url_raw ? home_url( $image_url_raw ) : spb_get_image_url( $block['image'] ?? '', 'large' );
$title         = $block['title']    ?? '';
$subtitle      = $block['subtitle'] ?? '';
$tag           = $block['tag']      ?? '';
$link_url      = $block['link_url'] ?? '';

if ( $link_url && ! preg_match( '#^https?://#', $link_url ) ) {
	$link_url = home_url( $link_url );
}

$bg_style = $image_url ? ' style="background-image:url(' . esc_url( $image_url ) . ')"' : '';
?>
<a class="spb-link-banner<?php echo $image_url ? '' : ' spb-link-banner--no-image'; ?>"
   href="<?php echo esc_attr( $link_url ); ?>">

	<div class="spb-link-banner__bg"<?php echo $bg_style; ?>></div>

	<div class="spb-link-banner__overlay"></div>

	<div class="spb-link-banner__body">

		<div class="spb-link-banner__text">
			<?php if ( $title ) : ?>
				<span class="spb-link-banner__title"><?php echo wp_kses_post( $title ); ?></span>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<span class="spb-link-banner__subtitle"><?php echo wp_kses_post( $subtitle ); ?></span>
			<?php endif; ?>
		</div>

		<div class="spb-link-banner__right">
			<?php if ( $tag ) : ?>
				<span class="spb-link-banner__tag"><?php echo wp_kses_post( $tag ); ?></span>
			<?php endif; ?>
			<span class="spb-link-banner__arrow" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
					<path d="M5 12h14M12 5l7 7-7 7"/>
				</svg>
			</span>
		</div>

	</div>

</a>
