jQuery( document ).ready( function( $ ) {
    $( ".pickdate" ).datepicker( { dateFormat: 'yy-mm-dd' } );
    //$( ".pickdate" ).datepicker( );
    $( ".erase-input" ).click( function ( e ) {
    	var target = $( e.target );
    	$( 'input[name=' + target.attr( 'name' ).substring( 6 ) + ']' ).val( '' );
    });
    $( "table.music-results" ).tablesorter();
} );
