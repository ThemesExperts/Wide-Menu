<?php
/*
 * Plugin Name: Wide Menu - Wordpress Mega Menu Plugin
 * Plugin URI: http://themesexperts.com/widemenu/
 * Description: Wide Menu - Wordpress Mega Menu Plugin animated, jquery, full and fix screen box, speed control, custom css, font sizing, feature image, round and box image upload, title and description block.
 * Author: Themes Experts
 * Author URI: http://themesexperts.com/
 * Version: 1.0 
 * Copyright: © 2015 Themes Experts
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
class Wide_Menu{
	function __construct(){

		if(is_admin()) {
			add_action('admin_menu', array($this, 'wide_menu_admin_setting_ui_init') );
		}

		add_action('init', array($this, 'wide_menu_featured_image_init') );
		add_filter('manage_nav-menus_columns', array($this, 'wide_menu_nav_menu_manage_columns'), 11 );
		add_filter('wp_edit_nav_menu_walker', array($this, 'wide_menu_nav_edit_walker'),10,2 );
		add_action('save_post', array($this, 'wide_menu_save_post_action'), 10, 2);
		add_filter('wp_nav_menu', array($this, 'wide_menu_nav_menu'),1,2);
		add_action('wp_enqueue_scripts', array($this, 'wide_menu_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'wide_menu_admin_scripts'));
		remove_filter('nav_menu_description', 'strip_tags');
	}


	function wide_menu_admin_setting_ui_init(){	
		add_theme_page('Wide Menu', 'Wide Menu', 'edit_theme_options', 'wide_menu', array($this, 'wide_menu_admin_setting_ui') );
	}

	function wide_menu_admin_setting_ui(){
		include 'admin/class_wide_menu_admin_settings.php';			
		$adminui = new Class_Wide_Menu_Admin_Settings();
		$adminui->wide_menu_admin_setting_ui();
		$adminui->wide_menu_admin_setting_ui_content();
	}

	function wide_menu_featured_image_init(){
		add_post_type_support( 'nav_menu_item', array( 'thumbnail' ) );
	}

	function wide_menu_nav_menu_manage_columns( $columns ) {
		return $columns + array('wide_menu' => 'Wide menu' );
	}

	function wide_menu_nav_edit_walker(){
		include_once 'classes/class_wide_menu_walker_nav_menu_edit.php';			
		return 'Wide_Menu_Walker_Nav_Menu_Edit';		
	}

	function wide_menu_save_post_action( $post_id, $post ) {
		$wide_menu_settings = array(
			'wide_menu_hide_label',
			'wide_menu_new_row',
			'wide_menu_new_row_width',
			'wide_menu_new_row_width_value',
			'wide_menu_featured_image_id',
			'wide_menu_featured_image_size',
			'wide_menu_sub_menu_style',
			'wide_menu_sub_menu_image_size',
			'wide_menu_sub_menu_align',
			'wide_menu_sub_menu_separator',
			'wide_menu_round_on'
		);

		foreach ( $wide_menu_settings as $setting_name ){
			if (isset($_POST[$setting_name][$post_id] ) && ! empty($_POST[$setting_name][$post_id] ) ){
				if( $setting_name == 'wide_menu_featured_image_id' ){
					$attachment_id = $_POST[$setting_name][$post_id];
					set_post_thumbnail($post, $attachment_id );
				}
				update_post_meta($post_id, "_$setting_name", esc_sql($_POST[$setting_name][$post_id] ) );
			}
			else{
				delete_post_meta($post_id, "_$setting_name" );
			}
		}
	}

	function wide_menu_nav_menu($nav_menu, $args ){
		$locations = get_nav_menu_locations();
		$menu_id = $locations[$args->theme_location];
		$wide_menu_settings = get_option( 'wide_menu_settings' );
		$wide_menu_options = $wide_menu_settings[$menu_id];
		$wide_menu = '';

		if($wide_menu_options['nav_menu_use_animate'] ){
			$wide_menu_args = (array)$args;
			$wide_menu_args['container'] = 'div';
			$wide_menu_args['wide_menu_options'] = $wide_menu_options;
			$wide_menu_args['echo'] = true;
			$wide_menu_args['container_class'] = 'wide_menu_'.str_replace('-','_',$args->theme_location).'_container wide_menu_container';			

			include_once 'classes/class_wide_menu_walker_nav_menu.php';			
			$wide_menu_args['walker'] = new Wide_Menu_Walker_Nav_Menu();

			$wide_menu_args['menu_id'] = 'wide_menu_'.str_replace('-','_',$args->theme_location);
			if($wide_menu_options['nav_menu_custom_css']){
				$wide_menu_args['items_wrap'] = '<style type="text/css">'.$wide_menu_options['nav_menu_custom_css'].'</style>';
			}
			else{
				$wide_menu_args['items_wrap'] = '';
			}
			$wide_menu_args['items_wrap'] .= '<div class="wide_menu" id="%1$s">%3$s</div>';
			if($wide_menu_args['walker'] != $args->walker){
				$wide_menu = wp_nav_menu($wide_menu_args);
			}		
		}
		return $nav_menu;
	}

	function wide_menu_admin_scripts(){
		wp_enqueue_script( 'wide_menu_jquery_ui', plugin_dir_url( __FILE__ ) . 'easing/jquery-ui.js', array( 'jquery' ) );
		wp_enqueue_script( 'wide_menu_easing', plugin_dir_url( __FILE__ ) . 'easing/jquery.easing.1.3.js', array( 'wide_menu_jquery_ui' ) );
	}

	function wide_menu_scripts(){
		$locations = get_nav_menu_locations();
		$wide_menu_settings = get_option('wide_menu_settings');
		//var_dump($wide_menu_settings);
		foreach($locations as $key => $value){
			foreach($wide_menu_settings as $wide_menu_option){
			if($value == $wide_menu_option['nav_menu_id']){
				$wide_menu_options[$key] = $wide_menu_option;
			}
		}
	}

		wp_enqueue_script( 'wide_menu_script', plugin_dir_url( __FILE__ ) . 'wide_menu_script.js', array( 'jquery' ) );
		wp_enqueue_script( 'wide_menu_jquery_ui', plugin_dir_url( __FILE__ ) . 'easing/jquery-ui.js', array( 'jquery' ) );
		wp_enqueue_script( 'wide_menu_easing', plugin_dir_url( __FILE__ ) . 'easing/jquery.easing.1.3.js', array( 'wide_menu_jquery_ui' ) );
		if(isset($wide_menu_options)){
			foreach($wide_menu_options as $key => $value){
				wp_localize_script( 'wide_menu_script', 'wide_menu_option_'.str_replace('-','_',$key), $value );
			}
		}
		wp_enqueue_style( 'wide_menu_style', plugin_dir_url( __FILE__ ) . 'css/wide_menu_style.css' );
	}
}
$global_wide_menu = new Wide_Menu();
?>