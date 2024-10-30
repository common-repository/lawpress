<?php

/**
 *
 * @link              https://businessupwebsite.com
 * @since             1.0.0
 * @package           Lawpress
 *
 * @wordpress-plugin
 * Plugin Name:       LawPress
 * Plugin URI:        https://wordpress.org/plugins/lawpress
 * Description:       LawPress is an all-in-one law data plugin that helps law firms manage site.
 * Version:           1.4.5
 * Author:            Ivan Chernyakov
 * Author URI:        https://businessupwebsite.com
 * License:			  GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       lawpress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'law_fs' ) ) {
    // Create a helper function for easy SDK access.
    function law_fs() {
        global $law_fs;

        if ( ! isset( $law_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $law_fs = fs_dynamic_init( array(
                'id'                  => '4448',
                'slug'                => 'lawpress',
                'type'                => 'plugin',
                'public_key'          => 'pk_0c07b9303a799878594bbb259e87e',
                'is_premium'          => false,
                'has_addons'          => true,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'lawpress',
                    'first-path'     => 'admin.php?page=lawpress',
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $law_fs;
    }

    // Init Freemius.
    law_fs();
    // Signal that SDK was initiated.
    do_action( 'law_fs_loaded' );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LAWPRESS_VERSION', '1.4.5' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lawpress-activator.php
 */
function activate_lawpress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lawpress-activator.php';
	Lawpress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lawpress-deactivator.php
 */
function deactivate_lawpress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lawpress-deactivator.php';
	Lawpress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lawpress' );
register_deactivation_hook( __FILE__, 'deactivate_lawpress' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lawpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lawpress() {

	$plugin = new Lawpress();
	$plugin->run();

}
run_lawpress();
