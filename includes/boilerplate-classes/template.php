<?php

class Plugin_Boilerplate_Template {

	static $parent;
	public $directory = '../../templates/'; //relative path to templates folder
	
	/**
	 * Store parent and init template directory
	 */
	function __construct( $instance ) {

		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
			
	}
	
	/**
	 * Allow templates to be loaded in the form of $object->templates->{template_name}();
	 */
	function __call( $template, $args = null ) {
		return $this->load( $template, $args );
	}
	
	/**
	 * Includes a template
	 * @param string $template the name of the template
	 * @param array $args arguments to extract in the template's scope
	 * 
	 * To pass arguments to the template, pass an array or compact
	 *
	 */
	function load( $template, $args = null ) {

		if ( is_array( $args ) )
			extract( $args );
		
		$template = self::$parent->api->apply_filters( 'template', $template );
		
		$cwd = getcwd();
		chdir( dirname( __FILE__ ) );
		$file = realpath( $this->directory . $template . '.php' );
		chdir( $cwd );

		if ( !file_exists( $file ) ) {
			trigger_error( "{self::$parent->name} -- cannot locate template $file" );
			return false;	
		}
			
		self::$parent->debug->log( $file );
		include( $file );
		
		return true;
		
	}
	
	/**
	 * Returns a template as a string
	 * @param string $template the name of the template
	 * @param array $args arguments to extract in the template's scope
	 * 
	 * To pass arguments to the template, pass an array or compact
	 */
	function get( $template, $args = null ) {
		
		ob_start();
		
		$this->load( $template, $args );
		
		return ob_get_clean();
		
	}

}