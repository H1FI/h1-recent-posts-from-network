=== Herc Recent Posts from Network ===
Contributors: toddnestor
Tags: spam, recaptcha, captcha, comments
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Hercules Recaptcha adds a Recaptcha to the comment form for non-logged in users.  It uses the latest Recaptcha API.

== Description ==

Hercules Recaptcha uses the latest Google Recaptcha API to more accurately determine if users are bots or not.
If the user is not logged in it will display a Recaptcha for the user to fill out in the comment form.  If the user
disables javascript and is not logged in then comments will fail to submit.

In a future version options for which forms to use this on will be provided, currently it only works with the comment forms.

Also in a future version support for multisite will be added with default keys that are used for sites that don't have
mapped domains, and options for the users to add their own Recaptcha keys if they are using a mapped domain.

== Installation ==

Add this plugin by uploading the zip using the "Add Plugin" feature built into Wordpress.  Otherwise manaully unzip the folder
and upload the entire directory to your blog's plugins folder ( /wp-content/plugins/ ).  Next you have to activate the plugin.

Lastly, to make it work you need to go to the Hercules Recaptcha settings page (a submenu of "Settings") and add your
site key and and secret key.  If you don't have these yet you need to go to https://www.google.com/recaptcha/ to register your site
and get keys.

== Frequently Asked Questions ==

= Why isn't my Recaptcha rendering? =

First of all make sure you are not logged into Wordpress and that you are viewing a post that has comments enabled.
If the Recaptcha isn't displaying then you may have not set your Recaptcha keys yet.  If it is displaying an error
message then you are using invalid keys (ones not associated with the domain that your blog is on) or there is some
sort of error in the keys (like an extra space in them).

= Where do I get Recaptcha keys? =

If you don't have these yet you need to go to https://www.google.com/recaptcha/ to register your site
and get keys.

== Screenshots ==

1. This is how the Recaptcha looks in a form on a real Wordpress theme.
2. This is what users see when they press the "I'm not a robot" checkbox.
3. This is the settings page where you set the Recaptcha keys.

== Changelog ==

