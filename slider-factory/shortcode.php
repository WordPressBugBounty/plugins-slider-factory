<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_shortcode( 'sf', 'wpfrank_sf_shortcode' );
function wpfrank_sf_shortcode( $sf_atts ) {
	ob_start();
	$sf_atts = shortcode_atts(
		array(
			'id'     => '',
			'layout' => '',
		),
		$sf_atts
	);

	$sf_slider_id     = sanitize_text_field( $sf_atts['id'] );
	$sf_slider_layout = sanitize_text_field( $sf_atts['layout'] );

	if ( empty( $sf_slider_id ) || empty( $sf_slider_layout ) ) {
		return '';
	}

	// load slider settings
	$slider = get_option( 'sf_slider_' . $sf_slider_id );
	if ( ! is_array( $slider ) || empty( $slider ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			?>
			<div class="sf-admin-notice sf-admin-notice-danger">
				<div class="sf-admin-notice-content">
					<p class="sf-admin-notice-title"><?php esc_html_e( 'Slider does not exist.', 'slider-factory' ); ?> (ID: <?php echo esc_html( $sf_slider_id ); ?>)</p>
					<p class="sf-admin-notice-sub"><?php esc_html_e( 'This warning is only visible to site administrators.', 'slider-factory' ); ?></p>
				</div>
			</div>
			<style>
			.sf-admin-notice {
				padding: 14px 18px;
				border-radius: 4px;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
				margin: 15px 0;
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 16px;
				box-shadow: 0 2px 4px rgba(0,0,0,0.05);
			}
			.sf-admin-notice-danger {
				background-color: #fef2f2;
				border: 1px solid #fca5a5;
				color: #991b1b;
			}
			.sf-admin-notice-warning {
				background-color: #fffbe6;
				border: 1px solid #ffe58f;
				color: #d48806;
			}
			.sf-admin-notice-title {
				font-size: 14px;
				font-weight: 700;
				margin: 0 0 3px 0;
				line-height: 1.3;
			}
			.sf-admin-notice-sub {
				font-size: 12px;
				margin: 0;
				opacity: 0.85;
			}
			.sf-admin-notice-btn {
				display: inline-flex;
				align-items: center;
				padding: 7px 14px;
				background-color: #d48806;
				color: #ffffff !important;
				font-size: 12px;
				font-weight: 700;
				border-radius: 4px;
				text-decoration: none !important;
				white-space: nowrap;
				transition: background-color 0.2s ease;
			}
			.sf-admin-notice-btn:hover {
				background-color: #b77400;
				color: #ffffff !important;
			}
			</style>
			<?php
			return ob_get_clean();
		}
		return '';
	}

	// Render-time migration: convert legacy flat-format slider data so old sliders
	// render correctly without requiring an admin visit first.
	if ( function_exists( 'sf_migrate_flat_slider_data' ) && isset( $slider['sf_slider_id'] ) && ( ! isset( $slider['sf_slide_id'] ) || ! is_array( $slider['sf_slide_id'] ) ) ) {
		$migrated = sf_migrate_flat_slider_data( $slider );
		if ( $migrated !== $slider ) {
			$slider = $migrated;
			update_option( 'sf_slider_' . $sf_slider_id, $slider );
		}
	}

	// check if slider has images
	$slide_ids = array();
	if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) && ! empty( $slider['sf_slide_id'] ) ) {
		$slide_ids = $slider['sf_slide_id'];
	} elseif ( isset( $slider['sf_slide_title'] ) && is_array( $slider['sf_slide_title'] ) && ! empty( $slider['sf_slide_title'] ) ) {
		$slide_ids = array_keys( $slider['sf_slide_title'] );
	}

	$slide_ids = array_filter( (array) $slide_ids, function( $val ) {
		return ! empty( $val );
	} );

	if ( empty( $slide_ids ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			$configure_url = admin_url( 'admin.php?page=sf-manage-slider&sf-slider-action=edit&sf-slider-id=' . intval( $sf_slider_id ) );
			?>
			<div class="sf-admin-notice sf-admin-notice-warning">
				<div class="sf-admin-notice-content">
					<p class="sf-admin-notice-title"><?php esc_html_e( 'No images added to the slider.', 'slider-factory' ); ?> (ID: <?php echo esc_html( $sf_slider_id ); ?>)</p>
					<p class="sf-admin-notice-sub"><?php esc_html_e( 'This warning is only visible to site administrators.', 'slider-factory' ); ?></p>
				</div>
				<a href="<?php echo esc_url( $configure_url ); ?>" class="sf-admin-notice-btn"><?php esc_html_e( 'Configure Slider', 'slider-factory' ); ?> &rarr;</a>
			</div>
			<style>
			.sf-admin-notice {
				padding: 14px 18px;
				border-radius: 4px;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
				margin: 15px 0;
				display: flex;
				align-items: center;
				justify-content: space-between;
				gap: 16px;
				box-shadow: 0 2px 4px rgba(0,0,0,0.05);
			}
			.sf-admin-notice-danger {
				background-color: #fef2f2;
				border: 1px solid #fca5a5;
				color: #991b1b;
			}
			.sf-admin-notice-warning {
				background-color: #fffbe6;
				border: 1px solid #ffe58f;
				color: #d48806;
			}
			.sf-admin-notice-title {
				font-size: 14px;
				font-weight: 700;
				margin: 0 0 3px 0;
				line-height: 1.3;
			}
			.sf-admin-notice-sub {
				font-size: 12px;
				margin: 0;
				opacity: 0.85;
			}
			.sf-admin-notice-btn {
				display: inline-flex;
				align-items: center;
				padding: 7px 14px;
				background-color: #d48806;
				color: #ffffff !important;
				font-size: 12px;
				font-weight: 700;
				border-radius: 4px;
				text-decoration: none !important;
				white-space: nowrap;
				transition: background-color 0.2s ease;
			}
			.sf-admin-notice-btn:hover {
				background-color: #b77400;
				color: #ffffff !important;
			}
			</style>
			<?php
			return ob_get_clean();
		}
		return '';
	}

	if ( isset( $slider['sf_slider_title'] ) ) {
		$sf_slider_title = $slider['sf_slider_title'];
	} else {
		$sf_slider_title = '';
	}
	if ( isset( $slider['sf_slider_desc'] ) ) {
		$sf_slider_desc = $slider['sf_slider_desc'];
	} else {
		$sf_slider_desc = '';
	}

	// print_r($slider);
	// echo "<hr>";

	// load slider start
	if ( $sf_slider_layout == 1 ) {
		require 'layouts/1.php';
	}
	if ( $sf_slider_layout == 2 ) {
		require 'layouts/2.php';
	}
	if ( $sf_slider_layout == 3 ) {
		require 'layouts/3.php';
	}
	if ( $sf_slider_layout == 4 ) {
		require 'layouts/4.php';
	}
	if ( $sf_slider_layout == 5 ) {
		require 'layouts/5.php';
	}
	if ( $sf_slider_layout == 6 ) {
		require 'layouts/6.php';
	}
	if ( $sf_slider_layout == 7 ) {
		require 'layouts/7.php';
	}
	if ( $sf_slider_layout == 8 ) {
		require 'layouts/8.php';
	}
	if ( $sf_slider_layout == 9 ) {
		require 'layouts/9.php';
	}
	if ( $sf_slider_layout == 10 ) {
		require 'layouts/10.php';
	}
	if ( $sf_slider_layout == 11 ) {
		include 'layouts/11.php';
	}
	if ( $sf_slider_layout == 12 ) {
		include 'layouts/12.php';
	}
	// load slider end

	return ob_get_clean();
}

