<?php 
$objects = array();
$debug = false;
$group = false;
$is_mobile = jetpack_is_mobile();
$home = Home::get_default();
$group_name = 'highlight';
$domain = IMD_Config::getOption( 'domain_name' );
global $ab_test;

// If Home not exists.
if ( ! isset( $home->ID ) ) :
	return;
endif;

// If Preview Home, get draft posts.
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
<section data-vr-zone="Top Stories" class="highlight clearfix" >
	<?php
		$position = 1;

		foreach ( $objects as $item ) :

			if ( ! is_object( $item->published_post ) ) {
				continue;
			}
			$published_post = $item->published_post;

			if ("paratimujer.us" === $domain ) :
			 	$image_src = ( isset( $published_post->post_image_large[0]) ) ? $published_post->post_image_large[0] : $published_post->post_image_medium[0];
			else :
				if ( isset( $published_post->post_image_medium[0] ) ) :
					$image_src = ( 1 === $position && isset( $published_post->post_image_large[0]) ) ? $published_post->post_image_large[0] : $published_post->post_image_medium[0];
				endif;
			endif;	

			//ID's for GTM tracking
			if ("laopinion.com" === $domain && $is_mobile ){
					$image_id = "HP_MOBILE_" . $ab_test . "_img-homemanager-article-link-" . $position;
					$title_id = "HP_MOBILE_" . $ab_test . "_title-homemanager-article-link-" . $position;
			}else{
					$image_id = "img-homemanager-article-link-" . $position;
					$title_id = "title-homemanager-article-link-" . $position;
			}


			$has_category = false;
			$category = "";

			if ( is_array( $published_post->categories ) && isset( $item->post_category ) && '0' !== $item->post_category && '' !== $item->post_category ) :
					$categories_pluck = wp_list_pluck( $published_post->categories, 'name','term_id' );
					$category = $categories_pluck[ $item->post_category ];
					$has_category = true;		
					$category_label = strtolower(sanitize_title($category));	
			endif; 

			if ( 1 === $position ) :
				// Main highlight.
	?>
			<div class="main-highlight-content">

				<article class="article clearfix <?php echo esc_attr( $item->css_class_name ); ?>" itemprop="blogPost" data-vr-contentbox="PrimaryNews" >


					<a href="<?php echo esc_url( get_permalink( $published_post->ID ) ); ?>" class="img-article-link" >
						<?php if ("paratimujer.us" === $domain ) :?>				
									<figure id="<?php echo esc_attr( $image_id );?>" style="background-image: url(<?php echo esc_url( $image_src); ?>);">
										<div class="<?php echo esc_attr("category-label category-label-" . strtolower(sanitize_title($category_label)) );?>" style="background-image: url(<?php echo esc_attr( "wp-content/themes/vip/impre-paratimujer/images/home-category-labels/" .$category_label . ".svg"); ?>);">	</div>		
									</figure>
								<?php 	else :  ?>		
									<figure id="<?php echo esc_attr( $image_id );?>" style="background-image:url(<?php echo esc_url( $image_src); ?>);">
									</figure>
							<?php 	endif;?>	

						
					</a>


					<?php
						if  ( $has_category ) :	?>
							<p class="category" >
								<?php echo esc_html( $category ); ?>
							</p>
					<?php endif; ?>	

					<h2>
						<a href="<?php echo esc_url( get_permalink( $published_post->ID ) ); ?>" class="title-article-link" id="<?php echo esc_attr( $title_id );?>">
							 <?php echo esc_html( $published_post->post_title ); ?> 	
						</a>
					</h2>

				</article>

			</div>	

				<?php
					if ( $is_mobile && ! Jetpack_User_Agent_Info::is_tablet() ) :
						imd_google_ad( array(
							'key' => '320x50_pos1',
							'class' => 'ad_320x50',
						) );
					endif;
				?>

		<div class="secondary-highlight-content">

	<?php	else : 

				if ( ! $is_mobile )	:
		?>
					<article class="article clearfix <?php echo esc_attr( $item->css_class_name ); ?>" itemprop="blogPost" data-vr-contentbox="PrimaryNews" >
						<a href="<?php echo esc_url( get_permalink( $published_post->ID ) ); ?>" class="title-article-link"  >
							<?php if ("paratimujer.us" === $domain ) :?>		
									<figure id="<?php echo esc_attr( $image_id );?>" style="background-image: url(<?php echo esc_url( $image_src); ?>);">
										<div class="category-label" style="background-image: url(<?php echo esc_attr( "wp-content/themes/vip/impre-paratimujer/images/home-category-labels/" .strtolower ($category) . ".svg"); ?>);">	</div>	
									</figure>	
								<?php 	else :  ?>		
									<figure id="<?php echo esc_attr( $image_id );?>" style="background-image:url(<?php echo esc_url( $image_src); ?>);">
									</figure>
							<?php 	endif;?>	
							
						</figure>
						</a>

						<?php
						if  ( $has_category ) :	?>
							<p class="category" >
								<?php echo esc_html( $category ); ?>
							</p>
						<?php endif; ?>	

						<a href="<?php echo esc_url( get_permalink( $published_post->ID ) ); ?>" class="title-article-link" id="<?php echo esc_attr( $title_id );?>">
							<h2><?php echo esc_html( $published_post->post_title ); ?></h2>
						</a>
						
					</article>

				<?php 	else :  ?>		

					<article class="article clearfix <?php echo esc_attr( $item->css_class_name ); ?>" itemprop="blogPost" data-vr-contentbox="PrimaryNews" >
						
						<div class="article-image">
							<a href="<?php echo esc_url( get_permalink( $published_post->ID ) ); ?>" class="img-article-link"  >
								<?php if ("paratimujer.us" === $domain ) :?>		
									<figure id="<?php echo esc_attr( $image_id );?>" style="background-image: url(<?php echo esc_url( $image_src); ?>);">
										<div class="category-label" style="background-image: url(<?php echo esc_attr( "wp-content/themes/vip/impre-paratimujer/images/home-category-labels/" .strtolower ($category) . ".svg"); ?>);">	</div>	
									</figure>
								<?php 	else :  ?>		
								<figure>
										<img src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $published_post->post_title ); ?>" id="<?php echo esc_attr( $image_id );?>" /> 
									</figure>
								<?php 	endif;?>		
							</a>
						</div>

						<div class="article-title">			
							<?php
								if ( is_array( $published_post->categories ) && isset( $item->post_category ) && '0' !== $item->post_category && '' !== $item->post_category ) :
									$categories_pluck = wp_list_pluck( $published_post->categories, 'name','term_id' );
							?>
									<p class="category" >
										<?php echo esc_html( $categories_pluck[ $item->post_category ] ); ?>
									</p>
							<?php endif; ?>
							<a href="<?php echo esc_url( get_permalink( $published_post->ID ) ); ?>" class="title-article-link" id="<?php echo esc_attr( $title_id );?>">
								<h2><?php echo esc_html( $published_post->post_title ); ?></h2>
							</a>
						</div>
					</article>	

	<?php 	
						endif;
			endif; ?>
	<?php 	$position++; 
		endforeach; ?>

	</div> <!--  Close secondary-highlight-content -->	
</section>
<?php endif; ?>