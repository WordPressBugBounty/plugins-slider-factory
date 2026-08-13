<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// slider settings
if ( isset( $slider['sf_8_width'] ) ) {
	$sf_8_width = $slider['sf_8_width'];
} else {
	$sf_8_width = '100%';
}
if ( isset( $slider['sf_8_height'] ) && ! empty( $slider['sf_8_height'] ) ) {
	$sf_8_height = trim( $slider['sf_8_height'] );
	if ( is_numeric( $sf_8_height ) ) {
		$sf_8_height .= 'px';
	}
} else {
	$sf_8_height = '300px';
}
if ( isset( $slider['sf_8_responsive'] ) ) {
	$sf_8_responsive = $slider['sf_8_responsive'];
} else {
	$sf_8_responsive = 'true';
}
if ( isset( $slider['sf_8_sorting'] ) ) {
	$sf_8_sorting = $slider['sf_8_sorting'];
} else {
	$sf_8_sorting = 0;
}

// CSS and JS
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'sf-8-infinite-slider-js' );
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(function(){
		jQuery('.sf-8-<?php echo esc_js( $sf_slider_id ); ?>').infiniteslide({
			'responsive': <?php echo esc_js( $sf_8_responsive ); ?>
		});
	});
});
</script>
<div class="infiniteslide sf-8-<?php echo esc_attr( $sf_slider_id ); ?>">
		<?php
		$sf_8_slide_ids = array();
		if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
			$sf_8_slide_ids = $slider['sf_slide_id'];
		} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			$sf_8_slide_ids = array_keys( $slider['sf_slide_title'] );
		}

		if ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			if ( $sf_8_sorting == 1 ) {
				ksort( $slider['sf_slide_title'] );
				$sf_8_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_8_sorting == 2 ) {
				krsort( $slider['sf_slide_title'] );
				$sf_8_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_8_sorting == 3 ) {
				shuffle( $sf_8_slide_ids );
			} elseif ( $sf_8_sorting == 4 ) {
				asort( $slider['sf_slide_title'] );
				$sf_8_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_8_sorting == 5 ) {
				arsort( $slider['sf_slide_title'] );
				$sf_8_slide_ids = array_keys( $slider['sf_slide_title'] );
			}
		}

		if ( ! empty( $sf_8_slide_ids ) ) {
			foreach ( $sf_8_slide_ids as $sf_id_1 ) {
				$attachment_id  = $sf_id_1;
				$attachment     = get_post( $attachment_id );
				$custom_title   = isset( $slider['sf_slide_title'][ $attachment_id ] ) ? trim( $slider['sf_slide_title'][ $attachment_id ] ) : '';
				$sf_slide_title = ( $custom_title !== '' ) ? $custom_title : ( $attachment ? get_the_title( $attachment_id ) : '' );
				$custom_alt     = isset( $slider['sf_slide_alt_text'][ $attachment_id ] ) ? trim( $slider['sf_slide_alt_text'][ $attachment_id ] ) : '';
				$sf_slide_alt   = ( $custom_alt !== '' ) ? $custom_alt : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$sf_slide_thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'large', true );
				$sf_slide_full_url      = wp_get_attachment_image_src( $attachment_id, 'full', true );
				if ( ! is_array( $sf_slide_thumbnail_url ) ) { continue; }
				$custom_desc    = isset( $slider['sf_slide_desc'][ $attachment_id ] ) ? trim( $slider['sf_slide_desc'][ $attachment_id ] ) : '';
				$sf_slide_descs = ( $custom_desc !== '' ) ? $custom_desc : ( $attachment ? $attachment->post_content : '' );
				?>
				<div class="scroll_item sf_8_margin-<?php echo esc_attr( $sf_slider_id ); ?>">
					<?php if ( $sf_slide_title || $sf_slide_descs ) { ?>
					<div class="sf-8-content descdiv">
						<?php if ( $sf_slide_title != '' ) { ?>
						<div class="sf-8-title">
							<?php echo esc_html( $sf_slide_title ); ?>
						</div>
						<?php } ?>
						
						<?php if ( $sf_slide_descs != '' ) { ?>
						<div class="sf-8-desc">
							<?php echo esc_html( $sf_slide_descs ); ?>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
					<div>
						<img class="sf_8_img-<?php echo esc_attr( $sf_slider_id ); ?>" src="<?php echo esc_url( $sf_slide_thumbnail_url[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_alt ); ?>" loading="lazy" decoding="async">
					</div>
				</div>
				<?php
			}//end of for each
		} //end of count
		?>
</div>
<style>
.sf-8-<?php echo esc_html( $sf_slider_id ); ?> {
	max-width: 100%;
	margin-left: auto;
	margin-right: auto;
}

.sf-8-<?php echo esc_html( $sf_slider_id ); ?> div.infiniteslide_wrap {
	width: <?php echo esc_html( $sf_8_width ); ?>;
	max-width: 100%;
	margin-left: auto;
	margin-right: auto;
	overflow: hidden;
}

.sf_8_margin-<?php echo esc_html( $sf_slider_id ); ?> {
	margin-right: 1%;
}

.sf-8-<?php echo esc_html( $sf_slider_id ); ?> .scroll_item {
	position: relative;
	overflow: hidden;
	height: <?php echo esc_html( $sf_8_height ); ?>;
}

.sf-8-<?php echo esc_html( $sf_slider_id ); ?> .descdiv {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	width: 100%;
	max-height: 55%;
	overflow: hidden;
	opacity: 0;
	color: #fff;
	padding: 10px 14px;
	background: rgba(0,0,0,0.65);
	transition: opacity 0.4s ease;
	box-sizing: border-box;
	pointer-events: none;
	word-wrap: break-word;
	overflow-wrap: break-word;
}

.sf-8-<?php echo esc_html( $sf_slider_id ); ?> .scroll_item:hover .descdiv {
	opacity: 1;
}

.sf-8-<?php echo esc_html( $sf_slider_id ); ?> .descdiv .sf-8-title {
	font-size: 15px;
	font-weight: 600;
	line-height: 1.3;
	margin-bottom: 2px;
}

.sf-8-<?php echo esc_html( $sf_slider_id ); ?> .descdiv .sf-8-desc {
	margin-top: 4px;
	margin-bottom: 4px;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
}

.sf-8-<?php echo esc_html( $sf_slider_id ); ?> .scroll_item > div:last-child {
	height: <?php echo esc_html( $sf_8_height ); ?>;
}

.sf_8_img-<?php echo esc_html( $sf_slider_id ); ?> {
	display: block;
	height: <?php echo esc_html( $sf_8_height ); ?>;
	width: auto;
	max-width: none;
	object-fit: cover;
}
</style>
