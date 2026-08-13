<?php // slider layout 11

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// slider settings
if ( isset( $slider['sf_11_width'] ) ) {
	$sf_11_width = $slider['sf_11_width'];
} else {
	$sf_11_width = '100%';
}
if ( isset( $slider['sf_11_height'] ) && ! empty( $slider['sf_11_height'] ) ) {
	$sf_11_height = $slider['sf_11_height'];
} else {
	$sf_11_height = '750px';
}

if ( empty( $sf_11_height ) || strpos( (string) $sf_11_height, '%' ) !== false || $sf_11_height === 'auto' ) {
	$sf_11_height_css = '750px';
} else {
	$sf_11_height_css = is_numeric( $sf_11_height ) ? $sf_11_height . 'px' : $sf_11_height;
}

// FREE EDITION: slider header is a PRO feature (locked in the original plugin) — fixed default.
$sf_11_slider_header = '';
if ( isset( $slider['sf_11_sorting'] ) ) {
	$sf_11_sorting = intval( $slider['sf_11_sorting'] );
} else {
	$sf_11_sorting = 0;
}
// Custom CSS is a PRO feature — intentionally not rendered in Free.

// CSS and JS
wp_enqueue_script( 'sf-11-product-slider-mordenizer-js' );
?>
<div class="sf-11-<?php echo esc_attr( $sf_slider_id ); ?>">
	<section id="ps-container-<?php echo esc_attr( $sf_slider_id ); ?>" class="ps-container-<?php echo esc_attr( $sf_slider_id ); ?>">

		<div class="ps-header-<?php echo esc_attr( $sf_slider_id ); ?>">
			<h1><?php echo esc_attr( $sf_11_slider_header ); ?></h1>
		</div>
		<!-- /ps-header -->

		<div class="ps-contentwrapper-<?php echo esc_attr( $sf_slider_id ); ?>">
			<?php
			$slide_ids = array();
			if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
				$slide_ids = $slider['sf_slide_id'];
			} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
				$slide_ids = array_keys( $slider['sf_slide_title'] );
			}

			if ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) ) {
				if ( $sf_11_sorting === 1 ) {
					ksort( $slider['sf_slide_title'] );
					$slide_ids = array_keys( $slider['sf_slide_title'] );
				} elseif ( $sf_11_sorting === 2 ) {
					krsort( $slider['sf_slide_title'] );
					$slide_ids = array_keys( $slider['sf_slide_title'] );
				} elseif ( $sf_11_sorting === 3 ) {
					shuffle( $slide_ids );
				} elseif ( $sf_11_sorting === 4 ) {
					asort( $slider['sf_slide_title'] );
					$slide_ids = array_keys( $slider['sf_slide_title'] );
				} elseif ( $sf_11_sorting === 5 ) {
					arsort( $slider['sf_slide_title'] );
					$slide_ids = array_keys( $slider['sf_slide_title'] );
				}
			}

			if ( ! empty( $slide_ids ) ) {
				foreach ( $slide_ids as $id ) {
					$attachment_id = $id;
					if ( isset( $slider['sf_slide_title'][ $id ] ) && '' !== trim( $slider['sf_slide_title'][ $id ] ) ) {
						$sf_slide_title = $slider['sf_slide_title'][ $id ];
					} else {
						$sf_slide_title = get_the_title( $attachment_id );
					}

					$sf_slide_alt = isset( $slider['sf_slide_alt_text'][ $id ] ) && trim( $slider['sf_slide_alt_text'][ $id ] ) !== '' ? trim( $slider['sf_slide_alt_text'][ $id ] ) : get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
					$sf_slide_full_url = wp_get_attachment_image_src( $attachment_id, 'full', true );
					if ( ! is_array( $sf_slide_full_url ) ) { continue; }

					if ( isset( $slider['sf_slide_desc'][ $id ] ) && '' !== trim( $slider['sf_slide_desc'][ $id ] ) ) {
						$sf_slide_descs = $slider['sf_slide_desc'][ $id ];
					} else {
						$attachment     = get_post( $attachment_id );
						$sf_slide_descs = ( $attachment ? $attachment->post_content : '' );
					}

					if ( isset( $slider['sf_slide_link_text_1'][ $id ] ) ) {
						$sf_slide_link_text_1 = $slider['sf_slide_link_text_1'][ $id ];
					} else {
						$sf_slide_link_text_1 = '';
					}
					if ( isset( $slider['sf_slide_link_1'][ $id ] ) ) {
						$sf_slide_link_1 = $slider['sf_slide_link_1'][ $id ];
					} else {
						$sf_slide_link_1 = '';
					}
					if ( isset( $slider['sf_slide_link_text_2'][ $id ] ) ) {
						$sf_slide_link_text_2 = $slider['sf_slide_link_text_2'][ $id ];
					} else {
						$sf_slide_link_text_2 = '';
					}
					if ( isset( $slider['sf_slide_link_2'][ $id ] ) ) {
						$sf_slide_link_2 = $slider['sf_slide_link_2'][ $id ];
					} else {
						$sf_slide_link_2 = '';
					}
					?>
				<div class="ps-content-<?php echo esc_attr( $sf_slider_id ); ?>">
					<h2><?php echo esc_html( $sf_slide_title ); ?></h2>
					<?php if ( $sf_slide_link_1 != '' ) { ?>
						<a href="<?php echo esc_url( $sf_slide_link_1 ); ?>" target="_blank" class="ps-price-link-<?php echo esc_attr( $sf_slider_id ); ?>"><span class="ps-price-<?php echo esc_attr( $sf_slider_id ); ?>"><?php echo esc_html( $sf_slide_link_text_1 ); ?></span></a>
					<?php } else { ?>
						<span class="ps-price-<?php echo esc_attr( $sf_slider_id ); ?>"><?php echo esc_html( $sf_slide_link_text_1 ); ?></span>
					<?php } ?>
					<p><?php echo esc_html( $sf_slide_descs ); ?></p>
					<?php if ( $sf_slide_link_2 != '' && $sf_slide_link_text_2 != '' ) { ?>
						<a href="<?php echo esc_url( $sf_slide_link_2 ); ?>" target="_blank" class="ps-buy-link-<?php echo esc_attr( $sf_slider_id ); ?>"><?php echo esc_html( $sf_slide_link_text_2 ); ?></a>
					<?php } ?>
				</div>
					<?php
				}
			}
			?>
		</div>
		<!-- /ps-contentwrapper -->

		<!-- loop again here for images -->
		<div class="ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?>">
			<div class="ps-slides-<?php echo esc_attr( $sf_slider_id ); ?>">
				<?php
				if ( ! empty( $slide_ids ) ) {
					foreach ( $slide_ids as $id ) {
						$attachment_id = $id;
						$sf_slide_full_url = wp_get_attachment_image_src( $attachment_id, 'full', true );
						if ( ! is_array( $sf_slide_full_url ) ) { continue; }
						?>
				<div style="background-image:url(<?php echo esc_url( $sf_slide_full_url[0] ); ?>);"></div>
						<?php
					}
				}
				?>
			</div>

			<nav>
				<a href="#" class="ps-prev-<?php echo esc_attr( $sf_slider_id ); ?>"></a>
				<a href="#" class="ps-next-<?php echo esc_attr( $sf_slider_id ); ?>"></a>
			</nav>
		</div>
		<!-- /ps-slidewrapper -->

	</section>
	<!-- /ps-container -->
</div>
<style>
.sf-11-<?php echo esc_attr( $sf_slider_id ); ?>{
	margin-left: auto;
	margin-right: auto;
	width:<?php echo esc_attr( $sf_11_width ); ?>;
	height:<?php echo esc_attr( $sf_11_height_css ); ?>;
}
.ps-container-<?php echo esc_attr( $sf_slider_id ); ?> {
	position: relative;
	width: 100%;
	height: 100%;
	overflow: hidden;
	color: #555;
	background: #fafafa;
}
.ps-container-<?php echo esc_attr( $sf_slider_id ); ?> > div {
	position: absolute;
	width: 50%;
}
.ps-container-<?php echo esc_attr( $sf_slider_id ); ?> > div > div,
.ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav,
.ps-slides-<?php echo esc_attr( $sf_slider_id ); ?> > div {
	position: absolute;
}
.ps-header-<?php echo esc_attr( $sf_slider_id ); ?> {
	top: 0px;
	left: 0px;
	height: 150px;
	z-index: 1001;
	background: #fff;
}
.ps-header-<?php echo esc_attr( $sf_slider_id ); ?> h1 {
	color: #ccc;
	line-height: 150px;
	margin: 0;
	padding: 0 50px;
	font-weight: 200;
	font-size: 14px;
	letter-spacing: 10px;
}
.ps-contentwrapper-<?php echo esc_attr( $sf_slider_id ); ?> {
	top: 150px;
	bottom: 0px;
	overflow: hidden;
	z-index: 1000;
}
.ps-content-<?php echo esc_attr( $sf_slider_id ); ?> {
	background: #fff;
	width: 100%;
	height: 100%;
	pointer-events: none;
	opacity: 0;
	transition: opacity 0.3s ease;
}
.ps-content-<?php echo esc_attr( $sf_slider_id ); ?>.ps-active-<?php echo esc_attr( $sf_slider_id ); ?> {
	pointer-events: auto;
	opacity: 1;
	z-index: 999;
}
.ps-content-<?php echo esc_attr( $sf_slider_id ); ?> h2 {
	padding: 10px 15px;
	border-right: 1px solid #f2f2f2;
	border-bottom: 1px solid #f2f2f2;
	letter-spacing: 4px;
	margin: 10px 0 30px;
	text-align: right;
	font-weight: 700;
}
.ps-content-<?php echo esc_attr( $sf_slider_id ); ?> p {
	line-height: 26px;
	font-size: 12px;
	letter-spacing: 1px;
	word-spacing: 0px;
	padding: 10px 15px;
	font-weight: 400;
	text-align: justify;
	border-left: 1px solid #f2f2f2;
	border-top: 1px solid #f2f2f2;
}
.ps-content-<?php echo esc_attr( $sf_slider_id ); ?> span.ps-price-<?php echo esc_attr( $sf_slider_id ); ?> {
	float: left;
	margin: 10px;
	width: auto;
	height: auto;
	line-height: 140px;
	text-align: center;
	color: #fff;
	background: #f7cfc6;
	background: rgba(247,197,185,0.8);
	font-size: 40px;
	font-weight: 200;
	padding: 0 10px 0 10px;
}
.ps-content-<?php echo esc_attr( $sf_slider_id ); ?> a.ps-price-link-<?php echo esc_attr( $sf_slider_id ); ?> {
	display: inline-block;
	float: left;
	text-decoration: none;
	cursor: pointer;
}
.ps-content-<?php echo esc_attr( $sf_slider_id ); ?> a.ps-buy-link-<?php echo esc_attr( $sf_slider_id ); ?> {
	font-size: 14px;
	font-weight: 700;
	color: #555;
	letter-spacing: 4px;
	float: right;
	border: 3px solid #555;
	padding: 3px;
	text-indent: 4px;
	margin-right: 15px;
	cursor: pointer;
	text-decoration: none;
}
.no-touch .ps-content-<?php echo esc_attr( $sf_slider_id ); ?> a.ps-buy-link-<?php echo esc_attr( $sf_slider_id ); ?>:hover {
	color: #b2d79d;
	border-color: #b2d79d;
}
.ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> {
	right: 0px;
	top: 0px;
	height: 100%;
	overflow: hidden;
}
.ps-slides-<?php echo esc_attr( $sf_slider_id ); ?> {
	top: 0px;
	bottom: 200px;
	width: 100%;
}
.ps-slides-<?php echo esc_attr( $sf_slider_id ); ?> > div {
	width: 100%;
	height: 100%;
	box-shadow: inset 0 0 0 9999px rgba(179,157,250,0.1);
}
.ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav {
	width: 100%;
	height: 200px;
	bottom: 0px;
	right: 0px;
	z-index: 1000;
}
.ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav > a {
	width: 50%;
	height: 100%;
	position: relative;
	float: left;
	box-shadow: inset 0 0 0 9999px rgba(207,227,206,0.8);
	outline: none;
}
.ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav > a:first-child {
	box-shadow: inset 0 0 0 9999px rgba(233,217,141,0.8);
}
.no-touch .ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav > a {
	-webkit-transition: box-shadow 0.4s ease-in-out;
	-moz-transition: box-shadow 0.4s ease-in-out;
	-ms-transition: box-shadow 0.4s ease-in-out;
	-o-transition: box-shadow 0.4s ease-in-out;
	transition: box-shadow 0.4s ease-in-out;
}
.no-touch .ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav > a:hover {
	box-shadow: inset 0 0 0 9999px rgba(246,224,121,0.1);
}
.no-touch .ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav > a:first-child:hover {
	box-shadow: inset 0 0 0 9999px rgba(249,15,15,0.1);
}
.ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav > a:after {
	content: '';
	position: absolute;
	width: 100px;
	height: 100px;
	top: 50%;
	left: 50%;
	margin: -20px 0 0 -50px;
	-webkit-transform: rotate(45deg);
	-moz-transform: rotate(45deg);
	-o-transform: rotate(45deg);
	-ms-transform: rotate(45deg);
	transform: rotate(45deg);
	border-left: 1px solid #fff;
	border-top: 1px solid #fff;
}
.ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav > a:first-child:after {
	-webkit-transform: rotate(-135deg);
	-moz-transform: rotate(-135deg);
	-o-transform: rotate(-135deg);
	-ms-transform: rotate(-135deg);
	transform: rotate(-135deg);
	margin: -80px 0 0 -50px;
}
.ps-slides-<?php echo esc_attr( $sf_slider_id ); ?> > div,
.ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav > a {
	background-color: #fff;
	background-position: center center;
	background-repeat: no-repeat;
	background-size: cover;
}
.ps-move-<?php echo esc_attr( $sf_slider_id ); ?> {
	-webkit-transition: top 400ms ease-out;
	-moz-transition: top 400ms ease-out;
	-o-transition: top 400ms ease-out;
	-ms-transition: top 400ms ease-out;
	transition: top 400ms ease-out;
}
@media screen and (max-width: 860px) {
	.js .ps-container-<?php echo esc_attr( $sf_slider_id ); ?> > div {
		width: 100%;
	}
	.js .ps-header-<?php echo esc_attr( $sf_slider_id ); ?> {
		height: 50px;
	}
	.js .ps-header-<?php echo esc_attr( $sf_slider_id ); ?> h1 {
		line-height: 50px;
		padding: 0px 20px;
		letter-spacing: 4px;
	}
	.js .ps-slides-<?php echo esc_attr( $sf_slider_id ); ?> {
		bottom: 320px;
		top: 50px;
	}
	.js .ps-slidewrapper-<?php echo esc_attr( $sf_slider_id ); ?> > nav {
		height: 100px;
	}
	.js .ps-contentwrapper-<?php echo esc_attr( $sf_slider_id ); ?> {
		top: auto;
		height: 220px;
		bottom: 100px;
	}
	.js .ps-content-<?php echo esc_attr( $sf_slider_id ); ?> {
		padding: 10px;
	}
	.js .ps-content-<?php echo esc_attr( $sf_slider_id ); ?> h2 {
		border-right: none;
		font-size: 18px;
		margin: 10px 0;
		padding-top: 0;
	}
	.js .ps-content-<?php echo esc_attr( $sf_slider_id ); ?> span.ps-price {
		font-size: 18px;
		width: 50px;
		height: 50px;
		line-height: 50px;
		font-weight: 700;
		margin-bottom: 0;
	}
	.js .ps-content-<?php echo esc_attr( $sf_slider_id ); ?> p {
		line-height: 20px;
		border: none;
		padding: 5px 10px;
		height: 80px;
		overflow-y: scroll;
	}
	.js .ps-content-<?php echo esc_attr( $sf_slider_id ); ?> a.ps-buy-link-<?php echo esc_attr( $sf_slider_id ); ?> {
		font-size: 13px;
		margin: 10px 20px 0 0;
	}
}
</style>
<script type="text/javascript">
var Slider_<?php echo esc_js( $sf_slider_id ); ?> = (function() {
	var $container = jQuery( '#ps-container-<?php echo esc_js( $sf_slider_id ); ?>' ),
		$contentwrapper = $container.children( 'div.ps-contentwrapper-<?php echo esc_js( $sf_slider_id ); ?>' ),
		$items = $contentwrapper.children( 'div.ps-content-<?php echo esc_js( $sf_slider_id ); ?>' ),
		itemsCount = $items.length,
		$slidewrapper = $container.children( 'div.ps-slidewrapper-<?php echo esc_js( $sf_slider_id ); ?>' ),
		$slidescontainer = $slidewrapper.find( 'div.ps-slides-<?php echo esc_js( $sf_slider_id ); ?>' ),
		$slides = $slidescontainer.children( 'div' ),
		$navprev = $slidewrapper.find( 'nav > a.ps-prev-<?php echo esc_js( $sf_slider_id ); ?>' ),
		$navnext = $slidewrapper.find( 'nav > a.ps-next-<?php echo esc_js( $sf_slider_id ); ?>' ),
		current = 0,
		isAnimating = false,
		support = typeof Modernizr !== 'undefined' ? Modernizr.csstransitions : true,
		transEndEventNames = {
			'WebkitTransition' : 'webkitTransitionEnd',
			'MozTransition' : 'transitionend',
			'OTransition' : 'oTransitionEnd',
			'msTransition' : 'MSTransitionEnd',
			'transition' : 'transitionend'
		},
		transEndEventName = (typeof Modernizr !== 'undefined' && Modernizr.prefixed) ? transEndEventNames[ Modernizr.prefixed( 'transition' ) ] : 'transitionend',

		init = function() {
			var $currentItem = $items.eq( current ),
				$currentSlide = $slides.eq( current ),
				initCSS = {
					top : 0,
					zIndex : 999
				};

			$items.removeClass('ps-active-<?php echo esc_js( $sf_slider_id ); ?>');
			$currentItem.addClass('ps-active-<?php echo esc_js( $sf_slider_id ); ?>').css( initCSS );
			$currentSlide.css( initCSS );
			updateNavImages();
			initEvents();
		},
		updateNavImages = function() {
			var configPrev = ( current > 0 ) ? $slides.eq( current - 1 ).css( 'background-image' ) : $slides.eq( itemsCount - 1 ).css( 'background-image' ),
				configNext = ( current < itemsCount - 1 ) ? $slides.eq( current + 1 ).css( 'background-image' ) : $slides.eq( 0 ).css( 'background-image' );

			$navprev.css( 'background-image', configPrev );
			$navnext.css( 'background-image', configNext );
		},
		initEvents = function() {
			$navprev.on( 'click', function( event ) {
				if( !isAnimating ) {
					slide( 'prev' );
				}
				return false;
			} );

			$navnext.on( 'click', function( event ) {
				if( !isAnimating ) {
					slide( 'next' );
				}
				return false;
			} );

			if (transEndEventName) {
				$items.on( transEndEventName, removeTransition );
				$slides.on( transEndEventName, removeTransition );
			}
		},
		removeTransition = function() {
			isAnimating = false;
			jQuery(this).removeClass('ps-move-<?php echo esc_js( $sf_slider_id ); ?>');
		},
		slide = function( dir ) {
			isAnimating = true;
			var $currentItem = $items.eq( current ),
				$currentSlide = $slides.eq( current );

			if( dir === 'next' ) {
				( current < itemsCount - 1 ) ? ++current : current = 0;
			} else if( dir === 'prev' ) {
				( current > 0 ) ? --current : current = itemsCount - 1;
			}
			var $newItem = $items.eq( current ),
				$newSlide = $slides.eq( current );

			$newItem.css( {
				top : ( dir === 'next' ) ? '-100%' : '100%',
				zIndex : 999
			} );
			$newSlide.css( {
				top : ( dir === 'next' ) ? '100%' : '-100%',
				zIndex : 999
			} );

			setTimeout( function() {
				$items.removeClass('ps-active-<?php echo esc_js( $sf_slider_id ); ?>');
				$newItem.addClass('ps-active-<?php echo esc_js( $sf_slider_id ); ?>');

				$currentItem.addClass( 'ps-move-<?php echo esc_js( $sf_slider_id ); ?>' ).css( {
					top : ( dir === 'next' ) ? '100%' : '-100%',
					zIndex : 1
				} );

				$currentSlide.addClass( 'ps-move-<?php echo esc_js( $sf_slider_id ); ?>' ).css( {
					top : ( dir === 'next' ) ? '-100%' : '100%',
					zIndex : 1
				} );

				$newItem.addClass( 'ps-move-<?php echo esc_js( $sf_slider_id ); ?>' ).css( 'top', 0 );
				$newSlide.addClass( 'ps-move-<?php echo esc_js( $sf_slider_id ); ?>' ).css( 'top', 0 );

				if( !support ) {
					isAnimating = false;
				}
			}, 0 );

			updateNavImages();
		};

	return { init : init };
})();

jQuery(function () {
	Slider_<?php echo esc_js( $sf_slider_id ); ?>.init();
});
</script>
