<?php
/*
Plugin Name: iSimpleDesign Posts Control Approve
Plugin URI: http://www.isimpledesign.co.uk/
Description: Plugin designed to allows blog posts to main website feed.
Version: The Plugin's Version Number, e.g.: 1.0
Author: Samuel East
Author URI: http://www.isimpledesign.co.uk/
License: A "Slug" license name e.g. GPL2
*/

############################################## MENUS ################################################

add_action('admin_menu', 'isd_create_menu');

function isd_create_menu() {//Function to create our menu
		
		if ( function_exists('add_menu_page') ) {
			
  add_menu_page('ISD Approve', 'ISD Approve', 'manage_options', basename(__FILE__), 'isdposts_control');
		}	
		
}

############################################## END MENUS ################################################

############################################## INCLUDE STYLESHEETS / JAVASCRIPT ################################################

function isdposts_stylesheet() {
	
		$current_path = get_option('siteurl').'/wp-content/plugins/'.basename(dirname(__FILE__));
		echo '<link href="https://s3-eu-west-1.amazonaws.com/isdcloud/jquery/thickbox/thickbox.css" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$current_path.'/css/style.css" type="text/css" rel="stylesheet" />';
}

add_action('admin_head','isdposts_stylesheet');

function isdposts_js() {
	
		$current_path = get_option('siteurl').'/wp-content/plugins/'.basename(dirname(__FILE__));
		echo '<script type="text/javascript" src="https://s3-eu-west-1.amazonaws.com/isdcloud/jquery/jquery-151min.js"></script>';
		echo '<script type="text/javascript" src="https://s3-eu-west-1.amazonaws.com/isdcloud/jquery/thickbox/thickbox-compressed.js"></script>';
		echo '<script type="text/javascript" src="'.$current_path.'/js/main.js"></script>';

}

add_action('admin_head','isdposts_js');

############################################## END INCLUDE STYLESHEETS / JAVASCRIPT ################################################

############################################## AJAX ################################################


add_action('wp_ajax_ajax_approve', 'ajax_approve');

function ajax_approve() {
	//print_r($_POST);
	global $wpdb;
	
	$id = $_POST['whatever'];
   
    $wpdb->update( "" . $wpdb->prefix . "posts", array( 'isd_approve' => 1 ), array( 'ID' => $id ), array( '%s', '%d' ), array( '%d' ) );
	
			   
$json = array( 
             isuccess => $id,
             amessage => 'testing'
);  
 
echo json_encode($json); 
			   
	die(); // this is required to return a proper result
}


add_action('wp_ajax_ajax_allapproved', 'ajax_allapproved');

function ajax_allapproved() {
	//print_r($_POST);
	global $wpdb;
	
	$id = $_POST['whatever'];
    $content = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "posts WHERE id = {$id}", ARRAY_A);
    $wpdb->update( "" . $wpdb->prefix . "posts", array( 'isd_approve' => 0 ), array( 'ID' => $id ), array( '%s', '%d' ), array( '%d' ) );
	
			   
$json = array( 
             isuccess => $id,
             amessage => 'testing'
);  
 
echo json_encode($json); 
			   
	die(); // this is required to return a proper result
}

############################################## END AJAX ################################################

############################################## EVENTS ################################################

function isdposts_control(){
	
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
		
	// add the global database struture
	global $wpdb;
	 
	// query the database 
	$activity = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_status = 'publish' AND isd_approve = 0 ORDER BY id DESC");
	
	$already_approved = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_status = 'publish' AND isd_approve = 1 ORDER BY id DESC");
	
			?>

<div class="wrap">
  <h2>iSimpleDesign Approve</h2>
  <a class="button approved" href="">Approve</a> | <a class="button allready_approved" href="">Approved</a>
  <p></p>
  <FORM id="main_bloc_approved" METHOD="POST" ACTION="">
    <table class='widefat'>
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Author</th>
          <th>View Post</th>
          <th>Date</th>
          <th>Approve</th>
        </tr>
      </thead>
      <tbody>
        <?php $num = "1"; ?>
        <?php $nume = "1"; ?>
        <?php foreach($activity as $v) { ?>
        <tr class="line-<?php echo $v->ID; ?>">
          <td><?php echo $v->ID; ?></td>
          <td><?php echo $v->post_title; ?></td>
          <td><?php $category = get_the_category($v->ID); echo $category[0]->cat_name; ?></td>
          <td><?php the_author_meta( "user_nicename", $v->post_author ); ?></td>
          <td><input alt="#TB_inline?height=300&width=400&inlineId=myOnPageContent-<?php echo $num++; ?>" title="<?php echo $v->post_title; ?>" class="thickbox" type="button" value="Show" />
            <div class="content_hide" id="myOnPageContent-<?php echo $nume++; ?>">
              <p><?php echo $v->post_content; ?></p>
            </div></td>
          <td><?php echo $v->post_date; ?></td>
          <td><input class="check" type="button" value="Change" name="approve" alt="<?php echo $v->ID; ?>" /></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </FORM>
  <!-- BREAK -->
  <FORM id="main_bloc_allready_approved" METHOD="POST" ACTION="">
    <table class='widefat'>
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Author</th>
          <th>View Post</th>
          <th>Date</th>
          <th>Unapprove</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($already_approved as $v) { ?>
        <tr class="line-<?php echo $v->ID; ?>">
          <td><?php echo $v->ID; ?></td>
          <td><?php echo $v->post_title; ?></td>
          <td><?php $category = get_the_category($v->ID); echo $category[0]->cat_name; ?></td>
          <td><?php the_author_meta( "user_nicename", $v->post_author ); ?></td>
          <td><input alt="#TB_inline?height=300&width=400&inlineId=myOnPageContent-<?php echo $num++; ?>" title="<?php echo $v->post_title; ?>" class="thickbox" type="button" value="Show" />
            <div class="content_hide" id="myOnPageContent-<?php echo $nume++; ?>">
              <p><?php echo $v->post_content; ?></p>
            </div></td>
          <td><?php echo $v->post_date; ?></td>
          <td><input class="check_already_approved" type="button" value="Change" name="approve"  alt="<?php echo $v->ID; ?>" /></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </FORM>
</div>
<?php	
		
}

############################################## END EVENTS ################################################


############################################## MAIN FUNCTION ################################################
function isd_posts_feeds(){

global $wpdb;

$showme = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE isd_approve = 1");

//print_r($showme);

foreach($showme as $v) { ?>
<div class="post">
  <div class="title">
    <h3><a href="<?php echo get_permalink($v->ID); ?>"><?php echo $v->post_title; ?></a></h3>
  </div>
  <!-- close title -->
  <div class="excerpt"><?php echo substr($v->post_content, 0, 200); ?></div>
  <!-- close excerpt -->
  <div class="meta">
    <p>Posted by: <strong>
      <?php the_author(); ?>
      </strong></p>
  </div>
  <!-- close meta -->
</div>
<!-- close post -->
<?php } }
function isdwpposts_activate(){ //This is all the stuff the plug-in needs to do when it is activated
global $wpdb;
$checkfields = $wpdb->get_col("SHOW FIELDS FROM " . $wpdb->prefix . "posts");
if (!in_array('isd_approve', $checkfields))
{
register_activation_hook( __FILE__, 'isdwpposts_activate' );
$result = mysql_query("ALTER TABLE " . $wpdb->prefix . "posts ADD isd_approve INT(2) DEFAULT 0");
return $result;
}
}
isdwpposts_activate();