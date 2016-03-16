<?php
/**
 * Plugin Name: WC Brazilian Postcodes
 * Plugin URI: https://github.com/claudiosmweb/wc-brazilian-postcodes
 * Description: Autocomplete address by postcodes.
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
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WooCommerce' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'scritps' ) );

			$this->includes();
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
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

	}

	/**
	 * WC Brazilian Postcodes scripts.
	 */
	public function scritps() {
		wp_enqueue_script( 'wc-brazilian-postcodes-postmon-api-integration', esc_url( plugins_url( 'assets/js/postmon-api-integration.js', __FILE__ ) ), array( 'jquery' ), WC_Brazilian_Postcodes::VERSION, true );
		wp_enqueue_script( 'wc-brazilian-postcodes-integration', esc_url( plugins_url( 'assets/js/checkout-integration.js', __FILE__ ) ), array( 'jquery' ), WC_Brazilian_Postcodes::VERSION, true );
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'wc-brazilian-postcodes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		include 'includes/admin/views/html-notice-missing-woocommerce.php';
	}
}

add_action( 'plugins_loaded', array( 'WC_Brazilian_Postcodes', 'get_instance' ) );

endif;
