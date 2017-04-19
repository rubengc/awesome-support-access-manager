<?php
/**
 * Settings
 *
 * @package     Awesome_Support\Access_Manager\Settings
 * @since       1.0.0
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Awesome_Support_Access_Manager_Settings' ) ) {

    class Awesome_Support_Access_Manager_Settings {

        public function __construct() {
            // Add "Access" section in "Advanced" tab
            add_filter( 'wpas_plugin_settings', array( $this, 'settings' ), 100, 1 );

            // Update roles capabilities
            add_action( 'tf_save_admin_wpas', array( $this, 'save' ) );
        }

        /**
         * Add "Access" section in "Advanced" tab
         *
         * @param array $def
         * @return array
         */
        public function settings( $def ) {
            $settings = array(
                array(
                    'name' => __( 'Access', 'awesome-support' ),
                    'type' => 'heading',
                ),
                array(
                    'name'    => __( 'Roles with access to the ticket system', 'awesome-support' ),
                    'id'      => 'roles_access',
                    'type'    => 'multicheck',
                    'desc'    => __( 'By default, only users with role "Support User" can access to the ticket system. From here you can grant same access to any role.', 'awesome-support' ),
                    'options' => $this->get_roles(),
                ),
            );

            if( class_exists( 'Easy_Digital_Downloads' ) ) {
                $settings[] = array(
                    'name'    => __( 'Automatic access to customers', 'awesome-support' ),
                    'id'      => 'customer_automatic_access',
                    'type'    => 'checkbox',
                    'desc'    => __( 'Automatically grant access to customers when purchase a product.', 'awesome-support' ),
                );
            }

            array_splice( $def['advanced']['options'], 3, 0, $settings );

            return $def;
        }

        // Roles as options
        public function get_roles() {
            // WordPress roles
            $roles_as_objects = wp_roles()->roles;

            // Roles as options
            $roles_as_options = array();

            $excluded_roles = array(
                // WordPress roles
                'administrator',
                // Awesome Support roles
                'wpas_manager',
                'wpas_support_manager',
                'wpas_agent',
                'wpas_user',
            );

            $excluded_roles = apply_filters( 'wpas_access_manager_excluded_roles', $excluded_roles );

            foreach( $roles_as_objects as $role => $role_args ) {
                // Only return roles not from awesome support
                if( ! in_array( $role, $excluded_roles ) ) {
                    $roles_as_options[$role] = $role_args['name'];
                }
            }

            $roles_as_options = apply_filters( 'wpas_access_manager_roles', $roles_as_options );

            // Extra check to prevent modify administrator role capabilities
            if( isset( $roles_as_options['administrator'] ) ) {
                unset( $roles_as_options['administrator'] );
            }

            return $roles_as_options;
        }

        /**
         * Update roles capabilities
         *
         * @param TitanFramework $container
         */
        public function save( $container ) {
            // Check if we are in the right page and tab
            if( isset( $_GET['page'] ) && $_GET['page'] == 'wpas-settings'
                && isset( $_GET['tab'] ) && $_GET['tab'] == 'advanced' ) {
                // All roles
                $roles = $this->get_roles();

                // Checked roles
                $granted_roles = $_POST['wpas_roles_access'];

                // Capabilities
                $client_capabilities = awesome_support_access_manager()->get_client_capabilities();

                foreach( $roles as $role => $name ) {
                    $role_object = get_role( $role );

                    if( $role_object ) {
                        $grant = in_array( $role, $granted_roles );

                        foreach( $client_capabilities as $client_capability ) {
                            $role_object->add_cap( $client_capability, $grant );
                        }
                    }
                }
            }
        }

    }

}