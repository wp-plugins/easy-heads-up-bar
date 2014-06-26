=== Easy Heads Up Bar ===
Contributors: Greenweb
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7YA9D9G4TE9BA
Tags: heads up bar, heads up, heads up, heads up bar, Callout Bar, top of the page, notification bar, notification, self promotion, floating-bar, beforesite
Requires at least: 3.6
Tested up to: 3.9.9
Stable tag: trunk
License: GPLv2 or later
 
The Easy Heads Up Bar Plugin allows you to quickly add a customizable notification bar to your WordPress website.

== Description ==

This plugin adds an easy to use notification bar to the top of your WordPress website

[youtube https://www.youtube.com/watch?v=DOsTdfnmtmI]

= Key Features =

* Customizable color schemes 
* Create multiple bars, as many as you want.
* If there is more than one bar then the bars will display randomly
* Schedule when your bars show up by setting an start and end date.
* Choose where to display bars, eg: 
 * All pages
 * Only the interior pages
 * Just the home page
 
= Display Date Options =

* The Bar can be set to expire on a specified date
* The Bar can be set to start on a specified date
* The Bar can be set to run between on a specified dates

= New Features =

* New bar management screen
* New bar editor
* No limit on bar height, it will just fit your content
* No limit to the amount of text or links in a bar
* Add images to bar
* Use another plugin's shortcodes in bar
* Choose between the top or the bottom of a page to display your bar
* Allow your users to hide and unhide the Heads Up Bar

== Installation ==

Install the plugin via WordPress's installation system then activate it

=OR=

1. Upload the `easy-heads-up-bar` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

* **Q:** Is the Bar Responsive?
 * **A:** Yes.

* **Q:** Can the bar be closed?
 * **A:** Yes.

* **Q:** Is it Possible to remove the line under bar?
 * **A:** Yes, just set the color of the line to the same color as the bar's background.

* **Q:** Can the bar remain at the top of the screen when I scroll?
 * **A:** Not right now, this may be added later. But there is nothing stopping you from achiving this effect by adding a bit of CSS to your theme. You can target the bar using it's ID *#ehu-bar*

* Feel free to ask any questions you may have at the [Support Forum](http://wordpress.org/support/plugin/easy-heads-up-bar)

== Screenshots ==

1. This an example Easy Heads Up Bar on a page
2. This is the Easy Heads Up Bar manager
3. This is the Easy Heads Up Bar editor
4. Color for the Easy Heads Up Bar in the Editor for the Easy Heads Up Bar
5. Character Count down in the Editor for the Easy Heads Up Bar

== Changelog ==

= 2.0 =

*New features:*

* Bars are now a custom post type
* New Icon
* New bar management screen
* New bar editor
* No limit to the amount of text or links in a bar
* No limit on bar height, it will just fit your content
* Add images to bars
* Use another plugin's shortcodes
* Choose between the top or the bottom of a page to display your bar
* Allow your users to hide and unhide the Heads Up Bar

== Upgrade Notice ==

This is a major update the old interface this is completely replaced with the standard WordPress user interface. 
Exsiting bars will be automaticaly converted to the new system. 
The wp_ehu_bar & wp_ehu_stats tables will be removed from the database as these where added by the last version of the plugin and need to be cleaned up.