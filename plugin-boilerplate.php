<?php
/*
Plugin Name: Plugin Boilerplate
Description: 
Version: 1.0
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

/*  Copyright 2012  Benjamin J. Balter  (email : ben@balter.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Plugin_Boilerplate {
	
	static $instance;
	public $name = 'Plugin Boilerplate';
	public $slug = 'plugin-boilerplate';
	public $version = '1.0';
	
	function __construct() {

		self::$instance = &$this;
		
		$this->load_subclasses();
		
		//i18n
		add_action( 'init', array( &$this, 'i18n' ) ); 
		
		//upgrade db
		add_action( 'admin_init', array( &$this, 'upgrade' ) );

	}
	
	/**
	 * Loads and substantiates all classes in the includes folder
	 * Classes should be named in the form of Plugin_Boilerplate_{Class_Name}
	 * Files should be the name of the class name e.g. class-name.php
	 */ 
	function load_subclasses() {

		foreach ( glob( dirname( __FILE__ ) . '/includes/*.php' ) as $file ) {
			
			$name = str_replace( '-', '_', basename( $file, '.php' ) );
			$class = 'Plugin_Boilerplate_' . ucwords( $name );
			
			if ( !class_exists( $class ) )
				@require_once( $file );
			
			if ( class_exists( $class ) )
				$this->$name = new $class( &$this );
	
		}
		
	}
	
	/**
	 * Init i18n files
	 */
	function i18n() {
		load_plugin_textdomain( $this->slug, false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	 * Upgrades DB
	 * Fires on admin init to support SVN
	 */
	function upgrade() {

		if ( $this->options->db_version == $this->version )
			return;
			
		$this->do_action( 'upgrade' );
			
		$this->options->db_version = $this->version;
		
	}
	
	/**
	 * Prepends slug to action and calls standard do_action function
	 * @param string $name the name of the action
	 */
	function do_action( $name ) {
		
		$args = func_get_args();
		array_unshift( $args, 'action' );
		
		call_user_func_array( array( &$this, 'api'), $args );
	
	}
	
	/**
	 * Prepends slug to action and calls standard apply_filters function
	 * @param string $name the name of the action
	 * @return the result of the filter
	 */	
	function apply_filters( $name ) {

		$args = func_get_args();
		array_unshift( $args, 'filter' );
		
		call_user_func_array( array( &$this, 'api'), $args );	
	}
	
	/**
	 * Prepends slug to do_action and apply_filters calls
	 * @param string $type either action or filter
	 * @param string $name the name of the api call
	 */
	function api( $type, $name ) {

		if ( $type == 'action' )
			$function = 'do_action';
		else
			$function = 'apply_filters';

		$args = func_get_args();
		array_shift( $args );
		$args[0] = str_replace( '-', '_', "{$this->slug}_$name" );

		return call_user_func_array( $function, $args );

	}
	
}

new Plugin_Boilerplate();