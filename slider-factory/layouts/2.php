<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// 1. Dimensions & Option Handling
$sf_2_width = ( isset( $slider['sf_2_width'] ) && ! empty( $slider['sf_2_width'] ) ) ? trim( $slider['sf_2_width'] ) : '100%';
if ( is_numeric( $sf_2_width ) ) {
	$sf_2_width .= 'px';
}

$sf_2_height = ( isset( $slider['sf_2_height'] ) && ! empty( $slider['sf_2_height'] ) ) ? trim( $slider['sf_2_height'] ) : 'auto';
if ( is_numeric( $sf_2_height ) ) {
	$sf_2_height .= 'px';
}

// FREE EDITION: startpoint, jump-back, jumppoint-click and show-title are PRO features
// (locked in the original plugin) — fixed defaults. Only sorting is free.
$sf_2_startpoint       = 1;
$sf_2_jump_back        = 'true';
$sf_2_jumppoint_click  = 'true';
$sf_2_sorting          = isset( $slider['sf_2_sorting'] ) ? intval( $slider['sf_2_sorting'] ) : 0;
$sf_2_show_title       = 'true';
// 6. Custom CSS is a PRO feature (locked in the original plugin) — intentionally not rendered in Free.

// 2. Slide ID Collection (respecting custom drag-and-drop order)
$slide_ids = array();
if ( isset( $slider['sf_slide_id'] ) ) {
	if ( is_array( $slider['sf_slide_id'] ) ) {
		$slide_ids = array_values( $slider['sf_slide_id'] );
	} elseif ( is_string( $slider['sf_slide_id'] ) ) {
		$slide_ids = array_filter( array_map( 'trim', explode( ',', $slider['sf_slide_id'] ) ) );
	}
} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
	$slide_ids = array_keys( $slider['sf_slide_title'] );
}

// 3. Slide Sorting
if ( ! empty( $slide_ids ) && isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
	if ( $sf_2_sorting == 1 ) {
		sort( $slide_ids, SORT_NUMERIC );
	} elseif ( $sf_2_sorting == 2 ) {
		rsort( $slide_ids, SORT_NUMERIC );
	} elseif ( $sf_2_sorting == 3 ) {
		shuffle( $slide_ids );
	} elseif ( $sf_2_sorting == 4 ) {
		uasort( $slide_ids, function( $a, $b ) use ( $slider ) {
			$t1 = isset( $slider['sf_slide_title'][$a] ) ? $slider['sf_slide_title'][$a] : '';
			$t2 = isset( $slider['sf_slide_title'][$b] ) ? $slider['sf_slide_title'][$b] : '';
			return strcmp( $t1, $t2 );
		});
	} elseif ( $sf_2_sorting == 5 ) {
		uasort( $slide_ids, function( $a, $b ) use ( $slider ) {
			$t1 = isset( $slider['sf_slide_title'][$a] ) ? $slider['sf_slide_title'][$a] : '';
			$t2 = isset( $slider['sf_slide_title'][$b] ) ? $slider['sf_slide_title'][$b] : '';
			return strcmp( $t2, $t1 );
		});
	}
}

// 4. CSS and JS Enqueue
wp_enqueue_script( 'jquery' );
wp_enqueue_style( 'sf-2-photoroller-css' );
wp_enqueue_script( 'sf-2-photoroller-js' );
?>
<script>
jQuery( document ).ready(function($) {
	var $elem = $('.sf-2-<?php echo esc_js( $sf_slider_id ); ?>');
	if ($elem.length) {
		$elem.photoroller({
			startpoint: <?php echo intval( $sf_2_startpoint ); ?>,
			jump_back: <?php echo ( $sf_2_jump_back === 'false' ) ? 'false' : 'true'; ?>,
			jumppoint_click: <?php echo ( $sf_2_jumppoint_click === 'true' ) ? 'true' : 'false'; ?>
		});
	}
});
</script>

<!-- slider start-->
<?php
if ( empty( $slide_ids ) ) {
	?>
	<div style="text-align:center;padding:40px 20px;background:#f8fafc;border-radius:8px;border:1px dashed #cbd5e1;">
		<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
		<p style="margin:12px 0 0;font-size:14px;color:#64748b;font-weight:500;"><?php esc_html_e( 'Add images to see the live preview', 'slider-factory' ); ?></p>
		<p style="margin:4px 0 0;font-size:12px;color:#94a3b8;"><?php esc_html_e( 'Click Add Images to upload slides', 'slider-factory' ); ?></p>
	</div>
	<?php
	return;
}
?>
<div class="sf-2-<?php echo esc_attr( $sf_slider_id ); ?>">
	<?php
	if ( ! empty( $slide_ids ) ) {
		foreach ( $slide_ids as $attachment_id ) {
			$attachment_id = trim( $attachment_id );
			if ( empty( $attachment_id ) ) { continue; }
			$attachment = get_post( $attachment_id );

			$custom_title   = isset( $slider['sf_slide_title'][ $attachment_id ] ) ? trim( $slider['sf_slide_title'][ $attachment_id ] ) : '';
			$sf_slide_title = ( $custom_title !== '' ) ? $custom_title : ( $attachment ? get_the_title( $attachment_id ) : '' );

			$custom_desc    = isset( $slider['sf_slide_desc'][ $attachment_id ] ) ? trim( $slider['sf_slide_desc'][ $attachment_id ] ) : '';
			$sf_slide_descs = ( $custom_desc !== '' ) ? $custom_desc : ( $attachment ? $attachment->post_content : '' );

			$custom_alt     = isset( $slider['sf_slide_alt_text'][ $attachment_id ] ) ? trim( $slider['sf_slide_alt_text'][ $attachment_id ] ) : '';
			$sf_slide_alt   = ( $custom_alt !== '' ) ? $custom_alt : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

			$sf_slide_full_url = wp_get_attachment_image_src( $attachment_id, 'full', true );
			if ( ! is_array( $sf_slide_full_url ) ) { continue; }
			?>
			<div class="sf-2-slide">
				<img class="sf-2-slide-image" src="<?php echo esc_url( $sf_slide_full_url[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_alt ); ?>" loading="lazy" decoding="async">
				<?php if ( $sf_2_show_title !== 'false' && ( $sf_slide_title !== '' || $sf_slide_descs !== '' ) ) { ?>
				<div class="sf-2-slide-content">
					<?php if ( $sf_slide_title !== '' ) { ?>
						<div class="sf-2-slide-title"><?php echo esc_html( $sf_slide_title ); ?></div>
					<?php } ?>
					<?php if ( $sf_slide_descs !== '' ) { ?>
						<div class="sf-2-slide-desc"><?php echo esc_html( $sf_slide_descs ); ?></div>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
			<?php
		}
	}
	?>
</div>
<!-- slider end-->

<?php
$sf_2_render_height = ( empty( $sf_2_height ) || strpos( $sf_2_height, '%' ) !== false ) ? 'auto' : $sf_2_height;
?>
<style>
.sf-2-<?php echo esc_html( $sf_slider_id ); ?> {
	margin-left: auto;
	margin-right: auto;
	width: <?php echo esc_html( $sf_2_width ); ?>;
	height: <?php echo esc_html( $sf_2_render_height ); ?>;
	position: relative;
	overflow: hidden;
}

.sf-2-<?php echo esc_html( $sf_slider_id ); ?> .sf-2-slide {
	width: 100%;
	height: 100%;
	overflow: hidden;
}

.sf-2-<?php echo esc_html( $sf_slider_id ); ?> .sf-2-slide-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.sf-2-<?php echo esc_html( $sf_slider_id ); ?> .sf-2-slide-content {
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

.sf-2-<?php echo esc_html( $sf_slider_id ); ?> .sf-2-slide-title {
	font-size: 14px;
	font-weight: 700;
	color: #ffffff;
	margin-bottom: 2px;
	line-height: 1.3;
	text-shadow: 0 1px 3px rgba(0,0,0,0.5);
}

.sf-2-<?php echo esc_html( $sf_slider_id ); ?> .sf-2-slide-desc {
	font-size: 12px;
	color: rgba(255,255,255,0.85);
	line-height: 1.4;
	text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

</style>
