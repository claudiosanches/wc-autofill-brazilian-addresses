<?php
/**
 * WooCommerce Brazilian Postcodes Integration class
 *
 * @package WC_Brazilian_Postcodes/Classes/Integration
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Brazilian_Postcodes_Integration extends WC_Integration {

	/**
	 * Table name.
	 */
	protected $table = 'brazillian_postcodes';

	/**
	 * Correios Webservice.
	 *
	 * @var string
	 */
	protected $webservice = 'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl';

	/**
	 * Initialize the integration.
	 */
	public function __construct() {
		$this->id                 = 'brazilian-postcodes';
		$this->method_title       = __( 'Brazilian Postcodes', 'wc-brazilian-postcodes' );
		$this->method_description = __( '', 'wc-brazilian-postcodes' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		$this->debug = $this->get_option( 'debug' );

		// Debug.
		if ( 'yes' === $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Actions.
		add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'wc_ajax_brazilian_autocomplete_address', array( $this, 'ajax_autocomplete' ) );
	}

	/**
	 * Settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'debug' => array(
				'title'       => __( 'Debug Log', 'wc-brazilian-postcodes' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'wc-brazilian-postcodes' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Log events such as API requests, you can check this log in %s.', 'wc-brazilian-postcodes' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'System Status &gt; Logs', 'wc-brazilian-postcodes' ) . '</a>' ),
			),
		);
	}

	/**
	 * Get address by postcode.
	 *
	 * @param string $postcode
	 *
	 * @return stdClass
	 */
	protected function get_address( $postcode ) {
		global $wpdb;

		$table    = $wpdb->prefix . $this->table;
		$address  = $wpdb->get_row( $wpdb->prepare( "
			SELECT *
			FROM $table
			WHERE postcode = %s
		", $postcode ) );

		if ( is_wp_error( $address ) || is_null( $address ) ) {
			$address = $this->fetch_address( $postcode );

			if ( ! is_null( $address ) ) {
				$this->save_address( (array) $address );
			}
		} else if ( strtotime( '+3 months', strtotime( $address->last_query ) ) < current_time( 'timestamp' ) ) {
			$address = $this->fetch_address( $postcode );

			if ( ! is_null( $address ) ) {
				$this->update_address( $id, (array) $address );
			}
		}

		return $address;
	}

	/**
	 * Insert an address.
	 *
	 * @param array $address
	 *
	 * @return bool
	 */
	protected function save_address( $address ) {
		global $wpdb;

		$default = array(
			'postcode'     => '',
			'address'      => '',
			'city'         => '',
			'neighborhood' => '',
			'state'        => '',
			'last_query'   => current_time( 'mysql' ),
		);

		$address = wp_parse_args( $address, $default );

		$result = $wpdb->insert(
			$wpdb->prefix . $this->table,
			$address,
			array( '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return false !== $result;
	}

	/**
	 * Delete an address from database.
	 *
	 * @return string $postcode
	 */
	protected function delete_address( $postcode ) {
		global $wpdb;

		$wpdb->delete( $wpdb->prefix . $this->table, array( 'postcode' => $postcode ), array( '%s' ) );
	}

	/**
	 * Update an address.
	 *
	 * @param array $address
	 *
	 * @return bool
	 */
	protected function update_address( $id, $address ) {
		$this->delete_address( $address['postcode'] );

		return $this->save_address( $address );
	}

	/**
	 * Fetch an address from Correios Webservices.
	 *
	 * @param string $postcode
	 * @return stdClass
	 */
	protected function fetch_address( $postcode ) {
		if ( 'yes' == $this->debug ) {
			$this->log->add( $this->id, sprintf( 'Fetching address for "%s" on Correios Webservices...', $postcode ) );
		}

		$address  = null;
		$soap_opt = array(
			'encoding'   => 'UTF-8',
			'trace'      => true,
			'exceptions' => true,
			'cache_wsdl' => false,
		);

		try {
			$soap       = new SoapClient( $this->webservice, $soap_opt );
			$response   = $soap->consultaCEP( array( 'cep' => $postcode ) );
			$data       = $response->return;
			$address    = new stdClass;

			$address->postcode     = $data->cep;
			$address->address      = $data->end;
			$address->city         = $data->cidade;
			$address->neighborhood = $data->bairro;
			$address->state        = $data->uf;
			$address->last_query   = current_time( 'mysql' );
		} catch ( Exception $e ) {
			if ( 'yes' == $this->debug ) {
				$this->log->add( $this->id, sprintf( 'An error occurred while trying to fetch address for "%s": %s', $postcode, $e->getMessage() ) );
			}
		}

		if ( 'yes' == $this->debug && ! is_null( $address ) ) {
			$this->log->add( $this->id, sprintf( 'Address for "%s" found successfully: %s', $postcode, print_r( $address, true ) ) );
		}

		return $address;
	}

	/**
	 * Sanitize postcode.
	 *
	 * @param string $postcode
	 * @return string
	 */
	protected function sanitize_postcode( $postcode ) {
		$postcode = sanitize_text_field( $postcode );

		return preg_replace( '([^0-9])', '', $postcode );
	}

	/**
	 * Frontend scripts.
	 */
	public function frontend_scripts() {
		if ( is_checkout() || is_account_page() ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( $this->id, plugins_url( 'assets/js/autocomplete-address' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_Brazilian_Postcodes::VERSION, true );

			wp_localize_script(
				$this->id,
				'wcBrazilianPostcodesParams',
				array(
					'url' => WC_AJAX::get_endpoint( 'brazilian_autocomplete_address' ),
				)
			);
		}
	}

	/**
	 * Ajax autocomplete endpoint.
	 */
	public function ajax_autocomplete() {
		if ( empty( $_GET['postcode'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Missing postcode paramater.', 'woocommerce' ) ) );
		}

		$postcode = $this->sanitize_postcode( $_GET['postcode'] );
		$address  = $this->get_address( $postcode );

		// Test if found any postcode.
		if ( is_null( $address ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid postcode.', 'woocommerce' ) ) );
		}

		// Unset ID and last_query.
		unset( $address->ID );
		unset( $address->last_query );

		wp_send_json_success( $address );
	}
}
