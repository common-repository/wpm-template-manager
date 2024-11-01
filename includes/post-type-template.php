<?php
/**
 * Register a product post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */


$labels = array(
	'name'                => __( 'Templates' ),
	'singular_name'       => __( 'Template' ),
	'add_new_item'        => __( 'Add New Template' ),
	'add_new'             => __( 'Add New' ),
	'edit_item'           => __( 'Edit Template' ),
);

$args = array(
	"label"               => __( 'Templates' ),
	"labels"              => $labels,
	"description"         => "",
	"public"              => false,
	"publicly_queryable"  => false,
	"show_ui"             => true,
	"show_in_rest"        => false,
	"rest_base"           => "",
	"has_archive"         => false,
	"show_in_menu"        => true,
	"exclude_from_search" => true,
	"capability_type"     => "post",
	"map_meta_cap"        => true,
	"hierarchical"        => false,
	"rewrite"             => false,
	"query_var"           => false,
	"menu_icon"           => "dashicons-grid-view",
	"supports"            => array( "title", "editor" ),
);

register_post_type( WPM_TPL_POST_TYPE_SLUG, $args );

/**
* Filter for template post type update admin notices
*
* @since 1.1.0
*/
function wpm_template_post_updated_messages( $messages ) {
	$messages[WPM_TPL_POST_TYPE_SLUG] = array(
		 0 => '', // Unused. Messages start at index 1.
		 1 => __( 'Template updated.' ),
		 2 => '',
		 3 => '',
		 4 => __( 'Template updated.' ),
		 5 => '',
		 6 => __( 'Template published.' ),
		 7 => __( 'Template saved.' ),
		 8 => __( 'Template submitted.' ),
		 9 => '',
		10 => __( 'Template draft updated.' ),
	);
	return $messages;
}
add_filter( 'post_updated_messages', 'wpm_template_post_updated_messages' );

/**
* Filter for template post type bulk actions admin notices
*
* @since 1.1.0
*/
function wpm_template_post_bulk_messages( $bulk_messages, $bulk_counts ) {
	$bulk_messages[WPM_TPL_POST_TYPE_SLUG] = array(
		'updated'   => _n( '%s template updated.', '%s templates updated.', $bulk_counts['updated'] ),
		'locked'    => ( 1 == $bulk_counts['locked'] ) ? __( '1 template not updated, somebody is editing it.' ) :
		                   _n( '%s template not updated, somebody is editing it.', '%s templates not updated, somebody is editing them.', $bulk_counts['locked'] ),
		'deleted'   => _n( '%s template permanently deleted.', '%s templates permanently deleted.', $bulk_counts['deleted'] ),
		'trashed'   => _n( '%s template moved to the Trash.', '%s templates moved to the Trash.', $bulk_counts['trashed'] ),
		'untrashed' => _n( '%s template restored from the Trash.', '%s templates restored from the Trash.', $bulk_counts['untrashed'] ),
	);
	return $bulk_messages;
}
add_filter( 'bulk_post_updated_messages', 'wpm_template_post_bulk_messages', 10, 2 );