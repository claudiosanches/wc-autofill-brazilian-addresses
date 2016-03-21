<?php
/**
 * WooCommerce Autofill Brazilian Addresses Install class
 *
 * @package WC_Autofill_Brazilian_Addresses/Classes/Installer
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Autofill_Brazilian_Addresses_Install {

	/**
	 * Create database.
	 */
	public static function create_database() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'brazillian_postcodes';

		$sql = "CREATE TABLE $table_name (
			ID bigint(20) NOT NULL auto_increment,
			postcode char(8) NOT NULL,
			address longtext NULL,
			city longtext NULL,
			neighborhood longtext NULL,
			state char(2) NULL,
			last_query datetime NULL,
			PRIMARY KEY  (ID),
			KEY postcode (postcode)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );
	}
}
