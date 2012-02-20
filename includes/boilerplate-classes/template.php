<?php
/**
 * Provides Templating Interface for plugins
 *
 * @author Benjamin J. Balter <ben@balter.com>
 * @package Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate_Template
 */

class Plugin_Boilerplate_Template_v_1 {

	private $parent;
	public $path      = '/templates/'; //path to templates folder relative to plugin root
	public $overrides = array(); //array of template slugs to allow themes to override by default

	/**
	 * Store parent and init template directory
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

	}


	/**
	 * Allow templates to be loaded in the form of $object->templates->{template_name}();
	 * @param string $template the template to load
	 * @param array $args (optional) an array of arguments to pass, e.g., via compact
	 * @return bool success/fail
	 */
	function __call( $template, $args = array() ) {

		if ( !empty( $args ) )
			$args = $args[0];

		return $this->load( $template, $args );
	}


	/**
	 * Includes a template
	 *
	 * To pass arguments to the template, pass an array or compact
	 *
	 * Variables in this function are prefixed to avoid collision
	 *
	 * @param string $_pb_template the template to load
	 * @param array $args (optional) arguments to extract in the template's scope
	 * @return bool success/fail
	 */
	function load( $_pb_template, $args = null ) {

		if ( is_array( $args ) )
			extract( $args );

		$_pb_template = $this->parent->api->apply_filters( 'template', $_pb_template );

		$_pb_file = false;

		//if in overrides or set by filter look in child then parent folder for the file
		//before looking in plugin's template folder
		//note: by default, this functionality is disabled
		if (  in_array( $_pb_template, $this->overrides )  ||
			$this->parent->api->apply_filters( 'allow_template_override', false, $_pb_template ) )
			$_pb_file = locate_template( $_pb_template );

		if ( !$_pb_file )
			$_pb_file = $this->parent->directory . $this->path . $_pb_template . '.php';

		if ( !file_exists( $_pb_file ) ) {
			$backtrace = debug_backtrace();
			trigger_error( $this->parent->name . " -- cannot locate template $_pb_file called on line {$backtrace[1]['line']} of {$backtrace[1]['file']}" );
			return false;
		}

		include $_pb_file;

		return true;

	}


	/**
	 * Returns a template as a string
	 *
	 * To pass arguments to the template, pass an array or compact
	 * @param string $template the name of the template
	 * @param array $args (optional) arguments to extract in the template's scope
	 * @return string the template's contents
	 */
	function get( $template, $args = null ) {

		ob_start();

		$this->parent->template->load( $template, $args );

		return ob_get_clean();

	}


}