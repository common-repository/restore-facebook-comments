=== Plugin Name ===
Contributors: nielsvanrenselaar
Donate link: 
Tags: facebook, comments, import
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: 0.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin lets you restore the Facebook comments on posts back to native WordPress comments. It uses the Facebook Open Graph information to do so.  Support for custom post types will be added soon and it still needs some polishing, but it works.

== Installation ==

1. Upload the folder `restore-fb-comments` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The import options are available trough Settings -> Restore FB Comments

== Changelog ==

= 0.9 =
* Tested on WordPress 3.8

= 0.8 =
* Added the plugin to the repository

== Screenshots ==

1. The main screen, which enables you to set a amount of posts to handle per batch.

== Upgrade Notice ==

= 0.8 =
* First version

== Frequently Asked Questions ==

= Why doesn't the plugin restore the comments e-mail and IP-Address =

The Facebook API does not return the comment users emailaddress and IP-Address. The plugin replaces this with facebook@facebook.com and 127.0.0.1. We can not change this behaviour at this time.