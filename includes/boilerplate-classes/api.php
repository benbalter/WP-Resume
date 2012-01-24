<?php

class Plugin_Boilerplate_Api_v_1 {
	
	private $parent;
	public $history = array();
	
	function __construct( $parent ) {
	
		$this->parent = &$parent;

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
	 * @param string $name the name of the hook called
	 * @param int $version the version the hook was deprecated
	 * @param string $replacement the proper hook to use
	 * @uses do_action
	 */
	function do_deprecated_action ( $name, $version, $replacement ) {
	
		//there are no callbacks for this action
		if ( !has_action( $name ) )
			return false;
	
		_doing_it_wrong( $name, sprintf( __( 'Use the action hook "%s" instead.'), $this->parent->prefix . $replacement ), sprintf( __( '%1$s of %2$s' ), $version, $$this->parent->name ) );
		
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
	 * @param string $name the name of the filter called
	 * @param int $version the version the filter was deprecated
	 * @param string $replacement the proper hook to use
	 * @uses apply_filters
	 */	
	function apply_deprecated_filters( $name, $version, $replacement, $value = null ) {	
				
		//there are no callbacks for this filter
		if ( !has_filter( $name ) )
			return $value;
		
		_doing_it_wrong( $name, sprintf( __( 'Use the filter "%s" instead.'), $this->parent->prefix . $replacement ), sprintf( __( '%1$s of %2$s'), $version, $this->parent->name ) );	
		
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
		$prefix = $this->parent->prefix;
		$args[0] = $prefix . $name;
		
		if ( current_user_can( 'manage_options') && WP_DEBUG )
			$this->history[] = $args;
			
		return call_user_func_array( $function, $args );

	}
	
	/**
	 * Returns all filters fired on the given page
	 */
	function get_history() {
		return $this->history;
	}
	
}