jQuery(document).ready(function($) {
	var tabs = $( '.nav-tab' );
	var tabc = $( '.tab-content' );

	tabs.click( function( event ) {
		var target = $( event.target );
		var id = target.attr( 'href' ).substring( 5 );
		// alert('#tabc_' + id);
		tabs.removeClass('nav-tab-active');
		target.addClass('nav-tab-active');
		tabc.hide();
		var div = $( '#tabc_' + id );
		div.show();
		div.find( 'input:first' ).focus();
		event.preventDefault();
	} );

	window.setTimeout( function() {
		$( '.updated' ).slideUp();
	}, 5000	);
});
