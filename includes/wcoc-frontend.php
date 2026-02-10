<?php
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'wcoc-frontend-css', WCOC_URL . 'assets/css/frontend.css' );

    wp_enqueue_script( 'wcoc-frontend', WCOC_URL . 'assets/js/frontend.js', ['jquery'], false, true );

    wp_localize_script( 'wcoc-frontend', 'WCOC', [
        'ajax' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'wcoc_nonce' ),
        'user' => get_current_user_id(),
    ]);
});

add_action( 'woocommerce_view_order', function( $order_id ) {
    if ( ! is_user_logged_in() ) return;

    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    // Ensure customer owns order
    if ( (int) $order->get_user_id() !== get_current_user_id() ) return;

    // MARK AS READ FOR CUSTOMER
    $order->update_meta_data( '_wcoc_unread_for_customer', 0 );
    $order->save();
 ?>
 <div id="wcoc-box" data-order="<?php echo esc_attr( $order_id ); ?>">
    
     <h3 class="wcoc-title">Order Chat</h3>

    <div id="wcoc-messages" class="wcoc-messages"></div>

    <div class="wcoc-input-area">
        <textarea id="wcoc-text" placeholder="Type your message..."></textarea>
        <button id="wcoc-send" class="button button-primary">Send</button>
    </div>

  </div>
 <?php
});

add_filter( 'woocommerce_my_account_my_orders_actions', function( $actions, $order ) {

    if ( ! $order instanceof WC_Order ) {
        return $actions;
    }

    // HPOS-safe way
    $count = (int) $order->get_meta( '_wcoc_unread_for_customer' );

    if ( $count > 0 ) {
        $actions['wcoc_unread'] = [
            'name' => sprintf( 'Chat (%d)', $count ),
            'url'  => $order->get_view_order_url(),
        ];
    }

    error_log(
        'Customer unread for order ' . $order->get_id() . ': ' . $count
    );

    return $actions;

}, 10, 2 );