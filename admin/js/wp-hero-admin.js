/**
	* Media uploader script
	* see: http://code.tutsplus.com/tutorials/getting-started-with-the-wordpress-media-uploader--cms-22011
 */
function renderMediaUploader(metaKey) {
    'use strict';
 
    var file_frame, image_data, json;

    if ( undefined !== file_frame ) {
 
        file_frame.open();
        return;
 
    }
 
    file_frame = wp.media.frames.file_frame = wp.media({
        frame:    'post',
        state:    'insert',
        multiple: false
    });
 
    file_frame.on( 'insert', function() {
 			json = file_frame.state().get( 'selection' ).first().toJSON();
 			//console.log (json);
 			// First, make sure that we have the URL of an image to display
	    if ( 0 > $.trim( json.url.length ) ) {
	        return;
	    }
	 
	    // After that, set the properties of the image and display it
	    $( '.image-container[data-meta-key="' + metaKey + '"]' )
	        .children( 'img' )
	            .attr( 'src', json.url )
	            .attr( 'alt', json.caption )
	            .attr( 'title', json.title )
	            .show()
	            .parent()
	            .removeClass( 'hidden' );

	    $( '.image-src[data-meta-key="' + metaKey + '"]' ).val( json.url );
	 
	    // Next, hide the anchor responsible for allowing the user to select an image
	    $( '.image-container[data-meta-key="' + metaKey + '"]' )
	        .prev()
	        .hide();
	     // Display the anchor for the removing the featured image
			$( '.image-container[data-meta-key="' + metaKey + '"]' )
		    .next()
		    .show();

		  
		
		});


 
    file_frame.open();
 
}

function resetUploadForm( metaKey ) {
    'use strict';
 
    $( '.image-container[data-meta-key="' + metaKey + '"]' )
        .children( 'img' )
        .hide();

    $( '.image-container[data-meta-key="' + metaKey + '"]' )
        .prev()
        .show();
 
    $( '.image-container[data-meta-key="' + metaKey + '"]' )
        .next()
        .hide()
        .addClass( 'hidden' );

    $( '.image-src[data-meta-key="' + metaKey + '"]' ).val( '' );
 
}

(function( $ ) {
	'use strict';
  console.log('[WP Hero] init')

	$('.color-picker').wpColorPicker();

	$( '.wp-hero-media-select' ).on( 'click', function( e ) {
      var metaKey;
      e.preventDefault();
      metaKey = e.target.dataset.metaKey;
			renderMediaUploader(metaKey);
  });

  $( '.wp-hero-remove-media' ).on( 'click', function( e ) {
      var metaKey;
      e.preventDefault();
      metaKey = e.target.dataset.metaKey;
   		resetUploadForm( metaKey );
  });

  $('#wp-hero_media-type').on('change', function( e ){
    $.ajax({
      url: ajaxurl,
      data: {
        'action': 'get_media_fields',
        'mediaType': $(this).val(),
        'post_id': $('input[name="post_ID"]').val()
      },
      type: 'post',
      beforeSend: function() {
        return $('.spinner').addClass('is-active');
      },
      success: function(html){
        $('#wp_hero-media-fields').html(html);
      },
      error: function(xhr, status, error){
        return console.error(error);
      },
      complete: function() {
        return $('.spinner').removeClass('is-active');
      }
    });
  });

})( jQuery );


