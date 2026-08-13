/**
 * @sf uploader v1.0.0 - MIT License
 */
jQuery(function(jQuery) {
	var file_frame,
	SF = {
		ul: '',
		init: function() {
			/**
			 * Add Image Callback Function
			 */
			jQuery('#sf-upload-slides').on('click', function(event) {
				event.preventDefault();
				var layout_num = jQuery(this).val();
				var currentCount = jQuery('.sf-slide-card-wrapper').length;
				if (layout_num == 12 && currentCount >= 2) {
					alert('Before-After Compare layout only supports a maximum of 2 images. Please remove an existing slide to add a new one.');
					return;
				}

				if (file_frame) {
					file_frame.open();
					return;
				}
				file_frame = wp.media.frames.file_frame = wp.media({
					multiple: true
				});

				file_frame.on('select', function() {
					var images = file_frame.state().get('selection').toJSON(),
							length = images.length;
					var currentCount = jQuery('.sf-slide-card-wrapper').length;
					if (layout_num == 12 && currentCount + length > 2) {
						alert('Before-After Compare layout only supports a maximum of 2 images. Only the first ' + (2 - currentCount) + ' selected image(s) will be added.');
						length = 2 - currentCount;
					}
					for (var i = 0; i < length; i++) {
						SF.get_thumbnail(images[i]['id'], layout_num);
					}
				});
				file_frame.open();
			});
		},
		get_thumbnail: function(id, layout_num, cb) {
			cb = cb || function() {
			};
			
			var sf_slider_id = jQuery("#sf_slider_id").val();
			var sf_upload_nonce = jQuery("#sf_upload_nonce").val();
			var data = {
				action: 'sf_image_id',
				sf_attachment_id: id,
				sf_slider_id: sf_slider_id,
				layout_num: layout_num,
				sf_upload_nonce: sf_upload_nonce,
			};
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				async: false,
				dataType: 'html',
				data: data,
				complete: function() { },
				success: function(response) {
					jQuery(".sf-slides").append(response);
					// renumber index badges so AJAX-added cards show their position
					if (typeof SFrenumberSlides === 'function') {
						SFrenumberSlides();
					} else {
						jQuery('#sf-slides .image_info_tile').each(function(i){
							jQuery(this).find('.sf-slide-card-index').text('#' + (i + 1));
						});
					}
					cb();
				}
			});
		}
	};
	SF.init();
});
