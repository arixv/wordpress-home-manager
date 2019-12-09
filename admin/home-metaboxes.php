<?php
/**
 * Plugin Name: Home Manager
 * Plugin URI: 
 * Description: A Home Manager Plugin
 * Version: 1.0
 * Author: FrooIT
 * Author URI: http://frooit.com
 * Requires at least: 3.8
 * Tested up to: 3.8
 *
 * Text Domain: homemanager
 *
 * @package HomeManager
 * @category Core
 * @author FrooIT
 */

/**
 * Function homemanager_add_custom_boxes
 */
function homemanager_add_custom_boxes() {

	if ( current_user_can('publish_posts' ) ) {

		add_meta_box(
			"home_id", 
			__("Home", "home_id" ) , 
			"homemanager_home_id_field",
			'home-group',
			'side',
			'low'
		);

		add_meta_box(
	    	"home_group_id",
	    	__("Home Group","imdcms"), 
	    	"homemanager_group_id_field", 
	    	"home-object", 
	    	"normal",
	    	"low"
	    );


	}
} 
add_action('admin_init', 'homemanager_add_custom_boxes' );



/**
 * Function homemanager_home_id_field
 */
function homemanager_home_id_field() {
	global $post;
	$home_id = get_post_meta( $post->ID, 'home_id', 1 );
	$group_tag = get_post_meta( $post->ID, 'group_tag', 1 );
	$HomeList = Home::get_list();
?>
	<div id="featured_custom_fields">
		<label><strong>Home</strong></label> 
		<select name="home_id" >
			<option value="">Select Home</option>
			<?php foreach ( $HomeList as $Home) :?>
				<option value="<?php echo esc_attr( $Home->ID ); ?>" <?php selected( $Home->ID, $home_id ); ?> ><?php echo esc_html( $Home->post_title ); ?></option>
			<?php endforeach; ?>
		</select>
		<br/>
		<label><strong>Group Tag</strong></label> 
		<select name="group_tag" >
			<option value="">- Select Group Tag -</option>
			<option value="highlight" <?php selected( $group_tag, 'highlight' ); ?> >highlight</option>
			<option value="secondary" <?php selected( $group_tag, 'secondary' ); ?> >secondary</option>
		</select>
	</div>
<?php
}

/**
 * Function homemanager_group_id_field
 */
function homemanager_group_id_field() {
	global $post;
	$home_id = get_post_meta( $post->ID, 'home_id', 1 );
	$post_category = get_post_meta( $post->ID, 'post_category', 1 );
	$css_class_name = get_post_meta( $post->ID, 'css_class_name', 1 );
	$home_group_id = get_post_meta( $post->ID, 'home_group_id', 1 );
	$published_id = get_post_meta( $post->ID, 'published_id', 1 );
	$draft_id = get_post_meta( $post->ID, 'draft_id' , 1 );
	$HomeList = Home::get_list();
	$HomeGroups = Home::get_groups( $home_id );
?>	
	<div id="featured_custom_fields">
		<label><strong>Home</strong></label> 
		<select name="home_id" >
			<option value="">Select Home</option>
			<?php foreach ( $HomeList as $Home) :?>
				<option value="<?php echo esc_attr( $Home->ID ); ?>" <?php selected( $Home->ID, $home_id); ?> ><?php echo esc_attr( $Home->post_title ); ?></option>
			<?php endforeach; ?>
		</select>
		<br/>
		<label><strong>Home Group</strong></label> 
		<select name="home_group_id" >
			<option value="">Select Home Group</option>
			<?php foreach ( $HomeGroups as $Group) :?>
				<option value="<?php echo esc_attr( $Group->ID ); ?>" <?php selected( $Group->ID, $home_group_id); ?> ><?php echo esc_attr( $Group->post_title ); ?></option>
			<?php endforeach; ?>
		</select>
		<br/>
		<label><strong>Post Category</strong></label> 
		<input type="text" name="post_category" value="<?php echo esc_attr( $post_category ); ?>"/>
		<br/>
		<label><strong>CSS Class Name</strong></label> 
		<input type="text" name="css_class_name" value="<?php echo esc_attr( $css_class_name ); ?>"/>
		<br/>
		<label><strong>Published Post</strong></label> 
		<input type="text" name="published_id" value="<?php echo esc_attr( $published_id ); ?>"/>
		<br/>
		<label><strong>Draft Post</strong></label> 
		<input type="text" name="draft_id" value="<?php echo esc_attr( $draft_id ); ?>"/>
	</div>
<?php
}
