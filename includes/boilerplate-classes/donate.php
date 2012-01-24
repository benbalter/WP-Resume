<?php
/**
 * Creates unobstrusive donation plea
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Donate_v_1 {

	private $parent;
	public $link = 'http://ben.balter.com/donate/'; //donation link
		
	function __construct( $parent ) {
	
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