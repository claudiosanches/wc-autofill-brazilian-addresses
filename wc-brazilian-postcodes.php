<?php
/**
 * Plugin Name: WC Brazilian Postcodes
 * Plugin URI: https://github.com/claudiosmweb/wc-brazilian-postcodes
 * Description: Completar endereço a partir do CEP
 * Author: Claudio Sanches
 * Author URI: http://claudiosmweb.com/
 * Version: 0.0.3
 * License: GPLv2 or later
 * Text Domain: wc-brazilian-postcodes
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
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
		// echo esc_url( plugins_url( 'checkout-integration.js', __FILE__ ) );
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WooCommerce' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'scritps' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * WC Brazilian Postcodes scripts.
	 *
	 * @return void
	 */
	public function scritps() {
		wp_enqueue_script( 'wc-brazilian-postcodes-postmon-api-integration', esc_url( plugins_url( 'assets/js/postmon-api-integration.js', __FILE__ ) ), array( 'jquery' ), WC_Brazilian_Postcodes::VERSION, true );
		wp_enqueue_script( 'wc-brazilian-postcodes-integration', esc_url( plugins_url( 'assets/js/checkout-integration.js', __FILE__ ) ), array( 'jquery' ), WC_Brazilian_Postcodes::VERSION, true );
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
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wc-brazilian-postcodes' );

		load_textdomain( 'wc-brazilian-postcodes', trailingslashit( WP_LANG_DIR ) . 'wc-brazilian-postcodes/wc-brazilian-postcodes-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc-brazilian-postcodes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return  string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WC Brazilian Postcodes depends of %s to work!', 'wc-brazilian-postcodes' ), '<a href="http://wordpress.org/plugins/woocommerce/">' . __( 'WooCommerce', 'wc-brazilian-postcodes' ) . '</a>' ) . '</p></div>';
	}
}

add_action( 'plugins_loaded', array( 'WC_Brazilian_Postcodes', 'get_instance' ) );

endif;
