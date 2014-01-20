jQuery( document ).ready( function( $ ) {
	window.setTimeout( function() {
		$( '.updated' ).slideUp();
	}, 5000	);

	var tabs = $( '.msv-tabs .nav-tab' );
	var tabc = $( '.tab-content' );

	if ( tabs.length == 0 ) { return; }

	var goto_id = function ( id ) {
		var target = $( '#tab_' + id );
		tabs.removeClass( 'nav-tab-active' );
		target.addClass( 'nav-tab-active' );
		tabc.hide();
		var div = $( '#tabc_' + id );
		div.show();
		window.setTimeout( function() {
			div.find( 'input:first' ).focus();
		}, 100 );
		// alert(div.find('input:first').html());
	};

	tabs.click( function( event ) {
		var id = $( event.target ).attr( 'href' ).substring( 5 );
		goto_id( id );
	} );

	if ( location.hash ) {
		var id = ('' + location.hash).substring( 5 );
		goto_id( id );
	}
} );
