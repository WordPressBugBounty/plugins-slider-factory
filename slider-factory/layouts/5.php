<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// slider settings
if ( isset( $slider['sf_5_width'] ) ) {
	$sf_5_width = $slider['sf_5_width'];
} else {
	$sf_5_width = '500px';
}
if ( isset( $slider['sf_5_height'] ) && ! empty( $slider['sf_5_height'] ) ) {
	$sf_5_height = $slider['sf_5_height'];
} else {
	$sf_5_height = '500px';
}
if ( isset( $slider['sf_5_auto_play'] ) ) {
	$sf_5_auto_play = $slider['sf_5_auto_play'];
} else {
	$sf_5_auto_play = 'false';
}
if ( isset( $slider['sf_5_sorting'] ) ) {
	$sf_5_sorting = $slider['sf_5_sorting'];
} else {
	$sf_5_sorting = 0;
}
// FREE EDITION: button color options are PRO features (locked in the original plugin) — fixed defaults.
$sf_5_button_bg_color = '#2563eb';
$sf_5_button_text_color = '#ffffff';

// CSS and JS
wp_enqueue_style( 'sf-5-cover-flow-flipster-slider-css' ); // v2.2.1
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'sf-5-cover-flow-flipster-slider-js' );
?>
<div class="slider-container-<?php echo esc_attr( $sf_slider_id ); ?>">
	<ul class="flip-items">
	<?php
	$sf_5_slide_ids = array();
	if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
		$sf_5_slide_ids = $slider['sf_slide_id'];
	} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
		$sf_5_slide_ids = array_keys( $slider['sf_slide_title'] );
	}

	if ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
		if ( $sf_5_sorting == 1 ) {
			ksort( $slider['sf_slide_title'] );
			$sf_5_slide_ids = array_keys( $slider['sf_slide_title'] );
		} elseif ( $sf_5_sorting == 2 ) {
			krsort( $slider['sf_slide_title'] );
			$sf_5_slide_ids = array_keys( $slider['sf_slide_title'] );
		} elseif ( $sf_5_sorting == 3 ) {
			shuffle( $sf_5_slide_ids );
		} elseif ( $sf_5_sorting == 4 ) {
			asort( $slider['sf_slide_title'] );
			$sf_5_slide_ids = array_keys( $slider['sf_slide_title'] );
		} elseif ( $sf_5_sorting == 5 ) {
			arsort( $slider['sf_slide_title'] );
			$sf_5_slide_ids = array_keys( $slider['sf_slide_title'] );
		}
	}

	if ( ! empty( $sf_5_slide_ids ) ) {
		foreach ( $sf_5_slide_ids as $sf_id_1 ) {
			$attachment_id  = $sf_id_1;
			$sf_slide_title = get_the_title( $attachment_id );
			$sf_slide_alt   = isset( $slider['sf_slide_alt_text'][ $attachment_id ] ) && trim( $slider['sf_slide_alt_text'][ $attachment_id ] ) !== '' ? trim( $slider['sf_slide_alt_text'][ $attachment_id ] ) : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			$sf_slide_thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'large', true );
			$sf_slide_full_url      = wp_get_attachment_image_src( $attachment_id, 'full', true );
			$attachment             = get_post( $attachment_id );
			$sf_slide_descs         = $attachment ? $attachment->post_content : '';
			
			$custom_title = isset( $slider['sf_slide_title'][ $attachment_id ] ) ? trim( $slider['sf_slide_title'][ $attachment_id ] ) : '';
			$sf_slide_title = ( $custom_title !== '' ) ? $custom_title : get_the_title( $attachment_id );
			$custom_desc = isset( $slider['sf_slide_desc'][ $attachment_id ] ) ? trim( $slider['sf_slide_desc'][ $attachment_id ] ) : '';
			$sf_slide_descs = ( $custom_desc !== '' ) ? $custom_desc : ( $attachment ? $attachment->post_content : '' );
			$sf_slide_link_text_1 = isset( $slider['sf_slide_link_text_1'][ $attachment_id ] ) ? trim( $slider['sf_slide_link_text_1'][ $attachment_id ] ) : '';
			$sf_slide_link_1 = isset( $slider['sf_slide_link_1'][ $attachment_id ] ) ? trim( $slider['sf_slide_link_1'][ $attachment_id ] ) : '';
			?>
			<li>
				<img src="<?php echo esc_url( $sf_slide_thumbnail_url[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_alt ); ?>" loading="lazy" decoding="async">
				<?php if ( ! empty( $sf_slide_title ) || ! empty( $sf_slide_descs ) || ( ! empty( $sf_slide_link_text_1 ) && ! empty( $sf_slide_link_1 ) ) ) { ?>
					<div class="sf-5-descdiv-<?php echo esc_attr( $sf_slider_id ); ?>">
						<?php if ( ! empty( $sf_slide_title ) ) { ?>
							<div class="sf-5-title-<?php echo esc_attr( $sf_slider_id ); ?>">
								<?php echo esc_html( $sf_slide_title ); ?>
							</div>
						<?php } ?>
						<?php if ( ! empty( $sf_slide_descs ) ) { ?>
							<div class="sf-5-desc-<?php echo esc_attr( $sf_slider_id ); ?>">
								<?php echo esc_html( $sf_slide_descs ); ?>
							</div>
						<?php } ?>
						<?php if ( ! empty( $sf_slide_link_text_1 ) && ! empty( $sf_slide_link_1 ) ) { ?>
							<a href="<?php echo esc_url( $sf_slide_link_1 ); ?>" target="_blank">
								<button type="button" class="sf-5-slide-button-1-<?php echo esc_attr( $sf_slider_id ); ?>">
									<?php echo esc_html( $sf_slide_link_text_1 ); ?>
								</button>
							</a>
						<?php } ?>
					</div>
				<?php } ?>
			</li>
			<?php
		}//end of for each
	} //end of count
	?>
	</ul>
</div>
<script>
jQuery( document ).ready(function() {
	jQuery(".slider-container-<?php echo esc_js( $sf_slider_id ); ?>").flipster({
		<?php if ( $sf_5_auto_play == 'true' ) { ?>
		autoplay: 2000,		// true/false - numbers in milliseconds 
		<?php } ?>
	});
});
</script>
<style>
.slider-container-<?php echo esc_html( $sf_slider_id ); ?> img {
	width : <?php echo esc_html( $sf_5_width ); ?>;
	max-width: 100%;
	height : <?php echo esc_html( $sf_5_height ); ?>;
	object-fit : cover;
}

/********* FIX FOR VERTICAL SCROLL BAR AND PREVIEW PADDING *********/
.flipster {
	display: block;
	overflow: hidden !important;
	position: relative;
	padding: 35px 0 25px 0 !important;
}

.sf-5-descdiv-<?php echo esc_attr( $sf_slider_id ); ?> {
	text-align: center;
	position: absolute;
	opacity: 0;
	bottom: 10%;
	color: #fff;
	background: rgba(0,0,0,0.65);
	width: 70%;
	left: 15%;
	padding: 8px 12px;
	border-radius: 6px;
	transition: all 0.4s ease;
	box-sizing: border-box;
	z-index: 10;
}
.sf-5-descdiv-<?php echo esc_attr( $sf_slider_id ); ?> .sf-5-title-<?php echo esc_attr( $sf_slider_id ); ?> {
	font-weight: 700;
	font-size: 15px;
	margin-bottom: 4px;
}
.sf-5-descdiv-<?php echo esc_attr( $sf_slider_id ); ?> .sf-5-desc-<?php echo esc_attr( $sf_slider_id ); ?> {
	font-size: 13px;
	opacity: 0.9;
}
.flipster__item--current .sf-5-descdiv-<?php echo esc_attr( $sf_slider_id ); ?>,
.flipster__item__content:hover .sf-5-descdiv-<?php echo esc_attr( $sf_slider_id ); ?> {
	opacity: 1;
}

.sf-5-slide-button-1-<?php echo esc_attr( $sf_slider_id ); ?>,
.sf-5-slide-button-2-<?php echo esc_attr( $sf_slider_id ); ?> {
	display: inline-block;
	min-width: 80px;
	margin: 6px 4px 0 4px;
	border-radius: 4px;
	border: none;
	color: <?php echo esc_attr( $sf_5_button_text_color ); ?>;
	background-color: <?php echo esc_attr( $sf_5_button_bg_color ); ?>;
	font-weight: 600;
	font-size: 12px;
	padding: 6px 14px;
	cursor: pointer;
	transition: background-color 0.2s ease, transform 0.2s ease;
	text-decoration: none;
}

.sf-5-slide-button-1-<?php echo esc_attr( $sf_slider_id ); ?>:hover,
.sf-5-slide-button-2-<?php echo esc_attr( $sf_slider_id ); ?>:hover {
	opacity: 0.9;
	color: <?php echo esc_attr( $sf_5_button_text_color ); ?>;
	transform: translateY(-1px);
}

/********* MODERNIZED FLIPSTER NAVIGATION & SPACING *********/
.slider-container-<?php echo esc_attr( $sf_slider_id ); ?> .flipster__nav {
	margin-top: 28px !important;
	margin-bottom: 20px !important;
	display: flex !important;
	flex-wrap: wrap !important;
	justify-content: center !important;
	gap: 8px !important;
	position: relative !important;
	z-index: 10 !important;
}
.slider-container-<?php echo esc_attr( $sf_slider_id ); ?> .flipster__nav__item {
	display: inline-block !important;
	margin: 0 !important;
}
.slider-container-<?php echo esc_attr( $sf_slider_id ); ?> .flipster__nav__link {
	display: inline-flex !important;
	align-items: center !important;
	justify-content: center !important;
	background: #f1f5f9 !important;
	color: #475569 !important;
	font-size: 13px !important;
	font-weight: 600 !important;
	border-radius: 4px !important;
	padding: 7px 18px !important;
	border: 1px solid #e2e8f0 !important;
	transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
	text-decoration: none !important;
	line-height: 1.2 !important;
}
.slider-container-<?php echo esc_attr( $sf_slider_id ); ?> .flipster__nav__link::after {
	display: none !important;
}
.slider-container-<?php echo esc_attr( $sf_slider_id ); ?> .flipster__nav__item--current > .flipster__nav__link,
.slider-container-<?php echo esc_attr( $sf_slider_id ); ?> .flipster__nav__link:hover {
	background: <?php echo esc_attr( $sf_5_button_bg_color ); ?> !important;
	color: <?php echo esc_attr( $sf_5_button_text_color ); ?> !important;
	border-color: <?php echo esc_attr( $sf_5_button_bg_color ); ?> !important;
	box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
	transform: translateY(-1px) !important;
}
.slider-container-<?php echo esc_attr( $sf_slider_id ); ?> .flipster__nav__child {
	display: none !important;
}

/********* Responsive media query *********/
@media only screen and (max-width: 768px) {
	.slider-container-<?php echo esc_html( $sf_slider_id ); ?> img {
		max-width : 300px;
	}
}
@media only screen and (max-width: 540px) {
	.slider-container-<?php echo esc_html( $sf_slider_id ); ?> img {
		max-width : 170px;
	}
}
</style>
