<?php
/**
 * Functions
 *
 * @package     Awesome_Support\Access_Manager\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Awesome_Support_Access_Manager_Functions' ) ) {

    class Awesome_Support_Access_Manager_Functions {

        public function __construct() {
            // Automatically grant access to customer after complete a purchase (if you checked this option)
            add_action( 'edd_complete_purchase', array( $this, 'access_on_purchase' ) );
        }

        /**
         * Automatically grant access to customer after complete a purchase (if you checked this option)
         *
         * @param int $payment_id
         */
        public function access_on_purchase( $payment_id ) {
            if ( false === wpas_get_option( 'customer_automatic_access', false ) ) {
                return;
            }

            $payment = new EDD_Payment( $payment_id );
            $user = new WP_User( $payment->user_id );

            if( $user ) {
                foreach( awesome_support_access_manager()->get_client_capabilities() as $client_capability ) {
                    $user->add_cap( $client_capability, true );
                }
            }
        }

    }

}