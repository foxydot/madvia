<?php

if(!class_exists('WPAlchemy_MetaBox')){
	include_once (WP_CONTENT_DIR.'/wpalchemy/MetaBox.php');
}

// global styles for the meta boxes
if (is_admin()) add_action('admin_enqueue_scripts', 'portfolio_metabox_style');

function portfolio_metabox_style() {
	global $msd_portfolio_url;
	wp_enqueue_style('portfolio-wpalchemy-metabox', $msd_portfolio_url . '/lib/css/portfolio-meta.css');
}

/* eof */