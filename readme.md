# WP Resume #
**Contributors:** benbalter  
**Donate link:** http://ben.balter.com/donate/  
**Tags:** resume, online reputation, personal branding, experience, education, awards, cv, hresume  
**Requires at least:** 3.2  
**Tested up to:** 3.4  
**Stable tag:** 2.2.3  

Out-of-the-box solution to get your resume online. Built on WordPress's custom post types, it offers a uniquely familiar approach to publishing

## Description ##

WP Resume is an out-of-the-box solution to get your resume online and keep it updated. Built on WordPress 3.0's custom post type functionality, it offers a uniquely familiar approach to publishing. If you've got a WordPress site, you already know how to use WP Resume.

You can [see it in action](http://ben.balter.com/resume/) or for information and troubleshooting, check out the [Plugin Homepage](http://ben.balter.com/2010/09/12/wordpress-resume-plugin/).

### Features include: ###

* Support for sections (e.g., education, experience), organizations (e.g., somewhere state university, Cogs, Inc.), positions (e.g., bachelor of arts, chief widget specialist), and details (e.g., grew bottom line by 15%, president of the sustainability club)
* Supports multiple resumes, on the same page, or on their own
* Follows best practices in resume layout and design
* One click install, just start adding content
* Drag and drop ordering of resume elements
* Outputs in [hResume](http://microformats.org/wiki/hresume) compatible format using HTMl5 semantic tags
* Can output as plain text for pasting into job applications
* Automatically saves revisions of every change you make
* The WYSIWYG editing experience you know and love
* Integrates with your theme like they were made for each other (via a shortcode)
* Spanish and Portuguese (BR) Translation Support
* Does not use pretentious accents on the word "resume"
* Extremely original title

### Under the hood: ###

* Built on existing WordPress code, utilizing a single custom post type and two custom taxonomies
* JSON API so you can use the data anywhere
* Support for custom templates and CSS files (like a child theme, just place them in your theme directory)
* Plugin API hooks for developers to build on

The hardest part of getting your resume online should be doing the work listed on it, not wrestling the publishing platform. Simply put, WP Resume steps aside and lets your experience shine.

**Developers,** have a killer feature you'd love to see included? Feel free to [fork the project on GitHub](https://github.com/benbalter/WP-Resume/) and submit your contributions via pull request.

*Enjoy using WP Resume? Please consider [making a small donation](http://ben.balter.com/donate/) to support the software’s continued development.*

## Installation ##

1. Upload the `wp-resume` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

### Use ###

WP Resume groups your resume content into sections. Common sections might be "Experience", "Education", or "Awards".

Within each section, your positions are grouped by organizations.  These are often places you've worked (e.g., Cogs, Inc.) or places you've studies (e.g., University U.). 

**Each position has a title (e.g., Widget Specialist), details (e.g., increased bottom line by 25%), and dates of service (e.g., from:** May 2005, to:August 2009). Dates are not required, but the positions on your resume are sorted reverse chronologically by default (you can always manually arrange them on the options page).  

1. Add content to your resume through the Resume->Add New Position panel in the WordPress administrative backend
2. Add your name, contact information, summary and order your sections in the Resume->Options panel
3. Create a new page as you would normally
4. Add the text [wp_resume] to the page's body
5. Your resume will now display on that page.

**Note:** Although some styling is included by default, you can customize the layout by modifying your theme's stylesheet or creating a custom template file.  

[Photo via [shawnmichael](http://www.flickr.com/photos/shawnmichael/4246330043/)]

## Frequently Asked Questions ##

### I don't like your CSS, can I use my own? ###

Yes, (and I don't blame you). Simply create a file in your theme directory called "resume-style.css" and the plugin will pull that style sheet rather than its built in default style.

### The included template doesn't fit into my theme all that well, can I build my own? ###

Yes. Simply place a file in your theme directory and call it "resume.php" (or "resume-json.php" or "resume-text.php" for the feeds) and edit away. The plugin will use your template, rather than the one included. It may be easier to begin by copying the default template into your theme folder and editing it to meet your needs rather than starting from scratch.

### hResume? What's that ###

[hResume](http://microformats.org/wiki/hresume) is a machine-readable format for sharing your resume on the net. It's a format that allows both users (you and me) and computers (automated job searching software) to read your resume. You don't have to do anything special to take advantage of the format, just fill out your resume if you would normally.

### I need a plain-text version of my resume to paste into a job application, can you help me out? ###

Yes. Just append "?feed=text" to the URL of your resume. So if you resume were at http://mydomain.com/resume/, the plain text version would be at http://mydomain.com/resume/?feed=text.

### I'm building a really cool app and it'd be awesome if I could get a JSON feed of the resume content to do awesome stuff ###

Awesome. Just append "?feed=json" to the URL of your resume and you're all set.  So if you resume were at http://mydomain.com/resume/, the API frontend would be at http://mydomain.com/resume/?feed=json.

### Wouldn't it be really cool if it could do X? ###

Yes.  It probably would be.  [Leave a comment on the plugin page](http://ben.balter.com/2010/09/12/wordpress-resume-plugin/), and I'll try my best to make it happen, or if you're a developer, feel free to [fork the code on Github](https://github.com/benbalter/WP-Resume/) and send a pull request to get your feature included.

### I just want to make a small tweak to the CSS, do I have to do the whole template thing? ###

No.  WP Resume includes a default stylesheet that can easily be overridden by your theme's defaults or other CSS you may drop in.

### I'm having trouble organizing some of the positions on the options screen; they're not sortable. What gives? ###

Make sure all your positions are associated with sections.

## Planned Features ##
 
* Import from LinkedIn via API

## Screenshots ##

###1. Example resume output with default CSS (can be customized to fit your site)###
![Example resume output with default CSS (can be customized to fit your site)](http://s.wordpress.org/extend/plugins/wp-resume/screenshot-1.png)

###2. Add position page -- identical to the add post page with a few extra boxes###
![Add position page -- identical to the add post page with a few extra boxes](http://s.wordpress.org/extend/plugins/wp-resume/screenshot-2.png)

###3. Resume menu in wp-admin sidebar###
![Resume menu in wp-admin sidebar](http://s.wordpress.org/extend/plugins/wp-resume/screenshot-3.png)


## Changelog ##

### 2.5 ###
* **NOTE: you may need to manually reactivate the plugin after upgrading to this version**
* Complete codebase overhaul for stability, scalability, and performance
* Added project to [Github](https://github.com/benbalter/WP-Resume/)
* Added qTranslate and enhanced i18n support for sections, organizations, and dates (Special thanks to [Fabio A Locati](http://fabiolocati.com/))
* Significant enhancements to json and plain text versions
* Custom section ordering now honored throughout administrative dashboard
* Plugin now relies on 22 custom capabilities, allowing administrators more granular control of user permissions, customizable through plugin such as [Members](http://wordpress.org/extend/plugins/members/).
* Better sanitization of position start and end dates
* Additional API hooks added to allow developers to modify and adapt the plugin's functionality
* GPL License now distributed with plugin
* Code cleanup and additional inline documentation

### 2.2.3 ###
* Fix for organization links not properly saving in certain cases
* Fix for plugin not retaining section ordering under certain circumstances
* Fix for Edit Resume link not appearing on some pages with embedded resumes
* Better scalability of organization link storage
* Better internal handling of plugin options

### 2.2.2 ###
* Additional refinement of upgrade path from certain older versions
 
### 2.2.1 ###
* Fixed bug where users upgrading from certain versions would receive an error upon reactivation.

### 2.2 ###
* User options are now site specific on multisite installs (beta)
* Organization names can now be links (e.g., organization's website)
* Stylesheets now included on organization, section, and position pages
* Updated translations (special thanks once again to [Rodolfo Buaiz](http://rodbuaiz.com/))
* "Resume" sub-menu label changed to "All Positions" for clarity
* Default templates moved to "includes" folder (resume.php, resume-text.php, resume-json.php)
* Support for custom templates in parent themes (using WordPress's native `locate_template()` function)
* Better handing of template files (using WordPress's native `load_template()` function)

### 2.1 ###
* Dragdrop interface for reordering resume elements completely rewritten for greater compatibility
* Handling of organizations in default resume template greatly improved (organizations are no longer required for all positions)
* Removed requirement that all positions have organizations before they could be saved
* Positions, sections, and organizations in dragdrop interface now link to their edit pages
* Fix bug where resume would not pass HTML validation if URL rewriting was enabled
* Fixed typo in organizations slug when URL rewriting was enabled

### 2.0.4 ###
* Added option to automatically hide page titles on resume pages (via .hide-title class)
* If URL rewriting is enabled position, organization, and section titles now appear as links
* URL Rewriting now properly works for sections and organizations, if enabled
* Added .resume CSS class to posts with resume shortcodes
* Created functions to help pull and format author's information into custom resume templates
* Added additional API hooks for developers to customize the plugin's functionality
* Added donate link to plugin page

### 2.0.3 ###
* Added Edit Resume button to the admin bar for logged in users
* Stylesheet and Javascript only loaded on pages with resumes
* Checks that positions have sections and organizations before saving
* Better caching support
* Better organization of Javascript and stylesheet files
* Added API Hooks and Filters to customize the plugin's behavior
* Added resume icon to admin menu
* Cleaned up administrative interfaces
* Fixed bug where second tinyMCE editor would appear on options screen in certain cases
* Fixed bug where contact fields could not be removed once added

### 2.0.2 ###
* Fixes bug where options would not save and positions/summary would not display if user_nicename differed from user_login -- Special thanks to [Paul Maisonneuve](http://aboutpaul.com)
* Options page is now accessible to non-administrators and performs proper capability checks (based off edit_posts, edit_others_posts, and manage_options)

### 2.0.1 ###
* Fixed bug where summary would not display on resume in certain circumstances

### 2.0 ###
* Complete code overhaul; rewritten from the ground up with performance and stability improvements
* Fix bug where some users were not able to edit settings on the options page
* Fix but where in some cases page with shortcode would appear blank
* Added Spanish and Portuguese internationalization support (special thanks to [Rodolfo Buaiz](http://rodbuaiz.com/) for the translation help )

### 1.6.6 ###
* Fixed handling of custom stylesheets and template files with child themes in versions greater than 3.1

### 1.6.5 ###
* Removed improperly used HTML5 details tag which forced position details into toggle in some browsers
* Fix bug with summary display on resume

### 1.6.4 ###
* Fixed error message on options page for single user blogs

### 1.6.3 ###
* HTML fixes on options page
* Fixed issue with options not appearing after save in some cases

### 1.6.2 ###
* Fixed E_NOTICE level warning on options page

### 1.6.1 ###
* Bugfix on options page
* Bugfix on upgrade procedure
* Permalink bugfix
* Added Plugin API Hooks for customization

### 1.6 ###
* Added ability to publish multiple resumes on the same site
* Added conditional HTML5shiv to force IE to support HTML5 Semantic tags and added toggle to options page
* Added option for individual position pages and organization/section indexes (URL rewriting)
* Added internationalization and WPML support (.POT file now in /18n/ folder)
* Moved resume options to usermeta table to improve scalability
* Fixed E_NOTICE level warning for undefined index in the "Resume Order" section when no organization exists
* Fixed IE bug on section ordering due to trailing comma in list
* Fixed display of headers for empty sections
* Improved upgrade procedure
* Fixed feed formatting

### 1.52 ###
* Added display:block to header and section to fix display issues in FireFox

### 1.5 ###
* HTML5 Semantic Tags
* hResume compatible (XHTML)
* JSON feed
* Plain text feed (for copying into applications)
* Summary field
* Revised contact info handling
* Customizable, theme-specific templates and CSS
* Bug, stability, and performances fixes

### 1.42 ###
* Removed stray hook which reset resume title to site title

### 1.41 ###
* Fixed minor bug where activated would display a notice level warning if wp_debug was on 
* Moved db_upgrade procedure to admin_init hook
* Made sure db_upgrade does not overwrite existing settings
* Gave the section ordering backend a much needed coat of paint (h/t Andrew Norcross)
* Fixed the contact info kses filter to allow tags normally allowed in posts (was comment filter)

### 1.4 ###
* Drag-and-drop ordering of resume elements (sections, organizations, and positions)
* Now sorts based on WordPress's built-in menu_order field rather than a custom meta value (smaller database footprint)
* Help text added to add/edit position page to aide in new ordering functionality
* Updates old versions of plugin data on activation/upgrade

### 1.31 ###
* Minor fix to the included stylesheet to reset #header and #header h2 backgrounds to none in case they conflict with the theme's defaults.

### 1.3 ###
* For greater flexibility and theme integration, switched from URL Rewrite rules via a "slug" option to [wp_resume] shortcode
* Added walkthrough to options page
* Check for <1.3 slug on activation and add shortcode to associated page if exists
* Fixed edit link CSS
* Fixed problem where template would prematurely close the resume div prior to last section
* Resume template now uses a sub-loop so as not to interfere with the page itself
* Lost the beta tag

### 1.2 ###
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

### 1.1 ###
* Fixed problem with resume expecting content and erring out when none existed

### 1.0 ###
* Initial Alpha Release

## Upgrade Notice ##

### 2.1 ###
Bug fixes for dragdrop interface and URL rewriting

### 2.0.4 ###
Formatting fixes including ability to hide page titles

### 2.0.3 ###
Minor performance improvements, bug fixes, and user interface tweaks

### 2.0.2 ###
Fixes bug where options would not save and positions/summary would not display if user_nicename differed from user_login

### 2.0 ###
Complete code overhaul; rewritten from the ground up with performance and stability improvements, bug fixes; Spanish and Portuguese internationalization.

### 1.6.6 ###
Fixes handling of custom stylesheets and template files with child themes in versions of WordPress greater than 3.1
