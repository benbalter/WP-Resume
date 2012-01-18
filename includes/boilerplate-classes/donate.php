<?php
/**
 * Creates unobstrusive donation plea
 * @package Plugin_Boilerplate
 */
class Plugin_Boilerplate_Donate {

	static $parent;
	public $link = 'http://ben.balter.com/donate/'; //donation link
		
	function __construct( &$instance ) {

		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
			
		add_action( 'wp_ajax_' . self::$parent->slug_ . '_hide_donate', array( &$this, 'hide_donate') );

	}
	
	/**
	 * Call to conditionally render the donate form
	 */
	function form() {
	
		//user has asked to hide the donate message
		if ( self::$parent->ge_user_option( 'hide-donate' ) )
			return;
		
		//render form
		self::$parent->template->donate();
	}
	
	/**
	 * Stores user's preference to hide the donate message via AJAX
	 */
	function hide() {
		
		check_ajax_referer( self::$parent->slug_ . '_hide_donate' , '_ajax_nonce-' . self::$parent->slug . '-hide-donate' );
		
		self::$parent->options->set_user_option( 'hide-donate', true );
		
		die( 1 );
		
	}
	
}