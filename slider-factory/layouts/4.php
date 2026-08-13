<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// slider settings
if ( isset( $slider['sf_4_width'] ) ) {
	$sf_4_width = $slider['sf_4_width'];
} else {
	$sf_4_width = '100%';
}
if ( isset( $slider['sf_4_height'] ) ) {
	$sf_4_height = $slider['sf_4_height'];
} else {
	$sf_4_height = '100%';
}

// Normalize width & height units
if ( is_numeric( $sf_4_width ) ) {
	$sf_4_width = $sf_4_width . 'px';
}
if ( is_numeric( $sf_4_height ) ) {
	$sf_4_height = $sf_4_height . 'px';
}

if ( isset( $slider['sf_4_auto_play'] ) ) {
	$sf_4_auto_play = $slider['sf_4_auto_play'];
} else {
	$sf_4_auto_play = 'true';
}
if ( isset( $slider['sf_4_sorting'] ) ) {
	$sf_4_sorting = $slider['sf_4_sorting'];
} else {
	$sf_4_sorting = 0;
}

// CSS and JS
wp_enqueue_script( 'jquery' );
wp_enqueue_style( 'sf-4-camera-css' ); // v1.0.0
wp_enqueue_script( 'jquery-effects-core' ); // v1.0.0
wp_enqueue_script( 'sf-4-camera-js' ); // v1.0.0
?>
<script>
jQuery( document ).ready(function() {
	// Avoid `console` errors in browsers that lack a console.
	(function () {
		var method;
		var noop = function () {};
		var methods = [
			'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
			'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
			'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
			'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
		];
		var length = methods.length;
		var console = (window.console = window.console || {});

		while (length--) {
			method = methods[length];

			// Only stub undefined methods.
			if (!console[method]) {
				console[method] = noop;
			}
		}
	}());

	jQuery('.sf-4-<?php echo esc_js( $sf_slider_id ); ?>').camera({
		autoAdvance:<?php echo esc_js( $sf_4_auto_play ); ?>,
		portrait: false,
		height: '<?php echo esc_js( $sf_4_height ); ?>',
	});
});
</script>

<!-- slider start-->
 <div class="sf-4-<?php echo esc_attr( $sf_slider_id ); ?>">
	<?php
	$sf_4_slide_ids = array();
	if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
		$sf_4_slide_ids = $slider['sf_slide_id'];
	} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
		$sf_4_slide_ids = array_keys( $slider['sf_slide_title'] );
	}

	if ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
		if ( $sf_4_sorting == 1 ) {
			ksort( $slider['sf_slide_title'] );
			$sf_4_slide_ids = array_keys( $slider['sf_slide_title'] );
		} elseif ( $sf_4_sorting == 2 ) {
			krsort( $slider['sf_slide_title'] );
			$sf_4_slide_ids = array_keys( $slider['sf_slide_title'] );
		} elseif ( $sf_4_sorting == 3 ) {
			shuffle( $sf_4_slide_ids );
		} elseif ( $sf_4_sorting == 4 ) {
			asort( $slider['sf_slide_title'] );
			$sf_4_slide_ids = array_keys( $slider['sf_slide_title'] );
		} elseif ( $sf_4_sorting == 5 ) {
			arsort( $slider['sf_slide_title'] );
			$sf_4_slide_ids = array_keys( $slider['sf_slide_title'] );
		}
	}

	if ( ! empty( $sf_4_slide_ids ) ) {
		foreach ( $sf_4_slide_ids as $sf_id_1 ) {
			$attachment_id  = $sf_id_1;
			$sf_slide_title = isset( $slider['sf_slide_title'][ $attachment_id ] ) && $slider['sf_slide_title'][ $attachment_id ] !== '' ? $slider['sf_slide_title'][ $attachment_id ] : get_the_title( $attachment_id );
			$sf_slide_alt   = isset( $slider['sf_slide_alt_text'][ $attachment_id ] ) && $slider['sf_slide_alt_text'][ $attachment_id ] !== '' ? $slider['sf_slide_alt_text'][ $attachment_id ] : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$sf_slide_full_url = wp_get_attachment_image_src( $attachment_id, 'full', true );
			if ( ! is_array( $sf_slide_full_url ) ) { continue; }
			$attachment = get_post( $attachment_id );
			$sf_slide_descs = isset( $slider['sf_slide_desc'][ $attachment_id ] ) && $slider['sf_slide_desc'][ $attachment_id ] !== '' ? $slider['sf_slide_desc'][ $attachment_id ] : ( $attachment ? $attachment->post_content : '' );
			
			if ( isset( $slider['sf_slide_link_text_1'][ $attachment_id ] ) ) {
				$sf_slide_link_text_1 = $slider['sf_slide_link_text_1'][ $attachment_id ];
			} else {
				$sf_slide_link_text_1 = '';
			}
			if ( isset( $slider['sf_slide_link_1'][ $attachment_id ] ) ) {
				$sf_slide_link_1 = $slider['sf_slide_link_1'][ $attachment_id ];
			} else {
				$sf_slide_link_1 = '';
			}
			?>
				<div data-src="<?php echo esc_url( $sf_slide_full_url[0] ); ?>">
					<img class="sf-4-slide-image" src="<?php echo esc_url( $sf_slide_full_url[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_alt ); ?>" loading="lazy" decoding="async">
					
				<?php if ( $sf_slide_title != '' || $sf_slide_descs != '' || ( $sf_slide_link_1 != '' && $sf_slide_link_text_1 != '' ) ) { ?>
					<div class="camera_caption sf-4-slide-content">
						<?php if ( $sf_slide_title != '' ) { ?>
						<p class="sf-4-slide-title"><?php echo esc_html( $sf_slide_title ); ?></p>
						<?php } ?>
						<?php if ( $sf_slide_descs != '' ) { ?>
						<p class="sf-4-slide-desc"><?php echo esc_html( $sf_slide_descs ); ?></p>
						<?php } ?>
						<?php if ( $sf_slide_link_1 != '' && $sf_slide_link_text_1 != '' ) { ?>
						<a class="sf-4-slide-button-link-1" href="<?php echo esc_url( $sf_slide_link_1 ); ?>" target="_blank">
							<button type="button" class="sf-4-slide-button-1">
							<?php echo esc_html( $sf_slide_link_text_1 ); ?>
							</button>
						</a>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
				<?php
		}//end of for each
	} //end of count
	?>
</div>
<!-- slider end-->

<style>
.sf-4-<?php echo esc_html( $sf_slider_id ); ?> {
	float: none !important;
	margin-left: auto !important;
	margin-right: auto !important;
	width: <?php echo esc_html( $sf_4_width ); ?>;
	height: <?php echo esc_html( $sf_4_height ); ?>;
}

.sf-4-<?php echo esc_html( $sf_slider_id ); ?> .camera_prev,
.sf-4-<?php echo esc_html( $sf_slider_id ); ?> .camera_next,
.sf-4-<?php echo esc_html( $sf_slider_id ); ?> .camera_commands {
	top: 50%;
	bottom: auto;
	transform: translateY(-50%);
	margin-top: 0;
}

.sf-4-<?php echo esc_html( $sf_slider_id ); ?> .camera_commands > .camera_play {
	display: block;
}

.sf-4-slide-content {

}

.sf-4-slide-image {
	width: 100%;
}

.sf-4-slide-title {
	color: #ffffff;
	font-size: 20px;
	font-weight: 700;
	margin: 0 0 6px 0;
	line-height: 1.3;
}
.sf-4-slide-desc {
	color: rgba(255, 255, 255, 0.9);
	font-size: 14px;
	font-weight: 400;
	margin: 0 0 12px 0;
	line-height: 1.5;
}

.sf-4-slide-button-1,
.sf-4-slide-button-2 {
	display: inline-block;
	padding: 8px 18px;
	font-size: 13px;
	font-weight: 600;
	line-height: 1.4;
	color: #ffffff;
	background-color: #2563eb;
	border: none;
	border-radius: 4px;
	cursor: pointer;
	transition: background-color 0.2s ease;
	margin-top: 4px;
}
.sf-4-slide-button-1:hover,
.sf-4-slide-button-2:hover {
	background-color: #1d4ed8;
}
.sf-4-slide-button-link-1,
.sf-4-slide-button-link-2 {
	text-decoration: none;
	display: inline-block;
}

/********* hide slide content on mobile with media query 26-Jan-2021 *********/
@media(max-width:770px){
	.camera_caption > div {
		padding: 5px 10px!important;
	}
	
	.camera_caption p {
		margin: 0 0 5px;
	}
	
	.sf-4-slide-title {
		color: #FFF;
		font-size: 17px;
	}
	
	.sf-4-slide-desc {
		color: #FFF;
		font-size: 13px;
	}
}
@media(max-width:580px){
	.sf-4-slide-desc {
		display: none;
	}
	.sf-4-slide-title {
		color: #FFF;
		font-size: 12px;
		text-align: center;
	}
}
</style>
