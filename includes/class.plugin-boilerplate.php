<?php
/*  Plugin Boilerplate
 *
 *  Provides common boilerplate for creating object-oriented WordPress plugins.
 *
 *  Copyright (C) 2011-2012  Benjamin J. Balter  ( ben@balter.com -- http://ben.balter.com )
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @copyright 2011-2012
 *  @license GPL v3
 *  @version 1.0
 *  @package Plugin_Boilerplate
 *  @author Benjamin J. Balter <ben@balter.com>
 *  @link https://github.com/benbalter/Plugin-Boilerplate/
 */

if ( !class_exists( 'Plugin_Boilerplate_v_1' ) ):

	class Plugin_Boilerplate_v_1 {

	public $name                = 'Plugin Boilerplate'; //Human-readable name of plugin
	public $slug                = 'plugin-boilerplate'; //plugin slug, generally base filename and in url on wordpress.org
	public $slug_               = 'plugin_boilerplate'; //slug with underscores (PHP/JS safe)
	public $prefix              = 'plugin_boilerplate_'; //prefix to append to all options, API calls, etc. w/ trailing underscore
	public $directory           = null;
	public $version             = '1.0';
	public $boilerplate_version = '1';
	public $min_wp              = '3.2';
	public $classes             = array();

	/**
	 * Register with WordPress API on Construct
	 * @param class $child (reference) the child (extended) class
	 */
	function __construct( &$child ) {

		//don't let this fire twice
		if ( get_class( &$this )  == 'Plugin_Boilerplate' )
			return;

		//verify minimum WP version, and shutdown if insufficient
		if ( !$this->_verify_wp_version() )
			return false;

		//upgrade db
		add_action( 'admin_init', array( &$this, '_upgrade_check' ) );

		//i18n
		add_action( 'init', array( &$this, '_i18n' ) );

		//load subclasses on init, allowing other plugins or self to override
		add_action( 'plugins_loaded', array( &$this, '_init' ), 5 );

	}


	/**
	 * Loads all subclasses
	 * Fires on init (rather than construct)
	 * Other plugins and child plugins can override default behavior
	 */
	function _init() {

		$this->_load_subclasses();

		$this->api->do_action( 'init' );

	}


	/**
	 * Loads and substantiates all classes in the includes and boilerplate-classes folders
	 * Classes should be named in the form of Plugin_Boilerplate_{Class_Name}
	 * Files should be the name of the class name e.g. class-name.php
	 * Classes will be autoloaded as $object->{class_name}
	 */
	function _load_subclasses() {

		//load all boilerplate core classes, followed by and child plugin classes
		$files = glob( dirname( __FILE__ ). '/boilerplate-classes/*.php' ) ;
		$files = array_merge( $files, glob( $this->directory . '/includes/*.php' ) );

		foreach ( $files as $file ) {

			//don't include self
			if ( basename( $file ) == basename( __FILE__ ) )
				continue;

			//the name of this particular class, e.g., API or Options
			$include = $this->_get_include_object( $file );

			if ( !apply_filters( "{$this->prefix}load_{$include->object_name}", true, $include ) )
				continue;

			if ( !class_exists( $include->class ) )
				@require_once $include->file;

			if ( !class_exists( $include->class ) ) {
				trigger_error( "{$this->name} -- Unable to load class {$include->class}. see the readme for class and file naming conventions" );
				continue;
			}

			$this->{$include->object_name} = new $include->class( &$this );
			$this->classes[ $include->object_name ] = $include->class;

		}

		//do this after all modules have loaded so we know API exists
		foreach ( $this->classes as $name=>$class)
			$this->api->do_action( "{$name}_init" );

	}


	/**
	 * Init i18n files
	 */
	function _i18n() {
		load_plugin_textdomain( $this->slug, false, plugin_basename( $this->directory ) . '/languages/' );
	}


	/**
	 * Upgrades DB
	 * Fires on admin init to support SVN
	 */
	function _upgrade_check() {

		if ( $this->options->db_version == $this->version )
			return;

		$this->upgrade( $this->options->db_version, $this->version );

		$this->api->do_action( 'upgrade', $this->version, $this->options->db_version );

		$this->options->db_version = $this->version;

	}


	/**
	 * Default upgrade procedure, to be overridden by child class
	 * @param unknown $from_version
	 * @param unknown $to_version
	 */
	function upgrade( $from_version, $to_version ) { }


	/**
	 * Verifies WordPress version meets the necessary minimum
	 * @return unknown
	 */
	function _verify_wp_version() {

		if ( get_bloginfo( 'version' ) >= $this->min_wp )
			return true;

		add_action( 'admin_notices', array( &$this, 'update_wp' ) );
		do_action( "{$this->prefix}_wp_outdated" );

		return false;
	}


	/**
	 * Default update notice
	 * Allow child plugins to override
	 */
	function update_wp() {
		$this->template->update_wp();
	}


	/**
	 * Returns an object with all information about a file to include
	 *
	 * Fields:
	 * file - path to file
	 * name - Title case name of class
	 * object_name - lowercase name that will become $this->{object_name}
	 * native - whether this is a native boilerplate class
	 *  base - the base of the class name (either Plugin_Boilerplate or the parent class name)
	 *  class - The name of the class
	 *
	 * @param string $file the file to include
	 * @return object the file object
	 */
	function _get_include_object( $file ) {

		$class = new stdClass();
		$class->file = $file;
		$name = basename( $file, '.php' );
		$name = str_replace( '-', '_', $name );
		$name = str_replace( '_', ' ', $name );
		$class->name = str_replace( ' ', '_', ucwords( $name ) );
		$class->object_name = str_replace( ' ', '_', $name );

		//base, either Plugin class or Plugin_Boilerplate
		$class->native = ( dirname( $file ) == dirname( __FILE__ ) . '/boilerplate-classes' );
		$class->base = ( $class->native ) ? 'Plugin_Boilerplate' : get_class( &$this );
		$class->class = $class->base . '_' . $class->name;

		//if this is a PB native class, append a version # to prevent collision
		if ( $class->native )
			$class->class .= '_v_' . $this->boilerplate_version;

		return $class;

	}


	/**
	 * Returns Array of all loaded classes
	 * Format: Object Name => Class Name
	 * @return array all registered classes
	 */
	function get_classes() {
		return $this->classes;
	}


}


endif;