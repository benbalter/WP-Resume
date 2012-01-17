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

	public $name = 'Hello Dolly 2.0'; //human-readable plugin name
	public $version = '2.0'; //plugin version
	public $slug = 'hd2'; //slug to prepend to api calls, etc.

	/**
	 * Construct the boilerplate and autoload all child classes
	 */
	function __construct() {
	
		parent::__construct();
		
	}
	
	
}

$hd2 = new Hello_Dolly2();
