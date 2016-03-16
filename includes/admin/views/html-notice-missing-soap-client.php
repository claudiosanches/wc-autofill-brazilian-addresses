<?php
/**
 * Missing SOAPClient notice.
 *
 * @package WC_Brazilian_Postcodes/Admin/Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="error">
	<p><strong><?php _e( 'WooCommerce Brazilian Postcodes', 'wc-brazilian-postcodes' ); ?></strong> <?php printf( __( 'needs %s to works!', 'wc-brazilian-postcodes' ), '<a href="https://secure.php.net/manual/book.soap.php" target="_blank">' . __( 'SOAP module', 'woocommerce' ) . '</a>' ); ?></p>
</div>

<?php
