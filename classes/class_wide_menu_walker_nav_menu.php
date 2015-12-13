<?php
/*-----------------------------------------------------------------------------------
Wide Menu - Wordpress Mega Menu Plugin
Wide Menu Navigation Menu Edit Walker
Copyright 2015 Themes Experts All rights reserved
TABLE OF CONTENTS
========================
- __construct()
- Wide_Menu_Walker_Nav_Menu()
- end_lvl
- start_el
- end_el
-----------------------------------------------------------------------------------*/
// File Security Check
if ( !defined( 'ABSPATH' ) )
	exit;
class Wide_Menu_Walker_Nav_Menu extends Walker_Nav_Menu {

	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );

		if ( $depth == 0 ) {
			$wide_menu_settings = get_option( 'wide_menu_settings' );
			$locations = get_nav_menu_locations();
			$menu_id = $locations[$args->theme_location];
			$wide_menu_options = $wide_menu_settings[$menu_id];

			if ( is_numeric( $wide_menu_options['nav_menu_lists_width'] ) ) {
				$lists_width = 'style="width:'.$wide_menu_options['nav_menu_lists_width'].'%"';
				$images_width = 'style="width:'.( 100-$wide_menu_options['nav_menu_lists_width'] ).'%"';
			}
			else {
				$lists_width = '';
			}

			$output .= "\n$indent<div class=\"wide_menu_sub_menu_div\">\n";
			$output .= "\n$indent<div class=\"wide_menu_lists\" ".$lists_width." ><div class=\"wide_menu_lists_row\"></div></div>\n";
			$output .= "\n$indent<div class=\"wide_menu_images\" ".$images_width." ></div>\n";
		}
		else {
			$output .= "\n$indent<ul class=\"wide_menu_sub_menu\">\n";
		}

	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		if ( $depth == 0 ) {
			$output .= "\n$indent</div>\n";
		}
		else {
			$output .= "$indent</ul>\n";
		}
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$wide_menu_settings = get_option( 'wide_menu_settings' );
		$locations = get_nav_menu_locations();
		$menu_id = $locations[$args->theme_location];
		$wide_menu_options = $wide_menu_settings[$menu_id];
		$wide_menu_sub_menu_style = get_post_meta( $item->ID, '_wide_menu_sub_menu_style', true );
		$wide_menu_featured_image_size = get_post_meta( $item->ID, '_wide_menu_featured_image_size', true );
		$wide_menu_sub_menu_align = get_post_meta( $item->ID, '_wide_menu_sub_menu_align', true );
		$wide_menu_sub_menu_separator = get_post_meta( $item->ID, '_wide_menu_sub_menu_separator', true );
		$wide_menu_thumbnail_id = get_post_thumbnail_id( $item->ID );

		$wide_menu_image_src = wp_get_attachment_image_src( $wide_menu_thumbnail_id, 'full' );
		$wide_menu_image_url = $wide_menu_image_src[0];		
		$wide_menu_featured_image_size=str_replace( 'wide_menu_featured_image_size_', '', $wide_menu_featured_image_size );
		
		$wide_menu_image_width="";
		$wide_menu_image_height="";
	
		if ( $wide_menu_featured_image_size!="wide_menu_featured_image_no" && $wide_menu_featured_image_size!="") {
			$wide_menu_image_width = $wide_menu_settings[$menu_id]['image_size'][$wide_menu_featured_image_size]['image_size_width'];
			$wide_menu_image_height = $wide_menu_settings[$menu_id]['image_size'][$wide_menu_featured_image_size]['image_size_height'];
		}
		
		$wide_menu_hide_label = get_post_meta( $item->ID, '_wide_menu_hide_label', true );
		$wide_menu_new_row = get_post_meta( $item->ID, '_wide_menu_new_row', true );
		$wide_menu_new_row_width = get_post_meta( $item->ID, '_wide_menu_new_row_width', true );
		$wide_menu_new_row_width_value = get_post_meta( $item->ID, '_wide_menu_new_row_width_value', true );
		$wide_menu_round_on = get_post_meta( $item->ID, '_wide_menu_round_on', true );
		$class_names = '';
		$width_str = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		if ( 3 > $depth && $depth > 0 ) {
			unset( $classes );
			if ( $depth == 1 ) {
				$classes[] = 'wide_menu_sub_menu_div_1';
				if ( $wide_menu_sub_menu_align ) {
					$classes[] = 'wide_menu_sub_menu_'.$wide_menu_sub_menu_align;
				};
				if ( $wide_menu_sub_menu_separator ) {
					$classes[] = 'wide_menu_sub_menu_'.$wide_menu_sub_menu_separator;
				}
			}
			else {
				$classes[] = 'wide_menu_sub_menu_li';
			}
		}

		if ( $wide_menu_sub_menu_style ) {
			$classes[] = $wide_menu_sub_menu_style;
		}

		if ( $wide_menu_new_row == '1' ) {
			$classes[] = 'wide_menu_sub_menu_list_new_row';
			if ( $wide_menu_new_row_width == '1' ) {
				$classes[] = 'wide_menu_sub_menu_list_new_row_custom_width';
				$width_str = 'style="width:'.$wide_menu_new_row_width_value.'%;" ';
			}
		}
		else {
			$classes[] = 'wide_menu_sub_menu_list_row';
		}
		if ( $wide_menu_sub_menu_separator== '1' ) {
			$classes[] = 'menu-spt';
		}else {
			//$classes[] = 'menu-spt';
		}

		$classes[] = 'menu-item-' . $item->ID;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		if ( 3 > $depth && $depth > 0 ) {
			if ( $depth == 1 ) {
				$output .= $indent . '<div' . $id . $class_names .$width_str.'>';
			}
			else {
				$output .= $indent . '<li' . $id . $class_names .'>';
			}
		}

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );
		$attributes = '';

		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		$item_output .= '<a '. $attributes .'>';

		if ( $wide_menu_image_url && $wide_menu_featured_image_size != 'wide_menu_featured_image_no' ) {
			if ( $wide_menu_round_on == '1' ) {
				$round_on = ' class="img_round" ';
			}
			else {
				$round_on = '';
			}
			$item_output .= '<img '.$round_on.' width="'.$wide_menu_image_width.'" height="'.$wide_menu_image_height.'" src="'.$wide_menu_image_url.'" /><br />';
		}

		if ( $wide_menu_hide_label == '1' ) {
			$item_output .= $args->link_before . $args->link_after;
		}
		else {
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		}

		$item_output .= '</a>';
		if ( $depth == 1 && $wide_menu_hide_label != '1' ) {
			$item_output .= '<br />';
		}
		if ( $item->description ) {
			$item_output .= '<div class="wide_menu_description">'.$item->description.'</div>';
		}

		$item_output .= $args->after;
		if ( 3 > $depth && $depth > 0 ) {
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		if ( 3 > $depth && $depth > 0 ) {
			if ( $depth == 1 ) {
				$output .= "</div>\n";
			}
			else {
				$output .= "</li>\n";
			}
		}
	}
}
?>