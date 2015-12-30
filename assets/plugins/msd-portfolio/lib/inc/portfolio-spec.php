<?php
global $msd_portfolio_path,$portfolio;
$portfolio = new WPAlchemy_MetaBox(array
(
	'id' => '_portfolio',
	'title' => 'Portfolio Information',
	'types' => array('msd_portfolio'), // added only for pages and to custom post type "events"
	'context' => 'normal', // same as above, defaults to "normal"
	'priority' => 'high', // same as above, defaults to "high"
	'template' => $msd_portfolio_path. '/lib/metaboxes/portfolio-meta.php',
	'mode' => WPALCHEMY_MODE_EXTRACT,
	'prefix' => '_msd_'
));

/* eof */