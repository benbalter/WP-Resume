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

require_once( 'class.plugin-boilerplate.php' );

class Plugin_Boilerplate_Sample extends Plugin_Boilerplate {

	public $name = 'Plugin Boilerplate Sample';
	public $version = '1.0';
	public $slug = 'pbs';

	function __construct() {
		parent::__construct(); 
	}
	
}

$pbs = new Plugin_Boilerplate_Sample();