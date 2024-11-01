<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Zeeker
 * @subpackage Zeeker/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Zeeker
 * @subpackage Zeeker/admin
 * @author     Zeeker Team <admin@zeeker.com>
 */
class Zeeker_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access  private
	 * @var string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access  private
	 * @var string    $plugin_version    The current version of this plugin.
	 */
	private $plugin_version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param   string $plugin_name       The name of this plugin.
	 * @param   string $plugin_version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $plugin_version;

		$this->load_dependencies();
		$this->add_hooks();
	}

	/**
	 * Load dependent files for admin section.
	 *
	 * @since 1.0.0
	 * @access  private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . '/inc/class-zeeker-admin-settings.php';
		new Zeeker_Admin_Settings();
	}

	/**
	 * Register admin hooks.
	 *
	 * @since 1.0.0
	 * @access  private
	 */
	private function add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_select2_search', array( $this, 'select2_search' ) );
	}

	/**
	 * Callback function to enqueue admin scripts.
	 * Enqueue plugin admin scripts.
	 *
	 * @since 1.0.0
	 * @access  public
	 */
	public function enqueue_scripts() {
		// styles.
		wp_enqueue_style( $this->plugin_name . '-select2-style', plugin_dir_url( __FILE__ ) . 'assets/vendors/select2/dist/css/select2.min.css', array(), $this->plugin_version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/zeeker-admin.css', array(), $this->plugin_version, 'all' );

		// scripts.
		wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'assets/vendors/select2/dist/js/select2.min.js', array( 'jquery' ), $this->plugin_version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/zeeker-admin.js', array( 'jquery', $this->plugin_name . '-select2' ), $this->plugin_version, true );
		wp_localize_script(
			$this->plugin_name,
			$this->plugin_name,
			array(
				'ajax'                   => admin_url( 'admin-ajax.php' ),
				'removeAccordionContent' => esc_html__(
					'Are your sure to remove this?',
					'zeeker'
				),
			)
		);
	}

	/**
	 * Callback function wp_ajax.
	 * Query posts/pages/medias/taxonomies (or custom post types).
	 *
	 * @since 1.0.0
	 * @access  public
	 */
	public function select2_search() {
		// check for nonce.
		if ( ! isset( $_GET['_zeeker_select2_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_zeeker_select2_nonce'] ) ), '_zeeker_select2_nonce' ) ) {
			return;
		}

		// get submitted values.
		$post_type = filter_input( INPUT_GET, 'post_type' );
		$search    = filter_input( INPUT_GET, 'search' );
		$page      = filter_input( INPUT_GET, 'page' );
		$taxonomy  = filter_input( INPUT_GET, 'tax' );

		if ( ! $post_type ) {
			$post_type = 'post';
		}

		if ( ! $page || ! is_numeric( $page ) ) {
			$page = 1;
		}

		$to_return = array(
			'results'    => array(),
			'pagination' => array(
				'more' => false,
			),
		);

		if ( $taxonomy ) {
			$args = array(
				'taxonomy'   => sanitize_text_field( $taxonomy ),
				'hide_empty' => false,
				'fields'     => 'count',
			);

			if ( $search ) {
				$args['name__like'] = sanitize_text_field( $search );
			}

			$total_terms = get_terms( $args );

			if ( ! is_numeric( $total_terms ) ) {
				$total_terms = 0;
			} else {
				$total_terms = floatval( $total_terms );
			}

			unset( $args['fields'] );

			$args['number'] = 20;
			$args['offset'] = ( $page - 1 ) * 20;

			$terms = get_terms( $args );

			if ( is_array( $terms ) ) {
				foreach ( $terms as $term ) {
					$to_return['results'][] = array(
						'id'   => $term->term_id,
						'text' => $term->name,
					);
				}

				$to_return['pagination']['more'] = ( floatval( $page ) < ceil( $total_terms / 20 ) );
				$to_return['count_filtered']     = $total_terms;
			}

			wp_send_json( $to_return );
			exit;
		}

		$args = array(
			'post_type'      => sanitize_text_field( $post_type ),
			'post_status'    => 'publish',
			'posts_per_page' => 20,
			'paged'          => $page,
			's'              => sanitize_text_field( $search ),
		);

		$posts = new WP_Query( $args );
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				$to_return['results'][] = array(
					'id'   => get_the_ID(),
					'text' => get_the_title(),
				);
			}

			if ( $posts->max_num_pages > $page ) {
				$to_return['pagination']['more'] = true;
			} else {
				$to_return['pagination']['more'] = false;
			}

			$to_return['count_filtered'] = $posts->found_posts;
			wp_reset_postdata();
		}

		wp_send_json( $to_return );
		exit;

	}

}
