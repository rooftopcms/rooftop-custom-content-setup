=== Rooftop Content Setup ===
Contributors: rooftopcms
Tags: rooftop, api, headless, content
Requires at least: 4.7
Tested up to: 4.8.1
Stable tag: 4.8
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Custom content types, taxonomies, and page templates in Rooftop

== Description ==

This plugin allows users to add content types, taxonomies (related to a post type or stand-alone), and page templates (without the need for a Wordpress theme)


== Frequently Asked Questions ==

= Can this be used without Rooftop CMS? =

Yes, it's a Wordpress plugin you're welcome to use outside the context of Rooftop CMS. We haven't tested it, though.


== Changelog ==

= 1.2.2 =
* Tweak how we register custom post types

= 1.2.1 =
* Hook into the new theme_$type_templates filter to add our page templates to WP Admin
* Tweak readme for packaging

= 1.2.0 =
* Don't initialise all custom posts with a custom-fields attribute
* Use get_sites instead of deprecated wp_get_sites


== What's Rooftop CMS? ==

Rooftop CMS is a hosted, API-first WordPress CMS for developers and content creators. Use WordPress as your content management system, and build your website or application in the language best suited to the job.

https://www.rooftopcms.com
