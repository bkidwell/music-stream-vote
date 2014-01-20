<?php
namespace GlumpNet\WordPress\MusicStreamVote;

?><!DOCTYPE html>
<html>
<head>
	<title>Player</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
	</script>
	<script type="text/javascript" src="<?php echo PLUGIN_URL; ?>js/player.js"></script>
</head>
<body>

<div class="container">

	<h1>Player</h1>

	<p class="lead">
		<button id="playbtn" type="button" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-play"></span></button>
		&nbsp;
		<span class="now-playing">...</span>
	</p>

	<p>Recent tracks:<br />
		<span class="recent-tracks">
		</span>
	</p>

</div>

<div id="jquery_jplayer"></div>

</body>
</html>
