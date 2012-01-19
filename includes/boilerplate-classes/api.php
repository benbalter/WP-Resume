<?php

class Plugin_Boilerplate_Api {
	
	static $parent;
	
	function __construct( $instance ) {
	
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;

	}
	
	/**
	 * Prepends prefix to action and calls standard do_action function
	 * @param string $name the name of the action
	 */
	function do_action( $name ) {
		
		$args = func_get_args();
		array_unshift( $args, 'action' );
		
		call_user_func_array( array( &$this, 'api'), $args );
	
	}
	
	/**
	 * Prepends prefix to action and calls standard apply_filters function
	 * @param string $name the name of the action
	 * @return the result of the filter
	 */	
	function apply_filters( $name ) {

		$args = func_get_args();
		array_unshift( $args, 'filter' );

		return call_user_func_array( array( &$this, 'api'), $args );	
	}
	
	/**
	 * Prepends prefix to do_action and apply_filters calls
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
		$prefix = self::$parent->prefix;
		$args[0] = $prefix . $name;
		
		return call_user_func_array( $function, $args );

	}
	
}