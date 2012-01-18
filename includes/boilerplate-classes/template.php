<?php

class Plugin_Boilerplate_Template {

	static $parent;
	public $path = '/templates/'; //path to templates folder relative to plugin root
	public $overrides = array(); //array of template slugs to allow themes to override by default
	
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
		
		$file = false;
		
		//if in overrides or set by filter look in child then parent folder for the file
		//before looking in plugin's template folder
		//note: by default, this functionality is disabled
		if ( 	in_array( $template, $this->overrides ) 	||
				self::$parent->api->apply_filters( 'allow_template_override', false, $template ) )
			$file = locate_template( $template );
		
		if ( !$file )
			$file = self::$parent->directory . $this->path . $template . '.php';

		if ( !file_exists( $file ) ) {
			trigger_error( "{self::$parent->name} -- cannot locate template $file" );
			return false;	
		}
			
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