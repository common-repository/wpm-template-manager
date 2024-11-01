<?php
/*
* Plugin name: Template Manager by WordPress Monsters
* Description: This is a temlate manager plugin for easy WordPress development from scratch when you are use Page Builders plugins.
* Author: WordPress Monsters
* Author URI: http://www.wpmonsters.org/
* Version: 1.1.0
* Tested up to: 4.7
*/

if ( ! function_exists( 'pr' ) ) {
	function pr($val) {
		echo '<pre class="debug-tool">';
		print_r( $val );
		echo "</pre>";
	}
}

define( 'WPM_TEMPLATE_MANAGER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPM_TEMPLATE_MANAGER_PLUGIN_FILE_NAME', plugin_basename( __FILE__ ) );
define( 'WPM_TPL_POST_TYPE_SLUG', 'wpm-template' ); // don't use underscores _

require_once( WPM_TEMPLATE_MANAGER_PLUGIN_DIR .  'includes/post-type-template.php' );
require_once( WPM_TEMPLATE_MANAGER_PLUGIN_DIR .  'includes/enqueues.php' );
require_once( WPM_TEMPLATE_MANAGER_PLUGIN_DIR .  'includes/class-wpm-template-manager.php' );
require_once( WPM_TEMPLATE_MANAGER_PLUGIN_DIR .  'includes/options-page.php' );

global $wpm_template_manager;
$wpm_template_manager = new WPM_Template_Manager();

require_once( WPM_TEMPLATE_MANAGER_PLUGIN_DIR .  'includes/meta-box.php' );


function wpm_sample_admin_notice__success() {
	if ( isset( $_POST['wpm_template_manager_nonce_field'] ) && ! empty( $_POST['wpm_template_manager_nonce_field'] ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php _e( 'Template Settings updated.', 'wpm-template-manager' ); ?></p>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'wpm_sample_admin_notice__success' );

function wpm_get_select_option( $wp_query, $options_elem ) {
	if ( $wp_query->have_posts() ) {
		$option = '';
		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();
			$option .= '<option ' . selected( $options_elem, get_the_ID()) . ' value="' . get_the_ID() . '">' . get_the_title() . '</option>';
		}
	}
	wp_reset_query();
	return $option;
}

/**
 * Gets template content for specified place.
 *
 * @since 1.0.0
 *
 * @param string $place. Can be 'before' or 'after'.
 */
function wpm_get_tm_content_for( $place ) {
	global $wpm_template_manager;

	if ( $place != 'before' && $place != 'after') {
		return;
	}
	$template_id = $wpm_template_manager->get_template();

	if ( ! $template_id ) {
		return;
	}

	$content_post = get_post( $template_id );

	$content = $content_post->post_content;
	$content_parts = explode( '[wpm-template-content]', $content, 2 );
	if ( $place == 'before') {
		$content = $content_parts[0];
	} else {
		$content = $content_parts[1];
	}
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
	return $content;
}

/**
 * Prints template content after get_header()
 *
 * @since 1.0.0
 *
 * @param string $name. Header template name passed by the hook.
 */
function wpm_before_content( $name ) {
	remove_action( 'get_header', 'wpm_before_content' );
	get_header( $name );
	echo wpm_get_tm_content_for('before');
	add_action( 'get_header', 'wpm_before_content' );
	return;
}
add_action( 'get_header', 'wpm_before_content' );

/**
 * Prints template content before get_footer().
 *
 * @since 1.0.0
 *
 * @param string $name. Footer template name passed by the hook.
 */
function wpm_after_content( $name ) {
	echo wpm_get_tm_content_for('after');
}
add_action( 'get_footer', 'wpm_after_content' );

/**
* Add default content to new template
*
* @sinse 1.1.0
*/
function wpm_set_default_template_content( $content, $post ) {
	if ( $post->post_type == WPM_TPL_POST_TYPE_SLUG ) {
		$content = '[wpm-template-content]';
	}
	return $content;
}
add_filter( 'default_content', 'wpm_set_default_template_content', 10, 2 );