<?php
/**
 * Configure widget tab contents.
 *
 * @since 1.0.0
 *
 * @package Zeeker
 * @subpackage Zeeker/admin/partials
 */

if ( ! $zeeker_widget_id ) {
	return;
}
?>

<div class="zeeker-tab-content zeeker-widget-configure <?php echo 'zeeker-widget-configure' === $active_tab ? 'zeeker-active-content' : ''; ?>">

	<?php
	$active_class = '';
	if ( ! $widget_created_with || 'widget-id' === $widget_created_with ) {
		if ( 'widget-id' === $widget_created_with ) {
			$active_class = 'zeeker-active-content';
		}
		?>
		<div id="zeeker-custom-widget-id" class="zeeker-widget-action-container <?php echo esc_attr( $active_class ); ?>">
			<div class="zeeker-form widget-card">
				<div class="widget-display-options">
					<form method="post" action="">
						<input type="hidden" name="custom-zeeker" value="yes" />
						<?php
						wp_nonce_field( 'zeeker-display-widget-nonce', 'zeeker-display-widget-nonce' );

						$this->zeeker_widget_display_cb( array( 'label_for' => 'zeeker_widget_display' ) );
						?>

						<div class="form-group form-last">
							<button type="submit" class="button-primary"><?php esc_html_e( 'Save Options', 'zeeker' ); ?></button>
						</div>
					</form>
				</div>
			</div>

			<?php if ( $zeeker_widget_id ) { ?>
				<div class="widget-preview-actions" style="justify-content: center;">
					<div class="widget-preview-action">
						<a href="javascript:void(0);" id="zeeker-delete-widget" data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_widget-' . $zeeker_widget_id ) ); ?>" class="button-primary"><?php esc_html_e( 'Delete Widget', 'zeeker' ); ?></a>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}
	?>

	<?php
	$active_class = '';
	if ( ! $widget_created_with || 'widget-api' === $widget_created_with ) {
		if ( 'widget-api' === $widget_created_with ) {
			$active_class = 'zeeker-active-content';
		}

		$zeeker_widget__id             = isset( $zeeker_widget_data['id'] ) ? $zeeker_widget_data['id'] : '';
		$zeeker_widget_bg_color        = isset( $zeeker_widget_data['backgroundColor'] ) ? $zeeker_widget_data['backgroundColor'] : '#a9a4a3';
		$zeeker_widget_position        = isset( $zeeker_widget_data['position'] ) ? $zeeker_widget_data['position'] : 'left';
		$zeeker_icon_type              = isset( $zeeker_widget_data['iconType'] ) ? $zeeker_widget_data['iconType'] : 'rounded';
		$zeeker_icon_color             = isset( $zeeker_widget_data['iconColor'] ) ? $zeeker_widget_data['iconColor'] : '#ff5836';
		$zeeker_widget_wc_text_enabled = isset( $zeeker_widget_data['welcomeTextEnabled'] ) ? $zeeker_widget_data['welcomeTextEnabled'] : false;
		?>
		<div id="zeeker-create-widget" class="zeeker-widget-action-container <?php echo esc_attr( $active_class ); ?>">

			<div class="zeeker-setting-preview" style="<?php echo empty( $zeeker_widget_data ) ? '' : ''; ?>">
				<div class="zeeker-widget-preview">
					<h2>
						<?php esc_html_e( 'Your Widget will look like this.', 'zeeker' ); ?>
						<a 
							href="javascript:void(0);" 
							class="zeeker-refresh-widget zeeker-tooltip" 
							data-tooltip="<?php echo esc_attr__( 'Refresh Widget', 'zeeker' ); ?>"
							style="float: right;"
						>
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
								<path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
							</svg>
						</a>
					</h2>

					<div class="zeeker-widget-preview-bg">
						<div class="widget-preview-container" id="widget-preview-container">
							<div class="widget-preview">
								<div class="widget-modal flex-<?php echo esc_attr( $zeeker_widget_position ); ?>">
									<div class="widget-modal-body">
										<div class="widget-modal-header" style="background: <?php echo esc_attr( $zeeker_widget_bg_color ); ?>">
											<div class="widget-modal-title">
												<a href="javascript:void(0);">LOGIN</a>
												<a href="javascript:void(0);">SIGNUP</a>
												<span>x</span>
											</div>	 
										</div>

										<div class="widget-modal-content">      	
											<div class="widget-modal-card">
												<div class="card-icons">
													<a href="javascript:void(0);" class="bookmark" style="color: <?php echo esc_attr( $zeeker_widget_bg_color ); ?>">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
															<path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
														</svg>
													</a>
													<a href="javascript:void(0);" class="edit" style="color: <?php echo esc_attr( $zeeker_widget_bg_color ); ?>">
														<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
															<path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
															<path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
														</svg>
													</a>
												</div>

												<div class="card-textarea">
													<textarea placeholder="Share your thoughts..." readonly="readonly"></textarea>
												</div>

												<div class="card-footer">
													<button type="button" class="button" style="background: <?php echo esc_attr( $zeeker_widget_bg_color ); ?>">Post</button>
												</div>
											</div>
											<div class="comment-links">
												<a href="javascript:void(0);" class="" style="border-bottom-color: <?php echo esc_attr( $zeeker_widget_bg_color ); ?>"><strong>Popular</strong></a>
												<a href="javascript:void(0);">Recent</a>
											</div>
											<div class="dummy-comments">
												<div class="dummy-comment">
													<div class="comment"></div>
													<div class="comment"></div>
													<div class="comment"></div>
												</div>
												<div class="dummy-comment">
													<div class="comment"></div>
													<div class="comment"></div>
													<div class="comment"></div>
												</div>
											</div>
										</div>

										<div class="widget-modal-footer">
											<a href="javascript:void(0);">FAQs</a>
											<a href="javascript:void(0);">Terms</a>
											<a href="javascript:void(0);">Privacy</a>
											<div class="dummy-comment">
												<div class="comment"></div>
											</div>
										</div>
									</div>
								</div>
								<div class="widget-icon flex-<?php echo esc_attr( $zeeker_widget_position ); ?>">
									<div class="icon <?php echo esc_attr( $zeeker_icon_type ); ?>" style="background: <?php echo esc_attr( $zeeker_icon_color ); ?>">
										<span>
											<img src="https://apps.zeeker.com/image/Icon-50.png" width="50" height="50" alt="Zeeker Icon" />
										</span>
									</div>

									<?php if ( $zeeker_widget_wc_text_enabled ) { ?>
										<div class="engage-message">
											<?php
											if ( isset( $zeeker_widget_data['welcomeText'] ) ) {
												echo esc_html( $zeeker_widget_data['welcomeText'] );
											}
											?>
										</div>
									<?php } ?>

								</div>
							</div>
						</div>
					</div>

					<div class="widget-preview-actions">
						<div class="widget-preview-action">
							<a
								href="https://portal.zeeker.com/widget/update/<?php echo esc_attr( $zeeker_widget__id ); ?>/?token=<?php echo esc_attr( $access_token ); ?>" 
								class="button-secondary zeeker-tooltip"
								data-tooltip="<?php echo esc_attr__( 'Customize the look and feel of Widget.', 'zeeker' ); ?>"
								target="_blank" 
								rel="noindex,nofollow"
							>
								<?php esc_html_e( 'Edit', 'zeeker' ); ?>
							</a>
						</div>

						<div class="widget-preview-action">
							<a 
								href="https://portal.zeeker.com/widget/stats/<?php echo esc_attr( $zeeker_widget__id ); ?>/?token=<?php echo esc_attr( $access_token ); ?>" 
								class="button-secondary zeeker-tooltip" 
								data-tooltip="<?php echo esc_attr__( 'View statistics about your Widget.', 'zeeker' ); ?>"
								target="_blank" 
								rel="noindex,nofollow"
							>
								<?php esc_html_e( 'Analytics', 'zeeker' ); ?>
							</a>
						</div>

						<div class="widget-preview-action">
							<a 
								href="https://portal.zeeker.com/widget/moderate/<?php echo esc_attr( $zeeker_widget__id ); ?>/?token=<?php echo esc_attr( $access_token ); ?>" 
								class="button-secondary zeeker-tooltip" 
								data-tooltip="<?php echo esc_attr__( 'Moderate the conversation taking place in the Widget.', 'zeeker' ); ?>"
								target="_blank" 
								rel="noindex,nofollow"
							>
								<?php esc_html_e( 'Moderate', 'zeeker' ); ?>
							</a>
						</div>
					</div>

					<div class="widget-preview-actions" style="justify-content: center;">
						<div class="widget-preview-action">
							<a href="javascript:void(0);" id="zeeker-delete-widget" data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_widget-' . $zeeker_widget_id ) ); ?>" class="button-primary"><?php esc_html_e( 'Delete Widget', 'zeeker' ); ?></a>
						</div>
					</div>
				</div>
				<div class="zeeker-widget-data">
					<div class="widget-info widget-card">
						<p>
							<strong><?php esc_html_e( 'Widget Name', 'zeeker' ); ?>:</strong>
							<span class="widget-name">
								<?php
								if ( isset( $zeeker_widget_data['widgetName'] ) ) {
									echo esc_html( $zeeker_widget_data['widgetName'] );
								}
								?>
							</span>
						</p>
						<p>
							<strong><?php esc_html_e( 'Widget ID', 'zeeker' ); ?>:</strong>
							<span class="widget-uuid">
								<?php
								if ( isset( $zeeker_widget_data['uuid'] ) ) {
									echo esc_html( $zeeker_widget_data['uuid'] );
								}
								?>
							</span>
						</p>
						<p>
							<strong><?php esc_html_e( 'Owner Email', 'zeeker' ); ?>:</strong>
							<span class="widget-owner">
								<?php
								if ( isset( $zeeker_widget_data['owner'] ) ) {
									echo esc_html( $zeeker_widget_data['owner']['email'] );
								}
								?>
							</span>
						</p>
						<p>
							<?php
							$is_active = get_option( '_zeeker_widget_active', false );
							?>
							<strong><?php esc_html_e( 'Widget Status (On/Off)', 'zeeker' ); ?>:</strong>
							<span class="widget-status">
								<label class="zeeker-switch">
									<input type="checkbox" <?php checked( $is_active, false ); ?> class="zeeker-widget-status" data-nonce="<?php echo esc_attr( wp_create_nonce( 'update_widget_status-' . $zeeker_widget_id ) ); ?>">
									<span class="zeeker-slider"></span>
									<span 
										class="zeeker-slider-tooltip"
										data-enabled="<?php echo esc_attr__( 'Click to turn "Off" Widget.', 'zeeker' ); ?>"
										data-disabled="<?php echo esc_attr__( 'Click to turn "On" Widget.', 'zeeker' ); ?>"
									>
									</span>
								</label>
							</span>
						</p>
					</div>

					<div class="widget-display zeeker-form widget-card">
						<div class="widget-display-options">
							<form method="post" action="">
								<?php
								wp_nonce_field( 'zeeker-display-widget-nonce', 'zeeker-display-widget-nonce' );

								$this->zeeker_widget_display_cb( array( 'label_for' => 'zeeker_widget_display' ) );
								?>

								<div class="form-group form-last">
									<button type="submit" class="button-primary"><?php esc_html_e( 'Save Options', 'zeeker' ); ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<div class="zeeker-toast-bar">This is toast message.</div>
</div> <!-- .zeeker-tab-content.zeeker-widget-configure -->
