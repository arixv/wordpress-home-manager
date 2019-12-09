<?php 
$objects = array();
$debug = false;
$group = false;
$is_mobile = jetpack_is_mobile();
$home = Home::get_default();
$group_name = 'secondary';
$domain =  IMD_Config::getOption('domain_name');

if ( ! isset( $home->ID ) ) :
	return;
endif;

if ( 1 === $preview ) :
	$objects = Home::get_home_objects( $home->ID, $group_tag, $debug );
else :
	
	// Get From Transient
	if ( false !== ( $home_published_objects = get_transient( 'home_published_objects' ) ) ) :
		$home_published_objects = json_decode( $home_published_objects );
	else :
		$home_objects = Home::create_json( $home->ID, $debug = false );
		set_transient( Home::$published_key, $home_objects, HOUR_IN_SECONDS );
		$home_published_objects = json_decode( $home_objects );
	endif;

	if ( is_array( $home_published_objects->groups ) ) :
		foreach( $home_published_objects->groups as $group ) :
			if ( $group_name === $group->group_tag ) :
				$found = $group;
				break;
			endif;
		endforeach;
		$objects = $found->objects;
	endif;
endif;
?>
<?php if ( ! empty( $objects ) ) : ?>
<section data-vr-zone="Más Noticias" itemscope itemtype="http://schema.org/Blog" >
	<section class="articles list clearfix">
		<?php 
			$position = 1;
			$total_objects = count( $objects );
		?>
			<div class="clearfix">
			<?php foreach ( $objects as $item ) :
					if ( ! is_object( $item->published_post ) ) {
						continue;
					}
					$published_post = $item->published_post;
			?>
				<div id="secondary-hightlight-<?php echo esc_attr( $position ); ?>" class="column">
					<?php if ( isset( $published_post->ID ) ) : ?>
					<article  class="article <?php echo esc_attr( ( isset( $published_post->css_class_name ) ) ? $published_post->css_class_name : '' ); ?>" itemprop="blogPost" data-vr-contentbox="SecondaryNews" >
						
						<a href="<?php echo esc_url( get_permalink( $published_post->ID ) ); ?>" >
							<span class="feat-image" style="background-image:url('<?php echo esc_url( $published_post->post_image_medium[0] ); ?>');" ></span>
						</a>

						<?php if ( isset( $published_post->post_media_type) && $published_post->post_media_type == 'video' ) : ?>
							<div class="media-icon" ><i class="fa fa-play-circle-o" style="" ></i></div>
						<?php endif; ?>
						<?php if ( isset( $published_post->post_media_type) && $published_post->post_media_type == 'gallery' ) : ?>
							<div class="media-icon" ><i class="fa fa-camera" ></i></div>
						<?php endif; ?>

						<div class="article-right">
							<?php  
								if ( ! $is_mobile) 
									$published_post->post_title = imd_reduce_long_title($published_post->post_title);
									
								if ( is_array( $published_post->categories ) && isset( $item->post_category ) && '0' !== $item->post_category && '' !== $item->post_category ) :
									$categories_pluck = wp_list_pluck( $published_post->categories, 'name','term_id' );
									if ( ! is_wp_error( $category_link = get_term_link( $item->post_category ) ) ) : ?>
										<div class="meta">
											<span class="category" >
												<a href="<?php echo esc_url( $category_link ); ?>" ><?php echo esc_html( $categories_pluck[ $item->post_category ] ); ?></a>
											</span>
										</div>
									<?php endif; ?>
							<?php endif; ?>
							<h2>
								<a href="<?php echo esc_url( get_permalink( $published_post->ID ) ); ?>" ><?php echo esc_html( $published_post->post_title ); ?>
								</a>
							</h2>
						</div>
					</article>
					<?php endif; ?>
				</div>

				<div class="column">
					<?php
						imd_google_ad( array(
							'key' => '300x250_native_ad_home_pos' . $position,
							'class' => 'native_ad_homepage_300x250',
						) );
					?>
				</div>
				
				<?php

				if (! $is_mobile){
					if ( 6 === $position ) :
						imd_google_ad( array(
							'key' => '728x90_pos2',
							'class' => 'ad_728x90'
						) );
					endif;  
				}	
				if ( 3 === $position && $is_mobile ) :  
					imd_google_ad( array(
						'key' => '320x50_pos2',
						'class' => 'ad_320x50',
					) );
				endif; 
				if ( 6 === $position && $is_mobile) :
					imd_google_ad( array(
						'key' => '320x50_pos3',
						'class' => 'ad_320x50'
					) );
				endif; 
				if ( 9 === $position && $is_mobile) :
					imd_google_ad( array(
						'key' => '320x50_pos4',
						'class' => 'ad_320x50'
					) );
				endif;
				?>
				<?php if ( $position % 3 == 0 && $position < $total_objects) : ?>
					</div><div class="clearfix" >
				<?php elseif ( $position == $total_objects) : ?>
				  	</div>
				<?php endif; ?>
			
			<?php $position ++; ?>
		<?php endforeach; ?>
	</section>	
</section>
<?php
endif;
