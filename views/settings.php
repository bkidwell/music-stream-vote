<?php
namespace GlumpNet\WordPress\MusicStreamVote;
?>

<style type="text/css">
.regular-text.msv-input-wide { width: 50em; }
</style>

<div class="wrap">

<div id="icon-options-general" class="icon32"><br></br></div>
<?php echo "<h2>" . PLUGIN_NAME . "</h2>"; ?>

<?php if ( $opt_saved ): ?>
<div class="updated"><p><strong>Settings saved.</strong></p></div>
<?php endif; ?>

<h3 class="title">Status</h3>

<table class="form-table"><tbody>
	<tr valign="top">
	<th scope="row"><label for="blogname">Database tables</label></th>
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
	$group_count = 0;
	$prev_group = '';
	for ( $i = 0; $i < count(Options::$option_descriptions); $i += 5 ):

	$group = Options::$option_descriptions[$i];
	$desc = Options::$option_descriptions[$i + 1];
	$key = Options::$option_descriptions[$i + 2];
	$hint = Options::$option_descriptions[$i + 3];
	$input_class = trim( 'regular-text ' . Options::$option_descriptions[$i + 4] );
	$value = $opt->__get($key);
	if ( $prev_group != $group ) {
		if ( $group_count != 0 ):
			?></tbody></table><?php
		endif;
		?>
		<h3 class="title"><?php echo $group; ?></h3>
		<table class="form-table"><tbody>
		<?php
	}
	?>

	<tr valign="top">
	<th scope="row"><label for="<?php echo PLUGIN_SLUG . '_' . $key; ?>"><?php echo $desc; ?></label></th>
	<td>
		<input name="<?php echo PLUGIN_SLUG . '_' . $key; ?>"
		value="<?php echo esc_attr( $value ); ?>" class="<?php echo $input_class; ?>" type="text">
		<p class="description"><?php echo esc_html( $hint ); ?></p>
	</td>
	</tr>
	<?php
	$group_count++;
	$prev_group = $group;

	endfor;
	?>

	</tbody></table>

	<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit" />
	</p>
</form>

</div>
