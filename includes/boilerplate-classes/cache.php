<?php
/**
 * Provides interface to store and retrieve cache entries
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Cache_v_1 {

	public $ttl = 3600;	
	private $parent;
	
	/**
	 * Stores parent class as static
	 */
	function __construct( $parent ) {
	
		$this->parent = &$parent;
			
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

		return wp_cache_get( $key, $this->parent->slug_ );

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
			
		return wp_cache_set( $key, $value, $this->parent->slug_, $ttl );
		
	}
	
	/**
	 * Remove a cache entry
	 * @param string $key the unique key
	 * @return bool success/fail
	 */
 	function delete( $key ) {
 		
 		return wp_cache_delete( $key, $this->parent->slug_ );
 		
 	}
}