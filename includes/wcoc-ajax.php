<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_wcoc_send_message', 'wcoc_send_message' );
add_action( 'wp_ajax_nopriv_wcoc_send_message', 'wcoc_send_message' );

function wcoc_send_message() {

    // Security
    check_ajax_referer( 'wcoc_nonce', 'nonce' );

    if ( empty( $_POST['order_id'] ) || empty( $_POST['message'] ) ) {
        wp_send_json_error( [ 'msg' => 'Invalid input' ] );
    }

    $order_id = absint( $_POST['order_id'] );
    $message  = wp_kses_post( wp_unslash( $_POST['message'] ) );

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( [ 'msg' => 'Invalid order' ] );
    }

    // Detect sender correctly (CAPABILITY-based)
    if ( current_user_can( 'manage_woocommerce' ) ) {
        $sender = 'admin';
    } else {
        // Ensure customer owns the order
        if ( (int) $order->get_user_id() !== get_current_user_id() ) {
            wp_send_json_error( [ 'msg' => 'Not allowed' ] );
        }
        $sender = 'customer';
    }

    // Save message
    WCOC_DB::insert_message( $order_id, $sender, $message );

    // Email notification
    wcoc_send_email_notification( $order_id, $message, $sender );

    // Update unread count (HPOS-safe)
    $meta_key = ( $sender === 'customer' )
        ? '_wcoc_unread_for_admin'
        : '_wcoc_unread_for_customer';

    $current = (int) $order->get_meta( $meta_key );
    $order->update_meta_data( $meta_key, $current + 1 );
    $order->save();

    wp_send_json_success( [ 'msg' => 'Message sent' ] );
}

add_action('wp_ajax_wcoc_get_messages', 'wcoc_get_messages');
add_action('wp_ajax_nopriv_wcoc_get_messages', 'wcoc_get_messages');

function wcoc_get_messages() {
  check_ajax_referer('wcoc_nonce', 'nonce');

  $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
  if (!$order_id) wp_send_json_error('Missing order');

  global $wpdb;
  $table = $wpdb->prefix . 'wc_order_chat';

  $messages = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT * FROM $table WHERE order_id = %d ORDER BY id ASC",
      $order_id
    ),
    ARRAY_A
  );
  wp_send_json_success( $messages );
}

function wcoc_send_email_notification( $order_id, $message, $sender_role ) {
  $order = wc_get_order( $order_id );
  if ( ! $order ) return;
  $admin_email    = get_option( 'admin_email' );
  $customer_email = $order->get_billing_email();
  $site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
  if ( $sender_role === 'customer' ) {
    // Email to admin
    $to      = $admin_email;
    $subject = "New Order Chat Message (Order #{$order_id})";
    $heading = "New message from customer";
  } else {
    // Email to customer
    if ( ! $customer_email ) return;
    $to      = $customer_email;
    $subject = "Update on your order #{$order_id}";
    $heading = "New message from admin";
  }
  $body = "
      <h3>{$heading}</h3>
      <p><strong>Order:</strong> #{$order_id}</p>
      <p><strong>Message:</strong></p>
      <blockquote>{$message}</blockquote>
      <p>
          <a href='" . esc_url( wc_get_page_permalink( 'myaccount' ) ) . "'>
              View Order Chat
          </a>
      </p>
  ";
  wp_mail(
    $to,
    $subject,
    $body,
    [
      'Content-Type: text/html; charset=UTF-8',
      'From: ' . $site_name . ' <' . $admin_email . '>',
    ]
  );
}

add_action( 'wp_ajax_wcoc_get_admin_unread_counts', 'wcoc_get_admin_unread_counts' );

function wcoc_get_admin_unread_counts() {

    check_ajax_referer( 'wcoc_nonce', 'nonce' );

    if ( ! current_user_can( 'manage_woocommerce' ) ) {
        wp_send_json_error();
    }

    global $wpdb;

    $results = $wpdb->get_results(
        "
        SELECT post_id, meta_value
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_wcoc_unread_for_admin'
          AND meta_value > 0
        ",
        ARRAY_A
    );

    $data = [];

    foreach ( $results as $row ) {
        $data[ (int) $row['post_id'] ] = (int) $row['meta_value'];
    }

    wp_send_json_success( $data );
}