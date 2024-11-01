<?php
/**
 * Support tab contents.
 *
 * @since 1.0.0
 *
 * @package Zeeker
 * @subpackage Zeeker/admin/partials
 */

?>

<div class="zeeker-tab-content zeeker-widget-support <?php echo 'zeeker-widget-support' === $active_tab ? 'zeeker-active-content' : ''; ?>">
	<h2><?php esc_html_e( 'How to Get Support?', 'zeeker' ); ?></h2>
	<a href="https://www.zeeker.com/faq" target="_blank" class="zeeker-support-link">
		<img src="<?php echo esc_url( ZEEKER_DIR_URL . 'admin/assets/images/faq.png' ); ?>" alt="FAQs" width="150" height="150" >
		FAQs
	</a>

	<a href="https://www.zeeker.com/contact" target="_blank" class="zeeker-support-link">
		<img src="<?php echo esc_url( ZEEKER_DIR_URL . 'admin/assets/images/call.png' ); ?>" alt="Contact" width="150" height="150" >
		Contact Us
	</a>

	<a href="https://wordpress.org/support/plugin/zeeker/" target="_blank" class="zeeker-support-link">
		<img src="<?php echo esc_url( ZEEKER_DIR_URL . 'admin/assets/images/discussion.png' ); ?>" alt="Forum" width="150" height="150" >
		Community Forum
	</a>
</div> <!-- .zeeker-tab-content.zeeker-widget-support -->
