<?php
/*
Plugin Name: WiredMinds
Description: Wiredminds for WordPress
Plugin URI: http://www.wiredminds.de/
Version: 1.0
Author: WiredMinds
Author URI: http://www.wiredminds.de/
*/

load_plugin_textdomain('wp_wm', $path = 'wp-content/plugins/wiredminds');

if (false === version_compare(phpversion(), '5', '>=')) {
	trigger_error('WiredMinds for WordPress requires PHP 5 or greater.', E_USER_ERROR);
}

/**
 * Add links
 */
function wp_wm_add_links() {
	if (function_exists('add_options_page')) {
		$settings = add_options_page('Wiredminds', 'Wiredminds', 'administrator', __FILE__, 'wp_wm_admin');
    }
}

/**
 * Create admin page
 */
function wp_wm_admin() {
	
	$default_wp_wm_cnt_server = "http://ctsde00.wiredminds.de";

	add_option('wp_wm_custnum', '');
	add_option('wp_wm_cnt_server', $default_wp_wm_cnt_server);
	if (!empty($_POST['action']) && ($_POST['action'] == 'save')) {
		update_option('wp_wm_custnum', trim($_POST['wp_wm_custnum']));
		update_option('wp_wm_cnt_server', trim($_POST['wp_wm_cnt_server']));
	}
	$wp_wm_custnum = stripslashes(get_option('wp_wm_custnum'));
	$wp_wm_cnt_server = stripslashes(get_option('wp_wm_cnt_server'));	
	?>
	
	<div class="wrap">
    <h2><?php _e("Pixel-Code Konfiguration"); ?></h2>
    <div class="postbox-container" style="width: 600px;">
        <div class="metabox-holder">
            <div class="meta-box-sortables">
                <form action="" method="post">
                    <div class="postbox">
						<h3 class="hndle"><span>Wiredminds Pixel-Code</span></h3>
						<div class="inside" style="padding:15px">

							<?php
							$error = 0;
							if (strlen(get_option('wp_wm_custnum'))<1) { $error++; }
							if (strlen(get_option('wp_wm_cnt_server'))<1) { $error++; }

							if($error > 0) { ?>
								<p>
									<span style="color:red; font-weight:bold">
										Bitte alle Felder ausfüllen.
									</span>
								</p>
                        	<?php } ?>

							<p>
								<label style="width:100px;text-align:right; float:left; display:block">Kundennummer (wm_custnum):</label>&nbsp;
								<input name="wp_wm_custnum" type="text" value="<?php echo $wp_wm_custnum; ?>" size="40"/>
							</p>
                            <p>
								<label style="width:100px;text-align:right; float:left; display:block">Zählserver Domain:</label>&nbsp;
								<input name="wp_wm_cnt_server" type="text" value="<?php echo $wp_wm_cnt_server; ?>" size="40"/>
							</p>
							 <p style="margin-left:110px; font-size: 10px">
								
                            </p>
						</div>
					</div>
                    <div class="submit" style="text-align:right">
                    <input type="hidden" name="action" value="save" />
                    <input type="submit" class="button-primary" name="submit" value="<?php _e("Speichern"); ?> &raquo;" />
                    </div>
                </form>
            </div>
        </div>
    </div>
	<?php
}

/**
 * Print pixelcode
 */
function wp_wm_pixel() {
	
	$wp_wm_custnum = stripslashes(get_option('wp_wm_custnum'));
	$wp_wm_cnt_server = stripslashes(get_option('wp_wm_cnt_server'));
	$wp_wm_cnt_server = trim($wp_wm_cnt_server, "/");
	
	?>
	<!-- WiredMinds eMetrics tracking with Enterprise Edition V5.9.2 START -->
	<script type="text/javascript" src="<?php echo $wp_wm_cnt_server ?>/track/count.js"></script>
	<script type="text/javascript"><!--
	wiredminds.push(["setTrackParam", "wm_custnum", "<?php echo $wp_wm_custnum ?>"]);
	// Begin own parameters.
	wiredminds.push(["setTrackParam", "wm_campaign_key", "utm_campaign"]);
	wiredminds.push(["setTrackParam", "wm_page_name", document.title]);
	// End own parameters.
	wiredminds.push(["count"]);
	// -->
	</script>

	<noscript>
	<div>
	<a href="http://www.wiredminds.de"><img
	 src="<?php echo $wp_wm_cnt_server ?>/track/ctin.php?wm_custnum=<?php echo $wp_wm_custnum ?>&amp;nojs=1&amp;wm_page_name=PAGE_NAME&amp;wm_group_name=GROUP_NAME"
	 alt="" style="border:0px;"/></a>
	</div>
	</noscript>
	<!-- WiredMinds eMetrics tracking with Enterprise Edition V5.9.2 END -->
	<?php
}

add_action('admin_menu', 'wp_wm_add_links');
add_action('wp_footer',  'wp_wm_pixel');

?>