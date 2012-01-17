<?php
/**
 * Main Hello Dolly 2.0 Logic
 * @package Plugin_Boilerplate
 */
 
class Plugin_Boilerplate_Sample_Lyrics {
	
	static $parent;
	
	function __construct( &$instance ) {
		
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
			
		add_filter( self::$parent->slug . '_lyric', array( &$this, 'add_p_tag' ) );
		add_action( 'admin_notices', array( &$this, 'lyric' ) );
		add_action( 'admin_head', array( &$this, 'css' ) );
	}

	/**
	 * Returns a single lyric
	 * @uses hd2_lyric filter
	 */
	function get_lyric() {

		$lyrics = self::$parent->options->lyrics;
		$lyric = wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
		return self::$parent->api->apply_filters( 'lyric', $lyric );
		
	}
	
	/**
	 * Echos a lyric
	 */
	function lyric() {
		echo $this->get_lyric();
	}
	
	/**
	 * Filter to wrap lyric in <p> tags
	 */
	function add_p_tag( $lyric ) { 
		return self::$parent->template->get( 'sample-header', compact( 'lyric' ) );
	}
	
	/**
	 * Inject CSS into admin header
	 */
	function css() {
		self::$parent->template->load( 'sample-css' );
	}
		
}