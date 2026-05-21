<?php
/**
 * Шаблон блока: Карточка калькулятора
 * Весь блок — кликабельная ссылка с фоновым изображением и белым текстом.
 * Переменные: $block (array)
 */
if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

$bg_image_url = trim( $block['bg_image_url'] ?? '' );
$bg_url       = $bg_image_url ? home_url( $bg_image_url ) : spb_get_image_url( $block['bg_image'] ?? '' );
$link_url = $block['link_url'] ?? '';
$title    = $block['title']    ?? '';
$subtitle = $block['subtitle'] ?? '';
$tag      = $block['tag']      ?? '';

/* Нормализация: хранится относительный путь (/slug/) → добавляем home_url() */
if ( $link_url && ! preg_match( '#^https?://#', $link_url ) ) {
	$link_url = home_url( $link_url );
} elseif ( $link_url && preg_match( '#^https?://#', $link_url ) ) {
	/* Полный URL: оставляем как есть (возможно задан вручную) */
}

$bg_style = $bg_url ? ' style="background-image:url(' . esc_attr( $bg_url ) . ')"' : '';
?>
<a class="spb-calc-card<?php echo $bg_url ? '' : ' spb-calc-card--no-image'; ?>"
   href="<?php echo esc_attr( $link_url ); ?>">

	<div class="spb-calc-card__bg"<?php echo $bg_style; ?>></div>

	<div class="spb-calc-card__overlay"></div>

	<div class="spb-calc-card__body">

		<div class="spb-calc-card__text">
			<?php if ( $title ) : ?>
				<h3 class="spb-calc-card__title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>

			<?php if ( $subtitle ) : ?>
				<p class="spb-calc-card__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $tag ) : ?>
			<div class="spb-calc-card__footer">
				<span class="spb-calc-card__tag">
					<svg class="spb-calc-card__tag-icon" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<rect x="3" y="2" width="18" height="5" rx="1"/>
						<rect x="3" y="10" width="5" height="4" rx="1"/>
						<rect x="9.5" y="10" width="5" height="4" rx="1"/>
						<rect x="16" y="10" width="5" height="4" rx="1"/>
						<rect x="3" y="17" width="5" height="4" rx="1"/>
						<rect x="9.5" y="17" width="5" height="4" rx="1"/>
						<rect x="16" y="17" width="5" height="4" rx="1"/>
					</svg>
					<?php echo esc_html( $tag ); ?>
				</span>
			</div>
		<?php endif; ?>
	</div>

	<!-- Декоративный элемент — угловая сетка/штриховка -->
	<div class="spb-calc-card__deco" aria-hidden="true"></div>

</a>
