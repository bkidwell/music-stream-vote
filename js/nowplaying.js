jQuery(document).ready(function($) {
	var spans = $( '.now-playing' );

	var loadNowPlaying = function() {
		$.get( MUSIC_STREAM_VOTE_URL + 'now_playing.txt', function( data ) {
			spans.text( data );
		});
		window.setTimeout( loadNowPlaying, 10000 );
	};

	// only do "now playing" polling if there is a "now playing" shortcode onscreen
	if ( spans.length ) {
		window.setTimeout( loadNowPlaying, 10000 );	
	}
});
