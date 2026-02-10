<?php

class WCOC_DB {

    public static function create_table() {
        global $wpdb;
        $table = $wpdb->prefix . 'wc_order_chat';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            order_id BIGINT UNSIGNED NOT NULL,
            sender VARCHAR(20) NOT NULL,
            message TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function insert_message( $order_id, $sender, $message ) {
        global $wpdb;
        return $wpdb->insert(
            $wpdb->prefix . 'wc_order_chat',
            [
                'order_id' => $order_id,
                'sender'   => $sender,
                'message'  => $message
            ],
            ['%d','%s','%s']
        );
    }

    public static function get_messages( $order_id ) {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}wc_order_chat WHERE order_id = %d ORDER BY created_at ASC",
                $order_id
            )
        );
    }
}