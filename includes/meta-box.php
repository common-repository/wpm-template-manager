<?php

/**
* Add WPM Template Manager Meta box to page edit screen
*
* @since 1.0.0
*/
function wpm_add_meta_box() {
    add_meta_box( 'wpm-meta-box', 'WPM Template Manager', 'wpm_meta_box_render', 'page', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'wpm_add_meta_box' );

function wpm_meta_box_render( $post, $box ) {
	global $wpm_template_manager;
	$templates = $wpm_template_manager->get_templates();
	//getting curent
	$wpm_page_template = get_post_meta( $post->ID, '_wpm_page_template', true );
	//CSRF protection
	wp_nonce_field( WPM_TEMPLATE_MANAGER_PLUGIN_FILE_NAME, 'wpm_save_meta_box' );
	//meta box form ?>
	<p>WPM Template:
		<select name="wpm_page_template">
			<option value="empty">Not selected</option>
			<?php foreach ($templates as $template => $tempate_title) : ?>
					<option value="<?php echo $template ?>" <?php selected( $template, $wpm_page_template ) ?>><?php echo $tempate_title ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

function wpm_save_meta_box( $post_id ) {
	if ( isset( $_POST['wpm_page_template'] ) ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		check_admin_referer( WPM_TEMPLATE_MANAGER_PLUGIN_FILE_NAME, 'wpm_save_meta_box' );
		update_post_meta( $post_id, '_wpm_page_template', sanitize_text_field( $_POST['wpm_page_template'] ) );
	}
}
add_action( 'save_post', 'wpm_save_meta_box' );