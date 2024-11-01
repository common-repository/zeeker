<?php
/**
 * Plugin language definition.
 *
 * @since      1.0.0
 *
 * @package    Zeeker
 * @subpackage Zeeker/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin i18n class.
 *
 * @since 1.0.0
 * @package    Zeeker
 * @subpackage Zeeker/includes
 * @author     Zeeker Team <admin@zeeker.com>
 */
class Zeeker_I18n {

	/**
	 * Zeeker_I18n class constructor.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name   Name of the plugin.
	 * @param string $plugin_version    Version of the pluign.
	 */
	public function __construct( $plugin_name, $plugin_version ) {
		load_plugin_textdomain(
			$plugin_name,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

}
