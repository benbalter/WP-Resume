<?php
/*
Plugin Name: Plugin Boilerplate Sample
Description: 
Version: 1.0
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

/*  Copyright 2012  Benjamin J. Balter  (email : ben@balter.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( dirname( __FILE__ ) . '/includes/class.plugin-boilerplate.php' );

class Hello_Dolly2 extends Plugin_Boilerplate {

	public $name = 'Hello Dolly 2.0';
	public $version = '2.0';
	public $slug = 'hd2';
	public $lyrics = array ( 	"Hello, Dolly",
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

	/**
	 * Register hooks with WordPress API
	 */
	function __construct() {
	
		parent::__construct(); // construct the boilerplate
		
		add_action( "{$this->slug}_upgrade", array( &$this, 'upgrade' ) );
		add_filter( "{$this->slug}_lyric", array( &$this, 'add_p_tag' ) );
		add_action( 'admin_notices', array( &$this, 'lyric' ) );
		add_action( 'admin_head', array( &$this, 'css' ) );
		
		$this->options->defaults = &$this->lyrics;

	}
	
	/**
	 * Returns a single lyric
	 * @uses hd2_lyric filter
	 */
	function get_lyric() {

		$lyrics = $this->options->lyrics;
		$lyric = wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
		return $this->apply_filters( 'lyric', $lyric );
		
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
		return "<p id='dolly'>$lyric</p>";
	}
	
	/**
	 * Inject CSS into admin header
	 */
	function css() {

		$x = is_rtl() ? 'left' : 'right';
		
		echo "
		<style type='text/css'>
		#dolly {
			float: $x;
			padding-$x: 15px;
			padding-top: 5px;		
			margin: 0;
			font-size: 11px;
		}
		</style>
		";
	}
		
}

$hd2 = new Hello_Dolly2();
