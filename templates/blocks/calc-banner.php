<?php
/**
 * Шаблон блока: Баннер калькулятора
 * Горизонтальный баннер на всю ширину, высота 150px.
 * Дизайн как у карточки калькулятора: тёмный фон, градиент, тег, декор.
 * Переменные: $block (array)
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

$bg_image_url = trim( $block['bg_image_url'] ?? '' );
$bg_url       = $bg_image_url ? home_url( $bg_image_url ) : spb_get_image_url( $block['bg_image'] ?? '' );
$link_url     = $block['link_url'] ?? '';
$title        = $block['title']    ?? '';
$subtitle     = $block['subtitle'] ?? '';
$tag          = $block['tag']      ?? '';

if ( $link_url && ! preg_match( '#^https?://#', $link_url ) ) {
	$link_url = home_url( $link_url );
}

$bg_style = $bg_url ? ' style="background-image:url(' . esc_attr( $bg_url ) . ')"' : '';
?>
<a class="spb-calc-banner<?php echo $bg_url ? '' : ' spb-calc-banner--no-image'; ?>"
   href="<?php echo esc_attr( $link_url ); ?>">

	<div class="spb-calc-banner__bg"<?php echo $bg_style; ?>></div>

	<div class="spb-calc-banner__overlay"></div>

	<div class="spb-calc-banner__body">

		<div class="spb-calc-banner__text">
			<?php if ( $title ) : ?>
				<h3 class="spb-calc-banner__title"><?php echo wp_kses_post( $title ); ?></h3>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<p class="spb-calc-banner__subtitle"><?php echo wp_kses_post( $subtitle ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $tag ) : ?>
			<div class="spb-calc-banner__tag-wrap">
				<span class="spb-calc-banner__tag">
					<svg class="spb-calc-banner__tag-icon" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<rect x="3" y="2" width="18" height="5" rx="1"/>
						<rect x="3" y="10" width="5" height="4" rx="1"/>
						<rect x="9.5" y="10" width="5" height="4" rx="1"/>
						<rect x="16" y="10" width="5" height="4" rx="1"/>
						<rect x="3" y="17" width="5" height="4" rx="1"/>
						<rect x="9.5" y="17" width="5" height="4" rx="1"/>
						<rect x="16" y="17" width="5" height="4" rx="1"/>
					</svg>
					<?php echo wp_kses_post( $tag ); ?>
				</span>
			</div>
		<?php endif; ?>

	</div>

	<div class="spb-calc-banner__deco" aria-hidden="true"></div>

</a>
