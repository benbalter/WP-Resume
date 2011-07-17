<?php
/*
Plugin Name: WP Resume
Plugin URI: http://ben.balter.com/2010/09/12/wordpress-resume-plugin/
Description: Out-of-the-box plugin which utilizes custom post types and taxonomies to add a snazzy resume to your personal blog or Web site. 
Version: 2.0
Author: Benjamin J. Balter
Author URI: http://ben.balter.com/
License: GPL2
*/

/**
 * @author Benjamin J. Balter
 * @shoutout Andrew Nacin (http://andrewnacin.com) for help with CPTs
 * @shoutout Andrew Norcross (http://andrewnorcross.com) for the drag-and-drop CSS
 * @shoutout Rvencu for help with WPML and multi-user prototyping
 */

class WP_Resume {

	static $instance;
	public $version = '2.0';
	public $author = '';

	function __construct() {
		
		self::$instance = $this;
		
		//cpt and CT
		add_action( 'init', array( &$this, 'register_cpt_and_t' ) );
		
		//ajax callbacks
		add_action('wp_ajax_add_wp_resume_section', array(&$this, 'ajax_add') );
		add_action('wp_ajax_add_wp_resume_organization', array(&$this, 'ajax_add') );
		
		//edit position screen
		add_action( 'save_post', array( &$this, 'save_wp_resume_position' ) );

		//frontend printstyles
		add_action( 'wp_print_styles', array( &$this, 'enqueue_styles' ) );

		//shortcode
		add_shortcode('wp_resume', array( &$this, 'shortcode' ) );
		
		//admin UI
		add_action( 'admin_menu', array( &$this, 'menu' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'wp_resume_organization_add_form', array( &$this, 'org_helptext' ) );
		add_action( 'admin_init', array( &$this, 'enqueue_scripts' ) );
		
		//rewrites and redirects
		add_action('template_redirect',array( &$this, 'add_feeds' ) );
		add_action('init', array( &$this, 'rewrite_rules' ) );
		add_action( 'post_type_link', array( &$this, 'permalink' ), 10, 4 );
	
		//i18n
		add_filter( 'list_terms_exclusions', array( &$this, 'exclude_the_terms' ) );

	}
	 
	/**
	 * Registers the "resume block" custom post type and the the section and organization custom taxonomy
	 * @since 1.0a
	 */
	function register_cpt_and_t() {
		
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
	  );
	  
	  //Custom post type settings array
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'register_meta_box_cb' => array( &$this, 'meta_callback' ),
		'supports' => array( 'title', 'editor', 'revisions', 'custom-fields', 'page-attributes', 'author'),
		'taxonomies' => array('wp_resume_section', 'wp_resume_organization'),
	  ); 
	  
	  //Register the "wp_resume_position" custom post type
	  register_post_type( 'wp_resume_position', $args );
	  
		//Section labels array
		 $labels = array(
		   'name' => _x( 'Sections', 'taxonomy general name' ),
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
		 
		//Register section taxonomy	
		register_taxonomy( 'wp_resume_section', 'wp_resume_position', array( 'hierarchical' => true, 'labels' => $labels,  'query_var' => true, 'rewrite' => false ) );
		
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
		 
		//Register organization taxonomy
		register_taxonomy( 'wp_resume_organization', 'wp_resume_position', array( 'hierarchical' => true, 'labels' => $labels,  'query_var' => true, 'rewrite' => false ) );
		
	}


	/**
	 * Customizes the edit screen for our custom post type
	 * @since 1.0a
	 */
	function meta_callback() {

		//pull out the standard post meta box , we don't need it
		remove_meta_box( 'postcustom', 'wp_resume_position', 'normal' );
		
		//build our own section taxonomy selector using radios rather than checkboxes
		//We use the same callback for both taxonomies and just pass the taxonomy type as an argument
		add_meta_box( 'wp_resume_sectiondiv', __('Section', 'wp-resume'), array( &$this, 'taxonomy_box' ), 'wp_resume_position', 'side', 'low', array('type'=>'wp_resume_section') );
		
		//same with orgs 
		add_meta_box( 'wp_resume_organizationdiv', __('Organization', 'wp-resume'), array( &$this, 'taxonomy_box' ), 'wp_resume_position', 'side', 'low', array('type'=>'wp_resume_organization') ); 
		
		//build the date meta input box
		add_meta_box( 'dates', __('Date', 'wp-resume'), array( &$this, 'date_box' ), 'wp_resume_position', 'normal', 'high');
		
		//build custom order box w/ helptext
		add_meta_box( 'pageparentdiv', __('Resume Order', 'wp-resume'), array( &$this, 'order_box' ), 'wp_resume_position', 'side', 'low');
	}

	function order_box($post) {
	?>
		<p><strong><?php _e('Order', 'wp-resume'); ?></strong></p>
		<p>	
			<div style="float:right; width: 200px; padding-right:10px; margin-top: -1em; display: inline;">
				<i><?php _e('Hint', 'wp-resume'); ?>:</i> <?php _e('Your resume will be sorted based on this number (ascending)', 'wp-resume'); ?>. <a href="#" id="wp_resume_help_toggle"><?php _e('More', 'wp-resume'); ?></a><br /> <br />

				<div id="wp_resume_help"><?php _e('When you add a new position, feel free to leave this number at "0" and a best guess will be made based on the position\'s end date (reverse chronological order)', 'wp-resume'); ?>. <br /><br /><?php _e('Of Course, you can always <a href="edit.php?post_type=wp_resume_position&page=wp_resume_options#sections">fine tune your resume order</a> on the options page', 'wp-resume');?>.</div>
			</div>
			<label class="screen-reader-text" for="menu_order"><?php _e('Order', 'wp-resume'); ?></label>
			<input type="text" name="menu_order" size="4" id="menu_order" value="<?php echo $post->menu_order; ?>">
		</p>
		<p style="clear: both; height: 5px;" id="wp_resume_clearfix"> </p>
	<?php
	}

	/**
	 * Generates the taxonomy radio inputs 
	 * @since 1.0a
	 * @params object $post the post object WP passes
	 * @params object $box the meta box object WP passes (with our arg stuffed in there)
	 */
	function taxonomy_box( $post, $type ) {

		//pull the type out from the meta box object so it's easier to reference
		if ( is_array( $type) )
			$type = $type['args']['type'];
		
		//get the taxonomies details
		$taxonomy = get_taxonomy($type);
			
		//grab an array of all terms within our custom taxonomy, including empty terms
		$terms = get_terms( $type, array( 'hide_empty' => false ) );

		//garb the current selected term where applicable so we can select it
		$current = wp_get_object_terms( $post->ID, $type );
		
		//loop through the terms
		foreach ($terms as $term) {
			
			//build the radio box with the term_id as its value
			echo '<input type="radio" name="'.$type.'" value="'.$term->term_id.'" id="'.$term->slug.'"';
			
			//if the post is already in this taxonomy, select it
			if ( isset( $current[0]->term_id ) )
				checked( $term->term_id, $current[0]->term_id );
			
			//build the label
			echo '> <label for="'.$term->slug.'">' . $term->name . '</label><br />'. "\r\n";
		}
			echo '<input type="radio" name="'.$type.'" value="" id="none" ';
			checked( empty($current[0]->term_id) );
			echo '/> <label for="none">' . __('None', 'wp-resume') .'</label><br />'. "\r\n"; ?>
			
			<a href="#" id="add_<?php echo $type ?>_toggle">+ <?php echo $taxonomy->labels->add_new_item; ?></a>
			<div id="add_<?php echo $type ?>_div" style="display:none">
				<label for="new_<?php echo $type ?>"><?php echo $taxonomy->labels->singular_name; ?>:</label> 
				<input type="text" name="new_<?php echo $type ?>" id="new_<?php echo $type ?>" /><br />
	<?php if ($type == 'wp_resume_organization') { ?>
				<label for="new_<?php echo $type ?>_location" style="padding-right:24px;"><?php _e('Location', 'wp_resume'); ?>:</label> 
				<input type="text" name="new_<?php echo $type ?>_location" id="new_<?php echo $type ?>_location" /><br />
	<?php } ?>
				<input type="button" value="Add New" id="add_<?php echo $type ?>_button" />
				<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="<?php echo $type ?>-ajax-loading" style="display:none;" alt="" />
			</div>
	<?php
		//nonce is a funny word
		wp_nonce_field( 'add_'.$type, '_ajax_nonce-add-'.$type );
		wp_nonce_field( 'wp_resume_taxonomy', 'wp_resume_nonce'); 
	}

	/**
	 * Processes AJAX request to add new terms
	 * @since 1.2
	 */
	function ajax_add() {
		
		//pull the taxonomy type (section or organization) from the action query var
		$type = substr($_GET['action'],4);
		
		//pull up the taxonomy details
		$taxonomy = get_taxonomy($type);
		
		//check the nonce
		check_ajax_referer( $_GET['action'] , '_ajax_nonce-add-' . $taxonomy->name );
		
		//check user capabilities
		if ( !current_user_can( $taxonomy->cap->edit_terms ) )
			die('-1');

		//insert term
		$desc = ( isset( $_POST['new_'. $type . '_location'] ) ) ? $_POST['new_'. $type . '_location'] : '';
		$term = wp_insert_term( $_POST['new_'. $type], $type, array('description' => $desc ) );
		
		//catch errors
		if ( is_wp_error( $term ) ) {
			$this->taxonomy_box( $post, $type );
			exit();
		}
		
		//associate position with new term
		wp_set_object_terms( $_POST['post_ID'], $term['term_id'], 'wp_resume_section' );
		
		//get updated post to send to taxonomy box
		$post = get_post( $_POST['post_ID'] );
		
		//return the HTML of the updated metabox back to the user so they can use the new term
		$this->taxonomy_box( $post, $type );
		
		exit();
	}

	/**
	 * Generates our date custom metadata box
	 * @since 1.0a
	 * @params object $post the post object WP passes
	 */
	function date_box( $post ) {	

		//pull the current values where applicable
		$from = get_post_meta( $post->ID, 'wp_resume_from', true );
		$to = get_post_meta( $post->ID, 'wp_resume_to', true );
		
		//format and spit out
		echo '<label for="from">From</label> <input type="text" name="from" id="from" value="'.$from.'" /> ';
		echo '<label for="to">to</label> <input type="text" name="to" id="to" value="'.$to.'" />';

	}

	/**
	 * Saves our custom taxonomies and date metadata on post add/update
	 * @since 1.0a
	 * @params int $post_id the ID of the current post as passed by WP
	 */
	function save_wp_resume_position( $post_id ) {
	
	//print_r( $_POST ); die();

		//Verify our nonce, also varifies that we are on the edit page and not updating elsewhere
		if ( !isset( $_POST['wp_resume_nonce'] ) || !wp_verify_nonce( $_POST['wp_resume_nonce'], 'wp_resume_taxonomy' , 'wp_resume_nonce' ) )
			return $post_id;
				
		//If we're autosaving we don't really care all that much about taxonomies and metadata
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
			
		//If this is a post revision and not the actual post, kick
		//(the save_post action hook gets called twice on every page save)
		if ( wp_is_post_revision($post_id) )
			return $post_id;
		
		//Verify user permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
				
		//Associate the wp_resume_position with our taxonomies
		wp_set_object_terms( $post_id, (int) $_POST['wp_resume_section'], 'wp_resume_section' );
		wp_set_object_terms( $post_id, (int) $_POST['wp_resume_organization'], 'wp_resume_organization' );
		
		//update the posts date meta
		update_post_meta( $post_id, 'wp_resume_from', $_POST['from'] );
		update_post_meta( $post_id, 'wp_resume_to', $_POST['to'] );
					
		//If they did not set a menu order, calculate a best guess bassed off of chronology
		//(menu order uses the posts's menu_order field and is 1 bassed by default)
		if ($_POST['menu_order'] == 0) {
			
			//grab the DB Obj.
			global $wpdb;
		
			//calculate the current timestamp
			$timestamp = strtotime( $_POST['to'] );
			if ( !$timestamp ) $timestamp = time();
			
			//set a counter
			$new_post_position = 1;
			
			//loop through posts 
			$section = get_term ($_POST['wp_resume_section'], 'wp_resume_section');
			$args = array(
				'post_type' => 'wp_resume_position',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'numberposts' => -1,
				'wp_resume_section' =>	$section->slug,
				'exclude' => $post_id
			);
			$posts = get_posts( $args );

			foreach ($posts as $post) {
		
				//build timestamp of post we're checking
				$ts_check = strtotime( get_post_meta( $post->ID, 'wp_resume_to', true) );
				if (!$ts_check) 
					$ts_check = time();
				
				//If we've inserted our new post in the menu_order, increment all subsequent positions
				if ($new_post_position != 1)
					//manually update the post b/c calling wp_update_post would create a recurssion loop
					$wpdb->update($wpdb->posts,array('menu_order'=>$post->menu_order+1),array('ID'=>$post->ID));
				
				//If the new post's timestamp is earlier than the current post, stick the new post here
				if ($timestamp <= $ts_check && $new_post_position == 1) 
					$new_post_position = $post->menu_order + 1;	
			
			}
			
			//manually update the post b/c calling wp_update_post would create a recurssion loop
			$wpdb->update($wpdb->posts,array('menu_order'=>$new_post_position),array('ID'=>$post_id));
			
		}

	}

	/**
	 * Function used to parse the date meta and move to human-readable format
	 * @since 1.0a
	 * @param int $ID post ID to generate date for
	 */
	function format_date( $ID ) {

		//Grab from and to post meta
		$from = get_post_meta( $ID, 'wp_resume_from', true ); 
		$to = get_post_meta( $ID, 'wp_resume_to', true ); 
		
		//if we have a start date, format as "[from] - [to]" (e.g., May 2005 - May 2006)
		if ( $from ) return '<span class="dtstart">' . $from . '</span> &ndash; <span class="dtend">' . $to . '</span>';
		
		//if we only have a to, just pass back the to (e.g., "May 2015")
		if ( $to ) return '<span class="dtend">' . $to . '</span>';
		
		//If there's no date meta, just pass back an empty string so we dont generate errors
		return '';
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
			$author = $user->user_login;
		}

		//get all sections ordered by term_id (order added)
		$sections = get_terms( 'wp_resume_section', array('hide_empty' => $hide_empty ) );
				
		//get the plugin options array to pulll the user-specified order
		$options = $this->get_options();
		
		//pull out the order array
		$user_options = $this->get_user_options($author);
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
		
		$output = apply_filters('wp_resume_sections', $output);
					
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
	
		global $wp_resume_author;
			
		//if the author isn't passed as a function arg, see if it has been set by the shortcode
		if ( $author == '' && isset( $wp_resume_author ) )
			$author = $wp_resume_author;
					
		//build our query
		$args = array(
			'post_type' => 'wp_resume_position',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'nopaging' => true,
			'wp_resume_section' => $section,
			'author_name' => $author
		);
		
		$args = apply_filters('wp_resume_query_args', $args);
			
		//query and return
		$query = new wp_query($args);
		return $query;
	}

	/**
	 * Retrieves the org associated with a given position
	 * @since 1.1a
	 */
	function get_org( $postID ) {

		$organization = wp_get_object_terms( $postID, 'wp_resume_organization' );
		if ( !is_array( $organization ) ) return false;
		return $organization[0];
		
	}

	/**
	 * Get's the options
	 * @since 1.2
	 */
	function get_options() {
		$options = get_option('wp_resume_options');
		return $options;
	}

	/**
	 * Gets wp_resume usermeta field
	 * @param int|string $user username or ID to retrieve
	 * @since 1.6
	 */
	function get_user_options($user) {
		
		//get ID if we have a username
		if ( !is_int($user) ) {
			$userdata =	get_userdatabylogin($user);
			$user = $userdata->ID;
		}
			
		return get_user_meta($user, 'wp_resume', true);
		
	}

	/**
	 * Adds custom CSS to WP's queue
	 * Checks to see if file 'resume-style.css' exists in the current template directory, otherwise includes default
	 * @since 1.0a
	 */
	function enqueue_styles() {

		if ( file_exists ( get_stylesheet_directory() . '/resume-style.css' ) )
			wp_enqueue_style('wp-resume-custom-stylesheet', get_stylesheet_directory_uri() . '/resume-style.css' );
		else 
			wp_enqueue_style('wp-resume-default-stylesheet', plugins_url(  'resume-style.css', __FILE__ ) );
	}

	/**
	 * Adds an options submenu to the resume menu in the admin pannel
	 * @since 1.0a
	 */
	function menu() {
		
		add_submenu_page( 'edit.php?post_type=wp_resume_position', __('Resume Options', 'wp-resume'), __('Options', 'wp-resume'), 'manage_options', 'wp_resume_options', array( &$this, 'options' ) );

	}

	/**
	 * Valdidates options submission data and stores position order
	 * @params array $data post data
	 * @since 1.5
	 * @returns array of validated data (without position order)
	 */
	function options_validate($data) {
		
		//make sure we're POSTing
		if ( empty($_POST) )
			return $data;
			
		//grab the existing options, we must hand WP back a complete option array
		$options = $this->get_options();
		
		//figure out what user we are acting on
		global $wpdb;
		$authors = 	$wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.user_nicename FROM $wpdb->users") );
		if ( sizeof($authors) == 1 ) {
		
			//if there is only one user in the system, it's gotta be him
			$current_author = $authors[0];
		
		} else if ( $_POST['old_user'] != $_POST['user'] ) {
		
			//if this is an auto save as a result of the author dropdown changing, 
			//save as old author, not author we're moving to
			$current_author = $_POST['old_user'];
			
			//Because we post to options.php and then get redirected, 
			//trick WP into appending the user as a parameter so we can update the dropdown
			//goes through wp_safe_redirect, so no need to escape, right?
			$_REQUEST['_wp_http_referer'] .= '&user=' . $_POST['user'];
			
		} else {
		
			//if this is a normal submit, just grab the author from the dropdown
			$current_author = $_POST['user'];
		
		}

		$user_options = $this->get_user_options($current_author);
		
		//strip html from fields
		$user_options['name'] = wp_filter_nohtml_kses( $data['name'] );
		$user_options['summary'] = wp_filter_post_kses( $data['summary'] );

		foreach ($data['contact_info_field'] as $id=>$value) {
			
			if ( !$value ) continue;
			$field = explode('|',$data['contact_info_field'][$id]);
			
			if ( sizeof($field) == 1)
				$user_options['contact_info'][$field[0]] = wp_filter_post_kses( $data['contact_info_value'][$id] );
			else
				$user_options['contact_info'][$field[0]][$field[1]] = wp_filter_post_kses( $data['contact_info_value'][$id] );

		}
		
		//sanitize section order data
		foreach ($data['order'] as $key=>$value)
			$user_options['order'][$key] = intval( $value );
		
		//store position order data
		if ( isset($data['position_order'] )  && is_array($data['position_order'] ) ) { 
			foreach ($data['position_order'] as $positionID => $order) {
				$post['ID'] = intval( $positionID );
				$post['menu_order'] = intval( $order );
				wp_update_post( $post );
			}
		}
				
		//move site-wide fields to output array
		$fields = array('fix_ie', 'rewrite');
		foreach ($fields as $field) {
			$options[$field] = $data[$field];
		}
		
		//store usermeta
		$user = get_userdatabylogin($current_author);
		update_user_meta($user->ID, 'wp_resume', $user_options);

		//flush in case they toggled rewrite
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		
		$options = apply_filters('wp_resume_options', $options);

		return $options;
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
		
		$fields = apply_filters('wp_resume_contact_fields', $fields );
		
		return $fields;
	}


	/**
	 * Callback to generate a contact info row for the options page
	 * @param int|string $value the field's current value
	 * @param int $field_id the id of the field to output
	 */
	function contact_info_row( $value = '', $field_id = '' ) { ?>
		<li id="contact_info_row[]" class="contact_info_row">
			<select name="wp_resume_options[contact_info_field][]" id="contact_info_field[]">
			<option></option>
			<?php 	foreach ( $this->contact_fields() as $id => $field) { ?>
					<?php 	if ( is_array($field) ) {
								foreach ($field as $subid => $subfield) { ?>
									<option value="<?php echo $id . '|' . $subid; ?>" <?php selected($field_id, $subid);?>>
										<?php echo $subfield; ?>
									</option>				
								<?php }
							} else { ?>
								<option value="<?php echo $id; ?>" <?php selected($field_id, $id);?>><?php echo esc_attr( $field ); ?></option>	
							<?php } ?>
			<?php } ?>
			</select>
			<input type="text" name="wp_resume_options[contact_info_value][]" id="contact_info_value[]" value="<?php echo $value; ?>"/> <br />
		</li>
	<?php } 

	/**
	 * Creates the options sub-panel
	 * @since 1.0a
	 */
	function options() { 	
		global $wpdb;
	?>
	<div class="wrap">
		<h2><?php _e('Resume Options', 'wp_resume'); ?></h2>
		<form method="post" action='options.php' id="wp_resume_form">
	<?php 
			
	//provide feedback
	settings_errors();

	//Tell WP that we are on the wp_resume_options page
	settings_fields( 'wp_resume_options' ); 

	//Pull the existing options from the DB
	$options = $this->get_options();

	//set up the current author
	$authors = $wpdb->get_results("SELECT display_name, user_nicename from $wpdb->users ORDER BY display_name");

	if ( sizeof($authors) == 1 ) {
		//if there's only one author, that's our author
		$current_author = $authors[0]->user_nicename;
	} else if ( isset($_GET['user'] ) ) {
		//if there's multiple authors, look for post data from author drop down
		$current_author = $_GET['user'];
	} else {
		//otherwise, assume the current user
		$current_user = wp_get_current_user();
		$current_author = $current_user->user_login;
	}

	$user_options = $this->get_user_options($current_author);

	?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Usage', 'wp-resume'); ?></label></th>
				<td>
					<strong><?php _e('To use WP Resume...', 'wp-resume'); ?></strong>
					<ol>
						<li><?php _e('Add content to your resume through the menus on the left', 'wp-resume'); ?></li>
						<li><?php _e('If you wish, add your name, contact information, summary, and order your resume below', 'wp-resume'); ?></li>
						<li><?php _e('Create a new page as you would normally', 'wp-resume'); ?>
						<li><?php _e('Add the text <code>[wp_resume]</code> to the page\'s body', 'wp-resume'); ?></li>
						<li><?php _e('Your resume will now display on that page', 'wp-resume'); ?>.</li>
					</ol><br />
					<strong><?php _e('Want to have multiple resumes on your site?', 'wp-resume'); ?></strong> <a href="#" id="toggleMultiple"><?php _e('Yes!', 'wp-resume'); ?></a><br />
					<div id="multiple">
					<?php _e('WP Resume associates each resume with a user. To create a second resume...', 'wp-resume'); ?>
					<ol>
						<li style="font-size: 11px;"><?php _e('Simply <a href="user-new.php">add a new user</a> (or select an existing user in step two)', 'wp-resume'); ?>.</li>
						<li style="font-size: 11px;"><a href="post-new.php?post_type=wp_resume_position"><?php _e('Add positions</a> as you would normally, being sure to select that user as the position\'s author. You may need to display the author box by enabling it in the "Screen Options" toggle in the top-right corner of the position page', 'wp-resume'); ?>.</li>
						<li style="font-size: 11px;"><?php _e('Select the author from the drop down below and fill in the name, contact info, and summary fields (optional)', 'wp-resume'); ?>.</li>
						<li style="font-size: 11px;"><a href="post-new.php?post_type=page"><?php _e('Create a new page</a> and add the <code>[wp_resume]</code> shortcode, similar to above, but set the page author to the resume\'s author (the author from step two). Again, you may need to enable the author box', 'wp-resume'); ?>.</li>
					</ol>
					 <em><?php _e('Note', 'wp_resume'); ?>:</em> <?php _e('To embed multiple resumes on the same page, you can alternatively use the syntax <code>[wp_resume user="user_login"]</code> where <code>user_login</code> is the username of the resume\'s author', 'wp-resume'); ?>.
					 </div>
				</td>
			</tr>
			<?php 
				if (sizeof($authors) > 1) {
				?>
			<tr valign="top">
				<th scope="row"><?php _e('User', 'wp_resume'); ?></label></th>
				<td>
					<select name="user" id="user">
						<?php foreach ($authors as $author) { ?>
						<option value="<?php echo $author->user_nicename; ?>" <?php selected($author->user_nicename, $current_author); ?>><?php echo $author->display_name; ?></option>
						<?php } ?>
					</select>
					<input type="hidden" name="old_user" value="<?php echo $current_author; ?>" />
				</td>
			</tr>
			<?php } ?>
			<tr valign="top">
				<th scope="row"><label for="wp_resume_options[name]"><?php _e('Name', 'wp-resume') ;?></label></th>
				<td>
					<input name="wp_resume_options[name]" type="text" id="wp_resume_options[name]" value="<?php if ( isset( $user_options['name'] ) ) echo $user_options['name']; ?>" class="regular-text" /><BR />
					<span class="description"><?php _e('Your name -- displays on the top of your resume', 'wp-resume'); ?>.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Contact Information', 'wp-resume'); ?></th>
				<td>
					<ul class="contact_info_blank" style="display:none;">
						<?php $this->contact_info_row(); ?>
					</ul>
					<ul id="contact_info">
						<?php if ( isset($user_options['contact_info'] ) && is_array( $user_options['contact_info'] ) ) 
							array_walk_recursive($user_options['contact_info'], array( &$this, 'contact_info_row' ) ); ?>
					</ul>
					<a href="#" id="add_contact_field">+ <?php _e('Add Field', 'wp-resume'); ?></a><br />
					<span class="description"><?php _e('(optional) Add any contact info you would like included in your resume', 'wp-resume'); ?>.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="wp_resume_options[summary]"><?php _e('Summary', 'wp-resume'); ?></label></th>
				<td id="poststuff">
				<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
					<?php the_editor( ( isset($user_options['summary'] ) ) ? $user_options['summary'] : '', "wp_resume_options[summary]" ); ?>	
				</div>
				<span class="description"><?php _e('(optional) Plain-text summary of your resume, professional goals, etc. Will appear on your resume below your contact information before the body', 'wp-resume'); ?>.</span>	
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Resume Order', 'wp-resume'); ?></th>
				<td>
				<ul id="sections">
	<?php foreach ( $this->get_sections( false, $current_author ) as $section ) { ?>

					<li class="section" id="<?php echo $section->term_id; ?>">
	<?php 				echo $section->name; ?>
						<ul class="organizations">
	<?php				$current_org='';
						$posts = $this->query( $section->slug, $current_author );
						if ( $posts->have_posts() ) : while ( $posts->have_posts() ) : $posts->the_post();
							$organization = $this->get_org( get_the_ID() ); 
							if ($organization && $organization->term_id != $current_org) {
								if ($current_org != '') { 
	?>								
										</ul><!-- .positions -->
									</li><!-- .organization -->
	<?php 						} 
								$current_org = $organization->term_id; 
	?>
								<li class="organization" id="<?php echo $organization->term_id; ?>">
									<?php echo $organization->name; ?>
									<ul class="positions">
	<?php						}  ?>
									<li class="position" id="<?php the_ID(); ?>">
										<?php echo the_title(); ?> <?php if ($date = $this->format_date( get_the_ID() ) ) echo "($date)"; ?>
									</li><!-- .position -->
	<?php 				endwhile; ?>
									</ul><!-- .positions -->				
	<?php				endif;	 ?>
							</li><!-- .organization -->
						</ul><!-- .organizations -->
					</li><!-- .section -->
					<?php } ?>
				</ul><!-- #sections -->
				<span class="description"><?php _e('New positions are automatically displayed in reverse chronological order, but you can fine tune that order by rearranging the elements in the list above', 'wp-resume'); ?>.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					&nbsp;
				</th>
				<td>
					<a href="#" id="toggleHood"><?php _e('Show Advanced Options', 'wp-resume'); ?></a>
				</td>
			</tr>
			<tr valign="top" class="underHood">
				<th scrope="row"><?php _e('Force IE HTML5 Support', 'wp-resume'); ?></th>
				<td>
					<input type="radio" name="wp_resume_options[fix_ie]" id="fix_ie_yes" value="1" <?php checked($options['fix_ie'], 1); ?>/> <label for="fix_ie_yes"><?php _e('Yes', 'wp-resume'); ?></label><br />
					<input type="radio" name="wp_resume_options[fix_ie]" id="fix_ie_no" value="0" <?php checked($options['fix_ie'], 0); ?>/> <label for="fix_ie_no"><?php _e('No', 'wp-resume'); ?></label><br />
					<span class="description"><?php _e('If Internet Explorer breaks your resume\'s formatting, conditionally including a short Javascript file should force IE to recognize html5 semantic tags', 'wp-resume'); ?>.</span>
				</td>
			</tr>
			<tr valign="top" class="underHood">
				<th scrope="row"><?php _e('Enable URL Rewriting', 'wp-resume'); ?></th>
				<td>
					<input type="radio" name="wp_resume_options[rewrite]" id="rewrite_yes" value="1" <?php checked($options['rewrite'], 1); ?>/> <label for="rewrite_yes"><?php _e('Yes', 'wp-resume'); ?></label><br />
					<input type="radio" name="wp_resume_options[rewrite]" id="rewrite_no" value="0" <?php checked($options['rewrite'], 0); ?> <?php checked($options['rewrite'], null); ?>/> <label for="rewrite_no"><?php _e('No', 'wp-resume'); ?></label><br />
					<span class="description"><?php _e('Creates individual pages for each position, and index pages for each section and organization', 'wp-resume'); ?>.</span>
				</td>
			</tr>
			<tr valign="top" class="underHood">
				<th scrope="row"><?php _e('Customizing WP Resume', 'wp-resume'); ?></th>
				<td>
					<Strong><?php _e('Style Sheets', 'wp-resume'); ?></strong><br />
					<?php _e('Although some styling is included by default, you can customize the layout by modifying <a href="theme-editor.php">your theme\'s stylesheet</a>', 'wp-resume'); ?>.<br /><br />
					
					<strong><?php _e('Templates', 'wp-resume'); ?></strong> <br />
					<?php _e("Any WP Resume template file (resume.php, resume-style.css, resume-text.php, etc.) found in your theme's directory will override the plugin's included template. Feel free to copy the file from the plugin directory into your theme's template directory and modify the file to meet your needs", 'wp-resume'); ?>.<br /><br />
					
					<strong><?php _e('Feeds', 'wp-resume'); ?></strong> <br />
					<?php _e('WP Resume allows you to access your data in three machine-readable formats. By default, the resume outputs in an <a href="http://microformats.org/wiki/hresume">hResume</a> compatible format. A JSON feed can be generated by appending <code>?feed=json</code> to your resume page\'s URL and a plain-text alternative (useful for copying and pasting into applications and forms) is available by appending <code>?feed=text</code> to your resume page\'s URL', 'wp-resume'); ?>.<br /><br />
				</td>
			</tr>				
		</table>
		<script>
		jQuery(document).ready(function($) {
		
		
		
	});
		</script>
		<p class="submit">
			 <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wp-resume') ?>" />
		</p>
		</form>
	</div>
	<?php
	}

	/**
	 * Checks DB version on admin init and upgrades if necessary
	 * Used b/c 1) no CPTs on activation hook, 2) no activation hook on multi-update
	 * @since 1.6
	 */
	function admin_init() {
			
		$options = $this->get_options();
		
		//check for upgrade and upgrade, works as an activation hook, more or less.
		if ( !isset($options['db_version']) || $options['db_version'] < $this->version )
			$options = $this->upgrade_db();

		register_setting( 'wp_resume_options', 'wp_resume_options', array( &$this, 'options_validate' ) );
		
		//make the plugin translation friendly
		load_plugin_textdomain( 'wp-resume', null, dirname( plugin_basename( __FILE__ ) ) .'/languages/' );	
		
		//If we are on the wp_resume_options page, enque the tinyMCE editor
		if ( !empty ($_GET['page'] ) && $_GET['page'] == 'wp_resume_options' ) {
			wp_enqueue_script('editor');
			add_thickbox();
			wp_enqueue_script('media-upload');
			wp_enqueue_script('post');
			add_action( 'admin_print_footer_scripts', 'wp_tiny_mce', 25 );
			
			//add css
			wp_enqueue_style('wp_resume_admin_stylesheet', plugins_url(  'admin-style.css', __FILE__ ) );	
		}
		
		
	}
	
	function enqueue_scripts() {
	
		$suffix = ( WP_DEBUG ) ? 'dev.' : '';
	
		$post = false;
		if ( !empty( $_GET['post'] ) )
			$post = get_post( $_GET['post'] );
	
		//load javascript with libraries on options page
		if ( !empty ( $_GET['page'] ) && $_GET['page'] == 'wp_resume_options' ) { 
			wp_enqueue_script( 'wp_resume', plugins_url('/wp_resume.' . $suffix . 'js', __FILE__), array("jquery", "jquery-ui-core", "jquery-ui-sortable", "wp-lists", "jquery-ui-sortable"), $this->version );		
		//if on the org, section, or edit page, load the script without all the libraries
		} else if ( ( !empty( $_GET['post_type'] ) && $_GET['post_type'] == 'wp_resume_position' ) ||
			 ( !empty( $_GET['post'] ) && $post && $post->post_type == 'wp_resume_position' ) ) { 
			wp_enqueue_script( 'wp_resume', plugins_url('/wp_resume.' . $suffix . 'js', __FILE__), array("jquery"), $this->version );		
		}
		
		$data = array( 
			'more' => __('More', 'wp-resume'),
			'less' => __('less', 'wp-resume'),
			'yes' => __('Yes!', 'wp-resume'),
			'no' => __('No.', 'wp-resume'),
			'hideAdv' => __('Hide Advanced Options', 'wp-resume'),
			'showAdv' => __('Show Advanced Options', 'wp-resume'),
			'orgName' => __('The name of the organization as you want it to appear', 'wp-resume'),
			'orgLoc' => __('Traditionally the location of the organization (optional)', 'wp-resume'),

		
		);
		wp_localize_script( 'wp_resume', 'wp_resume', $data );
	
	}

	/**
	 * Moves information around the database, supports back to 1.5
	 * @since 1.2
	 */
	function upgrade_db() {
		
		//default fields and values
		$fields['global'] = array('fix_ie' => true, 'rewrite' => false);
		$fields['user'] = array('name'=>'', 'summary' => '', 'contact_info'=> array() );
		$i = 0;	foreach ( $this->get_sections( false ) as $section)
				$fields['user']['order'][$section->term_id] = $i++;

		//get our options
		$options = $this->get_options();
		
		//check to see if we have any sections
		if ( sizeof( $this->get_sections(false) ) == 0 ) {
			//add the sections
			wp_insert_term( 'Education', 'wp_resume_section');
			wp_insert_term( 'Experience', 'wp_resume_section' );
			wp_insert_term( 'Awards', 'wp_resume_section' );
		}
			
		//add multi-user support (v. 1.6)
		if ( !isset($options['db_version']) || substr( $options['db_version'], 0, 2 ) < '1.6' ) {
			
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
			add_user_meta($current_user->ID, 'wp_resume', $usermeta);

		}
		
		//if global fields are null, set to default
		foreach ($fields['global'] as $key=>$value) {
			if ( !isset( $options[$key] ) )
				$options[$key] = $value;
		}
		
		//if user fields are null for any user, set to default
		global $wpdb;
		$users = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users") );
		foreach ($users as $user) {
		
			//get current options
			$user_options = get_user_meta($user, 'wp_resume', true);
			
			//loop default fields
			foreach ($fields['user'] as $key=>$value) {
			
				//check they exist, if not set
				if ( !isset( $user_options[$key] ) )
					$user_options[$key] = $value;
					
				//update
				update_user_meta($user,'wp_resume', $user_options);
			}	
			
		}
			
		//DB Versioning
		$options['db_version'] = $this->version;
		
		//store updated options
		update_option( 'wp_resume_options', $options );
		
		//flush rewrite rules just in case
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		
		do_action('wp_resume_upgrade', $options['db_version'] );
	  
		return $options;

	}


	/**
	 * Modifies the add organization page to provide help text and better descriptions
	 * @since 1.2
	 * @disclaimer it's not pretty, but it get's the job done.
	 */
	function org_helptext() { ?>
		<noscript>
			<h4><?php _e('Help', 'wp-resume'); ?></h4>
			<p><strong><?php _e('Name', 'wp-resume'); ?></strong>: <?php _e('The name of the organization', 'wp-resume'); ?></p>
			<p><strong><?php _e('Parent', 'wp-resume'); ?></strong>: <?php _e('Do not add a parent', 'wp-resume'); ?></p>
			<p><strong><?php _e('Description', 'wp-resume'); ?></strong>: <?php _e('You can put the location of the organization here (optional)', 'wp-resume'); ?></p>
		</noscript>
	<?php }


	/**
	 * Includes resume template on shortcode use 
	 * @since 1.3
	 */
	function shortcode( $atts ) {

		//determine author and set as global so templates can read
		$this->author = $this->get_author( $atts );

		ob_start();
		do_action('wp_resume_shortcode_pre');
		$this->include_template('resume.php');
		$resume = ob_get_contents();
		do_action('wp_resume_shortcode_post');
		ob_end_clean();
		
		apply_filters('wp_resume_shortcode', $resume);

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

	function header() { 
		$options = $this->get_options(); ?>
			<link rel="profile" href="http://microformats.org/profile/hcard" />
			<?php if ($options['fix_ie']) { ?>
			<!--[if lt IE 9]>
				<script type="text/javascript" src="<?php echo plugins_url(  'html5.js', __FILE__ ); ?>"></script>
			<![endif]-->
			<?php } ?>
	<?php }

	/**
	 * Includes the plain text template
	 * @since 1.5
	 */
	function plain_text() {
		$this->feed_get_author();
		header('Content-Type: text/plain; charset='. get_bloginfo('charset') );
		$this->include_template('resume-text.php');
		do_action('wp_resume_plain_text');
	}

	/**
	 * Includes the json template
	 * @since 1.5
	 */
	function json() {
		$this->feed_get_author();
		header('Content-type: application/json; charset='. get_bloginfo('charset') );
		$this->include_template('resume-json.php');
		do_action('wp_resume_json');
	}

	/**
	 * Includes a wp_resume template file
	 * First looks in current theme directory for file, otherwise includes defaults
	 * @since 1.5
	 */
	function include_template( $template ) {

		if ( file_exists( get_stylesheet_directory() . '/' . $template ) )
			include ( get_stylesheet_directory() . '/' . $template ) ;
		else 
			include ( $template );
							
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
		return $user->user_login;
	}

	/**
	 * Injects resume rewrite rules into the rewrite array when applicable
	 */
	function rewrite_rules() {
		$options = $this->get_options();
		
		if (!isset($options['rewrite']) || !$options['rewrite'] )
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

		$options = $this->get_options();
		
		if ( $post->post_type != 'wp_resume_position' && isset($options['rewrite']) && $options['rewrite'] )
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
		
		$link = apply_filters('wp_resume_permalink', $link);

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
			$this->author = $user->user_login; 
			
		} else {
		
			$this->author = $matches[1];
		
		}
		
		$this->author = apply_filters( 'wp_resume_author', $this->author );
		
		return $this->author;
		
	}
	
}

new WP_Resume();