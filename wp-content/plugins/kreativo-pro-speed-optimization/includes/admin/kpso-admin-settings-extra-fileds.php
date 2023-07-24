<?php

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/*function kpso_format_list($list)
{
    $list = trim($list);
    $list = $list ? array_map('trim', explode("\n", str_replace("\r", "", sanitize_textarea_field($list)))) : [];
    return $list;
}*/

function kpso_extra_settings_view()
{
	$kp_active_tab = isset($_GET['tab']) ? $_GET['tab'] : "main";
?>

<h2 class="nav-tab-wrapper">
    <a href="?page=kpso&tab=main" class="nav-tab <?php echo $kp_active_tab == 'main' ? 'nav-tab-active' : ''; ?>">Main Settings</a>
    <a href="?page=kpso&tab=extra" class="nav-tab <?php echo $kp_active_tab == 'extra' ? 'nav-tab-active' : ''; ?>">Extra Settings</a>
</h2>

<?php

    if (isset($_POST['kpso_extra_submit']))
	{
        update_option('kpso_white_label', $_POST['kpso_white_label']);
		update_option('kpso_cartflows', $_POST['kpso_cartflows']);
		update_option('kpso_video_include_list', kpso_format_list($_POST['kpso_video_include_list']));
		update_option('kpso_video_mobile_disabled', $_POST['kpso_video_mobile_disabled']);
		
		if ( is_plugin_active('wp-rocket/wp-rocket.php') )
		{
			rocket_clean_minify();
			rocket_clean_domain();
		}
		
		if( is_plugin_active('autoptimize/autoptimize.php') )
		{
			autoptimizeCache::clearall();
		}
    }
	
	$kpso_white_label = get_option('kpso_white_label');
	$kpso_cartflows = get_option('kpso_cartflows');
	$kpso_video_mobile_disabled = get_option('kpso_video_mobile_disabled');
	
	$kpso_video_include_list = get_option('kpso_video_include_list');
	if($kpso_video_include_list){
		$kpso_video_include_list = implode("\n", $kpso_video_include_list);
		$kpso_video_include_list = esc_textarea($kpso_video_include_list);
	} else
	{
		$kpso_video_include_list = "";
	}

    ?>
	<form method="POST">
		<?php wp_nonce_field('kpso', 'kpso-settings-form'); ?>
		<table class="form-table" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><label>White-Label</label></th>
				<td>
					<input type="hidden" name="kpso_white_label" value="no">
					<input type="checkbox" id="kpso_white_label" name="kpso_white_label" <?php  if($kpso_white_label == 'yes') { echo 'checked'; } ?> value="<?php if($kpso_white_label == 'yes') { echo 'yes'; } else { echo 'no'; } ?>"><label for="kpso_white_label">Hide Plugin from Plugins List and Settings.</label>
					<br>
				</td>
			</tr>
			<tr>
				<th scope="row"><label>CartFlows Support</label></th>
				<td>
					<input type="hidden" name="kpso_cartflows" value="no">
					<input type="checkbox" id="kpso_cartflows" name="kpso_cartflows" <?php  if($kpso_cartflows == 'yes') { echo 'checked'; } ?> value="<?php if($kpso_cartflows == 'yes') { echo 'yes'; } else { echo 'no'; } ?>"><label for="kpso_cartflows">Force Caching on CartFlows Pages.</label>
					<br>
				</td>
			</tr>
			<tr>
				<th scope="row"><label>Video Keywords</label></th>
				<td>
					<textarea name="kpso_video_include_list" rows="2" cols="50"><?php echo $kpso_video_include_list ?></textarea><br>
					<small class="description kp-code-desc">Keywords to identify videos for user interaction.</small><br><br>
					<small>
					<input type="hidden" name="kpso_video_mobile_disabled" value="no">
					<input type="checkbox" id="kpso_video_mobile_disabled" name="kpso_video_mobile_disabled" <?php if($kpso_video_mobile_disabled == "yes") { echo "checked"; } ?> value="<?php if($kpso_video_mobile_disabled == "yes") { echo "yes"; } else { echo "no"; } ?>"><label for="kpso_video_mobile_disabled">Disable Video Delay in Mobile</label>
					</small><br>
				</td>
			</tr>
		</tbody>
		</table>
		<p class="submit">
			<input type="submit" name="kpso_extra_submit" id="kpso_extra_submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
	<?php
}