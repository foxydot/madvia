<?php
add_shortcode('button','msd_child_button_function');
function msd_child_button_function($atts, $content = null){	
	extract( shortcode_atts( array(
      'url' => null,
	  'target' => '_self'
      ), $atts ) );
	$ret = '<div class="button-wrapper">
<a class="button btn btn-default" href="'.$url.'" target="'.$target.'">'.remove_wpautop($content).'</a>
</div>';
	return $ret;
}
add_shortcode('hero','msd_child_landing_page_hero');
function msd_child_landing_page_hero($atts, $content = null){
	$ret = '<div class="hero">'.remove_wpautop($content).'</div>';
	return $ret;
}
add_shortcode('callout','msd_child_landing_page_callout');
function msd_child_landing_page_callout($atts, $content = null){
	$ret = '<div class="callout">'.remove_wpautop($content).'</div>';
	return $ret;
}
function column_shortcode($atts, $content = null){
	extract( shortcode_atts( array(
	'cols' => '3',
	'position' => '',
	), $atts ) );
	switch($cols){
		case 5:
			$classes[] = 'one-fifth';
			break;
		case 4:
			$classes[] = 'one-fouth';
			break;
		case 3:
			$classes[] = 'one-third';
			break;
		case 2:
			$classes[] = 'one-half';
			break;
	}
	switch($position){
		case 'first':
		case '1':
			$classes[] = 'first';
		case 'last':
			$classes[] = 'last';
	}
	return '<div class="'.implode(' ',$classes).'">'.remove_wpautop($content).'</div>';
}

add_shortcode('columns','column_shortcode');

function msd_inline_menu($atts){
	extract( shortcode_atts( array(
	'menu' => 'primary-links',
	'walker' => 'msd_icon_desc_walker',
	), $atts ) );
	$walker = new $walker;
	$defaults = array(
			'menu'            => $menu,
			'container'       => 'div',
			'container_class' => '',
			'container_id'    => '',
			'menu_class'      => 'menu',
			'menu_id'         => '',
			'echo'            => false,
			'fallback_cb'     => 'wp_page_menu',
			'before'          => '',
			'after'           => '',
			'link_before'     => '<h3>',
			'link_after'      => '</h3>',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'depth'           => 0,
			'walker'          => $walker
	);
	return wp_nav_menu( $defaults );
}
add_shortcode('menu','msd_inline_menu');

class msd_icon_desc_walker extends Walker_Nav_Menu {
		function start_el(&$output, $item, $depth, $args) {
			global $wp_query;
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
			$class_names = $value = '';
	
			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
	
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = ' class="' . esc_attr( $class_names ) . '"';
			
			foreach ($item->classes AS $class){
				if(stristr($class,'my-')){
					$icon_class[] = preg_replace('/my\-/i', '', $class);
				}
			}
			$icon_classes = ' class="'.implode(' ',$icon_class).'"';
	
			$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
	
			$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) .'"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) .'"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) .'"' : '';
	
			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'><i'.$icon_classes.'></i>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= '<span class="sub">' . $item->description . '</span>';
			$item_output .= '</a>';
			$item_output .= $args->after;
	
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
	
add_shortcode('carousel','msd_bootstrap_carousel');
function msd_bootstrap_carousel($atts){
	$slidedeck = new SlideDeck();
	extract( shortcode_atts( array(
		'id' => NULL,
	), $atts ) );
	$sd = $slidedeck->get($id);
	$slides = $slidedeck->fetch_and_sort_slides( $sd );
	$i = 0;
	foreach($slides AS $slide){
		$active = $i==0?' active':'';
		$items .= '
		<div style="background: url('.$slide['image'].') center top no-repeat #000000;background-size: cover;" class="item'.$active.'">
			<div class="carousel-caption">
				'.$slide['content'].'
			</div>
		</div>';
		$i++;
	}
	return msd_carousel_wrapper($items,array('id' => $id));
}

function msd_carousel_wrapper($slides,$params = array()){
	extract( array_merge( array(
	'id' => NULL,
	'navleft' => '‹',
	'navright' => '›'
	), $params ) );
	return '
<div class="carousel slide" id="myCarousel_'.$id.'">
	<div class="carousel-inner">'.($slides).'</div>
	<a data-slide="prev" href="#myCarousel_'.$id.'" class="left carousel-control">'.$navleft.'</a>
	<a data-slide="next" href="#myCarousel_'.$id.'" class="right carousel-control">'.$navright.'</a>
</div>';
}

add_shortcode('get_children', 'msd_get_children');
function msd_get_children($atts){
    global $post;
    extract( shortcode_atts( array(
        'show' => array('title','content','featured_image'),
        'orderby' => 'title',
        'parent' => $post->post_name,
    ), $atts ) );
    $my_wp_query = new WP_Query();
    $args = array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'order' => 'ASC',
            'orderby' => $orderby,
            'tax_query' => array(
                    array(
                        'taxonomy' => 'msd_scrollie',
                        'field' => 'slug',
                        'terms' => $parent
                        )
                    )
            );
    $children = $my_wp_query->query($args);
    $i = 1;
    foreach($children AS $child){
        $thumbnail = get_the_post_thumbnail($child->ID,'sidebar-image');
        $edit = get_edit_post_link($child->ID) != ''?'<a href="'.get_edit_post_link($child->ID).'"><i class="fa fa-edit"></i></a>':'';
        $ret .= '<div id="'.$child->post_name.'" class="children child div-'.$child->post_name.' div'.$i.' trigger" postid="'.$child->ID.'">
                    <a href="'.get_permalink($child->ID).'">
                        '.$thumbnail.'
                        <h3>'.$child->post_title.'</h3>
                    </a>
                    '.$edit.'
                </div>';
        $i++;
    }
    return $ret;
}
