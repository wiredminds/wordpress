<?php
/*
Plugin Name: wiredminds leadlab
Description: wiredminds leadlab tracking pixel integration for WordPress
Plugin URI: https://github.com/wiredminds-gmbh/wordpress
Version: 1.2
Author: wiredminds GmbH
Author URI: http://www.wiredminds.de
*/

load_plugin_textdomain('wp_wm', $path = 'wp-content/plugins/wiredminds');

if (false === version_compare(phpversion(), '5', '>=')) {
    trigger_error('WiredMinds for WordPress requires PHP 5 or greater.', E_USER_ERROR);
}

/**
 * Add menu link
 */
function wp_wm_add_links()
{
    if (function_exists('add_options_page')) {
        add_options_page('wiredminds leadlab', 'wiredminds leadlab', 'administrator', __FILE__, 'wp_wm_admin');
    }
}

/**
 * Create admin page
 */
function wp_wm_admin()
{
    $default_wp_wm_cnt_server = 'testapp.wiredminds.de';

    add_option('wp_wm_custnum', '');
    add_option('wp_wm_milestones', array());
    add_option('wp_wm_cnt_server', $default_wp_wm_cnt_server);
    if (!empty($_POST['action'])) {
        if ($_POST['action'] == 'save') {
            update_option('wp_wm_custnum', trim($_POST['wp_wm_custnum']));
            update_option('wp_wm_cnt_server', trim($_POST['wp_wm_cnt_server']));
        }
        if ($_POST['action'] == 'milestone') {
            $milestone = get_option('wp_wm_milestones');
            if (isset($_POST['delete'])) {
                unset($milestone[$_POST['delete']]);
            }
            if (isset($_POST['key'], $_POST['value'])) {
                $milestone[$_POST['key']] = $_POST['value'];
            }
            update_option('wp_wm_milestones', $milestone);
        }
    }
    $wp_wm_custnum = stripslashes(get_option('wp_wm_custnum'));
    $wp_wm_cnt_server = stripslashes(get_option('wp_wm_cnt_server'));
    ?>

    <div class="wrap">
        <h2><?php
            _e('wiredminds leadlab Trackingpixel Konfiguration');
            ?></h2>
        <div class="postbox-container" style="width: 600px;">
            <div class="metabox-holder">
                <div class="meta-box-sortables">
                    <form action="" method="post">
                        <div class="postbox">
                            <h3 class="hndle"><span>Trackingdaten</span></h3>
                            <div class="inside"><?php
                                $error = 0;
                                if (strlen(get_option('wp_wm_custnum')) < 1) {
                                    $error++;
                                }
                                if (strlen(get_option('wp_wm_cnt_server')) < 1) {
                                    $error++;
                                }

                                if ($error > 0) { ?>
                                    <p>
									<span style="color:red; font-weight:bold">
										Bitte alle Felder ausfüllen.
									</span>
                                    </p>
                                <?php } ?>
                                <p>
                                    <label
                                        style="width:200px;text-align:right; float:left; display:block; line-height: 30px;"
                                        for="wp_wm_custnum">Kundennummer (wm_custnum):</label>&nbsp;
                                    <input name="wp_wm_custnum" id="wp_wm_custnum" type="text" value="<?php
                                    echo $wp_wm_custnum;
                                    ?>" size="40"/>
                                </p>
                                <p>
                                    <label
                                        style="width:200px;text-align:right; float:left; display:block; line-height: 30px;"
                                        for="wp_wm_cnt_server">Zählserver Domain:</label>&nbsp;
                                    https://<input name="wp_wm_cnt_server" id="wp_wm_cnt_server" type="text"
                                                   value="<?php
                                                   echo $wp_wm_cnt_server;
                                                   ?>" size="34"/>
                                </p>
                            </div>
                        </div>
                        <div style="text-align:right">
                            <input type="hidden" name="action" value="save"/>
                            <input type="submit" class="button-primary" name="submit" value="<?php
                            _e('Speichern');
                            ?> &raquo;"/>
                        </div>
                    </form>
                    <hr/>
                    <div class="postbox">
                        <h3 class="hndle"><span>Milestones</span></h3>
                        <div class="inside">
                            <form action="" method="post">
                                <div style="display: table;">
                                    <div style="display: table-row">
                                        <div style="display: table-cell; padding: 5px">
                                            <input type="text" name="key" value="Name"/>
                                        </div>
                                        <div style="display: table-cell; padding: 5px">
                                            <input type="text" name="value" value="Regex"/>
                                        </div>
                                        <div style="display: table-cell; padding: 5px">
                                            <input type="hidden" name="action" value="milestone">
                                            <input type="submit" class="button-primary" value="add"/>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    <div style="display: table;">
                        <?php
                        $milestones = get_option('wp_wm_milestones');
                        foreach ($milestones as $key => $value):
                            ?>
                            <div style="display: table-row;">
                                <div style="display: table-cell; padding: 5px">
                                    <?php echo $key; ?>
                                </div>
                                <div style="display: table-cell;padding: 5px">
                                    <?php echo $value; ?>
                                </div>
                                <div style="display: table-cell;padding: 5px">
                                    <form action="" method="post">
                                        <input type="hidden" name="delete" value="<?php echo $key; ?>"/>
                                        <input type="hidden" name="action" value="milestone"/>
                                        <input type="submit" class="button-primary" value="delete"/>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Output pixelcode
 */
function wp_wm_pixel()
{
    $wp_wm_custnum = trim(stripslashes(get_option('wp_wm_custnum')));
    $wp_wm_cnt_server = trim(stripslashes(get_option('wp_wm_cnt_server')), '/');
    if (!empty($wp_wm_custnum) && !empty($wp_wm_cnt_server)) {
        ?>
        <!-- wiredminds leadlab tracking V6.4 START -->
        <script type="text/javascript">
            <!--
            var wiredminds = [];
            wiredminds.push(["setTrackParam", "wm_custnum", "<?php
                echo $wp_wm_custnum;
                ?>"]);
            // Begin own parameters.
            wiredminds.push(["setTrackParam", "wm_campaign_key", "utm_campaign"]);
            wiredminds.push(["registerHeatmapEvent", "mousedown"]);
            wiredminds.push(["setTrackParam", "wm_page_name", <?php
                echo json_encode(trim(wp_title('', false)));
                ?>]);
            var wmDynamicConf = [];
            <?php
            $milestones = get_option('wp_wm_milestones');
            foreach($milestones as $key => $value):
            ?>
            wmDynamicConf.push(["wm_page_url",<?php echo "\"$value\""; ?>, ["setTrackParam", "wm_milestone", <?php echo "\"$key\""; ?>]]);
            <?php endforeach; ?>
            // End own parameters.
            wiredminds.push(["setDynamicParams", wmDynamicConf]);
            wiredminds.push(["count"]);

            (function () {
                function wm_async_load() {
                    var wm = document.createElement("script");
                    wm.type = "text/javascript";
                    wm.async = true;
                    wm.src = "//<?php
                        echo $wp_wm_cnt_server;
                        ?>/track/count.js";
                    var el = document.getElementsByTagName('script')[0];
                    el.parentNode.insertBefore(wm, el);
                }

                if (window.addEventListener) {
                    window.addEventListener('load', wm_async_load, false);
                } else if (window.attachEvent) {
                    window.attachEvent('onload', wm_async_load);
                }
            })();
            // -->
        </script>

        <noscript>
            <div>
                <a href="http://www.wiredminds.de"><img
                        src="//<?php
                        echo $wp_wm_cnt_server;
                        ?>/track/ctin.php?wm_custnum=<?php
                        echo $wp_wm_custnum;
                        ?>&amp;nojs=1"
                        alt="" style="border:0px;"/></a>
            </div>
        </noscript>
        <!-- wiredminds leadlab tracking V6.4 END -->
        <?php
    }
}

add_action('admin_menu', 'wp_wm_add_links');
add_action('wp_footer', 'wp_wm_pixel');
