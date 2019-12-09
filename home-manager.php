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

require_once('home.php' );
require_once('home-objects.php' );
require_once('admin/home-edit.php' );
require_once('admin/home-list.php' );
require_once('admin/home-metaboxes.php' );

/**
 * Function homemanager_save_postdata
 *
 * @author Ariel Velaz (ariel@frooit.com)
 */
function homemanager_save_postdata() {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) :
		return;
	endif;

	if ( ! current_user_can('publish_posts' ) ) :
		return;
	endif;

	global $post;

	if ( isset( $_POST['post_type'] ) ) :
		$post_type = sanitize_text_field( $_POST['post_type'] );

		// Home Updates.
		if ( 'home' === $post_type ) :
			$site_id = intval( $_POST['site_id'] );
			$home_id = intval( $_POST['home_id'] );
			if ( is_numeric( $site_id ) ) :
				update_post_meta( $post->ID, "site_id", $site_id );
			endif;
		endif;

		// Home Group Updates.
		if ( 'home-group' === $post_type ) :
			if ( isset( $_POST["home_id"] ) && '' !== intval( $_POST["home_id"] ) ) :
				update_post_meta( $post->ID, "home_id", intval( $_POST["home_id"] ) );
			endif;
			if ( isset( $_POST["group_tag"]) && $_POST["group_tag"] !== '' ) :
				update_post_meta( $post->ID, "group_tag", sanitize_text_field( $_POST["group_tag"] ) );
			endif;
		endif;

		// Home Objects Updates.
		if ( 'home-object' === $post_type ) :
			if ( isset( $_POST["home_id"] ) && '' !== intval( $_POST["home_id"] ) ) :
				update_post_meta( $post->ID, "home_id", intval( $_POST["home_id"] ) );
			endif;
			if ( isset( $_POST["home_group_id"] ) && '' !== sanitize_text_field( $_POST["home_group_id"] ) ) :
				update_post_meta( $post->ID, "home_group_id", intval( $_POST["home_group_id"] ) );
			endif;
			if ( isset( $_POST["css_class_name"] ) && '' !== sanitize_text_field( $_POST["css_class_name"] ) ) :
				update_post_meta( $post->ID, "css_class_name", sanitize_text_field( $_POST["css_class_name"] ) );
			endif;
			if ( isset( $_POST["post_category"] ) && '' !== sanitize_text_field( $_POST["post_category"] ) ) :
				update_post_meta( $post->ID, "post_category", sanitize_text_field( $_POST["post_category"] ) );
			endif;
			if ( isset( $_POST["draft_id"] ) && '' !== sanitize_text_field( $_POST["draft_id"] ) ) :
				update_post_meta( $post->ID, "draft_id", intval( $_POST["draft_id"] ));
			endif;
			if ( isset( $_POST["published_id"]) && '' !== sanitize_text_field( $_POST["published_id"] ) ) :
				update_post_meta( $post->ID, "published_id", intval( $_POST["published_id"] ) );
			endif;
		endif;
	endif;

}
add_action('save_post', 'homemanager_save_postdata' );

/**
 * Function home_display_part
 *
 * @author Ariel Velaz (ariel@frooit.com)
 * @param int $part Description.
 * @param int $preview Description.
 */
function home_display_part( $part, $preview = 0 ) {
	include( plugin_dir_path( __FILE__ ) . 'templates/'.$part . '.php' );
}

/**
 * Function homemanager_add_menu
 *
 * @author Ariel Velaz (ariel@frooit.com)
 */
function homemanager_add_menu() {
	if ( current_user_can('publish_posts' ) ) {

		add_menu_page(
			'Home Manager',
			'Home Manager',
			$capability = 'edit_posts',
			$menu_slug = 'homemanager_list_publications',
			$function = 'homemanager_list_publications',
			$icon_url = ''
		);
		add_submenu_page(
			null,
			'Edit Home Manager',
			'Edit Home Manager',
			$capability = 'edit_posts',
			$menu_slug='homemanager',
			$function='homemanager_settings_page_fn',
			$icon_url = '',
			$position = '-1'
		);

		add_submenu_page(
			null,
			'Add Home',
			'Add Home',
			$capability = 'edit_posts',
			$menu_slug='homemanager_add_new',
			$function='homemanager_add_new',
			$icon_url = '',
			$position = '-1'
		);
		add_submenu_page(
			null,
			'Add Home Position',
			'Add Home Position',
			$capability = 'edit_posts',
			$menu_slug='homemanager_add_position',
			$function='homemanager_add_position',
			$icon_url = '',
			$position = '-1'
		);
		add_submenu_page(
			null,
			'Add Home Position',
			'Add Home Position',
			$capability = 'edit_posts',
			$menu_slug='homemanager_do_add_position',
			$function='homemanager_do_add_position',
			$icon_url = '',
			$position = '-1'
		);
	}

}
add_action( 'admin_menu', 'homemanager_add_menu' );


function homemanager_enqueue( $hook) {
	wp_enqueue_script( 'load-posts', get_template_directory_uri() . '/plugins/home-manager/admin/js/load-posts.js' );
}
add_action( 'admin_enqueue_scripts', 'homemanager_enqueue' );


function add_toolbar_items( $admin_bar) {
	$admin_bar->add_menu( array(
		'id'    => 'home-manager',
		'title' => 'Home Manager',
		'href'  => admin_url() . 'admin.php?page=homemanager_list_publications',
		'meta'  => array(
			'title' => __('Home Manager' ),
		),
	) );
}
add_action('admin_bar_menu', 'add_toolbar_items', 100);

function homemanager_add_new() {
	$Sites = IMD_Config::getConfigDomain();
?>
	<div class="wrap">
		<h2>Add New Home</h2>
		<form method="post" action="?page=homemanager_create">
			<label>Name</label>
			<input type="text" name="home_name" />
			<br/>
			<label>Site</label>
			<select name="site_id" >
				<?php foreach ( $Sites as $site) :?>
				<option value="<?php echo esc_attr( $site['site_id'] ); ?>" >
					<?php echo esc_html( $site['name'] ); ?>
				</option>
				<?php endforeach; ?>
			</select>
			<br/>
			<button class="button button-primary">Save</button>
		</form>
	</div>
<?php
}



/**
 * Function display_element
 *
 * @author Ariel Velaz (ariel@frooit.com)
 * @param object $element Description.
 */
function display_element( $element ) { ?>
	<div class="element" id="element-<?php echo esc_attr( $element->ID ); ?>" position_id="<?php echo esc_attr( $element->ID ); ?>" post_id="<?php echo esc_attr( ( isset( $element->draft_id ) ) ? $element->draft_id : '' ); ?>" cat_id="<?php echo esc_attr( ( isset( $element->post_category ) ) ? $element->post_category : '' ); ?>" cat_name="<?php echo esc_attr( ( isset( $element->post_category_name ) ) ? $element->post_category_name : '' ); ?>" >
		<div class="element_head">
			<div class="tools">
				<a href="#" class="btn-edit-element" element-position="<?php echo esc_attr( $element->ID ); ?>" ><i class="fa fa-edit"></i></a>
				<a href="#" class="btn-remove-element" element-position="<?php echo esc_attr( $element->ID ); ?>" ><i class="fa fa-trash-o"></i></a>
			</div>
			<?php echo esc_html( $element->post_title ); ?>
			<?php //imd_console($element);  ?>
		</div>

		

		<div class="element_data">
			<?php if ( '0' != $element->draft_id && isset( $element->draft_post ) ) : ?>
				<div class="post" id="<?php echo esc_attr( $element->draft_post->ID ); ?>">

					<?php if ( isset( $element->draft_post->post_image[0] ) ) : ?>
						<figure><img src="<?php echo esc_url( $element->draft_post->post_image[0] ); ?>" alt="" /></figure>
					<?php endif; ?>
					
					<?php
					// Post Category.
					if ( '0' !==  $element->post_category ) : ?>
						<?php $category = get_category( $element->post_category ); ?>
						<h5 class="position_category" style="padding:0 10px;"><?php echo esc_html( $category->name ); ?></h5>
					<?php
					endif;
					?>
					
					<h4><?php echo esc_html( $element->draft_post->post_title ); ?></h4>
					<br clear="all" />
				</div>
			<?php else : ?>
				<div class='message'>Drag an Article from the list aside</div>
			<?php endif;?>
		</div>
	</div>
<?php }



/**
 * Function homemanager_save_position_callback
 *
 * @author Ariel Velaz (ariel@frooit.com)
 */
function homemanager_save_position_callback() {

	check_ajax_referer( 'save_position', 'security' );

	if ( current_user_can('publish_posts' ) ) :

		// HOME POSITION.
		$home_object_id = intval( $_POST['position_id'] );
		$home_id = intval( $_POST["home_id"] );
		$post_id = intval( $_POST["post_id"] );
		$post_category = intval( $_POST["post_category"] );

		// DELETE TRANSIENT
		delete_transient('home_objects_' . $home_id );

		// UPDATE HOME POSTS TEMP.
		update_post_meta( $home_object_id, "draft_id", $post_id );
		update_post_meta( $home_object_id, "post_category", $post_category );
		$draft_id = get_post_meta( $home_object_id, 'draft_id', $single = 1 );
	endif;
	wp_die();
}
add_action( 'wp_ajax_save_position_action', 'homemanager_save_position_callback' );


/**
 * Function homemanager_save_position_javascript
 *
 * @author Ariel Velaz (ariel@frooit.com)
 */
function homemanager_save_position_javascript() {
	$save_position_ajax_nonce  = wp_create_nonce('save_position');
?>
<script type="text/javascript">
	function save_position( data_position ) {
		var data = {
			action: 'save_position_action',
			security: <?php echo wp_json_encode( $save_position_ajax_nonce ); ?>,
			position_id:data_position.position_id,
			post_id: data_position.post_id,
			post_title: data_position.post_title,
			post_category: data_position.post_category,
			home_id: home_id
		};

		jQuery.post(
			ajaxurl,
			data,
			function(response) {});
	}
</script>
<?php
}
add_action( 'admin_footer', 'homemanager_save_position_javascript' );

/**
 * Function homemanager_load_posts_items_action
 *
 * @author Ariel Velaz (ariel@frooit.com)
 */
function homemanager_load_posts_items_action() {
	$params = array(
		'post_status' => 'publish',
		'paged' => intval( sanitize_text_field( $_POST['paged'] ) ),
		'posts_per_page'=> 5,
		'post_type'=> 'post'
	);

	$requested_category = false;

	if ( isset( $_POST['cat'] ) && 0 !== ( $requested_category = intval( sanitize_text_field( $_POST['cat'] ) ) ) ) :
		$params['cat'] = $requested_category ;
	endif;

	if ( isset( $_POST['s'] ) ) :
		$params['s'] = sanitize_text_field( $_POST['s'] );
	endif;

	$custom_query = new WP_QUERY( $params );
	$posts = array();

	if ( $custom_query->have_posts() ) :
		while ( $custom_query->have_posts()) :
			$custom_query->the_post();
			$thumb_id = get_post_thumbnail_id( $custom_query->post->ID );
			$post_image = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
			$featured_image = ( isset( $post_image[0] ) ) ? $post_image[0] : '';
			$post_categories = get_the_category();

			$post = array(
				'post_id' => $custom_query->post->ID,
				'post_date' => $custom_query->post->post_date,
				'post_title' => $custom_query->post->post_title,
				'post_permalink'=> '/'.$custom_query->post->post_name,
				'post_image' => $featured_image,
				'post_type' => get_post_type( $custom_query->post->ID )
			);

			if ( isset( $post_categories[0] ) ) :
				$post['category_id'] = $post_categories[0]->term_id;
				$post['category_name'] = $post_categories[0]->name;
				$post['category_link'] = '';

				if ( ! is_wp_error( $term_link = get_term_link( $post_categories[0]->term_id ) ) ) :
					$post['category_link'] = $term_link;					
				endif;

			else :
				$post['category_id'] = 0;
				$post['category_name'] = "";
				$post['category_link'] = "";
			endif;

			$posts[] = $post;
		endwhile;
	endif;
	wp_reset_postdata();
	wp_send_json( $posts );
	wp_die();
}
add_action( 'wp_ajax_load_posts_items_action', 'homemanager_load_posts_items_action' );

/**
 * function homemanager_publish_action
 *
 * @author Ariel Velaz (ariel@frooit.com)
 */

function homemanager_publish_action() {
	$home_id = intval( $_POST['home_id'] );
	Home::Publish( $home_id );
	wp_die();
}
add_action( 'wp_ajax_publish_action', 'homemanager_publish_action' );

/*
* AJAX PUBLISH FUNCTION
* @author Ariel Velaz (ariel@frooit.com)
*/
add_action( 'admin_footer', 'homemanager_publish_javascript' );
function homemanager_publish_javascript() {
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("#btn-publish").click(function() {
				if ( confirm("¿Are you sure to publish?"))
				{
					var data = {
						action: 'publish_action',
						home_id: home_id
					};

					jQuery('#publishing' ).show();

					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#publishing' ).hide();
						alert("Home Published");
					});
				}
			});
		});
	</script>
<?php
}


/*
*
* REMOVE ELEMENT HOME CALLBACK
* @author Ariel Velaz (ariel@frooit.com)
*
*/
add_action( 'wp_ajax_remove_element_action', 'homemanager_remove_element_action' );
function homemanager_remove_element_action() {

	check_ajax_referer( 'remove_element', 'security' );

	if ( current_user_can('publish_posts' ) ) :

		// HOME POSITION.
		$home_object_id = intval( $_POST['position_id'] );

		// UPDATE HOME POSTS TEMP.
		update_post_meta( $home_object_id, "draft_id", 0 );
		update_post_meta( $home_object_id, "post_category", 0 );
	endif;
	wp_die();
}


/*
*
* EDIT ELEMENT HOME
* @author Ariel Velaz (ariel@frooit.com)
*
*/
add_action( 'wp_ajax_get_element_action', 'homemanager_get_element_action' );
function homemanager_get_element_action() {

	//check_ajax_referer( 'get_element', 'security' );

	if ( current_user_can('publish_posts' ) ) {

		// HOME POSITION.
		$home_object_id = intval( $_POST['position_id'] );
		$home_object = Home::get_home_object_single( $home_object_id );
		if ( isset( $home_object->post_title ) && isset( $home_object->draft_post ) ) {
?>
		
		<div class="modal-dialog">
			<div class="modal-content">
				<input type="hidden" id="update_home_position_post_id" name="update_post_id" value="<?php echo esc_attr( $home_object->draft_post->ID ); ?>" />
				<input type="hidden" id="update_home_position_id" name="update_home_position_id" value="<?php echo esc_attr( $home_object->ID ); ?>" />
				<input type="hidden" id="update_home_position_home_id" name="update_home_id" value="<?php echo esc_attr( $home_object->home_id ); ?>" />

				<div class="modal-header">
	    			<button type="button" class="close" data-dismiss="modal">×</button>
	    			<h3>Update <?php echo esc_html( $home_object->post_title ); ?></h3>
	    		</div>
			    <div class="modal-body">
			    	<!-- <div id="titlediv">
			    		<div id="titlewrap">
			    			<input id="title" type="text" name="element_title" value="<?php echo esc_attr( $home_object->draft_post->post_title ); ?>" />
			    		</div>
			    	</div>
			    	<br/> -->
			    	<label>Category</label>
			    	<select id="update_home_position_category" name="category" >
			    		<?php foreach ( $home_object->draft_post->categories as $category ) : ?>
			    			<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php echo selected( $category->term_id, $home_object->post_category ); ?> ><?php echo esc_html( $category->name ); ?></option>
			    		<?php endforeach; ?>
			    	</select>
			    </div>
			    <div class="modal-footer">
				    <a href="#" id="btn-update-position" class="button button-primary button-large" data-dismiss="modal" >Guardar</a>
				    <a href="#" class="button button-default button-large" data-dismiss="modal">Cancelar</a>
			    </div>
			</div>
		</div>

<?php
		}
	} else {
		echo '';
	}
	wp_die();
}

/**
* Function imd_homemanager_edit_element
*/
add_action('admin_footer', 'imd_homemanager_edit_element');
function imd_homemanager_edit_element() {
	$ajax_nonce  = wp_create_nonce('edit_element');
?>
	<div class="modal fade" id="modal-edit-element" ></div>
	<script type="text/javascript" >
		jQuery(document).ready(function() {

			function update_home_position( position_id ) {
				post_category = jQuery('#update_home_position_category').val();
				post_id = jQuery('#update_home_position_post_id').val();
				home_id = jQuery('#update_home_position_home_id').val();

				var data = {
					action: 'save_position_action',
					position_id: position_id,
					home_id: home_id,
					post_id: post_id,
					post_category: post_category,
					security: <?php echo wp_json_encode( wp_create_nonce('save_position') ); ?>
				};

				jQuery.post( ajaxurl, data, function( response ) {
					jQuery('#element-' + position_id ).find('.position_category').html( jQuery("#update_home_position_category option[value='" + post_category + "']").text() );
				});
			}
			

			jQuery(".btn-edit-element").click( function(e) {
				e.preventDefault();
				jQuery('#modal-edit-element').html( '' );
				var position_id =  jQuery(this).attr("element-position");
				var data = {
					action: 'get_element_action',
					position_id: position_id,
					security: <?php echo wp_json_encode( $ajax_nonce ); ?>
				};

				jQuery.post( ajaxurl, data, function( response ) {

					jQuery('#modal-edit-element').html( response );
					jQuery("#btn-update-position").click( function(e) {
						update_home_position( position_id );
					} );
				});
				jQuery('#modal-edit-element').modal('show');
			} );
		} );
	</script>
<?php
}

/*
* AJAX PUBLISH FUNCTION
* @author Ariel Velaz (ariel@frooit.com)
*/
add_action( 'admin_footer', 'homemanager_remove_javascript' );
function homemanager_remove_javascript() {
	$ajax_nonce  = wp_create_nonce('remove_element');
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {

			jQuery(".btn-remove-element" ).click(function(e) {
				e.preventDefault();
				var element_position =  jQuery(this).attr("element-position");
	
				var data = {
					action: 'remove_element_action',
					position_id:element_position,
					security: <?php echo wp_json_encode( $ajax_nonce ); ?>
				};

				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#element-'+element_position).attr('post_id','0' );
					jQuery('#element-'+element_position).attr('post_permalink','' );
					jQuery('#element-'+element_position).attr('post_type','' );
					jQuery('#element-'+element_position).attr('cat_id','' );
					jQuery('#element-'+element_position).attr('cat_name','' );
					jQuery('#element-'+element_position).attr('cat_link','' );
					var div = jQuery('<div />');
					div.attr('class','message');
					div.text("Drag an Article from the list aside");
					var element_data = jQuery('#element-'+element_position).find(".element_data");
					element_data.html('');
					element_data.append(div);
				});
				
			});
		});
	</script>
<?php
}

/*
* AJAX PUBLISH FUNCTION
* @author Ariel Velaz (ariel@frooit.com)
*/
add_action( 'admin_footer', 'homemanager_switch_javascript' );
function homemanager_switch_javascript() {
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {

			jQuery(".btn-switch" ).click(function(e) {
				e.preventDefault();
				var group_id = jQuery(this).attr("group-id");
				var active = jQuery(this).attr("group-active");
				var message;
				var new_state;
				if ( active == 1) {
					message = "Are you sure to inactive these elements?";
					new_state = 0;
				}else{
					message = "Are you sure to Active these elements?";
					new_state = 1;
				}
				if ( confirm(message) ) {
					var data = {
						action: 'switch_action',
						group_id:group_id,
						group_active:new_state,
						home_id:home_id
					};

					jQuery.post(ajaxurl, data, function(response) {
						if ( new_state == 1) {
							jQuery('#group-'+group_id).find(".on").show();
							jQuery('#group-'+group_id).find(".off").hide();
						}else{
							jQuery('#group-'+group_id).find(".off").show();
							jQuery('#group-'+group_id).find(".on").hide();
						}

					});
				}
			});

		});
	</script>
<?php
}
