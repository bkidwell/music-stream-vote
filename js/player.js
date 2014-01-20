$ = jQuery;
// mp3: "http://stream2.fralnet.com:8000/music-128.mp3",
jQuery( document ).ready( function( $ ) {
	var stream = {
		title: "OO Radio",
		mp3: "http://stream2.fralnet.com:8000/music-128.mp3"
	},
	ready = false;

	var playbtn = $("#playbtn");
	var playbtn_state = playbtn.find("span.glyphicon");
	var playing = false;
	var player;
	playbtn.click(function() {
		playing = !playing;
		if(playing) {
			playbtn_state.removeClass("glyphicon-play").addClass("glyphicon-stop");
			player.jPlayer("setMedia", stream).jPlayer("play");
		} else {
			playbtn_state.removeClass("glyphicon-stop").addClass("glyphicon-play");
			player.jPlayer("clearMedia");
		}
	});

	$("#jquery_jplayer").jPlayer({
		ready: function (event) {
			player = $(this);
			ready = true;
		},
		error: function(event) {
			if(ready && event.jPlayer.error.type === $.jPlayer.error.URL_NOT_SET) {
				// Setup the media stream again and play it.
				$(this).jPlayer("setMedia", stream).jPlayer("play");
			}
		},
		swfPath: "<?php echo PLUGIN_URL; ?>lib/jquery.jplayer",
		volume: 1,
		supplied: "mp3",
		preload: "none",
		wmode: "window",
		keyEnabled: true
	});


	var np = $( '.now-playing' );
	var recent = $( '.recent-tracks' );

	var now_playing_text = '';
	var loadNowPlaying = function() {
		$.get( MUSIC_STREAM_VOTE_URL + 'now_playing.txt', function( data ) {
			if ( data != now_playing_text ) {
				now_playing_text = data;
				np.text( data );
				if ( recent.length ) {
					$.get( MUSIC_STREAM_VOTE_URL + 'recent_tracks.txt', function( data ) {
						recent.html("");
						$.each(data.split("\n"), function(i, row) {
							var cols = row.split("\t");
							$("<span />").text(
								(new Date(cols[0])).toString().split(' ')[4].substring(0, 5)
							).appendTo(recent);
							recent.append("&nbsp; ");
							$("<span />").text(cols[1]).appendTo(recent);
							recent.append("<br />");
						});
					} );
				}
			}
			window.setTimeout( loadNowPlaying, 10000 );
		} );
	};

	// only do "now playing" polling if there is a "now playing" shortcode onscreen
	if ( np.length || recent.length ) {
		window.setTimeout( loadNowPlaying, 100 );	
	}
});
