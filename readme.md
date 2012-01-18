WordPress Plugin Boilerplate
============================

Provides common boilerplate for creating object-oriented WordPress plugins.

Features
--------

* API wrapper to preface api calls (`apply_filters`, `do_action`) with plugin specific prefix
* WP_Cache wrapper that sets plugin-specific cache group and plugin-specific TTL
* Automatically add default plugin-specific capabilities
* Debug tools with Debug Bar integration
* Unobtrusive donate plea
* Automatically enqueues and localizes front-end and admin css and javascript files
* Options API wrapper for setting and retrieving plugin-specific user, site, and global options
* Templating system
* Loads i18n files with proper text domain
* Tracks DB version and fires activation / upgrade hook on `admin_init`
* Verifies minimum WordPress version requirements
* Autoloads all files in `/includes/` directory

Usage
-----

See `sample-plugin.php`, an object-oriented refresh of the classic Hello Dolly plugin for an example on how to extend the boilerplate for yoru plugin.

In short:
1. Fork the project
1. Rename `sample-plugin.php` to `{your-plugin-slug}.php` and update the plugin header, name, class name, slug, etc. acordingly (or begin with a new file, following the sample's format)
1. Remove all remainging `sample-*` files
1. Uncomment the `sample-*` directive from `.gitignore`
1. Build your plugin as you would normally
1. Place any sub-classes you want to include in the includes folder using the naming convention `{Parent_Class}_{Child_Class}` and the file name `child-class.php`
1. Place any css or javascript files in the appropriate folder

( work in progress )