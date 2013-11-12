<?php
/**
 * View for Settings screen
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */

namespace GlumpNet\WordPress\MusicStreamVote;

$option_defs = OptionDefs::$option_defs;
?>

<style type="text/css">
.regular-text.msv-input-wide { width: 50em; }
.regular-text.msv-input-tall { width: 50em; height: 14em; }
h2.nav-tab-wrapper.msv-tabs .nav-tab {
	font-size: 15px;
	font-weight: normal;
	line-height: 18px;
	padding: 2px 6px 2px;
	margin: 0px 0px -1px 0px;
}
div.tab-content { display: none; }
div.tab-content.tab-content-active { display: inherit; }
</style>

<div class="wrap">
<form name="<?php echo PLUGIN_SLUG; ?>_form" method="post">

<?php screen_icon(); ?>
<h2 class="nav-tab-wrapper msv-tabs">
<?php echo esc_html( PLUGIN_NAME ); ?><br />
<a class="nav-tab nav-tab-active" href="#tab_status">Status</a>
<?php foreach ( $option_defs as $group_name => $defs ) : ?>
<a class="nav-tab" href="#tab_<?php echo Util::get_slug( $group_name ); ?>"><?php echo esc_html( $group_name ); ?></a>
<?php endforeach; ?>
</h2>

<?php if ( $opt_saved ) : ?>
<?php if ( $opt_restarted ) : ?>
<div class="updated"><p><strong>Settings saved. Bot restared (if running).</strong></p></div>
<?php else : ?>
<div class="updated"><p><strong>Settings saved.</strong></p></div>
<?php endif; ?>
<?php endif; ?>

<div class="tab-content tab-content-active" id="tabc_status">
<h3 class="title">Status</h3>
<table class="form-table"><tbody>
	<tr valign="top">
	<th scope="row"><label>Bot startup time</label></th>
	<td>
		<p><?php echo $out['start_time']; ?></p>
	</td>
	</tr>
</tbody></table>
</div>

<input type="hidden" name="<?php echo PLUGIN_SLUG; ?>_o" value="1">

<?php

// echo "<pre>"; print_r($option_defs); echo "</pre>";
foreach ( $option_defs as $group_name => $defs ) : ?>

	<div class="tab-content" id="tabc_<?php echo Util::get_slug( $group_name ); ?>">

	<h3 class="title"><?php echo $group_name; ?></h3>
	<table class="form-table"><tbody>

	<?php
	foreach ( $defs as $option_name => $attr ) :

	$is_textarea = $attr['c'] == 'msv-input-tall';
	$input_class = trim( 'regular-text ' . $attr['c'] );
	$value = $opt->__get($option_name);
	?>

	<tr valign="top">
	<th scope="row">
		<label for="<?php echo PLUGIN_SLUG . '_' . $option_name; ?>"><?php echo $attr['t']; ?></label>
	</th>
	<td>
		<?php if ( !$is_textarea ) : ?>
		<input name="<?php echo PLUGIN_SLUG . '_' . $option_name; ?>"
		value="<?php echo esc_attr( $value ); ?>" class="<?php echo $input_class; ?>" type="text">
		<?php else : ?>
		<textarea name="<?php echo PLUGIN_SLUG . '_' . $option_name; ?>" class="<?php echo $input_class; ?>"
		><?php echo esc_html( $value ); ?></textarea>
		<?php endif; ?>
		<p class="description"><?php echo esc_html( $attr['h'] ); ?></p>
	</td>
	</tr>

	<?php
	endforeach;

?></tbody></table></div><?php
endforeach;
?>

<!-- make a line at the bottom of the options pane -->
<h2 class="nav-tab-wrapper"></h2>

<p class="submit">
<input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit" />
</p>

</form>
</div>
