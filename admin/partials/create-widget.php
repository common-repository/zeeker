<?php
/**
 * Create widget tab contents.
 *
 * @since      1.0.0
 *
 * @package    Zeeker
 * @subpackage Zeeker/admin/partials
 */

if ( $zeeker_widget_id ) {
	return;
}
?>

<div class="zeeker-tab-content zeeker-widget-instruction <?php echo 'zeeker-widget-instruction' === $active_tab ? 'zeeker-active-content' : ''; ?>">
	<h2><?php esc_html_e( 'You can create and deploy the widget via two different approaches.', 'zeeker' ); ?></h2>	
	<div class="zeeker-accordion">

		<div class="zeeker-accordion-main">
			<div class="zeeker-accordion-title" data-a-control>
				<h4><?php esc_html_e( 'Option 1: Automatic (Recommended)', 'zeeker' ); ?></h4>
			</div>
			<div class="zeeker-accordion-cnt-wrp" data-a-content>
				<div class="zeeker-accordion-inner-wrp">
					<div class="zeeker-accordion-content">
						<p><?php esc_html_e( 'Auto-create a conversation widget and deploy to your website in one click!', 'zeeker' ); ?></p>

						<div class="zeeker-create-widget-form zeeker-form widget-card">
							<h2><?php esc_html_e( 'Widget Detail', 'zeeker' ); ?></h2>

							<form action="" method="post" id="create-widget">
								<?php wp_nonce_field( 'zeeker-create-widget-nonce', 'zeeker-create-widget-nonce' ); ?>
								<div class="form-group">
									<label for="zeeker-organization-name">
										<?php esc_html_e( 'Name of the Organization', 'zeeker' ); ?><span>*</span>
									</label>
									<input type="text" required id="zeeker-organization-name" name="zeeker-organization-name" value="<?php echo esc_attr( get_option( 'blogname' ) ); ?>" />
								</div>

								<div class="form-group">
									<label for="zeeker-domain-url">
										<?php esc_html_e( 'Domain URL', 'zeeker' ); ?><span>*</span>
									</label>
									<input type="text" required disabled id="zeeker-domain-url" name="zeeker-domain-url" value="<?php echo esc_attr( get_option( 'siteurl' ) ); ?>" />
								</div>

								<div class="form-group">
									<label for="zeeker-widget-name">
										<?php esc_html_e( 'Widget Name', 'zeeker' ); ?><span>*</span>
									</label>
									<input type="text" required id="zeeker-widget-name" name="zeeker-widget-name" value="<?php echo esc_attr( get_option( 'blogname' ) ); ?>" />
								</div>

								<div class="form-group">
									<label for="zeeker-widget-category">
										<?php esc_html_e( 'Widget Category', 'zeeker' ); ?><span>*</span>
									</label>
									<select id="zeeker-widget-category" name="zeeker-widget-category" required>
										<option value="News" selected><?php esc_html_e( 'News', 'zeeker' ); ?></option>
										<option value="Blog"><?php esc_html_e( 'Blog', 'zeeker' ); ?></option>
										<option value="Ecommerce"><?php esc_html_e( 'Ecommerce', 'zeeker' ); ?></option>
										<option value="Personal Website"><?php esc_html_e( 'Personal Website', 'zeeker' ); ?></option>
										<option value="Local Business"><?php esc_html_e( 'Local Business', 'zeeker' ); ?></option>
									</select>
								</div>

								<div class="form-group form-last">
									<button type="submit" class="button-primary"><?php esc_html_e( 'Create Widget', 'zeeker' ); ?></button>
									<a href="https://www.zeeker.com/app/privacy.html" target="_blank" rel="noindex,nofollow" class="zeeker-privacy"><?php esc_html_e( 'Privacy Policy', 'zeeker' ); ?></a>
								</div>
							</form>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="zeeker-accordion-main">
			<div class="zeeker-accordion-title" data-a-control>
				<h4><?php esc_html_e( 'Option 2: Deploy a widget created on https://portal.zeeker.com', 'zeeker' ); ?></h4>
			</div>

			<div class="zeeker-accordion-cnt-wrp" data-a-content>
				<div class="zeeker-accordion-inner-wrp">
					<div class="zeeker-accordion-content">

						<ul>
							<li>
								<?php echo wp_kses_post( __( 'Go to <a href="https://portal.zeeker.com/dashboard" target="_blank">https://portal.zeeker.com/dashboard</a> and click the &ldquo;Get Started&rdquo; button. You will be prompted to login with your Zeeker ID. If you don&lsquo;t have a Zeeker ID, you can create one by signing up.', 'zeeker' ) ); ?>
							</li>
							<li>
								<?php echo wp_kses_post( __( 'Once logged in, you will start the widget creation process (4 steps). In the first step, you will be prompted to enter your domain. Include only the Top Level Domain (TLD) of the WordPress website where you want to deploy Zeeker.', 'zeeker' ) ); ?>
							</li>
							<li>
								<?php echo wp_kses_post( __( 'Complete the widget customization and enhancement steps. You can always change/modify these options later.', 'zeeker' ) ); ?>
							</li>
							<li>
								<?php echo wp_kses_post( __( 'Copy the Widget ID provided in the last step of the widget creation flow and paste it in the below form and click &ldquo;Add Widget&rdquo;.', 'zeeker' ) ); ?>
							</li>
						</ul>

						<div class="zeeker-form widget-card">
							<div class="widget-display-options">
								<form method="post" action="">
									<input type="hidden" name="custom-zeeker" value="yes" />
									<?php
									wp_nonce_field( 'zeeker-display-widget-nonce', 'zeeker-display-widget-nonce' );

									$this->zeeker_widget_display_cb( array( 'label_for' => 'zeeker_widget_display' ) );
									?>

									<div class="form-group form-last">
										<button type="submit" class="button-primary"><?php esc_html_e( 'Add Widget', 'zeeker' ); ?></button>
									</div>
								</form>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>
</div> <!-- .zeeker-tab-content.zeeker-widget-instruction -->
