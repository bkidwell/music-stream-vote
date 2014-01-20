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
.form-table th {
	font-weight: bold;
	/* border-bottom: 1px solid Silver; */
}
</style>

<div class="wrap">
<form name="<?php echo PLUGIN_SLUG; ?>_form" method="post">

<?php screen_icon(); ?>
<h2 class="nav-tab-wrapper msv-tabs">
<?php echo esc_html( PLUGIN_NAME ); ?><br />
<a class="nav-tab nav-tab-active" href="#tab_status" id="tab_status">Status</a>
<?php foreach ( $option_defs as $group_name => $defs ) : ?>
<a class="nav-tab" href="#tab_<?php echo Util::get_slug( $group_name ); ?>" id="tab_<?php echo Util::get_slug( $group_name ); ?>"><?php echo esc_html( $group_name ); ?></a>
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
<table class="form-table"><tbody>
	<tr valign="top">
	<td><label>Bot startup time</label></td>
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

	<table class="form-table">
	<?php if ( $group_name == 'Commands' ) : ?>
		<thead><tr>
			<th></th><th>Command Name and Aliases</th><th>Enabled</th>
		</tr></thead>
	<?php elseif ( $group_name == 'Responses' ) : ?>
		<thead><tr>
			<th></th><th>Text</th><th>Reply to</th>
		</tr></thead>
	<?php endif; ?>
	<tbody>

	<?php
	foreach ( $defs as $option_name => $attr ) :

	if ( substr( $option_name, -7 ) == '_switch' ) { continue; }

	if ( $group_name == 'Commands' || $group_name == 'Responses' ) {
		$switch = $opt->__get( $option_name . '_switch' );
		$chk0 = ( $switch == '0' ) ? ' checked="checked"' : '';
		$chk1 = ( $switch == '1' ) ? ' checked="checked"' : '';
	}

	$is_textarea = $attr['c'] == 'msv-input-tall';
	$input_class = trim( 'regular-text ' . $attr['c'] );
	$value = $opt->__get($option_name);
	?>

	<tr valign="top">
	<td>
		<label for="<?php echo PLUGIN_SLUG . '_' . $option_name; ?>"><?php echo $attr['t']; ?></label>
	</td>
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
	<?php if ( $group_name == 'Commands' ) : ?>
		<td>
		<label>
		<input name="<?php echo PLUGIN_SLUG . '_' . $option_name; ?>_switch" id="<?php echo PLUGIN_SLUG . '_' . $option_name; ?>_enabled" value="1" type="checkbox"<?php echo $chk1; ?>/>
		Enabled</label>
		</td>
	<?php elseif ( $group_name == 'Responses' ) : ?>
		<td>
		<fieldset><legend class="screen-reader-text">Reply to</legend>
		<label>
		<input class="tog" type="radio" value="0" name="<?php echo PLUGIN_SLUG . '_' . $option_name; ?>_switch"<?php echo $chk0; ?>></input>
		Same context
		</label><br />
		<label>
		<input class="tog" type="radio" value="1" name="<?php echo PLUGIN_SLUG . '_' . $option_name; ?>_switch"<?php echo $chk1; ?>></input>
		Sender
		</label>
		</td>
	<?php endif; ?>
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
