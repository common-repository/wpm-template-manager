<?php

/**
 * This plugin allows you to include templates with your plugin so that they can
 * be added with any theme.
 *
 * @package WPM Template Manager
 * @version 1.0.0
 * @since    0.1.0
 */
if ( ! class_exists( 'WPM_Template_Manager' ) ) :
	class WPM_Template_Manager {

		public $slug;
		public $settings;

		/**
		 *
		 *
		 * @version        1.0.0
		 * @since        1.0.0
		 */
		function __construct( $slug = WPM_TPL_POST_TYPE_SLUG ) {

			$this->slug     = $slug;
			$this->settings = get_option( 'wpm_template_manager_settings' );

			// Add Template to Admin Metabox
			// add_filter( 'theme_page_templates', array( $this, 'add_templates' ) );

			// Change default template
			// add_filter( 'template_include', array( $this, 'template_include' ) );

		} // end constructor

		/**
		 *
		 *
		 * @since    1.0.0
		 */
		// public function template_include( $page_template ) {
		// 	if ( $this->get_template() ) {
		// 		$page_template = WPM_TEMPLATE_MANAGER_PLUGIN_DIR . 'template.php';
		// 	}
		// 	return $page_template;
		// }

		/**
		 *
		 *
		 * @since    1.0.0
		 */
		// public function add_templates( $templates ) {
		// 	$templates['empty'] = '---- Templates Manager ----';
		// 	$templates          = array_merge( $templates, $this->get_templates() );

		// 	return $templates;
		// }

		public function get_archive_template() {
			$post_type = get_post_type();

			$template = '';

			if ( $post_type ) {
				$template = $this->slug . '_' . $this->settings[ 'archive_' . $post_type ];
			}

			return $template;
		}

		public function get_tax_template() {
			$tax_type = get_queried_object()->taxonomy;

			if ( $tax_type ) {
				$template = $this->slug . '_' . $this->settings[ 'tax_' . $tax_type ];
				return $template;
			}
		}

		public function get_single_template() {
			$post_type = get_post_type();

			$template = '';

			if ( $post_type ) {
				$template  = $this->slug . '_' . $this->settings[ 'single_' . $post_type ];
			}

			return $template;
		}

		public function get_page_wpm_template() {
			global $post;
			$wpm_page_template = get_post_meta( $post->ID, '_wpm_page_template', true );

			return $wpm_page_template;
		}

		public function is_template( $template ) {
			$template_a = explode( '_', $template );

			$is_template = ! empty( $template_a[0] )
			               && ! empty( $template_a[1] )
			               && $template_a[0] == $this->slug
			               && get_post_type( $template_a[1] );

			return $is_template;
		}

		/**
		 * Get template for queried object
		 *
		 * @since    1.0.0
		 */
		function get_template() {
			$template = false;

			if ( is_single() && $template = $this->get_single_template() ) :
			elseif ( is_page() && $template = $this->get_page_wpm_template() ) :
			elseif ( is_tax() && $template = $this->get_tax_template() ) :
			elseif ( is_archive() && $template = $this->get_archive_template() ) :
			elseif ( is_404() && $template = $this->slug . '_' . $this->settings['single_404'] ) :
			elseif ( is_search() && $template = $this->slug . '_' . $this->settings['single_search'] ) :
			endif;
			// $template = apply_filters( 'wpm_template', $template );

			if ( $this->is_template( $template ) ) {
				$template_a = explode( '_', $template );

				return $template_a[1];
			}

			return false;
		}

		/**
		 * Get all templates
		 *
		 * @since    1.0.0
		 */
		public function get_templates() {
			$query_args = array(
				'post_type'      => $this->slug,
				'posts_per_page' => - 1
			);

			$templates = array();

			$templates_q = get_posts( $query_args );
			foreach ( $templates_q as $template ) {
				$templates[ $this->slug . '_' . $template->ID ] = $template->post_title;
			}


			return $templates;
		}


	}
endif; // class_exists check
