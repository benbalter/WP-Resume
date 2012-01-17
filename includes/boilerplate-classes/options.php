<?php
/**
 * Provides interface to store and retrieve plugin and user options
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Options {

	//default scope for options when called directly,
	//choices: site, user, or global (user option across sites)
	public $scope = 'site'; 
	public $defaults = array();
	public $user_defaults = array();
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

	}
	
	/**
	 * Allows overloading to get option value
	 * Usage: $value = $object->{option name}
	 */
	function __get( $name ) {
		
		if ( $this->scope == 'site' )
			return $this->get_option( $name );
		else 
			return $this->get_user_option( $name );

	}
	
	/**
	 * Allows overloading to set option value
	 * Usage: $object->{option name} = $value
	 */	
	function __set( $name, $value ) {
		
		$global = ( $this->scope == 'global' );
		
		if ( $this->scope == 'site' )
			return $this->set_option( $name, $value );
		else
			return $this->get_user_option( $name, $value, $global );

	}

	/**
	 * Gets a user's stored options
	 * @param int $user the ID of the user to retrieve options for
	 * @return mixed the options
	 */
	function get_user_options( $user = null ) {
	
		if ( $user == null )
			$user = get_current_user_id();
		
		if ( !$options = self::$parent->cache->get( "{$user}_options" ) ) {
			$options = (array) get_user_option( self::$parent->slug, $user );
			$options = wp_parse_args( $options, $this->user_defaults );
			self::$parent->cache->set( "{$user}_options", $options );
		}
		
		return self::$parent->api->apply_filters( 'user_options', $options, $user );

	}
	
	/**
	 * Gets a user's stored option
	 * @param string $option the option to retrieve
	 * @param int $user the ID of the user to retrieve option for
	 * @return mixed the option
	 */	
	function get_user_option( $option, $user = null ) {
		$options = $this->get_user_options( $user );
		$value = ( isset( $options[ $option ] ) ) ? $options[ $option ] : false;
		return self::$parent->api->apply_filters( $option, $value );
	}
	
	/**
	 * Sets a user's option
	 * @param string $key the uniqure option key
	 * @param mixed $value the value to store
	 * @param int $user the user's ID
	 * @return bool success/fail
	 */
	function set_user_option( $key, $value, $user = null ) {
		$options = $this->get_user_options( $user );
		$options[ $key ] = $value;
		return $this->set_user_options( $options );
	}
	
	/**
	 * Sets all user options
	 * @param array $options the user options
	 * @param int $user the user's ID
	 * @param bool $global whether the option should be global or site specific
	 * @return bool success/fail
	 */
	function set_user_options( $options, $user = null, $global = false ) {
		
		if ( $user == null )
			$user = get_current_user_id();

		self::$parent->cache->set( "{$user}_options", $options );

		return update_user_option( $user, self::$parent->slug, $options, $global );
	}
	
	/**
	 * Retreive the options array
	 * @return array the options
	 */
	function get_options( ) {
		
		if ( !$options = self::$parent->cache->get( 'options' ) ) {
			$options = (array) get_option( self::$parent->slug ); 
			$options = wp_parse_args( $options, $this->defaults );
			self::$parent->cache->set( 'options', $options );
		}
		return self::$parent->api->apply_filters( 'options', $options );
	}
	
	/**
	 * Retreives a specific option
	 * @param string $option the unique option key
	 * @return mixed the value
	 */
	function get_option( $option ) {
		$options = $this->get_options( );
		$value = ( isset( $options[ $option ] ) ) ? $options[ $option ] : false;
		return self::$parent->api->apply_filters( $option, $value );
	}
	
	/**
	 * Sets a specific option
	 * @param string $key the unique option key
	 * @param mixed $value the value
	 * @return bool success/fail
	 */
	function set_option( $key, $value ) {
		$options = $this->get_options( );
		$options[ $key ] = $value;
		return $this->set_options( $options );
	}
	
	/**
	 * Sets all plugin options
	 * @param array $options the options array
	 * @return bool success/fail
	 */
	function set_options( $options ) {

		self::$parent->cache->set( 'options', $options );

		return update_option( self::$parent->slug, $options );
		
	}

}