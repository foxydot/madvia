<?php 
if (!class_exists('MSDRecentPosts')) {
    class MSDRecentPosts {
        //Properties
        static $add_script;
        //Methods
        /**
        * PHP 4 Compatible Constructor
        */
        public function MSDRecentPosts(){$this->__construct();}
    
        /**
         * PHP 5 Constructor
         */
        function __construct(){
            add_image_size('slider-thumb',260,145,TRUE);
            //Actions
            add_action('init', array(&$this,'register_scripts_and_styles'));
            add_action('wp_enqueue_scripts', array(&$this,'add_scripts_and_styles') );
            
            //Filters
            add_filter('the_content', array(&$this,'pre_process_shortcode'), 7);
            //Shortcodes
            add_shortcode('genesis_recent_posts',array(&$this,'genesis_recent_posts_function'));
        }
      
        function register_scripts_and_styles() {
            //wp_register_script('my-script', plugins_url('my-script.js', __FILE__), array('jquery'), '1.0', true);
        }
        
        function add_scripts_and_styles(){
           if ( ! self::$add_script )
            return;

           // wp_print_scripts('my-script');
        }
        
        function pre_process_shortcode($content) {
            global $shortcode_tags;
         
            // Backup current registered shortcodes and clear them all out
            $orig_shortcode_tags = $shortcode_tags;
            $shortcode_tags = array();
         
                add_shortcode("genesis_recent_posts","genesis_recent_posts");
         
            // Do the shortcode (only the one above is registered)
            $content = do_shortcode($content);
         
            // Put the original shortcodes back
            $shortcode_tags = $orig_shortcode_tags;
         
            return $content;
        }

        function genesis_recent_posts_function($atts){
            self::$add_script = true;
            //actual shortcode handling
                
            extract( shortcode_atts( array(
            ), $atts ) );
            
            $args = array( 'post_type' => 'post', 'numberposts' => 9, );
    
            $items = get_posts($args);
            $i = 0;
            foreach($items AS $item){ 
                if($i!=0 && $i%3==0){$post_list.= '</div><div class="item">';}
                $i++;
                $excerpt = $item->post_excerpt?$item->post_excerpt:msd_trim_headline($item->post_content);
                $post_list .= '<a href="'.get_permalink($item->ID).'" class="recent_post">
                    '.get_the_post_thumbnail($item->ID,'slider-thumb').'
                    <h3>'.$item->post_title.'</h3>
                    <div class="date">'.date('F j, Y',strtotime($item->post_date)).'</div>
                    <div class="excerpt">'.$excerpt.'</div>
                    <div class="overlay"><div class="bug">Read This</div></div>
                </a>';
             }
            
            return '<div id="recentPostsSlider" class="recent posts carousel slide">
                <div class="carousel-inner">
                    <div class="active item">'.$post_list.'</div>
                </div>
                <a class="carousel-control left" href="#recentPostsSlider" data-slide="prev">&lsaquo;</a>
                <a class="carousel-control right" href="#recentPostsSlider" data-slide="next">&rsaquo;</a>
            </div>';
        } 
  } //End Class
} //End if class exists statement
new MSDRecentPosts();
