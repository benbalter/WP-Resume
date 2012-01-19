<?php
/*
Plugin Name: WP Resume
Plugin URI: http://ben.balter.com/2010/09/12/wordpress-resume-plugin/
Description: Out-of-the-box plugin which utilizes custom post types and taxonomies to add a snazzy resume to your personal blog or Web site. 
Version: 3.0
Author: Benjamin J. Balter
Author URI: http://ben.balter.com/
License: GPL2
*/

/**
 * @author Benjamin J. Balter
 * @shoutout Andrew Nacin (http://andrewnacin.com) for help with CPTs
 * @shoutout Andrew Norcross (http://andrewnorcross.com) for the drag-and-drop CSS
 * @shoutout Rvencu for help with WPML and multi-user prototyping
 * @shoutout Rodolfo Buaiz (http://www.brasofilo.com) for the translation help
 */

require_once( dirname( __FILE__ ) . '/includes/class.plugin-boilerplate.php' );

class WP_Resume extends Plugin_Boilerplate {

	//plugin boilerplate config
	public $name = 'WP Resume';
	public $slug = 'wp-resume';
	public $slug_ = 'wp_resume';
	public $prefix = 'wp_resume_';
	public $version = '3.0';

	static $instance;
	public $author = '';
	public $query_obj;

	function __construct() {
		
		self::$instance = &$this;
		parent::__construct(); 
						
		//cpt and CT
		add_action( 'init', array( &$this, 'register_cpt_and_t' ) );
		
		//frontend printstyles
		add_action( 'wp_print_styles', array( &$this, 'enqueue_styles' ) );

		//admin bar
		add_action( 'admin_bar_menu', array( &$this, 'admin_bar' ), 100 );

		//shortcode
		add_shortcode('wp_resume', array( &$this, 'shortcode' ) );
		
		//rewrites and redirects
		add_action( 'template_redirect',array( &$this, 'add_feeds' ) );
		add_action( 'init', array( &$this, 'rewrite_rules' ) );
		add_action( 'post_type_link', array( &$this, 'permalink' ), 10, 4 );
	
		//i18n
		add_filter( 'list_terms_exclusions', array( &$this, 'exclude_the_terms' ) );
		
		add_action( 'plugins_loaded', array( &$this, 'init' ) );

	}

	function init() { 

		//default fields and values
		$this->options->defaults = array(	'fix_ie' => true, 
											'rewrite' => false, 
											'hide-title' => false 
										);
		
		$this->options->user_defaults = array(	'name'=> '', 
												'summary' => '', 
												'contact_info'=> array(), 
												'order'=>array(), 
												'hide-donate' => false 
											);
		//user overridable templates								
		$this->template->overrides = array( 'resume', 'resume-text', 'resume-json' );

	}
	 
	/**
	 * Registers the "resume block" custom post type and the the section and organization custom taxonomy
	 * @since 1.0a
	 */
	function register_cpt_and_t() {
	  	
	  	$options = $this->options->get_options();
	  		
	  	//Custom post type labels array
	  	$labels = array(
	  		'name' => _x('Positions', 'post type general name', 'wp-resume'),
	  		'singular_name' => _x('Resume Position', 'post type singular name', 'wp-resume'),
	  		'add_new' => __('Add New Position', 'wp-resume'),
	  		'add_new_item' => __('Add New Position', 'wp-resume'),
	  		'edit_item' => __('Edit Position', 'wp-resume'),
	  		'new_item' => __('New Position', 'wp-resume'),
	  		'view_item' => __('View Position', 'wp-resume'),
	  		'search_items' => __('Search Positions', 'wp-resume'),
	  		'not_found' =>  __('No Positions Found', 'wp-resume'),
	  		'not_found_in_trash' => __('No Positions Found in Trash', 'wp-resume'),
	  		'parent_item_colon' => '',
	  		'menu_name' => __('Resume', 'wp-resume' ),
	  		'all_items' => __('All Positions', 'wp-resume'),
	  	);
	  
		//Custom post type settings array
		$args = array(
		  'labels' => $labels,
		  'public' => true,
		  'publicly_queryable' => true,
		  'show_ui' => true, 
		  'menu_icon' => plugins_url( '/img/menu-icon.png', __FILE__ ),
		  'query_var' => true,
		  'rewrite' => ( isset( $options['rewrite'] ) && $options['rewrite'] ),
		  'capability_type' => 'post',
		  'hierarchical' => false,
		  'menu_position' => null,
		  'register_meta_box_cb' => array( &$this->admin, 'meta_callback' ),
		  'supports' => array( 'title', 'editor', 'revisions', 'custom-fields', 'page-attributes', 'author'),
		  'taxonomies' => array('wp_resume_section', 'wp_resume_organization'),
		); 
		
		$args = $this->api->apply_filters( 'cpt', $args );
		
		//Register the "wp_resume_position" custom post type
		register_post_type( 'wp_resume_position', $args );
		
		//Section labels array
		$labels = array(
		   'name' => _x( 'Sections', 'taxonomy general name', 'wp-resume' ),
		   'singular_name' => _x( 'Section', 'taxonomy singular name', 'wp-resume' ),
		   'search_items' =>  __( 'Search Sections', 'wp-resume' ),
		   'all_items' => __( 'All Sections', 'wp-resume' ),
		   'parent_item' => __( 'Parent Section', 'wp-resume' ),
		   'parent_item_colon' => __( 'Parent Section:', 'wp-resume' ),
		   'edit_item' => __( 'Edit Section', 'wp-resume' ), 
		   'update_item' => __( 'Update Section', 'wp-resume' ),
		   'add_new_item' => __( 'Add New Section', 'wp-resume' ),
		   'new_item_name' => __( 'New Section Name', 'wp-resume' ),
		 ); 	
		 
		$args = $this->api->apply_filters( 'section_ct', array( 'hierarchical' => true, 'labels' => $labels,  'query_var' => true, 'rewrite' => ( isset( $options['rewrite'] ) && $options['rewrite'] ) ? array( 'slug' => 'sections' ) : false ) ); 
		 
		//Register section taxonomy	
		register_taxonomy( 'wp_resume_section', 'wp_resume_position', $args );
		
		//orgs labels array
		$labels = array(
		   'name' => _x( 'Organizations', 'taxonomy general name', 'wp-resume' ),
		   'singular_name' => _x( 'Organization', 'taxonomy singular name', 'wp-resume' ),
		   'search_items' =>  __( 'Search Organizations', 'wp-resume' ),
		   'all_items' => __( 'All Organizations', 'wp-resume' ),
		   'parent_item' => __( 'Parent Organization', 'wp-resume' ),
		   'parent_item_colon' => __( 'Parent Organization:', 'wp-resume' ),
		   'edit_item' => __( 'Edit Organization', 'wp-resume' ), 
		   'update_item' => __( 'Update Organization', 'wp-resume' ),
		   'add_new_item' => __( 'Add New Organization', 'wp-resume' ),
		   'new_item_name' => __( 'New Organization Name', 'wp-resume' ),
		 ); 
		 
		$args = $this->api->apply_filters( 'organization_ct', array( 'hierarchical' => true, 'labels' => $labels,  'query_var' => true, 'rewrite' => ( isset( $options['rewrite'] ) && $options['rewrite'] ) ? array( 'slug' => 'organizations' ) : false ) );
		 
		//Register organization taxonomy
		register_taxonomy( 'wp_resume_organization', 'wp_resume_position', $args );
		
		$i = 0;	
		foreach ( $this->get_sections( false ) as $section)
				$this->options->user_defaults['order'][$section->term_id] = $i++;
		
	}
	
	/**
	 * Depricated for consistency
	 */
	function format_date( $ID ) {
		_deprecated_function( __FUNCTION__, '2.0.4 of WP Resume', 'get_date' );
		return $this->get_date( $ID );
	}

	/**
	 * Takes the section term taxonomy and re-keys it to the user specified order
	 * @returns array of term objects in user-specified order
	 * @since 1.0a
	 */
	function get_sections( $hide_empty = true, $author = '' ) {

		//init array
		$output = array();
		
		//set default author
		if ($author == '') {
			$user = wp_get_current_user();
			$author = $user->user_nicename;
		}
		
		if ( is_int( $author ) ) {
			$user = get_userdata( $author );
			$author = $user->user_nicename;
		}
		
		$cache_slug = $author . '_sections';
		if ( $hide_empty )
			$cache_slug .= '_hide_empty';
			
		if ( $cache = $this->cache->get( $cache_slug ) )
			return $cache;

		//get all sections ordered by term_id (order added)
		$sections = get_terms( 'wp_resume_section', array('hide_empty' => $hide_empty ) );
		
		//get the plugin options array to pull the user-specified order
		$options = $this->options->get_options();
		
		//pull out the order array
		$user_options = $this->options->get_user_options( $author );
		
		//user has not specified any sections, prevents errors on initial activation
		if ( !isset( $user_options['order'] ) )
			return $this->api->apply_filters( 'sections', $sections );

		$section_order = $user_options['order'];
			
		//Loop through each section
		foreach( $sections as $ID => $section ) { 
			
			//if the term is in our order array
			if ( is_array($section_order) && array_key_exists( $section->term_id, $section_order ) ) { 
			
				//push the term object into the output array keyed to it's order
				$output[ $section_order[$section->term_id] ] = $section;
			
				//pull the term out of the original array
				unset($sections[$ID]);
			
			}
		}
		
		//for those terms that we did not have a user-specified order, stick them at the end of the output array
		foreach($sections as $section) $output[] = $section;
		
		//sort by key
		ksort($output);

		$output = $this->api->apply_filters( 'sections', $output);
					
		$this->cache->set( $cache_slug, $output );
						
		//return the new array keyed to order
		return $output;
		
	}

	/**
	 * Queries for all the resume blocks within a given section
	 * @params string $section section slug
	 * @returns array array of post objects
	 * @since 1.0a
	 */
	function query( $section, $author = '' ) {
		
		//if the author isn't passed as a function arg, see if it has been set by the shortcode
		if ( $author == '' && isset( $this->author ) )
			$author = $this->author;
								
		//build our query
		$args = array(
			'post_type' => 'wp_resume_position',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'nopaging' => true,
			'wp_resume_section' => $section,
		);
		
		if ( is_int( $author ) )
			$args['author'] = $author;		
		else
			$args['author_name'] = $author;

		
		$args = $this->api->apply_filters( 'query_args', $args);
			
		//query and return
		$this->query_obj = new wp_query($args);
		return $this->query_obj;
		
	}

	/**
	 * Retrieves the org associated with a given position
	 * @since 1.1a
	 */
	function get_org( $postID ) {

		if ( $cache = $this->cache->get( $postID . '_organization' ) )
			return $cache;
		
		$organization = wp_get_object_terms( $postID, 'wp_resume_organization' );

		if ( is_wp_error( $organization ) || !isset( $organization[0] ) ) 
			return false;
		
		$org = $this->api->apply_deprecated_filters( 'organization', '3.0', 'resume_organization', $organization[0] );
		$org = $this->api->apply_filters( 'organization', $organization[0] );
		
		$this->cache->set( $postID . '_organization', $org );
		
		return $org;
		
	}
	
	/**
	 * Retrieves an organization's link, if any
	 * @param int $org the org ID
	 * @return string the org link
	 */
	function get_org_link( $org ) {
		
		$slug = 'wp_resume_organization_link_' . (int) $org;
		$link = $cache = $this->cache->get( $slug );
		
		if ( !$link ) {
			$link = $this->options->get_option( $slug );
			$this->cache->set( $slug, $link );
		}
			
		$link = $this->api->apply_filters( 'organization_link', $link, $org );
		
		return $link;
	}
	
	/**
	 * Stores an organization's link
	 * @param int $org the org ID
	 * @return bool success/fail
	 */
	function set_org_link( $org, $link ) {
		$slug = 'wp_resume_organization_link_' . (int) $org;
		$this->cache->set( $slug, $link );
		return update_option( $slug, esc_url( $link ) );
	}
	
	/**
	 * Flushes all wp-resume data from the object cache, if it exists
	 */ 
	function flush_cache() {
		global $wp_object_cache;
		unset( $wp_object_cache->cache['wp_resume']);
	}
	
	/**
	 * Loops through all posts in a given query to determine if any contain the resume shortcode
	 * @returns bool true if found, otherwise false
	 */
	function resume_in_query() {
	
		global $wp_query;

		if ( $cache = $this->cache->get( 'query_' . $wp_query->query_vars_hash) )
			return $cache;
				
		$enqueue = false;
		while ( have_posts() ): the_post();
		
			global $post;
			
			//if post is a position, we should load CSS
			if ( $post->post_type == 'position' )
				$enqueue = true;
				
			//post is a post/page, but has shortcode, so load CSS
			else if ( preg_match( '/\[wp_resume([^\]]*)]/i', get_the_content() ) != 0)
				$enqueue = true;
			
		endwhile;
		
		wp_reset_query();
		
		$this->cache->set( 'query_' . $wp_query->query_vars_hash, $enqueue );
				
		return $enqueue;
		
	}
	
	/**
	 * Adds links to the admin bar
	 * @since 2.0.3
	 */
	function admin_bar() {	
		global $wp_admin_bar;
		
	    if ( !is_admin_bar_showing() )
    	  return;
    	  
    	if ( !is_single() && !is_page() )
    		return;
		
  		if ( !$this->resume_in_query() )
  			return;
  			
  		global $post;
  		
  		if ( $post->post_author != get_current_user_id() && !current_user_can( 'edit_others_posts' ) )
  			return;
    
		$wp_admin_bar->add_menu( array( 
				'id' => 'wp-resume', 
				'title' => __( 'Edit Resume', 'wp-resume' ), 
				'href' => admin_url('edit.php?post_type=wp_resume_position'),
			) );
		
		$wp_admin_bar->add_menu( array( 
				'parent' => 'wp-resume', 
				'id' => 'wp-resume-options', 
				'title' => __( 'Resume Options', 'wp-resume' ), 
				'href' => admin_url( 'edit.php?post_type=wp_resume_position&page=wp_resume_options' ), 
			) );
		
	}

	/**
	 * Adds custom CSS to WP's queue
	 * Checks to see if file 'resume-style.css' exists in the current template directory, otherwise includes default
	 * @since 1.0a
	 */
	function enqueue_styles() {
		
		if ( !$this->resume_in_query() )
			return;
	
		add_filter( 'post_class', array( &$this, 'add_post_class' ) );

	}
	
	/**
	 * Adds resume class to div, optionally adds class to hide the title
	 * @param array $classes the classes as originally passed
	 * @returns array $classes the modified classes array
	 */
	function add_post_class( $classes ) {
		global $post;

		if ( preg_match( '/\[wp_resume([^\]]*)]/i', get_the_content() ) == false )
			return $classes;
			
		$classes[] = 'resume';
		
		if ( $this->options->get_option( 'hide-title' ) )
			$classes[] = 'hide-title';
				
		return $classes;

	}

	/**
	 * Returns default contact fields, i18n'd and filtered
	 * @returns array contact fields
	 */
	function contact_fields() {
	
		$fields = array( 
			'email' => __('E-Mail', 'wp-resume'),
			'tel' => __('Phone', 'wp-resume'),
			'other' => __('Other', 'wp-resume'),
			'adr' => array( 
						'street-address' => __('Street Address', 'wp-resume'),
						'locality' => __('City/Locality', 'wp-resume'),
						'region' => __('State/Region', 'wp-resume'),
						'postal-code' => __('Zip/Postal Code', 'wp-resume'),
						'country-name' => __('Country', 'wp-resume'),
					),
		);
		
		$fields = $this->api->apply_filters( 'contact_fields', $fields );
		
		return $fields;
		
	}

	/**
	 * Returns either the next or previous position's org
	 * @param int $delta either 1 or -1 for forward or backward
	 * @returns bool|object false if no org, org object if exists
	 * @since 2.0.5
	 */
	function get_delta_org( $delta ) {
		
		if ( empty( $this->query_obj->posts ) || !isset( $this->query_obj->current_post ) )
			return false;
		
		$post_key = $this->query_obj->current_post + $delta;
		
		if ( !isset( $this->query_obj->posts[ $post_key ] ) )
			return false;
		
		return $this->get_org( $this->query_obj->posts[ $post_key ]->ID ); 
	}
	
	/**
	 * Peaks forward in the loop if possible, and tries to get next position's org
	 * @returns bool|object either false or the org object
	 * @since 2.0.5
	 */
	function get_next_org( ) {
	
		return $this->get_delta_org( 1 );
		  
	}
	
	/**
	 * Peaks backward in the loop if possible, and tries to get previous position's org
	 * @returns bool|object either false or the org object
	 * @since 2.0.5
	 */
	function get_previous_org() {

		return $this->get_delta_org( -1 );
  
	}
	
	/**
	 * Moves information around the database, supports back to 1.5
	 * @since 1.2
	 */
	function upgrade( $from, $to ) {
	
		//get our options
		$options = $this->options->get_options();
		
		//check to see if we have any sections, if not add the sections
		if ( sizeof( $this->get_sections( false ) ) == 0 ) {
			wp_insert_term( 'Education', 'wp_resume_section');
			wp_insert_term( 'Experience', 'wp_resume_section' );
			wp_insert_term( 'Awards', 'wp_resume_section' );
		}
			
		//add multi-user support (v. 1.6)
		if ( !isset( $options['db_version'] ) || substr( $options['db_version'], 0, 2 ) < '1.6' ) {
			
			$usermeta = array();
			$current_user = wp_get_current_user();
			
			//migrate $options[field] to (usermeta) [wp_resume][field] and kill original
			foreach ($fields['user'] as $field=>$value) {
				if ( isset( $options[$field] ) ) {
					$usermeta[$field] = $options[$field];
					unset($options[$field]);
				} 
			}

			//store usermeta to current user
			//(assumption: user upgrading is author of resume)
			update_user_meta( $current_user->ID, 'wp_resume', $usermeta );

		}
		
		//if global fields are null, set to default
		foreach ( $fields['global'] as $key=>$value ) {
			if ( !isset( $options[$key] ) )
				$options[$key] = $value;
		}
		
		//if user fields are null for any user, set to default
		global $wpdb;
		$users = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users") );
		foreach ($users as $user) {
		
			if ( isset( $options['db_version'] ) && $options['db_version'] < '2.2' ) {
				//move from user_meta to user_option
				$user_options = get_user_meta( $user, 'wp_resume', true );
				delete_user_meta( $user, 'wp_resume' );
			} else {		
				//get current options
				$user_options = $this->options->get_user_options( (int) $user );
			}
			
			//loop default fields
			foreach ( $fields['user'] as $key=>$value ) {
			
				//check they exist, if not set
				if ( !isset( $user_options[$key] ) )
					$user_options[$key] = $value;

				//update
				$this->options->set_user_options( $user, $user_options );
			}	
			
		}
			
		//store updated options
		$this->options->set_options( $options );
		
		//flush rewrite rules just in case
		flush_rewrite_rules();
	  
		return $options;
	}

	/**
	 * Includes resume template on shortcode use 
	 * @since 1.3
	 */
	function shortcode( $atts ) {
	
		//determine author and set as global so templates can read
		$this->author = $this->get_author( $atts );

		ob_start();
		$this->api->do_action( 'shortcode_pre' );

		if ( !( $resume = $this->cache-get( $this->author . '_resume' ) ) ) {
			$this->template->resume( );
			$resume = ob_get_contents();
			$this->cache->set( $this->author . '_resume', $resume );
		}
		
		$this->api->do_action( 'shortcode_post' );
		ob_end_clean();
		
		$this->api->apply_filters( 'shortcode', $resume);

		return $resume;
	}

	/**
	 * Adds feed support to the resume 
	 * @since 1.5
	 */
	function add_feeds() {
	
		global $post;
		
		//feed 404
		if ( !$post )
			return false;
		
		if ( preg_match( '/\[wp_resume([^\]]*)]/i', $post->post_content ) === FALSE) 
			return;

		add_feed('text', array( &$this, 'plain_text' ) );
		add_feed('json', array( &$this, 'json' ) );
		
		add_action('wp_head', array( &$this, 'header' ) );
	}

	/**
	 * Adds HTML5 support to header
	 */
	function header() { 
	
		if ( !is_single() )
			return;
			
		if ( !$this->resume_in_query() )
			return;

		$options = $this->options->get_options();
		$this->template->header();
		
	 }

	/**
	 * Includes the plain text template
	 * @since 1.5
	 */
	function plain_text() {
		$this->feed_get_author();
		header('Content-Type: text/plain; charset='. get_bloginfo('charset') );
		$this->template->load( 'resume-text' );
		do_action('wp_resume_plain_text');
	}

	/**
	 * Includes the json template
	 * @since 1.5
	 */
	function json() {
		$this->feed_get_author();
		header('Content-type: application/json; charset='. get_bloginfo('charset') );
		$this->template->load( 'resume-json' );
		do_action('wp_resume_json');
	}

	/**
	 * Fuzzy gets author for current resume page
	 * Looks at:
	 * 1) Attributes of shorcode (user="[username]")
	 * 2) Author of page that calls the shortcode
	 * 
	 * @param array $atts attributes passed from shortcode callback
	 * @since 1.6
	 */
	function get_author( $atts = array() ) {
		
		//if user is passed as an attribute, that's our author
		if ( isset( $atts['user'] ) ) 
			return $atts['user'];
		
		//otherwise grab the author from the post
		global $post;
		$user = get_userdata($post->post_author);
		return $user->user_nicename;
	}

	/**
	 * Injects resume rewrite rules into the rewrite array when applicable
	 */
	function rewrite_rules() {
			
		if ( !$this->options->get_option( 'rewrite' ) );
			return;

		global $wp_rewrite;
		$rw_structure = 'resume/%wp_resume_section%/%wp_resume_organization%/%wp_resume_position%/';
		add_rewrite_tag("%wp_resume_section%", '([^/]+)', "wp_resume_section=");
		add_rewrite_tag("%wp_resume_organization%", '([^/]+)', "wp_resume_organization=");
		add_rewrite_tag("%wp_resume_position%", '([^/]+)', "wp_resume_position=");
		$wp_rewrite->add_permastruct('wp_resume_position', $rw_structure);  

	}

	/**
	 * Generates permalink for a given resume position
	 */
	function permalink($link, $post, $leavename, $sample) {
		
		if ( $post->post_type != 'wp_resume_position' || $this->options->get_option( 'rewrite' ) )
			return $link;
			
		$section = wp_get_post_terms($post->ID, 'wp_resume_section');
		$org = wp_get_post_terms($post->ID, 'wp_resume_organization');
		
		$rewritecode = array(
			  '%wp_resume_section%',
			  '%wp_resume_organization%',
		);
		
		$replace = array(
			( isset( $section[0]->slug) ) ? $section[0]->slug : null,
			( isset( $org[0]->slug) ) ? $org[0]->slug : null,
		);	
		
		$link = str_replace($rewritecode, $replace, $link);
		
		$link = $this->api->apply_filters( 'permalink', $link);

		return $link;
	}

	/**
	 * Adds WPML support to wp resume sections
	 * @since 1.6
	 * @h/t rvencu 
	 */
	function exclude_the_terms($exclusions) {
		
		//check for WPML, if not, kick
		if ( !class_exists('SitePress') )
			return $exclusions;
			
		//if WPML exists,  change the $exclusions
		global $sitepress;
		$exclusions .= $sitepress->exclude_other_terms( '', array( 'taxonomy' => 'wp_resume_section' ) );

		return $exclusions;
	}

	/**
	 * Parses current author for feeds from shortcode
	 */
	function feed_get_author(){
		global $post;
		
		if ( preg_match( '/\[wp_resume user=\"([^\"]*)"]/i', $post->post_content, $matches ) == 0) {
			
			$user = get_userdata($post->post_author);
			$this->author = $user->user_nicename; 
			
		} else {
		
			$this->author = $matches[1];
		
		}
		
		$this->author = $this->api->apply_filters( 'author', $this->author );
		
		return $this->author;
		
	}
		
}

$wp_resume = new WP_Resume();