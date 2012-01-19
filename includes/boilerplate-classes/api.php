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
	 * Provides mechanism to deprecate action hooks
	 * @param string $replacement the proper hook to use
	 * @param int $version the version the hook was deprecated
	 * @param string $name the name of the hook called
	 * @uses do_action
	 */
	function do_deprecated_action ( $replacement, $version, $name ) {
	
		_doing_it_wrong( $name, sprintf( __( '%1$s of %2$s' ), $version, $self::$parent->name ), sprintf( __( 'Use the action hook "%s" instead'), self::$parent->prefix . "_$replacement" ) );
		
		//remove replacement and version arguments
		$args = array_slice( func_get_args(), 2 );
		
		call_user_func_array( array( &$this, 'do_action'), $args );
		
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
	 * Provides mechanism to deprecate filters
	 * @param string $replacement the proper hook to use
	 * @param int $version the version the filter was deprecated
	 * @param string $name the name of the filter called
	 * @uses apply_filters
	 */	
	function apply_deprecated_filters( $replacement, $version, $name ) {	
	
		_doing_it_wrong( $name, sprintf( __( '%1$s of %2$s'), $version, $self::$parent->name ), sprintf( __( 'Use the filter "%s" instead'), self::$parent->prefix . "_$replacement" ) );	
		
		//remove replacement and version arguments
		$args = array_slice( func_get_args(), 2 );

		call_user_func_array( array( &$this, 'apply_filters'), $args );
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