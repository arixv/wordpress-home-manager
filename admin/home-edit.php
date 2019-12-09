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
 * Function  homemanager_settings_page_fn
 */
function homemanager_settings_page_fn() {
	if ( ! isset( $_GET['id'] ) ) :
		return false;
	endif;

	$home_id = intval( $_GET['id'] );
	add_thickbox();
	$home = Home::getById( $home_id );
?>
<div class="wrap">

	<link  rel="stylesheet" media="all" type="text/css" href="<?php echo esc_url( get_template_directory_uri() . '/plugins/home-manager/admin/css/home-style.css' ); ?>" />
	<link  rel="stylesheet" media="all" type="text/css" href="<?php echo esc_url( get_template_directory_uri() . '/plugins/home-manager/admin/css/bootstrap-modal.css' ); ?>" />
	<script type="text/javascript" src="<?php echo esc_url( get_template_directory_uri() . '/plugins/home-manager/admin/js/bootstrap-modal.js' ); ?>" ></script>
	
	<div class="post_elements">
		<div style="float:right;margin:20px;">
			<a href="<?php echo esc_url( get_bloginfo('url') ); ?>" target="_blank" id="viewsite" class="button button-large">View Home</a>
			<button type="button" id="btn-publish" class="button button-primary button-large"  >Publish</button>
		</div>

		<a href="?page=homemanager_list_publications"><i class="fa fa-chevron-circle-left"></i> List Homes</a>

		<h2><?php echo esc_html( $home->name ); ?></h2>

		<div id="publishing" class="home-alert" >
			<p>Publicando Home...</p>
			<div class="home-spinner"></div>
		</div>

		<?php
			if ( is_array( $home->groups)) :
				foreach ( $home->groups as $group) :
		?>
					<table class="widefat" style="width:100%;"  id="group-<?php echo esc_attr( $group->ID ); ?>" >
						<thead>
							<tr>
								<th>
									<div style="float:right">
										<a href="#" onclick="jQuery('#group-<?php echo esc_attr( $group->ID ); ?>' ).find('tbody' ).toggle();"  ><i class="fa fa-chevron-up"></i></a>
									</div>
									<?php echo esc_html( $group->post_title ); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<?php
										$home_objects = Home::get_home_objects( $home->ID );
										$elements = array();
										foreach ( $home_objects as $this_object ) :
											if ( (int) $this_object->home_group_id === (int) $group->ID ) :
												$elements[] = $this_object;
											endif;
										endforeach;
									?>
									<table class="element_table" >
										<tr>
											<?php
												$pos = 1;
												if ( is_array( $elements ) ) :
													foreach ( $elements as $element ) :
												?>
													<td><?php display_element( $element ); ?></td>
													<?php if ( $pos % 3 == 0 ) : ?> </tr><tr> <?php endif; ?>
												<?php
													$pos++;
													endforeach;
												endif;
											?>
										</tr>
									</table>

								</td>
							</tr>
					</tbody>
				</table>
			<?php endforeach;?>
		<?php endif;?>
	</div>
	</form>
	<div class="posts-side">
		<div class="categories-dropdown" >
			<?php
				wp_dropdown_categories(
					array(
						'show_option_all' => 'Todas las categorias',
					)
				);
			?>
			<script type="text/javascript" >
				jQuery('#cat').change(function(){
					load_posts_items({
						'page': 1,
						'cat': jQuery('#cat').val()
					});
				});
			</script>
		</div>
		<!-- // CATEGORY DROPDOWN -->

		<h3>Articles</h3>

		<!-- search -->
		<div class="search-box">
			<form name="the-search" id="the-search">
				<input type="search" id="post-search-input" name="s" value="" placeholder="Search" />
			</form>
			<script>
				jQuery('#the-search' ).submit(function(e){
					e.preventDefault();
					load_posts_items({
						'page':1,
						's':jQuery('#post-search-input').val()
					});
					return false;
				});
			</script>

		</div>
		<!-- //search -->
		<ul id="posts-items"></ul>
		<div class="tablenav-pages"></div>
	</div>

</div>
<!-- //wrap -->

<script type="text/javascript">
		var home_id = <?php echo wp_json_encode( $home->ID ); ?>;
		jQuery(document).ready(function() {

			// Droppable Function.
			jQuery( ".element" ).droppable({
				drop: function( event, ui ) {
					var image_src = ui.draggable.find('img').attr('src');
					var post_title = ui.draggable.find('h4').html();
					var element_data = jQuery(this).find('.element_data' );
					var post_category = ui.draggable.attr('cat_id');
					var post_category_name = ui.draggable.attr('cat_name');
					var post_id = ui.draggable.attr('post_id');
					var post_media_type = ui.draggable.attr('post_media_type');
					var post_permalink = ui.draggable.attr('post_permalink');

					// Update Element Attributes.
					jQuery(this).attr('post_id',post_id) ;
					jQuery(this).attr('cat_id',post_category );
					jQuery(this).attr('cat_name',post_category_name);

					// Empty Element HTML.
					element_data.html('');

					// Create Element Image.
					var pic = jQuery('<img />');
					pic.attr('src',image_src);
					var figure = jQuery('<figure />');
					figure.append(pic);
					element_data.append( figure );

					// Create Element Category.
					var h5 = jQuery('<h5>');
					h5.addClass("position_category");
					h5.attr("style",'padding:0 10px;');
					h5.text( post_category_name );
					element_data.append( h5 );

					// Create Element for Title.
					var h4 = jQuery('<h4>');
					h4.text(post_title);
					element_data.append( h4 );

					// Save Position Callback.
					var save_params = {
						'position_id': jQuery(this).attr('position_id'),
						'home_id': home_id,
						'post_id': post_id,
						'post_title': post_title,
						'post_category': post_category,
						'post_media_type': post_media_type,
						'post_permalink': post_permalink,
						'featured_image_source': image_src
					};
					save_position( save_params );

					// Delete data from Draggable Element.
					draggable_element_class = ui.draggable.attr('class');
					if ( draggable_element_class.indexOf( "element" ) >= 0 ) {
						var save_params = {
							'position_id': ui.draggable.attr('position_id'),
							'home_id': home_id,
							'post_id': 0,
							'post_title': '',
							'post_category': 0,
							'post_media_type': '',
							'post_permalink': '',
							'featured_image_source': ''
						};
						save_position( save_params );

						// Create Div Element For Message.
						var div = jQuery('<div />');
						div.attr('class','message');
						div.text("Drag an Article from the list aside");
						ui.draggable.find('.element_data').html('').append(div);

					}

				},
			  over: function(event,ui){
				jQuery(this).addClass('over' );
			  },
			  out: function(event,ui){
				jQuery(this).removeClass('over' );
			  }

			});

			// Make Draggable Post Items
			jQuery(".post-item" ).draggable({
				cursor:"crosshair",
				opacity: 0.9,
				helper: "clone",
			});

			// Make Draggable Elements.
			jQuery(".element" ).draggable({
				cursor:"crosshair",
				opacity: 0.9,
				helper: "clone",
			});


			// Load Posts Items.
			load_posts_items({
				'page': 1,
				'cat': false
			});

		});
	</script>
<?php
}
