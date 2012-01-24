<?php
/**
 * Provides interface to enqueue front end and back end css and javascript files
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Enqueue {
	
	static $parent;
	public $js_path = '/js/'; //path to javascript directory relative to plugin base
	public $css_path = '/css/'; //path to css directory relative to plugin base
	public $front_end_data = array(); //array of script localication data for front-end
	public $admin_data = array(); //array of script localication data for admin
	
	/**
	 * Register hooks with WP API
	 */
	function __construct( $instance ) {
	
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
				
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_js' ), 50 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_css' ), 50 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_front_end_css' ), 50 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_front_end_js' ), 50 );
		
	}	
	
	/**
	 * Enqueue scripts for admin backend, if any
	 */
	function enqueue_admin_js( ) {
		return $this->enqueue_js( 'admin' );
	}
	
	/**
	 * Enqueue scripts for front-end, if any
	 */
	function enqueue_front_end_js( ) {
		return $this->enqueue_js( 'front-end' );
	}
	
	/**
	 * Check if script exists and enqueue
	 * @param string $name the name of the script to enqueue
	 */	
	function enqueue_js( $name ) {
								
		$directory = self::$parent->directory . $this->js_path . $name . '/';

		$i = 0;
		foreach ( glob( $directory . '*.js' ) as $file ) {

			if ( !filesize( $file ) )
				continue;
				
			//when in debug mode prefer .dev.js version if it exists
			if ( ( WP_DEBUG || SCRIPT_DEBUG ) && file_exists( str_replace( '.js', '.dev.js', $file ) ) ) 
				continue;

			$file = basename( $file );
			
			//allow child plugins to control when js is enqueued
			if ( !self::$parent->api->apply_filters( 'enqueue_js', true, $file, $name ) )
				continue;

			$slug = ( $i === 0 ) ? self::$parent->slug : self::$parent->slug . "-$i";
			
			wp_enqueue_script( $slug, plugins_url( $this->js_path . $name . '/' . $file, self::$parent->directory . '/readme.txt' ), array( 'jquery' ), filemtime( $directory . $file ), true );
			
			$i++;
			
		}
		
		$data = str_replace( '-', '_', $name . '_data' );
		$this->$data = apply_filters( 'localize_script', $this->$data, $name );
		
		if ( empty( $this->$data ) )
			return;
			
		wp_localize_script( self::$parent->slug, self::$parent->slug_, $this->$data );
				
	}
	
	/**
	 * Enqueue stylesheets for admin backend, if any
	 */
	function enqueue_admin_css( ) { 
		return $this->enqueue_css( 'admin' );
	}

	/**
	 * Enqueue stylesheets for front-end, if any
	 */
	function enqueue_front_end_css( ) {
		return $this->enqueue_css( 'front-end' );
	}

	/**
	 * Check if stylesheet exists and enqueue
	 * @param string $name the name of the stylesheet to enqueue
	 */	
	function enqueue_css( $name ) {
		
		$directory = self::$parent->directory . $this->css_path . $name . '/';

		foreach ( glob( $directory . '*.css' ) as $file ) {

			if ( !filesize( $file ) )
				continue;
		
			$file = basename( $file );
			
			//allow child plugins to control when css is enqueued
			if ( !self::$parent->api->apply_filters( 'enqueue_css', true, $file, $name ) )
				continue;
	
		 	wp_enqueue_style( self::$parent->slug, plugins_url( $this->css_path . $name . '/' . $file , self::$parent->directory . '/readme.txt' ), null, filemtime( $directory . $file ) );
	
		}			
				
	}
	
}