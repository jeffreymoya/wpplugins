<?php 

// shortcode for single items 
function hicaliber_slider_single_shortcode( $atts, $content = null  ) {
	 $hss_options = get_option('hss_settings');
	extract( shortcode_atts( array( 
			'id'  		=> 'single-item',
			'category' 	=> ''
	), $atts, 'single' ) );
	
	//query post by category, pass by the variable category in the shortcode.	
	$query_posts = new WP_Query( 
		array( 'hicaliber_carousel_cat' => $category, 'posts_per_page' =>-1, 'post_type' => 'slider-items'));	

		$out='<div class="slider '.$id.'">';
				while($query_posts->have_posts()) : $query_posts->the_post();
					//retrieve metavalue
					$meta_values = get_post_meta(get_the_ID());
					$image = $meta_values['_cmb_banner_image'][0];
					$caption = $meta_values['_cmb_banner_description'][0];
					$out .= '<div class="slider-item" style="background-image: url('.$image.')">
									<div class="caption-wrap">
										<div class="inner">'.$caption.'</div>
									</div>
							  </div>';
				endwhile;
		$out .= '</div>';
		$out .= ' <script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery(".'.$id.'").slick({
					autoplay		: '.$hss_options['hss_autoplay'].',
					dots 			: false,
					infinite 		: '.$hss_options['hss_infiniteloop'].',
					speed 			: '.$hss_options['hss_speed'].',
					slidesToShow 	: 1,
					slidesToScroll 	: 1,
				});
			}); 	
		</script>';
		wp_reset_query();
		return $out;

	}

	add_shortcode('slider', 'hicaliber_slider_single_shortcode');	

	if( ! function_exists('hi_slick_slider')){
		function hi_slick_slider($category = '') {
		 	_e(do_shortcode('[slider category='.$category.']'));
		} 
	}
	
 ?>
