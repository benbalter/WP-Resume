<?php
/**
 * Creates unobstrusive donation plea
 * @author Benjamin J. Balter <ben@balter.com>
 * @package Plugin_Boilerplate
 * @subpackage Plugin_Boilerplate_Donate
 */
class Plugin_Boilerplate_Donate_v_1 {

	private $parent;
	public $link = 'http://ben.balter.com/donate/'; //donation link

	/**
	 * Register with WordPress API on init
	 * @param class $parent the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		add_action( 'wp_ajax_' . $this->parent->slug_ . '_hide_donate', array( &$this, 'hide') );

	}


	/**
	 * Call to conditionally render the donate form
	 */
	function form() {

		//user has asked to hide the donate message
		if ( $this->parent->ge_user_option( 'hide-donate' ) )
			return;

		//render form
		$this->parent->template->donate();
	}


	/**
	 * Stores user's preference to hide the donate message via AJAX
	 */
	function hide() {

		check_ajax_referer( $this->parent->slug_ . '_hide_donate' , '_ajax_nonce-' . $this->parent->slug . '-hide-donate' );

		$this->parent->options->set_user_option( 'hide-donate', true );

		die( 1 );

	}


}