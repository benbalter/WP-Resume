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

* Create a child plugin by extending the `Plugin_Boilerplate` class (*see `sample-plugin.php`*)
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

* Registers default capabilities with roles API
* If capability already exists, honors pre-existing grant
* If a custom role exists, will default to subscriber
* See inline documentation for details on how to pass roles/capabilities array
* Default capabilities can be overridden by 3d party plugins such as Members

**Debug**

* Available via `$this->debug` or `self::$parent->debug`
* Only displays for administrators while `WP_DEBUG` is enabled
* Integrates with Debug_Bar plugin
* Returns value so can be apply directly to a filter
* `$this->debug->debug( $variable );` will var_dump the variable
* `$this->debug->debug( $variable, true );` will var_dump the varible, then die immediatly after
* `$this->debug->debug( $variable, null, 'print_r'` will run variable through `print_r` (or other user-defined function)
* `$this->debug->log( $variable )` will log variable(s) to the Debug_Bar plugin

**Donate**

* Creates an unobtrusive donate link
* Usage: `$this->donate->form()` to display donation form
* Form customizable via `/templates/` folder
* Stores user-preference to hide via ajax

**Enqueue**

* Front End - Automatically enqueues all files in `/css/front-end/` and `/js/front-end`
* Admin - Automatically enqueues all files in `/css/admin/` and `/js/admin`
* To pass strings or information to script, pass an array to `$this->enqueue->data`
* Data will be available as `{plugin_slug}` via `wp_localize_script`

**Options**

* Stores all options in a single row in the options table
* All request run through `{prefix}_options` and `{prefix}_{options name}`
* All requests cached
* Three option scopes, site, user, or global
    * site - all users on the site
    * user - that specific user on that specific site
    * global - that specific user on all sites within network
* Default scope is site
* Set via `$this->options->scope`, e.g., `$this->options->scope = 'user';`
* Only applies to magic methods
* Magic methods
    * Retrieve a stored option called "name": `$value = $this->options->name;`
    * Store an option: `$this->options->name = 'Ben';`
* Other methods
    * Get all user options for a user: `$this->options->get_user_options()` (current user) or `$this->options->get_user_options( 1 )` (specific user)
    * Get specific user option: `$this->options->get_user_option( 'name' )`;
    * Set a specific user option: `$this->options->set_user_option( 'name', 'Ben' )`
    * Set all user options: `$this->options->set_user_options( $array )`
    * Get all site options: `$this->options->get_options()`;
    * Get a specific site option: `$this->options->get_option( 'name' );`
    * Set a specific site option: `$this->options->set_option( 'name' );`
    * Set all site options: `$this->options->set_options( $array )`

( work in progress )