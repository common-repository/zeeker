<?php
/**
 * The admin-specific settings/options functionality of the plugin.
 * Uses WordPress Settings API to register the setting options.
 *
 * @since      1.0.0
 *
 * @package    Zeeker
 * @subpackage Zeeker/admin
 */

/**
 * The admin-specific settings class of the plugin.
 *
 * @package    Zeeker
 * @subpackage Zeeker/admin
 * @author     Zeeker Team <admin@zeeker.com>
 */
class Zeeker_Admin_Settings {
	/**
	 * Plugin option values.
	 *
	 * @since 1.0.0
	 * @access  private
	 * @var array   $option_values  Plugin saved options values.
	 */
	private $options = array();

	/**
	 * Plugin display options.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array $displays     Plugin widget display options.
	 */
	private $displays = array();

	/**
	 * Zeeker_Admin_Settings class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'plugin_settings_page' ) );

		add_action( 'wp_ajax__zeeker_create_widget', array( $this, 'zeeker_create_widget' ) );
		add_action( 'wp_ajax__zeeker_widget_display_options', array( $this, 'save_widget_display_options' ) );

		add_action( 'wp_ajax__zeeker_delete_widget', array( $this, 'zeeker_delete_widget' ) );
		add_action( 'wp_ajax__zeeker_update_widget_status', array( $this, 'zeeker_widget_status' ) );

		add_filter( 'pre_update_option_zeeker_options', array( $this, 'zeeker_options_before_update' ) );

		$this->options = get_option( '_zeeker_options', array() );
	}

	/**
	 * Callback for the admin_menu action.
	 * Register plugin settings page as subpage under "Settings" menu.
	 *
	 * @since 1.0.0
	 * @access  public
	 */
	public function plugin_settings_page() {
		add_menu_page(
			esc_html__( 'Zeeker Settings Option', 'zeeker' ),
			esc_html__( 'Zeeker', 'zeeker' ),
			'manage_options',
			'zeeker-settings',
			array( $this, 'settings_page_cb' ),
			plugins_url( 'zeeker/images/Icon-20.png' )
		);
	}

	/**
	 * Callback for settings submenu page.
	 * Renders the settings options.
	 *
	 * @since 1.0.0
	 * @access  public
	 */
	public function settings_page_cb() {
		// check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$widget_created_with = isset( $this->options['widget_created_with'] ) ? $this->options['widget_created_with'] : '';
		$zeeker_widget_id    = isset( $this->options['zeeker_widget_id'] ) ? $this->options['zeeker_widget_id'] : '';

		$zeeker_widget_data = array();
		$error              = '';
		$access_token       = '';

		if ( 'widget-api' === $widget_created_with ) {
			if ( $zeeker_widget_id ) {
				if ( isset( $_SESSION ) && is_array( $_SESSION ) && isset( $_SESSION['zeeker_token'] ) ) {
					$access_token = $_SESSION['zeeker_token'];
				}

				if ( ! $access_token ) {
					$user_token = $this->get_user_token();

					if ( ! is_wp_error( $user_token ) && is_array( $user_token ) && isset( $user_token['access_token'] ) ) {
						if ( isset( $_SESSION ) && is_array( $_SESSION ) ) {
							$_SESSION['zeeker_token'] = $user_token['access_token'];
						}

						$access_token = $user_token['access_token'];
					}
				}

				$zeeker_widget_data = $this->get_widget_data( $zeeker_widget_id, $access_token );

				if ( is_wp_error( $zeeker_widget_data ) ) {
					$error = $zeeker_widget_data->get_error_message();
				}

				if ( ! is_array( $zeeker_widget_data ) ) {
					$zeeker_widget_data = array();
				}

				// Widget is deleted on the Zeeker portal.
				if ( isset( $zeeker_widget_data['deleted'] ) && false !== $zeeker_widget_data['deleted'] ) {
					$zeeker_widget_data  = array();
					$widget_created_with = '';

					delete_option( '_zeeker_options' );

					$this->options = array();
				}
			}
		}
		?>
		<div class="wrap">
			<?php if ( ! $zeeker_widget_id ) { ?>
				<h1><?php echo esc_html__( 'Zeeker', 'zeeker' ); ?></h1>
			<?php } else { ?>
				<h1><?php echo esc_html__( 'Widget Settings', 'zeeker' ); ?></h1>
			<?php } ?>

			<?php
			$active_tab = filter_input( INPUT_GET, 'tab' );
			if ( ! $active_tab || ! in_array( $active_tab, array( 'zeeker-widget-instruction', 'zeeker-widget-configure', 'zeeker-widget-support' ), true ) ) {
				$active_tab = 'zeeker-widget-instruction';

				if ( $zeeker_widget_id ) {
					$active_tab = 'zeeker-widget-configure';
				}
			} // end check $active_tab.
			?>

			<h2 class="zeeker-tabs nav-tab-wrapper">
				<?php if ( ! $zeeker_widget_id ) { ?>
					<a href="?page=zeeker-settings" data-zeeker_target="zeeker-widget-instruction" class="nav-tab <?php echo 'zeeker-widget-instruction' === $active_tab ? 'nav-tab-active' : ''; ?>">
						<?php esc_html_e( 'Create Widget', 'zeeker' ); ?>
					</a>
				<?php } ?>

				<?php if ( $zeeker_widget_id ) { ?>
					<a href="?page=zeeker-settings&tab=zeeker-widget-configure" data-zeeker_target="zeeker-widget-configure" class="nav-tab <?php echo 'zeeker-widget-configure' === $active_tab ? 'nav-tab-active' : ''; ?>">
						<?php esc_html_e( 'Configuration', 'zeeker' ); ?>
					</a>
				<?php } ?>

				<a href="?page=zeeker-settings&tab=zeeker-widget-support" data-zeeker_target="zeeker-widget-support" class="nav-tab <?php echo 'zeeker-widget-support' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Support', 'zeeker' ); ?>
				</a>
			</h2>

			<div class="zeeker-tab-contents">

				<?php
				if ( ! $zeeker_widget_id ) {
					include plugin_dir_path( __FILE__ ) . '../partials/create-widget.php';
				}

				if ( $zeeker_widget_id ) {
					include plugin_dir_path( __FILE__ ) . '../partials/configure.php';
				}

				include plugin_dir_path( __FILE__ ) . '../partials/support.php';
				?>

			</div>
		</div>
		<?php
	}


	/**
	 * Render the zeeker_widget_display field.
	 *
	 * @since 1.0.0
	 * @access  public
	 *
	 * @param array $args  Settings field arguments.
	 */
	public function zeeker_widget_display_cb( $args ) {
		$select2_placeholder = __( 'Leave blank to enable in all %s.', 'zeeker' );

		// nonce field for ajax.
		wp_nonce_field( '_zeeker_select2_nonce', 'zeeker_select2_nonce' );

		// check for selected option types.
		$value = isset( $this->options[ $args['label_for'] ] ) ? $this->options[ $args['label_for'] ] : array();
		// WP default pages.
		?>
		<div class="zeeker-widget-id form-group">
			<label for="zeeker_widget_id">
				<?php esc_html_e( 'Zeeker Widget ID', 'zeeker' ); ?><span>*</span>
			</label>
			<input type="text" required id="zeeker_widget_id" name="zeeker_options[zeeker_widget_id]" value="<?php echo isset( $this->options['zeeker_widget_id'] ) ? esc_attr( $this->options['zeeker_widget_id'] ) : ''; ?>" />
		</div>

		<?php
		// We do not have widget ID yet, so no need to display further options.
		if ( ! isset( $this->options['zeeker_widget_id'] ) || ! $this->options['zeeker_widget_id'] ) {
			return;
		}
		?>

		<div class="widget-preview-actions" id="zeeker-id-buttons">
			<div class="widget-preview-action">
				<a 
					href="https://portal.zeeker.com/dashboard" 
					class="button-secondary zeeker-tooltip" 
					target="_blank" 
					rel="noindex,nofollow"
					data-tooltip="<?php echo esc_attr__( 'Customize the look and feel of Widget.', 'zeeker' ); ?>"
				>
					<?php esc_html_e( 'Edit', 'zeeker' ); ?>
				</a>
			</div>

			<div class="widget-preview-action">
				<a 
					href="https://portal.zeeker.com/widget/statistics" 
					class="button-secondary zeeker-tooltip" 
					target="_blank" 
					rel="noindex,nofollow"
					data-tooltip="<?php echo esc_attr__( 'View statistics about your Widget.', 'zeeker' ); ?>"
				>
					<?php esc_html_e( 'Analytics', 'zeeker' ); ?>
				</a>
			</div>

			<div class="widget-preview-action">
				<a 
					href="https://portal.zeeker.com/moderation" 
					class="button-secondary zeeker-tooltip" 
					target="_blank" 
					rel="noindex,nofollow"
					data-tooltip="<?php echo esc_attr__( 'Moderate the conversation taking place in the Widget.', 'zeeker' ); ?>"
				>
					<?php esc_html_e( 'Moderate', 'zeeker' ); ?>
				</a>
			</div>
		</div>
		<h2><?php esc_html_e( 'Display Options', 'zeeker' ); ?></h2>

		<div class="form-group selectall">
			<?php $checked = array_key_exists( 'selectall', $value ) ? 'checked' : ''; ?>
			<label>
				<input type="checkbox" name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][selectall]" id="select-all" value="yes" <?php echo esc_attr( $checked ); ?>> <?php esc_html_e( 'Select All', 'zeeker' ); ?>
			</label>
		</div>

		<div class="zeeker-default-pages">
			<h4 style="margin-bottom: 10;">Pages:</h4>
			<?php
			// for frontpage.
			$class   = 'hidden';
			$checked = array_key_exists( 'frontpage', $value ) ? 'checked' : '';
			if ( $checked ) {
				$class = '';
			}
			?>
			<div class="form-group zeeker-default-page frontpage">
				<label>
					<input type="checkbox" class="field-display-option" name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][frontpage]" class="field-display-option frontpage" value="yes" <?php echo esc_attr( $checked ); ?>> <?php esc_html_e( 'Frontpage', 'zeeker' ); ?>
				</label>
			</div>

			<?php
			// for blog page.
			$class   = 'hidden';
			$checked = array_key_exists( 'blogs', $value ) ? 'checked' : '';
			if ( $checked ) {
				$class = '';
			}
			?>
			<div class="form-group zeeker-default-page blogpage">
				<label>
					<input type="checkbox" class="field-display-option" name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][blogs]" value="yes" <?php echo esc_attr( $checked ); ?>> <?php esc_html_e( 'Blog Page', 'zeeker' ); ?>
				</label>
			</div>

			<?php
			// for search section.
			$class   = 'hidden';
			$checked = array_key_exists( 'search', $value ) ? 'checked' : '';
			if ( $checked ) {
				$class = '';
			}
			?>
			<div class="form-group zeeker-default-page search-page">
				<label>
					<input type="checkbox" class="field-display-option" name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][search]" value="yes" <?php echo esc_attr( $checked ); ?>> <?php esc_html_e( 'Search Page', 'zeeker' ); ?>
				</label>
			</div>

			<?php
			// for 404 section.
			$class   = 'hidden';
			$checked = array_key_exists( '404', $value ) ? 'checked' : '';
			if ( $checked ) {
				$class = '';
			}
			?>
			<div class="form-group zeeker-default-page 404-page">
				<label>
					<input type="checkbox" class="field-display-option" name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][404]" value="yes" <?php echo esc_attr( $checked ); ?>> <?php esc_html_e( '404 Page', 'zeeker' ); ?>
				</label>
			</div>
		</div> <!-- .zeeker-default-pages -->

		<?php
		// WP post types.
		// get all registered and public post types.
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		// result must be an array.
		if ( ! is_array( $value ) ) {
			$value = array();
		}
		// check for $post_types.
		if ( ! empty( $post_types ) ) {
			?>
			<div class="zeeker-post-types">
				<h4 style="margin-bottom: 10;"><?php esc_html_e( 'Post Types:', 'zeeker' ); ?></h4>
				<?php
				// loop $post_types.
				foreach ( $post_types as $key => $post_type ) {
					// default extra class to hide element.
					$class = 'hidden';
					// if selected, do no hide the element.
					$checked = '';
					if ( isset( $value['post-type'] ) && array_key_exists( $key, $value['post-type'] ) ) {
						$checked = 'checked';
						$class   = '';
					}
					?>
					<div class="form-group zeeker-options zeeker-post-type <?php echo esc_attr( $key ); ?>">
						<label>
							<input type="checkbox" class="field-display-option zeeker-type" name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][post-type][<?php echo esc_attr( $key ); ?>][active]" value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $checked ); ?>> <?php echo esc_html( $post_type->label ); ?>
						</label>
						<div class="zeeker-extra-options <?php echo esc_attr( $class ); ?>">
							<?php
							// check if post type have archive enabled.
							if ( $post_type->has_archive ) {
								// if selected.
								$checked = '';
								if ( isset( $value['post-type'][ $key ] ) && is_array( $value['post-type'][ $key ] ) ) {
									$checked = array_key_exists( 'archive', $value['post-type'][ $key ] ) ? 'checked' : '';
								}
								?>
								<div class="zeeker-archive">
									<label>
										<input type="checkbox" name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][post-type][<?php echo esc_attr( $key ); ?>][archive]" value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $checked ); ?>> <?php echo esc_html__( 'Enable on archive also.', 'zeeker' ); ?>
									</label>
								</div>
								<?php
							} // end check $post_type->has_archive.

							// get pre selected post ids.
							$pre_selected     = array();
							$pre_selected_ids = array();
							if ( isset( $value['post-type'][ $key ] ) && is_array( $value['post-type'][ $key ] ) ) {
								$pre_selected_ids = array_key_exists( 'posts', $value['post-type'][ $key ] ) ? $value['post-type'][ $key ]['posts'] : '';
							}
							// if pre selected ids, get related posts.
							if ( is_array( $pre_selected_ids ) && 0 < count( $pre_selected_ids ) ) {
								$pre_selected = get_posts(
									array(
										'post_type'      => $key,
										'post_status'    => 'publish',
										'posts_per_page' => -1,
										'include'        => $pre_selected_ids,
									)
								);
							} // end check $pre_selected_ids.
							?>
							<div class="zeeker-posts">
								<select 
								data-post_type="<?php echo esc_attr( $key ); ?>" 
								name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][post-type][<?php echo esc_attr( $key ); ?>][posts][]" 
								multiple class="zeeker-select2" 
								style="width: 50%;"
								data-placeholder="<?php echo esc_attr( sprintf( $select2_placeholder, $post_type->label ) ); ?>"
								>
									<?php
									if ( 0 < count( $pre_selected ) ) {
										foreach ( $pre_selected as $p_selected ) {
											?>
											<option value="<?php echo esc_attr( $p_selected->ID ); ?>" selected="selected">
												<?php echo esc_html( $p_selected->post_title ); ?>
											</option>
											<?php
										} // end loop $pre_selected.
									} // end check $pre_selected_ids.
									?>
								</select>
							</div> <!-- .zeeker-posts -->
						</div> <!-- .zeeker-extra-options -->
					</div> <!-- .zeeker-options -->
					<?php
				} // end loop $post_types.
				?>
			</div> <!-- .zeeker-post-types -->
			<?php
		} // end check $post_types.

		// WP taxonomies.
		// get all registered and public taxonomies.
		$taxonomies = get_taxonomies(
			array(
				'public' => true,
			),
			'objects'
		);

		// check $taxonomies.
		if ( $taxonomies ) {
			?>
			<div class="zeeker-taxonomies">
				<h4 style="margin-bottom: 10;"><?php esc_html_e( 'Taxonomies:', 'zeeker' ); ?></h4>
				<?php
				// loop $taxonomies.
				foreach ( $taxonomies as $key => $taxonomy ) {
					// default extra class to hide element.
					$class = 'hidden';
					// if selected, do no hide the element.
					$checked = '';
					if ( isset( $value['taxonomy'] ) && array_key_exists( $key, $value['taxonomy'] ) ) {
						$checked = 'checked';
						$class   = '';
					}
					?>
					<div class="form-group zeeker-options zeeker-taxonomy <?php echo esc_attr( $key ); ?>">
						<label>
							<input type="checkbox" class="field-display-option zeeker-type" name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][taxonomy][<?php echo esc_attr( $key ); ?>][active]" value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $checked ); ?>> <?php echo esc_html( $taxonomy->label ); ?>
						</label>
						<div class="zeeker-extra-options <?php echo esc_attr( $class ); ?>">
							<div class="zeeker-posts">
								<?php
								// get pre selected post ids.
								$pre_selected     = array();
								$pre_selected_ids = array();
								if ( isset( $value['taxonomy'][ $key ] ) && is_array( $value['taxonomy'][ $key ] ) ) {
									$pre_selected_ids = array_key_exists( 'terms', $value['taxonomy'][ $key ] ) ? $value['taxonomy'][ $key ]['terms'] : array();
								}
								// if pre selected ids, get related posts.
								if ( is_array( $pre_selected_ids ) && 0 < count( $pre_selected_ids ) ) {
									$pre_selected = get_terms(
										array(
											'taxonomy'   => $key,
											'include'    => $pre_selected_ids,
											'hide_empty' => false,
										)
									);
								}
								?>
								<select
								data-taxonomy="<?php echo esc_attr( $key ); ?>" 
								name="zeeker_options[<?php echo esc_attr( $args['label_for'] ); ?>][taxonomy][<?php echo esc_attr( $key ); ?>][terms][]" 
								multiple 
								class="zeeker-select2" 
								style="width: 50%;"
								data-placeholder="<?php echo esc_attr( sprintf( $select2_placeholder, $taxonomy->label ) ); ?>"
								>
									<?php
									if ( 0 < count( $pre_selected ) ) {
										foreach ( $pre_selected as $p_selected ) {
											?>
											<option value="<?php echo esc_attr( $p_selected->term_id ); ?>" selected="selected">
												<?php echo esc_html( $p_selected->name ); ?>
											</option>
											<?php
										} // end loop $pre_selected.
									} // end check $pre_selected_ids.
									?>
								</select>
							</div> <!-- .zeeker-posts -->

						</div> <!-- .zeeker-extra-options -->
					</div> <!-- .zeeker-options.zeeker-taxonomy -->
					<?php
				} // end loop $taxonomies.
				?>
			</div> <!-- .zeeker-taxonomies -->
			<?php
		} // end check $taxonomies.
	}

	/**
	 * Callback function for the pre_update_option_zeeker_options filter.
	 * Modify the setting options value as per the condition.
	 *
	 * @since 1.0.0
	 * @access  public
	 *
	 * @param array $value  Submitted setting option values.
	 */
	public function zeeker_options_before_update( $value ) {
		if ( isset( $_POST['add-zeeker-widget-id'] ) && empty( $_POST['add-zeeker-widget-id'] ) ) { // phpcs:ignore
			$value['widget_created_with'] = 'widget-id';
		} elseif ( isset( $_POST['create-zeeker-widget'] ) && empty( $_POST['create-zeeker-widget'] ) ) { // phpcs:ignore
			$value['widget_created_with'] = 'widget-api';
		}

		return $value;
	}

	/**
	 * Crate Zeeker Widget through Zeeker API.
	 *
	 * @return void
	 */
	public function zeeker_create_widget() {
		$form_data = array();
		parse_str( filter_input( INPUT_POST, 'data' ), $form_data );

		$nonce = isset( $form_data['zeeker-create-widget-nonce'] ) ? $form_data['zeeker-create-widget-nonce'] : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'zeeker-create-widget-nonce' ) ) {
			wp_send_json_error( esc_html__( 'Invalid request. Nonce verification failed.', 'zeeker' ) );
			exit;
		}

		$organization_name = isset( $form_data['zeeker-organization-name'] ) ? $form_data['zeeker-organization-name'] : '';
		$widget_name       = isset( $form_data['zeeker-widget-name'] ) ? $form_data['zeeker-widget-name'] : '';
		$widget_category   = isset( $form_data['zeeker-widget-category'] ) ? $form_data['zeeker-widget-category'] : '';

		if ( ! $organization_name || ! $widget_name || ! $widget_category || ! in_array( $widget_category, array( 'News', 'Blog', 'Ecommerce', 'Personal Website', 'Local Business' ) ) ) {
			if ( ! $organization_name ) {
				$error['zeeker-organization-name'] = esc_html__( 'Name of the Organization is required.', 'zeeker' );
			}

			if ( ! $widget_name ) {
				$error['zeeker-widget-name'] = esc_html__( 'Widget Name is required.', 'zeeker' );
			}

			if ( ! $widget_name ) {
				$error['zeeker-widget-name'] = esc_html__( 'Widget Name is required.', 'zeeker' );
			}

			if ( ! $widget_category || ! in_array( $widget_category, array( 'News', 'Blog', 'Ecommerce', 'Personal Website', 'Local Business' ) ) ) {
				$error['zeeker-widget-category'] = esc_html__( 'Select valid category for your website.', 'zeeker' );
			}

			wp_send_json_error( $error );
			exit;
		}

		$user_token = $this->get_user_token();

		if ( is_wp_error( $user_token ) || ! is_array( $user_token ) || ! isset( $user_token['access_token'] ) || ! $user_token['access_token'] ) {
			wp_send_json_error( esc_html__( 'Create widget request failed. Please try again.', 'zeeker' ) );
			exit;
		}

		if ( isset( $_SESSION ) && is_array( $_SESSION ) ) {
			$_SESSION['zeeker_token'] = $user_token['access_token'];
		}

		$widget_data = $this->create_widget( $organization_name, $widget_name, $widget_category, $user_token['access_token'] );

		if ( is_wp_error( $widget_data ) || ! is_array( $widget_data ) ) {
			wp_send_json_error( esc_html__( 'Create widget request failed. Please try again.', 'zeeker' ) );
			exit;
		}

		if ( isset( $widget_data['nameAlreadyExists'] ) && $widget_data['nameAlreadyExists'] ) {
			$error                       = array();
			$error['zeeker-widget-name'] = esc_html__( 'This widget name is in use. Please pick another name.', 'zeeker' );
			wp_send_json_error( $error );
			exit;
		}

		if ( ! isset( $widget_data['uuid'] ) || ! $widget_data['uuid'] ) {
			wp_send_json_error( esc_html__( 'Create widget request failed. Please try again.', 'zeeker' ) );
			exit;
		}

		$display_options_data = array(
			'zeeker_widget_id'      => $widget_data['uuid'],
			'widget_created_with'   => 'widget-api',
			'zeeker_widget_display' => array(
				'selectall' => 'yes',
				'frontpage' => 'yes',
				'404'       => 'yes',
				'search'    => 'yes',
				'blogs'     => 'yes',
			),
		);

		// Post Types
		$display_options_data['zeeker_widget_display']['post-type'] = array();
		$registered_post_types                                      = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		if ( is_array( $registered_post_types ) && ! empty( $registered_post_types ) ) {
			foreach ( $registered_post_types as $key => $post_type ) {
				$array = array(
					'active' => $key,
				);

				if ( $post_type->has_archive ) {
					$array['archive'] = $key;
				}

				$display_options_data['zeeker_widget_display']['post-type'][ $key ] = $array;
			}
		}

		// Taxonomies
		$display_options_data['zeeker_widget_display']['taxonomy'] = array();
		$registered_taxonomies                                     = get_taxonomies(
			array(
				'public' => true,
			),
			'objects'
		);

		if ( is_array( $registered_taxonomies ) && ! empty( $registered_taxonomies ) ) {
			foreach ( $registered_taxonomies as $key => $taxonomy ) {
				$array = array(
					'active' => $key,
				);

				if ( $taxonomy->has_archive ) {
					$array['archive'] = $key;
				}

				$display_options_data['zeeker_widget_display']['taxonomy'][ $key ] = $array;
			}
		}

		update_option(
			'_zeeker_options',
			$display_options_data,
			false
		);

		wp_send_json_success(
			array(
				'redirect' => add_query_arg(
					array(
						'page' => 'zeeker-settings',
						'tab'  => 'zeeker-widget-configure',
					),
					admin_url( 'admin.php' )
				),
			)
		);
		exit;
	}

	/**
	 * Save Zeeker widget display options.
	 *
	 * @return void
	 */
	public function save_widget_display_options() {
		$form_data = array();
		parse_str( filter_input( INPUT_POST, 'data' ), $form_data );

		$nonce = isset( $form_data['zeeker-display-widget-nonce'] ) ? $form_data['zeeker-display-widget-nonce'] : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'zeeker-display-widget-nonce' ) ) {
			wp_send_json_error( esc_html__( 'Invalid request. Nonce verification failed.', 'zeeker' ) );
			exit;
		}

		$_zeeker_display_option = isset( $form_data['zeeker_options'] ) ? $form_data['zeeker_options'] : array();

		if ( ! isset( $_zeeker_display_option['zeeker_widget_id'] ) || ! $_zeeker_display_option['zeeker_widget_id'] ) {
			wp_send_json_error(
				array(
					'zeeker_widget_id' => esc_html__( 'Zeeker Widget ID must not be empty.', 'zeeker' ),
				)
			);
			exit;
		}

		$zeeker_display_option = $_zeeker_display_option;

		if ( isset( $_zeeker_display_option['zeeker_widget_display'] ) && is_array( $_zeeker_display_option['zeeker_widget_display'] ) ) {

			if ( isset( $_zeeker_display_option['zeeker_widget_display']['post-type'] ) && is_array( $_zeeker_display_option['zeeker_widget_display']['post-type'] ) ) {
				foreach ( $_zeeker_display_option['zeeker_widget_display']['post-type'] as $post_type => $data ) {
					if ( ! isset( $data['active'] ) ) {
						unset( $zeeker_display_option['zeeker_widget_display']['post-type'][ $post_type ] );
					}
				}
			}

			if ( isset( $_zeeker_display_option['zeeker_widget_display']['taxonomy'] ) && is_array( $_zeeker_display_option['zeeker_widget_display']['taxonomy'] ) ) {
				foreach ( $_zeeker_display_option['taxonomy'] as $taxonomy => $data ) {
					if ( ! isset( $data['active'] ) ) {
						unset( $zeeker_display_option['zeeker_widget_display']['taxonomy'][ $taxonomy ] );
					}
				}
			}

			$zeeker_display_option = array_filter( $zeeker_display_option );

			if ( empty( $zeeker_display_option['zeeker_widget_display']['post-type'] ) ) {
				unset( $zeeker_display_option['zeeker_widget_display']['post-type'] );
			}

			if ( empty( $zeeker_display_option['zeeker_widget_display']['taxonomy'] ) ) {
				unset( $zeeker_display_option['zeeker_widget_display']['taxonomy'] );
			}

			if ( empty( $zeeker_display_option['zeeker_widget_display'] ) ) {
				unset( $zeeker_display_option['zeeker_widget_display'] );
			}
		}

		if ( ! empty( $zeeker_display_option ) ) {
			$zeeker_display_option['widget_created_with'] = 'widget-api';
			update_option( '_zeeker_options', wp_unslash( $zeeker_display_option ) );
		} else {
			delete_option( '_zeeker_options' );
		}

		if ( isset( $form_data['custom-zeeker'] ) ) {
			$zeeker_display_option['widget_created_with'] = 'widget-id';
			update_option( '_zeeker_options', wp_unslash( $zeeker_display_option ) );

			// Widget is added through custom Widget ID, so we do not need the Widget data that came from API.
			delete_option( '_zeeker_widget_data' );

			if ( ! isset( $zeeker_display_option['zeeker_widget_display'] ) ) {
				wp_send_json_success(
					array(
						'message'  => esc_html__( 'Display options saved.', 'zeeker' ),
						'redirect' => add_query_arg(
							array(
								'page' => 'zeeker-settings',
								'tab'  => 'zeeker-widget-configure',
							),
							admin_url( 'admin.php' )
						),
					)
				);
			}
		}

		wp_send_json_success( esc_html__( 'Display options saved.', 'zeeker' ) );
		exit;

	}

	/**
	 * Delete widget data from database.
	 *
	 * @return void
	 */
	public function zeeker_delete_widget() {
		$nonce            = filter_input( INPUT_POST, 'nonce' );
		$zeeker_widget_id = isset( $this->options['zeeker_widget_id'] ) ? $this->options['zeeker_widget_id'] : '';

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'delete_widget-' . $zeeker_widget_id ) ) {
			wp_send_json_error( esc_html__( 'Invalid request.', 'zeeker' ) );
			exit;
		}

		delete_option( '_zeeker_options' );
		delete_option( '_zeeker_widget_active' );
		wp_send_json_success(
			array(
				'message'  => esc_html__( 'Widget deleted.', 'zeeker' ),
				'redirect' => add_query_arg(
					array(
						'page' => 'zeeker-settings',
					),
					admin_url( 'admin.php' )
				),
			)
		);
	}

	/**
	 * Update widget active status.
	 *
	 * @return void
	 */
	public function zeeker_widget_status() {
		$nonce            = filter_input( INPUT_POST, 'nonce' );
		$zeeker_widget_id = isset( $this->options['zeeker_widget_id'] ) ? $this->options['zeeker_widget_id'] : '';

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'update_widget_status-' . $zeeker_widget_id ) ) {
			wp_send_json_error( esc_html__( 'Invalid request.', 'zeeker' ) );
			exit;
		}

		$value = filter_input( INPUT_POST, 'value' );

		if ( 'true' !== $value ) {
			update_option( '_zeeker_widget_active', 'no' );
		} else {
			delete_option( '_zeeker_widget_active' );
		}
	}

	/**
	 * Get Zeeker user token.
	 *
	 * @return array|WP_Error
	 */
	private function get_user_token() {
		$user_token = wp_remote_post(
			'https://api.zeeker.com/forum/forum/api/wordpress/zeeker/usertoken',
			array(
				'httpversion' => '1.1',
				'sslverify'   => false,
				'timeout'     => 10,
				'headers'     => array(
					'Content-Type' => 'application/json',
				),
				'body'        => wp_json_encode(
					array(
						'wordpressToken' => '',
						'name'           => '',
						'email'          => get_option( 'admin_email' ),
					)
				),
			)
		);

		if ( is_wp_error( $user_token ) ) {
			return $user_token;
		}

		return json_decode( $user_token['body'], true );
	}

	/**
	 * Create Zeeker widget through API.
	 *
	 * @param string $organization_name Organization name.
	 * @param string $widget_name Name for Widget.
	 * @param string $token Authorization token.
	 *
	 * @return array|WP_Error
	 */
	private function create_widget( $organization_name, $widget_name, $widget_category, $token ) {
		$widget_data = wp_remote_post(
			'https://api.zeeker.com/forum/forum/api/wordpress/zeeker/createWidget',
			array(
				'httpversion' => '1.1',
				'sslverify'   => false,
				'timeout'     => 10,
				'headers'     => array(
					'Content-Type'  => 'application/json',
					'Authorization' => "Bearer {$token}",
				),
				'body'        => wp_json_encode(
					array(
						'name'       => $organization_name,
						'widgetName' => $widget_name,
						'category'   => $widget_category,
						'domain'     => get_option( 'siteurl' ),
					)
				),
			)
		);

		if ( is_wp_error( $widget_data ) ) {
			return $widget_data;
		}

		return json_decode( $widget_data['body'], true );
	}

	/**
	 * Get Zeeker widget data through API.
	 *
	 * @param string $widget_id Zeeker widget ID.
	 * @param string $token Zeeker user authorization token.
	 *
	 * @return array|WP_Error
	 */
	private function get_widget_data( $widget_id, $token ) {
		$widget_data = wp_remote_post(
			"https://api.zeeker.com/forum/forum/api/wordpress/zeeker/widgetSettings?widgetId={$widget_id}",
			array(
				'httpversion' => '1.1',
				'sslverify'   => false,
				'timeout'     => 10,
				'headers'     => array(
					'Content-Type'  => 'application/json',
					'Authorization' => "Bearer {$token}",
				),
			)
		);

		if ( is_wp_error( $widget_data ) ) {
			return $widget_data;
		}

		$body = json_decode( $widget_data['body'], true );
		if ( isset( $body['error'] ) && 'invalid_token' === $body['error'] ) {
			return new WP_Error( 400, esc_html__( 'Failed to get widget data. Invalid access token.', 'zeeker' ) );
		}

		if ( ! $body || empty( $body ) ) {
			return new WP_Error( 404, esc_html__( 'Failed to get widget data. Invalid Widget ID.', 'zeeker' ) );
		}

		return $body;
	}
}
