jQuery( document ).ready( function( $ ) {
    $( ".pickdate" ).datepicker();
    $( ".erase-input" ).click( function ( e ) {
    	var target = $( e.target );
    	$( 'input[name=' + target.attr( 'name' ).substring( 6 ) + ']' ).val( '' );
    });
} );
