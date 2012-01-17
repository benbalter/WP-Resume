<?php
/**
 * Provides interface to store and retrieve cache entries
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Cache {

	public $ttl = 3600;	
	static $parent;
	
	/**
	 * Stores parent class as static
	 */
	function __construct( $instance ) {
		
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
			
		$this->ttl = self::$parent->apply_filters( 'ttl', $this->ttl );

	}
	
	/**
	 * Retreive a cache entry
	 * usage: $value = $object->{key}
	 * @return mixed the cache value	 
	 */
	function __get( $key ) {
	
		return $this->get( $key );	
	}
	
	/**
	 * Retreive a cache entry	
	 * @param string $key unique key
	 * @return mixed the cache value
	 */
	function get( $key ) {

		return wp_cache_get( $key, self::$parent->slug );

	}
	
	/**
	 * Store a cache value
	 * usage: $object->{key} = $value
	 * @return bool success/fail
	 */
	function __set( $key, $value ) {
		
		return $this->set( $key, $value );
				
	}
	
	/**
	 * Store a cache entry	
	 * @param string $key unique key
	 * @param mixed $value the value to store
	 * @param int $ttl the cache ttl
	 * @return bool success/fail
	 */	
	function set( $key, $value, $ttl = null ) {
		
		if ( $ttl == null )
			$ttl = $this->ttl;
			
		return wp_cache_set( $key, $value, self::$parent->slug, $ttl );
		
	}
	
	/**
	 * Remove a cache entry
	 * @param string $key the unique key
	 * @return bool success/fail
	 */
 	function delete( $key ) {
 		
 		return wp_cache_delete( $key, self::$parent->slug );
 		
 	}
}