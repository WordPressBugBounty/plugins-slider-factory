<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// slider settings.
if ( isset( $slider['sf_3_width'] ) ) {
	$sf_3_width = $slider['sf_3_width'];
} else {
	$sf_3_width = '100%';
}
if ( isset( $slider['sf_3_height'] ) ) {
	$sf_3_height = $slider['sf_3_height'];
} else {
	$sf_3_height = '700';
}
$sf_3_design_preset    = 4; // Locked to Preset 4 in Free edition
if ( isset( $slider['sf_3_auto_play'] ) ) {
	$sf_3_auto_play = $slider['sf_3_auto_play'];
} else {
	$sf_3_auto_play = 'true';
}
$sf_3_auto_play_speed  = 3500;
$sf_3_transition_speed = 2500;
$sf_3_slide_gap        = 10;
$sf_3_navigation_arrow = 'true';
if ( isset( $slider['sf_3_sorting'] ) ) {
	$sf_3_sorting = $slider['sf_3_sorting'];
} else {
	$sf_3_sorting = 0;
}
// FREE EDITION: show-title is a PRO feature (locked in the original plugin) — fixed default.
$sf_3_show_title       = 'true';

// Normalize height: if percentage/auto, fallback to 500px; if numeric, append 'px'
$sf_3_height_css = trim( $sf_3_height );
if ( empty( $sf_3_height_css ) || strpos( $sf_3_height_css, '%' ) !== false || $sf_3_height_css === 'auto' ) {
	$sf_3_height_css = '500px';
} elseif ( is_numeric( $sf_3_height_css ) ) {
	$sf_3_height_css .= 'px';
}

// CSS and JS.
wp_enqueue_script( 'jquery' );
wp_enqueue_style( 'fontawesome-css' );
wp_enqueue_script( 'sf-3-accordion-carousel-blue-slider-js' );
?>
<script>
jQuery( document ).ready(function() {
	jQuery('.first-sample-<?php echo esc_attr( $sf_slider_id ); ?> .slider').blue_slider({
		// Preset 4 for Free edition
		slide_template: '1fr 6fr (2,1fr) .5fr',
		current_fr_index: 2,
		start_slide_index: 0, 
		current_fr_index_flow: false,

		auto_play: <?php echo esc_attr( $sf_3_auto_play ); ?>,						// to auto play slides (default: false)
		auto_play_period: <?php echo esc_attr( $sf_3_auto_play_speed ); ?>,			// auto play speed time (default: 3000) 3sec
		speed: <?php echo esc_attr( $sf_3_transition_speed ); ?>,					// slide transition time (default: 1000) 1 Sec

		slide_gap: <?php echo esc_attr( $sf_3_slide_gap ); ?>,						// gap between 2 slides (default: 0)

		current_fr_class: 'my-fr-current',								// highlighting the current frame
		active_fr_class: 'my-fr-active',
		sencitive_drag: 100,
		loop: false,
		arrows: true,
		prev_arrow: '.first-sample-<?php echo esc_attr( $sf_slider_id ); ?> .prev-slide',
		next_arrow: '.first-sample-<?php echo esc_attr( $sf_slider_id ); ?> .next-slide',
	});
});
</script>

<!-- slider start-->
<div class="sf-3-<?php echo esc_attr( $sf_slider_id ); ?>">
	<div class="first-sample-<?php echo esc_attr( $sf_slider_id ); ?>">
		<div class="slider">
		<?php
		$sf_3_slide_ids = array();
		if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
			$sf_3_slide_ids = $slider['sf_slide_id'];
		} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			$sf_3_slide_ids = array_keys( $slider['sf_slide_title'] );
		}

		if ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			if ( $sf_3_sorting == 1 ) {
				ksort( $slider['sf_slide_title'] );
				$sf_3_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_3_sorting == 2 ) {
				krsort( $slider['sf_slide_title'] );
				$sf_3_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_3_sorting == 3 ) {
				shuffle( $sf_3_slide_ids );
			} elseif ( $sf_3_sorting == 4 ) {
				asort( $slider['sf_slide_title'] );
				$sf_3_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_3_sorting == 5 ) {
				arsort( $slider['sf_slide_title'] );
				$sf_3_slide_ids = array_keys( $slider['sf_slide_title'] );
			}
		}

		if ( ! empty( $sf_3_slide_ids ) ) {
			foreach ( $sf_3_slide_ids as $id ) {
				$attachment_id  = $id;
				$attachment     = get_post( $attachment_id );

				$custom_title   = isset( $slider['sf_slide_title'][ $attachment_id ] ) ? trim( $slider['sf_slide_title'][ $attachment_id ] ) : '';
				$sf_slide_title = ( $custom_title !== '' ) ? $custom_title : ( $attachment ? get_the_title( $attachment_id ) : '' );

				$custom_desc    = isset( $slider['sf_slide_desc'][ $attachment_id ] ) ? trim( $slider['sf_slide_desc'][ $attachment_id ] ) : '';
				$sf_slide_descs = ( $custom_desc !== '' ) ? $custom_desc : ( $attachment ? $attachment->post_content : '' );

				$custom_alt     = isset( $slider['sf_slide_alt_text'][ $attachment_id ] ) ? trim( $slider['sf_slide_alt_text'][ $attachment_id ] ) : '';
				$sf_slide_alt   = ( $custom_alt !== '' ) ? $custom_alt : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

				$sf_slide_full_url = wp_get_attachment_image_src( $attachment_id, 'full', true );
				if ( ! is_array( $sf_slide_full_url ) ) { continue; }
				?>
				<div class="item">
					<img class="sf-3-slide-image" src="<?php echo esc_url( $sf_slide_full_url[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_alt ); ?>" loading="lazy" decoding="async">
					<?php if ( $sf_3_show_title !== 'false' && ( $sf_slide_title !== '' || $sf_slide_descs !== '' ) ) { ?>
					<div class="sf-3-slide-content">
						<?php if ( $sf_slide_title !== '' ) { ?>
							<div class="sf-3-slide-title"><?php echo esc_html( $sf_slide_title ); ?></div>
						<?php } ?>
						<?php if ( $sf_slide_descs !== '' ) { ?>
							<div class="sf-3-slide-desc"><?php echo esc_html( $sf_slide_descs ); ?></div>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
				<?php
			}
		}
		?>

		</div>
		<?php if ( $sf_3_navigation_arrow == 'true' ) { ?>
		<i class="prev-slide "><i class="fa fa-chevron-left"></i></i>
		<i class="next-slide "><i class="fa fa-chevron-right"></i></i>
		<?php } ?>

	</div>
</div>
<!-- slider end-->

<style>
.sf-3-<?php echo esc_attr( $sf_slider_id ); ?> {
	margin-left: auto;
	margin-right: auto;
	width: <?php echo esc_attr( $sf_3_width ); ?>;
	height: <?php echo esc_html( $sf_3_height_css ); ?>;
}

.sf-3-<?php echo esc_attr( $sf_slider_id ); ?> .sf-3-slide-content {
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	background: linear-gradient(to top, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.4) 70%, transparent 100%);
	color: #ffffff;
	padding: 16px;
	z-index: 5;
	pointer-events: none;
	box-sizing: border-box;
}

.sf-3-<?php echo esc_attr( $sf_slider_id ); ?> .sf-3-slide-title {
	font-size: 14px;
	font-weight: 700;
	color: #ffffff;
	line-height: 1.3;
	text-shadow: 0 1px 3px rgba(0,0,0,0.5);
	margin-bottom: 22px;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

.sf-3-<?php echo esc_attr( $sf_slider_id ); ?> .sf-3-slide-desc {
	font-size: 12px;
	color: rgba(255,255,255,0.85);
	line-height: 1.4;
	text-shadow: 0 1px 2px rgba(0,0,0,0.5);
	position: absolute;
	bottom: 14px;
	left: 16px;
	right: 16px;
	opacity: 0;
	visibility: hidden;
	pointer-events: none;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	transition: opacity 0.15s ease 0s, visibility 0s 0.15s;
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider .slide-item.my-fr-current .sf-3-slide-desc {
	opacity: 1;
	visibility: visible;
	pointer-events: auto;
	transition: opacity 0.5s ease 2.2s, visibility 0s 2.2s;
}

/* main CSS start */
.first-sample-<?php echo esc_html( $sf_slider_id ); ?> {
	width: 100%;				/* width of slider */
	height: 100%;				/* height of slider */
	border: none;
	border-radius: 0;
	position: relative;
	background-color: #1c1c1c;	/* background color of slider */
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider {
	height: 100%;
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider .slide-wrapper {
	overflow: hidden;
	height: 100%;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider .slide-tracker {
	height: 100%;
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider .slide-item {
	height: 100%;
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider .slide-tracker .my-fr-current .item::before {
	background-color: rgba(0, 0, 0, 0);
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider .item {
	width: 100%;
	height: 100%;
	overflow: hidden;
	position: relative;
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider .item::before {
	content: '';
	position: absolute;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.8);
	transition: background-color 1s;
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .slider .item img {
	width: 100%;
	height: 100%;
	object-fit: cover;				/* image fit or Zoom 100% */
	object-position: center center;
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .prev-slide,
.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .next-slide {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	width: 44px;
	height: 44px;
	background: rgba(0, 0, 0, 0.45);
	color: #ffffff;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 14px;
	cursor: pointer;
	transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	z-index: 10;
	border: 1px solid rgba(255, 255, 255, 0.15);
	backdrop-filter: blur(4px);
	-webkit-backdrop-filter: blur(4px);
}

.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .prev-slide:hover,
.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .next-slide:hover {
	background: rgba(0, 0, 0, 0.75);
	color: #ffffff;
	transform: translateY(-50%) scale(1.08);
	box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
}

/* position of previous button from slider container (default -70 px) */
.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .prev-slide {
	left: 20px;
}

/* position of next button from slider container (default -70 px) */
.first-sample-<?php echo esc_html( $sf_slider_id ); ?> .next-slide {
	right: 20px;
}
/* main CSS end */

/* media query CSS start */
@media only screen and (max-width: 600px) {
	.sf-3-<?php echo esc_html( $sf_slider_id ); ?> {
		height: calc(<?php echo esc_html( $sf_3_height_css ); ?> / 2);
	}
}
/* media query CSS end */
</style>
