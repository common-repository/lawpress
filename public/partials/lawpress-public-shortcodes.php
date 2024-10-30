<?php

/**
 * Law Shortcodes
 *
 * @link       https://businessupwebsite.com
 * @since      1.1.0
 * @since      1.1.1 - html structure
 * @since      1.2.0 - added info icons
 * @since      1.3.0 - added table shortcode type
 * @since      1.3.4 - Extensions compatibility
 * @since      1.3.5 - Extensions compatibility
 *
 * @package    Lawpress
 * @subpackage Lawpress/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $lawpress_options;

if ( $atts[ 'id' ] ) {
	// Get template content
	$shortcode_id = $atts[ 'id' ];
	if ( class_exists('ACF') ){
		if (get_field( 'lp_shortcode_type', $shortcode_id )){
			$post_type = get_field( 'lp_shortcode_type', $shortcode_id );

			// defaul vals
			$post_list_has_one = false;
			$post_list = '';
			$lp_attorney_settings = array();
			$lp_practice_area_settings = array();
			$lp_case_settings = array();
			$lp_display_type = 'grid';

			if ( get_field('lp_display_type', $shortcode_id ) ) {
				$lp_display_type = get_field('lp_display_type', $shortcode_id );
			}
			
			// query for your post type
			if ( $post_type == 'lp_case' ){
				$post_type_query  = new WP_Query(  
					array (  
						'post_type'      => $post_type,  
						'posts_per_page' => -1,
						'meta_key'			=> 'lp_case_settlement',
						'orderby'			=> 'meta_value',
						'order'				=> 'ASC' 
					)  
				);   
			} 
			else{
				$post_type_query  = new WP_Query(  
					array (  
						'post_type'      => $post_type,  
						'posts_per_page' => -1
					)  
				); 
			}

			$posts_array = $post_type_query->posts;   
			/*echo '<pre>';
			print_r($posts_array);
			echo '</pre>';*/
			$ids_array = wp_list_pluck( $posts_array, 'post_title', 'ID' );	

			if( is_array($ids_array) ) {
				$post_list_has_one = true;
				switch ($lp_display_type) {

					// grid type
					case 'grid':
						/* 
						*	loop of attorneys
						*/
						if ( $post_type == 'lp_attorney' ){
							$lp_icon_args = array(
								'show_info_icon' 	=> true,
								'info_icon_type' 	=> 'case',    
								'icon_loop' 			=> true,
							);

							$theme = wp_get_theme(); // gets the current theme
							if (('LawPress Solid' == $theme->parent_theme) || ('LawPress Solid' == $theme->name) ){
								$lawpress = new Lawpress();
								$plugin_public_solid = new Lawpress_Public_Solid( $lawpress->get_plugin_name(), $lawpress->get_version() );
								$post_list = $plugin_public_solid->lp_get_grid_with_clickable_img( $ids_array, 'attorney', $shortcode_id, true, $subtitle = __('Attorney', 'lawpress'), $lp_icon_args );
							}
							else {
								$post_list = $this->lp_get_grid( $ids_array, 'attorney', $shortcode_id, true, $subtitle = __('Attorney', 'lawpress'), $lp_icon_args );
							}

						}

						/*
						* loop of practice area
						*/
						if ( $post_type == 'lp_practice_area' ){
							$lp_icon_args = array(
								'show_info_icon' 	=> true,
								'info_icon_type' 	=> 'case',    
								'icon_loop' 			=> true,
							);
							$post_list = $this->lp_get_grid( $ids_array, 'practice_area', $shortcode_id, false, $subtitle = '', $lp_icon_args );

							$theme = wp_get_theme(); // gets the current theme
							if (('LawPress Solid' == $theme->parent_theme) || ('LawPress Solid' == $theme->name) ){
								$lawpress = new Lawpress();
								$plugin_public_solid = new Lawpress_Public_Solid( $lawpress->get_plugin_name(), $lawpress->get_version() );
								$post_list = $plugin_public_solid->lp_get_grid_with_buttons( $ids_array, 'practice_area', $shortcode_id, false, $subtitle = '', $lp_icon_args );
							}
							else {
								$post_list = $this->lp_get_grid( $ids_array, 'practice_area', $shortcode_id, false, $subtitle = '', $lp_icon_args );
							}
						}

						/**
						 * Loop of Cases
						 */
						if ( $post_type == 'lp_case' ){
							$lp_icon_args = array(
								'show_info_icon' 		=> true,
								'info_icon_type' 		=> 'case',    
								'icon_loop' 				=> false,
								'info_icon_field' 	=> 'case_settlement',
								'icon_position' 		=> 'left',
								'icon' 							=> 'fa-dollar-sign'
							);

							$theme = wp_get_theme(); // gets the current theme
							if (('LawPress Solid' == $theme->parent_theme) || ('LawPress Solid' == $theme->name) ){
								$lawpress = new Lawpress();
								$plugin_public_solid = new Lawpress_Public_Solid( $lawpress->get_plugin_name(), $lawpress->get_version() );
								$post_list = $plugin_public_solid->lp_get_grid_with_clickable_img_cases( $ids_array, 'case', $shortcode_id, false, $subtitle = '', $lp_icon_args );
							}
							else {
								$post_list = $this->lp_get_grid( $ids_array, 'case', $shortcode_id, false, $subtitle = '', $lp_icon_args );
							}

						}

						/**
						 * Loop of Testimonials
						 */
						if ( $post_type == 'lp_testimonial' ){
							if ( class_exists('Lawpress_Testimonials') ){
								$lp_icon_args = array(
									'show_info_icon' 		=> true,
									'info_icon_type' 		=> 'case',    
									'icon_loop' 				=> false,
									'info_icon_field' 	=> 'case_settlement',
									'icon_position' 		=> 'left',
									'icon' 							=> 'fa-dollar-sign'
								);
								$lawpress_testimonials = new Lawpress_Testimonials();
								$lawpress_testimonials_public = new Lawpress_Testimonials_Public( $lawpress_testimonials->get_lawpress_testimonials(), $lawpress_testimonials->get_version() );
								$post_list = $lawpress_testimonials_public->lp_get_testimonials_grid( $ids_array, 'testimonial', $shortcode_id, false, $subtitle = '', $lp_icon_args );
							}
							else {
								$post_list = 'Please activate LawPress Testimonials Extension.';
							}
						}

						/**
						 * Loop of Locations
						 */
						if ( $post_type == 'lp_location' ){
							if ( class_exists('Lawpress_Locations') ){
								$lp_icon_args = array(
									'show_info_icon' 		=> true,
									'info_icon_type' 		=> 'location',    
									'icon_loop' 				=> false,
									'info_icon_field' 	=> 'practice_area',
									'icon_position' 		=> 'left',
									'icon' 							=> 'fa-book'
								);
								$lawpress_locations = new Lawpress_Locations();
								$lawpress_locations_public = new Lawpress_Locations_Public( $lawpress_locations->get_lawpress_locations(), $lawpress_locations->get_version() );
								$post_list = $lawpress_locations_public->lp_get_locations_grid( $ids_array, 'location', $shortcode_id, false, $subtitle = '', $lp_icon_args );
							}
							else {
								$post_list = 'Please activate LawPress Locations Extension.';
							}
						}

					break;

					// table type
					case 'table':
						/**
						 * Tables
						 */
						if ( $post_type == 'lp_case' ){
							$secondary_columns = array('practice_area', 'attorney');
							$post_list = $this->lp_get_table( $ids_array, 'case', $shortcode_id, $secondary_columns );
						}

						if ( $post_type == 'lp_attorney' ){
							$secondary_columns = array('practice_area', 'case');
							$post_list = $this->lp_get_table( $ids_array, 'attorney', $shortcode_id, $secondary_columns );
						}

						if ( $post_type == 'lp_practice_area' ){
							$secondary_columns = array('attorney', 'case');
							$post_list = $this->lp_get_table( $ids_array, 'practice_area', $shortcode_id, $secondary_columns );
						}

						/**
						 * Loop of Testimonials
						 */
						if ( $post_type == 'lp_testimonial' ){
							if ( class_exists('Lawpress_Testimonials') ){
								$secondary_columns = array( 'case' );
								$lawpress_testimonials = new Lawpress_Testimonials();
								$lawpress_testimonials_public = new Lawpress_Testimonials_Public( $lawpress_testimonials->get_lawpress_testimonials(), $lawpress_testimonials->get_version() );
								$post_list = $lawpress_testimonials_public->lp_get_testimonials_table( $ids_array, 'testimonial', $shortcode_id, $secondary_columns );
							}
							else {
								$post_list = 'Please activate LawPress Testimonials Extension.';
							}
						}

						/**
						 * Loop of Locations
						 */
						if ( $post_type == 'lp_location' ){
							if ( class_exists('Lawpress_Locations') ){
								$secondary_columns = array( 'practice_area' );
								$lawpress_locations = new Lawpress_Locations();
								$lawpress_locations_public = new Lawpress_Locations_Public( $lawpress_locations->get_lawpress_locations(), $lawpress_locations->get_version() );
								$post_list = $lawpress_locations_public->lp_get_locations_table( $ids_array, 'location', $shortcode_id, $secondary_columns );
							}
							else {
								$post_list = 'Please activate LawPress Locations Extension.';
							}
						}

					break;

					// carousel type
					case 'carousel':
						if ( class_exists('Lawpress_Carousel') ){

							$lawpress_carousel = new Lawpress_Carousel();
							$lawpress_carousel_public = new Lawpress_Carousel_Public( $lawpress_carousel->get_lawpress_carousel(), $lawpress_carousel->get_version() );
							/**
							 * Loop of Cases
							 */
							if ( $post_type == 'lp_case' ){
								$lp_icon_args = array(
									'show_info_icon' 		=> true,
									'info_icon_type' 		=> 'case',    
									'icon_loop' 				=> false,
									'info_icon_field' 	=> 'case_settlement',
									'icon_position' 		=> 'left',
									'icon' 							=> 'fa-dollar-sign'
								);

								$post_list = $lawpress_carousel_public->lp_get_carousel( $ids_array, 'case', $shortcode_id, false, $subtitle = '', $lp_icon_args );
							}
							/* 
							*	loop of attorneys
							*/
							if ( $post_type == 'lp_attorney' ){
								$lp_icon_args = array(
									'show_info_icon' 	=> true,
									'info_icon_type' 	=> 'case',    
									'icon_loop' 			=> true,
								);
								$post_list = $lawpress_carousel_public->lp_get_carousel( $ids_array, 'attorney', $shortcode_id, true, $subtitle = __('Attorney', 'lawpress'), $lp_icon_args );
							}

							/*
							* loop of practice area
							*/
							if ( $post_type == 'lp_practice_area' ){
								$lp_icon_args = array(
									'show_info_icon' 	=> true,
									'info_icon_type' 	=> 'case',    
									'icon_loop' 			=> true,
								);

								$theme = wp_get_theme(); // gets the current theme
								if (('LawPress Solid' == $theme->parent_theme) || ('LawPress Solid' == $theme->name) ){
									$lawpress_carousel = new Lawpress_Carousel();
									$plugin_carousel_public_solid = new Lawpress_Carousel_Public_Solid( $lawpress_carousel->get_lawpress_carousel(), $lawpress_carousel->get_version() );
									$post_list = $plugin_carousel_public_solid->lp_get_carousel_with_grid_buttons( $ids_array, 'practice_area', $shortcode_id, false, $subtitle = '', $lp_icon_args );
								}
								else {
									$post_list = $lawpress_carousel_public->lp_get_carousel( $ids_array, 'practice_area', $shortcode_id, false, $subtitle = '', $lp_icon_args );
								}
							}

							/**
							 * Loop of Testimonials
							 */
							if ( $post_type == 'lp_testimonial' ){
								if ( class_exists('Lawpress_Testimonials') ){
									$lawpress_testimonials = new Lawpress_Testimonials();
									$lawpress_testimonials_public = new Lawpress_Testimonials_Public( $lawpress_testimonials->get_lawpress_testimonials(), $lawpress_testimonials->get_version() );			
									$lp_icon_args = array(
										'show_info_icon' 	=> true,
										'info_icon_type' 	=> 'case',    
										'icon_loop' 			=> true,
										'icon_position' 		=> 'left',
										'icon' 							=> 'fa-dollar-sign'
									);
									$post_list = $lawpress_carousel_public->lp_get_carousel( $ids_array, 'testimonial', $shortcode_id, false, $subtitle = '', $lp_icon_args );
								}
								else {
									$post_list = 'Please activate LawPress Testimonials Extension.';
								}
							}

						}
						else {
							$post_list = 'Please activate LawPress Carousel Extension.';
						}
					break;
				}

				// if empty
				if ( ! $post_list_has_one ){
					$post_list = '';
				}
			}		
			echo $post_list;
			wp_reset_postdata();
		}
	}
}

