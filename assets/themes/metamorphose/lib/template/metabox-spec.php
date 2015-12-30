<?php

$subtitle_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_subtitle',
	'title' => 'Subtitle',
	'types' => array('post','page'),
	'template' => get_stylesheet_directory() . '/lib/template/subtitle-meta.php',
));

/* eof */