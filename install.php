<?php
	function likephoto_install() 
	{
   		global $wpdb;
   		$votes_table_name = $wpdb->prefix . "likephoto_votes";
   		
   		if($wpdb->get_var("SHOW TABLES LIKE '$votes_table_name'") != $votes_table_name) 
   		{
			$sql = "CREATE TABLE " . $votes_table_name . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  time bigint(11) DEFAULT '0' NOT NULL,
			  image VARCHAR(255) NOT NULL,
			  ipaddress VARCHAR(20) NOT NULL,
			  UNIQUE KEY id (id)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
   		}
   	}
   	register_activation_hook(__FILE__,'likephoto_install');
?>