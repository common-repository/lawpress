<?php

/**
 * Fired during plugin activation
 *
 * @link       https://businessupwebsite.com
 * @since      1.0.0
 *
 * @package    Lawpress
 * @subpackage Lawpress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Lawpress
 * @subpackage Lawpress/includes
 * @author     Ivan Chernyakov <admin@businessupwebsite.com>
 */
class Lawpress_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		flush_rewrite_rules();
		$next_day = time() + (24 * 60 * 60);
		update_option( 'lawpress_its_review_time', $next_day );
		update_option( 'lawpress_review_notice', 'true' );
	}

}
