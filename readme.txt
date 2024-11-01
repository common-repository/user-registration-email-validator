=== User Registration Email Validator ===
Contributors: kalprajsolutions
Tags: user registration, is_email, user email checker, user registration email checker, email validator, email checker
Requires at least: 4.0
Tested up to: 6.6
Stable tag: trunk
Requires PHP: 7.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Validate and Verify any email using is_email() and stop spam comments spam logins and registration.

== Description ==

This plugin enables WordPress users to check their client’s email addresses on the registration form or any other form using is_email().
Which is a WordPress function to check valid email addresses. Which helps to stop spam and promotional emails from your inbox. 
It will only allow a valid email address users to sign-up or any Email based activity. 

This plugin is completely based on [emails-checker.net](https://emails-checker.net "emails-checker.net") third party api. Which will send your provided email address to emails checker api in order to validate it. [Emails Checker](https://emails-checker.net "Emails Checker") doesnot store anykind of data on their servers.

Note:
> This Plugin Requires a emails-checker.php api credentials to work. Please make sure to Read [Terms and Conditions](https://emails-checker.net/terms-and-conditions "Terms and Conditions") and [Privicy Policy](https://emails-checker.net/privacy-policy "Privacy Policy") before using this plugin.

= Key Features =

* DNS validation, including MX record lookup
* Disposable email address detection realtime
* Misspelled domain detection
* Email Syntax verification (IETF/RFC standard conformance)
* Mailbox existence checking
* Catch-All Testing
* Grey listing detection
* SMTP connection and availability checking



This plugin needs an Emails-Checker.NET API key to work. You can get it from [here](https://emails-checker.net/#pricing/ "Emails-Checket.net").

Note:
> This Plugin will automatically check your contact form emails syntax *FREE*. Only SMTP Checks will cost credit.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= How many Emails I can check? =

Our Free Plan will give you 100 SMTP Credits for free to check SMTP Status. 
If you have more than 100 Contact Form Submission per month check our affordable pricing at emails-checket.net/#pricing

= Do i need to change any thing? =

No,
Our plugin is very simple to use. Just enter your API Access key are you are done. The plugin will automatically filter all Emails.


== Screenshots ==

1. Emails Checker in Action
2. Emails Checker Settings

== Changelog ==

= 3.3 =
* Updated for newer wordpress version 6.6
* Improved wordpress user registration detection and validation

= 3.2 =
* Updated for newer wordpress version 6.4.2

= 3.1 =
* Started Changelog
* Updated for newer wordpress version 6.1.1
* Added Option to allow specific email types
* Added Option to block emails if out of credits
* Added more email catching abilities

== Upgrade Notice ==
