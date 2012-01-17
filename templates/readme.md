Templates
=========

Template files should go in this folder.

Naming
------

Template files should be named `{template-name}.php`.

Usage
-----

Called via `$base_class->template->load( 'template-name', $args )`. 

Variables
---------

Files are included within function scope in the plugin's template path. Object properties and methods are available via `self::$parent->property` or `self::$parent->method()`. Arguments can optionally be passed as an array and will be run through `extract()` prior to inclusion.


