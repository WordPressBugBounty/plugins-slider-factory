<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// slider settings
if ( isset( $slider['sf_7_width'] ) ) {
	$sf_7_width = $slider['sf_7_width'];
} else {
	$sf_7_width = '100%';
}
if ( isset( $slider['sf_7_height'] ) && ! empty( trim( $slider['sf_7_height'] ) ) && trim( $slider['sf_7_height'] ) !== '100%' ) {
	$sf_7_height = trim( $slider['sf_7_height'] );
} else {
	$sf_7_height = '600px';
}
if ( isset( $slider['sf_7_slide_circle_size'] ) && ! empty( $slider['sf_7_slide_circle_size'] ) ) {
	$sf_7_slide_circle_size = $slider['sf_7_slide_circle_size'];
} else {
	$sf_7_slide_circle_size = 340;
}
if ( isset( $slider['sf_7_inner_circle_size'] ) && ! empty( $slider['sf_7_inner_circle_size'] ) ) {
	$sf_7_inner_circle_size = $slider['sf_7_inner_circle_size'];
} else {
	$sf_7_inner_circle_size = 240;
}
if ( isset( $slider['sf_7_auto_play'] ) ) {
	$sf_7_auto_play = $slider['sf_7_auto_play'];
} else {
	$sf_7_auto_play = 'true';
}
if ( isset( $slider['sf_7_sorting'] ) ) {
	$sf_7_sorting = $slider['sf_7_sorting'];
} else {
	$sf_7_sorting = 0;
}

// Resolve percentage/numeric height to fixed pixels for half circle clipping.
$sf_7_container_height = $sf_7_height;
if ( strpos( (string) $sf_7_container_height, '%' ) !== false || is_numeric( $sf_7_container_height ) ) {
	$sf_7_container_height = (int) $sf_7_slide_circle_size . 'px';
}

// CSS and JS
wp_enqueue_style( 'sf-7-rotating-slider-css' );
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'sf-7-jquery.rotating-slider-js' );
?>
<style>
.sf-7-rotating-slider-container-<?php echo esc_attr( $sf_slider_id ); ?> *,
.sf-7-rotating-slider-container-<?php echo esc_attr( $sf_slider_id ); ?> *::before,
.sf-7-rotating-slider-container-<?php echo esc_attr( $sf_slider_id ); ?> *::after {
	box-sizing: content-box;
}

div.sf-7-rotating-slider-container-<?php echo esc_attr( $sf_slider_id ); ?> {
	margin-left: auto;
	margin-right: auto;
	font-family: 'Roboto Slab', sans-serif;
	font-weight: 300;
	overflow: hidden;
	width: <?php echo esc_html( $sf_7_width ); ?>;
	height: <?php echo esc_html( $sf_7_container_height ); ?>;
}

.sf-7-rotating-slider ul.slides li {
	background-position: center;
	background-repeat: no-repeat;
	background-size: cover;
}

.sf-7-rotating-slider ul.slides li .inner {
	box-sizing: border-box;
	padding-left: 2em;
	padding-right: 2em;
	height: 100%;
	width: 100%;
	word-wrap: break-word;
}

.sf-7-rotating-slider ul.slides li .inner .sf-7-title {
	font-size: 32px;
	color: #eeeeee;
	text-shadow: 1px 1px #616161;
}

.sf-7-rotating-slider ul.slides li .inner .sf-7-desc {
	font-size: 16px;
	color: #eeeeee;
	text-shadow: 1px 1px #616161;
	margin-bottom: 0; 
}

.sf-7-rotating-slider ul.slides li .inner .sf-7-button1 {
	background-color: #F1F1F1; 
	color: black;
	border: none; 
	cursor: pointer; 
	border-radius: 4px; 
	text-align: center; 
	padding: 6px 16px; 
	margin-top: 10px;
	font-size: 13px;
	font-weight: 600;
	transition: all .3s;
}

.sf-7-rotating-slider ul.slides li .inner .sf-7-button1:hover {
	background-color: #3E3D3D; 
	color: white; 
}
</style>

<!-- slider start-->
<div class="sf-7-rotating-slider-container-<?php echo esc_attr( $sf_slider_id ); ?>">
	<div class="sf-7-rotating-slider">
		<ul class="slides">
		<?php
		$sf_7_slide_ids = array();
		if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
			$sf_7_slide_ids = $slider['sf_slide_id'];
		} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			$sf_7_slide_ids = array_keys( $slider['sf_slide_title'] );
		}

		if ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			if ( $sf_7_sorting == 1 ) {
				ksort( $slider['sf_slide_title'] );
				$sf_7_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_7_sorting == 2 ) {
				krsort( $slider['sf_slide_title'] );
				$sf_7_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_7_sorting == 3 ) {
				shuffle( $sf_7_slide_ids );
			} elseif ( $sf_7_sorting == 4 ) {
				asort( $slider['sf_slide_title'] );
				$sf_7_slide_ids = array_keys( $slider['sf_slide_title'] );
			} elseif ( $sf_7_sorting == 5 ) {
				arsort( $slider['sf_slide_title'] );
				$sf_7_slide_ids = array_keys( $slider['sf_slide_title'] );
			}
		}

		if ( ! empty( $sf_7_slide_ids ) ) {
			foreach ( $sf_7_slide_ids as $sf_id_1 ) {
				$attachment_id  = $sf_id_1;
				$sf_slide_title = isset( $slider['sf_slide_title'][ $sf_id_1 ] ) && trim( $slider['sf_slide_title'][ $sf_id_1 ] ) !== '' ? trim( $slider['sf_slide_title'][ $sf_id_1 ] ) : get_the_title( $attachment_id );
				$sf_slide_alt   = isset( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) && trim( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) !== '' ? trim( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$sf_slide_thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'large', true );
				$sf_slide_full_url      = wp_get_attachment_image_src( $attachment_id, 'full', true );
				if ( ! is_array( $sf_slide_full_url ) ) { continue; }
				$attachment             = get_post( $attachment_id );
				$sf_slide_descs         = isset( $slider['sf_slide_desc'][ $sf_id_1 ] ) && trim( $slider['sf_slide_desc'][ $sf_id_1 ] ) !== '' ? trim( $slider['sf_slide_desc'][ $sf_id_1 ] ) : ( $attachment ? $attachment->post_content : '' );
				$sf_slide_link_text_1   = isset( $slider['sf_slide_link_text_1'][ $sf_id_1 ] ) ? $slider['sf_slide_link_text_1'][ $sf_id_1 ] : '';
				$sf_slide_link_1        = isset( $slider['sf_slide_link_1'][ $sf_id_1 ] ) ? $slider['sf_slide_link_1'][ $sf_id_1 ] : '';
				?>
					<li style="background-image: url('<?php echo esc_url( $sf_slide_full_url[0] ); ?>');" role="img" aria-label="<?php echo esc_attr( $sf_slide_alt ); ?>" title="<?php echo esc_attr( $sf_slide_alt ); ?>">
						<div class="inner">
							<div class="sf-7-title"><?php echo esc_html( $sf_slide_title ); ?></div>	<!-- Slide Title -->
							<div class="sf-7-desc"><?php echo esc_html( $sf_slide_descs ); ?></div>	<!-- Slide Desc -->

							<?php if ( $sf_slide_link_1 != '' && $sf_slide_link_text_1 != '' ) { ?>
							<a class="sf-7-slide-button-link-1" href="<?php echo esc_url( $sf_slide_link_1 ); ?>" target="_blank">
								<button type="button" class="sf-7-button1">
									<?php echo esc_html( $sf_slide_link_text_1 ); ?>
								</button>
							</a>
							<?php } ?>
						</div>
					</li>
				<?php
			}
		}
		?>
		</ul>
	</div>
</div>
<!-- slider end-->

<script>
jQuery(function(){ 
	jQuery('.sf-7-rotating-slider').rotatingSlider({
		autoRotate: <?php echo esc_js( $sf_7_auto_play ); ?>,
		autoRotateInterval: 3000,
		draggable: true,
		directionControls: true,
		directionLeftText: '&lsaquo;',
		directionRightText: '&rsaquo;',
		rotationSpeed: 1500,
		slideHeight: Math.min(<?php echo esc_js( $sf_7_slide_circle_size ); ?>, window.innerWidth - 80),
		slideWidth: Math.min(<?php echo esc_js( $sf_7_inner_circle_size ); ?>, window.innerWidth - 80)
	});
});
</script>
