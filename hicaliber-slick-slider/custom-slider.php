<?php 

/*
Plugin Name: Hicaliber Slick Slider
Plugin URI: http://hicaliber.net.au
Description: Simple Slick Slider
Version: 1.0
Author: hicaliber
Author URI: http://hicaliber.net.au
*/

/**
*  
*/
class Hicaliber_Slick_Slider {
	
	function __construct() {
		$this->set_defines();
		$this->set_actions();
		$this->set_include();
	}	

	/**
     * Defines To Be Used Anywhere
     */
    function set_defines(){
        /*
         * Define Base Paths to the plugin file
         */
        define( 'HSS_HTTP_PATH' , WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__) , "" , plugin_basename(__FILE__) ) );
        define( 'PLUGIN_ABSPATH' , WP_PLUGIN_DIR . '/' . str_replace(basename( __FILE__) , "" , plugin_basename(__FILE__) ) );
    }

	function set_actions(){
		add_action( 'init', array( $this, 'init' ) );
        add_action('admin_menu', array('HSS_OPTIONS', 'Add_Admin_Menus') );	
        add_action('admin_init', array('HSS_OPTIONS', 'options') );
	}

	function init(){
		$this->set_hss_post();
		$this->plugin_enqueues();
	}

	function plugin_enqueues() {
		if (! is_admin() ) {
            wp_enqueue_script('jquery');
			wp_enqueue_script( 'hss-js', HSS_HTTP_PATH . '/js/slick.min.js', array('jquery'), 1.0, false);
	    	wp_enqueue_style( 'hss-slick-css', HSS_HTTP_PATH . '/slick/slick.css');
            wp_enqueue_style( 'hss-slick-theme-css', HSS_HTTP_PATH . '/slick/slick-theme.css');
	    	wp_enqueue_style( 'hss-custom-css', HSS_HTTP_PATH .'/css/styles.css');	
		}
	}

	function set_hss_post() {

		$slidersargs = array(
            'labels' => array(
                'name' => __( 'Categories' ),
                'singular_name' => __( 'Slider' ),
                'search_items' =>  __( 'Search Sliders' ),
                'all_items' => __( 'All Sliders' ),
                'parent_item' => __( 'Parent Slider' ),
                'parent_item_colon' => __( 'Parent Slider:' ),
                'edit_item' => __( 'Edit Slider' ),
                'update_item' => __( 'Update Slider' ),
                'add_new_item' => __( 'Add New Slider' ),
                'new_item_name' => __( 'New Slider Name' ),
                'menu_name' => __( 'Categories' )
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => false
        );

		register_taxonomy( 'hicaliber_carousel_cat' , 'slider-items' , $slidersargs);

		     $slideargs = array(
                'labels' => array(
                        'name' => 'Slides',
                        'singular_name' => 'Slider Item',
                        'add_new' => 'New Slide',
                        'add_new_item' => 'Add New Slide',
                        'edit_item' => 'Edit Slide',
                        'new_item' => 'New Slide',
                        'search_items' => 'Search Slides',
                        'not_found' => 'No Slides Found',
                        'not_found_in_trash' => 'No Slides In The Trash'
                ),
                'description' => 'Slides For The Hicaliber Slick Slider',
                'public' => false,
                'show_ui' => true,
                'hierarchical' => true,
                'taxonomies' => array( 'hicaliber_carousel_cat' ),
                'supports' => array('thumbnail', 'title'),
                'menu_icon' => HSS_HTTP_PATH . 'images/icon.png',
                'rewrite' => false
        );

        register_post_type( 'slider-items', $slideargs );
			
	}

	function set_include(){
		//Adds Custom Metabox 
        include 'inc/hss-options.php';
		//include 'inc/custom_meta_box/lib/slider-meta-box.php';	
		include 'template/output.php';
	}

}

$Hicaliber_Slick_Slider = new Hicaliber_Slick_Slider();

?>