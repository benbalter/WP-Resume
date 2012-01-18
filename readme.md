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

See `sample-plugin.php`, an object-oriented refresh of the classic Hello Dolly plugin for an example on how to extend the boilerplate for yoru plugin. *In short...*

1.	Fork the project
2.  Rename `sample-plugin.php` to `{your-plugin-slug}.php` and update the plugin header, name, class name, slug, etc. acordingly (or begin with a new file, following the sample's format)
3.  Remove all remainging `sample-*` files
4.  Uncomment the `sample-*` directive from `.gitignore`
5.  Build your plugin as you would normally
6.  Place any sub-classes you want to include in the includes folder using the naming convention `{Parent_Class}_{Child_Class}` and the file name `child-class.php`
7.  Place any css or javascript files in the appropriate folder

Class-by-Class Usage
--------------------

**Plugin_Boilerplate**

* Create a child plugin by extending the `Plugin_Boilerplate` class (**see `sample-plugin.php`**)
* Define name, slug, slug_, prefix, version, and min_wp within your child class. These variables are used throughout the plugin to do things like prefix api calls or name your plugins options in the options table.
* Place any langauge files in the `/languages/` folder using the format `{plugin-slug}_{language}.php`
* Create a function in the child class called upgrade (or hook into the `{prefix_}slug` action) to handle upgrades / activation
* Place any sub-classes you want auto included into the `/includes/` folder
	* Files should be named {class-name}.php
  * Classes should be named {Parent_Class}_{Child_Class}
  * See `sample-lyrics.php` for an example
  * Classes will be available as `$this->{child_class}`
  * Be sure to store parent as static instance (passed via `__construct`) to access parent or sibling classes 
 
**API**

* Access via `$this->api->method()` in parent class or via `self::$parent->api->method()` in child classes
* `$this->api->do_action( $name [, $args...] )` will prepend `$prefix` to `$name` prior to calling the standard `do_action` API 
* Same applies for `$this->api->apply_filters( $name, $value [, $args...] );`

**Cache**

* Stores all cache entries in a plugin-specifc group to prevent collision
* Accessible via `$this->cache` (parent) or `self::$parent->cache` (child)
* Store a cache value of "posts" as "5": `$this->cache->posts = 5` or `$this->cache->set( 'posts', 5 )`
* Retrieve a cached value of "posts": `$this->cache->posts` or `$this->cache->get( 'posts )`
* Delete a cached value: `$this->cache->delete( 'posts' )`
* Specify default TTL `$this->cache->ttl = 3600`

**Capabilities**

*
( work in progress )