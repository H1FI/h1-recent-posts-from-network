=== Hercules Recent Posts from Network ===
Contributors: toddnestor
Tags: recent posts, posts, network, hercules
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Hercules Recent Posts from Network creates a widget that displays the recent posts from the entire network.

== Description ==

First of all, this plugin is for a Wordpress Multisite setup.  The user has the option to display thumbnails or not, and for how many posts they want to display.

This widget works differently than other widgets that display recent posts from an entire network.  A network that has lots of blogs (like several hundred, or even several thousand) can take a long time to query each blog and compare the times for the most recent posts.

If you are using some sort of database sharding it will take even more time.  This plugin works much more simply, as posts are published on the network they are pushed to a network option that contains an array of the most recent posts from the network.

At first this array will be empty, so it might be a bit until the widget has a full list (up to the maximum set by the user in the widget) of recent posts.  Since we are adding posts to the list at the time of publishing this also makes it easy to push the thumbnail to it also, which is why this
widget can show thumbnails when lots of other ones can't.

In a future version we will allow the network admin to limit the people who can use this widget to super admin users.  A future option will also allow network options for blogs to ignore (at a network level), blog owners will be able to opt out of having their blog's posts included, or the network admin will be able to explicitly list blogs that get included.

== Installation ==

Add this plugin by uploading the zip using the "Add Plugin" feature built into Wordpress.  Otherwise manaully unzip the folder
and upload the entire directory to your blog's plugins folder ( /wp-content/plugins/ ).  For this plugin to work effectively it must be network activated.  If it is not network activated then it can only get the recent posts from blogs that have activated the plugin.

Next you should go to the Widgets page and add it to the appropriate sidebar.

== Frequently Asked Questions ==

= Why aren't any posts showing? =

The way this plugin works so efficiently is by pushing posts to the list as they are published.  Only posts that are published after the plugin is activated will be pushed to the list.  It may take some time for the list to fill up depending on how busy your network is.

= Where can I see the widget? =

The widget will show up in the list of available widgets on the Widgets page.  You can drag it over to the sidebar you want the widget to appear in.

== Screenshots ==

1. This is the widget in action with thumbnails enabled
2. This is what users see when they add the widget to a sidebar.

== Changelog ==

= Version 1.1
* The default initial post (the "Hello Wolrd" one) will not be added to the widget when the site is created, but will be if the user edits the content of that post instead of deleting it.