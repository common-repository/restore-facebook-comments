<?php
/*
Plugin Name: Restore FB Comments
Plugin URI: http://media-enzo.nl
Description: This plugin imports al the Facebook Comments on posts into the native WordPress comments system
Version: 0.9
Author: Media-Enzo
Author URI: http://media-enzo.nl
Author Email: info@media-enzo.nl
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
  
*/

class RevertFBComments {


	function __construct() {			

		add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts') );
		add_action( 'admin_print_styles', array( &$this, 'register_admin_styles') );
		add_action( 'admin_menu', array( &$this, 'create_menus'));
		
	} // end constructor


	public function activate( $network_wide ) {
		
		
	} // end activate


	public function deactivate( $network_wide ) {
		
			
	} // end deactivate


	public function register_admin_styles() {

		wp_register_style( 'open-graph-metabox-admin-styles', plugins_url( 'open-graph-metabox/css/admin.css' ) );
		wp_enqueue_style( 'open-graph-metabox-admin-styles' );

	} // end register_admin_styles


	public function register_admin_scripts() {
		
		wp_register_script( 'open-graph-metabox-admin-script', plugins_url( 'open-graph-metabox/js/admin.js' ) );
		wp_enqueue_script( 'open-graph-metabox-admin-script' );

	} // end register_admin_scripts
	
	public function create_menus() {
		
		add_options_page('Restore FB Comments', 'Restore FB Comments', 'manage_options', 'restore-fb-comments', array( &$this, 'display_settings_page') );
		
	}
	
	public function display_settings_page() {
	
		
		
		if(isset($_GET['rfc_start']) OR isset($_GET['batch_offset'])) {
			 
			 $posts_per_batch = esc_attr($_GET['posts_per_batch']);
			 $batch_offset = (isset($_GET['batch_offset']) ? esc_attr($_GET['batch_offset']) : 0);
			 $total_posts = new WP_Query(
			 	
			 	array(
			 		"post_type" => "post",
			 		"posts_per_page" => -1
			 	)
			 
			 );
			 
			 $posts = new WP_Query(
			 	
			 	array(
			 		"post_type" => "post",
			 		"posts_per_page" => $posts_per_batch,
			 		"offset" => $batch_offset
			 	)
			 
			 );
			 
			 if($posts->have_posts()) {
				 while($posts->have_posts()) {
				 
					 $posts->the_post();					 
					 
					 $permalink = get_permalink();
					 $fb_request = wp_remote_get("https://graph.facebook.com/comments/?ids=".$permalink);
					 
					 $result = json_decode($fb_request['body']);
					 
					 if($result) {
						 $comments = current($result)->comments->data;
						 
						 if(sizeof($comments) > 0) {
							 
								foreach($comments AS $comment) {
									
									$comment_id = $comment->id;
									$comment_from_name = $comment->from->name;
									$comment_message = $comment->message;
									$comment_created = strtotime($comment->created_time)+(60*60*2);
									$comment_created = date("Y-m-d H:i:s", $comment_created);
									
									$data = array(
										'comment_post_ID' => get_the_ID(),
									    'comment_author' => $comment_from_name,
									    'comment_author_email' => 'facebook@facebook.com',
									    'comment_author_url' => '',
									    'comment_content' => $comment_message,
									    'comment_parent' => 0,
									    'comment_author_IP' => '127.0.0.1',
									    'comment_date' => $comment_created,
									    'comment_approved' => 1
									);

									wp_insert_comment($data);
									
								}
							 
						 } 
					 }
					 
					 ?>
					 <script type="text/javascript">window.location='/wp-admin/options-general.php?page=revert-fb-comments&posts_per_batch=<?=$posts_per_batch?>&batch_offset=<?=$batch_offset+$posts_per_batch?>'</script>
					 <?php
					 // done
				 }
			 } else {
			 
				 echo "<div class='updated'><p>Import finished!</p></div>";
				 
			}
			
			
		}
		
		?>
		
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div>
			<h2>Restore Facebook Comments</h2>
			
			<form name="form" method="get" action="options-general.php">
			
				<input type="hidden" name="page" value="revert-fb-comments" />
				<p>
					This tool will import your Facebook comments back into the comments from WordPress. We advice to empty the current comments
					when starting the import if you have duplicate comments. When starting the import proces, please don't interupt. This may take a while depending on the amount of posts.
					Since Facebook does not return the e-mailaddress and IP-address for the comments, this will be facebook@facebook.com and 127.0.0.1 as IP-address.				
				</p>
				
				<table class="form-table">
				
					<tr>
						<th><label for="posts_per_batch">Posts per batch</label></th>
						<td><input name="posts_per_batch" id="posts_per_batch" type="text" value="20" class="regular-text" /><br/>
							<em>Since there is an execution limit in PHP, the importer will batch 20 posts at a time. Increase at own risk.</em>
						</td>
					</tr>
				</table>
				
				<p class="submit">
					<input type="submit" name="rfc_start" id="submit" class="button button-primary" value="Start import"  />
				</p> 
			</form>
			<p>&nbsp;</p>
			<p>Plugin by <a href='http://www.media-enzo.nl' target="_blank">Media-Enzo</a>.</p>
		</div>
		
		<?php
		
	}
	
  
} // end class

new RevertFBComments();