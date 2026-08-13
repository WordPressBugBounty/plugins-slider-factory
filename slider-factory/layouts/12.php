<?php // slider layout 12

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// slider settings
if ( isset( $slider['sf_12_width'] ) && ! empty( $slider['sf_12_width'] ) ) {
	$sf_12_width = $slider['sf_12_width'];
	if ( is_numeric( $sf_12_width ) ) {
		$sf_12_width .= 'px';
	}
} else {
	$sf_12_width = '100%';
}
if ( isset( $slider['sf_12_height'] ) && ! empty( $slider['sf_12_height'] ) ) {
	$sf_12_height = $slider['sf_12_height'];
	if ( is_numeric( $sf_12_height ) ) {
		$sf_12_height .= 'px';
	}
} else {
	$sf_12_height = '600px';
}

// CSS and JS
wp_enqueue_style( 'sf-12-twentytwenty-css' );
wp_enqueue_script( 'sf-12-jquery-twentytwenty-js' );
wp_enqueue_script( 'sf-12-jquery-event-move-js' );
wp_enqueue_script( 'sf-12-jquery-images-loaded-js' );
?>
<style>
.sf-12-main-<?php echo esc_attr( $sf_slider_id ); ?>{
	margin-left: auto;
	margin-right: auto;
	width: <?php echo esc_attr( $sf_12_width ); ?>;
	<?php if ( $sf_12_height != '' && $sf_12_height != 'auto' ) { ?>
	height: <?php echo esc_attr( $sf_12_height ); ?>;
	<?php } else { ?>
	height: auto;
	<?php } ?>
	overflow: hidden;
}

.sf-12-main-<?php echo esc_attr( $sf_slider_id ); ?> .twentytwenty-container {
	width: 100%;
	<?php if ( $sf_12_height != '' && $sf_12_height != 'auto' ) { ?>
	height: <?php echo esc_attr( $sf_12_height ); ?>;
	<?php } ?>
}

.sf-12-main-<?php echo esc_attr( $sf_slider_id ); ?> img {
	width : 100%;
	<?php if ( $sf_12_height == '' || $sf_12_height == 'auto' ) { ?>
	height : auto;
	<?php } else { ?>
	height : <?php echo esc_attr( $sf_12_height ); ?>;  /* height in px or auto if blank */
	object-fit: cover;
	<?php } ?>
}
	
.sf-12-main-<?php echo esc_attr( $sf_slider_id ); ?> .twentytwenty-overlay:hover {
	background: rgba(0, 0, 0, 0); 
}
	
.twentytwenty-before-label, .twentytwenty-after-label, .twentytwenty-overlay {
	position: absolute;
	top: 0;
	width: 100%;
	height: 100%;
}
</style>

<div class="sf-12-main-<?php echo esc_attr( $sf_slider_id ); ?>">
	<div class="sf-12-container-<?php echo esc_attr( $sf_slider_id ); ?> twentytwenty-container">
		<?php
		$sf_12_slide_ids = array();
		if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
			$sf_12_slide_ids = array_values( $slider['sf_slide_id'] );
		} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
			$sf_12_slide_ids = array_keys( $slider['sf_slide_title'] );
		}
		$sf_12_slide_ids = array_slice( $sf_12_slide_ids, 0, 2 );

		if ( ! empty( $sf_12_slide_ids ) ) {
			foreach ( $sf_12_slide_ids as $sf_id_1 ) {
				$attachment_id  = $sf_id_1;
				$sf_slide_title = get_the_title( $attachment_id );
				$sf_slide_alt   = isset( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) && trim( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) !== '' ? trim( $slider['sf_slide_alt_text'][ $sf_id_1 ] ) : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$sf_slide_thumbnail_url = wp_get_attachment_image_src( $attachment_id, 'large', true );
				$sf_slide_full_url      = wp_get_attachment_image_src( $attachment_id, 'full', true );
				if ( ! is_array( $sf_slide_full_url ) ) { continue; }
				$attachment             = get_post( $attachment_id );
				$sf_slide_descs         = ( $attachment ? $attachment->post_content : '' );
				?>
					<!-- The before image is first in first loop -->
					<img alt="<?php echo esc_attr( $sf_slide_alt ); ?>" src="<?php echo esc_url( $sf_slide_full_url[0] ); ?>" loading="lazy" decoding="async" />
					<!-- The after image is last in second loop -->
				<?php
			}
		}
		?>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {	
	jQuery(".sf-12-container-<?php echo esc_js( $sf_slider_id ); ?>").imagesLoaded().done( function() {
		jQuery(".sf-12-container-<?php echo esc_js( $sf_slider_id ); ?>").twentytwenty();
		if (typeof sendHeight === 'function') {
			sendHeight();
		}
	});
});
</script>
