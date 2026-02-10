<?php
add_action( 'admin_enqueue_scripts', function ( $hook ) {

    $screen = get_current_screen();
    if ( ! $screen ) {
        return;
    }

    wp_enqueue_style(
        'wcoc-admin-css',
        WCOC_URL . 'assets/css/admin.css',
        [],
        WCOC_VERSION
    );

    wp_enqueue_script(
        'wcoc-admin',
        WCOC_URL . 'assets/js/admin.js',
        [ 'jquery' ],
        WCOC_VERSION,
        true
    );

    wp_localize_script(
        'wcoc-admin',
        'WCOC_ADMIN',
        [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'wcoc_nonce' ),
        ]
    );

    /**
     * Orders list page (HPOS)
     */
    if ( $screen->id === 'woocommerce_page_wc-orders' ) {

        wp_enqueue_script(
            'wcoc-admin-orders',
            WCOC_URL . 'assets/js/admin-orders.js',
            [ 'jquery' ],
            WCOC_VERSION,
            true
        );

        // HPOS-safe unread fetch
        $unread = [];

        $orders = wc_get_orders([
            'limit'        => -1,
            'meta_key'     => '_wcoc_unread_for_admin',
            'meta_value'   => 0,
            'meta_compare' => '>',
            'return'       => 'objects',
        ]);

        foreach ( $orders as $order ) {
            $count = (int) $order->get_meta( '_wcoc_unread_for_admin' );
            if ( $count > 0 ) {
                $unread[ $order->get_id() ] = $count;
            }
        }

        wp_localize_script(
            'wcoc-admin-orders',
            'WCOC_ORDERS',
            [
                'unread' => $unread,
            ]
        );
    }
});

// Add meta box
add_action( 'add_meta_boxes', function() {

    // HPOS-safe screen detection
    $screen = 'shop_order';
    if ( function_exists( 'wc_get_page_screen_id' ) ) {
        $screen = wc_get_page_screen_id( 'shop-order' );
    }

    add_meta_box(
        'wcoc_admin_chat_box',
        'Order Chat',
        'wcoc_admin_chat_box_html',
        $screen,
        'normal',
        'core'
    );
});

function wcoc_admin_chat_box_html( $post_or_order ) {

    $order = is_a( $post_or_order, 'WC_Order' )
        ? $post_or_order
        : wc_get_order( $post_or_order->ID );

    if ( ! $order ) {
        return;
    }

    // MARK AS READ FOR ADMIN
    $order->update_meta_data( '_wcoc_unread_for_admin', 0 );
    $order->save();

    echo '<div id="wcoc_admin_box" data-order-id="' . esc_attr( $order->get_id() ) . '">';
    echo '<div id="wcoc_admin_messages"></div>';
    echo '<textarea id="wcoc_admin_message" style="width:100%;"></textarea>';
    echo '<button class="button button-primary" id="wcoc_admin_send">Send</button>';
    echo '</div>';
}