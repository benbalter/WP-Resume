<?php

if ( !class_exists( 'Plugin_Boilerplate' ) ):

class Plugin_Boilerplate {
	
	static $instance;
	public $name = 'Plugin Boilerplate';
	public $slug = 'plugin-boilerplate';
	public $directory = null;
	public $version = '1.0';
	public $min_wp = '3.2';
	public $classes = array();
	
	function __construct() {

		self::$instance = &$this;
		
		//verify minimum WP version, and shutdown if insufficient
		if ( !$this->_verify_wp_version() )
			return false;
		
		//assume plugin base is one directory up
		$this->directory = dirname( dirname( __FILE__ ) );

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
	
	function _get_plugin_basename() {
		return plugin_basename( __FILE__ );	
	}
	
	/**
	 * Loads and substantiates all classes in the includes and boilerplate-classes folders
	 * Classes should be named in the form of Plugin_Boilerplate_{Class_Name}
	 * Files should be the name of the class name e.g. class-name.php
	 * Classes will be autoloaded as $object->{class_name}
	 */ 
	function _load_subclasses() {
			
		//load all boilerplate core classes, followed by and child plugin classes
		$files = glob( dirname( __FILE__ ) . '/boilerplate-classes/*.php' ) ;
		$files = array_merge( $files, glob( dirname( __FILE__ ) . '/*.php' ) );
		
		//don't include self
		unset( $files[ array_search( __FILE__, $files ) ] );

		foreach ( $files as $file ) {
					
			$name = str_replace( '-', ' ', basename( $file, '.php' ) );
			$base = ( dirname( __FILE__ ) == dirname( $file ) ) ? get_class( &$this ) : get_parent_class( &$this );
			$class = $base . '_' . str_replace( ' ', '_', ucwords( $name ) );
						
			if ( !apply_filters( "{$this->slug}_load_{$name}", true ) )
				continue;
			
			if ( !class_exists( $class ) )
				@require_once( $file );
			
			if ( !class_exists( $class ) ) {
				trigger_error( "{$this->name} -- Unable to load class {$class}. see the readme for class and file naming conventions" );
				continue;
			}
			
			$this->$name = new $class( &$this );
			$this->classes[ $name ] = $class;
			
			$this->api->do_action( "{$name}_init" );
			
			
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
	function _upgrade_check() {
	
		if ( $this->options->db_version == $this->version )
			return;
		
		$this->upgrade( $this->options->db_version, $this->version );
		$this->api->do_action( 'upgrade', $this->options->db_version, $this->version );
			
		$this->options->db_version = $this->version;
		
	}
	
	/**
	 * Default upgrade procedure, to be overridden by child class
	 */
	function upgrade( $from_version, $to_version ) {
	
	}
	
	/**
	 * Verifies WordPress version meets the necessary minimum
	 */
	function _verify_wp_version() {
		
		if ( get_bloginfo( 'version' ) >= $this->min_wp )
			return true;
		
		add_action( 'admin_notices', array( &$this, 'update_wp' ) );
		do_action( "{$this->slug}_wp_outdated" );
		
		return false;
	}
	
	/**
	 * Default update notice
	 * Allow child plugins to override
	 */
	function update_wp() {
		$this->template->update_wp();
	}
	
}

endif;