<?php

if ( !class_exists( 'Plugin_Boilerplate' ) ):

class Plugin_Boilerplate {
	
	static $instance;
	public $name = 'Plugin Boilerplate';
	public $slug = 'plugin-boilerplate';
	public $version = '1.0';
	
	function __construct() {

		self::$instance = &$this;
		
		$this->_load_subclasses();
		
		//i18n
		add_action( 'init', array( &$this, '_i18n' ) ); 
		
		//upgrade db
		add_action( 'admin_init', array( &$this, '_upgrade' ) );

	}
	
	/**
	 * Loads and substantiates all classes in the includes folder
	 * Classes should be named in the form of Plugin_Boilerplate_{Class_Name}
	 * Files should be the name of the class name e.g. class-name.php
	 */ 
	function _load_subclasses() {

		foreach ( glob( dirname( __FILE__ ) . '/boilerplate-classes/*.php' ) as $file ) {
					
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
	function _i18n() {
		load_plugin_textdomain( $this->slug, false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	 * Upgrades DB
	 * Fires on admin init to support SVN
	 */
	function _upgrade() {

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
		
		return call_user_func_array( array( &$this, 'api'), $args );	
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

endif;