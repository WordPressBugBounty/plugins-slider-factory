<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>
<style>
.d-none { display: none !important; }
</style>

<div class="p-4 m-3 sf-panel">
	<input type="hidden" class="form-control item-menu" name="sf_slider_id" id="sf_slider_id" value="<?php echo esc_attr( $sf_slider_id ); ?>">
	<input type="hidden" class="form-control item-menu" name="sf_upload_nonce" id="sf_upload_nonce" value="<?php echo esc_attr( wp_create_nonce( 'sf-upload-nonce' ) ); ?>">
	<input type="hidden" class="form-control item-menu" name="sf_slider_layout" id="sf_slider_layout" value="<?php echo esc_attr( $sf_slider_layout ); ?>">
	<input type="hidden" class="form-control item-menu" name="sf_preview_nonce" id="sf_preview_nonce" value="<?php echo esc_attr( wp_create_nonce( 'sf-preview-' . $sf_slider_id ) ); ?>">

	<!-- Header with inline slider title -->
	<div class="sf-editor-header mb-4">
		<div class="sf-editor-header-row">
			<div class="sf-editor-header-info">
				<h2 class="sf-title">
				<?php
				$sf_allowed_title = array( 'code' => array() );
				echo wp_kses( $sf_slider_heading, $sf_allowed_title );
				?>
				</h2>
			</div>
			<div class="sf-editor-header-actions">
				<button type="button" id="sf-save-slider" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo esc_html( $sf_slider_button_text ); ?></button>
			</div>
		</div>
		<div class="sf-editor-title-field">
			<label for="sf_slider_title" class="sf-label"><?php esc_html_e( 'Slider Name', 'slider-factory' ); ?></label>
			<input type="text" class="form-control" name="sf_slider_title" id="sf_slider_title" placeholder="<?php esc_attr_e( 'Enter slider name', 'slider-factory' ); ?>" value="<?php echo esc_attr( $sf_slider_title ); ?>">
		</div>
		<!-- Shortcode bar (shown only on edit, not create) -->
		<div id="sf-shortcode-content" class="sf-shortcode-bar <?php echo ( $sf_slider_action == 'create' ) ? 'd-none' : ''; ?>">
			<span class="sf-shortcode-label"><?php esc_html_e( 'Shortcode', 'slider-factory' ); ?></span>
			<?php $shortcode = '[sf id=' . esc_html( $sf_slider_id ) . ' layout=' . esc_html( $sf_slider_layout ) . ']'; ?>
			<div class="sf-shortcode-input-group">
				<input type="text" class="form-control" id="sf-slider-shortcode-text" value="<?php echo esc_attr( $shortcode ); ?>" readonly>
				<button type="button" id="sf-copy-slider-shortcode-btn" class="btn btn-outline-secondary" onclick="return WpfrankSFCopyShortcode();" title="<?php esc_attr_e( 'Copy Shortcode', 'slider-factory' ); ?>"><i class="fas fa-copy"></i></button>
			</div>
			<span id="sf-copied" class="sf-copied-inline d-none"><i class="fas fa-check"></i> <?php esc_html_e( 'Copied!', 'slider-factory' ); ?></span>
		</div>
	</div>

	<div class="row">
		<!-- Left Main Column (Slides) -->
		<div class="col-lg-8 col-md-12">
			<!-- Live Preview Card -->
			<div class="sf-panel-card mb-4 sf-legacy-preview-card">
				<div class="sf-card-header-row">
					<h3 class="sf-card-title-inline"><i class="fas fa-play-circle text-primary"></i> <?php esc_html_e( 'Live Preview', 'slider-factory' ); ?></h3>
					<div class="sf-card-header-actions">
						<button type="button" id="sf-refresh-preview" class="btn btn-outline-secondary btn-sm"><i class="fas fa-sync-alt"></i> <?php esc_html_e( 'Refresh', 'slider-factory' ); ?></button>
					</div>
				</div>
				<div class="sf-preview-body" style="background: #f8fafc; padding: 16px; position: relative; overflow: hidden; border-top: 1px solid var(--sf-border-color); border-radius: 0 0 12px 12px;">
					<iframe id="sf-live-preview-iframe" name="sf-live-preview-iframe" scrolling="no" style="width: 100%; height: 380px; border: none; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;" sandbox="allow-same-origin allow-scripts allow-forms allow-popups" onload="jQuery('#sf-preview-overlay-loader').addClass('d-none'); if (typeof SFresizePreviewIframe === 'function') SFresizePreviewIframe();" srcdoc="<?php echo '<html><body style=\'margin:0;display:flex;align-items:center;justify-content:center;height:100%;font-family:-apple-system,system-ui,BlinkMacSystemFont,sans-serif;background:#f8fafc;\'><div style=\'text-align:center;padding:20px;\'><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'48\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'#94a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><polyline points=\'21 15 16 10 5 21\'/></svg><p style=\'margin:12px 0 0;font-size:14px;color:#64748b;font-weight:500;\'>Add images to see the live preview</p><p style=\'margin:4px 0 0;font-size:12px;color:#94a3b8;\'>Click Add Images to upload slides</p></div></body></html>'; ?>"></iframe>
					<div id="sf-preview-overlay-loader" class="sf-spinner-wrap" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.85); align-items: center; justify-content: center; z-index: 10; border-radius: 0 0 var(--sf-radius-lg) var(--sf-radius-lg);">
						<div class="spinner-grow sf-spinner-primary" role="status" style="color: var(--sf-accent);"></div>
						<span style="font-size: 13px; font-weight: 600; color: var(--sf-accent); margin-left: 8px;"><?php esc_html_e( 'Updating Preview...', 'slider-factory' ); ?></span>
					</div>
				</div>
			</div>

			<!-- Slide Upload Card -->
			<div class="sf-panel-card mb-4">
				<div class="sf-card-header-row">
					<h3 class="sf-card-title-inline"><?php esc_html_e( 'Slides', 'slider-factory' ); ?></h3>
					<div class="sf-card-header-actions">
						<button type="button" id="sf-upload-slides" class="btn btn-primary btn-sm" value="<?php echo esc_attr( $sf_slider_layout ); ?>"><i class="fas fa-plus"></i> <?php esc_html_e( 'Add Images', 'slider-factory' ); ?></button>
						<button type="button" class="btn btn-outline-danger btn-sm" onclick="return SFremoveAllSlides();" title="<?php esc_attr_e( 'Remove All Image Slides', 'slider-factory' ); ?>"><i class="fas fa-trash"></i> <?php esc_html_e( 'Remove All', 'slider-factory' ); ?></button>
					</div>
				</div>
				<div id="sf-slides-help" class="sf-card-hint">
					<i class="fas fa-hand-pointer"></i> <?php esc_html_e( 'Drag slides to reorder. Hover a slide to edit or delete it.', 'slider-factory' ); ?>
				</div>

				<script>
				jQuery(document).ready(function () {
					jQuery( function() {
						jQuery( "#sf-slides" ).sortable({
							items: ".image_info_tile",
							handle: ".sf-slide-card",
							placeholder: "sf-slide-placeholder",
							forcePlaceholderSize: false,
							helper: "clone",
							tolerance: "pointer",
							start: function(e, ui){
								ui.helper.addClass("is-dragging");
							},
							stop: function(e, ui){
								ui.item.removeClass("is-dragging");
							}
						});
					});
				});
				</script>

				<div id="sf-slides" class="sf-slides">
					<?php
					if ( isset( $slider['sf_slide_id'] ) ) {
						$sf_slide_index = 0;
						foreach ( $slider['sf_slide_id'] as $id ) {
							$sf_slide_index++;
							// defaults
							$sf_slide_title = $sf_slide_alt = $sf_slide_descs = $sf_slide_thumbnail = '';
							// load values
							$attachment_id      = $id;
							$sf_slide_title     = get_the_title( $attachment_id );
							$sf_slide_alt       = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
							$sf_slide_thumbnail = wp_get_attachment_image_src( $attachment_id, 'full', true );
							$attachment         = get_post( $attachment_id );
							$sf_slide_descs     = $attachment->post_content;

							// Video support for layouts 13, 19, 20 (kept for compatibility)
							if ( ( $sf_slider_layout == 13 ) || ( $sf_slider_layout == 19 ) || ( $sf_slider_layout == 20 ) ) {
								if ( isset( $slider['sf_slide_type'][ $id ] ) ) {
									$sf_slide_type = $slider['sf_slide_type'][ $id ];
								} else {
									$sf_slide_type = '';
								}
								if ( isset( $slider['sf_slide_videoType'][ $id ] ) ) {
									$sf_slide_videoType = $slider['sf_slide_videoType'][ $id ];
								} else {
									$sf_slide_videoType = '';
								}
								if ( isset( $slider['sf_slide_videoSrc'][ $id ] ) ) {
									$sf_slide_videoSrc = $slider['sf_slide_videoSrc'][ $id ];
								} else {
									$sf_slide_videoSrc = '';
								}
							}

							// Link fields
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
							<!-- Visual slide card — image + overlay icons; fields live in hidden container below -->
							<div class="sf-slide-card-wrapper sf_slide_<?php echo esc_attr( $attachment_id ); ?> image_info_tile" data-position="<?php echo esc_attr( $attachment_id ); ?>" data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>">
								<div class="sf-slide-card" title="<?php esc_attr_e( 'Drag to reorder', 'slider-factory' ); ?>">
									<img class="sf-slide-card-img" src="<?php echo esc_url( $sf_slide_thumbnail[0] ); ?>" alt="<?php echo esc_attr( $sf_slide_alt ); ?>">
									<div class="sf-slide-card-top">
										<span class="sf-slide-card-grip" onclick="event.stopPropagation();" title="<?php esc_attr_e( 'Drag to reorder', 'slider-factory' ); ?>"><i class="fas fa-grip-vertical"></i></span>
										<span class="sf-slide-card-index" data-slide-index>#<?php echo esc_html( $sf_slide_index ); ?></span>
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

									<?php if ( ( $sf_slider_layout == 13 ) || ( $sf_slider_layout == 19 ) || ( $sf_slider_layout == 20 ) ) { ?>
									<select class="form-select sf_slide_type fas" name="sf_slide_type[<?php echo esc_attr( $attachment_id ); ?>]" aria-label="sf-slide-type">
										<option value="image" <?php if ( $sf_slide_type == 'image' ) { echo 'selected'; } ?>>Image</option>
										<option value="video" <?php if ( $sf_slide_type == 'video' ) { echo 'selected'; } ?>>Video</option>
									</select>
									<select class="form-select sf_slide_videoType" name="sf_slide_videoType[<?php echo esc_attr( $attachment_id ); ?>]" aria-label="sf-slide-videoType">
										<option value="youtube" <?php if ( $sf_slide_videoType == 'youtube' ) { echo 'selected'; } ?>>Youtube</option>
										<option value="vimeo" <?php if ( $sf_slide_videoType == 'vimeo' ) { echo 'selected'; } ?>>Vimeo</option>
										<option value="internal" <?php if ( $sf_slide_videoType == 'internal' ) { echo 'selected'; } ?>>Internal</option>
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
						}
					}
					?>
				</div>
			</div>
		</div>

		<!-- Right Sidebar Column (Settings & Save) -->
		<div class="col-lg-4 col-md-12">
			<!-- Settings Card -->
			<div class="sf-panel-card mb-4">
				<div class="sf-card-header-row">
					<h3 class="sf-card-title-inline"><?php esc_html_e( 'Settings', 'slider-factory' ); ?></h3>
					<div class="sf-card-header-actions">
						<span class="sf-card-layout-badge"><?php echo esc_html( get_sf_layout_name( $sf_slider_layout ) ); ?></span>
					</div>
				</div>
				<div class="sf-settings-container">
					<?php
					for ( $i = 1; $i <= 12; $i++ ) {
						if ( $sf_slider_layout == $i ) {
							include "settings/{$i}.php";
						}
					}
					?>
				</div>
			</div>

			<!-- Save / Processing (below settings) -->
			<div class="sf-panel-card sf-save-card mb-4">
				<div id="sf-slider-process" class="sf-spinner-wrap d-none" role="status">
					<div class="spinner-grow sf-spinner-primary" role="status"><span class="visually-hidden"><?php esc_html_e( 'Loading...', 'slider-factory' ); ?></span></div>
					<span class="sf-spinner-text"><?php esc_html_e( 'Saving...', 'slider-factory' ); ?></span>
				</div>
				<button type="button" id="sf-save-slider-bottom" class="btn btn-primary w-100"><i class="fas fa-save"></i> <?php echo esc_html( $sf_slider_button_text ); ?></button>
			</div>
		</div>
	</div>

	<!-- Slide Edit Modal (shared, one instance) -->
	<div class="modal fade" id="sfSlideModal" tabindex="-1" aria-labelledby="sfSlideModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="sfSlideModalLabel"><i class="fas fa-image"></i> <?php esc_html_e( 'Edit Slide', 'slider-factory' ); ?></h5>
					<button type="button" class="btn-close" onclick="sfSlideModal.hide();" aria-label="<?php esc_attr_e( 'Close', 'slider-factory' ); ?>"><i class="fas fa-times"></i></button>
				</div>
				<div class="modal-body">
					<div class="sf-slide-modal-preview-wrapper" style="position: relative; overflow: hidden; border-radius: 6px; margin-bottom: 16px;">
						<img class="sf-slide-modal-preview" id="sfSlideModalPreview" src="" alt="" style="width: 100%; display: block; border-radius: 6px; object-fit: cover; max-height: 280px; margin-bottom: 0;">
						<button type="button" id="sfSlideModalPrevBtn" class="sf-modal-nav-btn sf-modal-prev-btn" title="<?php esc_attr_e( 'Previous Slide', 'slider-factory' ); ?>"><i class="fas fa-chevron-left"></i></button>
						<button type="button" id="sfSlideModalNextBtn" class="sf-modal-nav-btn sf-modal-next-btn" title="<?php esc_attr_e( 'Next Slide', 'slider-factory' ); ?>"><i class="fas fa-chevron-right"></i></button>
					</div>
					<input type="hidden" id="sfSlideModalAttachmentId" value="">

					<?php if ( ( $sf_slider_layout == 13 ) || ( $sf_slider_layout == 19 ) || ( $sf_slider_layout == 20 ) ) { ?>
					<div class="sf-slide-modal-field" id="sfSlideModalMediaTypeWrap">
						<label><?php esc_html_e( 'Media Type', 'slider-factory' ); ?></label>
						<select class="form-select form-select-sm" id="sfSlideModalMediaType">
							<option value="image"><?php esc_html_e( 'Image', 'slider-factory' ); ?></option>
							<option value="video"><?php esc_html_e( 'Video', 'slider-factory' ); ?></option>
						</select>
					</div>
					<div class="sf-slide-modal-field d-none" id="sfSlideModalVideoWrap">
						<label><?php esc_html_e( 'Video Platform', 'slider-factory' ); ?></label>
						<select class="form-select form-select-sm" id="sfSlideModalVideoType">
							<option value="youtube">YouTube</option>
							<option value="vimeo">Vimeo</option>
							<option value="internal"><?php esc_html_e( 'Internal', 'slider-factory' ); ?></option>
						</select>
						<label class="mt-2"><?php esc_html_e( 'Video Source', 'slider-factory' ); ?></label>
						<input type="text" class="form-control form-control-sm" id="sfSlideModalVideoSrc" placeholder="<?php esc_attr_e( 'Youtube-ID Only', 'slider-factory' ); ?>">
					</div>
					<?php } ?>

					<div class="sf-slide-modal-field">
						<label><?php esc_html_e( 'Title', 'slider-factory' ); ?></label>
						<input type="text" class="form-control form-control-sm" id="sfSlideModalTitle" placeholder="<?php esc_attr_e( 'Slide Title', 'slider-factory' ); ?>">
					</div>
					<div class="sf-slide-modal-field">
						<label><?php esc_html_e( 'Description', 'slider-factory' ); ?></label>
						<textarea class="form-control form-control-sm" id="sfSlideModalDesc" rows="2" placeholder="<?php esc_attr_e( 'Slide Description', 'slider-factory' ); ?>"></textarea>
					</div>
					<div class="sf-slide-modal-field">
						<label><?php esc_html_e( 'Button 1', 'slider-factory' ); ?></label>
						<input type="text" class="form-control form-control-sm" id="sfSlideModalLinkText1" placeholder="<?php esc_attr_e( 'Button Text', 'slider-factory' ); ?>">
						<input type="text" class="form-control form-control-sm mt-1" id="sfSlideModalLink1" placeholder="<?php esc_attr_e( 'Button URL', 'slider-factory' ); ?>">
					</div>
					<div class="sf-slide-modal-field">
						<label><?php esc_html_e( 'Button 2', 'slider-factory' ); ?></label>
						<input type="text" class="form-control form-control-sm" id="sfSlideModalLinkText2" placeholder="<?php esc_attr_e( 'Button Text', 'slider-factory' ); ?>">
						<input type="text" class="form-control form-control-sm mt-1" id="sfSlideModalLink2" placeholder="<?php esc_attr_e( 'Button URL', 'slider-factory' ); ?>">
					</div>
					<div class="sf-slide-modal-field">
						<label><?php esc_html_e( 'Alt Text', 'slider-factory' ); ?></label>
						<input type="text" class="form-control form-control-sm" id="sfSlideModalAlt" placeholder="<?php esc_attr_e( 'Image Alt Text (SEO)', 'slider-factory' ); ?>">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" onclick="sfSlideModal.hide();"><?php esc_html_e( 'Cancel', 'slider-factory' ); ?></button>
					<button type="button" class="btn btn-primary" id="sfSlideModalSave"><i class="fas fa-check"></i> <?php esc_html_e( 'Save', 'slider-factory' ); ?></button>
				</div>
			</div>
		</div>
	</div>

	<!-- Custom Confirmation Modal (4px rounded corners, crisp slate borders, centered) -->
	<div id="sfConfirmModalOverlay">
		<div class="sf-confirm-modal-box">
			<div class="sf-confirm-modal-header">
				<h5 class="sf-confirm-modal-title" id="sfConfirmModalTitle"><?php esc_html_e( 'Confirm Action', 'slider-factory' ); ?></h5>
				<button type="button" class="sf-confirm-modal-close" onclick="SFcloseConfirmModal();" aria-label="<?php esc_attr_e( 'Close', 'slider-factory' ); ?>"><i class="fas fa-times"></i></button>
			</div>
			<div class="sf-confirm-modal-body" id="sfConfirmModalBody">
				<?php esc_html_e( 'Are you sure you want to proceed?', 'slider-factory' ); ?>
			</div>
			<div class="sf-confirm-modal-footer">
				<button type="button" class="sf-confirm-modal-btn-cancel" onclick="SFcloseConfirmModal();"><?php esc_html_e( 'Cancel', 'slider-factory' ); ?></button>
				<button type="button" class="sf-confirm-modal-btn-danger" id="sfConfirmModalBtnAction"><?php esc_html_e( 'Confirm', 'slider-factory' ); ?></button>
			</div>
		</div>
	</div>
</div>

<script>
var sfConfirmCallback = null;
function SFshowConfirmModal(title, message, confirmText, callback) {
	jQuery('#sfConfirmModalTitle').text(title || 'Confirm Action');
	jQuery('#sfConfirmModalBody').text(message || 'Are you sure you want to proceed?');
	jQuery('#sfConfirmModalBtnAction').text(confirmText || 'Confirm');
	sfConfirmCallback = callback;
	jQuery('#sfConfirmModalOverlay').addClass('sf-modal-show').css('display', 'flex');
}
function SFcloseConfirmModal() {
	jQuery('#sfConfirmModalOverlay').removeClass('sf-modal-show').css('display', 'none');
	sfConfirmCallback = null;
}
jQuery(document).on('click', '#sfConfirmModalBtnAction', function(e) {
	e.preventDefault();
	if (typeof sfConfirmCallback === 'function') {
		sfConfirmCallback();
	}
	SFcloseConfirmModal();
});

// sync both save buttons
jQuery('#sf-save-slider-bottom').click(function(){ jQuery('#sf-save-slider').trigger('click'); });

// copy shortcode to clipboard
function WpfrankSFCopyShortcode() {
	var copyShortcode = document.getElementById("sf-slider-shortcode-text");
	if (copyShortcode) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(copyShortcode.value);
		} else {
			copyShortcode.select();
			document.execCommand('copy');
		}
	}
	jQuery('#sf-copied').removeClass('d-none');
	setTimeout(function(){ jQuery('#sf-copied').addClass('d-none'); }, 2000);
}
window.WpfrankSFCopyShortcode = WpfrankSFCopyShortcode;
window.SFCopyShortcode = WpfrankSFCopyShortcode;

// remove single image slide
function SFremoveSlide(id) {
	SFshowConfirmModal(
		'Remove Slide',
		'Are you sure you want to remove this slide?',
		'Remove Slide',
		function() {
			jQuery(".sf_slide_" + id).fadeOut(700, function() {
				jQuery(".sf_slide_" + id).remove();
				SFrenumberSlides();
			});
		}
	);
	return false;
}

// remove all image slides
function SFremoveAllSlides() {
	SFshowConfirmModal(
		'Remove All Slides',
		'Are you sure you want to delete all slides? This action cannot be undone.',
		'Remove All',
		function() {
			jQuery(".image_info_tile").fadeOut(700, function() {
				jQuery(".image_info_tile").remove();
				SFrenumberSlides();
			});
		}
	);
	return false;
}

// print range call back
function WpfrankSFprintRange(id, value){
	field_name = '#' + id + '-value';
	jQuery(field_name).text(value);
}
// alias to maintain support for other scripts
window.WpfrankSFprintRange = WpfrankSFprintRange;
window.SFprintRange = WpfrankSFprintRange;

// Sizer override JS — Strip w-50, w-70, and column constraints from settings fields inside sidebar
jQuery(document).ready(function() {
	jQuery('.sf-settings-container .w-50, .sf-settings-container .w-70').removeClass('w-50 w-70');
	jQuery('.sf-settings-container .mb-3').removeClass('col-md-6 col-md-3 col-md-4 col-md-12 col-sm-6 col-sm-12');

	// Auto-group settings .mb-3 fields into collapsible sections by keyword.
	(function(){
		var $container = jQuery('.sf-settings-container .sf-panel-setting');
		if (!$container.length) return;

		// Remove HTML comment nodes from the container to prevent them from piling up
		$container.contents().filter(function(){ return this.nodeType === 8; }).remove();

		// 1. Normalize all settings fields to have the consistent sf-setting-row layout
		$container.find('.mb-3').each(function() {
			var $this = jQuery(this);
			
			// Skip if already formatted
			if ($this.hasClass('sf-setting-row')) {
				return;
			}
			
			// Skip if it contains nested columns or multiple separate controls
			if ($this.find('.btn-group').length > 1 || $this.find('select').length > 1 || $this.find('input[type="color"]').length > 1 || $this.find('input[type="text"]').length > 1) {
				return;
			}
			
			// If parent is a bootstrap row, remove the class to avoid horizontal flex squishing
			var $parentRow = $this.parent('.row');
			if ($parentRow.length) {
				$parentRow.removeClass('row');
			}

			// Must have a label/title to be classified as a settings row
			var $title = $this.find('h5, label.form-label, .sf-title').first();
			if (!$title.length) {
				return;
			}
			
			// Find tooltip/help text
			var $tooltip = $this.find('.form-text, .sf-tooltip').first();
			
			// Detach title and tooltip first to separate them from the control components
			$title.detach().removeClass('form-label').addClass('sf-title');
			if ($tooltip.length) {
				$tooltip.detach();
			}
			
			// Normalize inner controls before detaching
			$this.find('input[type="color"]').each(function() {
				var $el = jQuery(this);
				if (!$el.hasClass('sf-color-swatch')) {
					$el.addClass('form-control form-control-color border-0 p-0 m-0 sf-color-input');
				}
				var cid = $el.attr('id');
				$this.find('#' + cid + '-value, button[id$="-value"]').addClass('btn btn-sm btn-secondary d-inline-flex align-items-center justify-content-center m-0 px-3 sf-color-val-btn');
			});

			// Detach all remaining child content (including texts, inputs, color pickers, spans, etc.)
			var $remaining = $this.contents().detach();
			
			$remaining.find('input[type="text"], textarea').each(function() {
				var $input = jQuery(this);
				if (!$input.parent().is('.sp-replacer, .sp-container')) { // skip spectrum color picker elements
					$input.addClass('form-control').removeClass('w-50 w-70');
					var inputId = $input.attr('id') || '';
					var isWide = !/width|height|cols|rows|speed|duration|delay|ratio|size|gap|interval|min|start/i.test(inputId);
					if (isWide) {
						$input.addClass('sf-wide-input');
					}
				}
			});
			
			$remaining.find('select').addClass('form-select');
			
			$remaining.find('input[type="range"]').each(function() {
				var $rangeInput = jQuery(this);
				var rangeValId = $rangeInput.attr('id') + '-value';
				$remaining.find('#' + rangeValId).addClass('btn btn-sm btn-secondary d-inline-flex align-items-center justify-content-center m-0 px-3 sf-color-val-btn');
			});
			
			// Assemble the layout columns
			var $left = jQuery('<div class="sf-setting-row-left"></div>');
			$left.append($title);
			if ($tooltip.length) {
				$left.append($tooltip);
			}
			
			var $right = jQuery('<div class="sf-setting-row-right d-flex align-items-center gap-2"></div>');
			$right.append($remaining);
			
			// Insert the restructured layout
			$this.empty().append($left).append($right).addClass('sf-setting-row');
		});

		// Section rules: keyword(s) in the field label → section title + icon.
		var rules = [
			{ keys: ['width','height','size','dimensions','ratio','visibleSize'], title: 'Dimensions',  icon: 'fa-ruler-combined' },
			{ keys: ['preset','design','theme','skin','layout'],                 title: 'Design',      icon: 'fa-palette' },
			{ keys: ['autoplay','auto_play','transition','speed','fade','animation','duration','delay','easing','fx'], title: 'Playback & Animation', icon: 'fa-play-circle' },
			{ keys: ['navigation','arrow','dots','thumbnail','button','pagination','nav','play/pause','playpause','keyboard','touch','swipe','buttons','arrows'], title: 'Navigation', icon: 'fa-compass' },
			{ keys: ['color','colour','bg','background','text','caption','overlay','heading','header'], title: 'Appearance', icon: 'fa-paint-brush' },
			{ keys: ['sorting','order','align','rtl','direction','orientation','position','responsive','loop','infinite','fullscreen','full_screen','clone','cloning','jump','start','hover','pause','click'], title: 'Behavior', icon: 'fa-sliders-h' },
			{ keys: ['css','custom'],                                             title: 'Custom CSS',  icon: 'fa-code' }
		];

		function classify($field){
			// Normalize label
			var raw = $field.find('.form-label, .sf-title').first().text().toLowerCase().replace(/[\s_]+/g, '');
			for (var i = 0; i < rules.length; i++){
				for (var k = 0; k < rules[i].keys.length; k++){
					var needle = rules[i].keys[k].toLowerCase().replace(/[\s_]+/g, '');
					if (raw.indexOf(needle) !== -1){ return rules[i]; }
				}
			}
			return { title: 'Other', icon: 'fa-cog' };
		}

		var groups = {};
		var order = [];
		$container.children('.mb-3').each(function(){
			var $f = jQuery(this);
			var r = classify($f);
			if (!groups[r.title]){
				groups[r.title] = { rule: r, fields: [] };
				order.push(r.title);
			}
			groups[r.title].fields.push($f);
		});

		if (order.length <= 1) return; // nothing to section

		var $wrap = jQuery('<div class="sf-sections-wrap"></div>');
		order.forEach(function(title, idx){
			var g = groups[title];
			var $section = jQuery(
				'<div class="sf-section' + (idx === 0 ? ' is-open' : '') + '">' +
					'<div class="sf-section-header">' +
						'<span class="sf-section-title"><i class="fas ' + g.rule.icon + '"></i> ' + g.rule.title + '</span>' +
						'<span class="sf-section-meta">' +
							'<i class="fas fa-chevron-down sf-section-chevron"></i>' +
						'</span>' +
					'</div>' +
					'<div class="sf-section-body"></div>' +
				'</div>'
			);
			var $body = $section.find('.sf-section-body');
			g.fields.forEach(function($f){ $body.append($f); });
			$wrap.append($section);
		});

		$container.append($wrap);

		// Toggle handler
		$container.on('click', '.sf-section-header', function(){
			jQuery(this).closest('.sf-section').toggleClass('is-open');
		});
	})();

	/* =====================================================================
	   SLIDE EDIT MODAL — reads/writes the hidden form inputs inside each
	   card so the existing .serialize() save flow is fully preserved.
	   ===================================================================== */
	var sfSlideModalEl = document.getElementById('sfSlideModal');
	var sfSlideModal = {
		el: sfSlideModalEl,
		show: function() {
			if (this.el) {
				this.el.classList.add('sf-modal-open');
				var firstInput = document.getElementById('sfSlideModalTitle');
				if (firstInput) firstInput.focus();
			}
		},
		hide: function() {
			if (this.el) {
				this.el.classList.remove('sf-modal-open');
			}
		}
	};
	window.sfSlideModal = sfSlideModal;

	// Dismiss modal on clicking backdrop (disabled per user request)
	/* if (sfSlideModalEl) {
		sfSlideModalEl.addEventListener('click', function(e) {
			if (e.target === sfSlideModalEl) {
				sfSlideModal.hide();
			}
		});
	} */

	// Dismiss modal on Escape key
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' || e.keyCode === 27) {
			sfSlideModal.hide();
		}
	});

	function SFopenSlideModal(id) {
		var $card = jQuery('.sf_slide_' + id);
		if (!$card.length) return false;
		// Read current values from the hidden inputs
		jQuery('#sfSlideModalAttachmentId').val(id);
		jQuery('#sfSlideModalPreview').attr('src', $card.find('.sf-slide-card-img').attr('src'));
		jQuery('#sfSlideModalTitle').val($card.find('.sf_slide_title').val() || '');
		jQuery('#sfSlideModalDesc').val($card.find('.sf_slide_desc').val() || '');
		jQuery('#sfSlideModalLinkText1').val($card.find('.sf_slide_link_text_1').val() || '');
		jQuery('#sfSlideModalLink1').val($card.find('.sf_slide_link_1').val() || '');
		jQuery('#sfSlideModalLinkText2').val($card.find('.sf_slide_link_text_2').val() || '');
		jQuery('#sfSlideModalLink2').val($card.find('.sf_slide_link_2').val() || '');
		jQuery('#sfSlideModalAlt').val($card.find('.sf_slide_alt_text').val() || '');

		var $videoWrap = jQuery('#sfSlideModalVideoWrap');
		if ($videoWrap.length) {
			jQuery('#sfSlideModalMediaType').val($card.find('.sf_slide_type').val() || 'image');
			jQuery('#sfSlideModalVideoType').val($card.find('.sf_slide_videoType').val() || 'youtube');
			jQuery('#sfSlideModalVideoSrc').val($card.find('.sf_slide_videoSrc').val() || '');
			SFToggleVideoFields();
		}

		// Previous & Next navigation logic
		var $prevCard = $card.prev('.image_info_tile');
		var $nextCard = $card.next('.image_info_tile');

		if ($prevCard.length) {
			jQuery('#sfSlideModalPrevBtn').show().data('target-id', $prevCard.data('attachment-id'));
		} else {
			jQuery('#sfSlideModalPrevBtn').hide();
		}

		if ($nextCard.length) {
			jQuery('#sfSlideModalNextBtn').show().data('target-id', $nextCard.data('attachment-id'));
		} else {
			jQuery('#sfSlideModalNextBtn').hide();
		}

		sfSlideModal.show();
		return false;
	}
	// expose for inline onclick handlers
	window.SFopenSlideModal = SFopenSlideModal;

	function SFToggleVideoFields() {
		var isVideo = jQuery('#sfSlideModalMediaType').val() === 'video';
		jQuery('#sfSlideModalVideoWrap').toggleClass('d-none', !isVideo);
		var vt = jQuery('#sfSlideModalVideoType').val();
		var ph = 'Youtube-ID Only';
		if (vt === 'vimeo') ph = 'Vimeo-ID Only';
		else if (vt === 'internal') ph = 'Full Internal Video Link';
		jQuery('#sfSlideModalVideoSrc').attr('placeholder', ph);
	}

	jQuery(document).on('change', '#sfSlideModalMediaType, #sfSlideModalVideoType', SFToggleVideoFields);

	// Helper function to save modal data to hidden fields on the card
	function SFsaveModalData(id) {
		var $card = jQuery('.sf_slide_' + id);
		if (!$card.length) return false;
		$card.find('.sf_slide_title').val(jQuery('#sfSlideModalTitle').val());
		$card.find('.sf_slide_desc').val(jQuery('#sfSlideModalDesc').val());
		$card.find('.sf_slide_link_text_1').val(jQuery('#sfSlideModalLinkText1').val());
		$card.find('.sf_slide_link_1').val(jQuery('#sfSlideModalLink1').val());
		$card.find('.sf_slide_link_text_2').val(jQuery('#sfSlideModalLinkText2').val());
		$card.find('.sf_slide_link_2').val(jQuery('#sfSlideModalLink2').val());
		$card.find('.sf_slide_alt_text').val(jQuery('#sfSlideModalAlt').val());

		var $videoWrap = jQuery('#sfSlideModalVideoWrap');
		if ($videoWrap.length) {
			$card.find('.sf_slide_type').val(jQuery('#sfSlideModalMediaType').val());
			$card.find('.sf_slide_videoType').val(jQuery('#sfSlideModalVideoType').val());
			$card.find('.sf_slide_videoSrc').val(jQuery('#sfSlideModalVideoSrc').val());
		}
		
		// Trigger live preview update
		if (typeof SFupdatePreview === 'function') {
			SFupdatePreview();
		}
		return true;
	}
	window.SFsaveModalData = SFsaveModalData;

	// Save modal → write values back to the card's hidden inputs
	jQuery(document).on('click', '#sfSlideModalSave', function(){
		var id = jQuery('#sfSlideModalAttachmentId').val();
		SFsaveModalData(id);
		sfSlideModal.hide();
	});

	// Bind previous / next navigation handlers
	jQuery(document).on('click', '#sfSlideModalPrevBtn, #sfSlideModalNextBtn', function() {
		var currentId = jQuery('#sfSlideModalAttachmentId').val();
		SFsaveModalData(currentId); // save current data before switching
		var targetId = jQuery(this).data('target-id');
		if (targetId) {
			SFopenSlideModal(targetId);
		}
	});

	// Renumber index badges whenever the order changes (sortable stop / remove / create)
	function SFrenumberSlides() {
		jQuery('#sf-slides .image_info_tile').each(function(i){
			jQuery(this).find('.sf-slide-card-index').text('#' + (i + 1));
		});
		if (typeof SFupdatePreview === 'function') {
			SFupdatePreview();
		}
	}
	window.SFrenumberSlides = SFrenumberSlides;
	jQuery('#sf-slides').on('sortstop sortremove', SFrenumberSlides);
	SFrenumberSlides();

	// AJAX prefilter to intercept slider saves and inject/override the full slide info arrays
	jQuery.ajaxPrefilter(function(options, originalOptions, jqXHR) {
		if (options.data) {
			var isSave = false;
			if (typeof options.data === 'object' && options.data.action === 'sf_save_slider') {
				isSave = true;
			} else if (typeof options.data === 'string' && options.data.indexOf('action=sf_save_slider') !== -1) {
				isSave = true;
			}
			
			if (isSave) {
				var slideIds = jQuery('.sf_slide_id').serialize();
				var slideTitles = jQuery('input.sf_slide_title').serialize();
				var slideDescs = jQuery('.sf_slide_desc').serialize();
				var slideLinkTexts1 = jQuery('input.sf_slide_link_text_1').serialize();
				var slideLinks1 = jQuery('input.sf_slide_link_1').serialize();
				var slideLinkTexts2 = jQuery('input.sf_slide_link_text_2').serialize();
				var slideLinks2 = jQuery('input.sf_slide_link_2').serialize();
				var slideAlts = jQuery('input.sf_slide_alt_text').serialize();
				
				var slideType = jQuery('select.sf_slide_type').serialize();
				var slideVideoType = jQuery('select.sf_slide_videoType').serialize();
				var slideVideoSrc = jQuery('input.sf_slide_videoSrc').serialize();

				if (typeof options.data === 'object') {
					options.data.sf_slide_ids = slideIds;
					options.data.sf_slide_titles = slideTitles;
					options.data.sf_slide_descs = slideDescs;
					options.data.sf_slide_link_texts_1 = slideLinkTexts1;
					options.data.sf_slide_links_1 = slideLinks1;
					options.data.sf_slide_link_texts_2 = slideLinkTexts2;
					options.data.sf_slide_links_2 = slideLinks2;
					options.data.sf_slide_alts_text = slideAlts;
					options.data.sf_slide_type = slideType;
					options.data.sf_slide_videoType = slideVideoType;
					options.data.sf_slide_videoSrc = slideVideoSrc;
				} else if (typeof options.data === 'string') {
					options.data += '&' + slideIds + '&' + slideTitles + '&' + slideDescs +
						'&sf_slide_link_texts_1=' + encodeURIComponent(slideLinkTexts1) +
						'&sf_slide_links_1=' + encodeURIComponent(slideLinks1) +
						'&sf_slide_link_texts_2=' + encodeURIComponent(slideLinkTexts2) +
						'&sf_slide_links_2=' + encodeURIComponent(slideLinks2) +
						'&sf_slide_alts_text=' + encodeURIComponent(slideAlts) +
						'&sf_slide_type=' + encodeURIComponent(slideType) +
						'&sf_slide_videoType=' + encodeURIComponent(slideVideoType) +
						'&sf_slide_videoSrc=' + encodeURIComponent(slideVideoSrc);
				}
			}
		}
	});

	/* =====================================================================
	   LIVE PREVIEW SYSTEM — serializes all settings in real-time and
	   submits them to the isolated iframe via dynamic POST requests.
	   ===================================================================== */
	function SFupdatePreview() {
		var $iframe = jQuery('#sf-live-preview-iframe');
		if (!$iframe.length) return;

		var slideCount = jQuery('#sf-slides .image_info_tile').length;
		if (slideCount === 0) {
			jQuery('#sf-preview-overlay-loader').addClass('d-none');
			return;
		}

		jQuery('#sf-preview-overlay-loader').removeClass('d-none');

		var formId = 'sf-preview-hidden-form';
		var $form = jQuery('#' + formId);
		if (!$form.length) {
			$form = jQuery('<form>', {
				id: formId,
				method: 'POST',
				target: 'sf-live-preview-iframe',
				action: ajaxurl + '?action=sf_slider_preview'
			}).css('display', 'none').appendTo('body');
		}

		$form.empty();

		// Add editor info
		jQuery('<input>', { type: 'hidden', name: 'sf_slider_id', value: jQuery('#sf_slider_id').val() }).appendTo($form);
		jQuery('<input>', { type: 'hidden', name: 'sf_slider_layout', value: jQuery('#sf_slider_layout').val() }).appendTo($form);
		jQuery('<input>', { type: 'hidden', name: 'sf_slider_title', value: jQuery('#sf_slider_title').val() }).appendTo($form);
		jQuery('<input>', { type: 'hidden', name: 'sf_preview_nonce', value: jQuery('#sf_preview_nonce').val() }).appendTo($form);
		jQuery('<input>', { type: 'hidden', name: 'nonce', value: jQuery('#sf_preview_nonce').val() }).appendTo($form);

		// Resize preview iframe height dynamically to fit custom height settings (if specified in px/numeric)
		var layout = jQuery('#sf_slider_layout').val();
		var heightVal = jQuery('#sf_' + layout + '_height').val() || jQuery('#sf_' + layout + '_height-1').val() || '';
		if (heightVal && heightVal.indexOf('%') === -1) {
			var pxHeight = parseInt(heightVal, 10);
			if (!isNaN(pxHeight) && pxHeight > 100) {
				$iframe.css('height', (pxHeight + 40) + 'px');
			} else {
				$iframe.css('height', '380px');
			}
		} else {
			$iframe.css('height', '380px');
		}

		// Add all inputs inside the panel
		jQuery('.sf-panel input, .sf-panel select, .sf-panel textarea').each(function() {
			var $el = jQuery(this);
			var name = $el.attr('name');

			// If name is missing, but ID starts with sf_, derive the name from ID
			if (!name) {
				var id = $el.attr('id');
				if (id && id.indexOf('sf_') === 0) {
					name = id.replace(/-[0-9]+$/, '').replace(/-value$/, '');
				}
			}

			if (!name) return;

			if (($el.is(':radio') || $el.is(':checkbox')) && !$el.is(':checked')) {
				return;
			}

			jQuery('<input>', {
				type: 'hidden',
				name: name,
				value: $el.val()
			}).appendTo($form);
		});

		$form.submit();
	}
	window.SFupdatePreview = SFupdatePreview;

	function SFresizePreviewIframe() {
		try {
			var iframe = document.getElementById('sf-live-preview-iframe');
			if (!iframe) return;
			var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
			if (iframeDoc && iframeDoc.body) {
				iframeDoc.body.style.overflow = 'hidden';
				if (iframeDoc.documentElement) {
					iframeDoc.documentElement.style.overflow = 'hidden';
				}
				var newHeight = iframeDoc.documentElement.scrollHeight || iframeDoc.body.scrollHeight;
				if (newHeight && newHeight > 50) {
					var cappedHeight = Math.min(Math.max(newHeight + 16, 250), 480);
					jQuery(iframe).css('height', cappedHeight + 'px');
				}
			}
		} catch (e) {
			console.warn("Iframe auto-resize failed: ", e);
		}
	}

	jQuery('#sf-live-preview-iframe').on('load', function() {
		jQuery('#sf-preview-overlay-loader').addClass('d-none');
		SFresizePreviewIframe();
		setTimeout(SFresizePreviewIframe, 300);
		setTimeout(SFresizePreviewIframe, 1000);
	});

	window.addEventListener('message', function(e) {
		if (e.data && e.data.type === 'SF_PREVIEW_HEIGHT') {
			jQuery('#sf-preview-overlay-loader').addClass('d-none');
			if (e.data.height && e.data.height > 100) {
				var cappedHeight = Math.min(e.data.height, 480);
				jQuery('#sf-live-preview-iframe').css('height', cappedHeight + 'px');
			}
		}
	});

	// Trigger preview on initial page load
	setTimeout(SFupdatePreview, 200);

	// Refresh button
	jQuery(document).on('click', '#sf-refresh-preview', function(e) {
		e.preventDefault();
		SFupdatePreview();
	});

	// Dynamic triggers on settings changes
	jQuery(document).on('change input', '.sf-panel input, .sf-panel select', function(e) {
		if (jQuery(e.target).closest('#sf-live-preview-iframe').length) {
			return;
		}
		clearTimeout(window.sfPreviewTimeout);
		window.sfPreviewTimeout = setTimeout(SFupdatePreview, 600);
	});
	jQuery(document).on('change', '.sf-panel textarea', function(e) {
		if (jQuery(e.target).closest('#sf-live-preview-iframe').length) {
			return;
		}
		clearTimeout(window.sfPreviewTimeout);
		window.sfPreviewTimeout = setTimeout(SFupdatePreview, 600);
	});
});

// :has() fallback for older browsers
(function(){
	if (typeof CSS !== 'undefined' && CSS.supports && !CSS.supports('selector(:has(*))')) {
		document.querySelectorAll('.sf-panel .btn-group').forEach(function(g){
			if (g.querySelector('input[value="true"]')) {
				g.classList.add('sf-has-true');
				if (g.querySelector('input[value="true"]:checked')) {
					g.classList.add('sf-has-true-checked');
				}
				if (g.querySelector('input[value="false"]:checked')) {
					g.classList.add('sf-has-false-checked');
				}
			}
		});
		document.querySelectorAll('.sf-panel .mb-3 > p').forEach(function(p){
			if (p.querySelector('input[type="color"]')) {
				p.classList.add('sf-has-color-input');
			}
		});
		document.querySelectorAll('.sf-panel .mb-3').forEach(function(el){
			if (el.querySelector('.sf-title-disabled')) {
				el.classList.add('sf-has-disabled');
			}
		});
	}
})();
</script>
