<?php
/**
 * Plugin Name:     Awesome Support - Access Manager
 * Plugin URI:      https://wordpress.org/plugins/awesome-support-access-manager
 * Description:     Take the access control of Awesome Support.
 * Version:         1.0.0
 * Author:          Tsunoa
 * Author URI:      https://tsunoa.com
 * Text Domain:     awesome-support-access-manager
 *
 * @package         Awesome_Support\Access_Manager
 * @author          Tsunoa
 * @copyright       Copyright (c) Tsunoa
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'Awesome_Support_Access_Manager' ) ) {

    /**
     * Main Awesome_Support_Access_Manager class
     *
     * @since       1.0.0
     */
    class Awesome_Support_Access_Manager {

        /**
         * @var         Awesome_Support_Access_Manager $instance The one true Awesome_Support_Access_Manager
         * @since       1.0.0
         */
        private static $instance;

        /**
         * @var         Awesome_Support_Access_Manager_Functions $functions
         * @since       1.0.0
         */
        protected $functions;

        /**
         * @var         Awesome_Support_Access_Manager_Settings $settings
         * @since       1.0.0
         */
        protected $settings;

        /**
         * @var         Awesome_Support_Access_Manager_Users $users
         * @since       1.0.0
         */
        protected $users;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true Awesome_Support_Access_Manager
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new Awesome_Support_Access_Manager();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'AWESOME_SUPPORT_ACCESS_MANAGER_VER', '1.0.0' );

            // Plugin path
            define( 'AWESOME_SUPPORT_ACCESS_MANAGER_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'AWESOME_SUPPORT_ACCESS_MANAGER_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            // Include scripts
            require_once AWESOME_SUPPORT_ACCESS_MANAGER_DIR . 'includes/functions.php';
            require_once AWESOME_SUPPORT_ACCESS_MANAGER_DIR . 'includes/settings.php';
            require_once AWESOME_SUPPORT_ACCESS_MANAGER_DIR . 'includes/users.php';

            $this->functions = new Awesome_Support_Access_Manager_Functions();
            $this->settings = new Awesome_Support_Access_Manager_Settings();
            $this->users = new Awesome_Support_Access_Manager_Users();
        }

        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {

        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = AWESOME_SUPPORT_ACCESS_MANAGER_DIR . '/languages/';
            $lang_dir = apply_filters( 'awesome_support_access_manager_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'awesome-support-access-manager' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'awesome-support-access-manager', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/awesome-support-access-manager/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/awesome-support-access-manager/ folder
                load_textdomain( 'awesome-support-access-manager', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/awesome-support-access-manager/languages/ folder
                load_textdomain( 'awesome-support-access-manager', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'awesome-support-access-manager', false, $lang_dir );
            }
        }

        /**
         * Return filtered client capabilities
         *
         * @return array
         */
        public function get_client_capabilities() {
            return apply_filters( 'wpas_user_capabilities_client', array(
                'view_ticket',
                'create_ticket',
                'close_ticket',
                'reply_ticket',
                'attach_files'
            ) );
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true Awesome_Support_Access_Manager
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \Awesome_Support_Access_Manager The one true Awesome_Support_Access_Manager
 */
function awesome_support_access_manager() {
    return Awesome_Support_Access_Manager::instance();
}
add_action( 'plugins_loaded', 'awesome_support_access_manager' );