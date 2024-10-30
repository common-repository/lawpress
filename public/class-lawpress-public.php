<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://businessupwebsite.com
 * @since      1.0.0
 *
 * @package    Lawpress
 * @subpackage Lawpress/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Lawpress
 * @subpackage Lawpress/public
 * @author     Ivan Chernyakov <admin@businessupwebsite.com>
 */
class Lawpress_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @since    1.2.2 - added owl styles
	 * @since    1.3.0 - added bootstrap grid
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lawpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lawpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lawpress-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-font-awesome', plugin_dir_url( __FILE__ ) . 'css/fonts/all.min.css', array(), $this->version, 'all' );

		// owl-carousel
		wp_enqueue_style( $this->plugin_name.'-owl-carousel-main', plugin_dir_url( __FILE__ ) . 'css/owl.carousel.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-owl-carousel-theme', plugin_dir_url( __FILE__ ) . 'css/owl.theme.default.min.css', array(), $this->version, 'all' );

		// bootstrap grid
		wp_enqueue_style( $this->plugin_name.'-bootstrap-grid', plugin_dir_url( __FILE__ ) . 'css/bootstrap-grid.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0	 
	 * @since    1.2.2 - added owl scripts
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lawpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lawpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lawpress-public.js', array( 'jquery' ), $this->version, false );

		// owl-carousel
		wp_enqueue_script( $this->plugin_name.'-owl-carousel-js', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Filter content
	 *
	 * @since    1.0.0
	 * @since    1.4.0 - Addons Compatibility
	 */
	public function lp_filter_content($content){

		$public_path = plugin_dir_path(__FILE__);
		if ( class_exists( 'LawPress_Advanced' ) ){
			$lawpress_advanced = new Lawpress_Advanced();
			$lawpress_advanced_public = new Lawpress_Advanced_Public( $lawpress_advanced->get_plugin_name(), $lawpress_advanced->get_version() );
			$public_path = $lawpress_advanced_public->get_advanced_public_path();
		}

		if ( is_singular( 'lp_practice_area' ) ) {
			ob_start();
			include_once( $public_path . 'partials/lawpress-public-single-practice-area.php');
			$content = ob_get_clean();
		}
		if ( is_singular( 'lp_attorney' ) ) {
			if ( ! in_the_loop() ) return;
				ob_start();
				include_once( $public_path . 'partials/lawpress-public-single-attorney.php');
				$content = ob_get_clean();
		}
		if ( is_singular( 'lp_case' ) ) {
			if ( ! in_the_loop() ) return;
			ob_start();
			include_once( $public_path . 'partials/lawpress-public-single-case.php');
			$content = ob_get_clean();
		}

		return $content;
	}

	/**
	 * Grid shortcode
	 *
	 * @since    1.1.0
	 * @since    1.1.1 - include multipple instead once
	 */
	public function law_shortcode_return( $atts, $content = null ) {

		// Attributes
		$atts = shortcode_atts( array(
			'id' => '',
		), $atts, 'lp_shortcode' );

		ob_start();

		include(plugin_dir_path(__FILE__) . '/partials/lawpress-public-shortcodes.php');

		return ob_get_clean();

	}

	/**
	 * Get link list
	 *
	 * @since    1.3.0
	 * @param    string     $post_id       		Current post id.
	 * @param    string     $field_object_name      Name of field object
	 */
	public function lp_get_link_list( $field_object_name, $post_id ){
		$placeholder = '-';
		$output = '';
		if ( get_field_object( $field_object_name, $post_id ) ){
			$field_object = get_field_object( $field_object_name, $post_id );
		}
		if ( ! empty($field_object['value'])) : 
			$current_object = 0;
			$object_number = count($field_object['value']);
			foreach ( $field_object['value'] as $inner_id ){	
				if ( get_post_status($inner_id) == 'publish' ){
					$output .= '<a href="'.esc_url( get_permalink($inner_id) ).'">'.esc_html( get_the_title($inner_id) ).'</a>';
					if ( ($current_object != $object_number - 1) ) {
						$output .=  ', ';
					}
					$current_object++;
				}
				else 
					$output = $placeholder;
			}
		else : 	
			$output = $placeholder;
		endif;
		return $output;
	}

	/**
	 * Get link list new
	 *
	 * @since    1.3.0
	 * @param    string     $object_id       		Current post id
	 * @param    string     $post_name       		Name of post where to get field object.
	 * @param    string     $field_object_name      Name of field object
	 */
	public function lp_get_link_list_outside( $object_id, $post_name, $field_object_name ){
		$placeholder = '-';
		$icon_number = 0;
		$output = '';
		$output_ids = array();
		$has_one = false; 
		$inner_post_type_query  = new WP_Query(  
			array (  
				'post_type'      => 'lp_'.$post_name,  
				'posts_per_page' => -1  
			)  
		);   					
		//$grid_list .= '<pre>'.print_r($inner_post_type_query,true).'</pre>';
		$inner_posts_array = $inner_post_type_query->posts;
		$inner_ids_array = wp_list_pluck( $inner_posts_array, 'post_title', 'ID' );
		foreach( $inner_ids_array as $post_id => $case_name ) {
			// $grid_list .= '<pre>'.print_r($case_array,true).'</pre>';
			if ( get_field_object( $field_object_name, $post_id ) ){
				$field_object = get_field_object( $field_object_name, $post_id );
			}
			if ( ! empty($field_object['value'])) : 
				foreach ( $field_object['value'] as $inner_id ){	
					if ( $inner_id == $object_id ){
						array_push( $output_ids, $post_id );
					}										
				}
			endif;
		}

		// output
		$current_object = 0;
		$object_number = count($output_ids);
		foreach( $output_ids as $key_not_used => $output_id ) {
			$has_one = true;
			$output .= '<a href="'.esc_url( get_permalink($output_id) ).'">'.esc_html( get_the_title($output_id) ).'</a>';
			if ( ($current_object != $object_number - 1) ) {
				$output .=  ', ';
			}
			$current_object++;	
		}
		wp_reset_postdata();

		if ( ! $has_one ){
			$output = $placeholder;
		}
		return $output;	
	}

	/**
	 * Get Grid list
	 *
	 * @since    1.3.0
	 * @since    1.3.5 - Added info icon caption
	 * @param      array     $ids_array       		Array of all post types.
	 * @param      string    $post_name    			Post name (attorney, case, practice_area)
	 * @param      string    $shortcode_id    		Shortcode id
	 * @param      string    $show_subtitle    		Show subtitle
	 * @param      string    $subtitle    			Subtitle value
	 * @param      array     $icon_args    			Arguments of info icon
	 */
	public function lp_get_grid( array $ids_array, $post_name, $shortcode_id, $show_subtitle = false, $subtitle = '', array $icon_args ){
		global $lawpress_options;

		//icon defaults 
		$icon_defaults = array(
			'show_info_icon' 	=> true,
			'info_icon_type'	=> '',    
			'icon_loop' 		=> true, 
			'info_icon_field' 	=> '',
			'icon'				=> 'fa-gavel',
			'icon_position'		=> 'left'
		);
		$icon_args = array_merge($icon_defaults, $icon_args );

		switch ($icon_args['icon']) {
			case 'fa-gavel':
			$caption = __('Case(s)');
			break;
			
			case 'fa-dollar-sign':
			$caption = '';
			break;

			default:
			$caption = '';
			break;
		}
		if ( $caption != '' ){
			$caption = '<span class="lp-number-caption">'.esc_html( $caption ).'</span>';
		}	


		$grid_list = '';
		$grid_list .= '<div class="lp-grid-list container">';
		$grid_list .= '<div class="row">';
		if ( class_exists('ACF') ){
			if ( get_field( 'lp_'.$post_name.'_settings', $shortcode_id ) ) {
				$grid_settings = get_field( 'lp_'.$post_name.'_settings', $shortcode_id );
			}

			// if all
			if ( isset( $grid_settings['lp_is_all_'.$post_name] ) && $grid_settings['lp_is_all_'.$post_name] != 'all' ){
				if ( $grid_settings['lp_'.$post_name.'_include'] ){
					$objects_included = $grid_settings['lp_'.$post_name.'_include'];
					$ids_array = wp_list_pluck( $objects_included, 'post_title', 'ID' );
				}
			}
			foreach( $ids_array as $object_id => $object_name ) {
				$grid_list_has_one = true;	
				if ( $show_subtitle ){
					if ( get_field('lp_'.$post_name.'_niche', $object_id) ){
						$output_subtitle = get_field('lp_'.$post_name.'_niche', $object_id);
					}
					else{
						$output_subtitle = $subtitle;
					}
				}

				// get count of info icon 
				if ( $icon_args['show_info_icon'] ){
					if ( $icon_args['icon_loop'] ){
						$icon_number = 0;
						$inner_post_type_query  = new WP_Query(  
							array (  
								'post_type'      => 'lp_'.$icon_args['info_icon_type'],  
								'posts_per_page' => -1  
							)  
						);   					
						//$grid_list .= '<pre>'.print_r($inner_post_type_query,true).'</pre>';
						$inner_posts_array = $inner_post_type_query->posts;
						$inner_ids_array = wp_list_pluck( $inner_posts_array, 'post_title', 'ID' );
						foreach( $inner_ids_array as $post_id => $case_name ) {

							if ( get_field( 'lp_'.$icon_args['info_icon_type'].'_'.$post_name.'s', $post_id ) ){
								$icon_array = get_field( 'lp_'.$icon_args['info_icon_type'].'_'.$post_name.'s', $post_id );
										// $grid_list .= '<pre>'.print_r($case_array,true).'</pre>';
								foreach ( $icon_array as $key => $inner_object_id ){
									if ( $inner_object_id == $object_id ){
										$icon_number++;
									}
								}
							}
						}
						wp_reset_postdata();
					}
					else{
						if ( $icon_args['info_icon_field'] != '' ){
							if ( get_field( 'lp_'.$icon_args['info_icon_field'], $object_id ) ){					
								$icon_number = get_field( 'lp_'.$icon_args['info_icon_field'], $object_id );
							}
						}
					}
				}


				if ( get_the_post_thumbnail($object_id) ) {
					$card_classes = 'card-has-thumbnail';
				}
				else $card_classes = 'card-no-thumbnail';
				$grid_list .= '<div class="lp-grid-card col-md-4 '.$card_classes.'"><a href="'.esc_url( get_permalink($object_id) ).'">';

				$info_icon_content = '';
				if ( $icon_args['show_info_icon'] ){
					if ( array_key_exists('info_icons', $lawpress_options['lawpress_main']) ){
						if ( $lawpress_options['lawpress_main']['info_icons'] == 1 ){
											// info icon - cases
							if ( array_key_exists('info_icons', $lawpress_options['lawpress_main']) ){
								if ( $lawpress_options['lawpress_main']['info_icons'] == 1 ){
									if ( $icon_args['icon_position'] == 'right' ){
										$info_icon_content .= '<div class="lp-icon-info"><span class="lp-number">'.esc_html( $icon_number ).'</span><i class="fa '.esc_attr( $icon_args['icon'] ).'"></i>'.$caption.'</div>';
									}
									else{
										$info_icon_content .= '<div class="lp-icon-info"><i class="fa '.esc_attr($icon_args['icon']).'"></i><span class="lp-number">'.esc_html( $icon_number ).'</span>'.$caption.'</div>';
									}
								}
							}
						}
					}	
				}

				if ( get_the_post_thumbnail($object_id) ) {
					$grid_list .= '<div class="lp-thumbnail-container">';
					$grid_list .= '<div class="lp-thumbnail">'.get_the_post_thumbnail($object_id,array(300,300)).'</div>';		
					$grid_list .= '<div class="overlay"></div>';
					$grid_list .= $info_icon_content;					
					$grid_list .= '</div>';
				}
				else{
					$grid_list .= $info_icon_content;
				}

				$grid_list .= '<div class="lp-card-info">';
				$grid_list .= '<h5 class="lp-card-title">'.esc_html( $object_name ).'</h5>';
				if ( $show_subtitle ){ 
					$grid_list .= '<span class="lp-card-subtitle">'.esc_html( $output_subtitle ).'</span>';
				}
				$grid_list .= '</div>';
				$grid_list .= '</a></div>';
			}
		}	
		$grid_list .= '</div>';
		$grid_list .= '</div>';
		return $grid_list;
	}

	/**
	 * Get Related Grid list
	 *
	 * @since    1.3.5
	 * @since    1.4.0 - Extended
	 * @param      string    $title    			Section title	
	 * @param      string    $post_name    			Post name (attorney, case, practice_area)
	 * @param      string    $field_object_name       		Array of all post types.
	 * @param      string    $main_post_id    		Main post id
	 * @param      string    $show_subtitle    		Show subtitle
	 * @param      string    $subtitle    			Subtitle value
	 * @param      array     $icon_args    			Arguments of info icon
	 */
	public function lp_get_related_grid( $title, $post_name, $outside = false, $field_object_name, $main_post_id, $show_subtitle = false, $subtitle = '', array $icon_args ){
		global $lawpress_options;

		//icon defaults 
		$icon_defaults = array(
			'show_info_icon' 	=> true,
			'info_icon_type'	=> '',    
			'icon_loop' 		=> true, 
			'info_icon_field' 	=> '',
			'icon'				=> 'fa-gavel',
			'icon_position'		=> 'right'
		);
		$icon_args = array_merge($icon_defaults, $icon_args );

		$grid_list = '';
		$output_ids = array();
		$post_type_query  = new WP_Query(  
			array (  
				'post_type'      => 'lp_'.$post_name,  
				'posts_per_page' => -1  
			)  
		);   
		$posts_array = $post_type_query->posts;   
		$ids_array = wp_list_pluck( $posts_array, 'post_title', 'ID' );
		
		if ( class_exists('ACF') ){
			if ( ! $outside ){
				if ( get_field_object( $field_object_name ) ){
					$ids_object = get_field_object( $field_object_name );
					$ids_array = $ids_object['value'];
				}
			}
			else {			
				foreach( $ids_array as $post_id => $object_name ) {
					if ( get_field_object( $field_object_name, $post_id ) ){
						$field_object = get_field_object( $field_object_name, $post_id );
					}
					if ( ! empty($field_object['value'])) : 
						foreach ( $field_object['value'] as $inner_id ){	
							if ( $inner_id == $main_post_id ){
								array_push( $output_ids, $post_id );
							}										
						}
					endif;
				}
				$ids_array = $output_ids;			
			}
		}
		if ( empty( $ids_array ) ){
			return;
		}
		

		switch ($icon_args['icon']) {
			case 'fa-gavel':
			$caption = __('Case(s)');
			break;
			
			case 'fa-dollar-sign':
			$caption = '';
			break;

			default:
			$caption = '';
			break;
		}
		if ( $caption != '' ){
			$caption = '<span class="lp-number-caption">'.esc_html( $caption ).'</span>';
		}	
		$grid_list .= '<h3>'.esc_html( $title ).'</h3>';
		
		$grid_list .= '<div class="lp-grid-list container">';
		$grid_list .= '<div class="row">';
		if ( class_exists('ACF') ){
			/*if ( get_field( 'lp_'.$post_name.'_settings', $shortcode_id ) ) {
				$grid_settings = get_field( 'lp_'.$post_name.'_settings', $shortcode_id );
			}*/

			// if all
			/*if ( isset( $grid_settings['lp_is_all_'.$post_name] ) && $grid_settings['lp_is_all_'.$post_name] != 'all' ){
				if ( $grid_settings['lp_'.$post_name.'_include'] ){
					$objects_included = $grid_settings['lp_'.$post_name.'_include'];
					$ids_array = wp_list_pluck( $objects_included, 'post_title', 'ID' );
				}
			}*/
			foreach( $ids_array as $object_id ) {
				$grid_list_has_one = true;	
				if ( $show_subtitle ){
					if ( get_field('lp_'.$post_name.'_niche', $object_id) ){
						$output_subtitle = get_field('lp_'.$post_name.'_niche', $object_id);
					}
					else{
						$output_subtitle = $subtitle;
					}
				}

				// get count of info icon 
				if ( $icon_args['show_info_icon'] ){
					if ( $icon_args['icon_loop'] ){
						$icon_number = 0;
						$inner_post_type_query  = new WP_Query(  
							array (  
								'post_type'      => 'lp_'.$icon_args['info_icon_type'],  
								'posts_per_page' => -1  
							)  
						);   					
						//$grid_list .= '<pre>'.print_r($inner_post_type_query,true).'</pre>';
						$inner_posts_array = $inner_post_type_query->posts;
						$inner_ids_array = wp_list_pluck( $inner_posts_array, 'post_title', 'ID' );
						foreach( $inner_ids_array as $post_id => $case_name ) {

							if ( get_field( 'lp_'.$icon_args['info_icon_type'].'_'.$post_name.'s', $post_id ) ){
								$icon_array = get_field( 'lp_'.$icon_args['info_icon_type'].'_'.$post_name.'s', $post_id );
										// $grid_list .= '<pre>'.print_r($case_array,true).'</pre>';
								foreach ( $icon_array as $key => $inner_object_id ){
									if ( $inner_object_id == $object_id ){
										$icon_number++;
									}
								}
							}
						}
						wp_reset_postdata();
					}
					else{
						if ( $icon_args['info_icon_field'] != '' ){
							if ( get_field( 'lp_'.$icon_args['info_icon_field'], $object_id ) ){					
								$icon_number = get_field( 'lp_'.$icon_args['info_icon_field'], $object_id );
							}
						}
					}
				}


				if ( get_the_post_thumbnail($object_id) ) {
					$card_classes = 'card-has-thumbnail';
				}
				else $card_classes = 'card-no-thumbnail';
				$grid_list .= '<div class="lp-grid-card col-md-4 '.$card_classes.'"><a href="'.esc_url( get_permalink($object_id) ).'">';

				$info_icon_content = '';
				if ( $icon_args['show_info_icon'] ){
					if ( array_key_exists('info_icons', $lawpress_options['lawpress_main']) ){
						if ( $lawpress_options['lawpress_main']['info_icons'] == 1 ){
											// info icon - cases
							if ( array_key_exists('info_icons', $lawpress_options['lawpress_main']) ){
								if ( $lawpress_options['lawpress_main']['info_icons'] == 1 ){
									if ( $icon_args['icon_position'] == 'right' ){
										$info_icon_content .= '<div class="lp-icon-info"><span class="lp-number">'.esc_html( $icon_number ).'</span><i class="fa '.esc_attr( $icon_args['icon'] ).'"></i>'.$caption.'</div>';
									}
									else{
										$info_icon_content .= '<div class="lp-icon-info"><i class="fa '.esc_attr($icon_args['icon']).'"></i><span class="lp-number">'.esc_html( $icon_number ).'</span>'.$caption.'</div>';
									}
								}
							}
						}
					}	
				}

				if ( get_the_post_thumbnail($object_id) ) {
					$grid_list .= '<div class="lp-thumbnail-container">';
					$grid_list .= '<div class="lp-thumbnail">'.get_the_post_thumbnail($object_id,array(300,300)).'</div>';		
					$grid_list .= '<div class="overlay"></div>';
					$grid_list .= $info_icon_content;					
					$grid_list .= '</div>';
				}
				else{
					$grid_list .= $info_icon_content;
				}

				$grid_list .= '<div class="lp-card-info">';
				$grid_list .= '<h5 class="lp-card-title">'.esc_html( get_the_title($object_id) ).'</h5>';
				if ( $show_subtitle ){ 
					$grid_list .= '<span class="lp-card-subtitle">'.esc_html( $output_subtitle ).'</span>';
				}
				$grid_list .= '</div>';
				$grid_list .= '</a></div>';
			}
		}	
		$grid_list .= '</div>';
		$grid_list .= '</div>';
		return $grid_list;
	}

	/**
	 * Get Table
	 *
	 * @since    1.4.0
	 * @param      string    $title    			Section title	
	 * @param      string    $post_name    			Post name (attorney, case, practice_area)
	 * @param      string    $field_object_name       		Array of all post types.
	 * @param      string    $main_post_id    		Main post id
	 * @param      string    $show_subtitle    		Show subtitle
	 * @param      string    $subtitle    			Subtitle value
	 * @param      array     $icon_args    			Arguments of info icon
	 */
	public function lp_get_related_table( $title, $post_name, $outside = false, $field_object_name, $main_post_id, array $secondary_columns ){
		global $lawpress_options;
		$table = '';

		$secondary_columns_default = array();
		$secondary_columns = array_merge($secondary_columns_default, $secondary_columns );

		$output_ids = array();
		$post_type_query  = new WP_Query(  
			array (  
				'post_type'      => 'lp_'.$post_name,  
				'posts_per_page' => -1  
			)  
		);   
		$posts_array = $post_type_query->posts;   
		$ids_array = wp_list_pluck( $posts_array, 'post_title', 'ID' );
		
		if ( class_exists('ACF') ){
			if ( ! $outside ){
				if ( get_field_object( $field_object_name ) ){
					$ids_object = get_field_object( $field_object_name );
					$ids_array = $ids_object['value'];
				}
			}
			else {			
				foreach( $ids_array as $post_id => $object_name ) {
					if ( get_field_object( $field_object_name, $post_id ) ){
						$field_object = get_field_object( $field_object_name, $post_id );
					}
					if ( ! empty($field_object['value'])) : 
						foreach ( $field_object['value'] as $inner_id ){	
							if ( $inner_id == $main_post_id ){
								array_push( $output_ids, $post_id );
								//$output_ids[$post_id] = $object_name;
							}										
						}
					endif;
				}
				$ids_array = $output_ids;			
			}
		}
		//$table .= '<pre>'.print_r($ids_array, true).'</pre>';

		if ( empty( $ids_array ) ){
			return;
		}
		
		$table .= '<h3>'.esc_html( $title ).'</h3>';
		$table .= '<table class="lp-table">';
		
		// table headings
		$table .= '<thead>';
		$table .= '<tr>';
		switch ($post_name) {
			case 'case':
			$table .= '<th>'.__('Settlement', 'lawpress').'</th>';
			break;

			case 'practice_area':
			$table .= '<th>'.__('Area of Practice', 'lawpress').'</th>';
			break;

			case 'attorney':
			$table .= '<th>'.__('Attorney', 'lawpress').'</th>';
			break;
		}

		foreach ($secondary_columns as $key => $secondary_column) {
			switch ($secondary_column) {
				case 'case':
				$table .= '<th>'.__('Case(s)', 'lawpress').'</th>';
				break;

				case 'practice_area':
				$table .= '<th>'.__('Area(s) of Practice', 'lawpress').'</th>';
				break;

				case 'attorney':
				$table .= '<th>'.__('Attorney(s)', 'lawpress').'</th>';
				break;
			}
		}
		$table .= '</tr>';
		$table .= '</thead>';

		if ( class_exists('ACF') ){
			/*if ( get_field( 'lp_'.$post_name.'_settings', $main_post_id ) ) {
				$table_settings = get_field( 'lp_'.$post_name.'_settings', $main_post_id );
			}
			// if all
			if ( isset( $table_settings['lp_is_all_'.$post_name] ) && $table_settings['lp_is_all_'.$post_name] != 'all' ){
				if ( $table_settings['lp_'.$post_name.'_include'] ){
					$objects_included = $table_settings['lp_'.$post_name.'_include'];
					$ids_array = wp_list_pluck( $objects_included, 'post_title', 'ID' );
				}
			}*/
			$table .= '<tbody>';
			foreach( $ids_array as $object_id ) {
				// get case settlement 
				$main_column = '';
				$case_areas = '';
				$placeholder = '-';
				$table .= '<tr>';
				switch ($post_name) {
					case 'case':
					if ( get_field( 'lp_case_settlement', $object_id ) ){
						$main_column = get_field( 'lp_case_settlement', $object_id );
					}
					$table .= '<td><a href="'.esc_url( get_permalink($object_id) ).'">$'.esc_html( $main_column ).'</a></td>';
					break;

					case 'practice_area':
					$main_column = get_the_title($object_id);
					$table .= '<td><a href="'.esc_url( get_permalink($object_id) ).'">'.esc_html( $main_column ).'</a></td>';
					break;

					case 'attorney':
					$main_column = get_the_title($object_id);
					$table .= '<td><a href="'.esc_url( get_permalink($object_id) ).'">'.esc_html( $main_column ).'</a></td>';
					break;
				}
				foreach ($secondary_columns as $key => $secondary_column) {
					$output = '';
					switch ($secondary_column) {

						// secondary case column
						case 'case':
						if ( $post_name == 'attorney' ){ 
							$cases = $this->lp_get_link_list_outside( $object_id, 'case', 'lp_case_attorneys' );
						}
						if ( $post_name == 'practice_area' ){ 
							$cases = $this->lp_get_link_list_outside( $object_id, 'case', 'lp_case_practice_areas' );	
						}
						$table .= '<td>'.$this->lp_escape_html( $cases ).'</td>';
						break;

						// secondary practice area column
						case 'practice_area':
						if ( $post_name == 'attorney'){ 
							$secondary_field_object_name = 'lp_attorney_practice_areas';
						}
						if ( $post_name == 'case'){ 
							$secondary_field_object_name = 'lp_case_areas';
						}
						$practice_areas = $this->lp_get_link_list( $secondary_field_object_name, $object_id);
						$table .= '<td>'.$this->lp_escape_html( $practice_areas ).'</td>';
						break;

						// secondary attorney column
						case 'attorney':
						if ( $post_name == 'case'){ 
							$attorneys = $this->lp_get_link_list( 'lp_case_attorneys', $object_id);
						}
						if ( $post_name == 'practice_area'){
							$attorneys = $this->lp_get_link_list_outside( $object_id, 'attorney', 'lp_attorney_practice_areas' );
						}
						$table .= '<td>'.$this->lp_escape_html( $attorneys ).'</td>';
						break;
					}
				}
				$table .= '</tr>';
			}
			$table .= '</tbody>';	
		}	
		$table .= '</table>';
		return $table;
	}

	/**
	 * Escape output html and save links, etc.
	 *
	 * @since    1.3.0
	 */
	public function lp_escape_html( $string ){
		$allowed_html = array(
			'a' => array(
				'href' => array(),
				'title' => array(),
				'target' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
		);
		return wp_kses( $string, $allowed_html );
	}

	/**
	 * Get Table
	 *
	 * @since    1.3.0
	 */
	public function lp_get_table( array $ids_array, $post_name, $shortcode_id, array $secondary_columns ){
		global $lawpress_options;
		$secondary_columns_default = array();
		$secondary_columns = array_merge($secondary_columns_default, $secondary_columns );

		$table = '';
		$table .= '<table class="lp-table">';
		
		// table headings
		$table .= '<thead>';
		$table .= '<tr>';
		switch ($post_name) {
			case 'case':
			$table .= '<th>'.__('Settlement', 'lawpress').'</th>';
			break;

			case 'practice_area':
			$table .= '<th>'.__('Area of Practice', 'lawpress').'</th>';
			break;

			case 'attorney':
			$table .= '<th>'.__('Attorney', 'lawpress').'</th>';
			break;
		}

		foreach ($secondary_columns as $key => $secondary_column) {
			switch ($secondary_column) {
				case 'case':
				$table .= '<th>'.__('Case(s)', 'lawpress').'</th>';
				break;

				case 'practice_area':
				$table .= '<th>'.__('Area(s) of Practice', 'lawpress').'</th>';
				break;

				case 'attorney':
				$table .= '<th>'.__('Attorney(s)', 'lawpress').'</th>';
				break;
			}
		}
		$table .= '</tr>';
		$table .= '</thead>';

		if ( class_exists('ACF') ){
			if ( get_field( 'lp_'.$post_name.'_settings', $shortcode_id ) ) {
				$table_settings = get_field( 'lp_'.$post_name.'_settings', $shortcode_id );
			}
			// if all
			if ( isset( $table_settings['lp_is_all_'.$post_name] ) && $table_settings['lp_is_all_'.$post_name] != 'all' ){
				if ( $table_settings['lp_'.$post_name.'_include'] ){
					$objects_included = $table_settings['lp_'.$post_name.'_include'];
					$ids_array = wp_list_pluck( $objects_included, 'post_title', 'ID' );
				}
			}
			$table .= '<tbody>';
			foreach( $ids_array as $object_id => $object_name ) {
				// get case settlement 
				$main_column = '';
				$case_areas = '';
				$placeholder = '-';
				$table .= '<tr>';
				switch ($post_name) {
					case 'case':
					if ( get_field( 'lp_case_settlement', $object_id ) ){
						$main_column = get_field( 'lp_case_settlement', $object_id );
					}
					$table .= '<td><a href="'.esc_url( get_permalink($object_id) ).'">$'.esc_html( $main_column ).'</a></td>';
					break;

					case 'practice_area':
					$main_column = get_the_title($object_id);
					$table .= '<td><a href="'.esc_url( get_permalink($object_id) ).'">'.esc_html( $main_column ).'</a></td>';
					break;

					case 'attorney':
					$main_column = get_the_title($object_id);
					$table .= '<td><a href="'.esc_url( get_permalink($object_id) ).'">'.esc_html( $main_column ).'</a></td>';
					break;
				}
				foreach ($secondary_columns as $key => $secondary_column) {
					$output = '';
					switch ($secondary_column) {

						// secondary case column
						case 'case':
						if ( $post_name == 'attorney' ){ 
							$cases = $this->lp_get_link_list_outside( $object_id, 'case', 'lp_case_attorneys' );
						}
						if ( $post_name == 'practice_area' ){ 
							$cases = $this->lp_get_link_list_outside( $object_id, 'case', 'lp_case_practice_areas' );	
						}
						$table .= '<td>'.$this->lp_escape_html( $cases ).'</td>';
						break;

						// secondary practice area column
						case 'practice_area':
						if ( $post_name == 'attorney'){ 
							$secondary_field_object_name = 'lp_attorney_practice_areas';
						}
						if ( $post_name == 'case'){ 
							$secondary_field_object_name = 'lp_case_areas';
						}
						$practice_areas = $this->lp_get_link_list( $secondary_field_object_name, $object_id);
						$table .= '<td>'.$this->lp_escape_html( $practice_areas ).'</td>';
						break;

						// secondary attorney column
						case 'attorney':
						if ( $post_name == 'case'){ 
							$attorneys = $this->lp_get_link_list( 'lp_case_attorneys', $object_id);
						}
						if ( $post_name == 'practice_area'){
							$attorneys = $this->lp_get_link_list_outside( $object_id, 'attorney', 'lp_attorney_practice_areas' );
						}
						$table .= '<td>'.$this->lp_escape_html( $attorneys ).'</td>';
						break;
					}
				}
				$table .= '</tr>';
			}
			$table .= '</tbody>';	
		}	
		$table .= '</table>';
		return $table;
	}

}


