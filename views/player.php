<?php
namespace GlumpNet\WordPress\MusicStreamVote;

?><!DOCTYPE html>
<html>
<head>
	<title><?php echo esc_html( $opt->player_title ); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">
	<script type="text/javascript" src="<?php echo $site_root; ?>wp-includes/js/jquery/jquery.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo PLUGIN_URL; ?>lib/jquery.jplayer/jquery.jplayer.min.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	<script type="text/javascript">
	MUSIC_STREAM_VOTE_URL = '<?php echo PLUGIN_URL; ?>';
	STREAM_URL = '<?php echo esc_js( $opt->stream_audio_url ); ?>';
	</script>
	<script type="text/javascript" src="<?php echo PLUGIN_URL; ?>js/player.js"></script>
</head>
<body>

<div class="container">

	<h1><?php
		if ( strlen( $opt->player_banner_url )) {
			echo '<img src="' . $opt->player_banner_url . '" />';
		} else {
			echo esc_html( $opt->player_title );
		}
	?></h1>

	<p class="lead">
		<button id="playbtn" type="button" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-play"></span></button>
		&nbsp;
		<span class="now-playing">...</span>
	</p>

	<p>Recent tracks:<br />
		<span class="recent-tracks">
		</span>
	</p>

	<p>
		<button id="persona-login" type="button" class="btn btn-default">
			Login
		</button>
		<button id="persona-logout" type="button" class="btn btn-default">
			Log out
		</button>
	</p>

	<p>
		<button type="button" class="btn btn-default">-5</button>
		<button type="button" class="btn btn-default">-4</button>
		<button type="button" class="btn btn-default">-3</button>
		<button type="button" class="btn btn-default">-2</button>
		<button type="button" class="btn btn-default">-1</button>
		<button type="button" class="btn btn-default"> 0</button><br />
		<button type="button" class="btn btn-default">+1</button>
		<button type="button" class="btn btn-default">+2</button>
		<button type="button" class="btn btn-default">+3</button>
		<button type="button" class="btn btn-default">+4</button>
		<button type="button" class="btn btn-default">+5</button>
	</p>

</div>

<div id="jquery_jplayer"></div>

</body>
</html>
