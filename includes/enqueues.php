<?php
/**
 * Add admin enqueues
 */
function wpm_layout_builder_assets() {
    // wp_register_script( 'wpm_template_manager_script', plugins_url( 'assets/js/wpm-template-manager.js', __DIR__ ), array( 'jquery' ) );
    // wp_enqueue_script( 'wpm_template_manager_script' );

    wp_register_style( 'wpm_template_manager_styles', plugins_url( 'assets/css/admin-template-manager.css', __DIR__ ));
    wp_enqueue_style( 'wpm_template_manager_styles' );
}
add_action( 'admin_enqueue_scripts', 'wpm_layout_builder_assets' );
