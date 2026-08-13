<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Migrate old flat-format slider data to nested arrays.
 *
 * Old format:  $slider['sf_slide_id10'] = '10', $slider['sf_slide_title10'] = 'My Title'
 * New format:  $slider['sf_slide_id'][10] = '10', $slider['sf_slide_title'][10] = 'My Title'
 *
 * @param array $slider Raw slider option value.
 * @return array Slider data with nested slide arrays.
 */
if ( ! function_exists( 'sf_migrate_flat_slider_data' ) ) {
	function sf_migrate_flat_slider_data( $slider ) {
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

		// Known flat-key prefixes that need migration to nested arrays.
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

		// Build nested arrays
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

		// Remove old flat keys
		$keys_to_remove = array();
		foreach ( array_keys( $slider ) as $key ) {
			foreach ( $flat_prefixes as $prefix ) {
				if ( strpos( $key, $prefix ) === 0 && $key !== $prefix ) {
					$keys_to_remove[] = $key;
					break;
				}
			}
			// Also remove old sf_slide_id{N} flat keys
			if ( preg_match( '/^sf_slide_id\d+$/', $key ) ) {
				$keys_to_remove[] = $key;
			}
		}
		$slider = array_diff_key( $slider, array_flip( $keys_to_remove ) );

		return $slider;
	}
}

global $wpdb;
$sf_table_name = $wpdb->prefix . 'sf_sliders';

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['sf-slider-action'] ) && isset( $_GET['sf-slider-layout'] ) ) {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sf_slider_action = sanitize_text_field( wp_unslash( $_GET['sf-slider-action'] ) );
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sf_slider_layout = intval( $_GET['sf-slider-layout'] );

	if ( false ) { // Legacy PHP builder fallback disabled for uniform React Dashboard UI
		include 'slider-panel.php';
		return;
	}
}

// Render React Dashboard for all layouts (Edit/Create) and All Sliders list view
?>
<div id="sf-react-dashboard"></div>
<?php
if ( false ) { // prevent legacy PHP list code execution


	global $wpdb;
	$sf_options_table_name = "{$wpdb->prefix}options";
	$slider_key            = 'sf_slider_';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$all_sliders        = $wpdb->get_results(
		$wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE `option_name` LIKE %s ORDER BY option_id ASC", '%' . $slider_key . '%' )
	);
	
	$sf_create_nonce = wp_create_nonce( 'sf-create-nonce' );
	$sf_edit_nonce = wp_create_nonce( 'sf-edit-nonce' );
	?>
	<div class="sf-panel">
	<div class="sf-panel-card m-3 p-0">
		<?php
		if ( $wpdb->num_rows ) {
			?>
			<div class="sf-list-toolbar d-flex justify-content-between align-items-center">
				<h3 class="sf-card-title-inline"><?php esc_html_e( 'All Sliders', 'slider-factory' ); ?></h3>
				<div class="d-flex align-items-center gap-3">
					<div class="form-check d-flex align-items-center gap-2 mb-0">
						<input class="form-check-input" type="checkbox" id="sf-select-all" title="<?php esc_attr_e( 'Select All Sliders', 'slider-factory' ); ?>">
						<label class="form-check-label sf-label mb-0" for="sf-select-all">
							<?php esc_html_e( 'Select All', 'slider-factory' ); ?>
						</label>
					</div>
					<button type="button" id="sf-delete-selected" class="btn sf-btn-delete btn-sm d-none" title="<?php esc_attr_e( 'Delete Selected Sliders', 'slider-factory' ); ?>" onclick="return WpfrankSFremoveSlider('', 'multiple');">
						<i class="fas fa-trash-alt"></i> <?php esc_html_e( 'Delete Selected', 'slider-factory' ); ?>
					</button>
				</div>
			</div>
			
			<div id="sf-slider-list" class="sf-slider-list">
				<?php
				$sf_counter    = 1;
				foreach ( $all_sliders as $slider ) {
					$slider_key        = $slider->option_name;
					$sf_underscore_pos = strrpos( $slider_key, '_' );
					$sf_slider_id      = substr( $slider_key, ( $sf_underscore_pos + 1 ) );

					// load slider data
					$slider = get_option( 'sf_slider_' . $sf_slider_id );
					if ( isset( $slider['sf_slider_id'] ) ) {
						$sf_slider_id = $slider['sf_slider_id'];
					} else {
						$sf_slider_id = '';
					}
					if ( isset( $slider['sf_slider_title'] ) ) {
						$sf_slider_title = $slider['sf_slider_title'];
					} else {
						$sf_slider_title = '';
					}
					if ( isset( $slider['sf_slider_layout'] ) ) {
						$sf_slider_layout = $slider['sf_slider_layout'];
					} else {
						$sf_slider_layout = '';
					}
					$sf_slider_shortcode = '[sf id=' . $sf_slider_id . ' layout=' . $sf_slider_layout . ']';
					if ( $sf_slider_id && $sf_slider_layout ) {
						?>
						<div class="sf-slider-row" id="sf-slider-<?php echo esc_attr( $sf_slider_id ); ?>">
							<div class="sf-row-info">
								<div class="sf-row-title"><strong><?php echo esc_html( $sf_slider_title ); ?></strong></div>
								<div class="sf-row-meta">
									<span class="sf-badge-layout"><?php echo sprintf( esc_html__( 'Layout %s', 'slider-factory' ), esc_html( $sf_slider_layout ) ); ?></span>
									<span class="sf-row-id"><?php echo sprintf( esc_html__( 'ID: %s', 'slider-factory' ), esc_html( $sf_slider_id ) ); ?></span>
								</div>
							</div>
							<div class="sf-row-actions">
								<div class="sf-row-shortcode-wrap">
									<div class="input-group input-group-sm">
										<input type="text" id="sf-slider-shortcode-<?php echo esc_attr( $sf_slider_id ); ?>" class="form-control" value="<?php echo esc_attr( $sf_slider_shortcode ); ?>" readonly>
										<button type="button" id="sf-copy-shortcode-<?php echo esc_attr( $sf_slider_id ); ?>" class="btn btn-outline-secondary" title="<?php esc_attr_e( 'Click To Copy Slider Shortcode', 'slider-factory' ); ?>" onclick="return WpfrankSFCopyShortcode('<?php echo esc_attr( $sf_slider_id ); ?>');"><i class="fas fa-copy"></i></button>
									</div>
									<div class="sf-copied-<?php echo esc_attr( $sf_slider_id ); ?> sf-copied-small alert alert-success py-1 px-2 d-none mt-1 text-center"><?php esc_html_e( 'Copied', 'slider-factory' ); ?></div>
								</div>
								<div class="sf-row-btns">
									<a href="admin.php?page=sf-manage-slider&sf-slider-action=edit&sf-slider-id=<?php echo esc_attr( $sf_slider_id ); ?>&sf-slider-layout=<?php echo esc_attr( $sf_slider_layout ); ?>&sf-edit-nonce=<?php echo esc_attr( $sf_edit_nonce ); ?>" id="sf-edit-slider" class="btn sf-btn-edit btn-sm" title="<?php esc_attr_e( 'Edit Slider', 'slider-factory' ); ?>"><?php esc_html_e( 'Edit', 'slider-factory' ); ?></a>
									<button type="button" id="sf-clone-slider" class="btn sf-btn-clone btn-sm" title="<?php esc_attr_e( 'Clone Slider', 'slider-factory' ); ?>" value="<?php echo esc_attr( $sf_slider_id ); ?>" onclick="return WpfrankSFCloneSlider('<?php echo esc_attr( $sf_slider_id ); ?>', '<?php echo esc_attr( $sf_counter ); ?>');"><i class="fas fa-copy"></i></button>
									<button id="sf-delete-slider" class="btn sf-btn-delete btn-sm" title="<?php esc_attr_e( 'Delete Slider', 'slider-factory' ); ?>" value="<?php echo esc_attr( $sf_slider_id ); ?>" onclick="return WpfrankSFremoveSlider('<?php echo esc_attr( $sf_slider_id ); ?>', 'single');"><i class="fas fa-trash-alt"></i></button>
								</div>
								<div class="sf-row-checkbox">
									<input type="checkbox" id="sf-slider-id-<?php echo esc_attr( $sf_slider_id ); ?>" name="sf-slider-id" value="<?php echo esc_attr( $sf_slider_id ); ?>" title="<?php esc_attr_e( 'Select Slider Shortcode', 'slider-factory' ); ?>">
								</div>
							</div>
						</div>
						<?php
						$sf_counter++;
					}
				}
				?>
			</div>
		</div>
		<?php
		} else {
			?>
			<div class="m-3 sf-empty-state">
				<div class="sf-empty-state-icon"><i class="fas fa-images"></i></div>
				<div class="sf-empty-state-title"><?php esc_html_e( 'No Sliders Created Yet', 'slider-factory' ); ?></div>
				<div class="sf-empty-state-desc"><?php esc_html_e( 'Create your first slider to display beautiful galleries or carousels on your website.', 'slider-factory' ); ?></div>
				<a class="btn btn-primary px-4" href="admin.php?page=sf-manage-slider&amp;sf-slider-action=create&amp;sf-slider-layout=1&amp;sf-create-nonce=<?php echo esc_attr($sf_create_nonce); ?>">
					<i class="fas fa-plus"></i> <?php esc_html_e( 'Create New Slider', 'slider-factory' ); ?>
				</a>
			</div>
			<?php
		}
		?>
	</div><!-- /.sf-panel -->
	<script>
	// copy shortcode to clipboard for slider list
	function WpfrankSFCopyShortcode(id) {
		var copyShortcode = document.getElementById('sf-slider-shortcode-' + id);
		if (copyShortcode) {
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(copyShortcode.value);
			} else {
				copyShortcode.select();
				document.execCommand('copy');
			}
		}

		jQuery('.sf-copied-' + id).removeClass('d-none').fadeIn('2000', 'linear').fadeOut(3000,'swing');
	}

	// clone slide start
	function WpfrankSFCloneSlider(sf_slider_id, sf_slider_counter){
		console.log(sf_slider_id + sf_slider_counter);
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				'action': 'sf_clone_slider',
				'nonce': '<?php echo esc_js( wp_create_nonce( "sf-clone-slider" ) ); ?>',
				'sf_slider_id': sf_slider_id,
				'sf_slider_counter': sf_slider_counter,
			}, 
			success: function (result) {
				jQuery('#sf-slider-list').append(result);
			},
			error: function () {
			}
		});
	}

	//select all sliders
	jQuery('#sf-select-all').click(function () {
		jQuery('input:checkbox[name=sf-slider-id]').prop('checked', this.checked);
		jQuery('input:checkbox[name=sf-slider-id]').trigger('change');
	});

	// toggle bulk delete button visibility
	jQuery(document).on('change', 'input[name=sf-slider-id], #sf-select-all', function() {
		var anyChecked = jQuery('input[name=sf-slider-id]:checked').length > 0;
		if (anyChecked) {
			jQuery('#sf-delete-selected').removeClass('d-none');
		} else {
			jQuery('#sf-delete-selected').addClass('d-none');
		}
	});

	// remove slider/sliders start
	function WpfrankSFremoveSlider(sf_slider_id, do_action){
		console.log(sf_slider_id);
		if(do_action == 'multiple'){
			var sf_slider_id = [];
			jQuery('input:checkbox[name=sf-slider-id]:checked').each(function() {
				var rowId = jQuery(this).val();
				sf_slider_id.push(rowId);
				jQuery('.sf-slider-row#sf-slider-' + rowId).fadeOut('1500');
				(function(capturedId) {
					setTimeout(function() {
						jQuery('.sf-slider-row#sf-slider-' + capturedId).remove();
					}, 1000);
				})(rowId);
			});
			jQuery('#sf-delete-selected').addClass('d-none');
			jQuery('#sf-select-all').prop('checked', false);
		}
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				'action': 'sf_remove_slider',
				'do_action': do_action,
				'nonce': '<?php echo esc_js( wp_create_nonce( 'sf-remove-slider' ) ); ?>',
				'sf_slider_id': sf_slider_id,
			}, 
			success: function (result) {
				if(do_action == 'single'){
					jQuery('.sf-slider-row#sf-slider-' + sf_slider_id).fadeOut('1500');
					jQuery(function() {
						setTimeout(function() {
							jQuery('.sf-slider-row#sf-slider-' + sf_slider_id).remove();
						}, 1000);
					});
				}
			},
			error: function () {
			}
		});
	}
	</script>
	<?php
}