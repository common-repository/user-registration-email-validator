<?php
/*

Plugin Name: User Registration Email Validator
Plugin URI: https://wordpress.org/plugins/user-registration-email-validator/
Description: Validate and Verify any email using is_email() and stop spam comments spam logins and registration.
Author: Kalpraj Solutions 
Author URI: https://emails-checker.net
Version: 3.3
Text Domain: user-registration-email-validator
 
User Registration Email Validator
Copyright (C) 2020, Kalpraj Solutions, support@kalprajsolutions.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

# NOPE #
defined('ABSPATH') or die('Nope nope nope...');
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

/**
 * Start Class UREV_EmailValidator
 *
 **/
if (!class_exists('UREV_EmailValidator')) {

	class UREV_EmailValidator
	{

		public function __construct()
		{
			register_activation_hook(__FILE__, array($this, 'activate'));
			register_deactivation_hook(__FILE__, array($this, 'uninstall'));

			add_action('admin_menu', array($this, 'menu'));
			add_action('admin_init', array($this, 'admin_notices'));

			add_filter('plugin_action_links', array($this, 'addActionLinks'), 10, 2);

			//Check if Access Key is valid then add filters
			if ($this->getCurrentApiStatus()) {
				// Other plugins that used is_email
				if (is_admin() == false) {

					//do woocommerce fields validation
					add_action('woocommerce_after_checkout_validation', array($this, 'woocommerce_validate'), 10, 2);


					//check for errors.
					add_filter('registration_errors', array($this, 'availableError'));
					add_filter('user_profile_update_errors', array($this, 'availableError'));
					add_filter('login_errors', array($this, 'availableError'));

					// Wordpress Registration Form
					add_action('register_post', array($this, 'isEmail'), 10, 3);
					// another method
					if (isset($_POST['user_email']) && isset($_POST['wp-submit']) && $_POST['wp-submit'] == "Register") {
						add_filter('is_email', array($this, 'isEmail'), 1, 3);
					}
				}
			}
		}



		public function activate()
		{
			//set default recommended allowed email types
			update_option('urev_allowed_email_types', ['deliverable', 'unknown']);
		}

		public function uninstall()
		{
			delete_option('urev_access_key_status');
			delete_option('urev_access_key_status_color');
			delete_option('urev_access_key');
			delete_option('urev_allowed_email_types');
		}

		//check if access key is available or not and show warning		
		public function admin_notices()
		{
			if (empty(get_option('urev_access_key'))) {

				function urev_no_accesskey_found()
				{
?>
					<div class="notice notice-warning is-dismissible" style="margin-left: 0px;top: 10px;">
						<p><?php _e("To use <a href='/wp-admin/options-general.php?page=user-registration-email-validator'> User Registration Email Validator</a>, enter your Emails Checker API key in the plugin's settings.", 'email-checker-for-cf7'); ?></p>
					</div>
<?php
				}
				add_action('admin_notices', 'urev_no_accesskey_found');
			}
		}

		public function menu()
		{
			add_submenu_page(
				'options-general.php',
				'User Email Validator',
				'User Email Validator',
				'manage_options',
				'user-registration-email-validator',
				array($this, 'settings') // function
			);
		}

		public function kprj_email_check($email)
		{
			if (is_admin()) {
				// If user have logged as admin, meaning most probably they are editing the settings, let it pass if is_email been called.
				return true;
			}

			if (($_SERVER['REQUEST_URI'] == '/wp-login.php') | ($_SERVER['REQUEST_URI'] == '/wp-login.php?loggedout=true') | ($_SERVER['REQUEST_URI'] == '/wp-cron.php')) {
				// if wp-login.php is been called for login to dashboard, skip the check.
				return true;
			}

			// Check the formatting is correct
			if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				return false;
			}


			$urev_access_key = get_option('urev_access_key');
			$response        = wp_remote_get("https://api.emails-checker.net/check?access_key={$urev_access_key}&email={$email}");
			$response        = wp_remote_retrieve_body($response);
			$body            = @json_decode($response);

			if ($body->success == true) {
				if (in_array(strtolower(trim($body->response->result)), get_option('urev_allowed_email_types'))) {
					return true;
				}

				return false;
			} else {

				//some sort of error came so assume email as valid 
				if (get_option('urev_block_on_error')) {
					return false;
				} else {
					return true;
				}
			}
		}

		//check is_email function
		public function isEmail($is_valid, $email)
		{
			if (!$is_valid) {
				return FALSE;
			}

			//check email function
			return $this->kprj_email_check($email);
		}

		//check for errors
		public function availableError($errors)
		{
			return $errors;
		}

		/**
		 * Validation function to check if registration fields email address is valid or not.
		 *
		 * @param $errors
		 * @param $sanitized_user_login
		 * @param $email
		 * @return void
		 */
		public function registration_validate($errors, $sanitized_user_login, $email)
		{
			if (email_exists($email)) {
				return $errors;
			}

			// do the email validation
			$validation_result = $this->kprj_email_check($email);
			if ($validation_result == false) {
				$errors->add('invalid_email', __('This email address is invalid or not allowed.', 'urev-email-validator'));
				return $errors;
			}
			return $errors;


			// If it is not an email by WP or because we have hooked in already.
			// if (!is_email($email) || email_exists($email)) {
			// 	return $errors;
			// }

			// $validation_result = $this->kprj_email_check($email);

			// if ($validation_result == false) {
			// 	$errors->add('invalid_email', __('<strong>ERROR</strong>: The email address is not correct.', 'urev-email-validator'));
			// }
			// return $errors;
		}

		/**
		 * Woocoommerce Billing Email Fields
		 *
		 * @param $fields
		 * @param $errors
		 * @return void
		 */
		public function woocommerce_validate($fields, $errors)
		{
			if (!empty($fields['billing_email'])) {
				$validation_result = $this->kprj_email_check(sanitize_email($fields['billing_email']));
				if ($validation_result == false) {
					$errors->add('validation', __('The billing email address is invalid or not allowed - please check.', 'urev-email-validator'));
				}
			}
			if (!empty($fields['shipping_email'])) {
				$validation_result = $this->kprj_email_check(sanitize_email($fields['shipping_email']));
				if ($validation_result == false) {
					$errors->add('validation', __('The shipping email address is invalid or not allowed - please check.', 'urev-email-validator'));
				}
			}
			return $errors;
		}

		//Display HTML Content
		public function settings()
		{
			//do permissions check
			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			//define vars
			$available_credits = null;
			$kprjOutput        = "";

			//update data
			if (!empty($_POST['update'])) {
				//wp_verify_nonce implemented 
				if (wp_verify_nonce($_POST["urev_name_nonce"], "urev_action_nonce")) {

					if (!isset($_POST['urev_allowed_email_types'])) {
						$kprjOutput = '<div class="error" role="alert"> Please select atleast one allowed email type.</div>';
					} else {
						$urev_access_key = isset($_POST['urev_access_key']) ? sanitize_text_field($_POST['urev_access_key']) : '';
						update_option('urev_access_key', $urev_access_key);
						update_option('urev_block_on_error', isset($_POST['urev_block_on_error']) ? $_POST['urev_block_on_error'] : false);
						update_option('urev_allowed_email_types', $_POST['urev_allowed_email_types']);

						$updated = 1;
					}
				}
			}

			$urev_access_key = get_option('urev_access_key');

			//if access key is empty remove color and status name
			if (is_null($urev_access_key) || strlen($urev_access_key) < 1) {
				delete_option('urev_access_key_status_color');
				delete_option('urev_access_key_status');
			}

			if (!is_null($urev_access_key) && strlen($urev_access_key) > 2) {

				$response = wp_remote_get("https://api.emails-checker.net/credits?access_key={$urev_access_key}");
				$body     = wp_remote_retrieve_body($response);
				$body     = json_decode($body);

				if ($body->success == true) {

					//Update Status and Color
					update_option('urev_access_key_status', "CONNECTED");
					update_option('urev_access_key_status_color', "GREEN");

					//Retrive Required Details
					$available_credits = $body->response->credits;
				} else {
					//Update Status and Color
					$kprj_api_error = $body->response->error;
					update_option('urev_access_key_status', $kprj_api_error);
					update_option('urev_access_key_status_color', "RED");
					$kprjOutput = '<div class="error" role="alert">' . $kprj_api_error . '</div>';
				}
			}

			//all the data will be sent to view.php file
			require_once(dirname(__FILE__) . '/view.php');
			// include_once('view.php');
		}

		//plugin setting page url on plugin page
		function addActionLinks($actions, $plugin_file)
		{
			static $plugin;

			if (!isset($plugin)) {
				$plugin = plugin_basename(__FILE__);
			}

			if ($plugin == $plugin_file) {
				$settings = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=user-registration-email-validator')) . '">' . __('Settings', 'user-registration-email-validator') . '</a>';

				$actions = array_merge(array(
					'settings' => $settings,
				), $actions);
			}

			return $actions;
		}

		//Get current api status
		public function getCurrentApiStatus()
		{
			$urev_access_key = get_option('urev_access_key');
			if (!is_bool($urev_access_key) && strlen($urev_access_key) > 5) {
				$response = wp_remote_get("https://api.emails-checker.net/credits?access_key={$urev_access_key}");
				$body     = wp_remote_retrieve_body($response);
				$body     = json_decode($body);

				if ($body->success == true) {
					//Update Status and Color					
					update_option('urev_access_key_status', "CONNECTED");
					update_option('urev_access_key_status_color', "GREEN");
					return true;
					//dd($res->response->credits);
				} else {
					//Update Status and Color
					update_option('urev_access_key_status', $body->response->error);
					update_option('urev_access_key_status_color', "RED");
					return false;
				}
			} else {
				return false;
			}
		}
	}

	/**
	 *  UREV_EmailValidator Class Finished
	 *  Start the Class by Initializing it
	 **/
	$UREV_EmailValidator = new UREV_EmailValidator();
}
