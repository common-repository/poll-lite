<?php 
/*
 Plugin Name: Poll Lite
 Plugin URI: http://wordpress.phpanswer.com/poll/
 Description: With this plugin you can add one or more polls to your site. This can be done by pasting the shortcode of a poll in pages, posts or sidebar. Settings manageable from your admin page. For more features get the <a href="http://wordpress.phpanswer.com/poll/">Poll FULL</a> version.
 Version: 1.0.1
 Author: Cristian Merli
 Author URI: http://wordpress.phpanswer.com
 */

/*
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; version 2 of the License.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

include 'define.php';
include 'poll.class.php';
include 'poll-helper.class.php';


add_action('admin_menu', array('Merlic_Poll', 'admin_menu'));
add_action('init', array('Merlic_Poll', 'init'));
add_action('add_meta_boxes', array('Merlic_Poll', 'add_meta_boxes'));

add_action('wp_insert_post', array('Merlic_Poll', 'wp_insert_post'));
add_action('delete_post', array('Merlic_Poll', 'delete_post'));

add_shortcode('merlic_poll', array('Merlic_Poll', 'shortcode'));
add_filter('widget_text', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');

register_activation_hook(__FILE__, 'merlic_poll_activate');


function merlic_poll_activate() {
	global $wpdb;
	global $merlic_poll_version;
	global $poll_table_name;
	global $pollresult_table_name;
	
	if ($wpdb->get_var("show tables like '$poll_table_name'") != $poll_table_name) {
	
		$query = "
			CREATE TABLE IF NOT EXISTS `$poll_table_name` (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `question` varchar(255) NOT NULL DEFAULT '',
			  `a1` varchar(128) NOT NULL DEFAULT '0',
			  `a2` varchar(128) NOT NULL DEFAULT '0',
			  `a3` varchar(128) NOT NULL DEFAULT '0',
			  `a4` varchar(128) NOT NULL DEFAULT '0',
			  `a5` varchar(128) NOT NULL DEFAULT '0',
			  `a6` varchar(128) NOT NULL DEFAULT '0',
			  `a7` varchar(128) NOT NULL DEFAULT '0',
			  `a8` varchar(128) NOT NULL DEFAULT '0',
			  `a9` varchar(128) NOT NULL DEFAULT '0',
			  `a10` varchar(128) NOT NULL DEFAULT '0',
			  `date` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=MyISAM;
		";
			
		require_once (ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($query);
		
		$wpdb->query($query);
		add_option("merlic_poll_version", $merlic_poll_version);
	}
	else {
		/*
		$installed_ver = get_option("merlic_poll_version");
		
		if ($installed_ver != $merlic_poll_version) {
		
			$query = "
				CREATE TABLE IF NOT EXISTS `$poll_table_name` (
				  `ID` int(11) NOT NULL AUTO_INCREMENT,
				  `question` varchar(255) NOT NULL DEFAULT '',
				  `a1` varchar(128) NOT NULL DEFAULT '0',
				  `a2` varchar(128) NOT NULL DEFAULT '0',
				  `a3` varchar(128) NOT NULL DEFAULT '0',
				  `a4` varchar(128) NOT NULL DEFAULT '0',
				  `a5` varchar(128) NOT NULL DEFAULT '0',
				  `a6` varchar(128) NOT NULL DEFAULT '0',
				  `a7` varchar(128) NOT NULL DEFAULT '0',
				  `a8` varchar(128) NOT NULL DEFAULT '0',
				  `a9` varchar(128) NOT NULL DEFAULT '0',
				  `a10` varchar(128) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`ID`)
				) ENGINE=MyISAM;
			";
			
			require_once (ABSPATH.'wp-admin/includes/upgrade.php');
			dbDelta($query);
			
			update_option("merlic_poll_version", $merlic_poll_version);
		}
		*/
	}
	
	if ($wpdb->get_var("show tables like '$pollresult_table_name'") != $pollresult_table_name) {
	
		$query = "
			CREATE TABLE IF NOT EXISTS `$pollresult_table_name` (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `pollID` int(11) NOT NULL DEFAULT '0',
			  `answer` int(11) NOT NULL DEFAULT '0',
			  `session` varchar(128) DEFAULT NULL,
			  `ip` varchar(20) NOT NULL DEFAULT '',
			  `time` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=MyISAM
		";
		/*
		$query = "
			CREATE TABLE IF NOT EXISTS `$pollresult_table_name` (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `pollID` int(11) NOT NULL DEFAULT '0',
			  `v1` int(11) NOT NULL DEFAULT '0',
			  `v2` int(11) NOT NULL DEFAULT '0',
			  `v3` int(11) NOT NULL DEFAULT '0',
			  `v4` int(11) NOT NULL DEFAULT '0',
			  `v5` int(11) NOT NULL DEFAULT '0',
			  `v6` int(11) NOT NULL DEFAULT '0',
			  `v7` int(11) NOT NULL DEFAULT '0',
			  `v8` int(11) NOT NULL DEFAULT '0',
			  `v9` int(11) NOT NULL DEFAULT '0',
			  `v10` int(11) NOT NULL DEFAULT '0',
			  `session` varchar(128) DEFAULT NULL,
			  `ip` varchar(20) NOT NULL DEFAULT '',
			  `time` datetime NOT NULL,
			  PRIMARY KEY (`ID`)
			) ENGINE=MyISAM
		";
		*/
				
		$wpdb->query($query);
	}
	
	//$global_helper = new Merlic_Poll_Helper();
	//$global_helper->notification();
}

?>
