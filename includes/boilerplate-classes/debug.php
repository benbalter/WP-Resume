<?php
/**
 * Provides interface for debugging variables
 *
 * @author Benjamin J. Balter <ben@balter.com>
 * @package Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate_Debug
 */

class Plugin_Boilerplate_Debug_v_1 {

	public $history = array();
	private $parent;

	/**
	 * Register with WordPress API on construct
	 * @param class $parent the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = $parent;

		add_action( 'init', array( &$this, 'init' ), 5 );

	}


	/**
	 * Check user cap and WP_DEBUG on init to see if class should continue loading
	 */
	function init() {

		if ( !current_user_can( 'manage_options' ) || !WP_DEBUG )
			return;

		add_filter('debug_bar_panels', array( &$this, 'init_panel' ) );
		add_filter('debug_bar_panels',  array( &$this, 'register_panel' ), 20 );

	}


	/**
	 * Debugs a variable
	 * Only visible to admins if WP_DEBUG is on
	 * @param mixed $var the var to debug
	 * @param bool $die (optional) whether to die after outputting
	 * @param string $function (optional) the function to call, usually either print_r or var_dump, but can be anything
	 * @param unknown $output (optional)
	 * @return unknown
	 */
	function debug( $var, $die = false, $function = 'var_dump', $output = 'screen' ) {

		if ( !current_user_can( 'manage_options' ) || !WP_DEBUG )
			return;

		ob_start();
		echo "<!-- BEGIN DEBUG OUTPUT --><PRE>\n";
		call_user_func( $function, $var );
		echo "\n</PRE><!-- END DEBUG OUTPUT -->\n";

		if ( $die )
			die();

		$debug = ob_get_clean();

		if ( $output == 'screen' )
			echo $debug;

		$this->history[] = $debug;

		//allow this to be used as a filter
		return $var;

	}


	/**
	 * Shorthand function to log to debug bar directly (skipping screen)
	 * @param mixed $var the var
	 * @param string $function (optional) the function to run
	 * @return mixed the original var (to use as a filter)
	 */
	function log( $var, $function = 'var_dump' ) {
		return $this->debug( $var, false, $function, false );
	}


	/**
	 * Registers panel with debug bar
	 * @param array $panels default panels
	 * @return array the modified panels
	 */
	function register_panel( $panels ) {
		$slug = $this->parent->slug_;
		$class = "{$slug}_Debug_Panel";
		$panels[] = new $class( $this->parent->name . ' Debug', $this );

		return $panels;

	}


	/**
	 * Renders history for debug bar panel
	 */
	function render() {
		foreach ( $this->history as $debug )
			echo "<pre>$debug</pre>";
	}


	/**
	 * Because you can't declare a class within a class, create an anonymous function to extend Debug_Bar_Panel
	 * @param array $panels the default panels
	 * @return array passback the original panels
	 */
	function init_panel( $panels ) {

		$code = 'class ' . $this->parent->slug_ . '_Debug_Panel extends Debug_Bar_Panel {
			static $parent;

			function __construct( $name, $instance ) {
				$this->parent = $instance;
				parent::__construct( $name );
			}

			function render() {
				$this->parent->render();
			}

			function prerender() {
				if ( empty( $this->parent->history ) )
					$this->set_visible( false );
			}

		}';

		$init = create_function( '', $code );
		$init( );

		return $panels;

	}


}