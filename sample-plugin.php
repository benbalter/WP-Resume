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
		
		$this->options->defaults = &$this->lyrics;

	}
	
	
}

$hd2 = new Hello_Dolly2();
