<?php
/**
 * Main Hello Dolly 2.0 Logic
 * @package Plugin_Boilerplate
 * @subpackage Hello_Dolly2
 */
 
class Hello_Dolly2_Sample_Lyrics {
	
	static $parent;
	public $lyrics;
	
	function __construct( &$instance ) {
		
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
			
		$this->lyrics = array ( 
							__( "Hello ), Dolly" ),
	    					__( "Well ), hello ), Dolly" ),
	    					__( "It's so nice to have you back where you belong" ),
	    					__( "You're lookin' swell ), Dolly" ),
	    					__( "I can tell ), Dolly" ),
	    					__( "You're still glowin' ), you're still crowin'" ),
	    					__( "You're still goin' strong" ),
	    					__( "We feel the room swayin'" ),
	    					__( "While the band's playin'" ),
	    					__( "One of your old favourite songs from way back when" ),
	    					__( "So ), take her wrap ), fellas" ),
	    					__( "Find her an empty lap ), fellas" ),
	    					__( "Dolly'll never go away again" ),
	    					__( "Hello ), Dolly" ),
	    					__( "Well ), hello ), Dolly" ),
	    					__( "It's so nice to have you back where you belong" ),
	    					__( "You're lookin' swell ), Dolly" ),
	    					__( "I can tell ), Dolly" ),
	    					__( "You're still glowin' ), you're still crowin'" ),
	    					__( "You're still goin' strong" ),
	    					__( "We feel the room swayin'" ),
	    					__( "While the band's playin'" ),
	    					__( "One of your old favourite songs from way back when" ),
	    					__( "Golly ), gee ), fellas" ),
	    					__( "Find her a vacant knee ), fellas" ),
	    					__( "Dolly'll never go away" ),
	    					__( "Dolly'll never go away" ),
	    					__( "Dolly'll never go away again" ),
	    			);
			
		self::$parent->options->defaults = &$this->lyrics;
			
		add_filter( self::$parent->slug . '_lyric', array( &$this, 'add_p_tag' ) );
		add_action( 'admin_notices', array( &$this, 'lyric' ) );
		add_action( 'admin_head', array( &$this, 'css' ) );
		add_action( 'admin_init', array( &$this, 'js_lyrics' ) );
		
	}
	
	function get_lyrics() {
		$lyrics = self::$parent->options->lyrics;
		array_walk( &$lyrics, 'wptexturize' );
		return $lyrics;
	}

	/**
	 * Returns a single lyric
	 * @uses hd2_lyric filter
	 */
	function get_lyric() {

		$lyrics = $this->get_lyrics();
		$lyric = $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ];
		return self::$parent->api->apply_filters( 'lyric', $lyric );
		
	}
	
	/**
	 * Enqueue lyrics via localize script
	 */
	function js_lyrics() {
		self::$parent->enqueue->admin_data['lyrics'] = $this->get_lyrics();
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