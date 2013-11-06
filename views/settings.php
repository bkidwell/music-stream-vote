<?php
namespace GlumpNet\WordPress\MusicStreamVote;
?>

<style type="text/css">
.regular-text.msv-input-wide { width: 50em; }
.regular-text.msv-input-tall { width: 50em; height: 14em; }
</style>

<div class="wrap">

<div id="icon-options-general" class="icon32"><br></br></div>
<?php echo "<h2>" . PLUGIN_NAME . "</h2>"; ?>

<p><em>To change a setting back to its default, clear it out and save.</em></p>

<?php if ( $opt_saved ): ?>
<div class="updated"><p><strong>Settings saved.</strong></p></div>
<?php endif; ?>

<h3 class="title">Status</h3>

<table class="form-table"><tbody>
	<tr valign="top">
	<th scope="row"><label>Bot startup time</label></th>
	<td>
		<p><?php echo $out['start_time']; ?></p>
	</td>
	</tr>
	<th scope="row"><label>Database tables</label></th>
	<td>
		<p class="description">Voting tables not created.</p>
		<p>
		<a class="button">Create / Reset Tables</a>
		<a class="button">Delete Tables</a>
		</p>
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
			<textarea name="<?php echo PLUGIN_SLUG . '_' . $key; ?>" class="<?php echo $input_class; ?>"
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
