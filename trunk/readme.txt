=== Best Rating & Pageviews ===
Contributors: icopydoc
Donate link: https://sobe.ru/na/best_rating_pageviews
Tags: rating, stars, pageviews, widget, popular
Requires at least: 4.4.2
Tested up to: 5.6.1
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add Star rating, pageviews and adds a tool for analyzing the effectiveness of content with the supplied shortcode.

== Description ==

Add Star rating, pageviews and adds a tool for analyzing the effectiveness of content. Also this plugin adds a widget which shows popular posts and pages based on the rating and pageviews.

= Displays the number of page views =

[pageviews] - Displays the number of page views.

Notice:

*   This shortcode can be used in the body of the article.
*   This shortcode can be used in the loop body of templates.
*   This shortcode cannot be used outside the loop of the template.

Example:

`[pageviews]`
`do_shortcode('[pageviews]');`

= Display the rating starss =

[pageratings] - display the rating stars.

Notice:

*   This shortcode can be used in the body of the article.
*   This shortcode can be used in the loop body of templates.
*   This shortcode cannot be used outside the loop of the template.

Example:

`[pageratings]`
`do_shortcode('[pageratings]');`

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the entire `best-rating-pageviews` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Add the shortcode (`[pageviews]` or `[pageratings]`) to the page topic

== Frequently Asked Questions ==

= Bots are not counted? =

Yes. When counting the pageviews plugin does not take into account bots.

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 2.0.0 =
* Added the ability to delete all statistics

= 1.2.0 =
* Some changes

= 1.1.3 =
* Fix bugs
* Added itemReviewed

= 1.1.2 =
* Fix bugs

= 1.1.1 =
* Fix bugs

= 1.1.0 =
* Fix bugs
* Improved plugin algorithm
* Added settings page

= 1.0.1 =
* Fixed counting bots.
* Fixed sorting mechanism on dashboard.

= 1.0.0 =
* First relise.

== Upgrade Notice ==

= 2.0.0 =
* Added the ability to delete all statistics