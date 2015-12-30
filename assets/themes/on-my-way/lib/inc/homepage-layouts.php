<?php
/**
 * Add a hero space with the site description
 */
function msd_child_hero(){
	if(is_active_sidebar('homepage-top')){
		print '<div id="hp-top">';
		print '<div class="wrap">';
		dynamic_sidebar('homepage-top');
		print '</div>';
		print '<div class="wrap2">';
		do_action( 'genesis_site_description' );
		print '</div>';
		print '</div>';
	}
}

/**
 * Add a hero space with the site description
 */
function msd_callout(){
	if(is_active_sidebar('homepage-callout')){
		print '<div id="hp-callout">';
		print '<div class="wrap">';
		dynamic_sidebar('homepage-callout');
		print '</div>';
		print '</div>';
	}
}

/**
 * Add three widget areas
 */
function msd_child_homepage_widgets(){
	print '<div id="hp-bot">';
	print '<div class="wrap">';
	print '<div id="widgets-one" class="widget-area"><div class="wrap">';
	dynamic_sidebar('homepage-one');
	print '</div></div>';
	print '<div id="widgets-two" class="widget-area"><div class="wrap">';
	dynamic_sidebar('homepage-two');
	print '</div></div>';
	print '<div id="widgets-three" class="widget-area"><div class="wrap">';
	dynamic_sidebar('homepage-three');
	print '</div></div>';
	print '</div>';
	print '</div>';
}

/**
 * Create a long scrollie page with child pages of homepage.
 * Uses featured image for background of each wrap section.
 */
function msd_scrollie_page(){
	global $post;
	$edit = get_edit_post_link($post->ID) != ''?'<a href="'.get_edit_post_link($post->ID).'"><i class="fa fa-edit"></i></a>':'';
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
	$background = $thumbnail?' style="background-image:url('.$thumbnail[0].');"':'';
	remove_filter('the_content','wpautop',12);
	print '<div id="intro" class="scrollie parent div-intro div0">
				<div class="background-wrapper"'.$background.'>
						<div class="wrap">
							<div class="page-content">
									<div class="entry-content">';
	print apply_filters('the_content', $post->post_content);
	print '						</div>
							'.$edit.'
							</div>
						</div>
					</div>
				</div>';
	print '<div id="callout"><p>'.get_option('blogdescription').'</p></div>';

	add_filter('the_content','wpautop',12);
	$my_wp_query = new WP_Query();
	$args = array(
			'post_type' => 'page',
			'posts_per_page' => -1,
			'order' => 'ASC',
			'orderby' => 'menu_order',
			'tax_query' => array(
					array(
						'taxonomy' => 'msd_scrollie',
						'field' => 'slug',
						'terms' => 'home'
						)
					)
			);
	$children = $my_wp_query->query($args);
	$i = 1;
	foreach($children AS $child){
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($child->ID), 'full' );
		$background = $thumbnail?' style="background-image:url('.$thumbnail[0].');"':'';
		$form = $child->post_name=='contact-us'?do_shortcode('[gravityform id="1" name="Untitled Form" title="false" ajax="true"]'):'';
		$edit = get_edit_post_link($child->ID) != ''?'<a href="'.get_edit_post_link($child->ID).'"><i class="fa fa-edit"></i></a>':'';
		print '<div id="'.$child->post_name.'" class="scrollie child div-'.$child->post_name.' div'.$i.' trigger" postid="'.$child->ID.'">
				<div class="background-wrapper"'.$background.'>
						<div class="wrap">'.$form.'
							<div class="page-content">
								<h2 class="entry-title">'.$child->post_title.'</h2>
								<div class="entry-content">'.do_shortcode($child->post_content).'</div>
								'.$edit.'
							</div>
						</div>
					</div>
				</div>';
		$i++;
	}
}

/**
 * create a taxonomy for long scrollies
 */
function register_taxonomy_scrollie() {

	$labels = array(
			'name' => _x( 'Scrollie Sections', 'scrollie' ),
			'singular_name' => _x( 'Scrollie Section', 'scrollie' ),
			'search_items' => _x( 'Search Scrollie Sections', 'scrollie' ),
			'popular_items' => _x( 'Popular Scrollie Sections', 'scrollie' ),
			'all_items' => _x( 'All Scrollie Sections', 'scrollie' ),
			'parent_item' => _x( 'Parent Scrollie Section', 'scrollie' ),
			'parent_item_colon' => _x( 'Parent Scrollie Section:', 'scrollie' ),
			'edit_item' => _x( 'Edit Scrollie Section', 'scrollie' ),
			'update_item' => _x( 'Update Scrollie Section', 'scrollie' ),
			'add_new_item' => _x( 'Add New Scrollie Section', 'scrollie' ),
			'new_item_name' => _x( 'New Scrollie Section Name', 'scrollie' ),
			'separate_items_with_commas' => _x( 'Separate scrollies with commas', 'scrollie' ),
			'add_or_remove_items' => _x( 'Add or remove scrollies', 'scrollie' ),
			'choose_from_most_used' => _x( 'Choose from the most used scrollies', 'scrollie' ),
			'menu_name' => _x( 'Scrollie Sections', 'scrollie' ),
	);

	$args = array(
			'labels' => $labels,
			'public' => false,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true,

			'rewrite' => true,
			'query_var' => true
	);

	register_taxonomy( 'msd_scrollie', array('page'), $args );
}	
