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
 
get_header();
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$is_mobile = jetpack_is_mobile();
?>

<ul class="nav nav-tabs main-tabs visible-xs-block" >
	<li class="active" ><a href="#">Noticias</a></li>
	<li><a href="/ultimas-noticias">Últimas Noticias</a></li>
</ul>

<?php
if ( $is_mobile ) :
	imd_google_ad( array( 'id' => 'div-gpt-ad-320x50-1', 'class' => 'ad_320x50' ) );
else :
?>
	<div id="wrapper-ad" class="hidden-xs" >
		<?php imd_google_ad( array( 'id' => 'div_dfp_5', 'class' => 'ad_728x90' ) ); ?>
	</div>
<?php endif; ?>
		
<div class="page-container">
	<div class="flex-box" >
		<div class="box-cell" >
			<div class="home-content">
				<?php 
				home_display_part( 'highlights', $preview=true );
				if ( $is_mobile ) :
					imd_widget_newsletter( array(
						'form_position' => 'sidebar',
						'form_name' => 'populares',
						'instance'=>3,
					) );
				endif;
				home_display_part( 'secondary', $preview=true );

				if ( $is_mobile ) :
					imd_widget_clasificados();
					imd_google_ad( array( 'id' => 'div_dfp_3', 'class' => 'ad_300x250' ) );
					imd_widget_horoscopo();
				endif;

				imd_home_section( 'deportes' ); 
				imd_home_section( 'inmigracion' ); 
				?>
			</div><!-- // home-content -->
		</div><!-- box-cell -->

		<aside class="box-cell sidebar" >
			<?php get_sidebar(); ?>
		</aside>
	</div><!-- //flex-box-->
</div> <!-- page-container -->
		
<?php get_footer();
