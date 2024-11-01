<?php
/*
Plugin Name: WP Google Alerts
Plugin URI: http://www.imod.co.za/wordpress-plugins/
Description: Display your Google Alerts in your Wordpress Dashboard. Once activated, <a href="options-general.php?page=gAlerts.php">click here</a> to setup your alert.
Version: 1.0
Author: Christopher Mills
Author URI: http://www.imod.co.za/
License: GPL2
*/

/* Fetch and format feed */
function fetchATOM($uri, $items = 5, $print = true)
{
  $content = file_get_contents( $uri );
	$atom    = new SimpleXmlElement($content);
	$feed    = array();
	
	for( $i = 0; $i < $items; $i++ ) :
    $entry = $atom -> entry[$i];
    if( $print ) :
      printf( "<li style='list-style:none; border-bottom:1px solid #CCCCCC;'><p><span class='entry-title'>Title: </span>%s</p><p><span class='entry-title'>Published:</span>%s</p><p><span class='entry-title'>Link: </span><a href=%s target=_blank>Click here</a></p><p><span class='entry-title'>Content: </span>%s</p><p><span class='entry-title'>Author: </span>%s</p></li>", $entry -> title, date( "d M Y H:i:s", strtotime( $entry -> published ) ), $entry -> link['href'], substr(strip_tags( $entry -> content ),0,160)."..", $entry -> author -> name ); 
    else :
      $feed[] = $entry;
    endif;
	endfor;
	
	return $feed;
}


/* Write to Dashboard */
if( !class_exists( 'gAlerts_DashboardWidget') ) {
	class gAlerts_DashboardWidget {
		function gAlerts_dashboard_widget() {			
			include_once( ABSPATH . WPINC . "/feed.php" );
			$rss = get_option('rss_link');
			if( !is_wp_error($rss) ):
			  
			endif;			
			$feed = fetchAtom( $rss, 2, true );
			printf("<br><font size=1>WP Google Alerts by <a href=http://www.imod.co.za target=_blank>iMod</a></font>");
			if( !empty( $feed ) )
		  		var_dump( $feed );
		}

		function gAlerts_add_dashboard_widget() {
			wp_add_dashboard_widget( 'gAlerts-custom-widget', 'WP Google Alerts', array( 'gAlerts_DashboardWidget', 'gAlerts_dashboard_widget' ) );
		}		
	}
	add_action( 'wp_dashboard_setup', array( 'gAlerts_DashboardWidget', 'gAlerts_add_dashboard_widget' ) );
}


/* Set the options */
function gAlerts_update_options(){
    $ok = false;
    if ($_REQUEST['rss_link'])
    {
        update_option('rss_link',$_REQUEST['rss_link']);
        $ok = true;
    }

    if ($ok) {
        ?>
        <div id="message" class="updated fade">
        <p>Options Saved! <a href="index.php">Click here</a> to see your Google Alerts in action</p>
        </div>
        <?php
    }
    else
    {
        ?>
        <div id="message" class="error fade">
        <p>Failed to save Options</p>
        </div>
        <?php
    }
}

/* Form for RSS feed entry */
function gAlerts_show_form(){
    $default_rss = get_option('rss_link');
    ?>    
        <table class="optiontable">
        <form method="post">
          <tr valign="top">
            <td><p>Google Alerts are email updates of the latest relevant Google results (web, news, etc.) based on your choice of query or topic.</p> 
              <p>For example, if you wanted to track your website name, you would setup an alert for it and each time it's mentioned on the Internet, Google would send you an email. What Wordpress Google Alerts does, is instead of having to worry about emails, it simply displays your alerts on your Wordpress Dashboard.</p>
              <p>Getting started is easy, follow these steps:</p>
              <ol>
                <li>Click through to <a href="http://www.google.com/alerts" target="_blank">Google Alerts</a></li>
                <li>Follow the instructions and setup an alert</li>
                <li>Make sure you set &quot;Deliver to&quot; to &quot;Feed&quot;</li>
                <li>Save your settings</li>
                <li>You will see a feed button next to your alert, copy and then paste the feed link into the box below</li>
              </ol>              </td>
          </tr>
          <tr valign="top">
            <td>&nbsp;</td>
          </tr>
          <tr valign="top">
        
        <td><input type="text" name="rss_link" size="40" value="<?php echo $default_rss; ?>"></td>
        </tr>
        <tr valign="top">
        
        <td><input type="submit" name="submit" value="Submit"></td>
        </tr>
        </form>
    </table>
    <?php
}

/* Setup Admin Options */
function gAlerts_admin_options(){
    ?>
    <div class="wrap"><h2>Wordpress Google Alerts Setup</h2>
    <?php
    if ($_REQUEST['submit'])
    {
        gAlerts_update_options();
    }
    gAlerts_show_form();
    ?>
    </div>
    <?php
}

/* Create setting under settings */
function gAlerts_menu() {
    add_options_page('Google Alerts', 'WP Google Alerts', 'manage_options', __FILE__, 'gAlerts_admin_options' );
}

/* Make admin menu */
add_action('admin_menu','gAlerts_menu');

?>