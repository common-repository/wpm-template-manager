<?php

// Add search results page
function wpm_add_templates_settings_page() {
	add_submenu_page(
		'edit.php?post_type=' . WPM_TPL_POST_TYPE_SLUG,
		'Template Manager Settings',
		'Settings',
		'manage_options',
		'template_manager_settings',
		'wpm_admin_template_manager_settings_page'
	);
}

add_action( 'admin_menu', 'wpm_add_templates_settings_page' );

function wpm_admin_template_manager_settings_page() {
	$post_type_hidden  = apply_filters( 'wpm_template_manager_post_type', array(
		'revision',
		'page',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		WPM_TPL_POST_TYPE_SLUG,
		'shop_order',
		'shop_order_refund',
		'shop_coupon',
		'shop_webhook',
		'acf-field',
		'acf-field-group'
	) );
	$taxonomies_hidden = apply_filters( 'wpm_template_manager_taxonomies', array(
		'nav_menu',
		'link_category',
		'post_format',
		'product_shipping_class',
		'media_section',
	) );

	$post_types = array_diff( get_post_types(), $post_type_hidden );
	$taxonomies = array_diff( get_taxonomies(), $taxonomies_hidden );

	$args = array(
		'post_type' => WPM_TPL_POST_TYPE_SLUG,
		'posts_per_page' => -1
	);
	$wp_query = new WP_Query( $args );

	$options  = get_option( 'wpm_template_manager_settings' ) ? get_option( 'wpm_template_manager_settings' ) : array();

	// Save options
	if ( isset( $_POST ) && ! empty( $_POST ) ) {
		if ( ! isset( $_POST['wpm_template_manager_nonce_field'] ) || ! wp_verify_nonce( $_POST['wpm_template_manager_nonce_field'], 'wpm_template_manager_action' ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		} else {
			$options = $_POST;
			unset( $options['wpm_template_manager_nonce_field'] );
			unset( $options['_wp_http_referer'] );
			foreach ($options as $key => $value) {
				if ( 0 !== strpos( $key, 'archive_' ) && 0 !== strpos( $key, 'single_' ) && 0 !== strpos( $key, 'tax_' ) && 0 !== strpos( $key, 'single_404' ) && 0 !== strpos( $key, 'single_search' ) ) {
					unset( $options[ $key ] );
				} else {
					if ( $key !== sanitize_text_field( $key ) ) {
						unset( $options[ $key ] );
					} else {
						$options[ $key ] = sanitize_text_field( $value );
					}
				}
			}
			update_option( 'wpm_template_manager_settings', $options );
		}
	}
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Template Manager Settings</h1>
		<form method="post" class="template-options-box">
			<?php wp_nonce_field( 'wpm_template_manager_action', 'wpm_template_manager_nonce_field' ); ?>
			<h3>Post Types</h3>
			<table class="wp-list-table widefat fixed striped">
				<tr>
					<th></th>
					<th>Archive</th>
					<th>Single</th>
				</tr>
				<?php foreach ( $post_types as $type ) : ?>
					<tr>
						<td><?php echo $type; ?></td>
						<td>
							<select name="archive_<?php echo $type; ?>">
								<option value="empty">Not selected</option>
								<?php echo wpm_get_select_option( $wp_query, $options['archive_' . $type] ); ?>
							</select>
						</td>
						<td>
							<select name="single_<?php echo $type; ?>">
								<option value="empty">Not selected</option>
								<?php echo wpm_get_select_option( $wp_query, $options['single_' . $type] ); ?>
							</select>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<h3>Taxonomies</h3>
			<table class="wp-list-table widefat fixed striped">
				<?php foreach ( $taxonomies as $type ) : ?>
					<tr>
						<td class="column-title"><?php echo $type; ?></td>
						<td>

							<select name="tax_<?php echo $type; ?>">
								<option value="empty">Not selected</option>
								<?php echo wpm_get_select_option( $wp_query, $options[ 'tax_' . $type ] ); ?>
							</select>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<h3>Other</h3>
			<table class="wp-list-table widefat  striped">

				<tr>
					<td class="column-title">404</td>

					<td>
						<select name="single_404">
							<option value="empty">Not selected</option>
							<?php echo wpm_get_select_option( $wp_query, $options['single_404'] ); ?>
						</select>
					</td>
				</tr>

				<tr>
					<td class="column-title">Search results</td>

					<td>
						<select name="single_search">
							<option value="empty">Not selected</option>
							<?php echo wpm_get_select_option( $wp_query, $options['single_search'] ); ?>
						</select>
					</td>
				</tr>

			</table>
			<div class="tablenav bottom">

				<div class="alignleft actions bulkactions">
					<button type="submit" class="button button-primary button-large">Save settings</button>
				</div>


			</div>

		</form>
	</div>
	<?php
}
