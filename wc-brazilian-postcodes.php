<?php
/**
 * Plugin Name: WooCommerce Brazilian Postcodes
 * Plugin URI: https://github.com/claudiosmweb/wc-brazilian-postcodes
 * Description: Autocomplete address with postcodes.
 * Author: Claudio Sanches, Matheus Lopes
 * Author URI: https://claudiosmweb.com/
 * Version: 0.0.3
 * License: GPLv2 or later
 * Text Domain: wc-brazilian-postcodes
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Brazilian_Postcodes' ) ) :

/**
 * WC Brazilian Postcodes main class.
 */
class WC_Brazilian_Postcodes {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '0.0.3';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin public actions.
	 */
	private function __construct() {
		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Check for SOAP.
		if ( ! class_exists( 'SoapClient' ) ) {
			add_action( 'admin_notices', array( $this, 'soap_missing_notice' ) );
			return;
		}

		// Checks with WooCommerce is installed.
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		// Include classes.
		$this->includes();

		add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Includes.
	 */
	private function includes() {
		include_once 'includes/class-wc-brazilian-postcodes-integration.php';
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wc-brazilian-postcodes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * SOAPClient missing notice.
	 *
	 * @return string
	 */
	public function soap_missing_notice() {
		include 'includes/admin/views/html-notice-missing-soap-client.php';
	}

	/**
	 * WooCommerce missing notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		include 'includes/admin/views/html-notice-missing-woocommerce.php';
	}

	/**
	 * Add integration.
	 *
	 * @param array $integrations
	 * @return array
	 */
	public function add_integration( $integrations ) {
		$integrations[] = 'WC_Brazilian_Postcodes_Integration';

		return $integrations;
	}

	/**
	 * Action links.
	 *
	 * @param array $links
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array();

		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=brazilian-postcodes' ) ) . '">' . __( 'Settings', 'wc-brazilian-postcodes' ) . '</a>';

		return array_merge( $plugin_links, $links );
	}
}

// Install plugin.
include_once 'includes/class-wc-brazilian-postcodes-install.php';
register_activation_hook( __FILE__, array( 'WC_Brazilian_Postcodes_Install', 'create_database' ) );

// Initialize plugin.
add_action( 'plugins_loaded', array( 'WC_Brazilian_Postcodes', 'get_instance' ) );

endif;
