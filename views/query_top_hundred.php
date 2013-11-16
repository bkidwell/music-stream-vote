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
<input type="hidden" name="music_query" value="top_hundred" />
<?php $this->wp_view_state(); ?>

<p>
<strong><label for="artist">Start Time (UTC)</label></strong><br />
<input type="text" class="pickdate" name="start_date" placeholder="yyyy-mm-dd"
    value="<?php echo esc_attr( $v['start_date'] ); ?>" />
<input type="button" class="erase-input" value="&times;" name="clear_start_date" />
<input type="text" name="start_time" placeholder="hh:mm:ss"
    value="<?php echo esc_attr( $v['start_time'] ); ?>" />
<input type="button" class="erase-input" value="&times;" name="clear_start_time" />
</p>

<p>
<strong><label for="artist">Until (not including) (UTC)</label></strong><br />
<input type="text" class="pickdate" name="end_date" placeholder="yyyy-mm-dd"
    value="<?php echo esc_attr( $v['end_date'] ); ?>" />
<input type="button" class="erase-input" value="&times;" name="clear_end_date" />
<input type="text" name="end_time" placeholder="hh:mm:ss"
    value="<?php echo esc_attr( $v['end_time'] ); ?>" />
<input type="button" class="erase-input" value="&times;" name="clear_end_time" />
</p>

<p>
<input type="submit" value="Top Hundred" />
</p>

</form>
