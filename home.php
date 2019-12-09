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

class Home {
	
	static $published_key = 'home_published_objects';
	
	public static function get_config_cdn() {
		$config = array(
			'username'	=>'mchretien',
			'api_key'	=>'ee3d1f5a88561cf9070b0857a63c1e44',
			'container'	=>'IMDCMS-CF01',
			'root_path' => WP_CONTENT_DIR
		);
		return $config;
	}

	public static function get_list() {
		$query = new WP_Query( $params = array(
			'post_type' => 'home',
		) );
		$list = array();
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				array_push( $list, $query->post );
			endwhile;
			return $list;
		else :
			return false;
		endif;
	}

	public static function getById( $home_id ) {
		$home = get_post( $home_id );
		if ( ! empty( $home ) ) {
			$home->site_id = get_post_meta( $home_id,'site_id', 1 );
			$home->groups = self::get_groups( $home_id );
			return $home;
		}else{
			return false;
		}
	}

	public static function get_default() {
		$params = array(
			'post_type' => 'home',
			'posts_per_page' => 1,
		);
		$custom_query = new WP_Query( $params );

		if ( $custom_query->have_posts() ):
			return $custom_query->posts[0];
		else :
			return false;
		endif;
	}

	public static function get_groups( $home_id = false, $debug=false) {
		
		$params = array(
			'post_type' => 'home-group',
			'orderby' => 'order',
			'order' => 'ASC'
		);

		if ( $home_id !== false) {
			$params['meta_key'] = 'home_id';
			$params['meta_value'] = $home_id;
		}

		$custom_query = new WP_Query( $params);
		$groups = array();

		if ( $custom_query->have_posts()) :
			while ( $custom_query->have_posts()) :
				$custom_query->the_post();
				$group = $custom_query->post;
				$group->group_tag = get_post_meta( $group->ID,'group_tag',1);
				array_push( $groups, $group);
			endwhile;
			wp_reset_postdata();
			return $groups;
		else :
			return false;
		endif;

	}

	public static function get_by_site_id( $site_id = false ) {

		if ( false === $site_id ) :
			return false;
		endif;

		$params = array(
			'post_type' => 'home',
			'meta_key' => 'site_id',
			'meta_value' => $site_id,
			'meta_compare' => '=',
		);
		$custom_query = new WP_Query( $params );

		if ( $custom_query->have_posts() ):
			return $custom_query->posts[0];
		else :
			return false;
		endif;
	}

	public static function get_home_object_single( $home_object_id ) {
		$params = array(
			'post_type'=> 'home-object',
			'p' => $home_object_id,
		);
		
		$custom_query = new WP_Query( $params );
		
		if ( $custom_query->have_posts() ) :
			$object = $custom_query->posts[0];
			$object->home_id = get_post_meta( $object->ID, 'home_id', $single = 1 );
			$object->home_group_id = get_post_meta( $object->ID, 'home_group_id', $single = 1 );
			$object->published_id = get_post_meta( $object->ID, 'published_id', $single = 1 );
			$object->draft_id = get_post_meta( $object->ID, 'draft_id', $single = 1 );
			$object->post_category = get_post_meta( $object->ID, 'post_category', $single = 1 );
			$object->css_class_name = get_post_meta( $object->ID, 'css_class_name', $single = 1 );
			$object->draft_post = self::get_draft_object( $object->draft_id );
			unset( $object->post_content );
			return $custom_query->posts[0];
		else :
			return false;
		endif;
	}

	public static function get_home_objects( $home_id ) {

			$params = array(
				'post_type'=> 'home-object',
				'orderby' => 'order',
				'order' => 'ASC',
				'meta_key' => 'home_id',
				'nopaging' => true,
				'meta_value' => $home_id
			);
			$custom_query = new WP_Query( $params );
			$objects = array();
			$draft_id = '';
			$published_id = '';

			if ( $custom_query->have_posts() ) :
			
				while ( $custom_query->have_posts()) :
					$custom_query->the_post();
					$object = $custom_query->post;
					$object->post_category = get_post_meta( $object->ID, 'post_category', $single = 1 );
					$object->post_category_name = ( 0 !== $object->post_category && '0' !== $object->post_category && '' !== $object->post_category ) ? get_the_category_by_ID( $object->post_category ) : '';
					$object->home_group_id = get_post_meta( $object->ID, 'home_group_id', $single = 1 );
					$object->published_id = get_post_meta( $object->ID, 'published_id', $single = 1 );
					$object->draft_id = get_post_meta( $object->ID, 'draft_id', $single = 1 );
					$object->css_class_name = get_post_meta( $object->ID, 'css_class_name', $single = 1 );
					unset( $object->post_content );
					array_push( $objects, $object );
				endwhile;

				foreach ( $objects as $object) {
					if ( is_numeric( $object->draft_id ) ) :
						$object->draft_post = self::get_draft_object( $object->draft_id );
					endif;

					if ( is_numeric( $object->published_id ) ) :
						$object->published_post = self::get_published_object( $object->published_id );
					endif;
				}
				return $objects;
			else :
				return false;
			endif;
	}
	
	public static function get_draft_object( $draft_id ) {

		if ( 0 !== $draft_id && '0' !== $draft_id ) :
			$draft_post = get_post( $draft_id );
			unset( $draft_post->post_content );
			$thumb_id = get_post_thumbnail_id( $draft_post->ID );
			$draft_post->post_image = wp_get_attachment_image_src( $thumb_id, 'medium' );
			$draft_post->categories = get_the_category( $draft_id );
			return $draft_post;
		else : 
			return false;
		endif;
	}

	public static function get_published_object( $published_id ) {

		if ( '0' !== $published_id && 0 !== $published_id ) :
			$published_post = get_post( $published_id );
			unset( $published_post->post_content );
			$thumb_id = get_post_thumbnail_id( $published_id );
			if ( '' !== $thumb_id ) :
				$published_post->post_image_thumb = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
				$published_post->post_image_medium = wp_get_attachment_image_src( $thumb_id, 'medium' );
				$published_post->post_image_large = wp_get_attachment_image_src( $thumb_id, 'large' );
			endif;
		
			$published_post->media_type = get_post_meta( $published_id,'post_media_type', 1 );
			$published_post->categories = get_the_category( $published_id );		
			return $published_post;
		else :
			return false;
		endif;
	}


	public static function Publish( $home_id, $debug = false ) {

		$objects = self::get_home_objects( $home_id );
		foreach ( $objects as $object) {
			update_post_meta( $object->ID, 'published_id', $object->draft_id );
		}
		// Create json.
		$home_json = self::create_json( $home_id, $debug );

		// Update all transients.
		$published_key = 'home_published_objects';
		delete_transient( $published_key );
		//set_transient( $published_key, $home_json, $expiration = 0 );
		echo 1;
	}

	public static function create_json( $home_id, $debug = false ) {
		$home = Home::get_default();
		$home->groups = self::get_groups( $home_id );
		$home_objects = Home::get_home_objects( $home_id );
		foreach ( $home_objects as $key2 => $obj ) {
				unset( $home_objects[ $key2 ]->draft_id );
				unset( $home_objects[ $key2 ]->draft_post );
		}
		foreach ( $home->groups as $key => $group ) {
			$home->groups[$key]->objects = array();
			foreach ( $home_objects as $home_object ) {
				if ( (int) $home_object->home_group_id === (int) $group->ID ) {
					array_push( $home->groups[ $key ]->objects, $home_object );
				}
			}
		}
		return json_encode( $home );
	}

}
