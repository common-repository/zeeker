<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Zeeker
 * @subpackage Zeeker/includes
 * @author     Zeeker Team <admin@zeeker.com>
 */
class Zeeker {

	/**
	 * Class instance holder.
	 *
	 * @since 1.0.0
	 * @access  private
	 * @var object  $instance   Current class instance.
	 */
	private static $instance;

	/**
	 * The plugin unique name.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string  $plugin_name    Name of the plugin.
	 */
	protected $plugin_name;

	/**
	 * The plugin version.
	 *
	 * @since 1.0.0
	 * @access  protected
	 * @var string  $plugin_version Versions of the plugin.
	 */
	protected $plugin_version;

	/**
	 * The saved values of the plugin from plugin admin settings page.
	 *
	 * @since 1.0.0
	 * @access  protected
	 * @var array   $plugin_options The values saved from plugin option.
	 */
	protected $plugin_options;

	/**
	 * Plugin public class instance holder.
	 *
	 * @since 1.0.1
	 * @access public
	 * @var Zeeker_Public Zeeker_Public instance.
	 */
	public $plugin_public;

	/**
	 * Zeeker class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( defined( 'ZEEKER_VERSION' ) ) {
			$this->plugin_version = ZEEKER_VERSION;
		} else {
			$this->plugin_version = '1.0.0';
		}

		$this->plugin_name = 'zeeker';

		$this->plugin_options  = get_option( '_zeeker_options', array() );

		$this->load_dependencies();
		$this->dependencies_init();

		$this->add_hooks();
	}

	/**
	 * Load plugin dependencies.
	 *
	 * - Zeeker_I18n. Defines internationalization functionality.
	 * - Zeeker_Admin. Defines all hooks for the admin area.
	 * - Zeeker_Public. Defines all hooks for the public side of the site.
	 *
	 * @since 1.0.0
	 * @access  private
	 */
	private function load_dependencies() {
		/**
		 * Plugin language localization file.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-zeeker-i18n.php';

		/**
		 * Plugin admin setup class.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-zeeker-admin.php';

		/**
		 * Plugin public setup class.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-zeeker-public.php';
	}

	/**
	 * Initialize dependent field.
	 *
	 * - Initialize plugin language.
	 * - Initialize admin setup.
	 * - Initialize public setup.
	 *
	 * @since 1.0.0
	 * @access  private
	 */
	private function dependencies_init() {
		// plugin language.
		new Zeeker_I18n( $this->plugin_name, $this->plugin_version );

		if ( is_admin() ) {
			// plugin admin.
			new Zeeker_Admin( $this->plugin_name, $this->plugin_version );
		}

		// plugin public.
		$this->plugin_public = new Zeeker_Public( $this->plugin_name, $this->plugin_version );
	}

	/**
	 * Add plugin global hook.
	 *
	 * @since 1.0.0
	 * @access  private
	 */
	private function add_hooks() {
		add_filter( 'plugin_action_links_' . ZEEKER_BASENAME, array( $this, 'settings_link' ) );
	}

	/**
	 * Callback function for plugin_action_links_
	 * Add plugin setting page link in plugins page.
	 *
	 * @since 1.0.0
	 * @access  private
	 * @param array $links  Plugin detail links.
	 * @return array $links  Plugin detail links.
	 */
	public function settings_link( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=zeeker-settings' ) . '">' . esc_html__( 'Settings', 'zeeker' ) . '</a>';
		return $links;
	}

	/**
	 * Initialize class.
	 */
	public static function run() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}
	}

	/**
	 * Return the instance of class object.
	 * The instance must be initialized already (self::run()).
	 *
	 * @since 1.0.0
	 * @access public
	 * @return Zeeker   Class object instance.
	 */
	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Return saved Zeeker Widget ID.
	 * Get the value from options saved from plugin admin settings page.
	 *
	 * @since 1.0.0
	 * @access  public
	 * @return string $zeeker_widget_id Saved Zeeker Widget ID.
	 */
	public function get_zeeker_widget_id() {
		if ( ! is_array( $this->plugin_options ) || ! isset( $this->plugin_options['zeeker_widget_id'] ) || empty( $this->plugin_options['zeeker_widget_id'] ) ) {
			return '';
		}

		return $this->plugin_options['zeeker_widget_id'];
	}

	/**
	 * Return plugin option data.
	 *
	 * @since 1.0.0
	 * @access  public
	 * @return array
	 */
	public function get_zeeker_options() {
		return $this->plugin_options;
	}

	/**
	 * Return widget display option data.
	 *
	 * @since 1.0.0
	 * @access  public
	 * @return array
	 */
	public function get_zeeker_display_options() {
		if ( ! is_array( $this->plugin_options ) || ! isset( $this->plugin_options['zeeker_widget_display'] ) || empty( $this->plugin_options['zeeker_widget_display'] ) ) {
			return array();
		}

		return $this->plugin_options['zeeker_widget_display'];
	}

	/**
	 * Return the Zeeker Widget script URL.
	 *
	 * @since 1.0.0
	 * @access  private
	 * @return  string  Zeeker Widget script URL.
	 */
	public function get_zeeker_script_url() {
		return 'https://zeeker-script-library-prod.s3.amazonaws.com/app.bundle.js';
	}
}
