<?php
/**
 * Main Hello Dolly 2.0 Logic
 * @package Plugin_Boilerplate
 * @subpackage Hello_Dolly2
 */
 
class Hello_Dolly2_Sample_Lyrics {
	
	static $parent;
	public $lyrics = array ( 
							"Hello, Dolly",
	    					"Well, hello, Dolly",
	    					"It's so nice to have you back where you belong",
	    					"You're lookin' swell, Dolly",
	    					"I can tell, Dolly",
	    					"You're still glowin', you're still crowin'",
	    					"You're still goin' strong",
	    					"We feel the room swayin'",
	    					"While the band's playin'",
	    					"One of your old favourite songs from way back when",
	    					"So, take her wrap, fellas",
	    					"Find her an empty lap, fellas",
	    					"Dolly'll never go away again",
	    					"Hello, Dolly",
	    					"Well, hello, Dolly",
	    					"It's so nice to have you back where you belong",
	    					"You're lookin' swell, Dolly",
	    					"I can tell, Dolly",
	    					"You're still glowin', you're still crowin'",
	    					"You're still goin' strong",
	    					"We feel the room swayin'",
	    					"While the band's playin'",
	    					"One of your old favourite songs from way back when",
	    					"Golly, gee, fellas",
	    					"Find her a vacant knee, fellas",
	    					"Dolly'll never go away",
	    					"Dolly'll never go away",
	    					"Dolly'll never go away again",
	    			);
	
	function __construct( &$instance ) {
		
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
			
		self::$parent->options->defaults = &$this->lyrics;
			
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