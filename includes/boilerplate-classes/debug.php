<?php 
/**
 * Provides interface for debugging variables
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Debug {

	public $history = array();
	static $parent;

	function __construct( $instance ) {
	
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;

		add_filter('debug_bar_panels', array( &$this, 'init_panel' ) );	
		add_filter('debug_bar_panels',  array( &$this, 'register_panel' ), 20 );
	
	}
	
	/**
	 * Debugs a variable
	 * Only visible to admins if WP_DEBUG is on
	 * @param mixed $var the var to debug
	 * @param bool $die whether to die after outputting
	 * @param string $function the function to call, usually either print_r or var_dump, but can be anything
	 */
	function debug( $var, $die = false, $function = 'var_dump' ) {

	    if ( !current_user_can( 'manage_options' ) || !WP_DEBUG )
	    	return;
	    
	    ob_start();
	    echo "<!-- BEGIN DEBUG OUTPUT --><PRE>\n";
	    call_user_func( $function, $var );
	    echo "\n</PRE><!-- END DEBUG OUTPUT -->\n";
	    
	    if ( $die )
	    	die();
		
		$debug = ob_get_contents();
		ob_end_flush();
		
		$this->history[] = $debug;
		
		//allow this to be used as a filter
		return $var;
		
	}
		
	/**
	 * Registers panel with debug bar
	 */	
	function register_panel( $panels ) {
		$slug = self::$parent->slug;
		$class = "{$slug}_Debug_Panel";
		$panels[] = new $class( self::$parent->name . ' Debug', &$this );
		
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
	 */
	function init_panel( $panels ) {

		$slug = self::$parent->slug;
		$code = 'class ' . $slug . '_Debug_Panel extends Debug_Bar_Panel { 
			static $parent; 
			
			function __construct( $name, &$instance ) { 
				self::$parent = &$instance; 
				parent::__construct( $name ); 
			} 
			
			function render() { 
				self::$parent->render(); 
			} 
		}';
		$init = create_function( '', $code );
		$init( );
		
		return $panels;
		
	}

}