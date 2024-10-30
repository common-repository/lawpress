<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://businessupwebsite.com
 * @since      1.0.0
 *
 * @package    Lawpress
 * @subpackage Lawpress/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Lawpress
 * @subpackage Lawpress/includes
 * @author     Ivan Chernyakov <admin@businessupwebsite.com>
 */
class Lawpress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Lawpress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'LAWPRESS_VERSION' ) ) {
			$this->version = LAWPRESS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'lawpress';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Lawpress_Loader. Orchestrates the hooks of the plugin.
	 * - Lawpress_i18n. Defines internationalization functionality.
	 * - Lawpress_Admin. Defines all hooks for the admin area.
	 * - Lawpress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lawpress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lawpress-i18n.php';

		/**
		 * The class responsible for required plugins.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tgm-plugin-activation.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-lawpress-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-lawpress-public.php';

		$this->loader = new Lawpress_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Lawpress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Lawpress_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @since    1.2.3 - tgmpa - priority fix
	 * @since    1.3.2 - added admin notices
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Lawpress_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// rewrite permalinks
		$this->loader->add_action( 'admin_init', $plugin_admin, 'rewrite_rules' );


		// get options data
		$this->loader->add_action( 'init', $plugin_admin, 'aqpanel_get_data' );
		// add main options page - Lawpress
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'lp_add_plugin_admin_menu' );
		// main options page - settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_register_settings' );


		// required install plugins
		$theme = wp_get_theme(); // gets the current theme
		if ( ('LawPress Classic Theme' != $theme->name ) && ('LawPress Classic Theme' != $theme->parent_theme ) && ('LawPress Solid' != $theme->name ) && ('LawPress Solid' != $theme->parent_theme ) ){
			$this->loader->add_action( 'tgmpa_register', $plugin_admin, 'lawpress_register_required_plugins' );
		}

		// disable gutenberg
		$this->loader->add_action( 'gutenberg_can_edit_post_type', $plugin_admin, 'can_edit_post_type', 10, 2 );
		$this->loader->add_action( 'use_block_editor_for_post_type', $plugin_admin, 'can_edit_post_type', 10, 2 );

		// add post types
		$this->loader->add_action( 'init', $plugin_admin, 'lawpress_register_post_types');

		//acf options
		$this->loader->add_action( 'init', $plugin_admin, 'lawpress_acf_add_options' );


		// acf dynamic (attorneys in areas)
		$this->loader->add_filter( 'acf/load_field/name=lp_attorney_practice_areas', $plugin_admin, 'lawpress_acf_load_practice_area_choices' );

		// acf dynamic (areas in cases)
		$this->loader->add_filter( 'acf/load_field/name=lp_case_practice_areas', $plugin_admin, 'lawpress_acf_load_practice_area_choices' );

		// acf dynamic (attorneys in cases)
		$this->loader->add_filter( 'acf/load_field/name=lp_case_attorneys', $plugin_admin, 'lawpress_acf_load_attorneys_choices' );	


		// Law Shortcodes
		$this->loader->add_action( 'init', $plugin_admin, 'lp_shortcode_post_type' );
		$this->loader->add_action( 'add_meta_boxes_lp_shortcode', $plugin_admin, 'lp_shortcode_metabox' );

		// admin notices
		$this->loader->add_action( 'admin_print_styles', $plugin_admin, 'add_notices' );
		$this->loader->add_action( 'switch_theme', $plugin_admin, 'reset_notices' );
		

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Lawpress_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// practice area
		$this->loader->add_filter( 'the_content', $plugin_public, 'lp_filter_content' );

		// add law shortcode
		$this->loader->add_shortcode( 'lp_shortcode', $plugin_public, 'law_shortcode_return' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Lawpress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
