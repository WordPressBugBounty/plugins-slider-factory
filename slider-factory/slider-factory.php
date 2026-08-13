<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin Name:       Slider Factory
 * Plugin URI:        https://wpfrank.com/
 * Description:       Slider factory provides multiple slider layouts in single dashboard.
 * Version:           1.4.5
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            FARAZFRANK
 * Author URI:        https://profiles.wordpress.org/farazfrank/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       slider-factory
 * Domain Path:       /languages

Slider Factory Premium is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Slider Factory Premium is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Slider Factory Premium. If not, see https://wpfrank.com/.
 */

// SF activation
function wpfrank_sf_activation() {
	// update current plugin version
	if ( is_admin() ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$wpfrank_sf_plugin_data = get_plugin_data( __FILE__ );

		if ( isset( $wpfrank_sf_plugin_data['Version'] ) ) {
			$wpfrank_sf_plugin_version = $wpfrank_sf_plugin_data['Version'];
			update_option( 'wpfrank_sf_current_version', $wpfrank_sf_plugin_version );
		}
	}
}
register_activation_hook( __FILE__, 'wpfrank_sf_activation' );

// SF deactivation
function wpfrank_sf_deactivation() {
	// update last active plugin version
	$wpfrank_sf_last_version = get_option( 'wpfrank_sf_current_version' );
	if ( $wpfrank_sf_last_version !== '' ) {
		update_option( 'wpfrank_sf_last_version', $wpfrank_sf_last_version );
	}
}
register_deactivation_hook( __FILE__, 'wpfrank_sf_deactivation' );

// SF uninstall
function wpfrank_sf_uninstall() {
	global $wpdb;
	$slider_key_prefix = 'sf_slider_';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$all_sliders_rows = $wpdb->get_results(
		$wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE %s", '%' . $wpdb->esc_like( $slider_key_prefix ) . '%' )
	);
	foreach ( $all_sliders_rows as $row ) {
		delete_option( $row->option_name );
	}
	delete_option( 'wpfrank_sf_current_version' );
	delete_option( 'wpfrank_sf_last_version' );
}
register_uninstall_hook( __FILE__, 'wpfrank_sf_uninstall' );

// load translation
function wpfrank_sf_load_translation() {
	$sf_locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
	$sf_mofile = plugin_dir_path( __FILE__ ) . 'languages/slider-factory-' . $sf_locale . '.mo';
	if ( file_exists( $sf_mofile ) ) {
		load_textdomain( 'slider-factory', $sf_mofile );
	} else {
		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound
		load_plugin_textdomain( 'slider-factory', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}
add_action( 'plugins_loaded', 'wpfrank_sf_load_translation' );

// SF
function wpfrank_sf_menu_page() {
	// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	add_menu_page( __( 'Slider Factory', 'slider-factory' ), __( 'Slider Factory', 'slider-factory' ), 'manage_options', 'sf-slider-factory', 'wpfrank_sf_main', 'dashicons-format-gallery', 65 );
	// add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', int $position )
	add_submenu_page( null, __( 'Manage Slider', 'slider-factory' ), __( 'Manage Slider', 'slider-factory' ), 'manage_options', 'sf-manage-slider', 'wpfrank_sf_manage_slider' );
}
add_action( 'admin_menu', 'wpfrank_sf_menu_page' );

// SF main page body
function wpfrank_sf_main() {
	require 'admin/all-sliders.php';
}

// SF sub menu for managing slider create and update
function wpfrank_sf_manage_slider() {
	require 'admin/manage-slider.php';
}

// Fix PHP 8.1+ deprecation warning when accessing hidden admin menu page (parent_slug = null)
function wpfrank_sf_fix_admin_title() {
	$screen = get_current_screen();
	if ( $screen && false !== strpos( $screen->id, 'sf-manage-slider' ) ) {
		global $title;
		if ( null === $title || '' === $title ) {
			$title = __( 'Manage Slider', 'slider-factory' );
		}
	}
}
add_action( 'current_screen', 'wpfrank_sf_fix_admin_title' );

// Migrate old flat-format slider data to nested arrays.
if ( ! function_exists( 'sf_migrate_flat_slider_data' ) ) {
	function sf_migrate_flat_slider_data( $slider ) {
		if ( ! is_array( $slider ) ) {
			return array();
		}
		// Collect all attachment IDs from flat keys like "sf_slide_id{N}"
		$slide_ids = array();
		foreach ( array_keys( $slider ) as $key ) {
			if ( preg_match( '/^sf_slide_id(\d+)$/', $key, $m ) ) {
				$slide_ids[] = intval( $m[1] );
			}
		}
		if ( empty( $slide_ids ) ) {
			return $slider;
		}

		$flat_prefixes = array(
			'sf_slide_title',
			'sf_slide_desc',
			'sf_slide_type',
			'sf_slide_videoType',
			'sf_slide_videoSrc',
			'sf_slide_link_text_1',
			'sf_slide_link_1',
			'sf_slide_link_text_2',
			'sf_slide_link_2',
			'sf_slide_alt_text',
		);

		$slider['sf_slide_id'] = array();
		foreach ( $slide_ids as $id ) {
			$slider['sf_slide_id'][ $id ] = (string) $id;
		}

		foreach ( $flat_prefixes as $prefix ) {
			$slider[ $prefix ] = array();
			foreach ( $slide_ids as $id ) {
				$flat_key = $prefix . $id;
				$slider[ $prefix ][ $id ] = isset( $slider[ $flat_key ] ) ? $slider[ $flat_key ] : '';
			}
		}

		$keys_to_remove = array();
		foreach ( array_keys( $slider ) as $key ) {
			foreach ( $flat_prefixes as $prefix ) {
				if ( strpos( $key, $prefix ) === 0 && $key !== $prefix ) {
					$keys_to_remove[] = $key;
					break;
				}
			}
			if ( preg_match( '/^sf_slide_id\d+$/', $key ) ) {
				$keys_to_remove[] = $key;
			}
		}
		$slider = array_diff_key( $slider, array_flip( $keys_to_remove ) );

		return $slider;
	}
}

// One-time bulk migration of legacy flat-format slider options (runs on admin init once).
add_action( 'admin_init', function () {
	if ( get_option( 'sf_flat_data_migrated_v1' ) ) {
		return;
	}
	if ( ! function_exists( 'sf_migrate_flat_slider_data' ) ) {
		return;
	}

	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$rows = $wpdb->get_results(
		$wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", 'sf_slider_%' )
	);

	foreach ( $rows as $row ) {
		$slider = get_option( $row->option_name );
		if ( ! is_array( $slider ) || ! isset( $slider['sf_slider_id'] ) ) {
			continue;
		}
		if ( isset( $slider['sf_slide_id'] ) && is_array( $slider['sf_slide_id'] ) ) {
			continue; // Already in nested format.
		}
		$migrated = sf_migrate_flat_slider_data( $slider );
		if ( $migrated !== $slider ) {
			update_option( $row->option_name, $migrated );
		}
	}

	update_option( 'sf_flat_data_migrated_v1', 1 );
} );

// enqueue admin scripts
function wpfrank_sf_admin_scripts() {
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_GET['page'] ) ) {
			// load plugin required CSS and JS only on plugin pages
			$sf_current_page_slug = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			
			if ( $sf_current_page_slug === 'sf-slider-factory' || $sf_current_page_slug === 'sf-manage-slider' ) {
				wp_enqueue_media();
				// CSS for React Dashboard
				$sf_dash_css_path = plugin_dir_path( __FILE__ ) . 'admin/assets/dist/dashboard.css';
				$sf_dash_js_path  = plugin_dir_path( __FILE__ ) . 'admin/assets/dist/dashboard.js';
				$sf_dash_css_version = file_exists( $sf_dash_css_path ) ? filemtime( $sf_dash_css_path ) : '1.4.3';
				$sf_dash_js_version  = file_exists( $sf_dash_js_path ) ? filemtime( $sf_dash_js_path ) : '1.4.3';
				if ( file_exists( $sf_dash_css_path ) ) {
					wp_enqueue_style( 'sf-react-dashboard-css', plugin_dir_url( __FILE__ ) . 'admin/assets/dist/dashboard.css', array(), $sf_dash_css_version );
				}
				if ( file_exists( $sf_dash_js_path ) ) {
					wp_enqueue_script( 'sf-react-dashboard-js', plugin_dir_url( __FILE__ ) . 'admin/assets/dist/dashboard.js', array(), $sf_dash_js_version, true );
				}

				// Fetch active sliders to hydrate React app initial state
				global $wpdb;
				$slider_key_prefix = 'sf_slider_';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$all_sliders_rows = $wpdb->get_results(
					$wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE %s ORDER BY option_id ASC", '%' . $wpdb->esc_like( $slider_key_prefix ) . '%' )
				);
				$sliders_data = array();
				foreach ( $all_sliders_rows as $row ) {
					$slider_option_name = $row->option_name;
					$underscore_pos     = strrpos( $slider_option_name, '_' );
					$slider_id          = substr( $slider_option_name, ( $underscore_pos + 1 ) );
					$slider_opt         = get_option( 'sf_slider_' . $slider_id );
					if ( $slider_opt ) {
						$sliders_data[] = array(
							'sf_slider_id'     => isset( $slider_opt['sf_slider_id'] ) ? $slider_opt['sf_slider_id'] : $slider_id,
							'sf_slider_title'  => isset( $slider_opt['sf_slider_title'] ) ? $slider_opt['sf_slider_title'] : '',
							'sf_slider_layout' => isset( $slider_opt['sf_slider_layout'] ) ? $slider_opt['sf_slider_layout'] : '1'
						);
					}
				}

				$active_slider = null;
				if ( 'sf-manage-slider' === $sf_current_page_slug ) {
					$sf_action = isset( $_GET['sf-slider-action'] ) ? sanitize_text_field( wp_unslash( $_GET['sf-slider-action'] ) ) : '';
					if ( 'edit' === $sf_action && isset( $_GET['sf-slider-id'] ) ) {
						$active_id = intval( $_GET['sf-slider-id'] );
						$active_slider = get_option( 'sf_slider_' . $active_id );
						if ( $active_slider ) {
							if ( function_exists( 'sf_migrate_flat_slider_data' ) && isset( $active_slider['sf_slider_id'] ) && ! is_array( $active_slider['sf_slide_id'] ) ) {
								$active_slider = sf_migrate_flat_slider_data( $active_slider );
							}
							$active_slider['slide_thumbnails'] = array();
							if ( isset( $active_slider['sf_slide_id'] ) && is_array( $active_slider['sf_slide_id'] ) ) {
								foreach ( $active_slider['sf_slide_id'] as $s_id ) {
									$s_id_int = intval( $s_id );
									$thumb    = wp_get_attachment_image_src( $s_id_int, 'medium_large' );
									if ( ! $thumb ) {
										$thumb = wp_get_attachment_image_src( $s_id_int, 'large' );
									}
									if ( ! $thumb ) {
										$thumb = wp_get_attachment_image_src( $s_id_int, 'full' );
									}
									$active_slider['slide_thumbnails'][ $s_id ] = $thumb ? $thumb[0] : '';
								}
							}
						}
					} elseif ( 'create' === $sf_action ) {
						if ( ! isset( $_GET['sf-create-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['sf-create-nonce'] ) ), 'sf-create-nonce' ) ) {
							wp_die( esc_html__( 'Nonce not verified.', 'slider-factory' ) );
						}
						$new_id = get_sf_slider_id();
						$active_slider = array(
							'sf_slider_id'     => $new_id,
							'sf_slider_layout' => isset( $_GET['sf-slider-layout'] ) ? sanitize_text_field( wp_unslash( $_GET['sf-slider-layout'] ) ) : '1',
							'sf_slider_title'  => 'New Slider',
							'sf_slide_id'      => array(),
						);
					}
				}

				wp_localize_script( 'sf-react-dashboard-js', 'sfDashboardData', array(
					'sliders'      => $sliders_data,
					'createNonce'  => wp_create_nonce( 'sf-create-nonce' ),
					'editNonce'    => wp_create_nonce( 'sf-edit-nonce' ),
					'cloneNonce'   => wp_create_nonce( 'sf-clone-slider' ),
					'removeNonce'  => wp_create_nonce( 'sf-remove-slider' ),
					'previewNonce' => ( ! empty( $active_slider['sf_slider_id'] ) ) ? wp_create_nonce( 'sf-preview-' . $active_slider['sf_slider_id'] ) : wp_create_nonce( 'sf-preview-nonce' ),
					'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
					'createUrl'    => admin_url( 'admin.php?page=sf-manage-slider&sf-slider-action=create' ),
					'editUrl'      => admin_url( 'admin.php?page=sf-manage-slider&sf-slider-action=edit' ),
					'isPro'        => false,
					'version'      => get_option( 'wpfrank_sf_current_version', '1.4.3' ),
					'activeSlider' => $active_slider,
					'restUrl'      => esc_url_raw( rest_url( 'slider-factory/v1' ) ),
					'restNonce'    => wp_create_nonce( 'wp_rest' ),
					'i18n'         => array(
						'Overview'                   => __( 'Overview', 'slider-factory' ),
						'All Sliders'                => __( 'All Sliders', 'slider-factory' ),
						'Layout Presets'             => __( 'Layout Presets', 'slider-factory' ),
						'Docs & Help'                => __( 'Docs & Help', 'slider-factory' ),
						'Free vs Pro'                => __( 'Free vs Pro', 'slider-factory' ),
						'Get Pro'                    => __( 'Get Pro', 'slider-factory' ),
						'New Slider'                 => __( '+ New Slider', 'slider-factory' ),
						'Total Sliders Created'      => __( 'TOTAL SLIDERS CREATED', 'slider-factory' ),
						'Active Across Posts'        => __( 'Active across posts, pages, or sidebar widgets.', 'slider-factory' ),
						'Available Layouts'          => __( 'AVAILABLE LAYOUTS', 'slider-factory' ),
						'PreConfigured Templates'    => __( 'Pre-configured templates ready for insertion.', 'slider-factory' ),
						'Support Status'             => __( 'SUPPORT STATUS', 'slider-factory' ),
						'Standard Plan'              => __( 'Standard Plan', 'slider-factory' ),
						'Free Community Support'     => __( 'Free community support.', 'slider-factory' ),
						'Get Help'                   => __( 'Get Help', 'slider-factory' ),
						'Recent Sliders'             => __( 'RECENT SLIDERS', 'slider-factory' ),
						'View All'                   => __( 'View All', 'slider-factory' ),
						'TITLE'                      => __( 'TITLE', 'slider-factory' ),
						'LAYOUT'                     => __( 'LAYOUT', 'slider-factory' ),
						'SHORTCODE'                  => __( 'SHORTCODE', 'slider-factory' ),
						'ACTIONS'                    => __( 'ACTIONS', 'slider-factory' ),
						'Copy'                       => __( 'Copy', 'slider-factory' ),
						'Edit'                       => __( 'Edit', 'slider-factory' ),
						'Delete'                     => __( 'Delete', 'slider-factory' ),
						'Back to Dashboard'          => __( 'Back to Dashboard', 'slider-factory' ),
						'Save & Close'               => __( 'Save & Close', 'slider-factory' ),
						'Add Images'                 => __( 'Add Images', 'slider-factory' ),
						'Remove All'                 => __( 'Remove All', 'slider-factory' ),
						'No slides added yet'        => __( 'No slides added yet', 'slider-factory' ),
						'Select Media Library'       => __( 'Select images from your WordPress media library to start building your slider carousel.', 'slider-factory' ),
						'Select Media Library Images' => __( 'Select Media Library Images', 'slider-factory' ),
						'SETTINGS'                   => __( 'SETTINGS', 'slider-factory' ),
						'Dimensions'                 => __( 'Dimensions', 'slider-factory' ),
						'Width'                      => __( 'Width', 'slider-factory' ),
						'Container Height'           => __( 'Container Height', 'slider-factory' ),
						'Design & Typography'        => __( 'Design & Typography', 'slider-factory' ),
						'Playback & Animation'       => __( 'Playback & Animation', 'slider-factory' ),
						'Navigation'                 => __( 'Navigation', 'slider-factory' ),
						'Behavior'                   => __( 'Behavior', 'slider-factory' ),
						'Custom CSS'                 => __( 'Custom CSS', 'slider-factory' ),
						'Reset Defaults'             => __( 'Reset Defaults', 'slider-factory' ),
						'Save Changes'               => __( 'Save Changes', 'slider-factory' ),
						'Info'                       => __( 'Info', 'slider-factory' ),
						'Demo'                       => __( 'Demo', 'slider-factory' ),
						'Create'                     => __( 'Create', 'slider-factory' ),
						'PRO'                        => __( 'PRO', 'slider-factory' ),
						'Select All'                 => __( 'Select All', 'slider-factory' ),
						'Delete Selected'            => __( 'Delete Selected', 'slider-factory' ),
						'No Sliders Created Yet'     => __( 'No Sliders Created Yet', 'slider-factory' ),
						'Create First Slider'        => __( 'Create your first slider to display beautiful galleries or carousels on your website.', 'slider-factory' ),
						'Sticky'                     => __( 'Sticky', 'slider-factory' ),
						'Refresh'                    => __( 'Refresh', 'slider-factory' ),
						'SETTINGS'                   => __( 'SETTINGS', 'slider-factory' ),
						'SLIDES'                     => __( 'SLIDES', 'slider-factory' ),
						'Drag cards text'            => __( 'Drag cards or use arrow buttons to reorder', 'slider-factory' ),
						'Close'                      => __( 'Close', 'slider-factory' ),
						'Create Slider'              => __( 'Create Slider', 'slider-factory' ),
						'Unlock'                     => __( 'Unlock', 'slider-factory' ),
						'Unlock in Pro'              => __( 'Unlock in Pro', 'slider-factory' ),
						'Are you absolutely sure?'   => __( 'Are you absolutely sure?', 'slider-factory' ),
						'This action cannot be undone slider' => __( 'This action cannot be undone. It will permanently delete the slider and remove it from any pages using its shortcode.', 'slider-factory' ),
						'Cancel'                     => __( 'Cancel', 'slider-factory' ),
						'Delete Slider'              => __( 'Delete Slider', 'slider-factory' ),
						'Deleting...'                => __( 'Deleting...', 'slider-factory' ),
						'Delete Selected Sliders Question' => __( 'Delete selected slider(s)?', 'slider-factory' ),
						'This will permanently delete all selected sliders' => __( 'This will permanently delete all selected sliders. This action cannot be undone.', 'slider-factory' ),
						'Confirm'                    => __( 'Confirm', 'slider-factory' ),
						'Are you sure reset defaults' => __( 'Are you sure you want to reset slider settings to layout defaults?', 'slider-factory' ),
						'built-in features'          => __( 'built-in features', 'slider-factory' ),
						'3 Design Presets'           => __( '3 Design Presets', 'slider-factory' ),
						'Auto Play with Transition Speed' => __( 'Auto Play with Transition Speed', 'slider-factory' ),
						'Slider Navigation Thumbnails (ON/OFF)' => __( 'Slider Navigation Thumbnails (ON/OFF)', 'slider-factory' ),
						'Slide Navigation Arrows (ON/OFF)' => __( 'Slide Navigation Arrows (ON/OFF)', 'slider-factory' ),
						'Slider Pagination Dots (ON/OFF)' => __( 'Slider Pagination Dots (ON/OFF)', 'slider-factory' ),
						'Slide Title'                => __( 'Slide Title', 'slider-factory' ),
						'Slide Description'          => __( 'Slide Description', 'slider-factory' ),
						'Infinite Scroll (ON/OFF)'   => __( 'Infinite Scroll (ON/OFF)', 'slider-factory' ),
						'Transition Effects'         => __( 'Transition Effects', 'slider-factory' ),
						'RTL Support'                => __( 'RTL Support', 'slider-factory' ),
						'Sorting Order By Id and Title' => __( 'Sorting Order By Id and Title', 'slider-factory' ),
						'Carousel Slideshow'         => __( 'Carousel Slideshow', 'slider-factory' ),
						'PhotoRoller'                => __( 'PhotoRoller', 'slider-factory' ),
						'Accordion Slider'           => __( 'Accordion Slider', 'slider-factory' ),
						'Camera Master'              => __( 'Camera Master', 'slider-factory' ),
						'Cover Flow'                 => __( 'Cover Flow', 'slider-factory' ),
						'Carousel Wipe'              => __( 'Carousel Wipe', 'slider-factory' ),
						'Rotating Slider'            => __( 'Rotating Slider', 'slider-factory' ),
						'Infinite Scroll'            => __( 'Infinite Scroll', 'slider-factory' ),
						'Photo View Slider'          => __( 'Photo View Slider', 'slider-factory' ),
						'Snap Page Slider'           => __( 'Snap Page Slider', 'slider-factory' ),
						'Product Slider'             => __( 'Product Slider', 'slider-factory' ),
						'Before-After Compare'       => __( 'Before-After Compare', 'slider-factory' ),
						'Getting Started Guide'      => __( 'Getting Started Guide', 'slider-factory' ),
						'Everything you need'        => __( 'Everything you need to embed and style sliders.', 'slider-factory' ),
						'Choose a Layout'            => __( 'Choose a Layout', 'slider-factory' ),
						'Choose layout body'         => __( 'Head to Layouts, find a responsive slide format that matches your content, and click Create.', 'slider-factory' ),
						'Upload Slide Assets'        => __( 'Upload Slide Assets', 'slider-factory' ),
						'Upload assets body'         => __( 'Add titles, descriptions, links, and select images directly from the WordPress media library.', 'slider-factory' ),
						'Insert Shortcode'           => __( 'Insert Shortcode', 'slider-factory' ),
						'Insert shortcode body'      => __( 'Copy the shortcode (e.g. [sf id=1 layout=2]) into Elementor, Gutenberg, Divi, or your PHP templates.', 'slider-factory' ),
						'Read Documentation'         => __( 'Read Documentation', 'slider-factory' ),
						'Video Documentation'        => __( 'Video Documentation', 'slider-factory' ),
						'A visual walkthrough'       => __( 'A visual walkthrough showing how to customize options.', 'slider-factory' ),
						'Need priority help'         => __( 'Need priority help? Premium users get dedicated support.', 'slider-factory' ),
						'Active Sliders'             => __( 'Active Sliders', 'slider-factory' ),
						'Manage copy shortcodes'     => __( 'Manage, copy shortcodes, or delete saved sliders.', 'slider-factory' ),
						'Search sliders...'          => __( 'Search sliders...', 'slider-factory' ),
						'No Sliders Found'           => __( 'No Sliders Found', 'slider-factory' ),
						'Adjust your search'         => __( 'Adjust your search or create a new slider layout.', 'slider-factory' ),
					),
				) );
			}

			if ( strpos( $sf_current_page_slug, 'sf-' ) !== false ) {
				// CSS for builder submenus — FontAwesome & our design-system stylesheet.
				$sf_admin_css = plugin_dir_path( __FILE__ ) . 'admin/assets/css/style.css';
				wp_enqueue_style( 'sf-fontawesome-css', plugin_dir_url( __FILE__ ) . 'admin/assets/fontawesome-free-6.5.1-web/css/all.min.css', array(), '6.5.1' );
				wp_enqueue_style( 'sf-admin-style-css', plugin_dir_url( __FILE__ ) . 'admin/assets/css/style.css', array(), filemtime( $sf_admin_css ) );

				// JS for builder submenus
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'media-upload' );
				wp_enqueue_media();
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				$sf_uploader_js_path = plugin_dir_path( __FILE__ ) . 'admin/assets/js/sf-uploader.js';
				$sf_uploader_version = file_exists( $sf_uploader_js_path ) ? filemtime( $sf_uploader_js_path ) : '1.4.3';
				wp_enqueue_script( 'sf-uploader-js', plugin_dir_url( __FILE__ ) . 'admin/assets/js/sf-uploader.js', array( 'jquery' ), $sf_uploader_version, true );
			}
		}
	} // current_user_can end
}
add_action( 'admin_enqueue_scripts', 'wpfrank_sf_admin_scripts' );

// Make the React dashboard script load as an ES module (import.meta.url for images).
add_filter( 'script_loader_tag', function( $tag, $handle ) {
	if ( 'sf-react-dashboard-js' === $handle ) {
		$tag = str_replace( '<script ', '<script type="module" ', $tag );
	}
	return $tag;
}, 10, 2 );

// 1. Get / Create next slider id
if ( ! function_exists( 'get_sf_slider_id' ) ) {
	function get_sf_slider_id() {
		if ( current_user_can( 'manage_options' ) ) {
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$existing = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", 'sf_slider_%' ) );
			$max_id   = 0;
			if ( is_array( $existing ) ) {
				foreach ( $existing as $name ) {
					$parts = explode( '_', $name );
					$id    = (int) end( $parts );
					if ( $id > $max_id ) {
						$max_id = $id;
					}
				}
			}
			$next_id = $max_id + 1;
			while ( get_option( 'sf_slider_' . $next_id ) !== false ) {
				$next_id++;
			}
			return $next_id;
		}
		return 1;
	}
}

// Get user friendly layout name by ID
if ( ! function_exists( 'get_sf_layout_name' ) ) {
	function get_sf_layout_name( $layout_id ) {
		$layouts = array(
			1  => 'Carousel Slideshow',
			2  => 'PhotoRoller',
			3  => 'Accordion Slider',
			4  => 'Camera Master',
			5  => 'Cover Flow',
			6  => 'Carousel Wipe',
			7  => 'Rotating Slider',
			8  => 'Infinite Scroll',
			9  => 'Photo View Slider',
			10 => 'Snap Page Slider',
			11 => 'Product Slider',
			12 => 'Before-After Compare',
			13 => 'Video Slider',
			14 => 'Slidewiz',
			15 => 'Desoslide',
			16 => 'Adapto Full-Page',
			17 => 'Cube Slider',
			18 => 'Horizontal Hover Slider',
			19 => 'Accordion Slider',
			20 => 'Grid Accordion Slider',
			21 => 'Flux Slider',
		);
		return isset( $layouts[ $layout_id ] ) ? $layouts[ $layout_id ] : 'Layout ' . $layout_id;
	}
}


// 2. add slide images to the slider start
function wpfrank_sf_li_generate_ajax_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'slider-factory' ) );
	}
	if ( ! isset( $_POST['sf_upload_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sf_upload_nonce'] ) ), 'sf-upload-nonce' ) ) {
		wp_die( esc_html__( 'Nonce not verified.', 'slider-factory' ) );
	}
	if ( isset( $_POST['sf_attachment_id'] ) && isset( $_POST['sf_slider_id'] ) ) {
				$attachment_id = sanitize_text_field( wp_unslash( $_POST['sf_attachment_id'] ) );
				$layout_num    = isset( $_POST['layout_num'] ) ? sanitize_text_field( wp_unslash( $_POST['layout_num'] ) ) : '1';
				
				// load slider from DB for pre-population
				$slider = array();
				if ( isset( $_POST['sf_slider_id'] ) ) {
					$sf_slider_post_id = sanitize_text_field( wp_unslash( $_POST['sf_slider_id'] ) );
					$loaded = get_option( 'sf_slider_' . $sf_slider_post_id );
					if ( is_array( $loaded ) ) {
						$slider = $loaded;
					}
				}
				
				// defaults
				$sf_slide_title = $sf_slide_alt = $sf_slide_descs = $sf_slide_thumbnail = '';
				
				// load values
				$sf_slide_title     = get_the_title( $attachment_id );
				$sf_slide_alt       = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
				$sf_slide_thumbnail = wp_get_attachment_image_src( $attachment_id, 'large', true );
				$attachment         = get_post( $attachment_id );
				$sf_slide_descs     = $attachment->post_content;

				$id = $attachment_id;
				$sf_slide_type = '';
				$sf_slide_videoType = '';
				$sf_slide_videoSrc = '';
				$sf_slide_link_text_1 = '';
				$sf_slide_link_1 = '';
				$sf_slide_link_text_2 = '';
				$sf_slide_link_2 = '';
				?>
				<!-- Visual slide card (AJAX-added) — image + overlay icons; fields live in hidden container -->
				<div class="sf-slide-card-wrapper sf_slide_<?php echo esc_attr( $attachment_id ); ?> image_info_tile" data-position="<?php echo esc_attr( $attachment_id ); ?>" data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
					<div class="sf-slide-card" title="<?php esc_attr_e( 'Drag to reorder', 'slider-factory' ); ?>">
						<img class="sf-slide-card-img" src="<?php echo esc_url( $sf_slide_thumbnail[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_alt ); ?>">
						<div class="sf-slide-card-top">
							<span class="sf-slide-card-grip" onclick="event.stopPropagation();" title="<?php esc_attr_e( 'Drag to reorder', 'slider-factory' ); ?>"><i class="fas fa-grip-vertical"></i></span>
							<span class="sf-slide-card-index" data-slide-index>#</span>
						</div>
						<div class="sf-slide-card-actions">
							<button type="button" class="sf-slide-card-act sf-slide-edit" onclick="return SFopenSlideModal('<?php echo esc_attr( $attachment_id ); ?>');" title="<?php esc_attr_e( 'Edit slide details', 'slider-factory' ); ?>"><i class="fas fa-pen"></i> <?php esc_html_e( 'Edit', 'slider-factory' ); ?></button>
							<button type="button" class="sf-slide-card-act sf-slide-del" onclick="return SFremoveSlide('<?php echo esc_attr( $attachment_id ); ?>');" title="<?php esc_attr_e( 'Remove slide', 'slider-factory' ); ?>"><i class="fas fa-trash"></i></button>
						</div>
					</div>

					<!-- Hidden real form inputs — collected by jQuery .serialize() on save -->
					<div class="sf-slide-fields-hidden">
						<input type="text" class="form-control sf_slide_id" name="sf_slide_id[<?php echo esc_attr( $attachment_id ); ?>]" value="<?php echo esc_attr( $attachment_id ); ?>" readonly>
						<input type="text" class="form-control sf_slide_title" name="sf_slide_title[<?php echo esc_attr( $attachment_id ); ?>]" value="<?php echo esc_attr( $sf_slide_title ); ?>">

						<?php if ( ( $layout_num == 13 ) || ( $layout_num == 19 ) || ( $layout_num == 20 ) ) { ?>
						<select class="form-select sf_slide_type fas" name="sf_slide_type[<?php echo esc_attr( $attachment_id ); ?>]" aria-label="sf-slide-type">
							<option value="image" selected>Image</option>
							<option value="video">Video</option>
						</select>
						<select class="form-select sf_slide_videoType" name="sf_slide_videoType[<?php echo esc_attr( $attachment_id ); ?>]" aria-label="sf-slide-videoType">
							<option value="youtube" selected>Youtube</option>
							<option value="vimeo">Vimeo</option>
							<option value="internal">Internal</option>
						</select>
						<input type="text" class="form-control sf_slide_videoSrc" name="sf_slide_videoSrc[<?php echo esc_attr( $attachment_id ); ?>]" value="<?php echo esc_attr( $sf_slide_videoSrc ); ?>">
						<?php } ?>

						<textarea class="form-control sf_slide_desc" name="sf_slide_desc[<?php echo esc_attr( $attachment_id ); ?>]" rows="2"><?php echo esc_textarea( $sf_slide_descs ); ?></textarea>
						<input type="text" class="form-control sf_slide_link_text_1" name="sf_slide_link_text_1[<?php echo esc_attr( $attachment_id ); ?>]" value="<?php echo esc_attr( $sf_slide_link_text_1 ); ?>">
						<input type="text" class="form-control sf_slide_link_1" name="sf_slide_link_1[<?php echo esc_attr( $attachment_id ); ?>]" value="<?php echo esc_attr( $sf_slide_link_1 ); ?>">
						<input type="text" class="form-control sf_slide_link_text_2" name="sf_slide_link_text_2[<?php echo esc_attr( $attachment_id ); ?>]" value="<?php echo esc_attr( $sf_slide_link_text_2 ); ?>">
						<input type="text" class="form-control sf_slide_link_2" name="sf_slide_link_2[<?php echo esc_attr( $attachment_id ); ?>]" value="<?php echo esc_attr( $sf_slide_link_2 ); ?>">
						<input type="text" class="form-control sf_slide_alt_text" name="sf_slide_alt_text[<?php echo esc_attr( $attachment_id ); ?>]" value="<?php echo esc_attr( $sf_slide_alt ); ?>">
					</div>
				</div>
				<?php
				wp_die();
			}
	}
	add_action( 'wp_ajax_sf_image_id', 'wpfrank_sf_li_generate_ajax_callback' );
// 2. add slide images to the slider end

// 3. save slider start
function wpfrank_sf_save_slider_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'slider-factory' ) );
		}
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'save-slider' ) ) {
			wp_die( esc_html__( 'Nonce not verified.', 'slider-factory' ) );
		}
		// verified action
			// slider info
			$sf_slider_id     = isset( $_POST['sf_slider_id'] ) ? intval( $_POST['sf_slider_id'] ) : 0;
			if ( $sf_slider_id <= 0 ) {
				wp_die( esc_html__( 'Invalid Slider ID.', 'slider-factory' ) );
			}
			$sf_slider_layout = isset( $_POST['sf_slider_layout'] ) ? intval( $_POST['sf_slider_layout'] ) : 1;
			if ( $sf_slider_layout <= 0 ) {
				$sf_slider_layout = 1;
			}
			$sf_slider_title  = isset( $_POST['sf_slider_title'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_slider_title'] ) ) : '';
			$sf_slider_info   = array(
				'sf_slider_id'     => $sf_slider_id,
				'sf_slider_layout' => $sf_slider_layout,
				'sf_slider_title'  => $sf_slider_title,
			);

			$sf_slide_ids = array();
			$raw_slide_ids = isset( $_POST['sf_slide_ids'] ) ? wp_unslash( $_POST['sf_slide_ids'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			parse_str( $raw_slide_ids, $sf_slide_ids );

			$sf_slide_titles = array();
			$raw_slide_titles = isset( $_POST['sf_slide_titles'] ) ? wp_unslash( $_POST['sf_slide_titles'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			parse_str( $raw_slide_titles, $sf_slide_titles );

			$sf_slide_descs = array();
			$raw_slide_descs = isset( $_POST['sf_slide_descs'] ) ? wp_unslash( $_POST['sf_slide_descs'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			parse_str( $raw_slide_descs, $sf_slide_descs );

			$sf_slide_alts_text = array();
			if ( isset( $_POST['sf_slide_alts_text'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				parse_str( wp_unslash( $_POST['sf_slide_alts_text'] ), $sf_slide_alts_text );
			}

			// sanitizing parsed value of $sf_slide_ids.
			if ( isset( $sf_slide_ids['sf_slide_id'] ) && is_array( $sf_slide_ids['sf_slide_id'] ) ) {
				foreach ( $sf_slide_ids['sf_slide_id'] as $key1 => $value1 ) {
					$sf_slide_ids['sf_slide_id'][ $key1 ] = sanitize_text_field( $value1 );
				}
			} else {
				$sf_slide_ids['sf_slide_id'] = array();
			}

			// sanitizing parsed value of $sf_slide_titles.
			if ( isset( $sf_slide_titles['sf_slide_title'] ) && is_array( $sf_slide_titles['sf_slide_title'] ) ) {
				foreach ( $sf_slide_titles['sf_slide_title'] as $key2 => $value2 ) {
					$sf_slide_titles['sf_slide_title'][ $key2 ] = sanitize_text_field( $value2 );
				}
			} else {
				$sf_slide_titles['sf_slide_title'] = array();
			}

			// sanitizing parsed value of $sf_slide_descs.
			if ( isset( $sf_slide_descs['sf_slide_desc'] ) && is_array( $sf_slide_descs['sf_slide_desc'] ) ) {
				foreach ( $sf_slide_descs['sf_slide_desc'] as $key3 => $value3 ) {
					$sf_slide_descs['sf_slide_desc'][ $key3 ] = sanitize_textarea_field( $value3 );
				}
			} else {
				$sf_slide_descs['sf_slide_desc'] = array();
			}

			// sanitizing parsed value of $sf_slide_alts_text.
			if ( isset( $sf_slide_alts_text['sf_slide_alt_text'] ) && is_array( $sf_slide_alts_text['sf_slide_alt_text'] ) ) {
				foreach ( $sf_slide_alts_text['sf_slide_alt_text'] as $key4 => $value4 ) {
					$sf_slide_alts_text['sf_slide_alt_text'][ $key4 ] = sanitize_text_field( $value4 );
				}
			} else {
				$sf_slide_alts_text['sf_slide_alt_text'] = array();
			}

			$sf_slide_link_texts_1 = array();
			if ( isset( $_POST['sf_slide_link_texts_1'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				parse_str( wp_unslash( $_POST['sf_slide_link_texts_1'] ), $sf_slide_link_texts_1 );
				if ( ! empty( $sf_slide_link_texts_1['sf_slide_link_text_1'] ) ) {
					foreach ( $sf_slide_link_texts_1['sf_slide_link_text_1'] as $k => $v ) {
						$sf_slide_link_texts_1['sf_slide_link_text_1'][ $k ] = sanitize_text_field( $v );
					}
				}
			}

			$sf_slide_links_1 = array();
			if ( isset( $_POST['sf_slide_links_1'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				parse_str( wp_unslash( $_POST['sf_slide_links_1'] ), $sf_slide_links_1 );
				if ( ! empty( $sf_slide_links_1['sf_slide_link_1'] ) ) {
					foreach ( $sf_slide_links_1['sf_slide_link_1'] as $k => $v ) {
						$sf_slide_links_1['sf_slide_link_1'][ $k ] = esc_url_raw( $v );
					}
				}
			}

			$sf_slide_link_texts_2 = array();
			if ( isset( $_POST['sf_slide_link_texts_2'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				parse_str( wp_unslash( $_POST['sf_slide_link_texts_2'] ), $sf_slide_link_texts_2 );
				if ( ! empty( $sf_slide_link_texts_2['sf_slide_link_text_2'] ) ) {
					foreach ( $sf_slide_link_texts_2['sf_slide_link_text_2'] as $k => $v ) {
						$sf_slide_link_texts_2['sf_slide_link_text_2'][ $k ] = sanitize_text_field( $v );
					}
				}
			}

			$sf_slide_links_2 = array();
			if ( isset( $_POST['sf_slide_links_2'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				parse_str( wp_unslash( $_POST['sf_slide_links_2'] ), $sf_slide_links_2 );
				if ( ! empty( $sf_slide_links_2['sf_slide_link_2'] ) ) {
					foreach ( $sf_slide_links_2['sf_slide_link_2'] as $k => $v ) {
						$sf_slide_links_2['sf_slide_link_2'][ $k ] = esc_url_raw( $v );
					}
				}
			}

			$sf_slide_type = array();
			if ( isset( $_POST['sf_slide_type'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				parse_str( wp_unslash( $_POST['sf_slide_type'] ), $sf_slide_type );
				if ( ! empty( $sf_slide_type['sf_slide_type'] ) ) {
					foreach ( $sf_slide_type['sf_slide_type'] as $k => $v ) {
						$sf_slide_type['sf_slide_type'][ $k ] = sanitize_text_field( $v );
					}
				}
			}

			$sf_slide_videoType = array();
			if ( isset( $_POST['sf_slide_videoType'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				parse_str( wp_unslash( $_POST['sf_slide_videoType'] ), $sf_slide_videoType );
				if ( ! empty( $sf_slide_videoType['sf_slide_videoType'] ) ) {
					foreach ( $sf_slide_videoType['sf_slide_videoType'] as $k => $v ) {
						$sf_slide_videoType['sf_slide_videoType'][ $k ] = sanitize_text_field( $v );
					}
				}
			}

			$sf_slide_videoSrc = array();
			if ( isset( $_POST['sf_slide_videoSrc'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				parse_str( wp_unslash( $_POST['sf_slide_videoSrc'] ), $sf_slide_videoSrc );
				if ( ! empty( $sf_slide_videoSrc['sf_slide_videoSrc'] ) ) {
					foreach ( $sf_slide_videoSrc['sf_slide_videoSrc'] as $k => $v ) {
						$sf_slide_videoSrc['sf_slide_videoSrc'][ $k ] = sanitize_text_field( $v );
					}
				}
			}

			// Store slide title, alt, description within slider option structure (do not overwrite global WP media posts)
			$i = 0;
			if ( isset( $sf_slide_ids['sf_slide_id'] ) && is_array( $sf_slide_ids['sf_slide_id'] ) && count( $sf_slide_ids['sf_slide_id'] ) ) {
				foreach ( $sf_slide_ids['sf_slide_id'] as $sf_id ) {
					$sf_alt = isset( $sf_slide_alts_text['sf_slide_alt_text'][ $sf_id ] ) ? $sf_slide_alts_text['sf_slide_alt_text'][ $sf_id ] : '';
					if ( ! empty( $sf_alt ) ) {
						update_post_meta( $sf_id, '_wp_attachment_image_alt', sanitize_text_field( $sf_alt ) );
					}
					$i++;
				}
			}

			// settings
			$sf_slider_settings = array();
			// 1 start
			if ( $sf_slider_layout == 1 ) {
				$sf_1_width                     = isset( $_POST['sf_1_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_width'] ) ) : '100%';
				$sf_1_height                    = isset( $_POST['sf_1_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_height'] ) ) : '500px';
				$sf_1_design_preset             = isset( $_POST['sf_1_design_preset'] ) ? intval( $_POST['sf_1_design_preset'] ) : 1;
				$sf_1_auto_play                 = isset( $_POST['sf_1_auto_play'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_auto_play'] ) ) : 'true';
				$sf_1_auto_play_speed           = isset( $_POST['sf_1_auto_play_speed'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_auto_play_speed'] ) ) : '1500';
				$sf_1_auto_play_pause_on_hover = isset( $_POST['sf_1_auto_play_pause_on_hover'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_auto_play_pause_on_hover'] ) ) : 'false';
				$sf_1_infinite_scroll = isset( $_POST['sf_1_infinite_scroll'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_infinite_scroll'] ) ) : 'true';
				$sf_1_full_screen     = isset( $_POST['sf_1_full_screen'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_full_screen'] ) ) : 'true';
				$sf_1_fade            = isset( $_POST['sf_1_fade'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_fade'] ) ) : 'false';
			if ( $sf_1_fade === 'true' ) {
				$sf_1_infinite_scroll = 'false';
				$sf_1_design_preset   = 1;
			}
				$sf_1_adaptive_height           = isset( $_POST['sf_1_adaptive_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_adaptive_height'] ) ) : 'false';
				$sf_1_thumbnail                 = isset( $_POST['sf_1_thumbnail'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_thumbnail'] ) ) : 'false';
				$sf_1_navigation_arrow          = isset( $_POST['sf_1_navigation_arrow'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_navigation_arrow'] ) ) : 'true';
				$sf_1_navigation_dots           = isset( $_POST['sf_1_navigation_dots'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_navigation_dots'] ) ) : 'true';
				$sf_1_sorting                   = isset( $_POST['sf_1_sorting'] ) ? intval( $_POST['sf_1_sorting'] ) : 0;
				$sf_1_slide_align               = isset( $_POST['sf_1_slide_align'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_slide_align'] ) ) : 'center';
				$sf_1_rtl                       = isset( $_POST['sf_1_rtl'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_1_rtl'] ) ) : 'false';
				$sf_1_custom_css                = isset( $_POST['sf_1_custom_css'] ) ? wp_strip_all_tags( wp_unslash( $_POST['sf_1_custom_css'] ) ) : '';

				$sf_slider_settings = array(
					'sf_1_width'                     => $sf_1_width,
					'sf_1_height'                    => $sf_1_height,
					'sf_1_design_preset'             => $sf_1_design_preset,
					'sf_1_auto_play'                 => $sf_1_auto_play,
					'sf_1_auto_play_speed'           => $sf_1_auto_play_speed,
					'sf_1_auto_play_pause_on_hover' => $sf_1_auto_play_pause_on_hover,
					'sf_1_infinite_scroll'           => $sf_1_infinite_scroll,
					'sf_1_full_screen'               => $sf_1_full_screen,
					'sf_1_fade'                      => $sf_1_fade,
					'sf_1_adaptive_height'           => $sf_1_adaptive_height,
					'sf_1_thumbnail'                 => $sf_1_thumbnail,
					'sf_1_navigation_arrow'          => $sf_1_navigation_arrow,
					'sf_1_navigation_dots'           => $sf_1_navigation_dots,
					'sf_1_sorting'                   => $sf_1_sorting,
					'sf_1_slide_align'               => $sf_1_slide_align,
					'sf_1_rtl'                       => $sf_1_rtl,
					'sf_1_custom_css'                => $sf_1_custom_css,
				);
			}
			// 1 end

			// 2 start
			if ( $sf_slider_layout == 2 ) {
				$sf_2_width           = isset( $_POST['sf_2_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_2_width'] ) ) : '100%';
				$sf_2_height          = isset( $_POST['sf_2_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_2_height'] ) ) : 'auto';
				$sf_2_startpoint      = isset( $_POST['sf_2_startpoint'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_2_startpoint'] ) ) : 1;
				$sf_2_jump_back       = isset( $_POST['sf_2_jump_back'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_2_jump_back'] ) ) : 'true';
				$sf_2_jumppoint_click = isset( $_POST['sf_2_jumppoint_click'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_2_jumppoint_click'] ) ) : 'true';
				$sf_2_sorting         = isset( $_POST['sf_2_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_2_sorting'] ) ) : 0;
				$sf_2_custom_css      = isset( $_POST['sf_2_custom_css'] ) ? wp_strip_all_tags( wp_unslash( $_POST['sf_2_custom_css'] ) ) : '';
				$sf_slider_settings  = array(
					'sf_2_width'           => $sf_2_width,
					'sf_2_height'          => $sf_2_height,
					'sf_2_startpoint'       => $sf_2_startpoint,
					'sf_2_jump_back'        => $sf_2_jump_back,
					'sf_2_jumppoint_click'  => $sf_2_jumppoint_click,
					'sf_2_sorting'          => $sf_2_sorting,
					'sf_2_custom_css'       => $sf_2_custom_css,
				);
			}
			// 2 end

			// 3 start
			if ( $sf_slider_layout == 3 ) {
				$sf_3_width         = isset( $_POST['sf_3_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_3_width'] ) ) : '100%';
				$sf_3_height        = isset( $_POST['sf_3_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_3_height'] ) ) : '500px';
				$sf_3_auto_play     = isset( $_POST['sf_3_auto_play'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_3_auto_play'] ) ) : 'true';
				$sf_3_sorting       = isset( $_POST['sf_3_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_3_sorting'] ) ) : 0;
				$sf_slider_settings = array(
					'sf_3_width'     => $sf_3_width,
					'sf_3_height'    => $sf_3_height,
					'sf_3_auto_play' => $sf_3_auto_play,
					'sf_3_sorting'   => $sf_3_sorting,
				);
			}
			// 3 end

			// 4 start
			if ( $sf_slider_layout == 4 ) {
				$sf_4_width         = isset( $_POST['sf_4_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_4_width'] ) ) : '100%';
				$sf_4_height        = isset( $_POST['sf_4_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_4_height'] ) ) : '100%';
				$sf_4_auto_play     = isset( $_POST['sf_4_auto_play'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_4_auto_play'] ) ) : 'true';
				$sf_4_sorting       = isset( $_POST['sf_4_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_4_sorting'] ) ) : 0;
				$sf_slider_settings = array(
					'sf_4_width'     => $sf_4_width,
					'sf_4_height'    => $sf_4_height,
					'sf_4_auto_play' => $sf_4_auto_play,
					'sf_4_sorting'   => $sf_4_sorting,
				);
			}
			// 4 end

			// 5 start
			if ( $sf_slider_layout == 5 ) {
				$sf_5_width         = isset( $_POST['sf_5_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_5_width'] ) ) : '500px';
				$sf_5_height        = isset( $_POST['sf_5_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_5_height'] ) ) : '500px';
				$sf_5_auto_play     = isset( $_POST['sf_5_auto_play'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_5_auto_play'] ) ) : 'false';
				$sf_5_sorting       = isset( $_POST['sf_5_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_5_sorting'] ) ) : 0;
				$sf_slider_settings = array(
					'sf_5_width'     => $sf_5_width,
					'sf_5_height'    => $sf_5_height,
					'sf_5_auto_play' => $sf_5_auto_play,
					'sf_5_sorting'   => $sf_5_sorting,
				);
			}
			// 5 end

			// 6 start
			if ( $sf_slider_layout == 6 ) {
				$sf_6_width         = isset( $_POST['sf_6_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_6_width'] ) ) : '100%';
				$sf_6_height        = isset( $_POST['sf_6_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_6_height'] ) ) : '600px';
				$sf_6_auto_play     = isset( $_POST['sf_6_auto_play'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_6_auto_play'] ) ) : 'true';
				$sf_6_sorting       = isset( $_POST['sf_6_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_6_sorting'] ) ) : 0;
				$sf_slider_settings = array(
					'sf_6_width'     => $sf_6_width,
					'sf_6_height'    => $sf_6_height,
					'sf_6_auto_play' => $sf_6_auto_play,
					'sf_6_sorting'   => $sf_6_sorting,
				);
			}
			// 6 end

			// 7 start
			if ( $sf_slider_layout == 7 ) {
				$sf_7_width             = isset( $_POST['sf_7_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_7_width'] ) ) : '100%';
				$sf_7_height            = isset( $_POST['sf_7_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_7_height'] ) ) : '600px';
				$sf_7_slide_circle_size = isset( $_POST['sf_7_slide_circle_size'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_7_slide_circle_size'] ) ) : 340;
				$sf_7_inner_circle_size = isset( $_POST['sf_7_inner_circle_size'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_7_inner_circle_size'] ) ) : 240;
				$sf_7_auto_play         = isset( $_POST['sf_7_auto_play'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_7_auto_play'] ) ) : 'true';
				$sf_7_sorting           = isset( $_POST['sf_7_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_7_sorting'] ) ) : 0;
				$sf_slider_settings     = array(
					'sf_7_width'             => $sf_7_width,
					'sf_7_height'            => $sf_7_height,
					'sf_7_slide_circle_size' => $sf_7_slide_circle_size,
					'sf_7_inner_circle_size' => $sf_7_inner_circle_size,
					'sf_7_auto_play'         => $sf_7_auto_play,
					'sf_7_sorting'           => $sf_7_sorting,
				);
			}
			// 7 end

			// 8 start
			if ( $sf_slider_layout == 8 ) {
				$sf_8_width         = isset( $_POST['sf_8_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_8_width'] ) ) : '400px';
				$sf_8_height        = isset( $_POST['sf_8_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_8_height'] ) ) : '400px';
				$sf_8_responsive    = isset( $_POST['sf_8_responsive'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_8_responsive'] ) ) : 'true';
				$sf_8_sorting       = isset( $_POST['sf_8_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_8_sorting'] ) ) : 0;
				$sf_slider_settings = array(
					'sf_8_width'      => $sf_8_width,
					'sf_8_height'     => $sf_8_height,
					'sf_8_responsive' => $sf_8_responsive,
					'sf_8_sorting'    => $sf_8_sorting,
				);
			}
			// 8 end

			// 9 start
			if ( $sf_slider_layout == 9 ) {
				$sf_9_width         = isset( $_POST['sf_9_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_9_width'] ) ) : '100%';
				$sf_9_height        = isset( $_POST['sf_9_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_9_height'] ) ) : '700px';
				$sf_9_auto_play     = isset( $_POST['sf_9_auto_play'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_9_auto_play'] ) ) : 'true';
				$sf_9_sorting       = isset( $_POST['sf_9_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_9_sorting'] ) ) : 0;
				$sf_slider_settings = array(
					'sf_9_width'     => $sf_9_width,
					'sf_9_height'    => $sf_9_height,
					'sf_9_auto_play' => $sf_9_auto_play,
					'sf_9_sorting'   => $sf_9_sorting,
				);
			}
			// 9 end

			// 10 start
			if ( $sf_slider_layout == 10 ) {
				$sf_10_width        = isset( $_POST['sf_10_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_10_width'] ) ) : '100%';
				$sf_10_height       = isset( $_POST['sf_10_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_10_height'] ) ) : '100%';
				$sf_10_sorting      = isset( $_POST['sf_10_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_10_sorting'] ) ) : 0;
				$sf_slider_settings = array(
					'sf_10_width'   => $sf_10_width,
					'sf_10_height'  => $sf_10_height,
					'sf_10_sorting' => $sf_10_sorting,
				);
			}
			// 10 end

			// 11 start
			if ( $sf_slider_layout == 11 ) {
				$sf_11_width        = isset( $_POST['sf_11_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_11_width'] ) ) : '100%';
				$sf_11_height       = isset( $_POST['sf_11_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_11_height'] ) ) : '750px';
				$sf_11_sorting      = isset( $_POST['sf_11_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_11_sorting'] ) ) : 0;
				$sf_slider_settings = array(
					'sf_11_width'   => $sf_11_width,
					'sf_11_height'  => $sf_11_height,
					'sf_11_sorting' => $sf_11_sorting,
				);
			}
			// 11 end

			// 12 start
			if ( $sf_slider_layout == 12 ) {
				$sf_12_width               = isset( $_POST['sf_12_width'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_width'] ) ) : '100%';
				$sf_12_height              = isset( $_POST['sf_12_height'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_height'] ) ) : '450px';
				$sf_12_orientation         = isset( $_POST['sf_12_orientation'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_orientation'] ) ) : 'horizontal';
				$sf_12_beforeLabel         = isset( $_POST['sf_12_beforeLabel'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_beforeLabel'] ) ) : 'Before';
				$sf_12_afterLabel          = isset( $_POST['sf_12_afterLabel'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_afterLabel'] ) ) : 'After';
				$sf_12_move_slider_on_hover = isset( $_POST['sf_12_move_slider_on_hover'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_move_slider_on_hover'] ) ) : 'false';
				$sf_12_move_with_handle_only = isset( $_POST['sf_12_move_with_handle_only'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_move_with_handle_only'] ) ) : 'true';
				$sf_12_click_to_move       = isset( $_POST['sf_12_click_to_move'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_click_to_move'] ) ) : 'false';
				$sf_12_sorting             = isset( $_POST['sf_12_sorting'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_12_sorting'] ) ) : 0;
				$sf_12_custom_css          = isset( $_POST['sf_12_custom_css'] ) ? wp_strip_all_tags( wp_unslash( $_POST['sf_12_custom_css'] ) ) : '';

				$sf_slider_settings = array(
					'sf_12_width'               => $sf_12_width,
					'sf_12_height'              => $sf_12_height,
					'sf_12_orientation'         => $sf_12_orientation,
					'sf_12_beforeLabel'         => $sf_12_beforeLabel,
					'sf_12_afterLabel'          => $sf_12_afterLabel,
					'sf_12_move_slider_on_hover' => $sf_12_move_slider_on_hover,
					'sf_12_move_with_handle_only' => $sf_12_move_with_handle_only,
					'sf_12_click_to_move'       => $sf_12_click_to_move,
					'sf_12_sorting'             => $sf_12_sorting,
					'sf_12_custom_css'          => $sf_12_custom_css,
				);
			}
			// 12 end

			$sf_slider_array = array_merge(
				$sf_slider_info,
				$sf_slide_ids,
				$sf_slide_titles,
				$sf_slide_descs,
				$sf_slide_alts_text,
				$sf_slide_link_texts_1,
				$sf_slide_links_1,
				$sf_slide_link_texts_2,
				$sf_slide_links_2,
				$sf_slide_type,
				$sf_slide_videoType,
				$sf_slide_videoSrc,
				$sf_slider_settings
			);

		update_option( 'sf_slider_' . $sf_slider_id, $sf_slider_array );
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	add_action( 'wp_ajax_sf_save_slider', 'wpfrank_sf_save_slider_callback' );
// 3. save slider end


// 4. clone slider start
function wpfrank_sf_clone_slider_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'slider-factory' ) );
		}
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'sf-clone-slider' ) ) {
			wp_die( esc_html__( 'Nonce not verified.', 'slider-factory' ) );
		}
		// verified action
		if ( ! isset( $_POST['sf_slider_id'] ) || ! isset( $_POST['sf_slider_counter'] ) ) {
			wp_die( esc_html__( 'Missing required parameters.', 'slider-factory' ) );
		}
		$sf_slider_id      = sanitize_text_field( wp_unslash( $_POST['sf_slider_id'] ) );
		$sf_new_edit_nonce = wp_create_nonce( 'sf-edit-nonce' );
		$sf_slider_counter = sanitize_text_field( wp_unslash( $_POST['sf_slider_counter'] ) );
		// get cloning slider data
		$sf_cloning_slider = get_option( 'sf_slider_' . $sf_slider_id );

		// generate new slider id for clone
		$new_sf_slider_id     = get_sf_slider_id();
		$new_sf_slider_layout = $sf_cloning_slider['sf_slider_layout'];
		$new_sf_slider_title  = $sf_cloning_slider['sf_slider_title'] . ' - cloned';

		// update clone id into slider data
		foreach ( $sf_cloning_slider as $key => $value ) {
			$sf_cloning_slider['sf_slider_id']    = $new_sf_slider_id;
			$sf_cloning_slider['sf_slider_title'] = $new_sf_slider_title;
		}

		if ( add_option( 'sf_slider_' . $new_sf_slider_id, $sf_cloning_slider ) ) {
			$sf_slider_shortcode = '[sf id=' . $new_sf_slider_id . ' layout=' . $new_sf_slider_layout . ']';
			echo ( '
			<div class="sf-slider-row" id="sf-slider-' . esc_attr( $new_sf_slider_id ) . '">
				<div class="sf-row-info">
					<div class="sf-row-title"><strong>' . esc_html( $new_sf_slider_title ) . '</strong></div>
					<div class="sf-row-meta">
						<span class="sf-badge-layout">' . sprintf( esc_html__( 'Layout %s', 'slider-factory' ), esc_html( $new_sf_slider_layout ) ) . '</span>
						<span class="sf-row-id">' . sprintf( esc_html__( 'ID: %s', 'slider-factory' ), esc_html( $new_sf_slider_id ) ) . '</span>
					</div>
				</div>
				<div class="sf-row-actions">
					<div class="sf-row-shortcode-wrap">
						<div class="input-group input-group-sm">
							<input type="text" id="sf-slider-shortcode-' . esc_attr( $new_sf_slider_id ) . '" class="form-control" value="' . esc_attr( $sf_slider_shortcode ) . '" readonly>
							<button type="button" id="sf-copy-shortcode-' . esc_attr( $new_sf_slider_id ) . '" class="btn btn-outline-secondary" title="' . esc_attr__( 'Click To Copy Slider Shortcode', 'slider-factory' ) . '" onclick="return WpfrankSFCopyShortcode(\'' . esc_attr( $new_sf_slider_id ) . '\');"><i class="fas fa-copy"></i></button>
						</div>
						<div class="sf-copied-' . esc_attr( $new_sf_slider_id ) . ' sf-copied-small alert alert-success py-1 px-2 d-none mt-1 text-center">' . esc_html__( 'Copied', 'slider-factory' ) . '</div>
					</div>
					<div class="sf-row-btns">
						<a href="admin.php?page=sf-manage-slider&sf-slider-action=edit&sf-slider-id=' . esc_attr( $new_sf_slider_id ) . '&sf-slider-layout=' . esc_attr( $new_sf_slider_layout ) . '&sf-edit-nonce=' . esc_attr( $sf_new_edit_nonce ) . '" id="sf-edit-slider" class="btn sf-btn-edit btn-sm" title="' . esc_attr__( 'Edit Slider', 'slider-factory' ) . '">' . esc_html__( 'Edit', 'slider-factory' ) . '</a>
						<button type="button" id="sf-clone-slider" class="btn sf-btn-clone btn-sm" title="' . esc_attr__( 'Clone Slider', 'slider-factory' ) . '" value="' . esc_attr( $new_sf_slider_id ) . '" onclick="return WpfrankSFCloneSlider(\'' . esc_attr( $new_sf_slider_id ) . '\', \'' . esc_attr( $sf_slider_counter ) . '\');"><i class="fas fa-copy"></i></button>
						<button id="sf-delete-slider" class="btn sf-btn-delete btn-sm" title="' . esc_attr__( 'Delete Slider', 'slider-factory' ) . '" value="' . esc_attr( $new_sf_slider_id ) . '" onclick="return WpfrankSFremoveSlider(\'' . esc_attr( $new_sf_slider_id ) . '\', \'single\');"><i class="fas fa-trash-alt"></i></button>
					</div>
					<div class="sf-row-checkbox">
						<input type="checkbox" id="sf-slider-id-' . esc_attr( $new_sf_slider_id ) . '" name="sf-slider-id" value="' . esc_attr( $new_sf_slider_id ) . '" title="' . esc_attr__( 'Select Slider Shortcode', 'slider-factory' ) . '">
					</div>
				</div>
			</div>
			' );
		}
		wp_die();
	}
	add_action( 'wp_ajax_sf_clone_slider', 'wpfrank_sf_clone_slider_callback' );
// 4. clone slider end


// 5. remove slider/sliders start
function wpfrank_sf_remove_slider_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'slider-factory' ) );
		}
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'sf-remove-slider' ) ) {
			wp_die( esc_html__( 'Nonce not verified.', 'slider-factory' ) );
		}
		// verified action
		if ( ! isset( $_POST['sf_slider_id'] ) || ! isset( $_POST['do_action'] ) ) {
			wp_die( esc_html__( 'Missing required parameters.', 'slider-factory' ) );
		}

		$sf_do_action = sanitize_text_field( wp_unslash( $_POST['do_action'] ) );

		// single slider delete
		if ( $sf_do_action === 'single' ) {
			$sf_slider_id = intval( $_POST['sf_slider_id'] );
			if ( $sf_slider_id > 0 ) {
				delete_option( 'sf_slider_' . $sf_slider_id );
			}
		}

		// multiple slider delete
		if ( $sf_do_action === 'multiple' ) {
			$sf_slider_id = array_map( 'sanitize_text_field', ( wp_unslash( $_POST['sf_slider_id'] ) ) );
			foreach ( $sf_slider_id as $sf_single_id ) {
				delete_option( 'sf_slider_' . $sf_single_id );
			}
		}

		wp_die();
	}
	add_action( 'wp_ajax_sf_remove_slider', 'wpfrank_sf_remove_slider_callback' );
// 5. remove slider/sliders end

// register sf scripts
function wpfrank_sf_register_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_register_style( 'fontawesome-css', plugin_dir_url( __FILE__ ) . 'admin/assets/fontawesome-free-6.5.1-web/css/all.min.css', array(), '6.5.1' );

	// layout 1 CSS and JS start
	wp_register_style( 'sf-1-flickity-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/1/css/flickity.css', array(), '2.2.1' ); // v2.2.1
	wp_register_script( 'sf-1-flickity-pkgd-min-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/1/js/flickity.pkgd.js', array( 'jquery' ), '2.2.1', true );
	wp_register_style( 'sf-1-flickity-fade-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/1/css/flickity-fade.css', array(), '1.0.0' );
	wp_register_script( 'sf-1-flickity-fade-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/1/js/flickity-fade.js', array( 'jquery', 'sf-1-flickity-pkgd-min-js' ), '1.0.0', true );
	// layout 1 CSS and JS end

	// layout 2 CSS and JS start
	wp_register_style( 'sf-2-photoroller-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/2/css/photoroller.css', array(), '1.4.0' ); // v1.4.0
	wp_register_script( 'sf-2-photoroller-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/2/js/jquery.photoroller.js', array( 'jquery' ), '1.4.0', true );
	// layout 2 CSS and JS end

	// layout 3 CSS and JS start
	wp_register_script( 'sf-3-accordion-carousel-blue-slider-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/3/js/accordion-carousel-blue-slider.js', array( 'jquery' ), '1.0.0', true );
	// layout 3 CSS and JS end

	// layout 4 CSS and JS start
	wp_register_style( 'sf-4-camera-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/4/css/camera.css', array(), '1.0.0' ); // v1.0.0
	wp_register_script( 'sf-4-camera-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/4/js/camera.js', array( 'jquery' ), '1.0.0', true );
	// layout 4 CSS and JS end

	// layout 5 CSS and JS start
	wp_register_style( 'sf-5-cover-flow-flipster-slider-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/5/css/jquery.flipster.css', array(), '1.0.0' ); // v1.0.0
	wp_register_script( 'sf-5-cover-flow-flipster-slider-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/5/js/jquery.flipster.js', array( 'jquery' ), '1.0.0', true );
	// layout 5 CSS and JS end

	// layout 6 CSS and JS start
	wp_register_style( 'sf-6-wipeslider-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/6/css/wipeSlider.css', array(), '1.0.0' );
	wp_register_script( 'sf-6-wipeslider-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/6/js/jquery.wipeSlider.js', array( 'jquery' ), '1.0.0', true );
	// layout 6 CSS and JS end

	// layout 7 CSS and JS start
	wp_register_style( 'sf-7-rotating-slider-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/7/css/rotating-slider.css', array(), '1.0.0' );
	wp_register_script( 'sf-7-jquery.rotating-slider-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/7/js/jquery.rotating-slider.js', array( 'jquery' ), '1.0.0', true );
	// layout 7 CSS and JS end

	// layout 8 CSS and JS start
	wp_register_script( 'sf-8-infinite-slider-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/8/js/infiniteslidev2.js', array( 'jquery' ), '1.0.0', true );
	// layout 8 CSS and JS end

	// layout 11 CSS and JS start
	wp_register_style( 'sf-11-product-slider-style-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/11/css/test-style.css', array(), '1.0.1' );
	wp_register_script( 'sf-11-product-slider-mordenizer-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/11/js/modernizr.custom.js', array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'sf-11-product-slider-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/11/js/slider.js', array( 'jquery' ), '1.0.0', true );
	// layout 11 CSS and JS end

	// layout 12 CSS and JS start
	wp_register_style( 'sf-12-twentytwenty-css', plugin_dir_url( __FILE__ ) . 'layouts/assets/12/css/twentytwenty.css', array(), '1.0.0' );
	wp_register_script( 'sf-12-jquery-twentytwenty-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/12/js/jquery.twentytwenty.js', array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'sf-12-jquery-event-move-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/12/js/jquery.event.move.js', array( 'jquery' ), '2.0.0', true );
	wp_register_script( 'sf-12-jquery-images-loaded-js', plugin_dir_url( __FILE__ ) . 'layouts/assets/12/js/imagesloaded.pkgd.js', array( 'jquery' ), '4.1.4', true );
	// layout 12 CSS and JS end
}
add_action( 'wp_enqueue_scripts', 'wpfrank_sf_register_scripts' );

// AJAX Preview Callback
function wpfrank_sf_slider_preview_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Insufficient permissions.', 'slider-factory' ) );
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sf_slider_id = isset( $_REQUEST['sf_slider_id'] ) ? intval( $_REQUEST['sf_slider_id'] ) : ( isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0 );
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sf_slider_layout = isset( $_REQUEST['sf_slider_layout'] ) ? intval( $_REQUEST['sf_slider_layout'] ) : ( isset( $_REQUEST['layout'] ) ? intval( $_REQUEST['layout'] ) : 1 );

	$nonce = isset( $_REQUEST['sf_preview_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['sf_preview_nonce'] ) ) : ( isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '' );
	if ( empty( $nonce ) || ( ! wp_verify_nonce( $nonce, 'sf-preview-' . $sf_slider_id ) && ! wp_verify_nonce( $nonce, 'sf-preview-nonce' ) ) ) {
		wp_die( esc_html__( 'Security check failed.', 'slider-factory' ) );
	}

	$slider = array();
	if ( ! empty( $_POST ) ) {
		$sf_slider_info = array(
			'sf_slider_id'     => $sf_slider_id,
			'sf_slider_layout' => $sf_slider_layout,
			'sf_slider_title'  => isset( $_POST['sf_slider_title'] ) ? sanitize_text_field( wp_unslash( $_POST['sf_slider_title'] ) ) : '',
		);

		$array_keys = array(
			'sf_slide_id',
			'sf_slide_title',
			'sf_slide_desc',
			'sf_slide_alt_text',
			'sf_slide_link_text_1',
			'sf_slide_link_1',
			'sf_slide_link_text_2',
			'sf_slide_link_2',
			'sf_slide_type',
			'sf_slide_videoType',
			'sf_slide_videoSrc',
		);

		$parsed_slides = array();
		foreach ( $array_keys as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$unslashed_post = wp_unslash( $_POST[ $key ] );
				if ( is_array( $unslashed_post ) ) {
					$parsed_slides[ $key ] = array_map( 'sanitize_text_field', $unslashed_post );
				} else {
					$parsed = array();
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					parse_str( $unslashed_post, $parsed );
					if ( isset( $parsed[ $key ] ) && is_array( $parsed[ $key ] ) ) {
						if ( $key === 'sf_slide_desc' ) {
							$parsed_slides[ $key ] = array_map( 'sanitize_textarea_field', $parsed[ $key ] );
						} elseif ( $key === 'sf_slide_link_1' || $key === 'sf_slide_link_2' ) {
							$parsed_slides[ $key ] = array_map( 'esc_url_raw', $parsed[ $key ] );
						} else {
							$parsed_slides[ $key ] = array_map( 'sanitize_text_field', $parsed[ $key ] );
						}
					}
				}
			}
		}

		$sf_slider_settings = array();
		foreach ( $_POST as $key => $value ) {
			if ( preg_match( '/^sf_\d+_/', $key ) ) {
				$sf_slider_settings[ $key ] = sanitize_text_field( wp_unslash( $value ) );
			}
		}

		$slider = array_merge(
			$sf_slider_info,
			$parsed_slides,
			$sf_slider_settings
		);
	} else {
		$slider = get_option( 'sf_slider_' . $sf_slider_id );
		if ( ! is_array( $slider ) ) {
			$slider = array();
		}
		// Render-time migration so legacy flat-format sliders preview correctly.
		if ( function_exists( 'sf_migrate_flat_slider_data' ) && isset( $slider['sf_slider_id'] ) && ( ! isset( $slider['sf_slide_id'] ) || ! is_array( $slider['sf_slide_id'] ) ) ) {
			$slider = sf_migrate_flat_slider_data( $slider );
		}
		foreach ( $_GET as $key => $value ) {
			if ( preg_match( '/^sf_\d+_/', $key ) ) {
				$slider[ $key ] = sanitize_text_field( wp_unslash( $value ) );
			}
		}
	}

	wpfrank_sf_register_scripts();
	wp_enqueue_script( 'jquery' );
	wp_enqueue_style( 'fontawesome-css' );

	// Free edition supports layouts 1–12 only; the asset list uses only REGISTERED handles
	// (unregistered handles emit WP warnings and break preview styling).
	$layout_assets = array(
		1  => array(
			'styles'  => array( 'sf-1-flickity-css' ),
			'scripts' => array( 'sf-1-flickity-pkgd-min-js' ),
		),
		2  => array(
			'styles'  => array( 'sf-2-photoroller-css' ),
			'scripts' => array( 'sf-2-photoroller-js' ),
		),
		3  => array(
			'styles'  => array(),
			'scripts' => array( 'sf-3-accordion-carousel-blue-slider-js' ),
		),
		4  => array(
			'styles'  => array( 'sf-4-camera-css' ),
			'scripts' => array( 'sf-4-camera-js' ),
		),
		5  => array(
			'styles'  => array( 'sf-5-cover-flow-flipster-slider-css' ),
			'scripts' => array( 'sf-5-cover-flow-flipster-slider-js' ),
		),
		6  => array(
			'styles'  => array( 'sf-6-wipeslider-css' ),
			'scripts' => array( 'sf-6-wipeslider-js' ),
		),
		7  => array(
			'styles'  => array( 'sf-7-rotating-slider-css' ),
			'scripts' => array( 'sf-7-jquery.rotating-slider-js' ),
		),
		8  => array(
			'styles'  => array(),
			'scripts' => array( 'sf-8-infinite-slider-js' ),
		),
		9  => array(
			'styles'  => array(),
			'scripts' => array(),
		),
		10 => array(
			'styles'  => array(),
			'scripts' => array(),
		),
		11 => array(
			'styles'  => array( 'sf-11-product-slider-style-css' ),
			'scripts' => array( 'sf-11-product-slider-mordenizer-js', 'sf-11-product-slider-js' ),
		),
		12 => array(
			'styles'  => array( 'sf-12-twentytwenty-css' ),
			'scripts' => array( 'sf-12-jquery-twentytwenty-js', 'sf-12-jquery-event-move-js', 'sf-12-jquery-images-loaded-js' ),
		),
	);

	if ( isset( $layout_assets[ $sf_slider_layout ] ) ) {
		if ( ! empty( $layout_assets[ $sf_slider_layout ]['styles'] ) ) {
			foreach ( $layout_assets[ $sf_slider_layout ]['styles'] as $style ) {
				wp_enqueue_style( $style );
			}
		}
		if ( ! empty( $layout_assets[ $sf_slider_layout ]['scripts'] ) ) {
			foreach ( $layout_assets[ $sf_slider_layout ]['scripts'] as $script ) {
				wp_enqueue_script( $script );
			}
		}
	}
	?>
	<!DOCTYPE html>
	<html <?php language_attributes(); ?> style="margin-top: 0 !important;">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Slider Factory Preview</title>
		<?php wp_head(); ?>
		<style>
			html, body {
				height: 100% !important;
			}
			body {
				background: #ffffff;
				margin: 0;
				padding: 0;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			}
		</style>
	</head>
	<body>
		<?php
		$layout_path = plugin_dir_path( __FILE__ ) . 'layouts/' . $sf_slider_layout . '.php';
		if ( file_exists( $layout_path ) ) {
			include $layout_path;
		} else {
			echo '<p style="color:red; text-align:center;">Preview not available for this layout.</p>';
		}
		?>
		<?php wp_footer(); ?>
		<script>
			(function() {
				var lastSentHeight = 0;
				function sendHeight() {
					var sf3Container = document.querySelector('.sf-3-<?php echo esc_js( $sf_slider_id ); ?>') || document.querySelector('[class^="sf-3-"]');
					var sf7Container = document.querySelector('[class^="sf-7-rotating-slider-container"]');
					var sf8Container = document.querySelector('.infiniteslide_wrap');
					var sf10Container = document.querySelector('[class^="sf-10-main-container-"]');
					var sf11Container = document.querySelector('[class^="ps-container-"]');
					var sf12Container = document.querySelector('[class^="sf-12-main-"]');
					var content = document.querySelector('.sf-8-<?php echo esc_js( $sf_slider_id ); ?>') || document.querySelector('.sf-6-<?php echo esc_js( $sf_slider_id ); ?>') || document.querySelector('.sf-5-<?php echo esc_js( $sf_slider_id ); ?>') || document.querySelector('.sf-4-<?php echo esc_js( $sf_slider_id ); ?>') || document.querySelector('.sf-3-<?php echo esc_js( $sf_slider_id ); ?>') || document.querySelector('.carousel-main-<?php echo esc_js( $sf_slider_id ); ?>') || document.querySelector('.sf-2-<?php echo esc_js( $sf_slider_id ); ?>') || document.querySelector('.slider-container-<?php echo esc_js( $sf_slider_id ); ?>') || document.body.firstElementChild;
					if (sf3Container) {
						var measuredHeight = Math.ceil(sf3Container.offsetHeight || sf3Container.getBoundingClientRect().height);
					} else if (sf7Container) {
						// Use offsetHeight for Layout 7: it respects overflow:hidden clipping
						var measuredHeight = Math.ceil(sf7Container.offsetHeight);
					} else if (sf8Container) {
						// Use offsetHeight for Layout 8: it respects overflow:hidden clipping
						var measuredHeight = Math.ceil(sf8Container.offsetHeight || sf8Container.getBoundingClientRect().height);
					} else if (sf10Container) {
						// Use offsetHeight for Layout 10: it respects overflow:hidden clipping
						var measuredHeight = Math.ceil(sf10Container.offsetHeight || sf10Container.getBoundingClientRect().height);
					} else if (sf11Container) {
						// Use offsetHeight for Layout 11: it respects overflow:hidden clipping
						var measuredHeight = Math.ceil(sf11Container.offsetHeight || sf11Container.getBoundingClientRect().height);
					} else if (sf12Container) {
						// Use offsetHeight for Layout 12: TwentyTwenty comparison container
						var measuredHeight = Math.ceil(sf12Container.offsetHeight || sf12Container.scrollHeight || sf12Container.getBoundingClientRect().height);
					} else if (content) {
						var measuredHeight = Math.ceil(content.scrollHeight || content.offsetHeight || content.getBoundingClientRect().height);
					} else {
						return;
					}
					var finalHeight = Math.max(measuredHeight + 12, 180);

					if (Math.abs(finalHeight - lastSentHeight) > 3) {
						lastSentHeight = finalHeight;
						if (window.parent && window.parent !== window) {
							window.parent.postMessage({ type: 'SF_PREVIEW_HEIGHT', height: finalHeight }, window.location.origin);
						}
					}
				}
				window.addEventListener('load', sendHeight);
				window.addEventListener('resize', sendHeight);
				setTimeout(sendHeight, 200);
				setTimeout(sendHeight, 600);
			})();
		</script>
	</body>
	</html>
	<?php
	wp_die();
}
add_action( 'wp_ajax_sf_slider_preview', 'wpfrank_sf_slider_preview_callback' );

require 'admin/rest-api.php';
require 'shortcode.php';
// Slider Text Widget Support
// Shortcode execution is natively supported in WP 5.0+ Block Widgets
?>
