<?php
/**
 * WooCommerce Brazilian Postcodes Uninstall
 *
 * @package WC_Brazilian_Postcodes/Uninstaller
 * @version 1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}brazillian_postcodes" );
