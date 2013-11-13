jQuery(document).ready(function($) {
	var np = $( '.now-playing' );
	var recent = $( '.recent-tracks' );

	var now_playing_text = '';
	var loadNowPlaying = function() {
		$.get( MUSIC_STREAM_VOTE_URL + 'now_playing.txt', function( data ) {
			if ( data != now_playing_text ) {
				now_playing_text = data;
				np.text( data );
				if ( recent.length ) {
					$.get( MUSIC_STREAM_VOTE_URL + 'recent_tracks.html', function( data ) {
						recent.html( data );
					} );
				}
			}
			window.setTimeout( loadNowPlaying, 10000 );
		} );
	};

	// only do "now playing" polling if there is a "now playing" shortcode onscreen
	if ( np.length || recent.length ) {
		window.setTimeout( loadNowPlaying, 10000 );	
	}
});
