<?php
/**
 * Functions to format plaintext output
 *
 * @author Benjamin J. Balter <ben@balter.com>
 * @package WP_Resume
 */
class WP_Resume_Plaintext {

	private $parent;

	/**
	 * Register Init Hook
	 * @param $class $parent (reference) parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		add_action( 'init', array( &$this, 'init' ) );
	}


	/**
	 * Check if this is a feed, if not, don't register hooks
	 * note: is_feed() can't be run until init
	 */
	function init() {

		if ( !is_feed() )
			return;

		add_filter( 'resume_plaintext_content', array( &$this, 'bulletit'), 5 );
		add_filter( 'resume_plaintext_content', 'wp_filter_nohtml_kses' );
		add_filter( 'resume_plaintext_content', 'stripslashes' );
		add_filter( 'resume_plaintext_content', array( &$this, 'html_entity_decode' ) );
		add_filter( 'resume_plaintext_title', array( &$this, 'html_entity_decode' ) );
		add_filter( 'resume_plaintext_title', 'stripslashes' );
		add_filter( 'resume_plaintext_location', array( &$this, 'format_location' ) );
		add_filter( 'resume_plaintext_location', array( &$this, 'html_entity_decode' ) );
		add_filter( 'resume_plaintext_date', array( &$this, 'html_entity_decode' ) );
		add_filter( 'resume_plaintext_date', 'wp_filter_nohtml_kses' );
		add_filter( 'resume_plaintext_date', array( &$this, 'format_date_plaintext' ) );

	}


	/**
	 * Filters HTML from contact info array recursively
	 * @uses plaintext_contact_info_walker
	 * @param unknown $author (optional)
	 * @return unknown
	 */
	function contact_info( $author = null ) {

		$author = $this->parent->get_author( $author );
		$contact_info = $this->parent->options->get_user_option( 'contact_info', $author );

		array_walk_recursive( &$contact_info, array( &$this, 'contact_info_walker' ) );

		$contact_info = $this->parent->api->apply_filters( 'plaintext_contact_info', $contact_info );

		return $contact_info;

	}


	/**
	 * Helper function to parse contact info array from HTML to plaintext
	 * @param unknown $info (reference)
	 */
	function contact_info_walker( &$info ) {
		$info = wp_filter_nohtml_kses( $info );
	}


	/**
	 * Converts LIs to bullets
	 * @uses resume_plaintext_bullet
	 * @param string $text the HTML formatted text
	 * @return string plaintext with bullets
	 */
	function bulletit( $text ) {
		$bullet = $this->parent->api->apply_filters( 'plaintext_bullet', '&bull; ' );
		return preg_replace( "#<li[^>]*>#", $bullet, $text );
	}


	/**
	 * Wraps date in parenthesis where appropriate
	 * @param string $date the date
	 * @return string the formatted date
	 */
	function format_date_plaintext( $date ) {

		if ( strlen( trim( $date ) ) > 0 )
			return " ($date)";

		return $date;

	}


	/**
	 * Converts HTML entities, and passes proper charset
	 * @param strint $text the text
	 * @return string plaintext
	 */
	function html_entity_decode( $text ) {
		return html_entity_decode( $text, null, get_bloginfo('charset') );
	}


	/**
	 * Prepends dash to location when appropriate
	 * @param string $location the location
	 * @return string the formatted location
	 */
	function format_location( $location ) {

		if ( strlen( trim( $location ) ) == 0 )
			return '';

		return " &ndash; $location";

	}


}