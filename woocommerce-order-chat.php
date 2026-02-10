<?php
/**
 * Plugin Name: WooCommerce Order Chat
 * Description: Adds real-time chat between customer and admin inside WooCommerce orders.
 * Version: 1.0.0
 * Author: Rutva Prajapati
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'WCOC_VERSION' ) ) { define( 'WCOC_VERSION', '1.0.0' ); }
define( 'WCOC_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCOC_URL', plugin_dir_url( __FILE__ ) );

// HPOS Compatibility
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
});

// Include required files
require_once WCOC_PATH . 'includes/class-wcoc-db.php';
require_once WCOC_PATH . 'includes/wcoc-ajax.php';
require_once WCOC_PATH . 'includes/wcoc-frontend.php';
require_once WCOC_PATH . 'includes/wcoc-admin.php';

// Create Table
register_activation_hook( __FILE__, ['WCOC_DB', 'create_table'] );