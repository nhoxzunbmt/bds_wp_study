<?php


global $wpdb;

use Inpsyde\Wonolog\Channels;
use Monolog\Logger;

$wpdb->get_row('SELECT 1 FROM mb_users');

$wp_error = new \WP_Error( $wpdb->last_error, 'wpdb_error', [ 'query' => $wpdb->last_query ] );
do_action( 'wonolog.log', $wp_error, Logger::WARNING, Channels::DEBUG );

echo '123';
