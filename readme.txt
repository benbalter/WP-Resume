=== Plugin Name ===
Contributors: benbalter
Donate link: http://ben.balter.com/
Tags: resume, online reputation, personal branding, experience, education, awards, cv
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 1.42

Out-of-the-box solution to get your resume online. Built on WordPress's custom post types, it offers a uniquely familiar approach to publishing

== Description ==

WP Resume is an out-of-the-box solution to get your resume online and keep it updated. Built on WordPress 3.0's custom post type functionality, it offers a uniquely familiar approach to publishing. If you've got a WordPress site, you already know how to use WP Resume.

You can [see it in action](http://ben.balter.com/resume/) or for information and troubleshooting, check out the [Plugin Homepage](http://ben.balter.com/2010/09/12/wordpress-resume-plugin/).

Features include:

* Support for sections (e.g., education, experience), organizations (e.g., somewhere state university, Cogs, Inc.), positions (e.g., bachelor of arts, chief widget specialist), and details (e.g., grew bottom line by 15%, president of the sustainability club)
* Follows best practices in resume layout and design
* One click install, just start adding content
* Drag and drop ordering of resume elements
* Built on existing WordPress code, utilizing a single custom post type and two custom taxonomies
* The WYSIWYG editing experience you know and love
* Revisioning
* Integrates with your theme like they were made for each other (via a shortcode)
* Does not use pretentious accents on the word "resume"
* Extremely original title

The hardest part of getting your resume online should be doing the work listed on it, not wrestling the publishing platform. Simply put, WP Resume steps aside and lets your experience shine.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

= Use =

WP Resume groups your resume content into sections. Common sections might be "Experience", "Education", or "Awards".

Within each section, your positions are grouped by organizations.  These are often places you've worked (e.g., Cogs, Inc.) or places you've studies (e.g., University U.). 

Each position has a title (e.g., Widget Specialist), details (e.g., increased bottom line by 25%), and dates of service (e.g., from: May 2005, to:August 2009). Dates are not required, but the positions on your resume are sorted reverse chronologically by default (you can always manually arrange them on the options page).

1. Add content to your resume through the Resume->Add New Position panel in the WordPress administrative backend
2. If you wish, add a title, contact information, and order your sections in the Resume->Options panel
3. Create a new page as you would normally
4. Add the text [wp_resume] to the page's body
5. Your resume will now display on that page.

Note: Although some styling is included by default, you can customize the layout by modifying your theme's stylesheet

== Frequently Asked Questions == 

= Wouldn't it be really cool if it could do X? =

Yes.  It probably would be.  [Leave a comment on the plugin page](http://ben.balter.com/2010/09/12/wordpress-resume-plugin/), and I'll try my best to make it happen.

= Can I customize the layout and style of the resume =

Yes.  WP Resume includes a default stylesheet that can easily be overridden by your theme's defaults or other CSS you may drop in.

== Planned Features ==
 
* Import from LinkedIn via API
* Better SEO
* Export into machine-readable formats
* AJAX Inline editing of resume content

== Screenshots ==

1. Example resume output with default CSS (can be customized to fit your site)
2. Add position page -- identical to the add post page with a few extra boxes
3. Resume menu in wp-admin sidebar

== Changelog ==

= 1.42 =
* Removed stray hook which reset resume title to site title

= 1.41 =
* Fixed minor bug where activated would display a notice level warning if wp_debug was on 
* Moved db_upgrade procedure to admin_init hook
* Made sure db_upgrade does not overwrite existing settings
* Gave the section ordering backend a much needed coat of paint (h/t Andrew Norcross)
* Fixed the contact info kses filter to allow tags normally allowed in posts (was comment filter)

= 1.4 = 
* Drag-and-drop ordering of resume elements (sections, organizations, and positions)
* Now sorts based on WordPress's built-in menu_order field rather than a custom meta value (smaller database footprint)
* Help text added to add/edit position page to aide in new ordering functionality
* Updates old versions of plugin data on activation/upgrade

= 1.31 =
* Minor fix to the included stylesheet to reset #header and #header h2 backgrounds to none in case they conflict with the theme's defaults.

= 1.3 =
* For greater flexibility and theme integration, switched from URL Rewrite rules via a "slug" option to [wp_resume] shortcode
* Added walkthrough to options page
* Check for <1.3 slug on activation and add shortcode to associated page if exists
* Fixed edit link CSS
* Fixed problem where template would prematurely close the resume div prior to last section
* Resume template now uses a sub-loop so as not to interfere with the page itself
* Lost the beta tag

= 1.2 (beta) = 
* Adding WYSIWG editor for contact information
* Followed standard "loop" format for template
* Position title and content now call WordPress filters and action hooks
* Changed function names to avoid potential conflict with another plugin
* Fixed fatal error when no content existed
* HTML is now filtered (contact info & title)
* Edit buttons on resume for logged in users
* Fixed bug where CSS did not load properly in certain situations
* Default sections / options are added on activation
* Added help text to add term pages, revised some of the field labels for better clarity
* Removed unused fields from add section page
* AJAX adding of custom terms on add position page
* Added procedure to update database to new field prefix on activation

= 1.1 (alpha) =
* Fixed problem with resume expecting content and erring out when none existed

= 1.0 (alpha) =
* Initial Alpha Release