<?php
/**
 * Creates unobstrusive donation plea
 * @subpackage Plugin_Boilerplate_Donate
 * @author Benjamin J. Balter <ben@balter.com>
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Donate_v_1 {

	private $parent;
	public $activation_option = 'pb_activation';
	public $delay = 604800; // 60*60*24*7 = 1 week
	public $link = 'http://ben.balter.com/donate/'; //donation link

	/**
	 * Register with WordPress API on init
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		//add tracking codes
		$this->link = add_query_arg( 'utm_source', 'wp', $this->link );
		$this->link = add_query_arg( 'utm_medium', 'options', $this->link );
		$this->link = add_query_arg( 'utm_campaign', $this->parent->slug_, $this->link );

		add_action( 'wp_ajax_' . $this->parent->slug_ . '_hide_donate', array( &$this, 'hide') );
		add_action( 'admin_init', array( &$this, 'store_activation' ) );

	}


	/**
	 * Stores unix timestamp of plugin activation to delay activation
	 */
	function store_activation() {

		if ( $this->parent->options->get_option( $this->activation_option ) !== false )
			return;

		$this->parent->options->set_option( $this->activation_option, time() );

	}


	/**
	 * Preveents plea from displaying for set period
	 * @return bool true if plea should be delayed, false if should be shown
	 */
	function delay_plea() {

		$activated = $this->parent->options->get_option( $this->activation_option );

		if ( $activated !== false )
			$this->store_activation();

		return $this->parent->api->apply_filters( 'delay_plea', ( time() - $activated < $this->delay ) );

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