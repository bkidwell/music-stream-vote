<?php

/**
 * Display a query form
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */

namespace GlumpNet\WordPress\MusicStreamVote;

?>

<form method="get" action="<?php echo $this->action_url(); ?>" id="music_query_form">
<input type="hidden" name="music_query" value="nick" />
<?php $this->wp_view_state(); ?>

<p>
<strong><label for="artist">Nick</label></strong><br />
<input type="text" name="nick" placeholder=""
    value="<?php echo esc_attr( $v['nick'] ); ?>" />
<input type="button" class="erase-input" value="&times;" name="clear_nick" />
</p>

<p>
<input type="submit" value="Search" />
</p>

</form>
