<?php
/**
 * View for Help pages
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */

namespace GlumpNet\WordPress\MusicStreamVote;
?>

<div class="wrap">

<?php screen_icon(); ?>
<?php echo "<h2>" . PLUGIN_NAME . ": $page_title</h2>"; ?>

<p>
<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo PLUGIN_SLUG; ?>">&larr; Back to Settings</a>
&nbsp; Help pages:&nbsp; <?php echo $this->get_page_list(); ?>
</p>

<?php echo $html ?>

</div>
