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
<style type="text/css">
h2.nav-tab-wrapper .nav-tab {
    font-size: 15px;
    font-weight: normal;
    line-height: 18px;
    padding: 2px 6px 2px;
    margin: 0px 0px -1px 0px;
}
div.msv-help ul {
    list-style: disc outside;
    margin-left: 1.8em;
}
div.msv-help ol {
    list-style: decimal outside;
    margin-left: 1.8em;
}
div.msv-help li {
    margin: .5em 0;
}
</style>

<div class="wrap msv-help">

<?php screen_icon(); ?>
<h2 class="nav-tab-wrapper">
<?php echo PLUGIN_NAME . " Help"; ?><br />
<?php foreach ( $pages as $page ) :
    $active = ( $page[0] == $page_name ) ? ' nav-tab-active' : '';
    $url = $_SERVER['SCRIPT_NAME'] . '?page=' . PLUGIN_SLUG . '&help=' . $page[0];
?>
<a class="nav-tab<?php echo $active; ?>" href="<?php echo $url; ?>"><?php echo esc_html( $page[1] ); ?></a>
<?php endforeach; ?>
</h2>

<p>
<a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo PLUGIN_SLUG; ?>">&larr; Back to Settings</a>
</p>

<?php echo $html ?>

</div>
