<?php

/**
 * Display a query form
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */

namespace GlumpNet\WordPress\MusicStreamVote;

/*

types of queries:

* type=track
  * parms = artist, title
  * result =
    * 1: all votes for track
    * >1: show matching tracks

*/

?>

<form method="get" action="<?php echo $this->action_url(); ?>" id="music_query_form">
<input type="hidden" name="music_query" value="track" />
<?php $this->wp_view_state(); ?>

<p>
<strong><label for="artist">Artist</label></strong><br />
<input type="text" name="artist" placeholder="(all)"
    value="<?php echo esc_attr( $v['artist'] ); ?>" />
<input type="button" class="erase-input" value="&times;" name="clear_artist" />
</p>

<p>
<strong><label for="artist">Title</label></strong><br />
<input type="text" name="title" placeholder="(all)"
    value="<?php echo esc_attr( $v['title'] ); ?>" />
<input type="button" class="erase-input" value="&times;" name="clear_title" />
</p>

<p>
<input type="submit" value="Search" />
</p>

</form>
