<?php
/* Subtitle Support */
if(!class_exists('WPAlchemy_MetaBox')){
	include_once WP_CONTENT_DIR.'/wpalchemy/MetaBox.php';
}
add_action('init','add_custom_metaboxes');
function add_custom_metaboxes(){
	global $subtitle_metabox;
	$subtitle_metabox = new WPAlchemy_MetaBox(array
			(
					'id' => '_subtitle',
					'title' => 'Subtitle',
					'types' => array('page'),
					'template' => get_stylesheet_directory() . '/lib/template/subtitle-meta.php',
			));
}
add_action('admin_footer','subtitle_footer_hook');
function subtitle_footer_hook()
{
	?><script type="text/javascript">
		jQuery('#titlediv').after(jQuery('#_subtitle_metabox'));
	</script><?php
}

// include css to help style our custom meta boxes
add_action( 'admin_print_scripts', 'my_metabox_styles' );
 
function my_metabox_styles()
{
    if ( is_admin() )
    {
        wp_enqueue_style('wpalchemy-metabox', get_stylesheet_directory_uri() . '/lib/template/meta.css');
    }
}

//add_action( 'genesis_before_post_content', 'msdlab_do_post_subtitle' );

function msdlab_do_post_subtitle() {
	global $subtitle_metabox;
	$subtitle_metabox->the_meta();
	$subtitle = $subtitle_metabox->get_the_value('subtitle');

	if ( strlen( $subtitle ) == 0 )
		return;

	$subtitle = sprintf( '<h1 class="entry-subtitle">%s</h1>', apply_filters( 'genesis_post_title_text', $subtitle ) );
	echo apply_filters( 'genesis_post_title_output', $subtitle ) . "\n";

}