=== Plugin Name ===
Contributors: benbalter
Donate link: http://ben.balter.com/
Tags: resume
Requires at least: 3.0
Tested up to: 3.0
Stable tag: trunk

Out-of-the-box solution to get your resume online. Built on WordPress's custom post types, it offers a uniquely familiar approach to publishing

== Description ==

WP Resume is an out-of-the-box solution to get your resume online and keep it updated. Built on WordPress 3.0’s custom post type functionality, it offers a uniquely familiar approach to publishing. If you’ve got a WordPress site, you already know how to use WP Resume.

You can [see it in action](http://ben.balter.com/resume/) or fore information and troubleshooting, check out the [Plugin Homepage](http://ben.balter.com/2010/09/12/wordpress-resume-plugin/).

Please note: this is an initial release, so some problems are to be expected.

Features include:

* Support for sections (e.g., education, experience), organizations (e.g., somewhere state university, Cogs, Inc.), positions (e.g., bachelor of arts, chief widget specialist), and details (e.g., grew bottom line by 15%, president of the sustainability club)
* Follows best practices in resume layout and design
* One click install, just start adding content
* Built on existing WordPress code, utilizing a single custom post type and two custom taxonomies
* The WYSIWYG editing experience you know and love
* Revisioning
* Integrates with your theme like they were made for each other
* Custom URL
* Does not use pretentious accents on the word "resume"
* Extremely original title

The hardest part of getting your resume online should be doing the work listed on it, not wrestling the publishing platform. Simply put, WP Resume steps aside and lets your experience shine.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings via the Resume->Options menu

= Use =

1. Add sections via the Resume->Add Sections menu.  Common sections might be "Experience", "Education", or "Awards".
2. Add organizations via the Resume->Add Organizations menu.  These are often places you've worked (e.g., Cogs, Inc.) or places you've studies (e.g., University U.). 
	Place the organization's location in the description field.
3. Add positions via the Resume-Add Positions menu. 
	Your title (e.g., Widget Specialist) goes in the title field. 
	Put details in the content field (e.g., increased bottom line by 25%).  
	Put dates in the date fields (e.g., from: May 2005, to:August 2009). Neither is required.  Your resume will be sorted by the end date.
4. Repeat step #3 as needed for each position.

== Frequently Asked Questions == 

= Wouldn't it be really cool if it could do X? =

Yes.  It probably would be.  [Leave a comment on the plugin page](http://ben.balter.com/2010/09/12/wordpress-resume-plugin/), and I'll try my best to make it happen.

= Can I customize the layout and style of the resume =

Yes.  WP Resume includes a default stylesheet that can easily be overridden by your theme's defaults or other CSS you may drop in.

== Planned Features ==

= 1.0b planned = 

 * AJAX add of custom terms on add position page
 * AJAX Inline editing of resume content
 * Edit buttons on resume for logged in users
 * Revise text on add term pages (description should be location)
 * Filter Contact/Name HTML
 * Default sections / options on activation
 * WYSIWYG contact info editor
 * Drag and drop section ordering
 
= Beyond = 

 * Import from LinkedIn via API
 * Better SEO
 * Export into machine-readable formats

== Changelog ==

= 1.0a =
* Initial Alpha Release

= 1.1a =
* Fixed problem with resume expecting content and erring out when none existed
