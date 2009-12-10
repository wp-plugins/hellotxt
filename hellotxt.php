<?php
/*
Plugin Name: HelloTXT
Plugin URI: http://smheart.org/hellotxt/
Description: The plugin sends a message to the HelloTXT social notification network when a post is published in WordPress.
Author: Matthew Phillips
Version: 1.0.1
Author URI: http://smheart.org


Copyright 2009 SMHeart Inc, Matthew Phillips  (email : matthew@smheart.org)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

http://www.gnu.org/licenses/gpl.txt

Version
	1.0.1 10 December 2009
        1.0 - 1 December 2009


*/

add_action('admin_menu', 'hellotxt_menu');
add_action('admin_head', 'hellotxt_styles');
register_activation_hook(__FILE__, 'hellotxt_activation');
register_activation_hook(__FILE__, 'hellotxt_install');
add_action('transition_post_status', 'hellotxt_notification', 1, 3);

function hellotxt_install() {
	global $wpdb;
	if (!is_blog_installed()) return;
	add_option('htxt_userkey', 'Add your User Key Here', '', 'no');
	add_option('htxt_group', 'inhome', '', 'no');
	add_option('htxt_ellipse', '...', '', 'no');
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	}

function hellotxt_menu() {
	add_options_page('HelloTXT Options', 'HelloTXT', 8, __FILE__, 'hellotxt_options');
	}

function hellotxt_styles() {
	?>
 	<link rel="stylesheet" href="/wp-content/plugins/hellotxt/hellotxt.css" type="text/css" media="screen" charset="utf-8"/>
	<?php
	}

function hellotxt_options() {
	?>
	<div class="wrap">
		<h2>HelloTXT V1.0</h2>
		<div id="htxt_main">
			<div id="htxt_left_wrap">
				<div id="htxt_left_inside">
					<h3>Donate</h3>
					<p><em>If you like this plugin and find it useful, help keep this plugin free and actively developed by clicking the <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10178844" target="paypal"><strong>donate</strong></a> button or send me a gift from my <a href="http://amzn.com/w/11GK2Q9X1JXGY" target="amazon"><strong>Amazon wishlist</strong></a>.  Also follow me on <a href="http://twitter.com/kestrachern/" target="twitter"><strong>Twitter</strong></a>.</em></p>
					<a target="paypal" title="Paypal Donate"href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10178844"><img src="/wp-content/plugins/hellotxt/paypal.jpg" alt="Donate with PayPal" /></a>
					<a target="amazon" title="Amazon Wish List" href="http://amzn.com/w/11GK2Q9X1JXGY"><img src="/wp-content/plugins/hellotxt/amazon.jpg" alt="My Amazon wishlist" /> </a>
					<a target="Twitter" title="Follow me on Twitter" href="http://twitter.com/kestrachern/"><img src="/wp-content/plugins/hellotxt/twitter.jpg" alt="Twitter" /></a>	
				</div>
			</div>
			<div id="htxt_right_wrap">
				<div id="htxt_right_inside">
				<h3>About the Plugin</h3>
				<p> The plugin sends a message to the HelloTXT social notification network when a post is published in WordPress.</p>
				</div>
			</div>
		</div>
	<div style="clear:both;"></div>
	<fieldset class="options"><legend>HelloTXT Options</legend> 
	<form method="post" action="options.php">
		<?php echo wp_nonce_field('update-options'); ?>
		<table class="form-table">
			<tr valign="top">
				<?php
					echo '<td>HelloTxt - <a href="http://hellotxt.com/settings/api/wordpress-plug-in">User Key</a><br/>';
					echo '<input type="text" name="htxt_userkey" value="'.get_option('htxt_userkey').'" /></td>';
					echo '<td>Ellipse characters<br/><input type="text" name="htxt_ellipse" value="'.get_option('htxt_ellipse').'" /></td>';
					echo '</tr><tr valign="top">';
					echo '<td>Group<br/>';
					echo '<select name="htxt_group">';
					echo '<option value="inhome">Inhome</option>';
					echo '<option value="friend">Friend</option>';
					echo '<option value="collegue">Collegue</option>';
					echo '</select>';
					echo '</td>';
				?>
			</tr>
		</table>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="htxt_userkey,htxt_group,htxt_ellipse" />
		<p class="submit">
			<input type="submit" name="Submit" value="Set Options" />
		</p>
	</form>
	</fieldset>	
	<div style="clear:both;"></div>			
	<fieldset class="options"><legend>Feature Suggestion/Bug Report</legend> 
	<?php if ($_SERVER['REQUEST_METHOD'] != 'POST'){
      		$me = $_SERVER['PHP_SELF'].'?page=hellotxt/hellotxt.php';
		?>
		<form name="form1" method="post" action="<?php echo $me;?>">
		<table border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td>
				Make a:
			</td>
			<td>
				<select name="MessageType">
				<option value="Feature Suggestion">Feature Suggestion</option>
				<option value="Bug Report">Bug Report</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Name:
			</td>
			<td>
				<input type="text" name="Name">
			</td>
		</tr>
		<tr>
			<td>
				Your email:
			</td>
			<td>
				<input type="text" name="Email" value="<?php echo(get_option('admin_email')) ?>" />
			</td>
		</tr>
		<tr>
			<td valign="top">
				Message:
			</td>
			<td>
				<textarea name="MsgBody">
				</textarea>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<input type="submit" name="Submit" value="Send">
			</td>
		</tr>
	</table>
</form>
<?php
   } else {
      error_reporting(0);
	$recipient = 'support@smheart.org';
	$subject = stripslashes($_POST['MessageType']).'- helloTXT Plugin';
	$name = stripslashes($_POST['Name']);
	$email = stripslashes($_POST['Email']);
	if ($from == "") {
		$from = get_option('admin_email');
	}
	$header = "From: ".$name." <".$from.">\r\n."."Reply-To: ".$from." \r\n"."X-Mailer: PHP/" . phpversion();
	$msg = stripslashes($_POST['MsgBody']);
      if (mail($recipient, $subject, $msg, $header))
         echo nl2br("<h2>Message Sent:</h2>
         <strong>To:</strong> helloTXT Plugin Suport
         <strong>Subject:</strong> $subject
         <strong>Message:</strong> $msg");
      else
         echo "<h2>Message failed to send</h2>";
}
?>
	</fieldset>			
	</div>
	<?php
}

function hellotxt_notification($new_status, $old_status, $post) {
	global $wpdb;
	if ($old_status == 'publish' || $new_status != 'publish' || $post->post_type != 'post') return;
	$app_key = '1GNntqlC6X7UtVwj';
	$user_key = get_option('htxt_userkey');
	$group = get_option('htxt_group');
	$ellipse = get_option('htxt_ellipse');
	$link = get_permalink($post->ID);
	$subject = $post->post_title;
	$body = $post->post_content;
	$length = 140-strlen($ellipse);
	$content = substr($subject.": *".$link." - ".$body,0,$length).$ellipse;
	$ch = curl_init();    // initialize curl handle
	curl_setopt($ch, CURLOPT_URL,"http://hellotxt.com/api/v1/method/user.post"); // set posting URL
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
	curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
	curl_setopt($ch, CURLOPT_POST, 1); // set POST method
	curl_setopt($ch, CURLOPT_POSTFIELDS, 
		"app_key=".$app_key.
		"&user_key=".$user_key.
		"&body=".$content.
		"&group=".$group.
		"&"); // add POST fields
	$result = curl_exec($ch); // run the whole process
	curl_close($ch);  
}

?>