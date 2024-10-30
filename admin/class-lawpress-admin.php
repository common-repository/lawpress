<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://businessupwebsite.com
 * @since      1.0.0
 *
 * @package    Lawpress
 * @subpackage Lawpress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lawpress
 * @subpackage Lawpress/admin
 * @author     Ivan Chernyakov <admin@businessupwebsite.com>
 */
class Lawpress_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lawpress-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lawpress-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register required plugins.
	 *
	 * @since    1.0.0
	 */
	public function lawpress_register_required_plugins() {	
		$plugins = array(
			array(
				'name'      => 'Advanced Custom Fields',
				'slug'      => 'advanced-custom-fields',
				'required'  => true,
			),
		);

		$config = array(
			'id'           => 'lawpress',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php',            // Parent menu slug.
			'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			'strings'      => array(
				'notice_can_install_required'     => _n_noop(
					/* translators: 1: plugin name(s). */
					'LawPress plugin requires the following plugin: %1$s.',
					'LawPress plugin requires the following plugins: %1$s.',
					'lawpress'
				)
			),
		);
		tgmpa( $plugins, $config );
	}

	/**
	 * Modify Gutenberg behavior for custom post types.
	 *
	 * @since    1.0.0
	 */
	public function can_edit_post_type( $enabled, $post_type ) {
		return ( $post_type == 'lp_practice_area' || $post_type == 'lp_attorney' || $post_type == 'lp_case') ? false : $enabled;
	}
	
	/**
	 * Practice areas dynamicly choices
	 *
	 * @since    1.0.0
	 * @since    1.2.0 - added cases
	 */
	public function lawpress_acf_load_practice_area_choices( $field ) {
		
	    // reset choices
		$field['choices'] = array();		
		$post_type_query  = new WP_Query(  
			array (  
				'post_type'      => 'lp_practice_area',  
				'posts_per_page' => -1,
				'post_status'	 => 'publish'
			)  
		);   
		$posts_array      = $post_type_query->posts;   
		$post_title_array = wp_list_pluck( $posts_array, 'post_title', 'ID' );
		if( is_array($post_title_array) ) {
			
			foreach( $post_title_array as $post_id => $post_title ) {
				
				$field['choices'][ $post_id ] = $post_title;
				
			}
			
		}
		wp_reset_postdata();

	    // return the field
		return $field;
		
	}

	/**
	 * Attorneys dynamicly choices
	 *
	 * @since    1.2.0
	 */
	public function lawpress_acf_load_attorneys_choices( $field ) {
		
	    // reset choices
		$field['choices'] = array();		
		$post_type_query  = new WP_Query(  
			array (  
				'post_type'      => 'lp_attorney',  
				'posts_per_page' => -1,
				'post_status'	 => 'publish'
			)  
		);   
		$posts_array      = $post_type_query->posts;   
		$post_title_array = wp_list_pluck( $posts_array, 'post_title', 'ID' );
		if( is_array($post_title_array) ) {
			
			foreach( $post_title_array as $post_id => $post_title ) {
				
				$field['choices'][ $post_id ] = $post_title;
				
			}
			
		}
		wp_reset_postdata();

	    // return the field
		return $field;
		
	}

	public function lawpress_register_post_types() {

		/**
		 * Post Type: Practice Areas.
		 */

		$labels = array(
			"name" => __( "Practice Areas", "lawpress" ),
			"singular_name" => __( "Practice Area", "lawpress" ),
		);

		$args = array(
			"label" => __( "Practice Areas", "lawpress" ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"delete_with_user" => false,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"has_archive" => "practice-areas",
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "practice-area", "with_front" => true ),
			"query_var" => true,
			"supports" => array( "title", "editor", "thumbnail" ),
			"menu_icon"	=> 'dashicons-book-alt'
		);

		register_post_type( "lp_practice_area", $args );

		/**
		 * Post Type: Cases.
		 */

		$labels = array(
			"name" => __( "Cases", "lawpress" ),
			"singular_name" => __( "Case", "lawpress" ),
		);

		$args = array(
			"label" => __( "Cases", "lawpress" ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"delete_with_user" => false,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"has_archive" => "cases",
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "case", "with_front" => true ),
			"query_var" => true,
			"supports" => array( "title", "editor", "thumbnail" ),
			"menu_icon"	=> 'dashicons-portfolio'
		);

		register_post_type( "lp_case", $args );

		/**
		 * Post Type: Attorneys.
		 */

		$labels = array(
			"name" => __( "Attorneys", "lawpress" ),
			"singular_name" => __( "Attorney", "lawpress" ),
		);

		$args = array(
			"label" => __( "Attorneys", "lawpress" ),
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"delete_with_user" => false,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"has_archive" => "attorneys",
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "attorney", "with_front" => true ),
			"query_var" => true,
			"supports" => array( "title", "editor", "thumbnail" ),
			"menu_icon"	=>	'dashicons-admin-users'
		);

		register_post_type( "lp_attorney", $args );

	}


	/**
	 * Add acf options.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 - added law shortcodes
	 * @since 1.3.1 - added table shortcode type
	 * @since 1.3.4 - extensions compatibility
	 * @since 1.3.5 - extensions compatibility
	 */
	public function lawpress_acf_add_options($post ){
		if( function_exists('acf_add_local_field_group') ):

			$choices = array(
				'lp_practice_area' => 'Practice Area',
				'lp_attorney' => 'Attorney',
				'lp_case' => 'Case',
			);

			if ( class_exists( 'Lawpress_Testimonials' ) ){
				$choices['lp_testimonial'] = 'Testimonial';
			}

			if ( class_exists( 'Lawpress_Locations' ) ){
				$choices['lp_location'] = 'Location';
			}


			acf_add_local_field_group(array(
				'key' => 'group_5d4eaf62b6daa',
				'title' => 'Attorney Details',
				'fields' => array(
					array(
						'key' => 'field_5d4eb144f7042',
						'label' => 'Profession',
						'name' => 'lp_attorney_niche',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => 'Attorney',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5d4eafd2f7040',
						'label' => 'Phone',
						'name' => 'lp_attorney_phone',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '-',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5d4eb114f7041',
						'label' => 'Email',
						'name' => 'lp_attorney_email',
						'type' => 'email',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '-',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_5d4eaf80f703f',
						'label' => 'Areas of practice',
						'name' => 'lp_attorney_practice_areas',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
						),
						'default_value' => array(
						),
						'allow_null' => 0,
						'multiple' => 1,
						'ui' => 1,
						'ajax' => 0,
						'return_format' => 'value',
						'placeholder' => '',
					),
					array(
						'key' => 'field_5d4eb19ff7043',
						'label' => 'More Fields',
						'name' => '',
						'type' => 'message',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'message' => '<i>- Education
						- Membership
						and more...

						<b>Coming Soon</b></i>',
						'new_lines' => 'wpautop',
						'esc_html' => 0,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'lp_attorney',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));

			acf_add_local_field_group(array(
				'key' => 'group_5d62e624e87aa',
				'title' => 'Attorney Social Links',
				'fields' => array(
					array(
						'key' => 'field_5d62e636b8c7c',
						'label' => 'Facebook',
						'name' => 'lp_attorney_facebook',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'maxlength' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
					),
					array(
						'key' => 'field_5d62e65cb8c7d',
						'label' => 'Twitter',
						'name' => 'lp_attorney_twitter',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
					array(
						'key' => 'field_5d62e669b8c7e',
						'label' => 'Linkedin',
						'name' => 'lp_attorney_linkedin',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'maxlength' => '',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'lp_attorney',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
			
			acf_add_local_field_group(array(
				'key' => 'group_5d4e0817b1203',
				'title' => 'Practice Area Details',
				'fields' => array(
					array(
						'key' => 'field_5d4ead8784772',
						'label' => 'Cases Closed',
						'name' => 'lp_practice_area_cases_closed',
						'type' => 'number',
						'instructions' => 'Number',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '-',
						'prepend' => '',
						'append' => '',
						'min' => 0,
						'max' => '',
						'step' => 1,
					),
					array(
						'key' => 'field_5d4eadc584773',
						'label' => 'Successful Cases',
						'name' => 'lp_practice_area_successful_cases',
						'type' => 'number',
						'instructions' => '%',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '-',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5d4eae1e84774',
						'label' => 'Experience',
						'name' => 'lp_practice_area_experience',
						'type' => 'number',
						'instructions' => 'Years',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '-',
						'prepend' => '',
						'append' => '',
						'min' => 0,
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5d4eec4494bf4',
						'label' => 'Hide Thumbnail',
						'name' => 'lp_practice_area_hide_thumbnail',
						'type' => 'checkbox',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'yes' => 'Yes',
						),
						'allow_custom' => 0,
						'default_value' => array(
						),
						'layout' => 'vertical',
						'toggle' => 0,
						'return_format' => 'value',
						'save_custom' => 0,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'lp_practice_area',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));

			acf_add_local_field_group(array(
				'key' => 'group_5d594287acb34',
				'title' => 'Case Details',
				'fields' => array(
					array(
						'key' => 'field_5d59429468936',
						'label' => 'Practice Areas',
						'name' => 'lp_case_practice_areas',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
						),
						'default_value' => array(
						),
						'allow_null' => 0,
						'multiple' => 1,
						'ui' => 1,
						'ajax' => 0,
						'return_format' => 'value',
						'placeholder' => '',
					),
					array(
						'key' => 'field_5d5942c068937',
						'label' => 'Attorneys',
						'name' => 'lp_case_attorneys',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
						),
						'default_value' => array(
						),
						'allow_null' => 0,
						'multiple' => 1,
						'ui' => 1,
						'ajax' => 0,
						'return_format' => 'value',
						'placeholder' => '',
					),
					array(
						'key' => 'field_5d5951a07ee81',
						'label' => 'Settlement',
						'name' => 'lp_case_settlement',
						'type' => 'number',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_5d59a13aa0ddc',
						'label' => 'Hide Thumbnail',
						'name' => 'lp_case_hide_thumbnail',
						'type' => 'checkbox',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'yes' => 'Yes',
						),
						'allow_custom' => 0,
						'default_value' => array(
						),
						'layout' => 'vertical',
						'toggle' => 0,
						'return_format' => 'value',
						'save_custom' => 0,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'lp_case',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'side',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));

			acf_add_local_field_group(array(
				'key' => 'group_5d542dcf603f9',
				'title' => 'Shortcode Details',
				'fields' => array(
					array(
						'key' => 'field_5d542e5d932da',
						'label' => 'Shortcode Type',
						'name' => 'lp_shortcode_type',
						'type' => 'button_group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => $choices,
						'allow_null' => 0,
						'default_value' => '',
						'layout' => 'horizontal',
						'return_format' => 'value',
					),
					array(
						'key' => 'field_5d544b91fb417',
						'label' => 'Practice Area Settings',
						'name' => 'lp_practice_area_settings',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5d542e5d932da',
									'operator' => '==',
									'value' => 'lp_practice_area',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'sub_fields' => array(
							array(
								'key' => 'field_5d544b91fb418',
								'label' => 'Include',
								'name' => 'lp_practice_areas_is_all',
								'type' => 'button_group',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'all' => 'All',
									'custom_list' => 'Custom List',
								),
								'allow_null' => 0,
								'default_value' => '',
								'layout' => 'horizontal',
								'return_format' => 'value',
							),
							array(
								'key' => 'field_5d544b91fb419',
								'label' => 'Practice Areas Include',
								'name' => 'lp_practice_areas_include',
								'type' => 'post_object',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_5d544b91fb418',
											'operator' => '==',
											'value' => 'custom_list',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'post_type' => array(
									0 => 'lp_practice_area',
								),
								'taxonomy' => '',
								'allow_null' => 0,
								'multiple' => 1,
								'return_format' => 'object',
								'ui' => 1,
							),
						),
					),
					array(
						'key' => 'field_5d542f7e1526f',
						'label' => 'Attorney Settings',
						'name' => 'lp_attorney_settings',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5d542e5d932da',
									'operator' => '==',
									'value' => 'lp_attorney',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'sub_fields' => array(
							array(
								'key' => 'field_5d5447309355a',
								'label' => 'Include',
								'name' => 'lp_is_all_attorneys',
								'type' => 'button_group',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'all' => 'All',
									'custom_list' => 'Custom List',
								),
								'allow_null' => 0,
								'default_value' => '',
								'layout' => 'horizontal',
								'return_format' => 'value',
							),
							array(
								'key' => 'field_5d542f2250974',
								'label' => 'Attorney include',
								'name' => 'lp_attorney_include',
								'type' => 'post_object',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_5d5447309355a',
											'operator' => '==',
											'value' => 'custom_list',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'post_type' => array(
									0 => 'lp_attorney',
								),
								'taxonomy' => '',
								'allow_null' => 0,
								'multiple' => 1,
								'return_format' => 'object',
								'ui' => 1,
							),
						),
					),
					array(
						'key' => 'field_5d59561944e79',
						'label' => 'Case Settings',
						'name' => 'lp_case_settings',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5d542e5d932da',
									'operator' => '==',
									'value' => 'lp_case',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'sub_fields' => array(
							array(
								'key' => 'field_5d59561944e7a',
								'label' => 'Include',
								'name' => 'lp_is_all_cases',
								'type' => 'button_group',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'all' => 'All',
									'custom_list' => 'Custom List',
								),
								'allow_null' => 0,
								'default_value' => '',
								'layout' => 'horizontal',
								'return_format' => 'value',
							),
							array(
								'key' => 'field_5d59561944e7b',
								'label' => 'Cases include',
								'name' => 'lp_case_include',
								'type' => 'post_object',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_5d59561944e7a',
											'operator' => '==',
											'value' => 'custom_list',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'post_type' => array(
									0 => 'lp_case',
								),
								'taxonomy' => '',
								'allow_null' => 0,
								'multiple' => 1,
								'return_format' => 'object',
								'ui' => 1,
							),
						),
					),
					array(
						'key' => 'field_5d65afe223fd5',
						'label' => 'Testimonial Settings',
						'name' => 'lp_testimonial_settings',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5d542e5d932da',
									'operator' => '==',
									'value' => 'lp_testimonial',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'sub_fields' => array(
							array(
								'key' => 'field_5d65afe223fd6',
								'label' => 'Include',
								'name' => 'lp_is_all_testimonials',
								'type' => 'button_group',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'all' => 'All',
									'custom_list' => 'Custom List',
								),
								'allow_null' => 0,
								'default_value' => '',
								'layout' => 'horizontal',
								'return_format' => 'value',
							),
							array(
								'key' => 'field_5d65afe223fd7',
								'label' => 'Cases include',
								'name' => 'lp_testimonial_include',
								'type' => 'post_object',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_5d65afe223fd6',
											'operator' => '==',
											'value' => 'custom_list',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'post_type' => array(
									0 => 'lp_case',
								),
								'taxonomy' => '',
								'allow_null' => 0,
								'multiple' => 1,
								'return_format' => 'object',
								'ui' => 1,
							),
						),
					),
					array(
						'key' => 'field_5d685f917d370',
						'label' => 'Location Settings',
						'name' => 'lp_location_settings',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5d542e5d932da',
									'operator' => '==',
									'value' => 'lp_location',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'sub_fields' => array(
							array(
								'key' => 'field_5d685f917d371',
								'label' => 'Include',
								'name' => 'lp_is_all_locations',
								'type' => 'button_group',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'all' => 'All',
									'custom_list' => 'Custom List',
								),
								'allow_null' => 0,
								'default_value' => '',
								'layout' => 'horizontal',
								'return_format' => 'value',
							),
							array(
								'key' => 'field_5d685f917d372',
								'label' => 'Location include',
								'name' => 'lp_location_include',
								'type' => 'post_object',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_5d685f917d371',
											'operator' => '==',
											'value' => 'custom_list',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'post_type' => array(
									0 => 'lp_location',
								),
								'taxonomy' => '',
								'allow_null' => 0,
								'multiple' => 1,
								'return_format' => 'object',
								'ui' => 1,
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'lp_shortcode',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			));
			
			if ( ! class_exists('Lawpress_Carousel') ) :
				acf_add_local_field_group(array(
					'key' => 'group_5d5448920b59d',
					'title' => 'Shortcode General Details',
					'fields' => array(
						array(
							'key' => 'field_5d5448bbae724',
							'label' => 'Display Type',
							'name' => 'lp_display_type',
							'type' => 'button_group',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'choices' => array(
								'grid' => 'Grid',
								'table' => 'Table',
							),
							'allow_null' => 0,
							'default_value' => '',
							'layout' => 'horizontal',
							'return_format' => 'value',
						),
						array(
							'key' => 'field_5d55e99571c30',
							'label' => 'More display types',
							'name' => '',
							'type' => 'message',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'message' => 'Carousel type is available with <a href="'.esc_url( admin_url('admin.php?page=lawpress-addons') ).'">Carousel Extension</a>.',
							'new_lines' => 'wpautop',
							'esc_html' => 0,
						),
					),
					'location' => array(
						array(
							array(
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'lp_shortcode',
							),
						),
					),
					'menu_order' => 0,
					'position' => 'side',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
				));
			endif;

		endif;
	}

	/**
	 * Rewrite permalinks
	 *
	 * @since 1.0.1
	 */
	public function rewrite_rules(){
		flush_rewrite_rules();
	}

	/**
	 * Add menu page
	 *
	 * @since 1.0.0
	 * @since 1.4.0 - location submenu
	 */
	//public function add_page() {

		// Name
		/*$name = esc_html__( 'Law Shortcodes', 'lawpress' );

		add_menu_page(
			esc_html__( 'Law Shortcodes', 'lawpress' ),
			$name,
			'manage_options',
			'edit.php?post_type=lp_shortcode',
			'',
			'',
			50
		);*/

	//}

	/**
	 * Register Law Shortcode post type
	 *
	 * @since 1.1.0
	 */
	public static function lp_shortcode_post_type() {

		// Name
		$name = esc_html__( 'My Law Shortcodes', 'lawpress' );

		// Register the post type
		register_post_type( 'lp_shortcode', array(
			'labels' => array(
				'name' 					=> $name,
				'singular_name' 		=> esc_html__( 'Shortcode', 'lawpress' ),
				'add_new' 				=> esc_html__( 'Add New', 'lawpress' ),
				'add_new_item' 			=> esc_html__( 'Add New Shortcode', 'lawpress' ),
				'edit_item' 			=> esc_html__( 'Edit Shortcode', 'lawpress' ),
				'new_item' 				=> esc_html__( 'Add New Shortcode', 'lawpress' ),
				'view_item' 			=> esc_html__( 'View Shortcode', 'lawpress' ),
				'search_items' 			=> esc_html__( 'Search Shortcode', 'lawpress' ),
				'not_found' 			=> esc_html__( 'No Shortcodes Found', 'lawpress' ),
				'not_found_in_trash' 	=> esc_html__( 'No Shortcodes Found In Trash', 'lawpress' ),
				'menu_name' 			=> esc_html__( 'Law Shortcodes', 'lawpress' ),
			),
			'public' 					=> false,
			'hierarchical'          	=> false,
			'show_ui'               	=> true,
			'show_in_menu' 				=> true,
			'show_in_nav_menus'     	=> false,
			'can_export'            	=> true,
			'exclude_from_search'   	=> true,
			'capability_type' 			=> 'post',
			'rewrite' 					=> false,
			'supports' 					=> array( 'title', 'author' ),
			'menu_icon'					=> 'dashicons-paperclip'

		) );

	}

	/*function acf_load_taxonomy_choices( $field ) {

	    // reset choices
		$field['choices'] = get_taxonomies();

	    // return the field
		return $field;

	}*/

	/**
	 * Add shorcode metabox
	 *
	 * @since 1.1.0
	 */
	public static function lp_shortcode_metabox( $post ) {

		add_meta_box(
			'lp-shortcode-shortcode-metabox',
			esc_html__( 'Shortcode', 'lawpress' ),
			array( 'Lawpress_Admin', 'display_metabox' ),
			'lp_shortcode',
			'side',
			'low'
		);

	}

	/**
	 * Add shorcode metabox
	 *
	 * @since 1.1.0
	 */
	public static function display_metabox( $post ) { ?>

		<input type="text" class="widefat" value='[lp_shortcode id="<?php echo $post->ID; ?>"]' readonly />

	<?php
	}

	/**
     * Get data - $lawpress_options
     *
     * @since 1.2.0
     */
	public function aqpanel_get_data(){
		global $lawpress_options;
		$default_options = array(
			'lawpress_main' => array( 
				'info_icons' => 1,
			),
		);
		$lawpress_options = array_merge( $default_options , (array)get_option( 'lawpress_plugin_options', array() ) );
	}

	/**
     * Register the options menu.
     *
     * @since 1.2.0
     */
	public function lp_add_plugin_admin_menu()
	{
		$this->plugin_screen_hook_suffix = add_menu_page(
			__('LawPress', 'lawpress'), __('LawPress', 'lawpress'), 'manage_options', 'lawpress', array($this, 'display_plugin_admin_page'), 'dashicons-admin-site-alt3', 25
		);         
	}

	/**
     * Render the settings page for this plugin.
     *
     * @since 1.2.0
     */
	public function display_plugin_admin_page()
	{
	        // Check that the user is allowed to update options
		if (!current_user_can('manage_options'))
		{
			wp_die('You do not have sufficient permissions to access this page.');
		}
		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'lawpress-plugin-options-group' ); ?>
			<?php do_settings_sections( 'lawpress-settings-main' ); ?>
			<?php submit_button( __( 'Save Changes', 'lawpress' ) ); ?>
			<?php do_settings_sections( 'lawpress-info' ); ?>		
		</form>
		<?php
	}

	/**
	 * Plugin settings
	 *
     * @since 1.2.0
     * @since 1.4.1 - info renamed to links
	 */
	public function plugin_register_settings()
	{
		register_setting('lawpress-plugin-options-group', 'lawpress_plugin_options', array( $this, 'lawpress_plugin_options_validate') );
		add_settings_section('main_settings', __( 'LawPress Main Settings', 'lawpress' ) , array($this, 'lawpress_plugin_section_text_callback'), 'lawpress-settings-main');

		// Attorneys
		add_settings_field('lawpress_main', __( 'Modules', 'lawpress' ), array($this, 'lawpress_main_callback'), 'lawpress-settings-main', 'main_settings');

		// info
		add_settings_section('main_settings', __( 'Links', 'lawpress' ) , array($this, 'lawpress_info_callback'), 'lawpress-info');
	}

	/**
	 * Options description.
	 *
     * @since 1.2.0
     * @since 1.4.1 - added new links
	 */
    public function lawpress_info_callback($description) {
    	echo '
    	<div class="lawpress-info">
    		Documentation: <ul>
    		<li><a href="https://docs.businessupwebsite.com/docs/lawpress-plugin/dummy-data-import/" target="_blank">Dummy-data Import </a></li>
    		<li><a href="https://docs.businessupwebsite.com/docs/lawpress-lite-theme/home-page/" target="_blank">Home Page Setup (LawPress Lite Theme)</a></li></ul>
	    	Themes compatible with LawPress plugin: 
	    	<ul>
	    		<li><a href="https://businessupwebsite.com/themes/lawpress-lite/" target="_blank">LawPress Lite Theme (FREE)</a></li>
	    		<li><a href="https://businessupwebsite.com/themes/lawpress-solid/" target="_blank">LawPress Solid</a></li>
	    		<li><a href="https://businessupwebsite.com/themes/lawpress-classic/" target="_blank">LawPress Classic</a></li>
	    	</ul>
	    	All Add-ons Bundle here: <ul><li><a href="https://businessupwebsite.com/lawpress-all-add-ons/" target="_blank">Add-ons bundle</a></li></ul>
    	</div>';
    }

	/**
	 * Add empty array if doesn't exist.
	 *
     * @since 1.2.0
	 */
	public function lawpress_plugin_options_validate($input) {
		if ( ! isset( $input['lawpress_main'] ) ) {
			$input['lawpress_main'] = array();
		}
		return $input;
	}	

	/**
	 * Options description.
	 *
     * @since 1.2.0
	 */
	public function lawpress_plugin_section_text_callback($description) {
		echo "Edit you main display settings here.";
	}

	/**
	 * Post Types.
	 *
   * @since 1.2.0
   * @since 1.4.0 - Added additional modules info.
	 */
	public function lawpress_main_callback() {
		global $lawpress_options;
		?>
		<p>
			<label><input type="checkbox" name="lawpress_plugin_options[lawpress_main][info_icons]" value="1"
				<?php checked(isset($lawpress_options['lawpress_main']['info_icons']) && 1 == $lawpress_options['lawpress_main']['info_icons']); ?> /><?php _e("Info Icons",  'lawpress' ); ?>
			</label>
			<br><br>
			<label><?php _e("Advanced Settings",  'lawpress' ); ?> - 
				<?php if ( ! class_exists('LawPress_Advanced') ) : ?>
					<a href="https://businessupwebsite.com/lawpress-all-add-ons/" target="_blank"><?php _e("Premium Add-ons",  'lawpress' ); ?></a>
				<?php else : ?>
					<b style="color: #0b0; "><?php _e("Active",  'lawpress' ); ?></b>
				<?php endif; ?>
			</label>
			<br><br>
			<label><?php _e("Carousel",  'lawpress' ); ?> - 
				<?php if ( ! class_exists('LawPress_Carousel') ) : ?>
					<a href="https://businessupwebsite.com/lawpress-all-add-ons/" target="_blank"><?php _e("Premium Add-ons",  'lawpress' ); ?></a>
				<?php else : ?>
					<b style="color: #0b0; "><?php _e("Active",  'lawpress' ); ?></b>
				<?php endif; ?>
			</label>
			<br><br>
			<label><?php _e("Testimonials",  'lawpress' ); ?> - 
				<?php if ( ! class_exists('LawPress_Testimonials') ) : ?>
					<a href="https://businessupwebsite.com/lawpress-all-add-ons/" target="_blank"><?php _e("Premium Add-ons",  'lawpress' ); ?></a>
				<?php else : ?>
					<b style="color: #0b0; "><?php _e("Active",  'lawpress' ); ?></b>
				<?php endif; ?>
			</label>
			<br><br>
			<label><?php _e("Locations",  'lawpress' ); ?> - 
				<?php if ( ! class_exists('LawPress_Locations') ) : ?>
					<a href="https://businessupwebsite.com/lawpress-all-add-ons/" target="_blank"><?php _e("Premium Add-ons",  'lawpress' ); ?></a>
				<?php else : ?>
					<b style="color: #0b0; "><?php _e("Active",  'lawpress' ); ?></b>
				<?php endif; ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Notice html.
	 *
     * @since 1.3.2
	 */
	public function lawpress_recommended_theme() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'Current theme does not contain special styles for the LawPress elements. Please install a FREE <a href="https://businessupwebsite.com/themes/lawpress-lite/" target="_blank">LawPress Lite Theme.</a>', 'lawpress' ); ?></p>
			<p class="submit">
				<a class="button-primary" href="https://businessupwebsite.com/themes/lawpress-lite/" target="_blank"><?php _e( 'Download Theme', 'lawpress' ); ?></a>
				<a class="button" href="<?php echo add_query_arg( 'hide_theme_notice', 'true' ); ?>"><?php _e( 'Hide notice', 'lawpress' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Request review.
	 *
     * @since 1.3.3
	 */
	public function lawpress_request_review() {
		?>
		<div class="notice notice-info is-dismissible">
			<p><?php _e( 'Thank you for using <b>LawPress</b> plugin. Please leave a review on the plugin page so that we can do it better. Your feedback is very important for us.', 'sample-text-domain' ); ?></p>
			<p class="submit">
				<a class="button-primary" href="https://wordpress.org/support/plugin/lawpress/reviews/#new-post" target="_blank"><?php _e( '<span class="dashicons dashicons-star-filled"></span> Leave Review', 'lawpress' ); ?></a>
				<a class="button" href="<?php echo add_query_arg( 'hide_review_notice', 'true' ); ?>"><?php _e( "Already did", 'lawpress' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Reset Notices.
	 *
     * @since 1.3.2
	 */
	public function reset_notices() {
		update_option( 'lawpress_theme_notice', false );
	}

	/**
	 * Admin Notices.
	 *
     * @since 1.3.2
     * @since 1.3.3 - Added review request
	 */
	public function add_notices() {
		$lawpress_theme_notice = get_option('lawpress_theme_notice');
		$lawpress_review_notice = get_option('lawpress_review_notice');
		$lawpress_its_review_time = get_option('lawpress_its_review_time');
		
		if ( $lawpress_its_review_time == '' ){
			$next_day = time() + (24 * 60 * 60);
			update_option( 'lawpress_its_review_time', $next_day );
			$lawpress_its_review_time = $next_day;
		}
		if ( $lawpress_theme_notice == '' ){
			$lawpress_theme_notice = 'true';
		}
		if ( $lawpress_review_notice == '' ){
			$lawpress_review_notice = 'true';
		}
		if ( ! empty( $_GET['hide_theme_notice'] ) ) {
			update_option( 'lawpress_theme_notice', 'false' );
			$lawpress_theme_notice = 'false';
		}
		if ( ! empty( $_GET['hide_review_notice'] ) ) {
			update_option( 'lawpress_review_notice', 'false' );
			$lawpress_review_notice = 'false';
		}
		$template = get_option('template');
		if ( ! in_array( $template, array( 'lawpress-lite', 'lawpress-classic','lawpress-solid') ) && ( $lawpress_theme_notice == 'true' ) ) {
			add_action( 'admin_notices', array( $this, 'lawpress_recommended_theme') );
		}
		if ( ( $lawpress_review_notice == 'true' ) && ( time() >= $lawpress_its_review_time ) ){
			add_action( 'admin_notices', array( $this, 'lawpress_request_review') );
		}
		
	}
}
