/*
 * Wide Menu - Wordpress Mega Menu Plugin
 * wide_menu_script.js
 * 
 * The main javascript for Wide Menu Control Plugin by Themes Expert
 * 
 * Copyright 2015 Themes Experts All rights reserved
 * http://themesexperts.com
 * 
 */

var wide_menus = new Array();
var wide_menus_parent = new Array();
var wide_menus_parent_li = new Array();
var menu_option = new Array();
var menu_top = -9999;
var max_hight = 0;
var menu_width = 0;
var hoverTimer, outTimer;
var sub_menu_width = 100;
var menu_open_count=0;

jQuery(document).ready(function(){
	
	jQuery('.wide_menu_container').each(function(){
		var wide_menu = jQuery(this).clone();
		var temp_theme_location = '';
		var theme_location = '';
		
		wide_menu.removeClass('wide_menu_container');
		temp_theme_location = wide_menu.attr('class');
		theme_location = temp_theme_location.replace('wide_menu_','');
		theme_location = theme_location.replace('_container','');
		
		wide_menus[theme_location] = theme_location;
		wide_menus_parent[theme_location] = jQuery(this).parent();
		jQuery('body').prepend('<div id="wide_menu_width_'+theme_location+'"></div>');
		jQuery('#wide_menu_width_'+theme_location).prepend(jQuery(this));
	});
	
	jQuery('.wide_menu_sub_menu_div_1').each(function(){
		var lists_div = jQuery(this).parent().find('.wide_menu_lists');
		var images_div = jQuery(this).parent().find('.wide_menu_images');
		if(jQuery(this).hasClass('wide_menu_sub_menu_list')){
			jQuery(this).appendTo(lists_div);
		}
		else{
			jQuery(this).appendTo(images_div);
		}
	});
	i = 1;
	for(value in wide_menus){
		menu_option[value] = eval('wide_menu_option_'+ value);
		jQuery('.wide_menu_'+value+'_container').css('background-color','#'+menu_option[value].nav_menu_primary_color);
		jQuery('.wide_menu_'+value+'_container').css('border-color','#'+menu_option[value].nav_menu_secondary_color);
		jQuery('.wide_menu_sub_menu_div_1 a').css('font-size',menu_option[value].nav_menu_font_l2_size+'px');
		jQuery('.wide_menu_sub_menu_li a').css('font-size',menu_option[value].nav_menu_font_l3_size+'px');
		jQuery('.wide_menu_description').css('font-size',menu_option[value].nav_menu_font_description_size+'px');
		
		jQuery('.wide_menu_sub_menu').each(function(){
			var _this = this;
			if(jQuery(_this).parent().hasClass('wide_menu_sub_menu_image')){
				var li_count = jQuery(_this).children().length;
				var li_width = ((100/li_count)-4)+'%';
				jQuery(_this).children().each(function(){
					var min_image_width = jQuery(this).find('img').width();
					jQuery(this).css('width',li_width);
					if(jQuery(this).width() < min_image_width){
						jQuery(this).css('width',min_image_width);
					}
					jQuery(this).css('min-width',min_image_width);
				});
			}
		});
		jQuery('.wide_menu_lists').css('border-color','#'+menu_option[value].nav_menu_secondary_color);
		jQuery('.wide_menu_sub_menu_div_first').css('border-color','#'+menu_option[value].nav_menu_secondary_color);
		jQuery('.wide_menu_images').css('border-color','#'+menu_option[value].nav_menu_secondary_color);
		jQuery('.wide_menu_sub_menu').css('border-color','#'+menu_option[value].nav_menu_secondary_color);
		jQuery('.wide_menu_sub_menu_div_1').css('border-color','#'+menu_option[value].nav_menu_secondary_color);
		
		var menu = jQuery('#wide_menu_' + value );
		if(typeof(menu_option[value].nav_menu_use_custom_inner_width) != 'undefined' && menu_option[value].nav_menu_use_custom_inner_width == '1' ){
			if(typeof(menu_option[value].nav_menu_use_custom_inner_width_value) != 'undefined'){
				sub_menu_width = parseFloat(menu_option[value].nav_menu_use_custom_inner_width_value);
			}	
		}
		
		var margin = (100 - sub_menu_width)/2;
		menu_width = margin;		
		menu_top = wide_menu_get_menu_top( wide_menus_parent[value] );
		set_animate_width(wide_menus_parent[value],value);
		
		menu.children().each(function(){
			if(jQuery(this).children().length == 0){
				jQuery(this).remove();
			}
			else{
				if(i == 1){
					jQuery(this).addClass('wide_menu_sub_menu_div_first');
				}
				menu_width = menu_width + sub_menu_width;
				i++;
			}	
		});
		
		menu_width = menu_width + margin;
		menu.css('width',menu_width+'%');
		menu.children().css('width',100*sub_menu_width/menu_width+'%');
		menu.find('.wide_menu_sub_menu_div_first').css('margin-left',100*margin/menu_width+'%');
		jQuery('.wide_menu_'+value+'_container').css('top',menu_top+'px');
	};
	
	jQuery('.wide_menu_lists').each(function(){
		var wide_menu_lists_row = jQuery(this).find('.wide_menu_lists_row');
		var _this = this;
		if(jQuery(_this).children().length == 1){
			jQuery(_this).next().addClass('wide_menu_only');
			jQuery(_this).remove();
		}
		else{
			jQuery(_this).height('100%');
			jQuery(this).next().css('border-left','0');
		}
		
		var i = 0;
		jQuery(_this).children().each(function(){
			if(jQuery(this).hasClass('wide_menu_sub_menu_list_row')){
				jQuery(this).appendTo(wide_menu_lists_row);
			}
			else{
				i++;
			}
		});
		
		if(wide_menu_lists_row.children().length == 0){
			i = i-1;
			wide_menu_lists_row.remove();
		}
		
		jQuery(_this).children().each(function(){
			if(jQuery(this).hasClass('wide_menu_sub_menu_list_new_row_custom_width')){
			}
			else{
				jQuery(this).css('width',(100/i)+'%');
			}
		});
		
	});
	jQuery('.wide_menu_images').each(function(){
		if(jQuery(this).children().length == 0){
			jQuery(this).prev().addClass('wide_menu_only');
			jQuery(this).remove();
		}
		else{
			jQuery(this).height('100%');
		}
	});
	
	for(value in wide_menus){
		var menu = jQuery('#wide_menu_' + value );
		menu.children().each(function(){
			if(jQuery(this).outerHeight() > max_hight){
				 max_hight = jQuery(this).outerHeight();
			}
		});
	}
	
	//jQuery('.wide_menu_sub_menu_div').height(max_hight);
	
	jQuery(window).scroll(function (){
		for(value in wide_menus){
			menu_top = wide_menu_get_menu_top( wide_menus_parent[value] );
			jQuery('.wide_menu_'+value+'_container').css('top',menu_top+'px');
		}
	});
	
	jQuery(window).resize(function (){
		for(value in wide_menus){
			set_animate_width(wide_menus_parent[value],value);
		}
	});
	
	
	jQuery('.wide_menu_container').height(0);
	jQuery('.wide_menu_container').hide();

	for(value in wide_menus){
		menu_option[value] = eval('wide_menu_option_'+ value);
		menu_option[value].nav_menu_primary_color;
		jQuery('#wide_menu_' + value).css('top',-max_hight);
		li = wide_menus_parent[value].find("li[class*='menu-item-']:first");
		if(!li){
			li = parent.find("li[id^='menu-item-']:first");
		}
		j = 0;
		k = 0;
		
		li.parent().children().each(function(){
			jQuery(this).find('ul').css('display','none');
			if(jQuery(this).find('ul').length > 0){
				wide_menus_parent_li[k] = j;
				j++;
			}
			else{
				wide_menus_parent_li[k] = -1;
			}
			k++;
		});
		
		i = 0;
		jQuery.each(wide_menus_parent_li,function(n,index){
			if(index != -1){
				dropdown_effect = menu_option[value].nav_menu_dropdown_effect
				dropdown_speed = menu_option[value].nav_menu_dropdown_speed
				move_effect = menu_option[value].nav_menu_move_effect
				move_speed = menu_option[value].nav_menu_move_speed
				hold_time = menu_option[value].nav_menu_hold_time
				wide_menu(li.parent().children().eq(n), jQuery('#wide_menu_' + value).children().eq(index), i, dropdown_effect, dropdown_speed, move_effect, move_speed, hold_time );
				i++;
			}
		});
	
	}
	jQuery('.wide_menu_container').css('opacity',1);	
});


function wide_menu_get_menu_top( parent ){
	var li = parent.find("li[class*='menu-item-']:first");
	if(!li){
		li = parent.find("li[id^='menu-item-']:first");
	}
	if(jQuery("body").css('position') == 'relative' && jQuery("body").hasClass('admin-bar')){
		return li.offset().top + li.outerHeight() - 32 ;
	}
	return li.offset().top + li.outerHeight(); 
}

function wide_menu(parent, sub_menu, index, dropdown_effect, dropdown_speed, move_effect, move_speed, hold_time){
	if (typeof(dropdown_effect)=="undefined"){
		dropdown_effect = 'easeOutQuad';
	}
	if (typeof(dropdown_speed)=="undefined"){
		dropdown_speed = 2000;
	}
	if (typeof(move_effect)=="undefined"){
		move_effect = 'easeOutQuad';
	}
	if (typeof(move_speed)=="undefined"){
		move_speed = 2000;
	}
	if (typeof(hold_time)=="undefined"){
		hold_time = 2000;
	}
    dropdown_s = parseInt(dropdown_speed);
	move_s = parseInt(move_speed);
	hold_t = parseInt(hold_time);

parent.add(".wide_menu").mouseenter(function () { 
    clearTimeout(outTimer); 
    menu_open_count++;
    if (jQuery('.wide_menu').is(':hover')) {
	}
	else{
    hoverTimer = setTimeout(jQuery.proxy(function () { 
	  jQuery('.wide_menu_sub_menu_div').css('opacity',0.2);
	  effect = dropdown_effect;
	  speed = dropdown_s;
	  if(sub_menu.parent().css('top') == '0px'){speed = move_s;effect = move_effect;}
	  sub_menu.css('opacity',1);
	  	sub_menu.parent().stop(true,false).animate({'left':'-'+index*sub_menu_width+'%','top':'0'},{
		step:function(now, fx){			
			  if(fx.prop == 'top'){
				  sub_menu.parent().parent().show();
				  sub_menu.parent().parent().height(sub_menu.height()+now);
			  }		
		},
		duration : speed,
		easing : effect
	  });
     }, this), 100); 
    }
  }).mouseleave(function () { 
    var self = this; 
    menu_open_count--;
    clearTimeout(hoverTimer);  
    outTimer = setTimeout(function () { 
    if(menu_open_count==0){
		jQuery('.wide_menu_sub_menu_div').css('opacity',0.2);
			sub_menu.parent().stop().animate({'top':-max_hight},{
				step:function(now, fx){
				if(fx.prop == 'top')
					sub_menu.parent().parent().height(sub_menu.outerHeight(true)+now);
				},
				done:function(){
					sub_menu.parent().parent().hide();
					clearTimeout(hoverTimer);
				},
				duration : dropdown_s,
				easing : dropdown_effect
			});	
		}
    }, hold_time); 

  }); 

}

function set_animate_width(tag,theme_location){
	if(typeof(menu_option[theme_location].nav_menu_screen) == "undefined"){
		jQuery('#wide_menu_width_'+theme_location).width('100%');
	}
	else{
		if(menu_option[theme_location].nav_menu_screen == 'full'){
			jQuery('#wide_menu_width_'+theme_location).width('100%');
		}
		else{
			if(typeof(menu_option[theme_location].nav_menu_use_custom_width) != "undefined"  && menu_option[theme_location].nav_menu_use_custom_width == '1' && typeof(menu_option[theme_location].nav_menu_use_custom_width_value) != "undefined" && (menu_option[theme_location].nav_menu_use_custom_width_value.indexOf("px") > 0 || menu_option[theme_location].nav_menu_use_custom_width_value.indexOf("%") > 0) ){
				jQuery('#wide_menu_width_'+theme_location).width(menu_option[theme_location].nav_menu_use_custom_width_value);
				jQuery('#wide_menu_width_'+theme_location).css('margin','0 auto');
				jQuery('.wide_menu_'+theme_location+'_container').width(menu_option[theme_location].nav_menu_use_custom_width_value);
			}
			else{
				var body_width_css = jQuery('body').css('width');
				jQuery('body').css('width','4000px');
				var tag_parent_width = tag.parent().width();
				var body_width = jQuery('body').width();
				jQuery('body').css('width','100%');
				if(body_width > tag_parent_width){
					return set_animate_width(tag.parent(),theme_location);
				}
				else{
					jQuery('#wide_menu_width_'+theme_location).width(tag.width());
					jQuery('#wide_menu_width_'+theme_location).css('margin','0 auto');
					jQuery('.wide_menu_'+theme_location+'_container').width(tag.width());
				}
			}
		}
	}	
}