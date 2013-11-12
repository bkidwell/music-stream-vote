<?php
/**
 * View for Settings screen
 *
 * @author  Brendan Kidwell <snarf@glump.net>
 * @license  GPL3
 * @package  music-stream-vote
 */

namespace GlumpNet\WordPress\MusicStreamVote;
?>

<style type="text/css">
.regular-text.msv-input-wide { width: 50em; }
.regular-text.msv-input-tall { width: 50em; height: 14em; }
</style>

<div class="wrap">

<?php screen_icon(); ?>
<?php echo "<h2>" . PLUGIN_NAME . "</h2>"; ?>

<?php if ( $opt_saved ) : ?>
<?php if ( $opt_restarted ) : ?>
<div class="updated"><p><strong>Settings saved. Bot restared (if running).</strong></p></div>
<?php else : ?>
<div class="updated"><p><strong>Settings saved.</strong></p></div>
<?php endif; ?>
<?php endif; ?>

<h3 class="title">Status</h3>

<table class="form-table"><tbody>
	<tr valign="top">
	<th scope="row"><label>Bot startup time</label></th>
	<td>
		<p><?php echo $out['start_time']; ?></p>
	</td>
	</tr>
</tbody></table>

<form name="<?php echo PLUGIN_SLUG; ?>_form" method="post">
	<input type="hidden" name="<?php echo PLUGIN_SLUG; ?>_o" value="1">

	<?php

	$option_defs = OptionDefs::$option_defs;
	// echo "<pre>"; print_r($option_defs); echo "</pre>";
	foreach ( $option_defs as $group_name => $defs ) : ?>

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

	?></tbody></table><?php
	endforeach;
	?>

	<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit" />
	</p>
</form>

</div>
