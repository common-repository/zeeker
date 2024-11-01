<?php
/**
 * Delete plugin option data upon plugin uninstalled (delete).
 *
 * @since             1.0.0
 * @package           Zeeker
 */

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( '_zeeker_options' );
delete_option( '_zeeker_widget_active' );

// for site options in Multi-site.
delete_site_option( '_zeeker_options' );
delete_site_option( '_zeeker_widget_active' );
