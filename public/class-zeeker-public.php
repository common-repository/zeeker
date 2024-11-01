<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Zeeker
 * @subpackage Zeeker/public
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Zeeker
 * @subpackage Zeeker/public
 * @author     Zeeker Team <admin@zeeker.com>
 */
class Zeeker_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access   private
	 * @var string  $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access   private
	 * @var string  $plugin_version    The current version of this plugin.
	 */
	private $plugin_version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param   string $plugin_name       The name of the plugin.
	 * @param   string $plugin_version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $plugin_version;

		add_action( 'wp_footer', array( __CLASS__, 'load_widget' ), 9999 );
	}

	/**
	 * Callback function for template_redirect action.
	 * Enqueue Zeeker Widget Script as per the plugin setting options.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public static function load_widget() {
		$widget_is_active = get_option( '_zeeker_widget_active', false );
		if ( false !== $widget_is_active ) {
			return;
		}

		$zeeker = Zeeker::get_instance();

		$display_options = $zeeker->get_zeeker_display_options();

		$widget_id = $zeeker->get_zeeker_widget_id();

		if ( ! $widget_id ) {
			return;
		}

		$widget_enabled = false;

		if ( is_array( $display_options ) && 0 < count( $display_options ) ) {
			if ( is_front_page() && is_home() ) { // check if default homepage.
				if ( array_key_exists( 'frontpage', $display_options ) ) {
					$widget_enabled = true;
				}
			} elseif ( is_front_page() ) { // check if static homepage.
				if ( array_key_exists( 'frontpage', $display_options ) ) {
					$widget_enabled = true;
				}
			} elseif ( is_home() ) { // check if is blog page.
				if ( array_key_exists( 'blogs', $display_options ) ) {
					$widget_enabled = true;
				}
			} elseif ( is_search() ) { // check if search page.
				if ( array_key_exists( 'search', $display_options ) ) {
					$widget_enabled = true;
				}
			} elseif ( is_404() ) { // check if 404 page.
				if ( array_key_exists( '404', $display_options ) ) {
					$widget_enabled = true;
				}
			} elseif ( is_page() ) {
				global $post;
				if ( isset( $display_options['post-type'] ) ) {
					if ( isset( $display_options['post-type']['page'] ) ) {
						if ( isset( $display_options['post-type']['page']['posts'] ) && is_array( $display_options['post-type']['page']['posts'] ) ) {
							$available_ids = $display_options['post-type']['page']['posts'];
							if ( 0 < count( $available_ids ) && in_array( $post->ID, $available_ids ) ) { // phpcs:ignore
								$widget_enabled = true;
							}
						} elseif ( isset( $display_options['post-type']['page']['active'] ) ) {
							$widget_enabled = true;
						}
					}
				}
			} elseif ( is_single() ) {
				global $post;
				$post_type = $post->post_type;

				if ( isset( $display_options['post-type'] ) ) {
					if ( isset( $display_options['post-type'][ $post_type ] ) ) {
						if ( isset( $display_options['post-type'][ $post_type ]['posts'] ) && is_array( $display_options['post-type'][ $post_type ]['posts'] ) ) {
							$available_ids = $display_options['post-type'][ $post_type ]['posts'];
							if ( 0 < count( $available_ids ) && in_array( $post->ID, $available_ids ) ) { // phpcs:ignore
								$widget_enabled = true;
							}
						} elseif ( isset( $display_options['post-type'][ $post_type ]['active'] ) ) {
							$widget_enabled = true;
						}
					}
				}
			} elseif ( is_archive() ) {
				$queried_object = get_queried_object();

				if ( is_category() || is_tag() || is_tax() ) {
					if ( isset( $display_options['taxonomy'] ) ) {
						if ( isset( $display_options['taxonomy'][ $queried_object->taxonomy ] ) ) {
							if ( isset( $display_options['taxonomy'][ $queried_object->taxonomy ]['terms'] ) && is_array( $display_options['taxonomy'][ $queried_object->taxonomy ]['terms'] ) ) {
								$available_ids = $display_options['taxonomy'][ $queried_object->taxonomy ]['terms'];
								if ( 0 < count( $available_ids ) && in_array( $queried_object->term_id, $available_ids ) ) { // phpcs:ignore
									$widget_enabled = true;
								}
							} elseif ( isset( $display_options['taxonomy'][ $queried_object->taxonomy ]['active'] ) ) {
								$widget_enabled = true;
							}
						}
					}
				} elseif ( is_post_type_archive() ) {
					$post_type = $queried_object->name;

					if ( isset( $display_options['post-type'] ) ) {
						if ( isset( $display_options['post-type'][ $post_type ] ) ) {
							if ( isset( $display_options['post-type'][ $post_type ]['archive'] ) ) {
								$widget_enabled = true;
							}
						}
					}
				}
			}
		} // end check $display_options

		if ( true === $widget_enabled ) {
			?>
			<script id="zwidget" src="https://zeeker-script-library-prod.s3.amazonaws.com/app.bundle.js?id=<?php echo esc_attr( $widget_id ); ?>&amp;tagname="></script> <?php // phpcs:ignore ?>
			<?php
		}
	}

}
