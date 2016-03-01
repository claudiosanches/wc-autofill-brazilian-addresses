<?php
/**
 * Plugin Name: WooCommerce Complete Address by CEP
 * Plugin URI: https://github.com/claudiosmweb/woocommerce-complete-address-by-cep
 * Description: Completar endereço a partir do CEP
 * Author: Claudio Sanches, Matheus Lopes
 * Author URI: http://claudiosmweb.com/, http://matheuscl.com/
 * Version: 0.0.1
 * License: GPLv2 or later
 * Text Domain: woocommerce-complete-address-by-cep
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Complete_Address_By_Cep' ) ) :

/**
 * WooCommerce Complete Address by CEP main class.
 */
class WC_Complete_Address_By_Cep {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '0.0.1';

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
	 * Complete Address by CEP scripts.
	 *
	 * @return void
	 */
	public function scritps() {
		wp_enqueue_script( 'woocommerce-complete-address-by-cep-postmon-api-integration', esc_url( plugins_url( 'assets/js/postmon-api-integration.js', __FILE__ ) ), array( 'jquery' ), WC_Complete_Address_By_Cep::VERSION, true );
		wp_enqueue_script( 'woocommerce-complete-address-by-cep-checkout-integration', esc_url( plugins_url( 'assets/js/checkout-integration.js', __FILE__ ) ), array( 'jquery' ), WC_Complete_Address_By_Cep::VERSION, true );
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
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-complete-address-by-cep' );

		load_textdomain( 'woocommerce-complete-address-by-cep', trailingslashit( WP_LANG_DIR ) . 'woocommerce-complete-address-by-cep/woocommerce-complete-address-by-cep-' . $locale . '.mo' );
		load_plugin_textdomain( 'woocommerce-complete-address-by-cep', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return  string
	 */
	public function woocommerce_missing_notice() {
		echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Complete Address By CEP depends of %s to work!', 'woocommerce-complete-address-by-cep' ), '<a href="http://wordpress.org/plugins/woocommerce/">' . __( 'WooCommerce', 'woocommerce-complete-address-by-cep' ) . '</a>' ) . '</p></div>';
	}
}

add_action( 'plugins_loaded', array( 'WC_Complete_Address_By_Cep', 'get_instance' ) );

endif;
