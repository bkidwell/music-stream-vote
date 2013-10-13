<?php
namespace GlumpNet\WordPress\MusicStreamVote;
?>

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

	<h3 class="title">Settings</h3>

	<table class="form-table"><tbody>

	<tr valign="top">
	<th scope="row"><label for="<?php echo PLUGIN_SLUG; ?>_password">System Password</label></th>
	<td>
		<input name="<?php echo PLUGIN_SLUG; ?>_password"
		value="<?php echo esc_attr( $opt->password ); ?>" class="regular-text" type="text">
		<p class="description">Used by bot to login to vote web service.</p>
	</td>
	</tr>

	</tbody></table>

	<p class="submit">
	<input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit" />
	</p>
</form>

</div>
