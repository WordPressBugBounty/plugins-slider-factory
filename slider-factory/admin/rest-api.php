<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'sf_rest_permissions_check' ) ) {
	function sf_rest_permissions_check() {
		return current_user_can( 'manage_options' );
	}
}

if ( ! function_exists( 'sf_get_slider_thumbnails' ) ) {
	function sf_get_slider_thumbnails( $slide_ids ) {
		$thumbnails = array();
		if ( is_array( $slide_ids ) ) {
			foreach ( $slide_ids as $s_id ) {
				$s_id_int = intval( $s_id );
				$thumb = wp_get_attachment_image_src( $s_id_int, 'medium_large' );
				if ( ! $thumb ) {
					$thumb = wp_get_attachment_image_src( $s_id_int, 'large' );
				}
				if ( ! $thumb ) {
					$thumb = wp_get_attachment_image_src( $s_id_int, 'full' );
				}
				$thumbnails[ $s_id ] = $thumb ? $thumb[0] : '';
			}
		}
		return $thumbnails;
	}
}

if ( ! function_exists( 'sf_rest_get_slider' ) ) {
	function sf_rest_get_slider( $request ) {
		$id = intval( $request['id'] );
		$slider = get_option( 'sf_slider_' . $id );
		if ( ! $slider ) {
			return new WP_Error( 'no_slider', 'Slider not found', array( 'status' => 404 ) );
		}

		// Dynamic flat migration if needed
		if ( function_exists( 'sf_migrate_flat_slider_data' ) ) {
			if ( isset( $slider['sf_slider_id'] ) && ! is_array( $slider['sf_slide_id'] ) ) {
				$slider = sf_migrate_flat_slider_data( $slider );
				update_option( 'sf_slider_' . $id, $slider );
			}
		}

		$slider['slide_thumbnails'] = sf_get_slider_thumbnails( isset( $slider['sf_slide_id'] ) ? $slider['sf_slide_id'] : array() );

		return rest_ensure_response( $slider );
	}
}

if ( ! function_exists( 'sf_rest_save_slider' ) ) {
	function sf_rest_save_slider( $request ) {
		$payload = $request->get_json_params();
		if ( ! isset( $payload['sf_slider_id'] ) ) {
			return new WP_Error( 'missing_id', 'Slider ID is required', array( 'status' => 400 ) );
		}

		$id = intval( $payload['sf_slider_id'] );
		$layout = sanitize_text_field( $payload['sf_slider_layout'] );
		$title = sanitize_text_field( $payload['sf_slider_title'] );

		if ( intval( $layout ) > 12 ) {
			return new WP_Error( 'sf_rest_forbidden', 'Layouts above 12 require Slider Factory Pro.', array( 'status' => 403 ) );
		}

		// Start from the existing option so keys the UI does not manage
		// (e.g. sf_slider_desc, sf_height, legacy keys) are never silently lost.
		$existing = get_option( 'sf_slider_' . $id, array() );
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		$data = array(
			'sf_slider_id'     => $id,
			'sf_slider_layout' => $layout,
			'sf_slider_title'  => $title,
		);

		// Process Slide IDs (replace whole array so removals persist)
		if ( isset( $payload['sf_slide_id'] ) && is_array( $payload['sf_slide_id'] ) ) {
			$data['sf_slide_id'] = array_map( 'sanitize_text_field', $payload['sf_slide_id'] );
		} elseif ( isset( $existing['sf_slide_id'] ) ) {
			$data['sf_slide_id'] = $existing['sf_slide_id'];
		} else {
			$data['sf_slide_id'] = array();
		}

		// Metadata Keys (replace when sent; otherwise preserve existing)
		$slide_meta_keys = array(
			'sf_slide_title',
			'sf_slide_desc',
			'sf_slide_link_text_1',
			'sf_slide_link_1',
			'sf_slide_link_text_2',
			'sf_slide_link_2',
			'sf_slide_alt_text',
			'sf_slide_type',
			'sf_slide_videoType',
			'sf_slide_videoSrc',
			'sf_slide_custom_cover',
		);

		foreach ( $slide_meta_keys as $meta_key ) {
			if ( isset( $payload[ $meta_key ] ) && is_array( $payload[ $meta_key ] ) ) {
				$data[ $meta_key ] = array();
				foreach ( $payload[ $meta_key ] as $slide_id => $val ) {
					$s_id = intval( $slide_id );
					if ( $meta_key === 'sf_slide_link_1' || $meta_key === 'sf_slide_link_2' ) {
						$data[ $meta_key ][ $s_id ] = esc_url_raw( $val );
					} elseif ( $meta_key === 'sf_slide_desc' ) {
						$data[ $meta_key ][ $s_id ] = sanitize_textarea_field( $val );
					} else {
						$data[ $meta_key ][ $s_id ] = sanitize_text_field( $val );
					}
				}
			} elseif ( isset( $existing[ $meta_key ] ) ) {
				$data[ $meta_key ] = $existing[ $meta_key ];
			}
		}

		// Process Layout Options: accept ONLY the FREE whitelist keys for this slider's layout.
		// This mirrors the React dashboard's `freeKeys`, so PRO-locked options (navigation,
		// custom CSS, colors, speeds, etc.) can never be saved through the Free REST API.
		$sf_free_layout_keys = array(
			1  => array( 'sf_1_width', 'sf_1_height', 'sf_1_auto_play', 'sf_1_sorting' ),
			2  => array( 'sf_2_width', 'sf_2_height', 'sf_2_sorting' ),
			3  => array( 'sf_3_width', 'sf_3_height', 'sf_3_auto_play', 'sf_3_sorting' ),
			4  => array( 'sf_4_width', 'sf_4_height', 'sf_4_auto_play', 'sf_4_sorting' ),
			5  => array( 'sf_5_width', 'sf_5_height', 'sf_5_auto_play', 'sf_5_sorting' ),
			6  => array( 'sf_6_width', 'sf_6_height', 'sf_6_auto_play', 'sf_6_sorting' ),
			7  => array( 'sf_7_width', 'sf_7_height', 'sf_7_slide_circle_size', 'sf_7_inner_circle_size', 'sf_7_auto_play', 'sf_7_sorting' ),
			8  => array( 'sf_8_width', 'sf_8_height', 'sf_8_responsive', 'sf_8_sorting' ),
			9  => array( 'sf_9_width', 'sf_9_height', 'sf_9_auto_play', 'sf_9_sorting' ),
			10 => array( 'sf_10_width', 'sf_10_height', 'sf_10_btnBgColor', 'sf_10_btnTextColor', 'sf_10_sorting' ),
			11 => array( 'sf_11_width', 'sf_11_height', 'sf_11_sorting' ),
			12 => array( 'sf_12_width', 'sf_12_height', 'sf_12_sorting' ),
		);
		$layout_int    = intval( $layout );
		$allowed_keys  = isset( $sf_free_layout_keys[ $layout_int ] ) ? $sf_free_layout_keys[ $layout_int ] : array();
		foreach ( $payload as $key => $val ) {
			if ( in_array( $key, $allowed_keys, true ) ) {
				$data[ $key ] = sanitize_text_field( $val );
			} elseif ( $key === 'sf_height' ) {
				$data[ $key ] = sanitize_text_field( $val );
			}
		}

		// Merge with existing: keep unmanaged keys (sf_slider_desc, legacy, custom, etc.).
		$data = array_merge( $existing, $data );

		// Drop any foreign-layout keys that accumulated in the option (pollution cleanup).
		foreach ( array_keys( $data ) as $key ) {
			if ( preg_match( '/^sf_(\d+)_/', $key, $m ) && intval( $m[1] ) !== $layout_int ) {
				unset( $data[ $key ] );
			}
		}

		update_option( 'sf_slider_' . $id, $data );
		$data['slide_thumbnails'] = sf_get_slider_thumbnails( $data['sf_slide_id'] );

		return rest_ensure_response( array( 'success' => true, 'slider' => $data ) );
	}
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'slider-factory/v1', '/sliders/(?P<id>\d+)', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'sf_rest_get_slider',
		'permission_callback' => 'sf_rest_permissions_check',
	) );

	register_rest_route( 'slider-factory/v1', '/sliders/save/', array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'sf_rest_save_slider',
		'permission_callback' => 'sf_rest_permissions_check',
	) );
} );
