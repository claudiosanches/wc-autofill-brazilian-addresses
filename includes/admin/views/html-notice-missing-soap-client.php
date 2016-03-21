<?php
/**
 * Missing SOAPClient notice.
 *
 * @package WC_Autofill_Brazilian_Addresses/Admin/Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error">
	<p><strong><?php _e( 'WooCommerce Autofill Brazilian Addresses', 'wc-autofill-brazilian-addresses' ); ?></strong> <?php printf( __( 'needs %s to works!', 'wc-autofill-brazilian-addresses' ), '<a href="https://secure.php.net/manual/book.soap.php" target="_blank">' . __( 'SOAP module', 'woocommerce' ) . '</a>' ); ?></p>
</div>

<?php
