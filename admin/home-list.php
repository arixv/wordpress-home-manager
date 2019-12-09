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
 * Function  homemanager_list_publications
 */
function homemanager_list_publications() {
	
	$query = new WP_Query( $params = array(
		'post_type' => 'home',
	) );
?>
	<div class="wrap">
	<h2>Homes <a href="post-new.php?post_type=home" class="add-new-h2">Add New</a></h2>
	<table class="wp-list-table widefat fixed posts" >
		<thead>
			<tr>
				<th>Name</th>
				<th>Publish Date</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
<?php
	while ( $query->have_posts() ) : $query->the_post();
?>
	    	<tr>
				<td><?php echo esc_html( $query->post->post_title ); ?></td>
				<td><?php echo esc_html( $query->post->post_modified ); ?></td>
				<td><a href="<?php echo esc_url( 'admin.php?page=homemanager&amp;id=' . $query->post->ID ); ?>" class="button button-primary">Edit Home</a></td>
			</tr>
<?php
	endwhile;
?>
		</tbody>
	</table>
</div>
<?php
}
