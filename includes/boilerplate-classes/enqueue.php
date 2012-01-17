<?php
/**
 * Provides interface to enqueue front end and back end css and javascript files
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Enqueue {
	
	static $parent;
	
	/**
	 * Register hooks with WP API
	 */
	function __construct( $instance ) {
	
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
	
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_js' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_js' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_front_end_css' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_front_end_css' ) );
		
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
				
		//allow child plugins to control when js is enqueued
		if ( !self::$parent->api->apply_filters( 'enqueue_js', true, $name ) )
			return;
			
		$file = ( WP_DEBUG || SCRIPT_DEBUG ) ? "/js/$name.dev.js" : "/js/$name.js";
		
		//verify file exists and isn't empty
		if ( !file_exists( dirname( __FILE__ ) . $file ) || !filesize( dirname( __FILE__ ) . $file ) )
			return;
		
		return wp_enqueue_script( self::$parent->slug, plugins_url( $file, __FILE__ ), array( 'jquery' ), filemtime( dirname( __FILE__ ) . $file ), true );
				
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
	
		//allow child plugins to control when css is enqueued
		if ( !self::$parent->api->apply_filters( 'enqueue_css', true, $name ) )
			return;
			
		$file = "/css/$name.css";
		
		//verify file exists and isn't empty
		if ( !file_exists( dirname( __FILE__ ) . $file ) || !filesize( dirname( __FILE__ ) . $file ) )
			return;
		
		return wp_enqueue_style( self::$parent->slug, plugins_url( $file, __FILE__ ), null, filemtime( dirname( __FILE__ ) . $file ) );

	}
	
}