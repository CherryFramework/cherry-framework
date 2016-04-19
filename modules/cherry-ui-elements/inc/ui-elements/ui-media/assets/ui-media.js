/**
 * Media
 */
( function( $, CherryJsCore ){
	'use strict';

	CherryJsCore.utilites.namespace('ui_elements.media');
	CherryJsCore.ui_elements.media = {
		init: function ( target ) {
			var self = this;
			if( CherryJsCore.status.document_ready ){
				self.render( target );
			}else{
				CherryJsCore.variable.$document.on('ready', self.render( target ) );
			}
		},
		render: function ( target ) {
			$( document ).on(
				'click',
				'.upload-button',
				function(e){
					var button_parent = $(e.currentTarget).parents('.cherry-ui-media-wrap'),
						input = $('.cherry-upload-input', button_parent),
						title_text = $(e.currentTarget).data('title'),
						multiple = $(e.currentTarget).data('multi-upload'),
						library_type = $(e.currentTarget).data('library-type');

					CherryJsCore.ui_elements.media.img_holder = $('.cherry-upload-preview', button_parent);

					if ( undefined !== CherryJsCore.ui_elements.media.uploader ) {
						CherryJsCore.ui_elements.media.uploader.open();
						return;
					}

					CherryJsCore.ui_elements.media.uploader = wp.media.frames.file_frame = wp.media({
						title: title_text,
						button: {text: title_text},
						multiple: multiple,
						library : { type : library_type }
					});

					CherryJsCore.ui_elements.media.uploader.on('select', function() {
						var attachment = CherryJsCore.ui_elements.media.uploader.state().get('selection').toJSON(),
							count = 0,
							input_value = '',
							new_img = '',
							delimiter = '';

						if ( multiple ) {
							input_value = input.val();
							delimiter = ',';
							new_img = $('.cherry-all-images-wrap', CherryJsCore.ui_elements.media.img_holder).html();
						}

						while(attachment[count]){
							var img_data = attachment[count],
								return_data = img_data.id,
								mimeType = img_data.mime,
								img_src = '',
								thumb = '';

								switch (mimeType) {
									case 'image/jpeg':
									case 'image/png':
									case 'image/gif':
											if ( undefined !== img_data.sizes ) {
												img_src = img_data.sizes.thumbnail ? img_data.sizes.thumbnail.url : img_data.sizes.full.url;
											}
											thumb = '<img  src="' + img_src + '" alt="" data-img-attr="' + return_data + '">';
										break;
									case 'image/x-icon':
											thumb = '<span class="dashicons dashicons-format-image"></span>';
										break;
									case 'video/mpeg':
									case 'video/mp4':
									case 'video/quicktime':
									case 'video/webm':
									case 'video/ogg':
											thumb = '<span class="dashicons dashicons-format-video"></span>';
										break;
									case 'audio/mpeg':
									case 'audio/wav':
									case 'audio/ogg':
											thumb = '<span class="dashicons dashicons-format-audio"></span>';
										break;
								}

								new_img += '<div class="cherry-image-wrap">'+
											'<div class="inner">'+
												'<div class="preview-holder"  data-id-attr="' + return_data +'"><div class="centered">' + thumb + '</div></div>'+
												'<a class="cherry-remove-image" href="#"><i class="dashicons dashicons-no"></i></a>'+
												'<span class="title">' + img_data.title + '</span>'+
											'</div>'+
										'</div>';

							input_value += delimiter+return_data;
							count++;
						}

						input.val(input_value.replace(/(^,)/, '')).trigger( 'change' );

						$('.cherry-all-images-wrap', CherryJsCore.ui_elements.media.img_holder).html(new_img);

						$('.cherry-remove-image').on('click', function () {
							removeMediaPreview( $(this) );
							return !1;
						});
					}).open();

					return !1;
				}
			);

			// This function remove upload image
			jQuery('.cherry-remove-image', target).on('click', function () {
				removeMediaPreview( jQuery(this) );
				return !1;
			});

			var removeMediaPreview = function( item ){
				var button_parent = item.parents('.cherry-ui-media-wrap'),
					input = jQuery('.cherry-upload-input', button_parent),
					img_holder = item.parent().parent('.cherry-image-wrap'),
					img_attr = jQuery('.preview-holder', img_holder).data('id-attr'),
					imput_value = input.attr('value'),
					pattern = new RegExp(''+img_attr+'(,*)', 'i');

					imput_value = imput_value.replace(pattern, '');
					imput_value = imput_value.replace(/(,$)/, '');
					input.attr({'value':imput_value}).trigger( 'change' );
					img_holder.remove();
			};

			// Upload End
			// Image ordering
			jQuery('.cherry-all-images-wrap', target).sortable({
				items: 'div.cherry-image-wrap',
				cursor: 'move',
				scrollSensitivity: 40,
				forcePlaceholderSize: true,
				forceHelperSize: false,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'cherry-media-thumb-sortable-placeholder',
				update: function() {
					var attachment_ids = '';
						jQuery('.cherry-image-wrap', this).each(
							function() {
								var attachment_id = jQuery('.preview-holder', this).data( 'id-attr' );
									attachment_ids = attachment_ids + attachment_id + ',';
							}
						);
						attachment_ids = attachment_ids.substr(0, attachment_ids.lastIndexOf(',') );
						jQuery(this).parent().siblings('.cherry-element-wrap').find('input.cherry-upload-input').val( attachment_ids ).trigger( 'change' );
				}
			});
			// End Image ordering
		}
	};

	$( window ).on( 'cherry-ui-elements-init',
		function( event, data ) {
			CherryJsCore.ui_elements.media.init( data.target );
		}
	);
} ( jQuery, window.CherryJsCore ));
