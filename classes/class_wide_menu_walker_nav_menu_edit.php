<?php
/*-----------------------------------------------------------------------------------
Wide Menu - Wordpress Mega Menu Plugin
Wide Menu Navigation Menu Edit Walker
Copyright 2015 Themes Experts All rights reserved
TABLE OF CONTENTS
========================
- __construct()
- Wide_Menu_Walker_Nav_Menu_Edit()

-----------------------------------------------------------------------------------*/
// File Security Check
if ( !defined( 'ABSPATH' ) )
	exit;
require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
class Wide_Menu_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

	function start_lvl( &$output, $depth = 0, $args = array() ) {}

	function end_lvl( &$output, $depth = 0, $args = array() ) {}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
		$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;
		$recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );
		if ( $recently_edited != $nav_menu_selected_id && !is_nav_menu( $nav_menu_selected_id ) ) {
			$nav_menu_selected_id = $recently_edited;
		}

		$wide_menu_settings = get_option( 'wide_menu_settings' );
		$wide_menu_options = $wide_menu_settings[$nav_menu_selected_id];
		$wide_menu_thumbnail_id = get_post_thumbnail_id( $item->ID );
		$wide_menu_image_src = wp_get_attachment_image_src( $wide_menu_thumbnail_id, 'full' );
		$wide_menu_image_url = $wide_menu_image_src[0];
		$wide_menu_sub_menu_style = get_post_meta( $item->ID, '_wide_menu_sub_menu_style', true );
		$wide_menu_sub_menu_parent_style = get_post_meta( $item->menu_item_parent, '_wide_menu_sub_menu_style', true );
		$wide_menu_sub_menu_parent_image_size = get_post_meta( $item->menu_item_parent, '_wide_menu_sub_menu_image_size', true );


		if ( $wide_menu_sub_menu_parent_style && $wide_menu_sub_menu_parent_style != 'wide_menu_sub_menu_list' ) {
			update_post_meta( $item->ID, '_wide_menu_featured_image_size', str_replace( 'wide_menu_sub_menu_image_size_', 'wide_menu_featured_image_size_', $wide_menu_sub_menu_parent_image_size ) );
			$disabled = ' disabled="disabled"';
		}
		else {
			$disabled = '';
		}

		$wide_menu_featured_image_size = get_post_meta( $item->ID, '_wide_menu_featured_image_size', true );
		$wide_menu_sub_menu_image_size = get_post_meta( $item->ID, '_wide_menu_sub_menu_image_size', true );
		$wide_menu_hide_label = get_post_meta( $item->ID, '_wide_menu_hide_label', true );
		$wide_menu_new_row = get_post_meta( $item->ID, '_wide_menu_new_row', true );
		$wide_menu_new_row_width = get_post_meta( $item->ID, '_wide_menu_new_row_width', true );
		$wide_menu_new_row_width_value = get_post_meta( $item->ID, '_wide_menu_new_row_width_value', true );
		$wide_menu_sub_menu_align = get_post_meta( $item->ID, '_wide_menu_sub_menu_align', true );
		$wide_menu_sub_menu_separator = get_post_meta( $item->ID, '_wide_menu_sub_menu_separator', true );
		$wide_menu_round_on = get_post_meta( $item->ID, '_wide_menu_round_on', true );
		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);


		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = get_the_title( $original_object->ID );
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
		);

		$title = $item->title;
		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			$title = sprintf( __( '%s (Pending)' ), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;
		$submenu_text = '';
		if ( 0 == $depth )
			$submenu_text = 'style="display: none;"';
?>

<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
  <dl class="menu-item-bar">
    <dt class="menu-item-handle"> <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>>
      <?php _e( 'sub item' ); ?>
      </span></span> <span class="item-controls"> <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span> <span class="item-order hide-if-js"> <a href="<?php
		echo wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'move-up-menu-item',
					'menu-item' => $item_id,
				),
				remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
			),
			'move-menu_item'
		);

		?>" class="item-move-up"><abbr title="<?php esc_attr_e( 'Move up' ); ?>">&#8593;</abbr></a> | <a href="<?php
		echo wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'move-down-menu-item',
					'menu-item' => $item_id,
				),
				remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
			),
			'move-menu_item'
		);
		?>" class="item-move-down"><abbr title="<?php esc_attr_e( 'Move down' ); ?>">&#8595;</abbr></a> </span> <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e( 'Edit Menu Item' ); ?>" href="<?php
		echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );

		?>">

      <?php _e( 'Edit Menu Item' ); ?>

      </a> </span> </dt>
  </dl>

  <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
    <?php if ( 'custom' == $item->type ) : ?>
    <p class="field-url description description-wide">
      <label for="edit-menu-item-url-<?php echo $item_id; ?>">
        <?php _e( 'URL' ); ?>
        <br />
        <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
      </label>
    </p>
    <?php endif; ?>

    <p class="description description-thin">
      <label for="edit-menu-item-title-<?php echo $item_id; ?>">
        <?php _e( 'Navigation Label' ); ?>
        <br />
        <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
      </label>
    </p>

    <p class="description description-thin">
      <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
        <?php _e( 'Title Attribute' ); ?>
        <br />
        <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
      </label>
    </p>

    <p class="field-link-target description">
      <label for="edit-menu-item-target-<?php echo $item_id; ?>">
        <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
        <?php _e( 'Open link in a new window/tab' ); ?>
      </label>
    </p>
    <p class="field-css-classes description description-thin">
      <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
        <?php _e( 'CSS Classes (optional)' ); ?>
        <br />
        <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode( ' ', $item->classes ) ); ?>" />
      </label>
    </p>
    <p class="field-xfn description description-thin">
      <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
        <?php _e( 'Link Relationship (XFN)' ); ?>
        <br />
        <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
      </label>
    </p>
    <p class="field-wide_menu description description-wide">
      <label>
        <input type="checkbox" name="wide_menu_hide_label[<?php echo $item_id; ?>]" value="1" <?php echo ( $wide_menu_hide_label == '1' ) ? ' checked="checked" ' : ''; ?> />
        Hide label</label>
    </p>
    <p class="field-wide_menu description description-wide break_line">
      <?php
		wp_enqueue_media();
	  ?>
      <script type="text/javascript">
						jQuery(document).ready(function(){
							var wide_menu_upload_frame;
							var wide_menu_value_id;
							jQuery('#wide_menu_featured_image_choose_link_<?php echo $item_id; ?>').live('click',function(event){
								wide_menu_value_id =jQuery( this ).attr('id');
								event.preventDefault();
								if( wide_menu_upload_frame ){
									wide_menu_upload_frame.open();
									return;
								}
								wide_menu_upload_frame = wp.media({
									title: 'Insert image',
									button: {
										text: 'Insert',
									},
									multiple: false
								});
								wide_menu_upload_frame.on('select',function(){
									attachment = wide_menu_upload_frame.state().get('selection').first().toJSON();
									jQuery('#wide_menu_featured_image_id_<?php echo $item_id; ?>').val(attachment.id);
									jQuery('#wide_menu_featured_image_preview_<?php echo $item_id; ?>').attr("src",attachment.url);
									jQuery('#wide_menu_featured_image_preview_<?php echo $item_id; ?>').show();
								});
								wide_menu_upload_frame.open();
							});
						});
                    </script>

                    <style>.break_line{border-bottom:1px solid #E9E9E9;padding-bottom:10px;}</style>
             <span style="display:block;padding-bottom:5px;font-style:normal;color:#000;"><strong>Menu item featured image</strong></span>
      <label>
        <input type="hidden" id="wide_menu_featured_image_id_<?php echo $item_id; ?>" name="wide_menu_featured_image_id[<?php echo $item_id; ?>]" value="<?php echo $wide_menu_thumbnail_id; ?>" />
        <?php if ( empty( $wide_menu_image_url ) ) {
			$image_display = 'display:none;';
		}
?>
        <img id="wide_menu_featured_image_preview_<?php echo $item_id; ?>" style="padding-bottom:10px;display: block;clear: both;max-width:70%;<?php echo $image_display;?>" src="<?php echo $wide_menu_image_url; ?>"/>
        <a id="wide_menu_featured_image_choose_link_<?php echo $item_id; ?>" class="button">Choose Image</a> <span>Upload or choose an image</span> </label>
     <span style="display:block;padding-top:10px;">
      <label>Size
        <select name="wide_menu_featured_image_size[<?php echo $item_id; ?>]"<?php echo $disabled; ?>>
          <option value="wide_menu_featured_image_no">Do not show the image</option>
          <?php
		if ( count( $wide_menu_options['image_size'] )>0 ) {
			foreach ( $wide_menu_options['image_size'] as $key => $value ) {
				$selected = ( $wide_menu_featured_image_size == 'wide_menu_featured_image_size_'.$key ) ? ' selected="selected"' : '';
?>
          <option value="<?php echo 'wide_menu_featured_image_size_'.$key ?>"<?php echo $selected; ?>><?php echo ucfirst( $value['image_size_name'].' ('.$value['image_size_width'].'x'.$value['image_size_height'].')' ); ?></option>
          <?php
			}
		}
?>
        </select>
      </label>
      	  <?php
		if ( $wide_menu_round_on == '1' ) {
			$checked = ' checked="checked"';
		}
		else {
			$checked = '';
		}
?>
      <label>
        <input type="checkbox" name="wide_menu_round_on[<?php echo $item_id; ?>]" id="wide_menu_round_on_[<?php echo $item_id; ?>]" value="1" <?php echo $checked;?> />
        Image round on</label>
      </span>
    </p>
    <?php if ( $depth == 1 ) {?>
    <p class="field-wide_menu description description-wide break_line">
    <span style="display:block;clear:both;padding-bottom:5px;font-style:normal;color:#000;"><strong>Show this sub-menu as</strong></span>
      <?php
			if ( $wide_menu_sub_menu_style == 'wide_menu_sub_menu_image' ) {
				$wide_menu_sub_menu_image = 'checked="checked"';
				$wide_menu_sub_menu_list="";
			}
			else {
				$wide_menu_sub_menu_list = 'checked="checked"';
				$wide_menu_sub_menu_image="";
			}
?>
      <span style="display:block;clear:both;padding-bottom:5px;">
      <label>
        <input type="radio" name="wide_menu_sub_menu_style[<?php echo $item_id; ?>]" id="wide_menu_sub_menu_list_<?php echo $item_id; ?>" value="wide_menu_sub_menu_list" <?php echo $wide_menu_sub_menu_list;?> />
        List </label>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <label>
        <input type="checkbox" name="wide_menu_new_row[<?php echo $item_id; ?>]" value="1" <?php echo ( $wide_menu_new_row == '1' ) ? ' checked="checked" ' : ''; ?> />
        Show menu in new list</label>
      </span>
       <span style="display:block;clear:both;padding-bottom:5px;">
      <label>
        <input type="checkbox" name="wide_menu_new_row_width[<?php echo $item_id; ?>]" value="1" <?php echo ( $wide_menu_new_row_width == '1' ) ? ' checked="checked" ' : ''; ?> />
        Use custom list width</label>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <label>
        <input type="number" name="wide_menu_new_row_width_value[<?php echo $item_id; ?>]" value="<?php echo $wide_menu_new_row_width_value; ?>" />
        &nbsp;&nbsp;%</label></span>
      <label>
        <input type="radio" name="wide_menu_sub_menu_style[<?php echo $item_id; ?>]" id="wide_menu_sub_menu_image_<?php echo $item_id; ?>" value="wide_menu_sub_menu_image" <?php echo $wide_menu_sub_menu_image;?> />
        Image </label>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <label>Sub-menu image size
        <select name="wide_menu_sub_menu_image_size[<?php echo $item_id; ?>]"<?php echo $disabled; ?>>
          <?php
			if ( count( $wide_menu_options['image_size'] )>0 ) {
				foreach ( $wide_menu_options['image_size'] as $key => $value ) {
					$selected = ( $wide_menu_sub_menu_image_size == 'wide_menu_sub_menu_image_size_'.$key ) ? ' selected="selected"' : '';
		?>
          <option value="<?php echo 'wide_menu_sub_menu_image_size_'.$key ?>"<?php echo $selected; ?>><?php echo ucfirst( $value['image_size_name'].' ('.$value['image_size_width'].'x'.$value['image_size_height'].')' ); ?></option>
          <?php
				}
		}
?>
        </select>
      </label>
      <?php /*?><label>
        <input type="checkbox" name="wide_menu_menu_separator[<?php echo $item_id; ?>]" value="1" <?php echo ($wide_menu_menu_separator == '1') ? ' checked="checked" ' : ''; ?> />
         Show Separator</label><?php */?>
      <label>
    </p>
    <p class="field-wide_menu description description-wide">
    <span style="display:block;clear:both;padding-bottom:5px;font-style:normal;color:#000;"> <strong>Menu align as</strong></span>
      	<?php
      		$wide_menu_sub_menu_align_right="";
      		$wide_menu_sub_menu_align_center="";
      		$wide_menu_sub_menu_align_left="";
      		$wide_menu_sub_menu_align_separator="";
			if ( $wide_menu_sub_menu_align == 'right' ) {
				$wide_menu_sub_menu_align_right = 'checked="checked"';
			}
			elseif ( $wide_menu_sub_menu_align == 'center' ) {
				$wide_menu_sub_menu_align_center = 'checked="checked"';
			}
			else {
				$wide_menu_sub_menu_align_left = 'checked="checked"';
			}
			if ( $wide_menu_sub_menu_separator == 'separator' ) {
				$wide_menu_sub_menu_align_separator = 'checked="checked"';
			}
		?>

      <label>
        <input type="radio" name="wide_menu_sub_menu_align[<?php echo $item_id; ?>]" id="wide_menu_sub_menu_align_left_<?php echo $item_id; ?>" value="left" <?php echo $wide_menu_sub_menu_align_left;?> />
        Left </label>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <label>
        <input type="radio" name="wide_menu_sub_menu_align[<?php echo $item_id; ?>]" id="wide_menu_sub_menu_align_right_<?php echo $item_id; ?>" value="right" <?php echo $wide_menu_sub_menu_align_right;?> />
        Right </label>
      &nbsp;&nbsp;&nbsp;&nbsp;
      <label>
        <input type="radio" name="wide_menu_sub_menu_align[<?php echo $item_id; ?>]" id="wide_menu_sub_menu_align_center_<?php echo $item_id; ?>" value="center" <?php echo $wide_menu_sub_menu_align_center;?> />
        Center </label>
      <span style="display:block;clear:both;padding-top:10px;">
         <label>
         <input type="checkbox" name="wide_menu_sub_menu_separator[<?php echo $item_id; ?>]" id="wide_menu_sub_menu_align_separator_<?php echo $item_id; ?>" value="separator" <?php echo $wide_menu_sub_menu_align_separator;?> />
        Show separator</label>
        </span>
    </p>
    <?php }
    ?>
    <p class="field-description description description-wide">
      <label for="edit-menu-item-description-<?php echo $item_id; ?>">
        <?php _e( 'Description' ); ?>
        <br />
        <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
        <span class="description">
        <?php _e( 'The description will be displayed in the menu if the current theme supports it.' ); ?>
        </span> </label>
    </p>
    <p class="field-move hide-if-no-js description description-wide">
      <label> <span>
        <?php _e( 'Move' ); ?>
        </span> <a href="#" class="menus-move-up">
        <?php _e( 'Up one' ); ?>
        </a> <a href="#" class="menus-move-down">
        <?php _e( 'Down one' ); ?>
        </a> <a href="#" class="menus-move-left"></a> <a href="#" class="menus-move-right"></a> <a href="#" class="menus-move-top">
        <?php _e( 'To the top' ); ?>
        </a> </label>
    </p>
    <div class="menu-item-actions description-wide submitbox">
      <?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
<p class="link-to-original"> <?php printf( __( 'Original: %s' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?> </p>
      <?php endif; ?>
      <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
		echo wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'delete-menu-item',
					'menu-item' => $item_id,
				),
				admin_url( 'nav-menus.php' )
			),
			'delete-menu_item_' . $item_id
		); ?>">
      <?php _e( 'Remove' ); ?>
      </a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
		?>#menu-item-settings-<?php echo $item_id; ?>">
      <?php _e( 'Cancel' ); ?>
      </a> </div>
    <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
    <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
    <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
    <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
    <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
    <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
  </div>
  <ul class="menu-item-transport">
  </ul>
  <?php
		$output .= ob_get_clean();
	}
}