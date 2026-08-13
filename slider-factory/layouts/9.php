<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// slider settings
if ( isset( $slider['sf_9_width'] ) ) {
	$sf_9_width = $slider['sf_9_width'];
} else {
	$sf_9_width = '100%';
}
if ( isset( $slider['sf_9_height'] ) ) {
	$sf_9_height = $slider['sf_9_height'];
} else {
	$sf_9_height = '700px';
}
if ( isset( $slider['sf_9_auto_play'] ) ) {
	$sf_9_auto_play = $slider['sf_9_auto_play'];
} else {
	$sf_9_auto_play = 'true';
}
// FREE EDITION: auto-play-speed, fade-speed, colors, thumbnails and thumbnail size are PRO
// features (locked in the original plugin) — fixed defaults below. Only sorting is free.
$sf_9_auto_play_speed = '3000';
$sf_9_fade_speed = '400';
$sf_9_bgColor = '#252525';
$sf_9_textColor = '#ffffff';
$sf_9_thumbnails = 'true';
$sf_9_thumbWidth = '170';
$sf_9_thumbHeight = '100';
if ( isset( $slider['sf_9_sorting'] ) ) {
	$sf_9_sorting = $slider['sf_9_sorting'];
} else {
	$sf_9_sorting = 0;
}
// Custom CSS is a PRO feature — intentionally not rendered in Free.
?>
<div id="sf-9-<?php echo esc_attr( $sf_slider_id ); ?>" class="sf-9-container-<?php echo esc_attr( $sf_slider_id ); ?>">
	<div class="fullscreen-container-<?php echo esc_attr( $sf_slider_id ); ?> hidden-<?php echo esc_attr( $sf_slider_id ); ?>">
		<div class='fullscreen-div-<?php echo esc_attr( $sf_slider_id ); ?>'>
			<img class="remove-fullscreen-<?php echo esc_attr( $sf_slider_id ); ?>" src="<?php echo esc_url( plugin_dir_url( __DIR__ ).'layouts/assets/9/icons/remove_icon.webp' ); ?>" width="30" />
		</div>
	</div>

	<div id="sf-9-gallery-<?php echo esc_attr( $sf_slider_id ); ?>">
		<div id="slide-<?php echo esc_attr( $sf_slider_id ); ?>">
			<div class="counter-<?php echo esc_attr( $sf_slider_id ); ?>"></div>
			<a class="prev-<?php echo esc_attr( $sf_slider_id ); ?>">&#x2039;</a>
			<a class="next-<?php echo esc_attr( $sf_slider_id ); ?>">&#x203A;</a>
			<?php if ( $sf_9_auto_play == 'true' ) { ?>
			<img class="toggleDiapo-<?php echo esc_attr( $sf_slider_id ); ?>" src="<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/pause_diapo.png' ); ?>" width="24" />
			<?php }; ?>
			<?php if ( $sf_9_auto_play == 'false' ) { ?>
			<img class="toggleDiapo-<?php echo esc_attr( $sf_slider_id ); ?>" src="<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/play_diapo.png' ); ?>" width="24" />
			<?php }; ?>

			<div class="fullscreen-<?php echo esc_attr( $sf_slider_id ); ?>"></div>
			<img id='preview-<?php echo esc_attr( $sf_slider_id ); ?>' />
		</div>

		<div class="caption-container-<?php echo esc_attr( $sf_slider_id ); ?>">
			<span id="caption-<?php echo esc_attr( $sf_slider_id ); ?>"></span>
		</div>

		<div id="thumbnails" class="sf-9-thumbnails-wrap-<?php echo esc_attr( $sf_slider_id ); ?>">
			<div class="wrapper-<?php echo esc_attr( $sf_slider_id ); ?>">
				<?php
				$sf_9_slide_ids = array();
				if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
					$sf_9_slide_ids = $slider['sf_slide_id'];
				} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
					$sf_9_slide_ids = array_keys( $slider['sf_slide_title'] );
				}

				if ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
					if ( $sf_9_sorting == 1 ) {
						ksort( $slider['sf_slide_title'] );
						$sf_9_slide_ids = array_keys( $slider['sf_slide_title'] );
					} elseif ( $sf_9_sorting == 2 ) {
						krsort( $slider['sf_slide_title'] );
						$sf_9_slide_ids = array_keys( $slider['sf_slide_title'] );
					} elseif ( $sf_9_sorting == 3 ) {
						shuffle( $sf_9_slide_ids );
					} elseif ( $sf_9_sorting == 4 ) {
						asort( $slider['sf_slide_title'] );
						$sf_9_slide_ids = array_keys( $slider['sf_slide_title'] );
					} elseif ( $sf_9_sorting == 5 ) {
						arsort( $slider['sf_slide_title'] );
						$sf_9_slide_ids = array_keys( $slider['sf_slide_title'] );
					}
				}

				if ( ! empty( $sf_9_slide_ids ) ) {
					foreach ( $sf_9_slide_ids as $sf_id_1 ) {
						$attachment_id  = $sf_id_1;
						$sf_slide_title = isset( $slider['sf_slide_title'][ $sf_id_1 ] ) && trim( $slider['sf_slide_title'][ $sf_id_1 ] ) !== '' ? trim( $slider['sf_slide_title'][ $sf_id_1 ] ) : get_the_title( $attachment_id );
						$sf_slide_alt   = isset( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) && trim( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) !== '' ? trim( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
						$sf_slide_thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'large', true );
						$sf_slide_full_url      = wp_get_attachment_image_src( $attachment_id, 'full', true );
						if ( ! is_array( $sf_slide_thumbnail_url ) ) { continue; }
						$attachment             = get_post( $attachment_id );
						$sf_slide_descs         = isset( $slider['sf_slide_desc'][ $sf_id_1 ] ) && trim( $slider['sf_slide_desc'][ $sf_id_1 ] ) !== '' ? trim( $slider['sf_slide_desc'][ $sf_id_1 ] ) : ( $attachment ? $attachment->post_content : '' );
						?>
						<img class="thumbnail-<?php echo esc_attr( $sf_slider_id ); ?>" src="<?php echo esc_url( $sf_slide_thumbnail_url[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_title ); ?>" loading="lazy" decoding="async">
						<?php
					}
				}
				?>
			</div>
		</div>
	</div>
</div>

<style>
#sf-9-<?php echo esc_html( $sf_slider_id ); ?>,
#sf-9-<?php echo esc_html( $sf_slider_id ); ?> * {
	box-sizing: border-box;
}

#sf-9-<?php echo esc_html( $sf_slider_id ); ?> {
	width: <?php echo esc_html( $sf_9_width ); ?>;
	margin-left: auto;
	margin-right: auto;
}

.caption-container-<?php echo esc_html( $sf_slider_id ); ?> {
	background-color: <?php echo esc_html( $sf_9_bgColor ); ?>;
	text-align: center;
	padding: 6px 8px;
	color: <?php echo esc_html( $sf_9_textColor ); ?>;
	width: 100%;
}

.sf-9-thumbnails-wrap-<?php echo esc_html( $sf_slider_id ); ?> {
	text-align: center;
	white-space: nowrap;
	height: 20%;
	width: 100%;
	<?php if ( $sf_9_thumbnails == 'false' ) { ?>
	display: none;
	<?php } else { ?>
	display: block;
	<?php } ?>
}

.wrapper-<?php echo esc_html( $sf_slider_id ); ?> {
	position: relative;
	overflow: scroll;
	scroll-behavior: smooth;
	width: 100%;
	-ms-overflow-style: none;
}

.wrapper-<?php echo esc_html( $sf_slider_id ); ?>::-webkit-scrollbar {
	display: none;
}

#slide-<?php echo esc_html( $sf_slider_id ); ?> {
	position: relative;
	overflow: hidden;
}

#preview-<?php echo esc_html( $sf_slider_id ); ?> {
	height: <?php echo esc_html( $sf_9_height ); ?>;
	width: 100%;
	object-fit: cover;
}

.thumbnail-<?php echo esc_html( $sf_slider_id ); ?> {
	width: <?php echo esc_html( $sf_9_thumbWidth ); ?>px;
	height: <?php echo esc_html( $sf_9_thumbHeight ); ?>px;
	opacity: 0.5;
	display: inline-block;
	object-fit: cover;
	cursor: pointer;
}

.selected-<?php echo esc_html( $sf_slider_id ); ?> {
	opacity: 1;
}

.thumbnail-<?php echo esc_html( $sf_slider_id ); ?>:hover {
	opacity: 1;
}

#sf-9-gallery-<?php echo esc_html( $sf_slider_id ); ?> {
	width: 100%;
	margin: auto;
	padding: auto;
}

#sf-9-<?php echo esc_html( $sf_slider_id ); ?> a.prev-<?php echo esc_html( $sf_slider_id ); ?>,
#sf-9-<?php echo esc_html( $sf_slider_id ); ?> a.next-<?php echo esc_html( $sf_slider_id ); ?> {
	cursor: pointer;
	position: absolute;
	top: 50%;
	width: auto;
	padding: 12px 16px;
	transform: translateY(-50%);
	color: #ffffff;
	font-weight: bold;
	font-size: 40px;
	border-radius: 3px;
	user-select: none;
	-webkit-user-select: none;
	text-decoration: none;
	box-shadow: none;
	border-bottom: none;
}

.next-<?php echo esc_html( $sf_slider_id ); ?> {
	right: 0;
}

.prev-<?php echo esc_html( $sf_slider_id ); ?> {
	left: 0;
}

.toggleDiapo-<?php echo esc_html( $sf_slider_id ); ?> {
	position: absolute;
	bottom: 15px;
	left: 50%;
	transform: translateX(-50%);
	cursor: pointer;
	padding: 5px;
	user-select: none;
	-webkit-user-select: none;
}

.fullscreen-<?php echo esc_html( $sf_slider_id ); ?> {
	position: absolute;
	top: 15px;
	right: 15px;
	width: 20px;
	height: 20px;
	cursor: pointer;
	user-select: none;
	background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3'/%3E%3C/svg%3E");
	background-size: contain;
	background-repeat: no-repeat;
	transition: opacity 0.2s ease;
}

.fullscreen-<?php echo esc_html( $sf_slider_id ); ?>:hover {
	opacity: 0.8;
}

.prev-<?php echo esc_html( $sf_slider_id ); ?>:hover,
.next-<?php echo esc_html( $sf_slider_id ); ?>:hover,
.toggleDiapo-<?php echo esc_html( $sf_slider_id ); ?>:hover {
	background-color: rgba(0, 0, 0, 0.8);
}

.counter-<?php echo esc_html( $sf_slider_id ); ?> {
	position: absolute;
	border-radius: 50%;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 36px;
	height: 36px;
	top: 15px;
	left: 15px;
	background-color: <?php echo esc_html( $sf_9_bgColor ); ?>;
	color: <?php echo esc_html( $sf_9_textColor ); ?>;
	border: 1px solid <?php echo esc_html( $sf_9_textColor ); ?>;
	font-family: system-ui, -apple-system, sans-serif;
	font-size: 13px;
	font-weight: 600;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.fullscreen-container-<?php echo esc_html( $sf_slider_id ); ?> {
	position: fixed;
	top: 0;
	left: 0;
	bottom: 0;
	right: 0;
	z-index: 10;
	background-color: rgba(0, 0, 0, 0.9);
	display: block;
}

.fullscreen-div-<?php echo esc_html( $sf_slider_id ); ?> {
	width: 100%;
	height: 100%;
	display: block;
	margin: auto;
	position: relative;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	box-sizing: content-box;
}

.hidden-<?php echo esc_html( $sf_slider_id ); ?> {
	display: none;
}

.counter-<?php echo esc_html( $sf_slider_id ); ?>,
.fullscreen-<?php echo esc_html( $sf_slider_id ); ?>,
.prev-<?php echo esc_html( $sf_slider_id ); ?>,
.next-<?php echo esc_html( $sf_slider_id ); ?>,
.toggleDiapo-<?php echo esc_html( $sf_slider_id ); ?> {
	z-index: 5;
}

.remove-fullscreen-<?php echo esc_html( $sf_slider_id ); ?> {
	position: absolute;
	top: 15px;
	right: 15px;
	cursor: pointer;
}

.remove-fullscreen-<?php echo esc_html( $sf_slider_id ); ?>:hover {
	background-color: rgba(0, 0, 0, 0.8);
}

</style>
<script>
window.myNameSpace = window.myNameSpace || {};

jQuery(function () {

	// load images
	jQuery.fn.addImage = function (filename, description) {
		var img = document.createElement('img');
		img.src = "images/" + filename;
		img.alt = description;
		img.className = "thumbnail-<?php echo esc_js( $sf_slider_id ); ?>";
		jQuery(this).append(img);
	}

	// check if element is hidden after scrollbar
	jQuery.fn.overflown = function () {
		var limitLeft = jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>').offset().left;
		var limitRight = limitLeft + jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>').width();
		var elemOffsetLeft = jQuery(this[0]).offset().left;
		var elemOffsetRight = elemOffsetLeft + jQuery(this[0]).width() / 2;
		return (elemOffsetRight > limitRight || elemOffsetLeft < limitLeft) ? true : false;
	}

	// scroll to the end of element
	function scrollToElement(el, direction) {
		element_width = el.width();
		scroll_left = jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>')[0].scrollLeft;
		if (direction == 'next')
			jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>')[0].scrollTo(scroll_left + element_width, 0);
		else if (direction == 'prev')
			jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>')[0].scrollTo(scroll_left - element_width, 0);
	}

	function showNextImg() {
		clearInterval(interv);
		
		interv = setInterval(showNextImg, <?php echo esc_js( $sf_9_auto_play_speed ); ?>);
		
		var el = jQuery('.selected-<?php echo esc_js( $sf_slider_id ); ?>');
		var counter = jQuery('.counter-<?php echo esc_js( $sf_slider_id ); ?>');
		if (el.next().length != 0) {
			counter.text(el.index() + 1);
			if (el.next().overflown())
				scrollToElement(el, 'next');
			el.next().trigger('click');
			el.show();
		}
		else {
			jQuery('.thumbnail-<?php echo esc_js( $sf_slider_id ); ?>:first').trigger('click');
			jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>')[0].scrollTo(0, 0);
			counter.text('1');
		}
		jQuery('.toggleDiapo-<?php echo esc_js( $sf_slider_id ); ?>').attr('src', "<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/pause_diapo.png' ); ?>");
	}

	function showPrevImg() {
		clearInterval(interv);
		
		interv = setInterval(showNextImg, <?php echo esc_js( $sf_9_auto_play_speed ); ?>);
		
		var el = jQuery('.selected-<?php echo esc_js( $sf_slider_id ); ?>');
		var counter = jQuery('.counter-<?php echo esc_js( $sf_slider_id ); ?>');
		if (el.prev().length != 0) {
			counter.text(el.index() + 1);
			if (el.prev().overflown())
				scrollToElement(el, 'prev');
			el.prev().trigger('click');
		}
		else {
			jQuery('.thumbnail-<?php echo esc_js( $sf_slider_id ); ?>:last').trigger('click');
			jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>')[0].scrollTo(jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>')[0].scrollWidth, 0);
			counter.text(jQuery('.thumbnail-<?php echo esc_js( $sf_slider_id ); ?>:last').index() + 1);
		}
		jQuery('.toggleDiapo-<?php echo esc_js( $sf_slider_id ); ?>').attr('src', "<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/pause_diapo.png' ); ?>");
	}

	function previewImg(e) {
		if (e.originalEvent !== undefined) {
			jQuery('.toggleDiapo-<?php echo esc_js( $sf_slider_id ); ?>').attr('src', "<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/play_diapo.png' ); ?>");
			interv = clearInterval(interv);
		}
		var wrapper = jQuery('.wrapper-<?php echo esc_js( $sf_slider_id ); ?>');
		var index = jQuery(this).index();
		jQuery('.selected-<?php echo esc_js( $sf_slider_id ); ?>').toggleClass('selected-<?php echo esc_js( $sf_slider_id ); ?>');
		jQuery(this).toggleClass('selected-<?php echo esc_js( $sf_slider_id ); ?>')
		jQuery("#caption-<?php echo esc_js( $sf_slider_id ); ?>").text(jQuery('.selected-<?php echo esc_js( $sf_slider_id ); ?>').attr('alt'));
		jQuery('.counter-<?php echo esc_js( $sf_slider_id ); ?>').text(index + 1);
		var src = jQuery(this).attr('src');
		jQuery('#preview-<?php echo esc_js( $sf_slider_id ); ?>').fadeOut(<?php echo esc_js( $sf_9_fade_speed ); ?>, () => {
			jQuery('#preview-<?php echo esc_js( $sf_slider_id ); ?>').attr('src', src);
			jQuery('#preview-<?php echo esc_js( $sf_slider_id ); ?>').fadeIn(<?php echo esc_js( $sf_9_fade_speed ); ?>);
		});
	}

	function toggleDiapo() {
		interv = (interv != null) ? clearInterval(interv) : setInterval(showNextImg, <?php echo esc_js( $sf_9_auto_play_speed ); ?>);
		var src = jQuery('.toggleDiapo-<?php echo esc_js( $sf_slider_id ); ?>').attr('src');
		if (src == "<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/play_diapo.png' ); ?>")
			jQuery('.toggleDiapo-<?php echo esc_js( $sf_slider_id ); ?>').attr('src', "<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/pause_diapo.png' ); ?>");
		else
			jQuery('.toggleDiapo-<?php echo esc_js( $sf_slider_id ); ?>').attr('src', "<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/play_diapo.png' ); ?>");
	}

	function goFullscreen() {
		jQuery('.toggleDiapo-<?php echo esc_js( $sf_slider_id ); ?>').attr('src', "<?php echo esc_url( plugin_dir_url( __DIR__ ). 'layouts/assets/9/icons/remove_icon.webp' ); ?>");
		interv = clearInterval(interv);

		var selected = jQuery('.selected-<?php echo esc_js( $sf_slider_id ); ?>').attr('src');
		var container = jQuery('.fullscreen-container-<?php echo esc_js( $sf_slider_id ); ?>');
		jQuery('.fullscreen-div-<?php echo esc_js( $sf_slider_id ); ?>').css({
			'background-image': 'url(' + selected + ')',
			'background-size': 'contain',
			'background-repeat': 'no-repeat',
			'background-position': 'center'
		});
		container.fadeIn('slow');
		container.on('click', function () {
			jQuery(this).fadeOut('slow');
		});
	}

	// show first image
	var first = jQuery('.thumbnail-<?php echo esc_js( $sf_slider_id ); ?>:first').toggleClass('selected-<?php echo esc_js( $sf_slider_id ); ?>');
	jQuery('.counter-<?php echo esc_js( $sf_slider_id ); ?>').text('1');
	jQuery('#preview-<?php echo esc_js( $sf_slider_id ); ?>').attr('src', first.attr('src'));
	jQuery("#caption-<?php echo esc_js( $sf_slider_id ); ?>").text(first.attr('alt'));
	// start auto diapo
	var interv;
	<?php if ( $sf_9_auto_play == 'true' ) { ?>
	interv = setInterval(showNextImg, <?php echo esc_js( $sf_9_auto_play_speed ); ?>);
	<?php }; ?>
	<?php if ( $sf_9_auto_play == 'false' ) { ?>
	interv = null;
	<?php }; ?>

	// setup event listeners
	jQuery('.next-<?php echo esc_js( $sf_slider_id ); ?>').on('click', showNextImg);
	jQuery('.prev-<?php echo esc_js( $sf_slider_id ); ?>').on('click', showPrevImg);
	jQuery('.thumbnail-<?php echo esc_js( $sf_slider_id ); ?>').on('click', previewImg);
	jQuery('.toggleDiapo-<?php echo esc_js( $sf_slider_id ); ?>').on('click', toggleDiapo);
	jQuery('.fullscreen-<?php echo esc_js( $sf_slider_id ); ?>').on('click', goFullscreen);

});
</script>
