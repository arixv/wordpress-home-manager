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
add_action( 'init', 'homemanager_custom_objects' );

function homemanager_custom_objects() {

	/* Custom Post Type */
	register_post_type( 'home' , array(
        'labels'  => array(
			'name' 					=> _x('Homes','post type general name','homemanager' ),
			'singular_name'			=> _x('Home','post type singular name','homemanager' ),
			'add_new' 				=> _x('Add New Home','celebrity','homemanager' ),
			'add_new_item'			=> __('Add Home','homemanager' ),
			'edit_item'				=> __('Edit Home','homemanager' ),
			'new_item'				=> __('New Home','homemanager' ),
			'view_item'				=> __('View Home','homemanager' ),
			'search_items'			=> __('Search Home','homemanager' ),
			'not_found' 			=> __('No Home found','homemanager' ),
			'not_found_in_trash' 	=> __('No Home found in Trash','homemanager' )
		),
        'public'                => true,
        'show_ui'               => true,
        'query_var' 			=> true,
        'show_in_menu'          => false,
        'capability_type' 		=> 'post',
        'supports'              => array( 'title', 'editor', 'excerpt', 'page-attributes' ),	
        'has_archive'           => true
    ) );

	register_post_type( 'home-group' , array(
        'labels'  => array(
			'name' 					=> _x('Home Group','post type general name','homemanager' ),
			'singular_name'			=> _x('Home Group','post type singular name','homemanager' ),
			'add_new' 				=> _x('Add New Home Group','celebrity','homemanager' ),
			'add_new_item'			=> __('Add Home Group','homemanager' ),
			'edit_item'				=> __('Edit Home Group','homemanager' ),
			'new_item'				=> __('New Home Group','homemanager' ),
			'view_item'				=> __('View Home Group','homemanager' ),
			'search_items'			=> __('Search Home Group','homemanager' ),
			'not_found' 			=> __('No Home Group found','homemanager' ),
			'not_found_in_trash' 	=> __('No Home Group found in Trash','homemanager' )
		),
        'public'                => true,
        'show_ui'               => true,
        'query_var' 			=> true,
        'show_in_menu'          => false,
        'capability_type' 		=> 'post',
        'supports'              => array( 'title', 'editor', 'excerpt', 'page-attributes' ),	
        'has_archive'           => false
    ) );

	register_post_type( 'home-object' , array(
        'labels'  => array(
			'name' 					=> _x('Home Objects','post type general name','homemanager' ),
			'singular_name'			=> _x('Home Objects','post type singular name','homemanager' ),
			'add_new' 				=> _x('Add New Home Object','celebrity','homemanager' ),
			'add_new_item'			=> __('Add Home Object','homemanager' ),
			'edit_item'				=> __('Edit Home Object','homemanager' ),
			'new_item'				=> __('New Home Object','homemanager' ),
			'view_item'				=> __('View Home Object','homemanager' ),
			'search_items'			=> __('Search Home Object','homemanager' ),
			'not_found' 			=> __('No Home Object found','homemanager' ),
			'not_found_in_trash' 	=> __('No Home Object found in Trash','homemanager' )
		),
        'public'                => true,
        'show_ui'               => true,
        'query_var' 			=> true,
        'show_in_menu'          => false,
        'capability_type' 		=> 'post',
        'supports'              => array( 'title', 'editor', 'excerpt', 'page-attributes' ),	
        'has_archive'           => true
    ) );


}

?>