<?php
/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           Zeeker
 *
 * @wordpress-plugin
 * Plugin Name:       Zeeker: In-Page Box For Q&A, Reviews, Comments
 * Plugin URI:        https://www.zeeker.com/
 * Description:       Display a powerful engagement button on your website
 * Version:           2.0.1
 * Author:            Zeeker Team
 * Author URI:        https://www.zeeker.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zeeker
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'ZEEKER_VERSION', '1.0.1' );

/**
 * Current plugin constants.
 */
define( 'ZEEKER_BASENAME', plugin_basename( __FILE__ ) );
define( 'ZEEKER_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin activation.
 */
function zeeker_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zeeker-activator.php';
	Zeeker_Activator::activate();
}
register_activation_hook( __FILE__, 'zeeker_activate' );

/**
 * Plugin deactivation.
 */
function zeeker_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zeeker-deactivator.php';
	Zeeker_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'zeeker_deactivate' );

/**
 * The plugin core class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-zeeker.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function zeeker_run() {
	Zeeker::run();
}
zeeker_run();
