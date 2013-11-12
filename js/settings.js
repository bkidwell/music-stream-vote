jQuery(document).ready(function($) {
	var tabs = $( '.nav-tab' );
	var tabc = $( '.tab-content' );

	var goto_id = function ( id ) {
		var target = $('#tab_' + id);
		tabs.removeClass('nav-tab-active');
		target.addClass('nav-tab-active');
		tabc.hide();
		var div = $( '#tabc_' + id );
		div.show();
		div.find( 'input:first' ).focus();
	};

	tabs.click( function( event ) {
		var id = $( event.target ).attr( 'href' ).substring( 5 );
		goto_id( id );
	} );

	if ( location.hash ) {
		var id = ('' + location.hash).substring( 5 );
		goto_id( id );
	}

	window.setTimeout( function() {
		$( '.updated' ).slideUp();
	}, 5000	);
});
