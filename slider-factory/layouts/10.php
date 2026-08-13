<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// slider settings
if ( isset( $slider['sf_10_width'] ) ) {
	$sf_10_width = $slider['sf_10_width'];
} else {
	$sf_10_width = '100%';
}
if ( isset( $slider['sf_10_height'] ) && ! empty( $slider['sf_10_height'] ) ) {
	$sf_10_height = trim( $slider['sf_10_height'] );
} else {
	$sf_10_height = '700px';
}

if ( strpos( $sf_10_height, '%' ) !== false || $sf_10_height === 'auto' ) {
	$sf_10_height_css = '700px';
} else {
	$sf_10_height_css = is_numeric( $sf_10_height ) ? $sf_10_height . 'px' : $sf_10_height;
}
$sf_10_snap_height = '100%';

// FREE EDITION: slider background/text and dot colors are PRO features (locked in the
// original plugin) — fixed defaults below. Button colors are free in Free.
$sf_10_BgColor = '#323232';
$sf_10_TextColor = '#ffffff';
$sf_10_dotsColor = '#323232';
$sf_10_dotsActiveColor = '#ffffff';
if ( isset( $slider['sf_10_btnBgColor'] ) ) {
	$sf_10_btnBgColor = $slider['sf_10_btnBgColor'];
} else {
	$sf_10_btnBgColor = '#F1F1F1';
}
if ( isset( $slider['sf_10_btnTextColor'] ) ) {
	$sf_10_btnTextColor = $slider['sf_10_btnTextColor'];
} else {
	$sf_10_btnTextColor = '#000000';
}
if ( isset( $slider['sf_10_sorting'] ) ) {
	$sf_10_sorting = $slider['sf_10_sorting'];
} else {
	$sf_10_sorting = 0;
}
// Custom CSS is a PRO feature — intentionally not rendered in Free.

// CSS and JS
wp_enqueue_script( 'jquery' );
?>

<div class="sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?>">
	<div class="sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?>">
		<?php
		$sf_10_slide_ids = array();
		if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
			$sf_10_slide_ids = $slider['sf_slide_id'];
		} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			$sf_10_slide_ids = array_keys( $slider['sf_slide_title'] );
		}

		if ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			if ( $sf_10_sorting == 1 ) {
				ksort( $slider['sf_slide_title'] );
				$sf_10_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_10_sorting == 2 ) {
				krsort( $slider['sf_slide_title'] );
				$sf_10_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_10_sorting == 3 ) {
				shuffle( $sf_10_slide_ids );
			} elseif ( $sf_10_sorting == 4 ) {
				asort( $slider['sf_slide_title'] );
				$sf_10_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_10_sorting == 5 ) {
				arsort( $slider['sf_slide_title'] );
				$sf_10_slide_ids = array_keys( $slider['sf_slide_title'] );
			}
		}

		if ( ! empty( $sf_10_slide_ids ) ) {
			foreach ( $sf_10_slide_ids as $sf_id ) {
				$attachment_id = $sf_id;
				if ( isset( $slider['sf_slide_title'][ $sf_id ] ) && '' !== trim( $slider['sf_slide_title'][ $sf_id ] ) ) {
					$sf_slide_title = $slider['sf_slide_title'][ $sf_id ];
				} else {
					$sf_slide_title = get_the_title( $attachment_id );
				}
				$sf_slide_alt   = isset( $slider['sf_slide_alt_text'][ $sf_id ] ) && trim( $slider['sf_slide_alt_text'][ $sf_id ] ) !== '' ? trim( $slider['sf_slide_alt_text'][ $sf_id ] ) : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$sf_slide_thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'large', true );
				$sf_slide_full_url      = wp_get_attachment_image_src( $attachment_id, 'full', true );
				if ( ! is_array( $sf_slide_full_url ) ) { continue; }
				if ( isset( $slider['sf_slide_desc'][ $sf_id ] ) && '' !== trim( $slider['sf_slide_desc'][ $sf_id ] ) ) {
					$sf_slide_descs = $slider['sf_slide_desc'][ $sf_id ];
				} else {
					$attachment = get_post( $attachment_id );
					$sf_slide_descs = ( $attachment ? $attachment->post_content : '' );
				}
				if ( isset( $slider['sf_slide_link_text_1'][ $sf_id ] ) ) {
					$sf_slide_link_text_1 = $slider['sf_slide_link_text_1'][ $sf_id ];
				} else {
					$sf_slide_link_text_1 = '';
				}
				if ( isset( $slider['sf_slide_link_1'][ $sf_id ] ) ) {
					$sf_slide_link_1 = $slider['sf_slide_link_1'][ $sf_id ];
				} else {
					$sf_slide_link_1 = '';
				}
				if ( isset( $slider['sf_slide_link_text_2'][ $sf_id ] ) ) {
					$sf_slide_link_text_2 = $slider['sf_slide_link_text_2'][ $sf_id ];
				} else {
					$sf_slide_link_text_2 = '';
				}
				if ( isset( $slider['sf_slide_link_2'][ $sf_id ] ) ) {
					$sf_slide_link_2 = $slider['sf_slide_link_2'][ $sf_id ];
				} else {
					$sf_slide_link_2 = '';
				}
				?>
				<section style="background-image: url('<?php echo esc_url( $sf_slide_full_url[0] ); ?>');">
					<div class="content">
						<span class="sf-10-title"><?php echo esc_html( $sf_slide_title ); ?></span>
						<span class="sf-10-desc"><?php echo esc_html( $sf_slide_descs ); ?></span>
						<?php if ( $sf_slide_link_1 != '' && $sf_slide_link_text_1 != '' ) { ?>
							<a class="sf-10-slide-button-link-1" href="<?php echo esc_url( $sf_slide_link_1 ); ?>" target="_blank"><button type="button" class="sf-10-bttns"><?php echo esc_html( $sf_slide_link_text_1 ); ?></button></a>
							<?php
						}
						if ( $sf_slide_link_2 != '' && $sf_slide_link_text_2 != '' ) {
							?>
							<a class="sf-10-slide-button-link-2" href="<?php echo esc_url( $sf_slide_link_2 ); ?>" target="_blank"><button type="button" class="sf-10-bttns"><?php echo esc_html( $sf_slide_link_text_2 ); ?></button></a>
						<?php } ?>
					</div>
				</section>
				<?php
			}
		}
		?>
	</div>
	<div class="nav-dots-container">
		<ul class="nav-dots">
			<?php
			if ( ! empty( $sf_10_slide_ids ) ) {
				$y = 0;
				foreach ( $sf_10_slide_ids as $sf_id_2 ) {
					?>
					<li data-slide-link="<?php echo esc_attr( $y ); ?>"></li>
					<?php
					$y++;
				}
			}
			?>
		</ul>
	</div>
</div>

<style>
.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> {
	margin-left: auto;
	margin-right: auto;
	font-family: sans-serif;
	background: <?php echo esc_attr( $sf_10_BgColor ); ?>;
	position: relative;
	height: <?php echo esc_attr( $sf_10_height_css ); ?>;
	width: <?php echo esc_attr( $sf_10_width ); ?>;
	overflow: hidden; 
	box-sizing: border-box;
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> section {
	color: <?php echo esc_attr( $sf_10_TextColor ); ?>;
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> {
	height: <?php echo esc_attr( $sf_10_snap_height ); ?>;
	overflow-y: scroll;
	scroll-snap-type: y mandatory;
	-ms-overflow-style: none;
	border: 15px solid rgba(0, 0, 0, 0); 
	box-sizing: border-box;
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?>::-webkit-scrollbar {
	display: none; 
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section {
	height: 100%;
	width: 100%;
	box-sizing: border-box;
	background-position: center center;
	background-size: cover;
	background-repeat: no-repeat;
	position: relative;
	background-color: rgba(0, 0, 0, 0.5);
	-webkit-transition: opacity 2s ease;
	-moz-transition: opacity 2s ease;
	-ms-transition: opacity 2s ease;
	-o-transition: opacity 2s ease;
	transition: opacity 2s ease;
	margin: 0;
	scroll-snap-align: start;
	scroll-snap-stop: always;
	padding: 30px; 
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section::before {
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	margin: auto;
	content: '';
	position: absolute;
	display: inline-block;
	height: 100%;
	width: 100%;
	background-image: inherit;
	background-position: center center;
	background-size: cover;
	background-repeat: no-repeat;
	-webkit-transition: opacity 2s ease;
	-moz-transition: opacity 2s ease;
	-ms-transition: opacity 2s ease;
	-o-transition: opacity 2s ease;
	transition: opacity 2s ease;
	opacity: 0.25;
	z-index: -1; 
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section.active {
	background-color: rgba(0, 0, 0, 0); 
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section.active::before {
	opacity: 1; 
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section:first-child {
	margin-top: 0; 
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section:last-of-type {
	margin-bottom: 0; 
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section .content {
	margin-top: 0vh; 
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section .content .sf-10-title {
	font-size: 24px;
	font-weight: bold;
	display: block;
	padding: 10px;
	color: <?php echo esc_attr( $sf_10_TextColor ); ?>;
}

.sf-10-snap-container-<?php echo esc_attr( $sf_slider_id ); ?> section .content .sf-10-desc {
	font-size: 16px;
	display: block;
	padding: 10px;
	color: <?php echo esc_attr( $sf_10_TextColor ); ?>;
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> section .content .sf-10-bttns {
	background-color: <?php echo esc_attr( $sf_10_btnBgColor ); ?>; 
	color: <?php echo esc_attr( $sf_10_btnTextColor ); ?>; 
	border: 1px solid rgba(255, 255, 255, 0.15); 
	cursor: pointer; 
	border-radius: 2px; 
	text-align: center; 
	padding: 8px 22px; 
	margin-left: 10px;
	font-size: 13px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.8px;
	transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> section .content .sf-10-bttns:hover {
	opacity: 0.9;
	transform: translateY(-1px);
	box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> .nav-dots-container {
	position: absolute;
	top: 50%;
	right: 40px;
	transform: translateY(-50%);
	height: 160px;
	width: 40px;
	overflow: hidden;
	display: flex;
	align-items: center;
	justify-content: center;
	z-index: 10;
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> .nav-dots {
	display: flex;
	flex-direction: column;
	padding: 0;
	margin: 0;
	list-style-type: none;
	transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
	align-items: center;
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> .nav-dots li {
	display: block;
	margin: 6px auto;
	background: <?php echo esc_attr( $sf_10_dotsColor ); ?>;
	border-radius: 50%;
	width: 10px;
	height: 10px; 
	min-width: 10px;
	min-height: 10px;
	flex-shrink: 0;
	box-sizing: border-box;
	cursor: pointer;
	transition: transform 0.3s ease, background-color 0.3s ease, opacity 0.3s ease;
	transform: scale(0.6);
	opacity: 0.5;
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> .nav-dots li.active {
	background-color: <?php echo esc_attr( $sf_10_dotsActiveColor ); ?>;
	transform: scale(1.3);
	opacity: 1;
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> .nav-dots li.adjacent-1 {
	transform: scale(0.9);
	opacity: 0.8;
}

.sf-10-main-container-<?php echo esc_attr( $sf_slider_id ); ?> .nav-dots li.adjacent-2 {
	transform: scale(0.6);
	opacity: 0.6;
}

</style>

<script>
var isAnimating<?php echo esc_js( $sf_slider_id ); ?> = false;

function updateNavDots<?php echo esc_js( $sf_slider_id ); ?>(activeIndex) {
	var $dots = jQuery(".sf-10-main-container-<?php echo esc_js( $sf_slider_id ); ?> li");
	$dots.removeClass("active adjacent-1 adjacent-2");
	
	var $activeDot = $dots.eq(activeIndex);
	$activeDot.addClass("active");
	
	if (activeIndex > 0) {
		$dots.eq(activeIndex - 1).addClass("adjacent-1");
	}
	if (activeIndex > 1) {
		$dots.eq(activeIndex - 2).addClass("adjacent-2");
	}
	if (activeIndex < $dots.length - 1) {
		$dots.eq(activeIndex + 1).addClass("adjacent-1");
	}
	if (activeIndex < $dots.length - 2) {
		$dots.eq(activeIndex + 2).addClass("adjacent-2");
	}
	
	var dotHeight = 22;
	var containerHeight = 160;
	var translateY = (containerHeight / 2) - (activeIndex * dotHeight + (dotHeight / 2));
	
	jQuery(".sf-10-main-container-<?php echo esc_js( $sf_slider_id ); ?> .nav-dots").css(
		"transform", "translateY(" + translateY + "px)"
	);
}

jQuery(document).ready((function() {
	jQuery(".sf-10-snap-container-<?php echo esc_js( $sf_slider_id ); ?> section:nth-child(1)").addClass("active");
	updateNavDots<?php echo esc_js( $sf_slider_id ); ?>(0);
}));

jQuery(".sf-10-snap-container-<?php echo esc_js( $sf_slider_id ); ?>").scroll((function() {
	if (isAnimating<?php echo esc_js( $sf_slider_id ); ?>) return;
	
	var $container = jQuery(this);
	var containerHeight = $container.height();
	var viewportCenter = containerHeight / 2;
	
	var activeIndex = 0;
	var minDistance = Infinity;
	
	var $sections = $container.find("section");
	$sections.each(function(index) {
		var sectionCenterInViewport = jQuery(this).position().top + jQuery(this).outerHeight() / 2;
		var distance = Math.abs(viewportCenter - sectionCenterInViewport);
		if (distance < minDistance) {
			minDistance = distance;
			activeIndex = index;
		}
	});
	
	$sections.removeClass("active");
	$sections.eq(activeIndex).addClass("active");
	
	updateNavDots<?php echo esc_js( $sf_slider_id ); ?>(activeIndex);
})); 

jQuery(".sf-10-main-container-<?php echo esc_js( $sf_slider_id ); ?> li").click((function() {
	if (isAnimating<?php echo esc_js( $sf_slider_id ); ?>) return;
	
	var index = jQuery(this).attr("data-slide-link");
	var $container = jQuery(".sf-10-snap-container-<?php echo esc_js( $sf_slider_id ); ?>");
	var $section = $container.find("section").eq(index);
	if ($section.length) {
		isAnimating<?php echo esc_js( $sf_slider_id ); ?> = true;
		
		updateNavDots<?php echo esc_js( $sf_slider_id ); ?>(parseInt(index));
		
		var currentScrollTop = $container.scrollTop();
		var sectionTop = $section.position().top;
		var targetScrollTop = currentScrollTop + sectionTop;
		
		$container.css("scroll-snap-type", "none");
		
		$container.animate({
			scrollTop: targetScrollTop
		}, 1000, function() {
			$container.css("scroll-snap-type", "y mandatory");
			isAnimating<?php echo esc_js( $sf_slider_id ); ?> = false;
		});
	}
}));
</script>
