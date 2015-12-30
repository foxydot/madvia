<?php
/**
 * @package MSD Portfolio CPT
 * @version 0.1
 */

class MSDPortfolioCPT {
	
	function __construct(){
		global $msd_portfolio_url,$wpalchemy_media_access;
		add_action( 'init', array(&$this,'register_cpt_portfolio') );
		add_action( 'init', array(&$this,'register_taxonomy_portfolio_tags') );
		add_action( 'init', array(&$this,'register_thumbnail') );
		//add_action('admin_head', array(&$this,'plugin_header'));
		add_shortcode( 'portfolio', array(&$this,'msd_portfolio_grid') );
		add_shortcode( 'portfolio-slider', array(&$this,'msd_portfolio_slider') );	
		add_shortcode( 'portfolio-popup-slider', array(&$this,'msd_portfolio_popup_slider') );	
		add_shortcode( 'portfolio-all', array(&$this,'all_portfolio_items') );
		add_filter( 'the_content', array(&$this,'add_lazy_src_to_allowed_attributes') );
		add_filter( 'enter_title_here', array(&$this,'change_default_title') );
		wp_enqueue_script('lazy-bootstrap-carousel',$msd_portfolio_url.'lib/js/lazy-bootstrap-carousel.js',array('jquery','bootstrap-jquery'));
		wp_enqueue_script('msd-portfolio-jquery',$msd_portfolio_url.'lib/js/msd-portfolio.jquery.js',array('jquery','bootstrap-jquery'));
		if($screen->post_type == 'msd_portfolio')
			add_action('admin_footer',array(&$this,'info_footer_hook') );

		if(!class_exists('WPAlchemy_MediaAccess')){
			include_once (WP_CONTENT_DIR . '/wpalchemy/MediaAccess.php');
		}

		$wpalchemy_media_access = new WPAlchemy_MediaAccess();
	}
	
	public function MSDPortfolioCPT(){
		$this->__construct();
	}
	
	public function register_taxonomy_portfolio_tags() {
	
		$labels = array(
				'name' => _x( 'Portfolio', 'portfolio' ),
				'singular_name' => _x( 'Portfolio', 'portfolio' ),
				'search_items' => _x( 'Search Portfolios', 'portfolio' ),
				'popular_items' => _x( 'Popular Portfolios', 'portfolio' ),
				'all_items' => _x( 'All Portfolios', 'portfolio' ),
				'parent_item' => _x( 'Parent Portfolio', 'portfolio' ),
				'parent_item_colon' => _x( 'Parent Portfolio:', 'portfolio' ),
				'edit_item' => _x( 'Edit Portfolio', 'portfolio' ),
				'update_item' => _x( 'Update Portfolio', 'portfolio' ),
				'add_new_item' => _x( 'Add New Portfolio', 'portfolio' ),
				'new_item_name' => _x( 'New Portfolio Name', 'portfolio' ),
				'separate_items_with_commas' => _x( 'Separate portfolios with commas', 'portfolio' ),
				'add_or_remove_items' => _x( 'Add or remove portfolios', 'portfolio' ),
				'choose_from_most_used' => _x( 'Choose from the most used portfolios', 'portfolio' ),
				'menu_name' => _x( 'Portfolios', 'portfolio' ),
		);
	
		$args = array(
				'labels' => $labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => false,
	
				'rewrite' => true,
				'query_var' => true
		);
	
		register_taxonomy( 'msd_portfolio_tag', array('msd_portfolio'), $args );
	}
		
	function register_cpt_portfolio() {
	    $labels = array( 
	        'name' => _x( 'Portfolio Items', 'portfolio' ),
	        'singular_name' => _x( 'Portfolio Item', 'portfolio' ),
	        'add_new' => _x( 'Add New', 'portfolio' ),
	        'add_new_item' => _x( 'Add New Portfolio Item', 'portfolio' ),
	        'edit_item' => _x( 'Edit Portfolio Item', 'portfolio' ),
	        'new_item' => _x( 'New Portfolio Item', 'portfolio' ),
	        'view_item' => _x( 'View Portfolio Item', 'portfolio' ),
	        'search_items' => _x( 'Search Portfolio Items', 'portfolio' ),
	        'not_found' => _x( 'No portfolio items found', 'portfolio' ),
	        'not_found_in_trash' => _x( 'No portfolio items found in Trash', 'portfolio' ),
	        'parent_item_colon' => _x( 'Parent Portfolio Item:', 'portfolio' ),
	        'menu_name' => _x( 'Portfolio Items', 'portfolio' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => true,
	        'description' => 'Customer Portfolios',
	        'supports' => array( 'title', 'editor', 'author', 'thumbnail'),
	        'taxonomies' => array('msd_portfolio_tag'),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'menu_position' => 20,
	        
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'has_archive' => true,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => array('slug'=>'portfolio','with_front'=>false),
	        'capability_type' => 'post'
	    );
	
	    register_post_type( 'msd_portfolio', $args );
	}
	
	function register_thumbnail(){
		if (class_exists('MultiPostThumbnails')) {
			new MultiPostThumbnails(
					array(
							'label' => 'Grid Thumbnail',
							'id' => 'grid-image',
							'post_type' => 'msd_portfolio'
					)
			);
		}
	}

	function plugin_header() {
		global $post_type;
		?>
			<style>
			<?php if (($_GET['post_type'] == 'msd_portfolio') || ($post_type == 'msd_portfolio')) : ?>
			#icon-edit { background:transparent url('<?php echo get_stylesheet_directory_uri().'/lib/img/msd_portfolio-over.png';?>') no-repeat; }		
			<?php endif; ?>	
			#adminmenu #menu-posts-msd_portfolio div.wp-menu-image{background:transparent url("<?php echo get_stylesheet_directory_uri().'/lib/img/msd_portfolio.png';?>") no-repeat center center;}
			#adminmenu #menu-posts-msd_portfolio:hover div.wp-menu-image,#adminmenu #menu-posts-msd_profile.wp-has-current-submenu div.wp-menu-image{background:transparent url("<?php echo get_stylesheet_directory_uri().'/lib/img/msd_portfolio-over.png';?>") no-repeat center center;}	
		    </style>
		    <?php
		}
		function get_portfolio_items($tags){
			$args = array( 
				'post_type' => 'msd_portfolio', 
				'numberposts' => -1,
				'order' => 'ASC',
				'orderby' => 'menu_order',
			);
			if(count($tags)>0){
				$args['tax_query'] =  array(
						array(
								'taxonomy' => 'msd_portfolio_tag',
								'field' => 'slug',
								'terms' => $tags
						)
				);
			}
			return get_posts($args);
		}
		
		function get_portfolio_grid_image($item){
			global $portfolio,$post,$msd_portfolio_url;
			$portfolio->the_meta($item->ID);
			$youtube = $portfolio->get_the_value('youtube');
			if($youtube!=''){
				if (class_exists('MultiPostThumbnails') && $post_thumbnail_id = MultiPostThumbnails::get_post_thumbnail_id('msd_portfolio', 'grid-image',$item->ID)) {
					$featured_image = wp_get_attachment_image_src( $post_thumbnail_id, 'portfolio', false, $attr );
					$featured_image = $featured_image[0];
				} else {
					preg_match('/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/i',$youtube,$matches);
					$videoid = $matches[2];
					$featured_image = 'http://img.youtube.com/vi/'.$videoid.'/0.jpg';
				}
			} else {
				if (class_exists('MultiPostThumbnails') && $post_thumbnail_id = MultiPostThumbnails::get_post_thumbnail_id('msd_portfolio', 'grid-image',$item->ID)) {
					$featured_image = wp_get_attachment_image_src( $post_thumbnail_id, 'portfolio', false, $attr );
					$featured_image = $featured_image[0];
				} else {
					$featured_image = featured_image($item->ID,'portfolio');
					$featured_image = $featured_image[0];
				}
			}
			return $featured_image;
		}
		
		function get_portfolio_content($item){
			global $portfolio,$post,$msd_portfolio_url;
			$portfolio->the_meta($item->ID);
			$youtube = $portfolio->get_the_value('youtube');
			if($youtube!=''){
				$youtube = preg_replace('@http(s)?\:\/\/@i', 'httpv://', $youtube);
				$norelated = strrpos($youtube,'?')>1?'&rel=0':'?rel=0';
				$content = $youtube.$norelated;
				if(function_exists('lyte_parse')) { $content = lyte_parse($content); }
			} else {
				$large_image = wp_get_attachment_image_src( get_post_thumbnail_id($item->ID),'large' );
				$content = $large_image?'<img lazy-src="'.$large_image[0].'" class="dropshadow" />':FALSE;
			}
			$content = array(
					'title' => $item->post_title,
					'description' => $item->post_content,
					'image' => $content,
			);
			if($content_array = $this->get_additional_portfolio_content($item)){
				array_unshift($content_array,$content);
				return $content_array;
			}
			$content_array[] = $content;
			return $content_array;
		}
		
		function get_additional_portfolio_content($item){
			global $portfolio,$post,$msd_portfolio_url;
			if($portfolio->have_fields('multientry')):
				while($portfolio->have_fields('multientry')):
					$content = array(
						'title' => $portfolio->get_the_value('title')?$portfolio->get_the_value('title'):$item->post_title,
						'description' => $portfolio->get_the_value('description')?$portfolio->get_the_value('description'):$item->post_content,
						'image' => '<img src="'.$portfolio->get_the_value('image').'" class="dropshadow" />',
					);
					$content_array[] = $content;
				endwhile; //end loop
				return $content_array;
			endif;
			return FALSE;
		}

		function msd_portfolio_grid( $atts ){
			global $portfolio,$post,$msd_portfolio_url;
			extract( shortcode_atts( array(
			'tags' => '',
			'cols' => 4,
			), $atts ) );
			$tags = explode(',',$tags);
			$ID = $tags[0];
				
			$items = $this->get_portfolio_items($tags);
			$count = (floor(count($items)/$cols))*$cols;
			$items = array_slice($items, 0, $count);
			$i = 1;
			foreach($items AS $item){
				$portfolio->the_meta($item->ID);
				$youtube = $portfolio->get_the_value('youtube');
				$featured_image = $this->get_portfolio_grid_image($item);
				$content = $this->get_portfolio_content($item);
		
				$menu .= '<li class="tab-'.$item->post_name.'" style="background:url('.$featured_image.') no-repeat center center;background-size:cover;"><a href="#'.$item->post_name.'" title="'.$item->post_title.'" data-toggle="modal">'.$item->post_title.'</a></li>'."\n";
				$j = 0;
					foreach ($content AS $piece){
						if(!empty($piece['image'])){
							$key = $j==0?'':'-'.$j;
							$slides .=  '<div id="'.$item->post_name.$key.'" class="modal hide fade div-'.$item->post_name.$key.'" role="dialog">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									</div>
					        		<div class="modal-body">
										<div class="portfolio-piece">'.remove_wpautop(apply_filters('the_content', $piece['image'])).'</div>
					    				<h3 class="portfolio-piece-title">'.$piece['title'].'</h3>
										<div class="entry-content">'.remove_wpautop(apply_filters('the_content', $piece['description'])).'</div>
									</div>
								</div>';
							$j++;
						}
					}
				$i++;
			}
		
			return "\n".'<div class="portfolio-grid portfolio-'.$ID.'">'."\n".'<ul class="nav">'."\n".$menu."\n".'</ul>'."\n".'<div class="content">'."\n".$slides."\n".'</div>'."\n".$nav."\n".'</div>';
		}
		function msd_portfolio_list( $atts ){
			global $portfolio,$post,$msd_portfolio_url;
			extract( shortcode_atts( array(
			'tags' => '',
			'cols' => 4,
			), $atts ) );
			$tags = explode(',',$tags);
			$ID = $tags[0];
			
			$items = $this->get_portfolio_items($tags);
			$count = (floor(count($items)/$cols))*$cols;
			$items = array_slice($items, 0, $count);
			$i = 1;
			foreach($items AS $item){
				$portfolio->the_meta($item->ID);
				$youtube = $portfolio->get_the_value('youtube');
				$featured_image = $this->get_portfolio_grid_image($item);
				$content = $this->get_portfolio_content($item);
				
				$menu .= '<li class="tab-'.$item->post_name.'"><a href="#'.$item->post_name.'" title="'.$item->post_title.'" style="background:url('.$featured_image.') no-repeat center center;background-size:cover;" data-toggle="modal">'.$item->post_title.'</a><h3>'.$item->post_title.'</h3></li>'."\n";
				$j = 0;
				foreach ($content AS $piece){
					if(!empty($piece['image'])){
						$key = $j==0?'':'-'.$j;
						$slides .=  '<div id="'.$item->post_name.$key.'" class="modal hide fade div-'.$item->post_name.$key.'" role="dialog">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								</div>
				        		<div class="modal-body">
									<div class="portfolio-piece">'.remove_wpautop(apply_filters('the_content', $piece['image'])).'</div>
				    				<h3 class="portfolio-piece-title">'.$piece['title'].'</h3>
									<div class="entry-content">'.remove_wpautop(apply_filters('the_content', $piece['description'])).'</div>
								</div>
							</div>';
						$j++;
					}
				}
				$i++;				
			}

			return "\n".'<div class="portfolio portfolio-'.$ID.'">'."\n".'<ul class="nav">'."\n".$menu."\n".'</ul>'."\n".'<div class="content">'."\n".$slides."\n".'</div>'."\n".$nav."\n".'</div>';			
		}	

		function msd_portfolio_slider( $atts ){
			global $portfolio,$post,$msd_portfolio_url;
			extract( shortcode_atts( array(
			'tags' => '',
			'cols' => 4,
			), $atts ) );
			$tags = explode(',',$tags);
			$ID = $tags[0];
			$items = $this->get_portfolio_items($tags);
			$count = (floor(count($items)/$cols))*$cols;
			$items = array_slice($items, 0, $count);
			$i = 1;
			foreach($items AS $item){
				$active = $i==1?' active':'';
				$portfolio->the_meta($item->ID);
				$youtube = $portfolio->get_the_value('youtube');
				$featured_image = $this->get_portfolio_grid_image($item);
				$content = $this->get_portfolio_content($item);
				$j = 0;
				foreach ($content AS $piece){
					if(!empty($piece['image'])){
						$key = $j==0?'':'-'.$j;
						$slides .=  '<div id="'.$item->post_name.$key.'" class="item div-'.$item->post_name.$key.$active.'">
							<div class="portfolio-piece">'.remove_wpautop(apply_filters('the_content', $piece['image'])).'</div>
		    				<h3 class="portfolio-piece-title">'.$piece['title'].'</h3>
							<div class="entry-content">'.remove_wpautop(apply_filters('the_content', $piece['description'])).'</div>
						</div>';
						$j++;
					}
				}
				$i++;
			}
			$nav = ' <!-- Image loading -->
                <div class="loading hide"><i class="icon-spinner icon-spin icon-large"></i></div>
					<!-- Carousel nav -->
			<a class="portfolio-control portfolio-control-'.$ID.' left" href="#portfolio-'.$ID.'" data-slide="prev">&lsaquo;</a>
			<a class="portfolio-control portfolio-control-'.$ID.' right" href="#portfolio-'.$ID.'" data-slide="next">&rsaquo;</a>';
		
			return "\n".'<div id="portfolio-'.$ID.'" class="carousel slide portfolio">'."\n".'<div class="carousel-inner">'."\n".$slides."\n".'</div>'."\n".$nav."\n".'</div>';
		}

		function msd_portfolio_popup_slider( $atts ){
			global $portfolio,$post,$msd_portfolio_url;
			extract( shortcode_atts( array(
			'tags' => '',
			'cols' => 4,
			), $atts ) );
			$tags = explode(',',$tags);
			$ID = $tags[0];
			$items = $this->get_portfolio_items($tags);
			$count = (floor(count($items)/$cols))*$cols;
			$items = array_slice($items, 0, $count);
			$i = 1;
			foreach($items AS $item){
				$active = $i==1?' active':'';
				$portfolio->the_meta($item->ID);
				$youtube = $portfolio->get_the_value('youtube');
				$featured_image = $this->get_portfolio_grid_image($item);
				$content = $this->get_portfolio_content($item);				
				$menu .= '<li class="tab-'.$item->post_name.'" title="'.$item->post_title.'" style="background:url('.$featured_image.') no-repeat center center;background-size:cover;"><a href="#'.$ID.'" title="'.$item->post_name.'" data-toggle="modal">'.$item->post_title.'</a></li>'."\n";
				$j = 0;
				foreach ($content AS $piece){
					if(!empty($piece['image'])){
						$key = $j==0?'':'-'.$j;
						$slides .=  '<div id="'.$item->post_name.$key.'" class="item div-'.$item->post_name.$key.$active.'">
							<div class="portfolio-piece">'.remove_wpautop(apply_filters('the_content', $piece['image'])).'</div>
		    				<h3 class="portfolio-piece-title">'.$piece['title'].'</h3>
							<div class="entry-content">'.remove_wpautop(apply_filters('the_content', $piece['description'])).'</div>
						</div>';
						$j++;
					}
				}			
				$i++;
			}
			$nav = ' <!-- Image loading -->
                <div class="loading hide"><i class="icon-spinner icon-spin icon-large"></i></div>
				<!-- Carousel nav -->
			<a class="portfolio-control portfolio-control-'.$ID.' left" href="#portfolio-'.$ID.'" data-slide="prev">&lsaquo;</a>
			<a class="portfolio-control portfolio-control-'.$ID.' right" href="#portfolio-'.$ID.'" data-slide="next">&rsaquo;</a>';
			$slides = '<div id="'.$ID.'" class="modal hide fade div-'.$ID.'" role="dialog">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">
						<div id="portfolio-'.$ID.'" class="carousel slide portfolio">
							<div class="carousel-inner">
								'.$slides.'
							</div>
								'.$nav.'
						</div>
					</div>
				</div>';
			return "\n".'<div class="portfolio-grid portfolio-popup-slider portfolio-'.$ID.'">'."\n".'<ul class="nav">'."\n".$menu."\n".'</ul>'."\n".$slides."\n".'</div>';
		}
	//some utils
	function add_lazy_src_to_allowed_attributes($content){
		global $allowedposttags;
		$tags = array('a','img','button','div');
		$atts = array('lazy-src');
		foreach($tags AS $t){
			foreach($atts AS $a){
				$allowedposttags[$t][$a]=true;
			}
		}
		$content = wp_kses($content,$allowedposttags);
		return $content;
	}	
	
	
	
		function all_portfolio_items( $atts ){
			global $portfolio,$post,$msd_portfolio_url;
			extract( shortcode_atts( array(
			'cols' => 4,
			), $atts ) );
			$ID = 'all';
			$items = $this->get_portfolio_items(array());
			$i = 1;
			foreach($items AS $item){
				$portfolio->the_meta($item->ID);
				$youtube = $portfolio->get_the_value('youtube');
				$featured_image = $this->get_portfolio_grid_image($item);
				$content = $this->get_portfolio_content($item);
		
				$menu .= '<li class="tab-'.$item->post_name.'" style="background:url('.$featured_image.') no-repeat center center;background-size:cover;"><a href="#'.$item->post_name.'" data-toggle="modal">'.$item->post_title.'</a><a href="'.get_edit_post_link( $item->ID ).'"><i class="icon-edit"></i></a>'.get_the_term_list($item->ID,'msd_portfolio_tag').'</li>'."\n";
				$j = 0;
				foreach ($content AS $piece){
					if(!empty($piece['image'])){
						$key = $j==0?'':'-'.$j;
						$slides .=  '<div id="'.$item->post_name.$key.'" class="item div-'.$item->post_name.$key.$active.'">
							<div class="portfolio-piece">'.remove_wpautop(apply_filters('the_content', $piece['image'])).'</div>
		    				<h3 class="portfolio-piece-title">'.$piece['title'].'</h3>
							<div class="entry-content">'.remove_wpautop(apply_filters('the_content', $piece['description'])).'</div>
						</div>';
						$j++;
					}
				}	
				$i++;
			}
		
			return "\n".'<div class="portfolio-grid portfolio-'.$ID.'">'."\n".'<ul class="nav">'."\n".$menu."\n".'</ul>'."\n".'<div class="content">'."\n".$slides."\n".'</div>'."\n".$nav."\n".'</div>';
		}
			
function change_default_title( $title ){
		$screen = get_current_screen();
		if  ( $screen->post_type == 'msd_portfolio' ) {
			return __('Enter Portfolio Title Here','msd_portfolio');
		} else {
			return $title;
		}
	}
	
	function info_footer_hook()
	{
		?><script type="text/javascript">
			jQuery('#titlediv').after(jQuery('#_portfolio_metabox'));
			jQuery('#postdivrich').hide();
		</script><?php
	}
}
$portfolio_cpt = new MSDPortfolioCPT();