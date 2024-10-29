<?php
/**
 * Plugin Name: Affiliates Manager MailPoet Integration
 * Plugin URI: https://wpaffiliatemanager.com/sign-affiliates-to-mailpoet-list/
 * Description: This Addon allows you to sign up your affiliates to MailPoet newsletter list
 * Version: 1.0.1
 * Author: wp.insider
 * Author URI: https://wpaffiliatemanager.com
 * Requires at least: 5.2
 */

if (!defined('ABSPATH')){
    exit;
}

if (!class_exists('AFFMGR_MAILPOET_ADDON')) {

    class AFFMGR_MAILPOET_ADDON {

        var $version = '1.0.1';
        var $db_version = '1.0';
        var $plugin_url;
        var $plugin_path;

        function __construct() {
            $this->define_constants();
            $this->includes();
            $this->loader_operations();
            //Handle any db install and upgrade task
            add_action('init', array(&$this, 'plugin_init'), 0);
            add_action('wpam_after_main_admin_menu', array(&$this, 'mailpoet_do_admin_menu'));
            add_action('wpam_front_end_registration_form_submitted', array(&$this, 'do_mailpoet_signup'), 10, 2);
        }

        function define_constants() {
            define('AFFMGR_MAILPOET_ADDON_VERSION', $this->version);
            define('AFFMGR_MAILPOET_ADDON_URL', $this->plugin_url());
            define('AFFMGR_MAILPOET_ADDON_PATH', $this->plugin_path());
        }

        function includes() {
            include_once('affmgr-mailpoet-settings.php');
        }

        function loader_operations() {
            //add_action('plugins_loaded', array(&$this, 'plugins_loaded_handler')); //plugins loaded hook		
        }

        function plugin_init() {//Gets run with WP Init is fired
        }

        function mailpoet_do_admin_menu($menu_parent_slug) {
            add_submenu_page($menu_parent_slug, __("MailPoet", 'wpam'), __("MailPoet", 'wpam'), 'manage_options', 'wpam-mailpoet', 'wpam_mailpoet_admin_interface');
        }

        function do_mailpoet_signup($model, $request) {

            $first_name = strip_tags($request['_firstName']);
            $last_name = strip_tags($request['_lastName']);
            $email = strip_tags($request['_email']);
            $mailpoet_list_id = get_option('wpam_mailpoet_list_id'); //List ID where an affiliate will be signed up to. 
            WPAM_Logger::log_debug("Mailpoet newsletter addon. After registration hook. Debug data: " . $mailpoet_list_id . "|" . $email . "|" . $first_name . "|" . $last_name);
            if (class_exists(\MailPoet\API\API::class)) {
                WPAM_Logger::log_debug("Mailpoet newsletter addon. Initializing the Mailpoet API class");
                // Get MailPoet API instance
                $mailpoet_api = \MailPoet\API\API::MP('v1');
                $existing_subscriber = array();
                $subscriber = array(
                    'email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name
                );
                $list_ids = array($mailpoet_list_id);
                // Check if subscriber exists. If subscriber doesn't exist an exception is thrown
                try {                    
                    $existing_subscriber = $mailpoet_api->getSubscriber($subscriber['email']);
                    WPAM_Logger::log_debug("Mailpoet newsletter addon. A subscriber with this email exists: ".$subscriber['email']);
                } catch (\Exception $e) {
                    WPAM_Logger::log_debug("Mailpoet newsletter addon. A subscriber with this email does not exist: ".$e->getMessage());
                }

                try {
                  if (!$existing_subscriber) {
                    // Subscriber doesn't exist let's create one
                    WPAM_Logger::log_debug("Mailpoet newsletter addon. Adding a new subscriber to the list");  
                    $mailpoet_api->addSubscriber($subscriber, $list_ids);
                  } else {
                    // In case subscriber exists just add him to new lists
                    WPAM_Logger::log_debug("Mailpoet newsletter addon. Adding existing subscriber to the list");  
                    $mailpoet_api->subscribeToLists($subscriber['email'], $list_ids);
                  }
                  WPAM_Logger::log_debug("MailPoet signup complete!");
                } catch (\Exception $e) {
                  WPAM_Logger::log_debug("Mailpoet newsletter addon. Error: ".$e->getMessage());
                }
            }          
        }

        function plugin_url() {
            if ($this->plugin_url)
                return $this->plugin_url;
            return $this->plugin_url = plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__));
        }

        function plugin_path() {
            if ($this->plugin_path)
                return $this->plugin_path;
            return $this->plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
        }

    }

    //End of plugin class
}//End of class not exists check

$GLOBALS['AFFMGR_MAILPOET_ADDON'] = new AFFMGR_MAILPOET_ADDON();
