<?php
/**
 * Provides interface to enqueue front end and back end css and javascript files
 * @author Benjamin J. Balter <ben@balter.com>
 * @package Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate_Enqueue
 */
class Plugin_Boilerplate_Enqueue_v_1 {

	private $parent;
	public $js_path = '/js/'; //path to javascript directory relative to plugin base
	public $css_path = '/css/'; //path to css directory relative to plugin base
	public $front_end_data = array(); //array of script localication data for front-end
	public $admin_data = array(); //array of script localication data for admin

	/**
	 * Register hooks with WP API
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_js' ), 50 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_css' ), 50 );
		add_action( 'wp_print_styles', array( $this, 'enqueue_front_end_css' ), 50 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_end_js' ), 50 );

	}


	/**
	 * Enqueue scripts for admin backend, if any
	 * @return bool success/fail
	 */
	function enqueue_admin_js( ) {
		return $this->enqueue_js( 'admin' );
	}


	/**
	 * Enqueue scripts for front-end, if any
	 * @return bool success/fail
	 */
	function enqueue_front_end_js( ) {
		return $this->enqueue_js( 'front-end' );
	}


	/**
	 * Check if script exists and enqueue
	 * @param string $name the name of the script to enqueue
	 */
	function enqueue_js( $name ) {

		$directory = $this->parent->directory . $this->js_path . $name . '/';

		$i = 0;
		
		//fix in case glob returns error, see http://ben.balter.com/2010/11/29/twitter-mentions-as-comments/#comment-10226
		$files = glob( $directory . '*.js' );
		if ( !$files )
			return;
			
		foreach ( $files as $file ) {

			if ( !filesize( $file ) )
				continue;

			//when in debug mode prefer .dev.js version if it exists
			if ( ( WP_DEBUG || SCRIPT_DEBUG ) && file_exists( str_replace( '.js', '.dev.js', $file ) ) )
				continue;

			$file = basename( $file );

			//allow child plugins to control when js is enqueued
			if ( !$this->parent->api->apply_filters( 'enqueue_js', true, $file, $name ) )
				continue;

			$slug = ( $i === 0 ) ? $this->parent->slug : $this->parent->slug . "-$i";
			
			//if debugging, use filemtime, otherwise use plugin version as script version 
			$version = ( WP_DEBUG ) ? filemtime( $directory . $file ) : $this->parent->version;

			wp_enqueue_script( $slug, plugins_url( $this->js_path . $name . '/' . $file, $this->parent->directory . '/readme.txt' ), array( 'jquery' ), $version, true );

			$i++;

		}

		$data = str_replace( '-', '_', $name . '_data' );
		$this->$data = apply_filters( 'localize_script', $this->$data, $name );
		
		if ( empty( $this->$data ) )
			return;

		wp_localize_script( $this->parent->slug, $this->parent->slug_, $this->$data );

	}


	/**
	 * Enqueue stylesheets for admin backend, if any
	 * @return unknown
	 */
	function enqueue_admin_css( ) {
		return $this->enqueue_css( 'admin' );
	}


	/**
	 * Enqueue stylesheets for front-end, if any
	 * @return bool success/fail
	 */
	function enqueue_front_end_css( ) {
		return $this->enqueue_css( 'front-end' );
	}


	/**
	 * Check if stylesheet exists and enqueue
	 * @param string $name the name of the stylesheet to enqueue
	 */
	function enqueue_css( $name ) {

		$directory = $this->parent->directory . $this->css_path . $name . '/';

		//fix in case glob returns error, see http://ben.balter.com/2010/11/29/twitter-mentions-as-comments/#comment-10226
		$files = glob( $directory . '*.css' );
		if ( !$files )
			return;

		foreach ( $files as $file ) {

			if ( !filesize( $file ) )
				continue;

			$file = basename( $file );

			//allow child plugins to control when css is enqueued
			if ( !$this->parent->api->apply_filters( 'enqueue_css', true, $file, $name ) )
				continue;
				
			//if debugging, use filemtime, otherwise use plugin version as stylesheet version 
			$version = ( WP_DEBUG ) ? filemtime( $directory . $file ) : $this->parent->version;

			wp_enqueue_style( $this->parent->slug, plugins_url( $this->css_path . $name . '/' . $file , $this->parent->directory . '/readme.txt' ), null, $version );

		}

	}


}