<?php  
    /* 
    Plugin Name: Like Photo 
    Plugin URI: http://www.minioak.com/portfolio/like-photo 
    Description: Plugin for adding a "Like" button to individual images. 
    Author: John Mitchell
    Version: 1.0 
    Author URI: http://www.minioak.com
    */ 
    
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
   
   	function likephoto_queryvars( $qvars )
	{
		$qvars[] = 'imgid';
		return $qvars;
	}
	add_filter('query_vars', 'likephoto_queryvars' );

	function likephoto_js_header() 
	{
?>
<script type="text/javascript">
//<![CDATA[
siteurl = "<?php echo get_site_url(); ?>";
//]]>
</script>
<?php
	}
	add_action('wp_head', 'likephoto_js_header' );

	function get_image_votes($matches)
	{
		global $wpdb; 
		global $post;
   		$votes_table_name = $wpdb->prefix . "likephoto_votes";
   		
   		$vote_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $votes_table_name WHERE image=%s", $matches[2]));
   		$ip_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $votes_table_name WHERE image=%s and ipaddress=%s", $matches[2], $_SERVER["REMOTE_ADDR"]));
		
		$vote_link = "";
		
		$vote_on_this_image = __("vote on this image");
		
		if ($ip_count == 0)
		{
			$vote_link = '<a href="'.$matches[2].'" rel="like_photo-'.$post->ID.'" title="'.$vote_on_this_image.'">'.$vote_on_this_image.'</a>';
		}	
		
		$votes_text = __("Votes: ");
		
		return '<div class="like-photo-wrapper">'.$matches[0].'<div class="votes"><span class="currentVotes">'.$votes_text.' '.$vote_count.'</span>'.$vote_link.'</div></div>';
	}
	
	function add_like_link ($content) {
		global $post;
		
		$pattern        = '#(<a.[^>]*?>)?<img[^>]*src="([^"]*)"[^>/]*?/>(?(1)\s*</a>)#isU';
		
		$content = preg_replace_callback($pattern, "get_image_votes", $content);
		
		return $content;
		
	}
	add_filter('the_content', 'add_like_link', 99 );
	
    function add_like_photo_stylesheet() 
    {
        $myStyleUrl = WP_PLUGIN_URL . '/like-photo/style.css';
        $myStyleFile = WP_PLUGIN_DIR . '/like-photo/style.css';
        if ( file_exists($myStyleFile) ) 
        {
            wp_register_style('like-photo-style', $myStyleUrl);
            wp_enqueue_style( 'like-photo-style');
        }
    }
	add_action('wp_print_styles', 'add_like_photo_stylesheet');
	
	function likephoto_vote() 
	{
		global $wpdb;
   		$votes_table_name = $wpdb->prefix . "likephoto_votes";
		
		$ip_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $votes_table_name WHERE image=%s and ipaddress=%s", $_POST["imgid"], $_SERVER["REMOTE_ADDR"]));
		
		if ($ip_count == 0) 
		{
			$wpdb->query( $wpdb->prepare("insert into $votes_table_name set image=%s, ipaddress=%s", $_POST["imgid"], $_SERVER["REMOTE_ADDR"]));
				
		}
		$vote_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $votes_table_name WHERE image=%s", $_POST["imgid"]));
		
		echo $vote_count;
		die();
	}
	add_action('wp_ajax_nopriv_likephoto_vote', 'likephoto_vote');
	add_action('wp_ajax_likephoto_vote', 'likephoto_vote');
	
	if (!is_admin())
	{
		wp_enqueue_script("like-photo", WP_PLUGIN_URL."/like-photo/like-photo.min.js", array('jquery'));
	}
?>