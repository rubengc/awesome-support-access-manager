<?php
/**
 * Users
 *
 * @package     Awesome_Support\Access_Manager\Users
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Awesome_Support_Access_Manager_Users' ) ) {

    class Awesome_Support_Access_Manager_Users {

        public function __construct() {
            // User profile fields
            add_action( 'show_user_profile', array( $this, 'user_profile' ) );
            add_action( 'edit_user_profile', array( $this, 'user_profile' ) );

            // Update user capabilities
            add_action( 'personal_options_update', array( $this, 'save_user' ) );
            add_action( 'edit_user_profile_update', array( $this, 'save_user' ) );
        }

        /**
         * User profile fields
         *
         * @param WP_User $user
         */
        public function user_profile( $user ) {
            if ( ! current_user_can( 'administrator' ) ) {
                return;
            }

            ?>
            <h3><?php _e('Awesome Support - Access Manager', 'awesome-support'); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label><?php _e('Awesome Support Access', 'awesome-support'); ?></label></th>
                    <td>
                        <?php
                        $has_access = true;

                        foreach( awesome_support_access_manager()->get_client_capabilities() as $client_capability ) {
                            if( ! user_can( $user->ID, $client_capability ) ) {
                                $has_access = false;
                            }
                        }
                        ?>
                        <label for="wpas_allow_access"><input type="checkbox" id="wpas_allow_access" name="wpas_allow_access" value="yes" <?php checked( $has_access, true, true ); ?>> <?php _e( 'Allow this user access to the ticket system', 'awesome-support' ); ?></label>
                    </td>
                </tr>
            </table>
            <?php
        }

        /**
         * Update user capabilities
         *
         * @param int $user_id
         * @return bool
         */
        public function save_user( $user_id ) {
            if ( ! current_user_can( 'edit_user' ) ) {
                return false;
            }

            if ( ! current_user_can( 'administrator' ) ) {
                return false;
            }

            $user = new WP_User( $user_id );

            if( $user ) {
                // Grant/Revoke capabilities if checkbox is checked/unchecked
                $grant = (bool) isset( $_POST['wpas_allow_access'] );

                foreach( awesome_support_access_manager()->get_client_capabilities() as $client_capability ) {
                    $user->add_cap( $client_capability, $grant );
                }
            }

            return true;
        }

    }

}