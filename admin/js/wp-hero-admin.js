/**
	* Media uploader script
	* see: http://code.tutsplus.com/tutorials/getting-started-with-the-wordpress-media-uploader--cms-22011
 */
function renderMediaUploader() {
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
	    $( '#feature-image-container' )
	        .children( 'img' )
	            .attr( 'src', json.url )
	            .attr( 'alt', json.caption )
	            .attr( 'title', json.title )
	                        .show()
	        .parent()
	        .removeClass( 'hidden' );

	    $( '#feature-image-src' ).val( json.url );
	 
	    // Next, hide the anchor responsible for allowing the user to select an image
	    $( '#feature-image-container' )
	        .prev()
	        .hide();
	     // Display the anchor for the removing the featured image
			$( '#feature-image-container' )
		    .next()
		    .show();

		  
		
		});


 
    file_frame.open();
 
}

function resetUploadForm( $ ) {
    'use strict';
 
    $( '#feature-image-container' )
        .children( 'img' )
        .hide();

    $( '#feature-image-container' )
        .prev()
        .show();
 
    $( '#feature-image-container' )
        .next()
        .hide()
        .addClass( 'hidden' );

    $( '#feature-image-src' ).val( '' );
 
}

(function( $ ) {
	'use strict';

	$('.color-picker').wpColorPicker();

	$( '#set-feature-image' ).on( 'click', function( evt ) {
      evt.preventDefault();
			renderMediaUploader();
  });

  $( '#remove-feature-image' ).on( 'click', function( evt ) {
      evt.preventDefault();
   		resetUploadForm( $ );
  });

})( jQuery );


