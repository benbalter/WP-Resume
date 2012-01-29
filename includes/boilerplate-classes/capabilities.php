<?php
/**
 * Interface to allow plugins to define custom capabilities
 * @author Benjamin J. Balter <ben@balter.com>
 * @package Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate_Capabilities
 */
class Plugin_Boilerplate_Capabilities_v_1 {

	private $parent;

	/* An array of arrays to define default capabilities which can then be overridden by 3d party plugins like Members
	 *
	 * Format:
	 * 			$defaults = array(
	 *				'editor' => array(
	 *						'edit_documents' => true,
	 *						'edit_others_documents' => true,
	 *				),
	 *				'author' => array(
	 *						'edit_documents' => true,
	 *						'edit_others_documents' => false,
	 * 				),
	 *				'subscriber' =>  array(
	 *						'edit_documents' => false,
	 *						'edit_others_documents' => false,
	 *				),
	 *			);
	 */
	public $defaults = array();

	/**
	 * Register with WordPress API
	 * @param class $parent the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		add_action( 'init', array( &$this, 'add_caps' ) );

	}


	/**
	 * Adds plugin-specific caps to all roles so that 3rd party plugins can manage them
	 */
	function add_caps() {

		if ( empty( $this->defaults ) )
			return;

		global $wp_roles;
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles;

		foreach (  $wp_roles->role_names as $role=>$label ) {

			//if the role is a standard role, map the default caps, otherwise, map as a subscriber
			$caps = ( array_key_exists( $role, $this->defaults ) ) ? $this->defaults[$role] : $this->defaults['subscriber'];

			$caps = $this->parent->api->apply_filters( 'caps', $caps, $role );

			//loop and assign
			foreach ( $caps as $cap=>$grant ) {

				//check to see if the user already has this capability, if so, don't re-add as that would override grant
				if ( !isset( $wp_roles->roles[$role]['capabilities'][$cap] ) )
					$wp_roles->add_cap( $role, $cap, $grant );

			}
		}

	}


}