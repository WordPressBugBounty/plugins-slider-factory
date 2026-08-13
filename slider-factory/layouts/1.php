<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// 1. Dimensions & Height Handling
$sf_1_width = ( isset( $slider['sf_1_width'] ) && ! empty( $slider['sf_1_width'] ) ) ? trim( $slider['sf_1_width'] ) : '100%';
if ( is_numeric( $sf_1_width ) ) {
	$sf_1_width .= 'px';
}

$sf_1_adaptive_height = ( isset( $slider['sf_1_adaptive_height'] ) && $slider['sf_1_adaptive_height'] === 'false' ) ? 'false' : 'true';

if ( isset( $slider['sf_1_height'] ) && ! empty( $slider['sf_1_height'] ) ) {
	$sf_1_height = trim( $slider['sf_1_height'] );
	if ( is_numeric( $sf_1_height ) ) {
		$sf_1_height .= 'px';
	}
} else {
	$sf_1_height = '700px';
}

// 2. Design Preset (1: 100% Single Slide, 2: 49% 2-Card, 3: 32% 3-Card Grid)
// FREE EDITION: design presets are PRO features (locked in the original plugin) — fixed to Preset 1.
$sf_1_design_preset = 1;

// 3. Playback & Animation
$sf_1_auto_play = ( isset( $slider['sf_1_auto_play'] ) && $slider['sf_1_auto_play'] === 'false' ) ? 'false' : 'true';
// FREE EDITION: auto-play speed / pause-on-hover are PRO features (locked in the original plugin) — fixed defaults.
$sf_1_auto_play_speed = 1500;
$sf_1_pause_on_hover = 'false';

// 4. Navigation — FREE EDITION: arrows/dots/thumbnails are PRO features (locked in the original plugin).
// Fixed to the original Free plugin's behavior: arrows ON, dots ON, no thumbnail bar.
$sf_1_navigation_arrow = 'true';
$sf_1_navigation_dots  = 'true';
$sf_1_thumbnail        = 'false';

// 5. Behavior — FREE EDITION: infinite scroll, slide align and RTL are PRO
// features (locked in the original plugin) — fixed defaults below. Only sorting is free.
$sf_1_infinite_scroll = 'true';
$sf_1_sorting = isset( $slider['sf_1_sorting'] ) ? intval( $slider['sf_1_sorting'] ) : 0;
$sf_1_slide_align = 'center';
$sf_1_rtl = 'false';

// 6. Custom CSS is a PRO feature (locked in the original plugin) — intentionally not rendered in Free.

// Collect slide IDs in exact order matching the slides grid
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

// Slide Sorting
if ( ! empty( $slide_ids ) && isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
	if ( $sf_1_sorting == 1 ) {
		sort( $slide_ids, SORT_NUMERIC );
	} elseif ( $sf_1_sorting == 2 ) {
		rsort( $slide_ids, SORT_NUMERIC );
	} elseif ( $sf_1_sorting == 3 ) {
		shuffle( $slide_ids );
	} elseif ( $sf_1_sorting == 4 ) {
		uasort( $slide_ids, function( $a, $b ) use ( $slider ) {
			$t1 = isset( $slider['sf_slide_title'][$a] ) ? $slider['sf_slide_title'][$a] : '';
			$t2 = isset( $slider['sf_slide_title'][$b] ) ? $slider['sf_slide_title'][$b] : '';
			return strcmp( $t1, $t2 );
		});
	} elseif ( $sf_1_sorting == 5 ) {
		uasort( $slide_ids, function( $a, $b ) use ( $slider ) {
			$t1 = isset( $slider['sf_slide_title'][$a] ) ? $slider['sf_slide_title'][$a] : '';
			$t2 = isset( $slider['sf_slide_title'][$b] ) ? $slider['sf_slide_title'][$b] : '';
			return strcmp( $t2, $t1 );
		});
	}
}

// CSS and JS Enqueue
wp_enqueue_script( 'jquery' );
wp_enqueue_style( 'sf-1-flickity-css' );
wp_enqueue_script( 'sf-1-flickity-pkgd-min-js' );
?>

<script>
jQuery( document ).ready(function($) {
	var $main = $('.carousel-main-<?php echo esc_js( $sf_slider_id ); ?>');

	if ($main.data('flickity')) {
		$main.flickity('destroy');
	}

	var mainCarousel = $main.flickity({
		cellSelector: '.carousel-cell-<?php echo esc_js( $sf_slider_id ); ?>',
		imagesLoaded: true,
		<?php if ( $sf_1_auto_play === 'true' ) { ?>
		autoPlay: <?php echo esc_js( $sf_1_auto_play_speed ); ?>,
		<?php } else { ?>
		autoPlay: false,
		<?php } ?>
		pauseAutoPlayOnHover: <?php echo esc_js( $sf_1_pause_on_hover ); ?>,
		wrapAround: <?php echo esc_js( $sf_1_infinite_scroll ); ?>,
		adaptiveHeight: <?php echo esc_js( $sf_1_adaptive_height ); ?>,
		prevNextButtons: <?php echo esc_js( $sf_1_navigation_arrow ); ?>,
		pageDots: <?php echo esc_js( $sf_1_navigation_dots ); ?>,
		<?php if ( $sf_1_design_preset > 1 ) { ?>
		contain: true,
		cellAlign: 'left',
		<?php } else { ?>
		cellAlign: '<?php echo esc_js( $sf_1_slide_align ); ?>',
		<?php } ?>
		rightToLeft: <?php echo esc_js( $sf_1_rtl ); ?>,
			arrowShape: 'M 60,15 L 65,20 L 35,50 L 65,80 L 60,85 L 25,50 Z'
	});

	$main.find('img').on('load', function() {
		$main.flickity('resize');
	});
	setTimeout(function() {
		$main.flickity('resize');
	}, 100);
});
</script>

<!-- slider start -->
<div class="carousel-<?php echo esc_attr( $sf_slider_id ); ?> carousel-main-<?php echo esc_attr( $sf_slider_id ); ?> preset-<?php echo esc_attr( $sf_1_design_preset ); ?>">
	<?php
	// Render slides in exact order
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
			<div class="carousel-cell-<?php echo esc_attr( $sf_slider_id ); ?>">
				<img class="sf-1-slide-image" src="<?php echo esc_url( $sf_slide_full_url[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_alt ); ?>" loading="lazy" decoding="async" width="100%">
				
				<?php if ( $sf_slide_title != '' || $sf_slide_descs != '' ) { ?>
				<div class="sf-1-slide-content">
					<?php if ( $sf_slide_title != '' ) { ?>
						<div class="sf-1-slide-title"><?php echo esc_html( $sf_slide_title ); ?></div>
					<?php } ?>
					<?php if ( $sf_slide_descs != '' ) { ?>
						<div class="sf-1-slide-desc"><?php echo esc_html( $sf_slide_descs ); ?></div>
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

<style>
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> {
	margin-left: auto;
	margin-right: auto;
	width: <?php echo esc_html( $sf_1_width ); ?>;
	margin-bottom: 25px;
	position: relative;
}

.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-viewport {
	border-radius: 4px;
	overflow: hidden;
	box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
	transition: height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
<?php if ( strpos( $sf_1_height, '%' ) === false && $sf_1_height !== 'auto' && ! empty( $sf_1_height ) ) { ?>
	height: <?php echo esc_html( $sf_1_height ); ?>;
	min-height: 250px;
<?php } ?>
}

.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .sf-1-slide-image {
	display: block;
	max-width: 100%;
	width: 100%;
<?php if ( strpos( $sf_1_height, '%' ) === false && $sf_1_height !== 'auto' && ! empty( $sf_1_height ) ) { ?>
	height: <?php echo esc_html( $sf_1_height ); ?>;
	object-fit: cover;
<?php } else { ?>
	height: auto;
<?php } ?>
}

.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .sf-1-slide-content {
	position: absolute;
	color: white;
	width: 100%;
	bottom: 0;
	text-align: center;
	background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0) 100%);
	padding: 45px 20px 20px 20px;
	box-sizing: border-box;
	pointer-events: none;
}

.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .sf-1-slide-title {
	font-size: 22px;
	font-weight: 700;
	text-shadow: 0 2px 8px rgba(0,0,0,0.5);
	margin-bottom: 4px;
	padding: 0;
}

.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .sf-1-slide-desc {
	font-size: 14px;
	font-weight: 400;
	text-shadow: 0 2px 8px rgba(0,0,0,0.5);
	opacity: 0.9;
	padding: 0;
}

/* Preset Cell Width Rules */
<?php if ( $sf_1_design_preset === 2 ) { ?>
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .carousel-cell-<?php echo esc_html( $sf_slider_id ); ?> {
	width: 48.5% !important;
	margin-right: 3% !important;
}
<?php } elseif ( $sf_1_design_preset === 3 ) { ?>
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .carousel-cell-<?php echo esc_html( $sf_slider_id ); ?> {
	width: 31.33% !important;
	margin-right: 3% !important;
}
<?php } else { ?>
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .carousel-cell-<?php echo esc_html( $sf_slider_id ); ?> {
	width: 100% !important;
	margin-right: 0 !important;
}
<?php } ?>

.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .carousel-cell-<?php echo esc_html( $sf_slider_id ); ?> {
<?php if ( strpos( $sf_1_height, '%' ) === false && $sf_1_height !== 'auto' && ! empty( $sf_1_height ) ) { ?>
	height: <?php echo esc_html( $sf_1_height ); ?>;
<?php } else { ?>
	height: auto;
<?php } ?>
}

/* Active Slide Visibility Rule */
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .carousel-cell-<?php echo esc_html( $sf_slider_id ); ?>.is-selected {
	z-index: 2;
}

/* Thumbnail Navigation Styling */
.sf-1-thumb-nav {
	margin-top: 12px;
	margin-left: auto;
	margin-right: auto;
	width: <?php echo esc_html( $sf_1_width ); ?>;
}
.sf-1-thumb-cell {
	width: 80px;
	height: 55px;
	margin-right: 8px;
	cursor: pointer;
	opacity: 0.6;
	transition: opacity 0.2s;
	border-radius: 4px;
	overflow: hidden;
}
.sf-1-thumb-cell.is-nav-selected {
	opacity: 1;
	border: 2px solid #2563eb;
}
.sf-1-thumb-cell img {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

/* Premium Navigation Arrows */
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-prev-next-button {
	width: 44px;
	height: 60px;
	background: rgba(0, 0, 0, 0.2);
	border-radius: 4px;
	border: none;
	box-shadow: none;
	opacity: 0;
	transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-prev-next-button.previous {
	left: 15px;
	transform: translateY(-50%) translateX(-15px);
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-prev-next-button.next {
	right: 15px;
	transform: translateY(-50%) translateX(15px);
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?>:hover .flickity-prev-next-button.previous {
	opacity: 0.75;
	transform: translateY(-50%) translateX(0);
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?>:hover .flickity-prev-next-button.next {
	opacity: 0.75;
	transform: translateY(-50%) translateX(0);
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-prev-next-button:hover {
	opacity: 1;
	background: rgba(0, 0, 0, 0.4);
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-prev-next-button .flickity-button-icon {
	width: 45%;
	height: 45%;
	left: 27.5%;
	top: 27.5%;
	fill: #ffffff;
}

/* Page Dots */
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-page-dots {
	position: relative;
	bottom: auto;
	margin-top: 15px;
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 6px;
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-page-dots .dot {
	width: 8px;
	height: 8px;
	background: #cbd5e1;
	opacity: 1;
	border-radius: 50%;
	transition: all 0.3s ease;
	margin: 0;
	border: none;
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-page-dots .dot:hover {
	background: #94a3b8;
}
.carousel-main-<?php echo esc_html( $sf_slider_id ); ?> .flickity-page-dots .dot.is-selected {
	background: #64748b;
	width: 20px;
	border-radius: 4px;
	box-shadow: none;
}

</style>
