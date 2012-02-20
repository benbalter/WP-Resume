<?php
/**
 * Provides interface to store and retrieve plugin and user options
 * @author Benjamin J. Balter <ben@balter.com>
 * @package Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate_Options
 */
class Plugin_Boilerplate_Options_v_1 {

	//default scope for options when called directly,
	//choices: site, user, or global (user option across sites)
	public $scope         = 'site';
	public $defaults      = array();
	public $user_defaults = array();
	private $parent;

	/**
	 * Stores parent class as static
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		add_action( 'admin_init', array( &$this, 'options_init' ) );
		add_action( $this->parent->prefix . 'options', array( &$this, 'default_options_filter' ), 20 );
		
	}


	/**
	 * Tells WP that we're using a custom settings field
	 */
	function options_init() {

		if ( empty( $this->parent->options->defaults ) )
			return;

		register_setting( $this->parent->slug_, $this->parent->slug_, array( &$this, 'validate' ) );

	}


	/**
	 * Runs options through filter prior to saving
	 * @param array $options the options array
	 * @return array sanitized options array
	 */
	function validate( $options ) {

		return $this->parent->api->apply_filters( 'options_validate', $options );

	}


	/**
	 * Allows overloading to get option value
	 * Usage: $value = $object->{option name}
	 * @param string $name the option name
	 * @return mixed the option value
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
	 * @param string $name unique option key
	 * @param mixed $value the value to store
	 * @return bool success/fail
	 */
	function __set( $name, $value ) {

		$global = ( $this->scope == 'global' );

		if ( $this->scope == 'site' )
			return $this->set_option( $name, $value );
		else
			return $this->set_user_option( $name, $value, $global );

	}


	/**
	 * Gets a user's stored options
	 * @param int $user (optional) the ID of the user to retrieve options for
	 * @return mixed the options
	 */
	function get_user_options( $user = null ) {

		if ( $user == null )
			$user = get_current_user_id();

		if ( !$options = $this->parent->cache->get( "{$user}_options" ) ) {
			$options = (array) get_user_option( $this->parent->slug_, $user );
			$options = wp_parse_args( $options, $this->user_defaults );
			$this->parent->cache->set( "{$user}_options", $options );
		}

		return $this->parent->api->apply_filters( 'user_options', $options, $user );

	}


	/**
	 * Gets a user's stored option
	 * @param string $option the option to retrieve
	 * @param int $user (optional) the ID of the user to retrieve option for
	 * @return mixed the option
	 */
	function get_user_option( $option, $user = null ) {
		$options = $this->get_user_options( $user );
		$value = ( isset( $options[ $option ] ) ) ? $options[ $option ] : false;
		return $this->parent->api->apply_filters( $option, $value );
	}


	/**
	 * Sets a user's option
	 * @param string $key the uniqure option key
	 * @param mixed $value the value to store
	 * @param int $user (optional) the user's ID
	 * @return bool success/fail
	 */
	function set_user_option( $key, $value, $user = null ) {
		$options = array( $key => $value );
		$this->set_options( $options );
		return $this->set_user_options( $options, $user );
	}


	/**
	 * Sets all user options
	 * @param array $options the user options
	 * @param int $user (optional) the user's ID
	 * @param bool $global (optional) whether the option should be global or site specific
	 * @param bool $merge (optional) whether to overwrite or merge with existing options
	 * @return bool success/fail
	 */
	function set_user_options( $options, $user = null, $global = false, $merge = true) {

		if ( $user == null )
			$user = get_current_user_id();

		if ( $merge ) {
			$defaults = $this->get_user_options( $user );
			$options = wp_parse_args( $options, $defaults );
		}

		$this->parent->cache->set( "{$user}_options", $options );

		return update_user_option( $user, $this->parent->slug_, $options, $global );
	}


	/**
	 * Retreive the options array
	 * @return array the options
	 */
	function get_options( ) {

		if ( !$options = $this->parent->cache->get( 'options' ) ) {
			$options = get_option( $this->parent->slug_ );
			$this->parent->cache->set( 'options', $options );
		}

		return $this->parent->api->apply_filters( 'options', $options );

	}

	/**
	 * If any options are not set, merge with defaults
	 * @param array $options the saved options
	 * @return array the merged options with defaults
	 */
	function default_options_filter( $options ) {

		$this->defaults[ 'db_version' ] = $this->parent->version;
		$options = wp_parse_args( $options, $this->defaults );
		$this->parent->cache->set( 'options', $options );
		return $options;

	}


	/**
	 * Retreives a specific option
	 * @param string $option the unique option key
	 * @return mixed the value
	 */
	function get_option( $option ) {
		$options = $this->get_options( );
		$value = ( isset( $options[ $option ] ) ) ? $options[ $option ] : false;
		return $this->parent->api->apply_filters( $option, $value );
	}


	/**
	 * Sets a specific option
	 * @param string $key the unique option key
	 * @param mixed $value the value
	 * @return bool success/fail
	 */
	function set_option( $key, $value ) {
		$options = array( $key => $value );
		$this->set_options( $options );
	}


	/**
	 * Sets all plugin options
	 * @param array $options the options array
	 * @param unknown $merge (optional)
	 * @return bool success/fail
	 */
	function set_options( $options, $merge = true ) {

		if ( $merge ) {
			$defaults = $this->parent->options->get_options();
			$options = wp_parse_args( $options, $defaults );
		}

		$this->parent->cache->set( 'options', $options );

		return update_option( $this->parent->slug_, $options );

	}


}